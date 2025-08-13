<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\ProdutoImagem;

class LimparImagensOrfas extends Command
{
    protected $signature = 'imagens:limpar-orfas
                          {--dry-run : Executa em modo de teste sem fazer alterações}
                          {--verbose : Mostra detalhes da operação}';

    protected $description = 'Remove referências de imagens que não existem mais no sistema de arquivos';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $verbose = $this->option('verbose');

        $this->info('🔍 Verificando imagens órfãs...');
        $this->newLine();

        $imagensOrfas = collect();
        $imagensOK = collect();

        // Buscar todas as imagens no banco
        $imagens = ProdutoImagem::all();

        $this->info("📊 Total de imagens no banco: {$imagens->count()}");

        $progressBar = $this->output->createProgressBar($imagens->count());
        $progressBar->start();

        foreach ($imagens as $imagem) {
            $caminhoCompleto = storage_path('app/public/produtos/' . $imagem->arquivo);

            if (!file_exists($caminhoCompleto)) {
                $imagensOrfas->push([
                    'id' => $imagem->id,
                    'arquivo' => $imagem->arquivo,
                    'produto_id' => $imagem->produto_id,
                    'principal' => $imagem->principal ? 'Sim' : 'Não'
                ]);

                if ($verbose) {
                    $this->newLine();
                    $this->warn("❌ Órfã: {$imagem->arquivo} (ID: {$imagem->id})");
                }
            } else {
                $imagensOK->push($imagem);

                if ($verbose) {
                    $this->newLine();
                    $this->line("✅ OK: {$imagem->arquivo}");
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Relatório
        $this->info("📈 RELATÓRIO:");
        $this->table(
            ['Status', 'Quantidade', 'Percentual'],
            [
                ['✅ Imagens OK', $imagensOK->count(), round(($imagensOK->count() / $imagens->count()) * 100, 1) . '%'],
                ['❌ Imagens Órfãs', $imagensOrfas->count(), round(($imagensOrfas->count() / $imagens->count()) * 100, 1) . '%'],
                ['📊 Total', $imagens->count(), '100%']
            ]
        );

        if ($imagensOrfas->isNotEmpty()) {
            $this->newLine();
            $this->warn("🗑️ IMAGENS ÓRFÃS ENCONTRADAS:");

            $this->table(
                ['ID', 'Arquivo', 'Produto ID', 'Principal'],
                $imagensOrfas->toArray()
            );

            if (!$dryRun) {
                if ($this->confirm('Deseja remover essas referências órfãs do banco de dados?', true)) {
                    $ids = $imagensOrfas->pluck('id');
                    $deleted = ProdutoImagem::whereIn('id', $ids)->delete();

                    $this->info("🗑️ Removidas {$deleted} referências órfãs do banco de dados.");
                } else {
                    $this->info("⏭️ Operação cancelada pelo usuário.");
                }
            } else {
                $this->info("🔍 Modo dry-run: Nenhuma alteração foi feita.");
                $this->info("   Execute sem --dry-run para remover as referências órfãs.");
            }
        } else {
            $this->info("🎉 Nenhuma imagem órfã encontrada! Sistema está consistente.");
        }

        $this->newLine();
        $this->info("✅ Verificação concluída!");

        return 0;
    }
}
