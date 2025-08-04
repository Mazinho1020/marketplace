<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup {--compress} {--name=}';
    protected $description = 'Cria backup seguro do banco de dados';

    public function handle()
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $name = $this->option('name') ?: "backup_$timestamp";
        $compress = $this->option('compress');

        $backupDir = storage_path('backups');

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $extension = $compress ? '.sql.gz' : '.sql';
        $backupFile = $backupDir . "/$name$extension";

        $this->info("💾 Criando backup...");
        $this->info("Arquivo: $backupFile");

        $command = $compress
            ? "docker exec marketplace-mysql mysqldump -u root -proot meufinanceiro | gzip > $backupFile"
            : "docker exec marketplace-mysql mysqldump -u root -proot meufinanceiro > $backupFile";

        $progressBar = $this->output->createProgressBar();
        $progressBar->start();

        system($command, $return);

        $progressBar->finish();

        if ($return === 0) {
            $size = number_format(filesize($backupFile) / 1024 / 1024, 2);
            $this->info("\n✅ Backup criado com sucesso!");
            $this->info("📁 Arquivo: $backupFile");
            $this->info("📏 Tamanho: {$size} MB");

            // Lista backups existentes
            $this->listarBackups();

            return 0;
        } else {
            $this->error("❌ Erro ao criar backup");
            return 1;
        }
    }

    private function listarBackups()
    {
        $backupDir = storage_path('backups');
        $backups = glob($backupDir . "/backup_*.{sql,sql.gz}", GLOB_BRACE);
        rsort($backups);

        if (empty($backups)) {
            return;
        }

        $this->info("\n📋 Backups disponíveis:");

        $data = [];
        foreach (array_slice($backups, 0, 10) as $backup) {
            $size = number_format(filesize($backup) / 1024 / 1024, 2);
            $date = date('d/m/Y H:i:s', filemtime($backup));
            $data[] = [
                basename($backup),
                "{$size} MB",
                $date
            ];
        }

        $this->table(['Arquivo', 'Tamanho', 'Data'], $data);

        if (count($backups) > 10) {
            $this->info("... e mais " . (count($backups) - 10) . " backups");
        }
    }
}
