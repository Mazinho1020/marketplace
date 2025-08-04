<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class NotificacaoConfigService
{
    protected $empresaId;

    public function __construct($empresaId = null)
    {
        $this->empresaId = $empresaId ?? (Auth::check() ? Auth::user()->empresa_id : 1);
    }

    public function isAppEnabled(string $appSlug): bool
    {
        return $this->getConfigValue("app_{$appSlug}_enabled", false);
    }

    public function getAppChannels(string $appSlug): array
    {
        return $this->getConfigValue("channels_{$appSlug}", []);
    }

    public function getAutomationConfig(string $eventType): array
    {
        $prefix = "auto_{$eventType}";

        return [
            'enabled' => $this->getConfigValue("{$prefix}_enabled", false),
            'time' => $this->getConfigValue("{$prefix}_time", '09:00'),
            'apps' => $this->getConfigValue("{$prefix}_apps", []),
            'bonus_points' => $this->getConfigValue("{$prefix}_bonus_points", 0),
        ];
    }

    public function getBehaviorConfig(): array
    {
        return [
            'queue_enabled' => $this->getConfigValue('notification_queue_enabled', true),
            'retry_attempts' => $this->getConfigValue('notification_retry_attempts', 3),
            'rate_limit' => $this->getConfigValue('notification_rate_limit', 100),
            'debug_enabled' => $this->getConfigValue('notification_debug_enabled', false),
        ];
    }

    protected function getConfigValue(string $chave, $default = null)
    {
        $cacheKey = "config_{$this->empresaId}_{$chave}";

        return Cache::remember($cacheKey, 3600, function () use ($chave, $default) {
            $result = DB::table('config_definitions')
                ->join('config_values', 'config_definitions.id', '=', 'config_values.config_id')
                ->where('config_definitions.empresa_id', $this->empresaId)
                ->where('config_definitions.chave', $chave)
                ->value('config_values.valor');

            if ($result === null) {
                return $default;
            }

            // Cast automático baseado no tipo
            $tipo = DB::table('config_definitions')
                ->where('empresa_id', $this->empresaId)
                ->where('chave', $chave)
                ->value('tipo');

            return $this->castValue($result, $tipo);
        });
    }

    protected function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'json', 'array' => json_decode($value, true),
            default => $value
        };
    }

    public function clearCache(string $chave = null)
    {
        if ($chave) {
            Cache::forget("config_{$this->empresaId}_{$chave}");
        } else {
            Cache::flush(); // Clear all cache
        }
    }
}
