<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSafeImport extends Command
{
    protected $signature = 'db:safe-import {file} {--backup} {--dry-run}';
    protected $description = 'Importa SQL de forma segura sem deletar dados existentes';

    public function handle()
    {
        $sqlFile = $this->argument('file');

        if (!file_exists($sqlFile)) {
            $this->error("Arquivo não encontrado: $sqlFile");
            return 1;
        }

        $this->info("🛡️  IMPORTAÇÃO SEGURA INICIADA");
        $this->info("Arquivo: $sqlFile");

        // Backup automático se solicitado
        if ($this->option('backup')) {
            $this->info("💾 Criando backup...");
            $this->call('db:backup');
        }

        // Conta tabelas atuais
        $tabelasAntes = $this->contarTabelas();
        $this->info("📊 Tabelas atuais: $tabelasAntes");

        // Lê e analisa SQL
        $sql = file_get_contents($sqlFile);

        // Remove comandos perigosos
        $sql = $this->removerComandosPerigosos($sql);

        // Extrai CREATE TABLE
        preg_match_all('/CREATE TABLE(?:\s+IF\s+NOT\s+EXISTS)?\s+`([^`]+)`[^;]*\([^;]*\)[^;]*;/is', $sql, $matches);
        $createCommands = $matches[0];
        $tableNames = $matches[1];

        $this->info("🔍 Comandos CREATE TABLE encontrados: " . count($createCommands));

        // Filtra tabelas que não existem
        $novasTabelas = [];
        $comandosSeguro = [];

        foreach ($tableNames as $index => $tableName) {
            if (!Schema::hasTable($tableName)) {
                $novasTabelas[] = $tableName;
                $comando = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $createCommands[$index]);
                $comandosSeguro[] = $comando;
            }
        }

        $this->info("🆕 Tabelas novas para criar: " . count($novasTabelas));

        if (empty($novasTabelas)) {
            $this->info("✅ Todas as tabelas já existem!");
            return 0;
        }

        // Mostra tabelas que serão criadas
        $this->table(['#', 'Tabela'], array_map(function ($index, $tabela) {
            return [$index + 1, $tabela];
        }, array_keys($novasTabelas), $novasTabelas));

        // Dry run
        if ($this->option('dry-run')) {
            $this->warn("🔍 DRY RUN - Nenhuma alteração foi feita");
            return 0;
        }

        // Confirmação
        if (!$this->confirm('Deseja continuar com a criação das tabelas?')) {
            $this->info("Operação cancelada");
            return 0;
        }

        // Executa comandos
        $success = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar(count($comandosSeguro));
        $progressBar->start();

        foreach ($comandosSeguro as $index => $comando) {
            try {
                DB::statement($comando);
                $success++;
                $this->line("\n✅ Criada: " . $novasTabelas[$index]);
            } catch (\Exception $e) {
                $errors++;
                $this->line("\n❌ Erro em " . $novasTabelas[$index] . ": " . $e->getMessage());
            }
            $progressBar->advance();
        }

        $progressBar->finish();

        // Resultado final
        $tabelasDepois = $this->contarTabelas();

        $this->info("\n\n🎉 IMPORTAÇÃO CONCLUÍDA");
        $this->table(['Métrica', 'Valor'], [
            ['Tabelas antes', $tabelasAntes],
            ['Tabelas depois', $tabelasDepois],
            ['Criadas com sucesso', $success],
            ['Erros', $errors],
            ['Tabelas criadas', $tabelasDepois - $tabelasAntes]
        ]);

        return 0;
    }

    private function contarTabelas()
    {
        return count(DB::select("SHOW TABLES"));
    }

    private function removerComandosPerigosos($sql)
    {
        $this->info("🧹 Removendo comandos perigosos...");

        // Remove comandos que podem deletar dados
        $sql = preg_replace('/DELETE\s+FROM[^;]*;/is', '', $sql);
        $sql = preg_replace('/DROP\s+TABLE[^;]*;/is', '', $sql);
        $sql = preg_replace('/TRUNCATE[^;]*;/is', '', $sql);
        $sql = preg_replace('/DROP\s+DATABASE[^;]*;/is', '', $sql);

        return $sql;
    }
}
