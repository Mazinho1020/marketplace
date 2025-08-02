<?php

namespace App\Core\Config;

use App\Models\Config\ConfigDefinition;
use App\Models\Config\ConfigValue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Gerenciador Central de Configurações
 * Responsável por gerenciar todas as configurações do sistema
 * com cache inteligente e fallback seguro
 */
class ConfigManager
{
    private const CACHE_TTL = 3600; // 1 hora
    private const CACHE_PREFIX = 'config';

    /**
     * Busca uma configuração específica
     */
    public function get(string $key, int $empresa_id = 1, $default = null)
    {
        $cacheKey = $this->getCacheKey($empresa_id, $key);

        try {
            return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($key, $empresa_id, $default) {
                $value = ConfigValue::query()
                    ->join('config_definitions', 'config_values.config_id', '=', 'config_definitions.id')
                    ->where('config_definitions.chave', $key)
                    ->where('config_values.empresa_id', $empresa_id)
                    ->value('config_values.valor');

                return $value ?? $default;
            });
        } catch (\Exception $e) {
            Log::error("Erro ao buscar configuração: {$key}", [
                'empresa_id' => $empresa_id,
                'error' => $e->getMessage()
            ]);

            return $default;
        }
    }

    /**
     * Define uma configuração
     */
    public function set(string $key, $value, int $empresa_id = 1): bool
    {
        try {
            $definition = ConfigDefinition::where('chave', $key)->first();

            if (!$definition) {
                Log::warning("Configuração não encontrada: {$key}");
                return false;
            }

            // Validar tipo do valor
            $validatedValue = $this->validateValue($value, $definition->tipo);

            ConfigValue::updateOrCreate(
                [
                    'config_id' => $definition->id,
                    'empresa_id' => $empresa_id
                ],
                ['valor' => $validatedValue]
            );

            // Invalidar cache
            $this->invalidateCache($empresa_id, $key);

            Log::info("Configuração atualizada", [
                'key' => $key,
                'empresa_id' => $empresa_id,
                'value' => $validatedValue
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Erro ao definir configuração: {$key}", [
                'empresa_id' => $empresa_id,
                'value' => $value,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Busca múltiplas configurações por grupo
     */
    public function getGroup(string $groupCode, int $empresa_id = 1): array
    {
        $cacheKey = $this->getCacheKey($empresa_id, "group_{$groupCode}");

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($groupCode, $empresa_id) {
            return ConfigValue::query()
                ->join('config_definitions', 'config_values.config_id', '=', 'config_definitions.id')
                ->join('config_groups', 'config_definitions.grupo_id', '=', 'config_groups.id')
                ->where('config_groups.codigo', $groupCode)
                ->where('config_values.empresa_id', $empresa_id)
                ->pluck('config_values.valor', 'config_definitions.chave')
                ->toArray();
        });
    }

    /**
     * Busca configurações específicas de planos
     */
    public function getPlanConfig(): array
    {
        return [
            'basic_monthly' => $this->get('planos_basic_mensal', 1, 97.00),
            'premium_monthly' => $this->get('planos_premium_mensal', 1, 197.00),
            'enterprise_monthly' => $this->get('planos_enterprise_mensal', 1, 397.00),
            'annual_discount' => $this->get('planos_desconto_anual', 1, 16.67),
            'trial_days' => $this->get('planos_dias_trial', 1, 7),
            'grace_period' => $this->get('planos_grace_period', 1, 3),
        ];
    }

    /**
     * Busca configurações de afiliados
     */
    public function getAffiliateConfig(): array
    {
        return [
            'default_commission' => $this->get('afiliados_comissao_padrao', 1, 20.00),
            'bronze_commission' => $this->get('afiliados_comissao_bronze', 1, 25.00),
            'silver_commission' => $this->get('afiliados_comissao_prata', 1, 30.00),
            'gold_commission' => $this->get('afiliados_comissao_ouro', 1, 35.00),
            'minimum_withdrawal' => $this->get('afiliados_saque_minimo', 1, 100.00),
            'approval_days' => $this->get('afiliados_dias_aprovacao', 1, 15),
            'pix_enabled' => $this->get('afiliados_pix_ativo', 1, true),
            'ted_enabled' => $this->get('afiliados_ted_ativo', 1, true),
            'auto_approval' => $this->get('afiliados_registro_automatico', 1, false),
            'cookie_days' => $this->get('afiliados_cookie_dias', 1, 30),
        ];
    }

    /**
     * Busca configurações de comerciantes
     */
    public function getMerchantConfig(): array
    {
        return [
            'notification_days' => $this->get('comerciantes_dias_notificacao', 1, 7),
            'basic_user_limit' => $this->get('comerciantes_limite_usuarios_basic', 1, 3),
            'premium_user_limit' => $this->get('comerciantes_limite_usuarios_premium', 1, 10),
            'auto_suspend' => $this->get('comerciantes_suspensao_automatica', 1, true),
            'backup_retention' => $this->get('comerciantes_backup_retencao', 1, 90),
        ];
    }

    /**
     * Invalidar cache específico
     */
    public function invalidateCache(int $empresa_id, string $key = null): void
    {
        if ($key) {
            Cache::forget($this->getCacheKey($empresa_id, $key));
        } else {
            // Invalidar todo o cache da empresa
            $pattern = self::CACHE_PREFIX . ":{$empresa_id}:*";
            // Note: Em produção, usar Redis com pattern delete
            Cache::flush(); // Temporário - invalidar todo cache
        }
    }

    /**
     * Validar valor conforme tipo
     */
    private function validateValue($value, string $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'string':
            case 'text':
            default:
                return (string) $value;
        }
    }

    /**
     * Gerar chave de cache
     */
    private function getCacheKey(int $empresa_id, string $key): string
    {
        return self::CACHE_PREFIX . ":{$empresa_id}:{$key}";
    }

    /**
     * Verificar se sistema está configurado
     */
    public function isSystemConfigured(): bool
    {
        $essentialConfigs = [
            'safe2pay_token',
            'planos_basic_mensal',
            'afiliados_comissao_padrao'
        ];

        foreach ($essentialConfigs as $config) {
            if (!$this->get($config)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obter configurações para frontend
     */
    public function getPublicConfig(): array
    {
        return [
            'plans' => [
                'basic' => [
                    'price' => $this->get('planos_basic_mensal', 1, 97.00),
                    'features' => ['financeiro', 'relatorios_basicos']
                ],
                'premium' => [
                    'price' => $this->get('planos_premium_mensal', 1, 197.00),
                    'features' => ['financeiro', 'pdv', 'delivery']
                ],
                'enterprise' => [
                    'price' => $this->get('planos_enterprise_mensal', 1, 397.00),
                    'features' => ['financeiro', 'pdv', 'delivery', 'api']
                ]
            ],
            'trial_days' => $this->get('planos_dias_trial', 1, 7),
            'affiliate' => [
                'cookie_days' => $this->get('afiliados_cookie_dias', 1, 30)
            ]
        ];
    }
}
