<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixNaturezaFinanceiraCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fix:natureza-financeira';

    /**
     * The console command description.
     */
    protected $description = 'Corrige valores incorretos no campo natureza_financeira';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando correção dos valores de natureza_financeira...');

        try {
            // Corrigir valores que deveriam ser 'pagar'
            $contasPagar = DB::table('lancamentos_financeiros')
                ->whereIn('natureza_financeira', ['despesa', 'custo', 'investimento'])
                ->count();

            if ($contasPagar > 0) {
                DB::table('lancamentos_financeiros')
                    ->whereIn('natureza_financeira', ['despesa', 'custo', 'investimento'])
                    ->update(['natureza_financeira' => 'pagar']);
                
                $this->info("Corrigidos {$contasPagar} registros para 'pagar'");
            }

            // Corrigir valores que deveriam ser 'receber'
            $contasReceber = DB::table('lancamentos_financeiros')
                ->whereIn('natureza_financeira', ['receita', 'vendas', 'entrada'])
                ->count();

            if ($contasReceber > 0) {
                DB::table('lancamentos_financeiros')
                    ->whereIn('natureza_financeira', ['receita', 'vendas', 'entrada'])
                    ->update(['natureza_financeira' => 'receber']);
                
                $this->info("Corrigidos {$contasReceber} registros para 'receber'");
            }

            // Verificar se há valores inválidos restantes
            $valoresInvalidos = DB::table('lancamentos_financeiros')
                ->whereNotIn('natureza_financeira', ['pagar', 'receber'])
                ->get(['id', 'natureza_financeira']);

            if ($valoresInvalidos->count() > 0) {
                $this->warn('Encontrados valores inválidos que precisam de análise manual:');
                foreach ($valoresInvalidos as $item) {
                    $this->line("ID: {$item->id} - Valor: {$item->natureza_financeira}");
                }
            }

            $this->info('Correção concluída com sucesso!');
            
        } catch (\Exception $e) {
            $this->error('Erro durante a correção: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
