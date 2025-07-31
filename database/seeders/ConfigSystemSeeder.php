<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar empresa padrão se não existir
        $empresaId = DB::table('empresas')->insertGetId([
            'nome' => 'Marketplace Demo',
            'razao_social' => 'Marketplace Demonstração Ltda',
            'email' => 'admin@marketplace.local',
            'ativo' => true,
            'sync_status' => 'synced',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Criar ambientes
        $ambientes = [
            [
                'empresa_id' => $empresaId,
                'codigo' => 'online',
                'nome' => 'Ambiente Online',
                'descricao' => 'Ambiente para operações online (com internet)',
                'is_producao' => true,
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'codigo' => 'offline',
                'nome' => 'Ambiente Offline',
                'descricao' => 'Ambiente para operações offline (sem internet)',
                'is_producao' => false,
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'codigo' => 'desenvolvimento',
                'nome' => 'Desenvolvimento',
                'descricao' => 'Ambiente de desenvolvimento e testes',
                'is_producao' => false,
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($ambientes as $ambiente) {
            DB::table('config_environments')->insert($ambiente);
        }

        // Criar sites
        $sites = [
            [
                'empresa_id' => $empresaId,
                'codigo' => 'admin',
                'nome' => 'Painel Administrativo',
                'descricao' => 'Interface de administração do marketplace',
                'base_url_padrao' => '/admin',
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'codigo' => 'pdv',
                'nome' => 'Ponto de Venda',
                'descricao' => 'Sistema de ponto de venda (PDV)',
                'base_url_padrao' => '/pdv',
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'codigo' => 'fidelidade',
                'nome' => 'Sistema de Fidelidade',
                'descricao' => 'Programa de fidelidade e cashback',
                'base_url_padrao' => '/fidelidade',
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'codigo' => 'delivery',
                'nome' => 'Sistema de Delivery',
                'descricao' => 'Plataforma de delivery e logística',
                'base_url_padrao' => '/delivery',
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($sites as $site) {
            DB::table('config_sites')->insert($site);
        }

        // Criar grupos de configurações
        $grupos = [
            [
                'empresa_id' => $empresaId,
                'codigo' => 'sistema',
                'nome' => 'Sistema Geral',
                'descricao' => 'Configurações gerais do sistema',
                'grupo_pai_id' => null,
                'icone_class' => 'fas fa-cogs',
                'ordem' => 1,
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'codigo' => 'interface',
                'nome' => 'Interface',
                'descricao' => 'Configurações de interface e tema',
                'grupo_pai_id' => null,
                'icone_class' => 'fas fa-paint-brush',
                'ordem' => 2,
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'codigo' => 'pdv_config',
                'nome' => 'PDV',
                'descricao' => 'Configurações do ponto de venda',
                'grupo_pai_id' => null,
                'icone_class' => 'fas fa-cash-register',
                'ordem' => 3,
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'codigo' => 'fidelidade_config',
                'nome' => 'Fidelidade',
                'descricao' => 'Configurações do programa de fidelidade',
                'grupo_pai_id' => null,
                'icone_class' => 'fas fa-star',
                'ordem' => 4,
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($grupos as $grupo) {
            DB::table('config_groups')->insert($grupo);
        }

        // Obter IDs dos grupos criados
        $grupoSistema = DB::table('config_groups')->where('codigo', 'sistema')->where('empresa_id', $empresaId)->first();
        $grupoInterface = DB::table('config_groups')->where('codigo', 'interface')->where('empresa_id', $empresaId)->first();
        $grupoPdv = DB::table('config_groups')->where('codigo', 'pdv_config')->where('empresa_id', $empresaId)->first();
        $grupoFidelidade = DB::table('config_groups')->where('codigo', 'fidelidade_config')->where('empresa_id', $empresaId)->first();

        // Criar definições de configurações
        $configuracoes = [
            // Sistema Geral
            [
                'empresa_id' => $empresaId,
                'chave' => 'app_name',
                'nome' => 'Nome da Aplicação',
                'descricao' => 'Nome exibido na aplicação',
                'tipo' => 'string',
                'grupo_id' => $grupoSistema->id,
                'valor_padrao' => 'Marketplace',
                'obrigatorio' => true,
                'editavel' => true,
                'ordem' => 1,
                'dica' => 'Nome que aparecerá no cabeçalho',
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'app_debug',
                'nome' => 'Modo Debug',
                'descricao' => 'Ativar modo de debug da aplicação',
                'tipo' => 'boolean',
                'grupo_id' => $grupoSistema->id,
                'valor_padrao' => 'false',
                'obrigatorio' => true,
                'editavel' => true,
                'avancado' => true,
                'ordem' => 2,
                'dica' => 'Apenas para desenvolvimento',
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'max_upload_size',
                'nome' => 'Tamanho Máximo de Upload',
                'descricao' => 'Tamanho máximo para upload de arquivos (MB)',
                'tipo' => 'integer',
                'grupo_id' => $grupoSistema->id,
                'valor_padrao' => '10',
                'obrigatorio' => true,
                'min_length' => 1,
                'max_length' => 100,
                'editavel' => true,
                'ordem' => 3,
                'dica' => 'Valor em megabytes',
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Interface
            [
                'empresa_id' => $empresaId,
                'chave' => 'theme_primary_color',
                'nome' => 'Cor Primária',
                'descricao' => 'Cor primária do tema',
                'tipo' => 'string',
                'grupo_id' => $grupoInterface->id,
                'valor_padrao' => '#007bff',
                'obrigatorio' => true,
                'regex_validacao' => '^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$',
                'editavel' => true,
                'ordem' => 1,
                'dica' => 'Código hexadecimal da cor (#000000)',
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'items_per_page',
                'nome' => 'Itens por Página',
                'descricao' => 'Quantidade padrão de itens por página nas listagens',
                'tipo' => 'integer',
                'grupo_id' => $grupoInterface->id,
                'valor_padrao' => '15',
                'obrigatorio' => true,
                'min_length' => 5,
                'max_length' => 100,
                'opcoes' => json_encode(['5', '10', '15', '25', '50', '100']),
                'editavel' => true,
                'ordem' => 2,
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // PDV
            [
                'empresa_id' => $empresaId,
                'chave' => 'pdv_impressora_termica',
                'nome' => 'Impressora Térmica',
                'descricao' => 'Usar impressora térmica para cupons',
                'tipo' => 'boolean',
                'grupo_id' => $grupoPdv->id,
                'valor_padrao' => 'true',
                'obrigatorio' => false,
                'editavel' => true,
                'ordem' => 1,
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'pdv_desconto_maximo',
                'nome' => 'Desconto Máximo (%)',
                'descricao' => 'Percentual máximo de desconto permitido',
                'tipo' => 'float',
                'grupo_id' => $grupoPdv->id,
                'valor_padrao' => '20.0',
                'obrigatorio' => true,
                'min_length' => 0,
                'max_length' => 100,
                'editavel' => true,
                'ordem' => 2,
                'dica' => 'Valor em percentual (0-100)',
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Fidelidade
            [
                'empresa_id' => $empresaId,
                'chave' => 'fidelidade_pontos_por_real',
                'nome' => 'Pontos por Real',
                'descricao' => 'Quantidade de pontos ganhos por real gasto',
                'tipo' => 'integer',
                'grupo_id' => $grupoFidelidade->id,
                'valor_padrao' => '1',
                'obrigatorio' => true,
                'min_length' => 1,
                'editavel' => true,
                'ordem' => 1,
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'fidelidade_cashback_percentual',
                'nome' => 'Percentual de Cashback',
                'descricao' => 'Percentual de cashback para compras',
                'tipo' => 'float',
                'grupo_id' => $grupoFidelidade->id,
                'valor_padrao' => '2.5',
                'obrigatorio' => true,
                'min_length' => 0,
                'max_length' => 100,
                'editavel' => true,
                'ordem' => 2,
                'dica' => 'Valor em percentual (ex: 2.5 para 2,5%)',
                'ativo' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($configuracoes as $config) {
            DB::table('config_definitions')->insert($config);
        }

        // Criar alguns valores de exemplo
        $ambienteOnline = DB::table('config_environments')->where('codigo', 'online')->where('empresa_id', $empresaId)->first();
        $siteAdmin = DB::table('config_sites')->where('codigo', 'admin')->where('empresa_id', $empresaId)->first();
        $configAppName = DB::table('config_definitions')->where('chave', 'app_name')->where('empresa_id', $empresaId)->first();

        DB::table('config_values')->insert([
            'empresa_id' => $empresaId,
            'config_id' => $configAppName->id,
            'site_id' => $siteAdmin->id,
            'ambiente_id' => $ambienteOnline->id,
            'valor' => 'Marketplace Admin',
            'sync_status' => 'synced',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ ConfigSystemSeeder executado com sucesso!');
        $this->command->info('📊 Dados criados:');
        $this->command->info('   - 1 empresa: Marketplace Demo');
        $this->command->info('   - 3 ambientes: online, offline, desenvolvimento');
        $this->command->info('   - 4 sites: admin, pdv, fidelidade, delivery');
        $this->command->info('   - 4 grupos de configuração');
        $this->command->info('   - 10 configurações de exemplo');
        $this->command->info('   - 1 valor específico para admin');
    }
}
