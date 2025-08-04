<?php

namespace App\Jobs;

use App\Models\Notificacao\NotificacaoEnviada;
use App\Services\NotificacaoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificacaoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notificacao;
    protected $canal;

    public $tries = 3;
    public $timeout = 60;

    public function __construct(NotificacaoEnviada $notificacao, string $canal)
    {
        $this->notificacao = $notificacao;
        $this->canal = $canal;

        // Define a fila baseada no canal
        $this->onQueue("notifications_{$canal}");
    }

    public function handle()
    {
        try {
            Log::info('Processando notificação', [
                'id' => $this->notificacao->id,
                'canal' => $this->canal,
                'tentativa' => $this->attempts()
            ]);

            // Verifica se a notificação ainda é válida
            if ($this->notificacao->isExpirada()) {
                $this->notificacao->update(['status' => 'expirado']);
                return;
            }

            // Envia baseado no canal
            $success = $this->sendByChannel($this->canal);

            if ($success) {
                $this->notificacao->update([
                    'status' => 'enviado',
                    'enviado_em' => now(),
                    'tentativas' => $this->attempts()
                ]);
            } else {
                throw new \Exception('Falha no envio da notificação');
            }
        } catch (\Exception $e) {
            $this->notificacao->update([
                'status' => 'erro',
                'mensagem_erro' => $e->getMessage(),
                'tentativas' => $this->attempts()
            ]);

            Log::error('Erro no job de notificação', [
                'id' => $this->notificacao->id,
                'canal' => $this->canal,
                'tentativa' => $this->attempts(),
                'erro' => $e->getMessage()
            ]);

            // Se esgotou as tentativas, marca como falha
            if ($this->attempts() >= $this->tries) {
                $this->notificacao->update(['status' => 'falha']);
            }

            throw $e; // Re-throw para o sistema de retry
        }
    }

    protected function sendByChannel(string $canal): bool
    {
        switch ($canal) {
            case 'websocket':
                return $this->sendWebSocket();
            case 'push':
                return $this->sendPush();
            case 'email':
                return $this->sendEmail();
            case 'sms':
                return $this->sendSMS();
            case 'in_app':
                return $this->saveInApp();
            default:
                return false;
        }
    }

    protected function sendWebSocket(): bool
    {
        // Implementar WebSocket - exemplo com broadcasting
        /*
        broadcast(new NotificationEvent([
            'id' => $this->notificacao->id,
            'titulo' => $this->notificacao->titulo,
            'mensagem' => $this->notificacao->mensagem,
            'dados' => $this->notificacao->dados_processados,
            'usuario_id' => $this->notificacao->usuario_id
        ]))->toOthers();
        */

        Log::info('WebSocket notification sent', ['id' => $this->notificacao->id]);
        return true;
    }

    protected function sendPush(): bool
    {
        // Implementar Push Notification
        // Exemplo com FCM, OneSignal, etc.
        Log::info('Push notification sent', ['id' => $this->notificacao->id]);
        return true;
    }

    protected function sendEmail(): bool
    {
        // Implementar envio de email
        /*
        Mail::to($this->notificacao->email_destinatario)
            ->send(new NotificationMail($this->notificacao));
        */

        Log::info('Email notification sent', ['id' => $this->notificacao->id]);
        return true;
    }

    protected function sendSMS(): bool
    {
        // Implementar SMS
        // Exemplo com Twilio, Zenvia, etc.
        Log::info('SMS notification sent', ['id' => $this->notificacao->id]);
        return true;
    }

    protected function saveInApp(): bool
    {
        // Para notificações in-app, só atualiza o status
        $this->notificacao->update([
            'status' => 'entregue',
            'entregue_em' => now()
        ]);

        Log::info('In-app notification saved', ['id' => $this->notificacao->id]);
        return true;
    }

    public function failed(\Exception $exception)
    {
        Log::error('Job de notificação falhou definitivamente', [
            'id' => $this->notificacao->id,
            'canal' => $this->canal,
            'erro' => $exception->getMessage()
        ]);

        $this->notificacao->update([
            'status' => 'falha',
            'mensagem_erro' => $exception->getMessage()
        ]);
    }
}
