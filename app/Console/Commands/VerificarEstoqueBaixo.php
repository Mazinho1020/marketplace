<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EstoqueBaixoService;
use App\Models\Empresa;

class VerificarEstoqueBaixo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'estoque:verificar-baixo 
                           {--empresa= : ID específico da empresa para verificar} 
                           {--limpar-antigas : Remove notificações antigas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica produtos com estoque baixo e cria notificações';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Iniciando verificação de estoque baixo...');

        $estoqueBaixoService = new EstoqueBaixoService();

        // Limpar notificações antigas se solicitado
        if ($this->option('limpar-antigas')) {
            $this->info('🧹 Limpando notificações antigas...');
            $removidas = $estoqueBaixoService->limparNotificacoesAntigas();
            $this->info("✅ {$removidas} notificações antigas removidas.");
        }

        $empresaId = $this->option('empresa');

        if ($empresaId) {
            // Verificar empresa específica
            $empresa = Empresa::find($empresaId);
            if (!$empresa) {
                $this->error("❌ Empresa com ID {$empresaId} não encontrada!");
                return Command::FAILURE;
            }

            $this->info("🏢 Verificando estoque da empresa: {$empresa->nome_fantasia}");
            $resultados = $estoqueBaixoService->executarVerificacaoCompleta($empresaId);
            $this->exibirResultados($resultados, $empresa->nome_fantasia);
        } else {
            // Verificar todas as empresas
            $empresas = Empresa::where('ativo', true)->get();
            $this->info("🏢 Verificando estoque de {$empresas->count()} empresas...");

            $totalNotificacoes = 0;
            $totalProdutosBaixo = 0;
            $totalProdutosZerado = 0;

            foreach ($empresas as $empresa) {
                $this->info("   🔄 Verificando: {$empresa->nome_fantasia}");

                $resultados = $estoqueBaixoService->executarVerificacaoCompleta($empresa->id);

                $totalNotificacoes += $resultados['total_notificacoes_criadas'];
                $totalProdutosBaixo += $resultados['produtos_estoque_baixo']->count();
                $totalProdutosZerado += $resultados['produtos_estoque_zerado']->count();

                if ($resultados['total_notificacoes_criadas'] > 0) {
                    $this->warn("   ⚠️  {$empresa->nome_fantasia}: {$resultados['total_notificacoes_criadas']} notificações criadas");
                }
            }

            $this->info("\n📊 RESUMO GERAL:");
            $this->table([
                'Metric',
                'Valor'
            ], [
                ['Empresas verificadas', $empresas->count()],
                ['Produtos com estoque baixo', $totalProdutosBaixo],
                ['Produtos com estoque zerado', $totalProdutosZerado],
                ['Total de notificações criadas', $totalNotificacoes]
            ]);
        }

        $this->info('✅ Verificação de estoque concluída!');
        return Command::SUCCESS;
    }

    /**
     * Exibe os resultados da verificação
     */
    private function exibirResultados(array $resultados, string $nomeEmpresa = 'Empresa')
    {
        $this->info("\n📊 RESULTADOS PARA: {$nomeEmpresa}");

        $this->table([
            'Tipo',
            'Quantidade'
        ], [
            ['Produtos com estoque baixo', $resultados['produtos_estoque_baixo']->count()],
            ['Produtos com estoque zerado', $resultados['produtos_estoque_zerado']->count()],
            ['Notificações criadas', $resultados['total_notificacoes_criadas']]
        ]);

        // Mostrar detalhes dos produtos com problemas
        if ($resultados['produtos_estoque_zerado']->count() > 0) {
            $this->error("\n❌ PRODUTOS COM ESTOQUE ESGOTADO:");
            foreach ($resultados['produtos_estoque_zerado'] as $produto) {
                $this->line("   • {$produto->nome} (SKU: {$produto->sku})");
            }
        }

        if ($resultados['produtos_estoque_baixo']->count() > 0) {
            $this->warn("\n⚠️ PRODUTOS COM ESTOQUE BAIXO:");
            foreach ($resultados['produtos_estoque_baixo'] as $produto) {
                $this->line("   • {$produto->nome} - Atual: {$produto->estoque_atual} | Mínimo: {$produto->estoque_minimo}");
            }
        }

        if ($resultados['total_notificacoes_criadas'] == 0) {
            $this->info("✅ Todos os produtos estão com estoque adequado!");
        }
    }
}
