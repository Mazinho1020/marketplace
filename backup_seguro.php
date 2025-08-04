<?php

/**
 * Sistema de Backup e Restore SEGURO
 */

class DatabaseBackup
{
    private $pdo;
    private $backupDir = 'backups';

    public function __construct()
    {
        $this->pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=meufinanceiro;charset=utf8mb4", 'root', 'root');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    /**
     * Cria backup completo do banco
     */
    public function criarBackup()
    {
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = $this->backupDir . "/backup_$timestamp.sql";

        echo "💾 Criando backup em: $backupFile\n";

        $command = "docker exec marketplace-mysql mysqldump -u root -proot meufinanceiro > $backupFile";
        system($command, $return);

        if ($return === 0) {
            echo "✅ Backup criado com sucesso!\n";
            return $backupFile;
        } else {
            throw new Exception("Erro ao criar backup");
        }
    }

    /**
     * Lista backups disponíveis
     */
    public function listarBackups()
    {
        $backups = glob($this->backupDir . "/backup_*.sql");
        rsort($backups); // Mais recentes primeiro

        echo "📋 Backups disponíveis:\n";
        foreach ($backups as $i => $backup) {
            $size = number_format(filesize($backup) / 1024 / 1024, 2);
            $date = date('d/m/Y H:i:s', filemtime($backup));
            echo sprintf("%2d. %s (%s MB) - %s\n", $i + 1, basename($backup), $size, $date);
        }

        return $backups;
    }

    /**
     * Conta tabelas atuais
     */
    public function contarTabelas()
    {
        $stmt = $this->pdo->query("SHOW TABLES");
        $tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return count($tabelas);
    }

    /**
     * Verifica se uma tabela existe
     */
    public function tabelaExiste($tabela)
    {
        $stmt = $this->pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$tabela]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Importa SQL de forma SEGURA
     */
    public function importarSeguro($sqlFile)
    {
        echo "🛡️  MODO SEGURO: Verificando antes de importar...\n";

        // 1. Backup automático
        $backup = $this->criarBackup();

        // 2. Conta tabelas antes
        $tabelasAntes = $this->contarTabelas();
        echo "📊 Tabelas antes: $tabelasAntes\n";

        // 3. Lê SQL e filtra apenas CREATE TABLE seguros
        $sql = file_get_contents($sqlFile);

        // Remove comandos perigosos
        $sql = preg_replace('/DELETE\s+FROM[^;]*;/is', '', $sql);
        $sql = preg_replace('/DROP\s+TABLE[^;]*;/is', '', $sql);
        $sql = preg_replace('/TRUNCATE[^;]*;/is', '', $sql);

        echo "🧹 Comandos perigosos removidos\n";

        // 4. Extrai apenas CREATE TABLE
        preg_match_all('/CREATE TABLE(?:\s+IF\s+NOT\s+EXISTS)?\s+`([^`]+)`[^;]*\([^;]*\)[^;]*;/is', $sql, $matches);
        $createCommands = $matches[0];
        $tableNames = $matches[1];

        // 5. Filtra apenas tabelas inexistentes
        $novasTabelas = [];
        $comandosSeguro = [];

        foreach ($tableNames as $index => $tableName) {
            if (!$this->tabelaExiste($tableName)) {
                $novasTabelas[] = $tableName;
                $comando = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $createCommands[$index]);
                $comandosSeguro[] = $comando;
            }
        }

        echo "🆕 Novas tabelas para criar: " . count($novasTabelas) . "\n";

        if (empty($novasTabelas)) {
            echo "✅ Nenhuma tabela nova para criar\n";
            return;
        }

        // 6. Executa comandos seguros
        $success = 0;
        foreach ($comandosSeguro as $i => $comando) {
            try {
                $this->pdo->exec($comando);
                $success++;
                echo "✅ Criada: " . $novasTabelas[$i] . "\n";
            } catch (Exception $e) {
                echo "❌ Erro: " . $novasTabelas[$i] . " - " . $e->getMessage() . "\n";
            }
        }

        // 7. Verifica resultado
        $tabelasDepois = $this->contarTabelas();
        echo "\n📊 Resultado:\n";
        echo "   Antes: $tabelasAntes tabelas\n";
        echo "   Depois: $tabelasDepois tabelas\n";
        echo "   Criadas: " . ($tabelasDepois - $tabelasAntes) . " tabelas\n";
        echo "   Backup: $backup\n";
    }
}

// Uso do sistema
try {
    $backup = new DatabaseBackup();

    echo "🎯 SISTEMA DE BACKUP E IMPORTAÇÃO SEGURA\n";
    echo "=====================================\n\n";

    echo "1. Estado atual:\n";
    echo "   Tabelas: " . $backup->contarTabelas() . "\n\n";

    echo "2. Backups existentes:\n";
    $backup->listarBackups();
    echo "\n";

    echo "3. Importação segura:\n";
    $sqlFile = 'C:\\Users\\leoma\\Downloads\\teste2 - Copia.sql';
    if (file_exists($sqlFile)) {
        $backup->importarSeguro($sqlFile);
    } else {
        echo "❌ Arquivo SQL não encontrado: $sqlFile\n";
    }
} catch (Exception $e) {
    echo "💥 ERRO: " . $e->getMessage() . "\n";
}
