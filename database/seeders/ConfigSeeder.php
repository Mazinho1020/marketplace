<?php

namespace Database\Seeders;

use App\Models\Config\{
    ConfigDbConnection,
    ConfigDefinition,
    ConfigEnvironment,
    ConfigGroup,
    ConfigSite,
    ConfigUrlMapping,
    ConfigValue
};
use App\Models\Business\Business;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder para configurações do sistema
 * 
 * Cria estrutura inicial de configurações para o marketplace
 * seguindo os padrões definidos
 */
class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar empresa padrão ou criar uma para exemplo
        $business = Business::first();

        if (!$business) {
            $business = Business::create([
                'nome' => 'Empresa Exemplo',
                'cnpj' => '00.000.000/0001-00',
                'email' => 'contato@exemplo.com',
                'telefone' => '(99) 99999-9999',
                'endereco' => 'Rua Exemplo, 123',
                'cidade' => 'Cidade Exemplo',
                'uf' => 'EX',
                'cep' => '00000-000',
                'ativo' => true,
            ]);
        }

        $empresaId = $business->id;

        // Criar ambientes
        $environments = $this->createEnvironments($empresaId);

        // Criar sites
        $sites = $this->createSites($empresaId);

        // Criar grupos de configuração
        $groups = $this->createGroups($empresaId);

        // Criar definições de configuração
        $this->createDefinitions($empresaId, $groups);

        // Criar mapeamentos de URL
        $this->createUrlMappings($empresaId, $sites, $environments);

        // Criar conexões de banco
        $this->createDbConnections($empresaId, $environments);

        // Criar valores de configuração padrão
        $this->createConfigValues($empresaId, $sites, $environments);
    }

    /**
     * Cria ambientes de execução
     */
    private function createEnvironments(int $empresaId): array
    {
        return [
            'offline' => ConfigEnvironment::create([
                'empresa_id' => $empresaId,
                'codigo' => 'offline',
                'nome' => 'Desenvolvimento Local',
                'descricao' => 'Ambiente de desenvolvimento local (localhost)',
                'is_producao' => false,
                'ativo' => true,
            ]),
            'online' => ConfigEnvironment::create([
                'empresa_id' => $empresaId,
                'codigo' => 'online',
                'nome' => 'Produção',
                'descricao' => 'Ambiente de produção online',
                'is_producao' => true,
                'ativo' => true,
            ]),
        ];
    }

    /**
     * Cria sites do marketplace
     */
    private function createSites(int $empresaId): array
    {
        return [
            'sistema' => ConfigSite::create([
                'empresa_id' => $empresaId,
                'codigo' => 'sistema',
                'nome' => 'Sistema Principal',
                'descricao' => 'Sistema administrativo principal',
                'base_url_padrao' => '/marketplace/sistema/public',
                'ativo' => true,
            ]),
            'pdv' => ConfigSite::create([
                'empresa_id' => $empresaId,
                'codigo' => 'pdv',
                'nome' => 'PDV',
                'descricao' => 'Ponto de Venda',
                'base_url_padrao' => '/marketplace/pdv/public',
                'ativo' => true,
            ]),
            'fidelidade' => ConfigSite::create([
                'empresa_id' => $empresaId,
                'codigo' => 'fidelidade',
                'nome' => 'Fidelidade',
                'descricao' => 'Sistema de fidelidade e cashback',
                'base_url_padrao' => '/marketplace/fidelidade/public',
                'ativo' => true,
            ]),
            'delivery' => ConfigSite::create([
                'empresa_id' => $empresaId,
                'codigo' => 'delivery',
                'nome' => 'Delivery',
                'descricao' => 'Sistema de delivery',
                'base_url_padrao' => '/marketplace/delivery/public',
                'ativo' => true,
            ]),
        ];
    }

    /**
     * Cria grupos de configuração
     */
    private function createGroups(int $empresaId): array
    {
        return [
            'geral' => ConfigGroup::create([
                'empresa_id' => $empresaId,
                'codigo' => 'geral',
                'nome' => 'Geral',
                'descricao' => 'Configurações gerais do sistema',
                'icone' => 'uil uil-cog',
                'ordem' => 1,
            ]),
            'empresa' => ConfigGroup::create([
                'empresa_id' => $empresaId,
                'codigo' => 'empresa',
                'nome' => 'Empresa',
                'descricao' => 'Dados da empresa',
                'icone' => 'uil uil-building',
                'ordem' => 2,
            ]),
            'sync' => ConfigGroup::create([
                'empresa_id' => $empresaId,
                'codigo' => 'sync',
                'nome' => 'Sincronização',
                'descricao' => 'Configurações de sincronização',
                'icone' => 'uil uil-sync',
                'ordem' => 3,
            ]),
            'telegram' => ConfigGroup::create([
                'empresa_id' => $empresaId,
                'codigo' => 'telegram',
                'nome' => 'Telegram',
                'descricao' => 'Configurações do Telegram',
                'icone' => 'uil uil-telegram-alt',
                'ordem' => 4,
            ]),
            'fidelidade' => ConfigGroup::create([
                'empresa_id' => $empresaId,
                'codigo' => 'fidelidade',
                'nome' => 'Fidelidade',
                'descricao' => 'Configurações do sistema de fidelidade',
                'icone' => 'uil uil-star',
                'ordem' => 5,
            ]),
            'pdv' => ConfigGroup::create([
                'empresa_id' => $empresaId,
                'codigo' => 'pdv',
                'nome' => 'PDV',
                'descricao' => 'Configurações do Ponto de Venda',
                'icone' => 'uil uil-shopping-cart',
                'ordem' => 6,
            ]),
        ];
    }

    /**
     * Cria definições de configuração
     */
    private function createDefinitions(int $empresaId, array $groups): void
    {
        $definitions = [
            // Grupo Geral
            ['chave' => 'app_name', 'grupo' => 'geral', 'tipo' => 'string', 'descricao' => 'Nome da aplicação', 'valor_padrao' => 'Marketplace Mazinho1020', 'obrigatorio' => true],
            ['chave' => 'app_author', 'grupo' => 'geral', 'tipo' => 'string', 'descricao' => 'Autor da aplicação', 'valor_padrao' => 'Mazinho1020'],
            ['chave' => 'app_email', 'grupo' => 'geral', 'tipo' => 'string', 'descricao' => 'Email de contato', 'valor_padrao' => 'contato@marketplace.com'],
            ['chave' => 'app_version', 'grupo' => 'geral', 'tipo' => 'string', 'descricao' => 'Versão do sistema', 'valor_padrao' => '1.0.0', 'obrigatorio' => true],
            ['chave' => 'debug', 'grupo' => 'geral', 'tipo' => 'boolean', 'descricao' => 'Modo de depuração', 'valor_padrao' => '0'],

            // Grupo Empresa
            ['chave' => 'empresa_nome', 'grupo' => 'empresa', 'tipo' => 'string', 'descricao' => 'Nome da empresa', 'valor_padrao' => 'Empresa Exemplo', 'obrigatorio' => true],
            ['chave' => 'empresa_cnpj', 'grupo' => 'empresa', 'tipo' => 'string', 'descricao' => 'CNPJ da empresa', 'valor_padrao' => '00.000.000/0001-00', 'obrigatorio' => true],
            ['chave' => 'empresa_telefone', 'grupo' => 'empresa', 'tipo' => 'string', 'descricao' => 'Telefone da empresa', 'valor_padrao' => '(99) 99999-9999'],
            ['chave' => 'empresa_endereco', 'grupo' => 'empresa', 'tipo' => 'string', 'descricao' => 'Endereço da empresa', 'valor_padrao' => 'Rua Exemplo, 123 - Centro'],

            // Grupo Telegram
            ['chave' => 'telegram_token', 'grupo' => 'telegram', 'tipo' => 'string', 'descricao' => 'Token do bot do Telegram', 'avancado' => true],
            ['chave' => 'telegram_chat_id', 'grupo' => 'telegram', 'tipo' => 'string', 'descricao' => 'ID do chat do Telegram'],
            ['chave' => 'telegram_api_url', 'grupo' => 'telegram', 'tipo' => 'string', 'descricao' => 'URL da API do Telegram', 'valor_padrao' => 'https://api.telegram.org/bot$token/sendDocument', 'avancado' => true],
            ['chave' => 'telegram_message', 'grupo' => 'telegram', 'tipo' => 'string', 'descricao' => 'Mensagem padrão do Telegram', 'valor_padrao' => 'Mensagem do Marketplace'],

            // Grupo Sincronização
            ['chave' => 'sync_interval_minutes', 'grupo' => 'sync', 'tipo' => 'integer', 'descricao' => 'Intervalo de sincronização (minutos)', 'valor_padrao' => '15', 'obrigatorio' => true],
            ['chave' => 'sync_auto_on_startup', 'grupo' => 'sync', 'tipo' => 'boolean', 'descricao' => 'Sincronizar ao iniciar', 'valor_padrao' => '1'],
            ['chave' => 'sync_backup_local', 'grupo' => 'sync', 'tipo' => 'boolean', 'descricao' => 'Backup local antes de sincronizar', 'valor_padrao' => '1'],
            ['chave' => 'export_dir', 'grupo' => 'sync', 'tipo' => 'string', 'descricao' => 'Diretório de exportação', 'valor_padrao' => '../backups/exports/', 'avancado' => true],
            ['chave' => 'import_dir', 'grupo' => 'sync', 'tipo' => 'string', 'descricao' => 'Diretório de importação', 'valor_padrao' => '../temp/auto_import/', 'avancado' => true],
            ['chave' => 'backup_dir', 'grupo' => 'sync', 'tipo' => 'string', 'descricao' => 'Diretório de backup', 'valor_padrao' => '../backups/imports/', 'avancado' => true],
            ['chave' => 'log_dir', 'grupo' => 'sync', 'tipo' => 'string', 'descricao' => 'Diretório de logs', 'valor_padrao' => '../logs/', 'avancado' => true],

            // Grupo Fidelidade
            ['chave' => 'fidelidade_ativo', 'grupo' => 'fidelidade', 'tipo' => 'boolean', 'descricao' => 'Sistema de fidelidade ativo', 'valor_padrao' => '1'],
            ['chave' => 'fidelidade_percentual_cashback', 'grupo' => 'fidelidade', 'tipo' => 'float', 'descricao' => 'Percentual de cashback padrão', 'valor_padrao' => '2.5'],
            ['chave' => 'fidelidade_min_compra_cashback', 'grupo' => 'fidelidade', 'tipo' => 'float', 'descricao' => 'Valor mínimo para ganhar cashback', 'valor_padrao' => '10.00'],
            ['chave' => 'fidelidade_max_cashback_dia', 'grupo' => 'fidelidade', 'tipo' => 'float', 'descricao' => 'Valor máximo de cashback por dia', 'valor_padrao' => '50.00'],

            // Grupo PDV
            ['chave' => 'pdv_ativo', 'grupo' => 'pdv', 'tipo' => 'boolean', 'descricao' => 'PDV ativo', 'valor_padrao' => '1'],
            ['chave' => 'pdv_impressora_automatica', 'grupo' => 'pdv', 'tipo' => 'boolean', 'descricao' => 'Impressão automática de cupons', 'valor_padrao' => '1'],
            ['chave' => 'pdv_desconto_maximo', 'grupo' => 'pdv', 'tipo' => 'float', 'descricao' => 'Desconto máximo permitido (%)', 'valor_padrao' => '10.00'],
        ];

        foreach ($definitions as $def) {
            ConfigDefinition::create([
                'empresa_id' => $empresaId,
                'chave' => $def['chave'],
                'descricao' => $def['descricao'],
                'tipo' => $def['tipo'],
                'grupo_id' => $groups[$def['grupo']]->id,
                'valor_padrao' => $def['valor_padrao'] ?? null,
                'obrigatorio' => $def['obrigatorio'] ?? false,
                'avancado' => $def['avancado'] ?? false,
            ]);
        }
    }

    /**
     * Cria mapeamentos de URL
     */
    private function createUrlMappings(int $empresaId, array $sites, array $environments): void
    {
        $mappings = [
            // URLs para ambiente online
            ['site' => 'sistema', 'env' => 'online', 'dominio' => 'marketplace.com.br', 'base_url' => 'https://marketplace.com.br/sistema/public'],
            ['site' => 'pdv', 'env' => 'online', 'dominio' => 'marketplace.com.br', 'base_url' => 'https://marketplace.com.br/pdv/public'],
            ['site' => 'fidelidade', 'env' => 'online', 'dominio' => 'marketplace.com.br', 'base_url' => 'https://marketplace.com.br/fidelidade/public'],
            ['site' => 'delivery', 'env' => 'online', 'dominio' => 'marketplace.com.br', 'base_url' => 'https://marketplace.com.br/delivery/public'],

            // URLs para ambiente offline
            ['site' => 'sistema', 'env' => 'offline', 'dominio' => 'localhost', 'base_url' => 'http://localhost/marketplace/sistema/public'],
            ['site' => 'pdv', 'env' => 'offline', 'dominio' => 'localhost', 'base_url' => 'http://localhost/marketplace/pdv/public'],
            ['site' => 'fidelidade', 'env' => 'offline', 'dominio' => 'localhost', 'base_url' => 'http://localhost/marketplace/fidelidade/public'],
            ['site' => 'delivery', 'env' => 'offline', 'dominio' => 'localhost', 'base_url' => 'http://localhost/marketplace/delivery/public'],
        ];

        foreach ($mappings as $mapping) {
            ConfigUrlMapping::create([
                'empresa_id' => $empresaId,
                'site_id' => $sites[$mapping['site']]->id,
                'ambiente_id' => $environments[$mapping['env']]->id,
                'dominio' => $mapping['dominio'],
                'base_url' => $mapping['base_url'],
                'api_url' => $mapping['base_url'] . '/api',
                'asset_url' => $mapping['base_url'] . '/assets',
            ]);
        }
    }

    /**
     * Cria conexões de banco de dados
     */
    private function createDbConnections(int $empresaId, array $environments): void
    {
        // Conexão para ambiente local
        ConfigDbConnection::create([
            'empresa_id' => $empresaId,
            'nome' => 'Banco Local',
            'ambiente_id' => $environments['offline']->id,
            'driver' => 'mysql',
            'host' => 'localhost',
            'porta' => 3306,
            'banco' => 'marketplace',
            'usuario' => 'root',
            'senha' => encrypt(''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'padrao' => true,
        ]);

        // Conexão para ambiente de produção (exemplo)
        ConfigDbConnection::create([
            'empresa_id' => $empresaId,
            'nome' => 'Banco Produção',
            'ambiente_id' => $environments['online']->id,
            'driver' => 'mysql',
            'host' => 'servidor.com.br',
            'porta' => 3306,
            'banco' => 'marketplace_prod',
            'usuario' => 'marketplace_user',
            'senha' => encrypt('senha_segura'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'padrao' => true,
        ]);
    }

    /**
     * Cria alguns valores de configuração específicos
     */
    private function createConfigValues(int $empresaId, array $sites, array $environments): void
    {
        // Buscar algumas definições para criar valores específicos
        $appName = ConfigDefinition::where('empresa_id', $empresaId)->where('chave', 'app_name')->first();
        $empresaNome = ConfigDefinition::where('empresa_id', $empresaId)->where('chave', 'empresa_nome')->first();

        if ($appName) {
            // Valor específico para o site PDV
            ConfigValue::create([
                'empresa_id' => $empresaId,
                'config_id' => $appName->id,
                'site_id' => $sites['pdv']->id,
                'valor' => 'PDV Marketplace',
            ]);

            // Valor específico para o site Fidelidade
            ConfigValue::create([
                'empresa_id' => $empresaId,
                'config_id' => $appName->id,
                'site_id' => $sites['fidelidade']->id,
                'valor' => 'Fidelidade Marketplace',
            ]);
        }

        if ($empresaNome) {
            // Valor global
            ConfigValue::create([
                'empresa_id' => $empresaId,
                'config_id' => $empresaNome->id,
                'valor' => 'Marketplace Mazinho1020',
            ]);
        }
    }
}
