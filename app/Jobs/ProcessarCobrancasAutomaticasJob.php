<?php

namespace App\Jobs;

use App\Services\Financial\CobrancaAutomaticaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessarCobrancasAutomaticasJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // 5 minutos
    public int $tries = 3;

    public function __construct(
        public ?int $empresaId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(CobrancaAutomaticaService $cobrancaService): void
    {
        try {
            Log::info('Iniciando processamento de cobranças automáticas', [
                'empresa_id' => $this->empresaId,
                'timestamp' => now()
            ]);

            $resultado = $cobrancaService->processarCobrancasDiarias();

            // Log do resultado
            Log::info('Cobranças automáticas processadas com sucesso', $resultado);

            // Enviar relatório por email se configurado
            $this->enviarRelatorioProcessamento($resultado);

        } catch (\Exception $e) {
            Log::error('Erro no processamento de cobranças automáticas: ' . $e->getMessage(), [
                'empresa_id' => $this->empresaId,
                'exception' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Enviar relatório de processamento por email
     */
    private function enviarRelatorioProcessamento(array $resultado): void
    {
        try {
            // Configurar destinatários do relatório
            $destinatarios = config('financial.relatorio_cobranca.emails', []);
            
            if (empty($destinatarios)) {
                return;
            }

            $dadosRelatorio = [
                'data_processamento' => now(),
                'empresa_id' => $this->empresaId,
                'resultado' => $resultado,
                'resumo' => [
                    'total_avisos' => $resultado['avisos_vencimento'],
                    'total_cobrancas' => $resultado['cobrancas_vencidas'],
                    'contas_atualizadas' => $resultado['contas_atualizadas'],
                    'emails_enviados' => $resultado['emails_enviados'],
                    'erros' => count($resultado['erros']),
                ]
            ];

            // Aqui você implementaria o envio do email de relatório
            // Mail::to($destinatarios)->send(new RelatorioCobrancaMail($dadosRelatorio));
            
            Log::info('Relatório de cobrança enviado', [
                'destinatarios' => $destinatarios,
                'resumo' => $dadosRelatorio['resumo']
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao enviar relatório de cobrança: ' . $e->getMessage());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de cobrança automática falhou', [
            'empresa_id' => $this->empresaId,
            'exception' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Notificar administradores sobre a falha
        $this->notificarFalhaProcessamento($exception);
    }

    /**
     * Notificar sobre falha no processamento
     */
    private function notificarFalhaProcessamento(\Throwable $exception): void
    {
        try {
            $admins = config('financial.admin_emails', []);
            
            if (empty($admins)) {
                return;
            }

            $dadosNotificacao = [
                'empresa_id' => $this->empresaId,
                'data_falha' => now(),
                'erro' => $exception->getMessage(),
                'tentativas' => $this->attempts(),
            ];

            // Aqui você implementaria o envio do email de falha
            // Mail::to($admins)->send(new FalhaCobrancaMail($dadosNotificacao));

        } catch (\Exception $e) {
            Log::error('Erro ao notificar falha de cobrança: ' . $e->getMessage());
        }
    }
}