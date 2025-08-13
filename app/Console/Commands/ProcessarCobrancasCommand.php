<?php

namespace App\Console\Commands;

use App\Jobs\ProcessarCobrancasAutomaticasJob;
use App\Services\Financial\CobrancaAutomaticaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;

class ProcessarCobrancasCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'financial:processar-cobrancas
                            {--empresa= : ID específico da empresa}
                            {--sync : Executar sincronamente ao invés de usar queue}
                            {--force : Forçar execução mesmo em ambiente de produção}';

    /**
     * The console command description.
     */
    protected $description = 'Processar cobranças automáticas do sistema financeiro';

    /**
     * Execute the console command.
     */
    public function handle(CobrancaAutomaticaService $cobrancaService): int
    {
        $this->info('🚀 Iniciando processamento de cobranças automáticas...');

        $empresaId = $this->option('empresa');
        $sync = $this->option('sync');
        $force = $this->option('force');

        // Verificar ambiente de produção
        if (app()->environment('production') && !$force) {
            if (!$this->confirm('Você está em produção. Deseja continuar?')) {
                $this->info('Operação cancelada.');
                return 0;
            }
        }

        try {
            if ($sync) {
                // Executar sincronamente
                $this->info('Executando sincronamente...');
                $resultado = $cobrancaService->processarCobrancasDiarias();
                $this->exibirResultado($resultado);
            } else {
                // Executar via queue
                $this->info('Adicionando job à queue...');
                ProcessarCobrancasAutomaticasJob::dispatch($empresaId);
                $this->info('✅ Job adicionado à queue com sucesso!');
                
                if ($this->option('verbose')) {
                    $this->info('Use o comando "php artisan queue:work" para processar os jobs.');
                }
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erro durante o processamento: ' . $e->getMessage());
            
            if ($this->option('verbose')) {
                $this->line($e->getTraceAsString());
            }
            
            return 1;
        }
    }

    /**
     * Exibir resultado do processamento
     */
    private function exibirResultado(array $resultado): void
    {
        $this->newLine();
        $this->info('📊 Resultado do Processamento:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $this->line("📧 Avisos de vencimento enviados: {$resultado['avisos_vencimento']}");
        $this->line("🔴 Cobranças de vencidas enviadas: {$resultado['cobrancas_vencidas']}");
        $this->line("🔄 Contas atualizadas (status): {$resultado['contas_atualizadas']}");
        $this->line("📬 Total de emails enviados: {$resultado['emails_enviados']}");
        
        if (!empty($resultado['erros'])) {
            $this->newLine();
            $this->error('❌ Erros encontrados:');
            foreach ($resultado['erros'] as $erro) {
                $this->line("  • {$erro}");
            }
        } else {
            $this->newLine();
            $this->info('✅ Processamento concluído sem erros!');
        }
        
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}