<?php

namespace App\Services\Config;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Gerenciador de Configurações Multi-Empresa (Versão Simplificada)
 * 
 * Sistema de configuração que:
 * - Carrega configurações base do Laravel
 * - NUNCA consulta base online no modo offline
 * - Funciona mesmo sem as tabelas de configuração
 */
class ConfigManager
{
    private static ?self $instance = null;
    private ?int $currentEmpresaId = null;
    private array $cache = [];
    private bool $isOnlineMode = false;

    private function __construct()
    {
        $this->detectCurrentContext();
        $this->detectOnlineMode();
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
        if (Auth::check() && method_exists(Auth::user(), 'empresa_id')) {
            $this->currentEmpresaId = Auth::user()->empresa_id;
        }
    }

    /**
     * Detecta se está em modo online baseado no banco
     */
    private function detectOnlineMode(): void
    {
        try {
            $databaseName = DB::connection()->getDatabaseName();
            
            // Se o nome da base contém finanp06_, está online
            if (str_contains($databaseName, 'finanp06_')) {
                $this->isOnlineMode = true;
                Log::warning('ConfigManager: Modo ONLINE detectado - Base: ' . $databaseName);
                return;
            }
            
            $this->isOnlineMode = false;
            Log::info('ConfigManager: Modo OFFLINE detectado - Base: ' . $databaseName);
            
        } catch (Exception $e) {
            // Em caso de erro, assumir offline por segurança
            $this->isOnlineMode = false;
            Log::error('ConfigManager: Erro ao detectar modo, assumindo offline', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtém uma configuração
     */
    public function get(string $key, $default = null)
    {
        // Se estiver online, não carregar configurações dinâmicas
        if ($this->isOnlineMode) {
            return config($key, $default);
        }

        // Verificar cache local primeiro
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        // Tentar carregar do banco se offline e tabelas existirem
        if ($this->hasConfigTables()) {
            $value = $this->loadFromDatabase($key);
            if ($value !== null) {
                $this->cache[$key] = $value;
                return $value;
            }
        }

        // Fallback para configuração padrão do Laravel
        return config($key, $default);
    }

    /**
     * Define uma configuração (apenas em memória se online)
     */
    public function set(string $key, $value): bool
    {
        $this->cache[$key] = $value;
        
        // Se estiver online, não salvar no banco
        if ($this->isOnlineMode) {
            Log::info('ConfigManager: Set bloqueado (modo online)', ['key' => $key]);
            return false;
        }

        return true;
    }

    /**
     * Verifica se está em modo online
     */
    public function isOnlineMode(): bool
    {
        return $this->isOnlineMode;
    }

    /**
     * Obtém o ID da empresa atual
     */
    public function getCurrentEmpresaId(): ?int
    {
        return $this->currentEmpresaId;
    }

    /**
     * Define a empresa atual
     */
    public function setCurrentEmpresaId(?int $empresaId): self
    {
        $this->currentEmpresaId = $empresaId;
        $this->cache = []; // Limpar cache
        return $this;
    }

    /**
     * Verifica se as tabelas de configuração existem
     */
    public function hasConfigTables(): bool
    {
        try {
            return DB::getSchemaBuilder()->hasTable('config_definitions');
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Carrega configuração específica do banco
     */
    private function loadFromDatabase(string $key)
    {
        if ($this->isOnlineMode || !$this->currentEmpresaId) {
            return null;
        }

        try {
            $result = DB::table('config_definitions as cd')
                ->join('config_values as cv', 'cd.id', '=', 'cv.config_id')
                ->where('cv.empresa_id', $this->currentEmpresaId)
                ->where('cd.chave', $key)
                ->first(['cv.valor', 'cd.tipo']);

            if ($result) {
                return $this->castValue($result->valor, $result->tipo);
            }
        } catch (Exception $e) {
            Log::error('ConfigManager: Erro ao carregar do banco', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Converte valor para tipo correto
     */
    private function castValue($value, $type)
    {
        return match($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'array', 'json' => json_decode($value, true) ?: [],
            default => $value
        };
    }

    /**
     * Carrega configurações do banco se estiver offline
     */
    public function loadFromDatabase(): bool
    {
        if ($this->isOnlineMode) {
            Log::info('ConfigManager: Carregamento pulado - modo online');
            return false;
        }

        if (!$this->hasConfigTables()) {
            Log::info('ConfigManager: Tabelas não encontradas');
            return false;
        }

        return true;
    }

    /**
     * Obtém ambiente atual (simulado)
     */
    public function getCurrentEnvironment(): ?array
    {
        return [
            'nome' => $this->isOnlineMode ? 'Online' : 'Offline',
            'codigo' => $this->isOnlineMode ? 'online' : 'offline',
            'is_producao' => $this->isOnlineMode
        ];
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

        // Helper para JSON que pode já estar decodificado
        $modulesData = $this->get("{$clientPrefix}modules", '[]');
        $modules = is_string($modulesData) ? json_decode($modulesData, true) : $modulesData;
        if (!is_array($modules)) {
            $modules = [];
        }

        $clientConfig = [
            'id' => $clientEmpresaId,
            'nome' => $this->get("{$clientPrefix}nome", "Cliente {$clientEmpresaId}"),
            'plano' => $this->get("{$clientPrefix}plano", "standard"),
            'ativo' => (bool)$this->get("{$clientPrefix}ativo", true),
            'max_usuarios' => (int)$this->get("{$clientPrefix}usuarios_max", 5),
            'data_expiracao' => $this->get("{$clientPrefix}data_expiracao", date('Y-m-d', strtotime('+1 year'))),
            'trial_days' => (int)$this->get("{$clientPrefix}trial_days", 0),
            'modules' => $modules
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

    /**
     * Verifica se está em modo online (conectado à base finanp06_*)
     * 
     * @return bool
     */
    public function isOnlineMode(): bool
    {
        try {
            $databaseName = DB::connection()->getDatabaseName();
            
            // Se o nome da base contém finanp06_, está online
            if (str_contains($databaseName, 'finanp06_')) {
                return true;
            }
            
            // Verificar se consegue acessar uma tabela característica da base online
            if (DB::getSchemaBuilder()->hasTable('planos_licenca')) {
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            // Em caso de erro, assumir offline por segurança
            return false;
        }
    }

    /**
     * Obtém o ID da empresa atual
     * 
     * @return int|null
     */
    public function getCurrentEmpresaId(): ?int
    {
        return $this->currentEmpresaId;
    }

    /**
     * Define a empresa atual
     * 
     * @param int|null $empresaId
     * @return self
     */
    public function setCurrentEmpresaId(?int $empresaId): self
    {
        $this->currentEmpresaId = $empresaId;
        $this->clearCache();
        return $this;
    }

    /**
     * Verifica se as tabelas de configuração existem
     * 
     * @return bool
     */
    public function hasConfigTables(): bool
    {
        try {
            return DB::getSchemaBuilder()->hasTable('config_definitions');
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Carrega configurações do banco se estiver offline
     * 
     * @return bool
     */
    public function loadFromDatabase(): bool
    {
        // NUNCA carregar do banco se estivermos online
        if ($this->isOnlineMode()) {
            Log::info('ConfigManager: Pulando carregamento - modo online detectado');
            return false;
        }

        // Verificar se as tabelas existem
        if (!$this->hasConfigTables()) {
            Log::info('ConfigManager: Tabelas de configuração não encontradas');
            return false;
        }

        try {
            // Carregar configurações básicas do banco
            $this->loadBasicConfigs();
            return true;
        } catch (Exception $e) {
            Log::error('ConfigManager: Erro ao carregar configurações do banco', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Carrega configurações básicas do banco
     */
    private function loadBasicConfigs(): void
    {
        if (!$this->currentEmpresaId) {
            return;
        }

        // Carregar todas as configurações da empresa atual
        $configs = DB::table('config_definitions as cd')
            ->join('config_values as cv', 'cd.id', '=', 'cv.config_id')
            ->where('cv.empresa_id', $this->currentEmpresaId)
            ->select('cd.chave', 'cv.valor', 'cd.tipo')
            ->get();

        foreach ($configs as $config) {
            $value = $this->castConfigValue($config->valor, $config->tipo);
            $this->cache[$this->getCacheKey($config->chave)] = $value;
        }
    }

    /**
     * Converte valor de configuração para o tipo correto
     */
    private function castConfigValue($value, $type): mixed
    {
        return match($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'array', 'json' => json_decode($value, true) ?: [],
            default => $value
        };
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
