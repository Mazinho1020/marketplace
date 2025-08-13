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
    private bool $cacheEnabled = true;

    // Constante para empresa desenvolvedora
    const DEVELOPER_COMPANY_ID = 1;

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
            $value = $this->loadFromDatabasePrivate($key);
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
    private function loadFromDatabasePrivate(string $key)
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
        return match ($type) {
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

    /**
     * Verifica se o contexto atual é da empresa desenvolvedora
     */
    public function isDeveloperCompany(): bool
    {
        return $this->currentEmpresaId === self::DEVELOPER_COMPANY_ID;
    }

    /**
     * Obtém o contexto atual
     */
    public function getCurrentContext(): array
    {
        return [
            'empresa_id' => $this->currentEmpresaId,
            'site_id' => null,
            'environment_id' => null,
        ];
    }

    /**
     * Define contexto manual
     */
    public function setContext(?int $empresaId, ?int $siteId = null, ?int $environmentId = null): void
    {
        $this->currentEmpresaId = $empresaId;
        $this->clearCache();
    }

    /**
     * Limpa todo o cache
     */
    public function clearCache(): void
    {
        $this->cache = [];
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
