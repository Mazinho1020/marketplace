<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Financial\CategoriaContaGerencial;

/**
 * Seeder para categorias de conta gerencial
 * 
 * Popula as categorias baseado na estrutura encontrada
 * no banco atual (database_clean.sql)
 */
class CategoriaContaGerencialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            [
                'nome' => 'despesa_fixa',
                'nome_completo' => 'Despesa Fixa',
                'descricao' => 'Despesas fixas mensais que não variam com a produção ou vendas',
                'cor' => '#fd7e14',
                'icone' => 'fa-calendar-check',
                'e_custo' => false,
                'e_despesa' => true,
                'e_receita' => false,
                'ativo' => true,
                'empresa_id' => 1,
            ],
            [
                'nome' => 'despesa_variavel',
                'nome_completo' => 'Despesa Variável',
                'descricao' => 'Despesas que variam conforme o volume de atividades',
                'cor' => '#ffc107',
                'icone' => 'fa-chart-line',
                'e_custo' => false,
                'e_despesa' => true,
                'e_receita' => false,
                'ativo' => true,
                'empresa_id' => 1,
            ],
            [
                'nome' => 'custo_fixo',
                'nome_completo' => 'Custo Fixo',
                'descricao' => 'Custos fixos de produção que não variam com o volume',
                'cor' => '#dc3545',
                'icone' => 'fa-industry',
                'e_custo' => true,
                'e_despesa' => false,
                'e_receita' => false,
                'ativo' => true,
                'empresa_id' => 1,
            ],
            [
                'nome' => 'custo_variavel',
                'nome_completo' => 'Custo Variável',
                'descricao' => 'Custos que variam diretamente com a produção',
                'cor' => '#e74c3c',
                'icone' => 'fa-cogs',
                'e_custo' => true,
                'e_despesa' => false,
                'e_receita' => false,
                'ativo' => true,
                'empresa_id' => 1,
            ],
            [
                'nome' => 'receita_vendas',
                'nome_completo' => 'Receita de Vendas',
                'descricao' => 'Receitas provenientes das vendas de produtos ou serviços',
                'cor' => '#28a745',
                'icone' => 'fa-shopping-cart',
                'e_custo' => false,
                'e_despesa' => false,
                'e_receita' => true,
                'ativo' => true,
                'empresa_id' => 1,
            ],
            [
                'nome' => 'outras_receitas',
                'nome_completo' => 'Outras Receitas',
                'descricao' => 'Receitas diversas não relacionadas à atividade principal',
                'cor' => '#20c997',
                'icone' => 'fa-coins',
                'e_custo' => false,
                'e_despesa' => false,
                'e_receita' => true,
                'ativo' => true,
                'empresa_id' => 1,
            ],
        ];

        foreach ($categorias as $categoria) {
            CategoriaContaGerencial::updateOrCreate(
                [
                    'nome' => $categoria['nome'],
                    'empresa_id' => $categoria['empresa_id']
                ],
                $categoria
            );
        }

        $this->command->info('Categorias de conta gerencial criadas com sucesso!');
    }
}