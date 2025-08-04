<?php

namespace App\Services;

use App\Models\Notificacao\NotificacaoEnviada;
use App\Models\Notificacao\NotificacaoTemplate;
use App\Models\Notificacao\NotificacaoPreferenciaUsuario;
use App\Jobs\NotificacaoJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class NotificacaoService
{
    protected $configService;
    protected $templateService;
    protected $empresaId;

    public function __construct(
        NotificacaoConfigService $configService,
        NotificacaoTemplateService $templateService,
        $empresaId = null
    ) {
        $this->configService = $configService;
        $this->templateService = $templateService;
        $this->empresaId = $empresaId ?? 1;
    }

    public function sendEvent(string $eventType, array $data, array $options = []): bool
    {
        try {
            // Verifica rate limit
            if (!$this->checkRateLimit()) {
                Log::warning('Rate limit atingido para notificações', [
                    'empresa_id' => $this->empresaId,
                    'event_type' => $eventType
                ]);
                return false;
            }

            // Busca aplicações ativas para este evento
            $activeApps = $this->getActiveAppsForEvent($eventType);

            if (empty($activeApps)) {
                Log::info('Nenhuma aplicação ativa para evento', [
                    'event_type' => $eventType,
                    'empresa_id' => $this->empresaId
                ]);
                return true;
            }

            // Processa cada aplicação
            foreach ($activeApps as $appSlug) {
                $this->processAppNotification($eventType, $data, $appSlug, $options);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao enviar notificação', [
                'event_type' => $eventType,
                'empresa_id' => $this->empresaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    protected function processAppNotification(string $eventType, array $data, string $appSlug, array $options = [])
    {
        // Busca template
        $template = $this->templateService->getTemplateWithABTest($eventType, $appSlug, $data);

        if (!$template) {
            Log::warning('Template não encontrado', [
                'event_type' => $eventType,
                'app_slug' => $appSlug,
                'empresa_id' => $this->empresaId
            ]);
            return;
        }

        // Processa variáveis do template
        $processedTemplate = $this->templateService->processTemplate($template, $data);

        // Determina canais habilitados
        $appChannels = $this->configService->getAppChannels($appSlug);
        $templateChannels = $template->canais ?? [];
        $enabledChannels = array_intersect($appChannels, $templateChannels);

        if (empty($enabledChannels)) {
            Log::warning('Nenhum canal habilitado', [
                'app_channels' => $appChannels,
                'template_channels' => $templateChannels,
                'app_slug' => $appSlug
            ]);
            return;
        }

        // Determina usuários alvo
        $targetUsers = $this->getTargetUsers($data, $options);

        // Envia para cada usuário
        foreach ($targetUsers as $userId) {
            $this->sendToUser($template, $processedTemplate, $enabledChannels, $userId, $data);
        }
    }

    protected function sendToUser(
        NotificacaoTemplate $template,
        array $processedTemplate,
        array $channels,
        int $userId,
        array $originalData
    ) {
        // Verifica preferências do usuário
        $userChannels = $this->getUserEnabledChannels($userId, $template->aplicacao_id, $channels);

        if (empty($userChannels)) {
            return;
        }

        // Cria registro da notificação
        $notificacao = NotificacaoEnviada::create([
            'empresa_id' => $this->empresaId,
            'template_id' => $template->id,
            'tipo_evento_id' => $template->tipo_evento_id,
            'aplicacao_id' => $template->aplicacao_id,
            'usuario_id' => $userId,
            'titulo' => $processedTemplate['titulo'],
            'mensagem' => $processedTemplate['mensagem'],
            'dados_processados' => $processedTemplate,
            'dados_evento_origem' => $originalData,
            'canal' => implode(',', $userChannels),
            'prioridade' => $template->prioridade,
            'status' => 'pendente',
            'agendado_para' => now(),
            'expira_em' => $template->expira_em_minutos ? now()->addMinutes($template->expira_em_minutos) : null,
        ]);

        // Envia através dos canais
        $this->sendThroughChannels($notificacao, $userChannels);
    }

    protected function sendThroughChannels(NotificacaoEnviada $notificacao, array $channels)
    {
        $behaviorConfig = $this->configService->getBehaviorConfig();

        foreach ($channels as $channel) {
            if ($behaviorConfig['queue_enabled']) {
                // Envia via fila
                NotificacaoJob::dispatch($notificacao, $channel)
                    ->onQueue("notifications_{$channel}");
            } else {
                // Envia síncronamente
                $this->sendImmediately($notificacao, $channel);
            }
        }
    }

    protected function sendImmediately(NotificacaoEnviada $notificacao, string $channel)
    {
        try {
            switch ($channel) {
                case 'websocket':
                    $this->sendWebSocket($notificacao);
                    break;
                case 'push':
                    $this->sendPush($notificacao);
                    break;
                case 'email':
                    $this->sendEmail($notificacao);
                    break;
                case 'sms':
                    $this->sendSMS($notificacao);
                    break;
                case 'in_app':
                    $this->saveInApp($notificacao);
                    break;
            }

            $notificacao->update([
                'status' => 'enviado',
                'enviado_em' => now()
            ]);
        } catch (\Exception $e) {
            $notificacao->update([
                'status' => 'erro',
                'mensagem_erro' => $e->getMessage(),
                'tentativas' => $notificacao->tentativas + 1
            ]);

            Log::error('Erro ao enviar notificação por canal', [
                'notification_id' => $notificacao->id,
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function sendWebSocket(NotificacaoEnviada $notificacao)
    {
        // Implementar WebSocket
        // broadcast(new NotificationEvent($notificacao))->toOthers();
        Log::info('WebSocket notification sent', ['id' => $notificacao->id]);
    }

    protected function sendPush(NotificacaoEnviada $notificacao)
    {
        // Implementar Push Notification
        Log::info('Push notification sent', ['id' => $notificacao->id]);
    }

    protected function sendEmail(NotificacaoEnviada $notificacao)
    {
        // Implementar Email
        Log::info('Email notification sent', ['id' => $notificacao->id]);
    }

    protected function sendSMS(NotificacaoEnviada $notificacao)
    {
        // Implementar SMS
        Log::info('SMS notification sent', ['id' => $notificacao->id]);
    }

    protected function saveInApp(NotificacaoEnviada $notificacao)
    {
        $notificacao->update([
            'status' => 'entregue',
            'entregue_em' => now()
        ]);
        Log::info('In-app notification saved', ['id' => $notificacao->id]);
    }

    protected function getActiveAppsForEvent(string $eventType): array
    {
        $allApps = ['cliente', 'empresa', 'admin', 'entregador', 'fidelidade'];
        $activeApps = [];

        foreach ($allApps as $app) {
            if ($this->configService->isAppEnabled($app)) {
                $activeApps[] = $app;
            }
        }

        return $activeApps;
    }

    protected function getTargetUsers(array $data, array $options): array
    {
        // Por padrão, usa o usuario_id dos dados
        if (isset($data['usuario_id'])) {
            return [$data['usuario_id']];
        }

        // Ou busca baseado em outros critérios
        if (isset($options['target_users'])) {
            return $options['target_users'];
        }

        return [];
    }

    protected function getUserEnabledChannels(int $userId, int $aplicacaoId, array $channels): array
    {
        $preferencias = NotificacaoPreferenciaUsuario::where('usuario_id', $userId)
            ->where('aplicacao_id', $aplicacaoId)
            ->first();

        if (!$preferencias) {
            return $channels; // Se não tem preferência, usa todos os canais
        }

        // Verifica horário de silêncio
        if ($preferencias->isHorarioSilencio()) {
            return ['in_app']; // Durante silêncio, só in-app
        }

        $enabledChannels = [];
        foreach ($channels as $channel) {
            if ($preferencias->getCanalHabilitado($channel)) {
                $enabledChannels[] = $channel;
            }
        }

        return $enabledChannels;
    }

    protected function checkRateLimit(): bool
    {
        $rateLimit = $this->configService->getBehaviorConfig()['rate_limit'];
        $key = "notifications:{$this->empresaId}";

        if (RateLimiter::tooManyAttempts($key, $rateLimit)) {
            return false;
        }

        RateLimiter::hit($key);
        return true;
    }
}
