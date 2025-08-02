<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FunforcliSeeder extends Seeder
{
    /**
     * Popula dados de fidelidade para os clientes existentes
     */
    public function run(): void
    {
        // Buscar todos os clientes e funcionários
        $clientes = DB::table('funforcli')
            ->where('tipo', 'cliente')
            ->orWhere('tipo', 'funcionario')
            ->get();

        foreach ($clientes as $cliente) {
            // Gerar dados aleatórios de fidelidade
            $pontos = rand(0, 5000);
            $nivel = $this->determinarNivel($pontos);
            $saldo = rand(0, 1000);
            $cashback = rand(0, 500);
            $totalCompras = rand(1, 50);
            $valorTotalGasto = rand(100, 10000);

            DB::table('funforcli')
                ->where('id', $cliente->id)
                ->update([
                    'pontos_acumulados' => $pontos,
                    'nivel_fidelidade' => $nivel,
                    'saldo_disponivel' => $saldo,
                    'cashback_acumulado' => $cashback,
                    'data_ultimo_uso' => now()->subDays(rand(1, 90)),
                    'total_compras' => $totalCompras,
                    'valor_total_gasto' => $valorTotalGasto,
                    'programa_fidelidade_ativo' => true,
                    'data_aniversario' => now()->subYears(rand(18, 70))->format('Y-m-d')
                ]);
        }
    }

    /**
     * Determina o nível baseado nos pontos
     */
    private function determinarNivel($pontos)
    {
        if ($pontos >= 3000) {
            return 'ouro';
        } elseif ($pontos >= 1000) {
            return 'prata';
        } else {
            return 'bronze';
        }
    }
}
