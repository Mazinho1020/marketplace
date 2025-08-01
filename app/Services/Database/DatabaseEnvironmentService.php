<?php

namespace App\Services\Database;

use App\Models\Config\ConfigEnvironment;
use App\Models\Config\ConfigDbConnection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

/**
 * Service para gerenciamento dinâmico de conexões de banco
 * Usa funcionalidades nativas do Laravel
 */
class DatabaseEnvironmentService
{
    private static ?self $instance = null;
    private ?string $currentEnvironment = null;
    private ?array $currentConfig = null;
    private bool $configurationLoaded = false;

    private function __construct()
    {
        $this->detectEnvironment();
        // Não carregar configuração do banco no constructor para evitar problemas
        // A configuração será carregada quando necessário
    }

    /**
     * Singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Detecta o ambiente atual usando Laravel
     */
    private function detectEnvironment(): void
    {
        // Usar APP_ENV do Laravel - modo mais seguro
        $appEnv = env('APP_ENV', 'local');

        // Detectores adicionais
        $isLocal = $this->isLocalEnvironment();
        $isDevelopment = in_array($appEnv, ['local', 'development', 'dev', 'testing']);

        // Mapear para códigos da tabela
        if ($isDevelopment || $isLocal) {
            $this->currentEnvironment = 'desenvolvimento';
        } else {
            $this->currentEnvironment = 'producao';
        }

        // Log só se o Laravel estiver disponível
        if (class_exists(\Illuminate\Support\Facades\Log::class)) {
            try {
                Log::info("DatabaseEnvironmentService: Ambiente detectado", [
                    'app_env' => $appEnv,
                    'is_local' => $isLocal,
                    'mapped_environment' => $this->currentEnvironment
                ]);
            } catch (\Exception $e) {
                // Ignorar erro de log se Laravel não estiver pronto
            }
        }
    }

    /**
     * Verifica se é ambiente local
     */
    private function isLocalEnvironment(): bool
    {
        $indicators = [
            'localhost' => [
                $_SERVER['HTTP_HOST'] ?? null,
                $_SERVER['SERVER_NAME'] ?? null,
                gethostname(),
            ],
            '127.0.0.1' => [
                $_SERVER['SERVER_ADDR'] ?? null,
                $_SERVER['REMOTE_ADDR'] ?? null,
            ],
            'local_patterns' => [
                str_contains(php_uname('n'), 'DESKTOP'),
                str_contains(php_uname('n'), 'laptop'),
                str_contains(getcwd(), 'xampp'),
                str_contains(getcwd(), 'laragon'),
            ]
        ];

        foreach ($indicators as $type => $values) {
            foreach ($values as $value) {
                if ($value && (
                    str_contains(strtolower($value), 'localhost') ||
                    str_contains($value, '127.0.0.1') ||
                    ($type === 'local_patterns' && $value === true)
                )) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Carrega configuração de banco usando Eloquent
     */
    private function loadDatabaseConfiguration(): void
    {
        try {
            // Usar cache para performance - só se Laravel estiver disponível
            $cacheKey = "db_config_{$this->currentEnvironment}";

            if (class_exists(\Illuminate\Support\Facades\Cache::class)) {
                try {
                    $this->currentConfig = Cache::remember($cacheKey, 300, function () {
                        return $this->fetchDatabaseConfigFromDb();
                    });
                } catch (\Exception $e) {
                    // Se cache falhar, buscar diretamente
                    $this->currentConfig = $this->fetchDatabaseConfigFromDb();
                }
            } else {
                $this->currentConfig = $this->fetchDatabaseConfigFromDb();
            }

            if ($this->currentConfig) {
                $this->applyDatabaseConfiguration();
                $this->configurationLoaded = true;
            }
        } catch (Exception $e) {
            // Log só se disponível
            if (class_exists(\Illuminate\Support\Facades\Log::class)) {
                try {
                    Log::error("DatabaseEnvironmentService: Erro ao carregar configuração", [
                        'environment' => $this->currentEnvironment,
                        'error' => $e->getMessage()
                    ]);
                } catch (\Exception $logError) {
                    // Ignorar erro de log
                }
            }

            // Usar configuração padrão
            $this->useDefaultConfiguration();
        }
    }

    /**
     * Busca configuração do banco usando Eloquent
     */
    private function fetchDatabaseConfigFromDb(): ?array
    {
        try {
            // Verificar se Laravel/Eloquent está disponível
            if (!class_exists(\App\Models\Config\ConfigEnvironment::class)) {
                return $this->fetchDatabaseConfigWithPDO();
            }

            // Tentar com Eloquent
            try {
                // Buscar ambiente
                $environment = \App\Models\Config\ConfigEnvironment::active()
                    ->byCode($this->currentEnvironment)
                    ->first();

                if (!$environment) {
                    Log::warning("DatabaseEnvironmentService: Ambiente não encontrado", [
                        'environment_code' => $this->currentEnvironment
                    ]);
                    return $this->fetchDatabaseConfigWithPDO();
                }

                // Buscar conexão padrão
                $dbConnection = $environment->defaultDbConnection()->first();

                if (!$dbConnection) {
                    Log::warning("DatabaseEnvironmentService: Conexão padrão não encontrada", [
                        'environment_id' => $environment->id
                    ]);
                    return $this->fetchDatabaseConfigWithPDO();
                }

                return [
                    'environment_id' => $environment->id,
                    'environment_name' => $environment->nome,
                    'connection_name' => $dbConnection->nome,
                    'driver' => $dbConnection->driver,
                    'host' => $dbConnection->host,
                    'port' => $dbConnection->porta,
                    'database' => $dbConnection->banco,
                    'username' => $dbConnection->usuario,
                    'password' => $this->decryptPassword($dbConnection->senha),
                    'charset' => $dbConnection->charset,
                    'collation' => $dbConnection->collation,
                    'prefix' => $dbConnection->prefixo ?? '',
                ];
            } catch (\Exception $e) {
                // Se Eloquent falhar, usar PDO
                return $this->fetchDatabaseConfigWithPDO();
            }
        } catch (Exception $e) {
            if (class_exists(\Illuminate\Support\Facades\Log::class)) {
                try {
                    Log::error("DatabaseEnvironmentService: Erro na consulta", [
                        'error' => $e->getMessage()
                    ]);
                } catch (\Exception $logError) {
                    // Ignorar erro de log
                }
            }
            return $this->fetchDatabaseConfigWithPDO();
        }
    }

    /**
     * Busca configuração usando PDO direto (fallback)
     */
    private function fetchDatabaseConfigWithPDO(): ?array
    {
        try {
            // Usar configuração do .env para conectar inicialmente
            $pdo = new \PDO(
                "mysql:host=" . env('DB_HOST', '127.0.0.1') . ";port=" . env('DB_PORT', 3306) . ";dbname=" . env('DB_DATABASE', 'meufinanceiro'),
                env('DB_USERNAME', 'root'),
                env('DB_PASSWORD', ''),
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );

            // Buscar ambiente
            $stmt = $pdo->prepare("
                SELECT id, nome, codigo, descricao 
                FROM config_environments 
                WHERE codigo = :codigo AND ativo = 1 
                LIMIT 1
            ");
            $stmt->execute(['codigo' => $this->currentEnvironment]);
            $env = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$env) {
                return null;
            }

            // Buscar conexão padrão para o ambiente
            $stmt = $pdo->prepare("
                SELECT * 
                FROM config_db_connections 
                WHERE environment_id = :env_id AND padrao = 1 AND ativo = 1 
                LIMIT 1
            ");
            $stmt->execute(['env_id' => $env['id']]);
            $connection = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$connection) {
                return null;
            }

            // Descriptografar senha se necessário
            $password = $this->decryptPassword($connection['senha']);

            return [
                'environment_id' => $env['id'],
                'environment_name' => $env['nome'],
                'connection_name' => $connection['nome'],
                'driver' => $connection['driver'],
                'host' => $connection['host'],
                'port' => $connection['porta'],
                'database' => $connection['banco'],
                'username' => $connection['usuario'],
                'password' => $password,
                'charset' => $connection['charset'],
                'collation' => $connection['collation'],
                'prefix' => $connection['prefixo'] ?? '',
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Aplica configuração usando Config do Laravel
     */
    private function applyDatabaseConfiguration(): void
    {
        $laravelConfig = [
            'driver' => $this->currentConfig['driver'],
            'url' => null,
            'host' => $this->currentConfig['host'],
            'port' => $this->currentConfig['port'],
            'database' => $this->currentConfig['database'],
            'username' => $this->currentConfig['username'],
            'password' => $this->currentConfig['password'],
            'unix_socket' => '',
            'charset' => $this->currentConfig['charset'],
            'collation' => $this->currentConfig['collation'],
            'prefix' => $this->currentConfig['prefix'],
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                \PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ];

        // Aplicar configuração - só se Laravel estiver disponível
        if (class_exists(\Illuminate\Support\Facades\Config::class)) {
            try {
                Config::set('database.connections.mysql', $laravelConfig);

                // Purgar conexões existentes
                if (class_exists(\Illuminate\Support\Facades\DB::class)) {
                    DB::purge('mysql');
                }
            } catch (\Exception $e) {
                // Ignorar se Laravel não estiver disponível
            }
        }

        // Log só se disponível
        if (class_exists(\Illuminate\Support\Facades\Log::class)) {
            try {
                Log::info("DatabaseEnvironmentService: Configuração aplicada", [
                    'connection_name' => $this->currentConfig['connection_name'],
                    'host' => $this->currentConfig['host'],
                    'database' => $this->currentConfig['database'],
                ]);
            } catch (\Exception $e) {
                // Ignorar erro de log
            }
        }
    }

    /**
     * Usar configuração padrão do .env
     */
    private function useDefaultConfiguration(): void
    {
        $this->currentConfig = [
            'environment_name' => 'Padrão (.env)',
            'connection_name' => 'Configuração Padrão',
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 3306),
            'database' => env('DB_DATABASE', 'meufinanceiro'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ];

        Log::info("DatabaseEnvironmentService: Usando configuração padrão");
    }

    /**
     * Descriptografa senha
     */
    private function decryptPassword(string $encryptedPassword): string
    {
        try {
            if (str_starts_with($encryptedPassword, 'encrypted:')) {
                return decrypt(substr($encryptedPassword, 10));
            }

            if ($encryptedPassword === 'encrypted_empty_password_here') {
                return '';
            }

            // Tentar descriptografar diretamente
            return decrypt($encryptedPassword);
        } catch (Exception $e) {
            Log::warning("DatabaseEnvironmentService: Erro ao descriptografar senha", [
                'error' => $e->getMessage()
            ]);

            // Se falhar, usar valor original
            return str_replace('encrypted:', '', $encryptedPassword);
        }
    }

    /**
     * Testa conexão atual
     */
    public function testConnection(): bool
    {
        try {
            // Carregar configuração se não foi carregada
            if (!$this->configurationLoaded) {
                $this->loadDatabaseConfiguration();
            }

            // Tentar usando Laravel DB se disponível
            if (class_exists(\Illuminate\Support\Facades\DB::class)) {
                try {
                    DB::connection()->getPdo();
                    $database = DB::connection()->getDatabaseName();

                    if (class_exists(\Illuminate\Support\Facades\Log::class)) {
                        Log::info("DatabaseEnvironmentService: Teste de conexão bem-sucedido", [
                            'database' => $database
                        ]);
                    }

                    return true;
                } catch (\Exception $e) {
                    // Se falhar, tentar PDO direto
                }
            }

            // Fallback: teste direto com PDO
            if ($this->currentConfig) {
                $pdo = new \PDO(
                    "mysql:host={$this->currentConfig['host']};port={$this->currentConfig['port']};dbname={$this->currentConfig['database']}",
                    $this->currentConfig['username'],
                    $this->currentConfig['password']
                );
                return true;
            }

            return false;
        } catch (Exception $e) {
            if (class_exists(\Illuminate\Support\Facades\Log::class)) {
                try {
                    Log::error("DatabaseEnvironmentService: Falha no teste de conexão", [
                        'error' => $e->getMessage()
                    ]);
                } catch (\Exception $logError) {
                    // Ignorar erro de log
                }
            }
            return false;
        }
    }

    /**
     * Recarrega configuração (limpa cache)
     */
    public function reloadConfiguration(): void
    {
        if (class_exists(\Illuminate\Support\Facades\Cache::class)) {
            try {
                Cache::forget("db_config_{$this->currentEnvironment}");
            } catch (\Exception $e) {
                // Ignorar se cache não estiver disponível
            }
        }

        $this->configurationLoaded = false;
        $this->loadDatabaseConfiguration();
    }

    /**
     * Obtém informações de debug
     */
    public function getDebugInfo(): array
    {
        $connectionTest = $this->testConnection();
        $currentDatabase = null;

        try {
            $currentDatabase = DB::connection()->getDatabaseName();
        } catch (Exception $e) {
            // Ignorar se não conseguir conectar
        }

        return [
            'environment' => $this->currentEnvironment,
            'configuration_loaded' => $this->configurationLoaded,
            'connection_test' => $connectionTest,
            'current_database' => $currentDatabase,
            'config' => $this->currentConfig,
            'detection_info' => [
                'app_env' => env('APP_ENV'),
                'is_local' => $this->isLocalEnvironment(),
                'hostname' => gethostname(),
                'request_host' => $_SERVER['HTTP_HOST'] ?? null,
                'server_addr' => $_SERVER['SERVER_ADDR'] ?? null,
                'cwd' => getcwd(),
            ]
        ];
    }

    /**
     * Obtém configuração atual
     */
    public function getConfig(): ?array
    {
        if (!$this->configurationLoaded) {
            $this->loadDatabaseConfiguration();
        }
        return $this->currentConfig;
    }

    /**
     * Obtém ambiente atual
     */
    public function getCurrentEnvironment(): string
    {
        return $this->currentEnvironment;
    }
}
