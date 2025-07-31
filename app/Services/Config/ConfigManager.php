<?php

namespace App\Services\Config;

use App\Models\Config\{
    ConfigDefinition,
    ConfigEnvironment,
    ConfigGroup,
    ConfigHistory,
    ConfigSite,
    ConfigValue
};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Serviço principal para gerenciamento de configurações
 * 
 * Implementa o padrão de configurações hierárquicas:
 * 1. Configurações específicas para site e ambiente
 * 2. Configurações específicas para site (todos ambientes)
 * 3. Configurações específicas para ambiente (todos sites)
 * 4. Configurações globais (todos sites e ambientes)
 */
class ConfigManager
{
    private static ?self $instance = null;
    private ?int $currentEmpresaId = null;
    private ?int $currentSiteId = null;
    private ?int $currentEnvironmentId = null;
    private array $cache = [];
    private bool $cacheEnabled = true;
    private int $cacheExpiry = 300; // 5 minutos

    // Constante para empresa desenvolvedora
    private const DEVELOPER_COMPANY_ID = 1; // ID da empresa desenvolvedora

    private function __construct()
    {
        $this->detectCurrentContext();
    }

    /**
     * Singleton pattern
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Detecta o contexto atual (empresa, site, ambiente)
     */
    private function detectCurrentContext(): void
    {
        // Detectar empresa do usuário logado
        if (Auth::check() && Auth::user()->empresa_id) {
            $this->currentEmpresaId = Auth::user()->empresa_id;
        }

        // Detectar ambiente baseado no domínio
        $serverName = request()->getHost();
        $isLocal = in_array($serverName, ['localhost', '127.0.0.1', '::1']);

        $environment = ConfigEnvironment::where('empresa_id', $this->currentEmpresaId)
            ->where('codigo', $isLocal ? 'offline' : 'online')
            ->first();

        if ($environment) {
            $this->currentEnvironmentId = $environment->id;
        }

        // Detectar site baseado na URL
        $path = request()->path();
        $site = ConfigSite::where('empresa_id', $this->currentEmpresaId)
            ->where(function ($query) use ($path) {
                $query->where('codigo', 'sistema'); // padrão
                if (str_contains($path, 'pdv')) {
                    $query->orWhere('codigo', 'pdv');
                }
                if (str_contains($path, 'fidelidade')) {
                    $query->orWhere('codigo', 'fidelidade');
                }
                if (str_contains($path, 'delivery')) {
                    $query->orWhere('codigo', 'delivery');
                }
            })
            ->first();

        if ($site) {
            $this->currentSiteId = $site->id;
        }
    }

    /**
     * Verifica se o contexto atual é da empresa desenvolvedora
     */
    public function isDeveloperCompany(): bool
    {
        return $this->currentEmpresaId === self::DEVELOPER_COMPANY_ID;
    }

    /**
     * Alterna para o contexto da empresa desenvolvedora
     */
    public function useDeveloperContext(): self
    {
        $originalContext = $this->getCurrentContext();

        // Armazenar contexto original para possível restauração posterior
        if (!$this->isDeveloperCompany()) {
            session(['original_config_context' => $originalContext]);
        }

        $this->setContext(self::DEVELOPER_COMPANY_ID);
        return $this;
    }

    /**
     * Restaura o contexto original (não-desenvolvedor)
     */
    public function restoreOriginalContext(): self
    {
        $originalContext = session('original_config_context');

        if ($originalContext) {
            $this->setContext(
                $originalContext['empresa_id'],
                $originalContext['site_id'],
                $originalContext['environment_id']
            );
            session()->forget('original_config_context');
        }

        return $this;
    }

    /**
     * Obtém configurações de um cliente específico
     * 
     * @param int $clientEmpresaId ID da empresa cliente
     * @return array Configurações do cliente
     */
    public function getClientConfig(int $clientEmpresaId): array
    {
        // Salvar contexto atual
        $currentContext = $this->getCurrentContext();

        // Alternar para contexto da desenvolvedora
        $this->useDeveloperContext();

        // Buscar configurações do cliente
        $clientPrefix = "cliente_{$clientEmpresaId}_";
        $clientConfig = [
            'id' => $clientEmpresaId,
            'nome' => $this->get("{$clientPrefix}nome", "Cliente {$clientEmpresaId}"),
            'plano' => $this->get("{$clientPrefix}plano", "standard"),
            'ativo' => (bool)$this->get("{$clientPrefix}ativo", true),
            'max_usuarios' => (int)$this->get("{$clientPrefix}usuarios_max", 5),
            'data_expiracao' => $this->get("{$clientPrefix}data_expiracao", date('Y-m-d', strtotime('+1 year'))),
            'trial_days' => (int)$this->get("{$clientPrefix}trial_days", 0),
            'modules' => json_decode($this->get("{$clientPrefix}modules", '[]'), true)
        ];

        // Calcular dias restantes da licença
        $expDate = new \DateTime($clientConfig['data_expiracao']);
        $now = new \DateTime();
        $clientConfig['days_remaining'] = $now > $expDate ? 0 : $now->diff($expDate)->days;

        // Restaurar contexto anterior
        $this->setContext(
            $currentContext['empresa_id'],
            $currentContext['site_id'],
            $currentContext['environment_id']
        );

        return $clientConfig;
    }

    /**
     * Verifica se um cliente específico está ativo
     * 
     * @param int $clientEmpresaId ID da empresa cliente
     * @return bool True se o cliente está ativo
     */
    public function isClientActive(int $clientEmpresaId): bool
    {
        $clientConfig = $this->getClientConfig($clientEmpresaId);
        return $clientConfig['ativo'] && ($clientConfig['days_remaining'] > 0 || $clientConfig['trial_days'] > 0);
    }

    /**
     * Obtém uma configuração seguindo a hierarquia
     */
    public function get(string $key, $default = null)
    {
        if (!$this->currentEmpresaId) {
            return $default;
        }

        $cacheKey = $this->getCacheKey($key);

        if ($this->cacheEnabled && isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $value = $this->loadConfigValue($key);

        if ($this->cacheEnabled) {
            $this->cache[$cacheKey] = $value;
        }

        return $value ?? $default;
    }

    /**
     * Define uma configuração
     */
    public function set(
        string $key,
        $value,
        ?int $siteId = null,
        ?int $environmentId = null,
        string $grupo = 'geral',
        string $tipo = 'string',
        ?string $descricao = null
    ): bool {
        if (!$this->currentEmpresaId) {
            return false;
        }

        try {
            // Buscar ou criar definição
            $definition = $this->getOrCreateDefinition($key, $grupo, $tipo, $descricao);

            if (!$definition) {
                return false;
            }

            // Buscar valor existente
            $configValue = ConfigValue::where('empresa_id', $this->currentEmpresaId)
                ->where('config_id', $definition->id)
                ->where('site_id', $siteId)
                ->where('ambiente_id', $environmentId)
                ->first();

            $oldValue = $configValue?->valor;
            $newValue = ConfigValue::prepareForStorage($value, $tipo);

            if ($configValue) {
                $configValue->update(['valor' => $newValue]);
            } else {
                $configValue = ConfigValue::create([
                    'empresa_id' => $this->currentEmpresaId,
                    'config_id' => $definition->id,
                    'site_id' => $siteId,
                    'ambiente_id' => $environmentId,
                    'valor' => $newValue,
                ]);
            }

            // Registrar histórico
            ConfigHistory::recordChange(
                $this->currentEmpresaId,
                $configValue->id,
                $oldValue,
                $newValue,
                'Alteração via ConfigManager'
            );

            // Limpar cache
            $this->clearCacheKey($key);

            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao definir configuração', [
                'key' => $key,
                'value' => $value,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Carrega o valor de uma configuração seguindo hierarquia
     */
    private function loadConfigValue(string $key)
    {
        $definition = ConfigDefinition::where('empresa_id', $this->currentEmpresaId)
            ->where('chave', $key)
            ->first();

        if (!$definition) {
            return null;
        }

        // Buscar valor seguindo hierarquia de prioridade
        $value = ConfigValue::where('empresa_id', $this->currentEmpresaId)
            ->where('config_id', $definition->id)
            ->where(function ($query) {
                $query
                    // 1. Específico para site e ambiente atual
                    ->where(function ($q) {
                        $q->where('site_id', $this->currentSiteId)
                            ->where('ambiente_id', $this->currentEnvironmentId);
                    })
                    // 2. Específico para site atual (todos ambientes)
                    ->orWhere(function ($q) {
                        $q->where('site_id', $this->currentSiteId)
                            ->whereNull('ambiente_id');
                    })
                    // 3. Específico para ambiente atual (todos sites)
                    ->orWhere(function ($q) {
                        $q->whereNull('site_id')
                            ->where('ambiente_id', $this->currentEnvironmentId);
                    })
                    // 4. Global (todos sites e ambientes)
                    ->orWhere(function ($q) {
                        $q->whereNull('site_id')
                            ->whereNull('ambiente_id');
                    });
            })
            ->orderByRaw('
                CASE 
                    WHEN site_id = ? AND ambiente_id = ? THEN 1
                    WHEN site_id = ? AND ambiente_id IS NULL THEN 2
                    WHEN site_id IS NULL AND ambiente_id = ? THEN 3
                    WHEN site_id IS NULL AND ambiente_id IS NULL THEN 4
                    ELSE 5
                END
            ', [
                $this->currentSiteId,
                $this->currentEnvironmentId,
                $this->currentSiteId,
                $this->currentEnvironmentId
            ])
            ->first();

        if ($value) {
            return $definition->castValue($value->valor);
        }

        // Retornar valor padrão da definição
        return $definition->getDefaultValue();
    }

    /**
     * Busca ou cria uma definição de configuração
     */
    private function getOrCreateDefinition(
        string $key,
        string $grupo,
        string $tipo,
        ?string $descricao
    ): ?ConfigDefinition {
        $definition = ConfigDefinition::where('empresa_id', $this->currentEmpresaId)
            ->where('chave', $key)
            ->first();

        if ($definition) {
            return $definition;
        }

        // Buscar ou criar grupo
        $group = ConfigGroup::where('empresa_id', $this->currentEmpresaId)
            ->where('codigo', $grupo)
            ->first();

        if (!$group) {
            $group = ConfigGroup::create([
                'empresa_id' => $this->currentEmpresaId,
                'codigo' => $grupo,
                'nome' => ucfirst($grupo),
                'ordem' => 999,
            ]);
        }

        // Criar definição
        return ConfigDefinition::create([
            'empresa_id' => $this->currentEmpresaId,
            'chave' => $key,
            'descricao' => $descricao,
            'tipo' => $tipo,
            'grupo_id' => $group->id,
        ]);
    }

    /**
     * Obtém todas as configurações de um grupo
     */
    public function getGroup(string $groupCode): Collection
    {
        if (!$this->currentEmpresaId) {
            return collect();
        }

        $group = ConfigGroup::where('empresa_id', $this->currentEmpresaId)
            ->where('codigo', $groupCode)
            ->first();

        if (!$group) {
            return collect();
        }

        $definitions = ConfigDefinition::where('empresa_id', $this->currentEmpresaId)
            ->where('grupo_id', $group->id)
            ->orderBy('ordem')
            ->orderBy('chave')
            ->get();

        return $definitions->mapWithKeys(function ($definition) {
            return [$definition->chave => $this->get($definition->chave)];
        });
    }

    /**
     * Limpa todo o cache
     */
    public function clearCache(): void
    {
        $this->cache = [];

        if ($this->cacheEnabled) {
            Cache::forget($this->getCachePrefix() . '*');
        }
    }

    /**
     * Limpa cache de uma chave específica
     */
    public function clearCacheKey(string $key): void
    {
        $cacheKey = $this->getCacheKey($key);
        unset($this->cache[$cacheKey]);

        if ($this->cacheEnabled) {
            Cache::forget($cacheKey);
        }
    }

    /**
     * Gera chave de cache
     */
    private function getCacheKey(string $key): string
    {
        return $this->getCachePrefix() . $key;
    }

    /**
     * Gera prefixo de cache baseado no contexto atual
     */
    private function getCachePrefix(): string
    {
        return sprintf(
            'config_%d_%d_%d_',
            $this->currentEmpresaId ?? 0,
            $this->currentSiteId ?? 0,
            $this->currentEnvironmentId ?? 0
        );
    }

    /**
     * Define contexto manual (útil para testes ou casos específicos)
     */
    public function setContext(?int $empresaId, ?int $siteId = null, ?int $environmentId = null): void
    {
        $this->currentEmpresaId = $empresaId;
        $this->currentSiteId = $siteId;
        $this->currentEnvironmentId = $environmentId;
        $this->clearCache();
    }

    /**
     * Obtém o contexto atual
     */
    public function getCurrentContext(): array
    {
        return [
            'empresa_id' => $this->currentEmpresaId,
            'site_id' => $this->currentSiteId,
            'environment_id' => $this->currentEnvironmentId,
        ];
    }

    /**
     * Habilita/desabilita cache
     */
    public function setCacheEnabled(bool $enabled): void
    {
        $this->cacheEnabled = $enabled;
        if (!$enabled) {
            $this->clearCache();
        }
    }
}

/**
 * Helper function para facilitar o acesso
 */
function config_marketplace(?string $key = null, $default = null)
{
    $manager = ConfigManager::getInstance();

    if ($key === null) {
        return $manager;
    }

    return $manager->get($key, $default);
}
