<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PessoasSeeder extends Seeder
{
    /**
     * Popula dados de fidelidade para os clientes existentes
     */
    public function run(): void
    {
        // Buscar todos os clientes e funcionários
        $clientes = DB::table('pessoas')
            ->where('tipo', 'like', '%cliente%')
            ->orWhere('tipo', 'like', '%funcionario%')
            ->get();

        foreach ($clientes as $cliente) {
            // Criar carteira de fidelidade se não existir
            $carteiraExiste = DB::table('fidelidade_carteiras')
                ->where('cliente_id', $cliente->id)
                ->exists();

            if (!$carteiraExiste) {
                // Gerar dados aleatórios de fidelidade
                $pontos = rand(0, 5000);
                $nivel = $this->determinarNivel($pontos);
                $saldo = rand(0, 1000);
                $cashback = rand(0, 500);
                $totalCompras = rand(1, 50);
                $valorTotalGasto = rand(100, 10000);

                DB::table('fidelidade_carteiras')->insert([
                    'cliente_id' => $cliente->id,
                    'empresa_id' => $cliente->empresa_id,
                    'saldo_cashback' => $cashback,
                    'saldo_creditos' => $pontos,
                    'saldo_total_disponivel' => $saldo,
                    'nivel_atual' => $nivel,
                    'xp_total' => $pontos,
                    'status' => 'ativo',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
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
