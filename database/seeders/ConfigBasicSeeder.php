<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigBasicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar grupos de configurações
        $grupos = [
            [
                'codigo' => 'sistema',
                'nome' => 'Sistema Geral',
                'descricao' => 'Configurações gerais do sistema',
                'icone_class' => 'fas fa-cogs',
                'ordem' => 1,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'interface',
                'nome' => 'Interface',
                'descricao' => 'Configurações de interface e tema',
                'icone_class' => 'fas fa-paint-brush',
                'ordem' => 2,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'pdv',
                'nome' => 'PDV',
                'descricao' => 'Configurações do ponto de venda',
                'icone_class' => 'fas fa-cash-register',
                'ordem' => 3,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'fidelidade',
                'nome' => 'Fidelidade',
                'descricao' => 'Configurações do programa de fidelidade',
                'icone_class' => 'fas fa-star',
                'ordem' => 4,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($grupos as $grupo) {
            DB::table('config_groups')->updateOrInsert(
                ['codigo' => $grupo['codigo']],
                $grupo
            );
        }

        // Obter IDs dos grupos criados
        $grupoSistema = DB::table('config_groups')->where('codigo', 'sistema')->first();
        $grupoInterface = DB::table('config_groups')->where('codigo', 'interface')->first();
        $grupoPdv = DB::table('config_groups')->where('codigo', 'pdv')->first();
        $grupoFidelidade = DB::table('config_groups')->where('codigo', 'fidelidade')->first();

        // Criar definições de configurações
        $configuracoes = [
            // Sistema Geral
            [
                'chave' => 'app_name',
                'nome' => 'Nome da Aplicação',
                'descricao' => 'Nome exibido na aplicação',
                'tipo' => 'string',
                'grupo_id' => $grupoSistema->id,
                'valor_padrao' => 'Marketplace Admin',
                'obrigatorio' => true,
                'editavel' => true,
                'ordem' => 1,
                'dica' => 'Nome que aparecerá no cabeçalho',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chave' => 'app_version',
                'nome' => 'Versão da Aplicação',
                'descricao' => 'Versão atual do sistema',
                'tipo' => 'string',
                'grupo_id' => $grupoSistema->id,
                'valor_padrao' => '1.0.0',
                'obrigatorio' => false,
                'editavel' => true,
                'ordem' => 2,
                'dica' => 'Versão do sistema',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chave' => 'maintenance_mode',
                'nome' => 'Modo de Manutenção',
                'descricao' => 'Ativar/desativar modo de manutenção',
                'tipo' => 'boolean',
                'grupo_id' => $grupoSistema->id,
                'valor_padrao' => 'false',
                'obrigatorio' => false,
                'editavel' => true,
                'ordem' => 3,
                'dica' => 'Quando ativo, apenas administradores podem acessar',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Interface
            [
                'chave' => 'theme_color',
                'nome' => 'Cor do Tema',
                'descricao' => 'Cor principal do tema',
                'tipo' => 'string',
                'grupo_id' => $grupoInterface->id,
                'valor_padrao' => '#007bff',
                'obrigatorio' => false,
                'editavel' => true,
                'ordem' => 1,
                'dica' => 'Cor em hexadecimal (#000000)',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chave' => 'sidebar_collapsed',
                'nome' => 'Barra Lateral Recolhida',
                'descricao' => 'Iniciar com barra lateral recolhida',
                'tipo' => 'boolean',
                'grupo_id' => $grupoInterface->id,
                'valor_padrao' => 'false',
                'obrigatorio' => false,
                'editavel' => true,
                'ordem' => 2,
                'dica' => 'Define o estado inicial da barra lateral',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // PDV
            [
                'chave' => 'pdv_auto_print',
                'nome' => 'Impressão Automática',
                'descricao' => 'Imprimir comprovantes automaticamente',
                'tipo' => 'boolean',
                'grupo_id' => $grupoPdv->id,
                'valor_padrao' => 'true',
                'obrigatorio' => false,
                'editavel' => true,
                'ordem' => 1,
                'dica' => 'Imprimir comprovantes sem confirmação',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Fidelidade
            [
                'chave' => 'fidelidade_enabled',
                'nome' => 'Sistema de Fidelidade Ativo',
                'descricao' => 'Ativar/desativar sistema de fidelidade',
                'tipo' => 'boolean',
                'grupo_id' => $grupoFidelidade->id,
                'valor_padrao' => 'true',
                'obrigatorio' => false,
                'editavel' => true,
                'ordem' => 1,
                'dica' => 'Permite usar o programa de fidelidade',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chave' => 'fidelidade_cashback_percent',
                'nome' => 'Porcentagem de Cashback',
                'descricao' => 'Porcentagem padrão de cashback',
                'tipo' => 'float',
                'grupo_id' => $grupoFidelidade->id,
                'valor_padrao' => '5.0',
                'obrigatorio' => false,
                'editavel' => true,
                'ordem' => 2,
                'dica' => 'Porcentagem aplicada por padrão (0-100)',
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($configuracoes as $config) {
            $configId = DB::table('config_definitions')->updateOrInsert(
                ['chave' => $config['chave']],
                $config
            );

            // Inserir valor padrão na tabela de valores
            $configRecord = DB::table('config_definitions')->where('chave', $config['chave'])->first();
            if ($configRecord) {
                DB::table('config_values')->updateOrInsert(
                    ['config_id' => $configRecord->id],
                    [
                        'config_id' => $configRecord->id,
                        'valor' => $config['valor_padrao'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
