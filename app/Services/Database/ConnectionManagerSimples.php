<?php
// Classe para Gerenciamento Simplificado de Conexões
// Substitui toda a complexidade anterior por uma solução simples e eficaz

namespace App\Services\Database;

use PDO;
use Exception;

class ConnectionManagerSimples
{
    private $config;
    private $pdo;
    private $ambiente;
    private static $instance = null;

    // Singleton para garantir uma única instância
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->loadConfig();
        $this->detectEnvironment();
        $this->connect();
    }

    private function loadConfig()
    {
        $configPath = __DIR__ . '/../../../config/database_simples.php';
        if (!file_exists($configPath)) {
            throw new Exception("Arquivo de configuração não encontrado: {$configPath}");
        }

        $this->config = require $configPath;
    }

    private function detectEnvironment()
    {
        // Se detecção automática estiver habilitada
        if ($this->config['deteccao_auto']) {
            $hostname = gethostname();
            $cwd = getcwd();

            // Detectar ambiente local/desenvolvimento
            $isLocal = (
                str_contains(strtolower($hostname), 'desktop') ||
                str_contains(strtolower($hostname), 'laptop') ||
                str_contains(strtolower($hostname), 'servidor') ||
                str_contains($cwd, 'xampp') ||
                str_contains($cwd, 'laragon') ||
                str_contains($cwd, 'wamp') ||
                ($_SERVER['SERVER_NAME'] ?? '') === 'localhost' ||
                ($_SERVER['REMOTE_ADDR'] ?? '') === '127.0.0.1' ||
                ($_SERVER['SERVER_ADDR'] ?? '') === '127.0.0.1'
            );

            if ($isLocal) {
                $this->ambiente = 'desenvolvimento';
            } else if (str_contains($_SERVER['HTTP_HOST'] ?? '', 'homolog')) {
                $this->ambiente = 'homologacao';
            } else {
                $this->ambiente = 'producao';
            }
        } else {
            // Usar o ambiente definido na configuração
            $this->ambiente = $this->config['ambiente'];
        }
    }

    private function connect()
    {
        if (!isset($this->config['conexoes'][$this->ambiente])) {
            throw new Exception("Configuração para o ambiente '{$this->ambiente}' não encontrada");
        }

        $conn = $this->config['conexoes'][$this->ambiente];

        try {
            $dsn = "{$conn['driver']}:host={$conn['host']};port={$conn['porta']};dbname={$conn['banco']};charset={$conn['charset']}";

            $this->pdo = new PDO($dsn, $conn['usuario'], $conn['senha'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5,
            ]);
        } catch (Exception $e) {
            throw new Exception("Erro ao conectar ao banco [{$this->ambiente}]: " . $e->getMessage());
        }
    }

    // Métodos públicos
    public function getPDO()
    {
        return $this->pdo;
    }

    public function getAmbiente()
    {
        return $this->ambiente;
    }

    public function getConexaoAtual()
    {
        return $this->config['conexoes'][$this->ambiente];
    }

    public function getConfig()
    {
        return $this->config;
    }

    // Método para trocar de ambiente manualmente
    public function alternarAmbiente($novoAmbiente)
    {
        $ambientesValidos = ['desenvolvimento', 'homologacao', 'producao'];

        if (!in_array($novoAmbiente, $ambientesValidos)) {
            throw new Exception("Ambiente inválido: {$novoAmbiente}. Válidos: " . implode(', ', $ambientesValidos));
        }

        // Atualizar o arquivo de configuração
        $configPath = __DIR__ . '/../../../config/database_simples.php';
        $config = require $configPath;

        // Adicionar ao histórico
        $config['historico'][] = [
            'ambiente_anterior' => $this->ambiente,
            'ambiente_novo' => $novoAmbiente,
            'data_mudanca' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI'
        ];

        // Limitar histórico a 10 entradas
        if (count($config['historico']) > 10) {
            $config['historico'] = array_slice($config['historico'], -10);
        }

        $config['ambiente'] = $novoAmbiente;
        $config['deteccao_auto'] = false; // Desativar detecção automática quando alterado manualmente

        // Gerar novo conteúdo do arquivo
        $content = "<?php\n// Configuração Simplificada de Banco de Dados\n";
        $content .= "// Última atualização: " . date('Y-m-d H:i:s') . "\n\n";
        $content .= "return " . var_export($config, true) . ";\n";

        if (file_put_contents($configPath, $content)) {
            // Recarregar configuração e reconectar
            $this->loadConfig();
            $this->ambiente = $novoAmbiente;
            $this->connect();

            return true;
        } else {
            throw new Exception("Erro ao salvar o arquivo de configuração. Verifique as permissões.");
        }
    }

    // Método para testar conexão
    public function testarConexao($ambiente = null)
    {
        $ambienteTestar = $ambiente ?? $this->ambiente;

        if (!isset($this->config['conexoes'][$ambienteTestar])) {
            return ['sucesso' => false, 'erro' => "Configuração para '{$ambienteTestar}' não encontrada"];
        }

        $conn = $this->config['conexoes'][$ambienteTestar];

        // Verificar se o ambiente está habilitado
        if (isset($conn['habilitado']) && $conn['habilitado'] === false) {
            return [
                'sucesso' => false,
                'ambiente' => $ambienteTestar,
                'erro' => 'Ambiente desabilitado - configure o servidor antes de testar',
                'desabilitado' => true
            ];
        }

        try {
            $dsn = "{$conn['driver']}:host={$conn['host']};port={$conn['porta']};dbname={$conn['banco']};charset={$conn['charset']}";
            $testPdo = new PDO($dsn, $conn['usuario'], $conn['senha'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 3,
            ]);

            // Testar uma consulta simples
            $stmt = $testPdo->query("SELECT 1 as teste");
            $resultado = $stmt->fetch();

            return [
                'sucesso' => true,
                'ambiente' => $ambienteTestar,
                'host' => $conn['host'],
                'banco' => $conn['banco'],
                'teste_query' => $resultado['teste'] == 1
            ];
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'ambiente' => $ambienteTestar,
                'erro' => $e->getMessage()
            ];
        }
    }

    // Método para obter informações do sistema
    public function getInfoSistema()
    {
        return [
            'ambiente_atual' => $this->ambiente,
            'deteccao_auto' => $this->config['deteccao_auto'],
            'hostname' => gethostname(),
            'working_dir' => getcwd(),
            'conexao_atual' => $this->getConexaoAtual(),
            'historico_mudancas' => $this->config['historico'] ?? []
        ];
    }
}
