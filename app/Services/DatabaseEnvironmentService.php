<?php

namespace App\Services;

use PDO;
use Exception;

/**
 * Serviço simples e robusto para detecção de ambiente e configuração de banco
 */
class DatabaseEnvironmentService
{
    private static $instance = null;
    private $environment = null;
    private $config = null;

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

    private function __construct()
    {
        $this->detectEnvironment();
        $this->loadConfig();
    }

    /**
     * Detecta se está em desenvolvimento ou produção
     */
    private function detectEnvironment(): void
    {
        // Métodos de detecção simples e confiável
        $indicators = [
            // Se está rodando em localhost/127.0.0.1
            ($_SERVER['SERVER_NAME'] ?? '') === 'localhost',
            ($_SERVER['HTTP_HOST'] ?? '') === 'localhost',
            str_contains($_SERVER['HTTP_HOST'] ?? '', 'localhost'),
            ($_SERVER['REMOTE_ADDR'] ?? '') === '127.0.0.1',
            ($_SERVER['SERVER_ADDR'] ?? '') === '127.0.0.1',

            // Se é Windows (normalmente desenvolvimento)
            str_contains(strtoupper(PHP_OS), 'WIN'),

            // Se tem APP_ENV=local no .env
            $this->getEnvVar('APP_ENV') === 'local',
        ];

        // Se qualquer indicador for verdadeiro, é desenvolvimento
        $this->environment = in_array(true, $indicators, true) ? 'desenvolvimento' : 'producao';
    }

    /**
     * Carrega configuração do ambiente detectado
     */
    private function loadConfig(): void
    {
        if ($this->environment === 'desenvolvimento') {
            // Configuração fixa para desenvolvimento
            $this->config = [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => 3306,
                'database' => 'meufinanceiro',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
            ];
        } else {
            // Para produção, tenta carregar da tabela de configuração
            $this->config = $this->loadProductionConfig();
        }
    }

    /**
     * Carrega configuração de produção da tabela
     */
    private function loadProductionConfig(): array
    {
        try {
            // Conecta ao banco local primeiro para buscar configuração de produção
            $localPdo = new PDO(
                'mysql:host=127.0.0.1;dbname=meufinanceiro;charset=utf8mb4',
                'root',
                '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Busca ambiente de produção
            $stmt = $localPdo->prepare("
                SELECT id FROM config_environments 
                WHERE codigo = 'producao' AND ativo = 1 
                LIMIT 1
            ");
            $stmt->execute();
            $ambiente = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$ambiente) {
                throw new Exception('Ambiente de produção não encontrado');
            }

            // Busca conexão padrão de produção
            $stmt = $localPdo->prepare("
                SELECT * FROM config_db_connections 
                WHERE ambiente_id = ? AND padrao = 1 
                LIMIT 1
            ");
            $stmt->execute([$ambiente['id']]);
            $connection = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$connection) {
                throw new Exception('Conexão de produção não encontrada');
            }

            return [
                'driver' => $connection['driver'],
                'host' => $connection['host'],
                'port' => (int)$connection['porta'],
                'database' => $connection['banco'],
                'username' => $connection['usuario'],
                'password' => $this->decryptPassword($connection['senha']),
                'charset' => $connection['charset'],
                'collation' => $connection['collation'],
                'prefix' => $connection['prefixo'] ?? '',
            ];
        } catch (Exception $e) {
            // Se falhar, usa configuração de fallback
            return [
                'driver' => 'mysql',
                'host' => $this->getEnvVar('DB_HOST', '127.0.0.1'),
                'port' => (int)$this->getEnvVar('DB_PORT', 3306),
                'database' => $this->getEnvVar('DB_DATABASE', 'meufinanceiro'),
                'username' => $this->getEnvVar('DB_USERNAME', 'root'),
                'password' => $this->getEnvVar('DB_PASSWORD', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
            ];
        }
    }

    /**
     * Descriptografa senha
     */
    private function decryptPassword(string $encryptedPassword): string
    {
        if (str_starts_with($encryptedPassword, 'encrypted:')) {
            return substr($encryptedPassword, 10);
        }

        if ($encryptedPassword === 'encrypted_empty_password_here') {
            return '';
        }

        return $encryptedPassword;
    }

    /**
     * Lê variável do .env
     */
    private function getEnvVar(string $key, $default = null)
    {
        static $envVars = null;

        if ($envVars === null) {
            $envVars = [];
            $envFile = dirname(__DIR__, 2) . '/.env';

            if (file_exists($envFile)) {
                $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
                        continue;
                    }
                    [$k, $v] = explode('=', trim($line), 2);
                    $envVars[trim($k)] = trim($v, '"');
                }
            }
        }

        return $envVars[$key] ?? $default;
    }

    /**
     * Retorna configuração atual
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Retorna ambiente atual
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Testa a conexão
     */
    public function testConnection(): bool
    {
        try {
            $config = $this->config;
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            $pdo = new PDO($dsn, $config['username'], $config['password']);
            $pdo->query('SELECT 1');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Retorna informações de debug
     */
    public function getDebugInfo(): array
    {
        return [
            'environment' => $this->environment,
            'config' => array_merge($this->config, ['password' => '***']),
            'connection_test' => $this->testConnection(),
            'detection_info' => [
                'server_name' => $_SERVER['SERVER_NAME'] ?? 'N/A',
                'http_host' => $_SERVER['HTTP_HOST'] ?? 'N/A',
                'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
                'server_addr' => $_SERVER['SERVER_ADDR'] ?? 'N/A',
                'php_os' => PHP_OS,
                'app_env' => $this->getEnvVar('APP_ENV'),
            ]
        ];
    }
}
