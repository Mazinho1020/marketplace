<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sistema de Configurações Completo - Marketplace Multi-Sites
     * Seguindo padrões de nomenclatura e multitenancy definidos
     */
    public function up(): void
    {
        // Tabela de ambientes de execução
        Schema::create('config_environments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('empresa_id')->comment('Empresa proprietária');
            $table->string('codigo', 50)->comment('Código único do ambiente (ex: online, offline)');
            $table->string('nome', 100)->comment('Nome de exibição do ambiente');
            $table->text('descricao')->nullable()->comment('Descrição detalhada do ambiente');
            $table->boolean('is_producao')->default(false)->comment('Indica se é ambiente de produção');
            $table->boolean('ativo')->default(true)->comment('Status do ambiente');

            // Campos obrigatórios de sincronização
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            $table->timestamps();
            $table->softDeletes();

            // Índices e constraints
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->index(['empresa_id', 'sync_status']);
            $table->unique(['empresa_id', 'codigo'], 'config_environments_empresa_codigo_unique');
        });

        // Tabela de sites do marketplace
        Schema::create('config_sites', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('empresa_id')->comment('Empresa proprietária');
            $table->string('codigo', 50)->comment('Código único do site (ex: sistema, pdv, fidelidade)');
            $table->string('nome', 100)->comment('Nome de exibição do site');
            $table->text('descricao')->nullable()->comment('Descrição do site');
            $table->string('base_url_padrao')->nullable()->comment('URL base padrão do site');
            $table->boolean('ativo')->default(true)->comment('Status do site');

            // Campos obrigatórios de sincronização
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            $table->timestamps();
            $table->softDeletes();

            // Índices obrigatórios
            $table->index(['empresa_id', 'sync_status']);
            $table->unique(['empresa_id', 'codigo'], 'config_sites_empresa_codigo_unique');
        });

        // Tabela de grupos de configuração
        Schema::create('config_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Empresa proprietária');
            $table->string('codigo', 50)->comment('Código único do grupo');
            $table->string('nome', 100)->comment('Nome de exibição do grupo');
            $table->text('descricao')->nullable()->comment('Descrição do grupo');
            $table->foreignId('grupo_pai_id')->nullable()->constrained('config_groups')->onDelete('set null')->comment('ID do grupo pai para hierarquia');
            $table->string('icone', 50)->nullable()->comment('Classe de ícone para interface');
            $table->integer('ordem')->default(0)->comment('Ordem de exibição do grupo');
            $table->boolean('ativo')->default(true)->comment('Status do grupo');

            // Campos obrigatórios de sincronização
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            $table->timestamps();
            $table->softDeletes();

            // Índices obrigatórios
            $table->index(['empresa_id', 'sync_status']);
            $table->unique(['empresa_id', 'codigo'], 'config_groups_empresa_codigo_unique');
        });

        // Tabela de definições de configuração
        Schema::create('config_definitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Empresa proprietária');
            $table->string('chave', 100)->comment('Nome da chave de configuração');
            $table->text('descricao')->nullable()->comment('Descrição da configuração');
            $table->enum('tipo', ['string', 'integer', 'float', 'boolean', 'array', 'json', 'date', 'datetime'])->default('string')->comment('Tipo de dado da configuração');
            $table->foreignId('grupo_id')->nullable()->constrained('config_groups')->onDelete('set null')->comment('Grupo ao qual pertence');
            $table->text('valor_padrao')->nullable()->comment('Valor padrão quando não definido');
            $table->boolean('obrigatorio')->default(false)->comment('Se a configuração é obrigatória');
            $table->string('validacao')->nullable()->comment('Regras de validação (regex, min, max, etc)');
            $table->json('opcoes')->nullable()->comment('Opções possíveis para seleção');
            $table->boolean('visivel_admin')->default(true)->comment('Visível na interface de administração');
            $table->boolean('editavel')->default(true)->comment('Se pode ser editado via interface');
            $table->boolean('avancado')->default(false)->comment('Se é uma configuração avançada');
            $table->integer('ordem')->default(0)->comment('Ordem de exibição');
            $table->text('dica')->nullable()->comment('Dica de ajuda na interface');
            $table->boolean('ativo')->default(true)->comment('Status da definição');

            // Campos obrigatórios de sincronização
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            $table->timestamps();
            $table->softDeletes();

            // Índices obrigatórios
            $table->index(['empresa_id', 'sync_status']);
            $table->unique(['empresa_id', 'chave'], 'config_definitions_empresa_chave_unique');
        });

        // Tabela de valores das configurações
        Schema::create('config_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Empresa proprietária');
            $table->foreignId('config_id')->constrained('config_definitions')->onDelete('cascade')->comment('ID da definição da configuração');
            $table->foreignId('site_id')->nullable()->constrained('config_sites')->onDelete('cascade')->comment('ID do site específico ou NULL para global');
            $table->foreignId('ambiente_id')->nullable()->constrained('config_environments')->onDelete('cascade')->comment('ID do ambiente específico ou NULL para todos');
            $table->text('valor')->nullable()->comment('Valor da configuração');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->comment('ID do usuário que fez a última alteração');

            // Campos obrigatórios de sincronização
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            $table->timestamps();
            $table->softDeletes();

            // Índices obrigatórios
            $table->index(['empresa_id', 'sync_status']);
            $table->unique(['empresa_id', 'config_id', 'site_id', 'ambiente_id'], 'config_values_unique');
        });

        // Tabela de histórico de alterações
        Schema::create('config_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Empresa proprietária');
            $table->foreignId('config_value_id')->constrained('config_values')->onDelete('cascade')->comment('ID do valor da configuração');
            $table->text('valor_anterior')->nullable()->comment('Valor anterior');
            $table->text('valor_novo')->nullable()->comment('Novo valor');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->comment('ID do usuário que fez a alteração');
            $table->string('ip', 45)->nullable()->comment('IP do usuário');
            $table->text('user_agent')->nullable()->comment('User-Agent do navegador');
            $table->text('motivo')->nullable()->comment('Motivo da alteração');

            // Campos obrigatórios de sincronização
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            $table->timestamps();
            $table->softDeletes();

            // Índices obrigatórios
            $table->index(['empresa_id', 'sync_status']);
            $table->index(['config_value_id', 'created_at']);
        });

        // Tabela de papéis para controle de acesso
        Schema::create('config_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Empresa proprietária');
            $table->string('nome', 100)->comment('Nome do papel');
            $table->text('descricao')->nullable()->comment('Descrição do papel');

            // Campos obrigatórios de sincronização
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            $table->timestamps();
            $table->softDeletes();

            // Índices obrigatórios
            $table->index(['empresa_id', 'sync_status']);
            $table->unique(['empresa_id', 'nome'], 'config_roles_empresa_nome_unique');
        });

        // Tabela de permissões de acesso
        Schema::create('config_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Empresa proprietária');
            $table->foreignId('role_id')->constrained('config_roles')->onDelete('cascade')->comment('ID do papel');
            $table->foreignId('grupo_id')->nullable()->constrained('config_groups')->onDelete('cascade')->comment('ID do grupo de configuração ou NULL para todos');
            $table->foreignId('config_id')->nullable()->constrained('config_definitions')->onDelete('cascade')->comment('ID da configuração específica ou NULL para todas do grupo');
            $table->boolean('pode_ler')->default(true)->comment('Permissão para leitura');
            $table->boolean('pode_editar')->default(false)->comment('Permissão para edição');

            // Campos obrigatórios de sincronização
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            $table->timestamps();
            $table->softDeletes();

            // Índices obrigatórios
            $table->index(['empresa_id', 'sync_status']);
        });

        // Tabela de mapeamento de URLs
        Schema::create('config_url_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Empresa proprietária');
            $table->foreignId('site_id')->constrained('config_sites')->onDelete('cascade')->comment('ID do site');
            $table->foreignId('ambiente_id')->constrained('config_environments')->onDelete('cascade')->comment('ID do ambiente');
            $table->string('dominio')->comment('Domínio do site');
            $table->string('base_url')->comment('URL base do site');
            $table->string('api_url')->nullable()->comment('URL da API');
            $table->string('asset_url')->nullable()->comment('URL para assets');

            // Campos obrigatórios de sincronização
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            $table->timestamps();
            $table->softDeletes();

            // Índices obrigatórios
            $table->index(['empresa_id', 'sync_status']);
            $table->unique(['empresa_id', 'site_id', 'ambiente_id'], 'config_url_mappings_unique');
        });

        // Tabela de conexões de banco de dados
        Schema::create('config_db_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->comment('Empresa proprietária');
            $table->string('nome', 100)->comment('Nome da conexão');
            $table->foreignId('ambiente_id')->constrained('config_environments')->onDelete('cascade')->comment('ID do ambiente');
            $table->string('driver', 50)->default('mysql')->comment('Driver de banco de dados');
            $table->string('host')->comment('Host do banco de dados');
            $table->unsignedInteger('porta')->default(3306)->comment('Porta do banco de dados');
            $table->string('banco')->comment('Nome do banco de dados');
            $table->string('usuario')->comment('Usuário do banco de dados');
            $table->text('senha')->comment('Senha do banco de dados (criptografada)');
            $table->string('charset', 20)->default('utf8mb4')->comment('Charset do banco');
            $table->string('collation', 20)->default('utf8mb4_unicode_ci')->comment('Collation do banco');
            $table->string('prefixo', 20)->nullable()->comment('Prefixo das tabelas');
            $table->boolean('padrao')->default(false)->comment('Se é a conexão padrão');

            // Campos obrigatórios de sincronização
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            $table->timestamps();
            $table->softDeletes();

            // Índices obrigatórios
            $table->index(['empresa_id', 'sync_status']);
            $table->unique(['empresa_id', 'ambiente_id', 'nome'], 'config_db_connections_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_db_connections');
        Schema::dropIfExists('config_url_mappings');
        Schema::dropIfExists('config_permissions');
        Schema::dropIfExists('config_roles');
        Schema::dropIfExists('config_history');
        Schema::dropIfExists('config_values');
        Schema::dropIfExists('config_definitions');
        Schema::dropIfExists('config_groups');
        Schema::dropIfExists('config_sites');
        Schema::dropIfExists('config_environments');
    }
};
