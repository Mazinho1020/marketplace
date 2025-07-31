<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Config Environments
        Schema::create('config_environments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade')->comment('ID da empresa');
            $table->string('codigo', 50)->comment('Código único do ambiente (ex: online, offline)');
            $table->string('nome', 100)->comment('Nome de exibição do ambiente');
            $table->text('descricao')->nullable()->comment('Descrição detalhada do ambiente');
            $table->boolean('is_producao')->default(false)->comment('Indica se é ambiente de produção');
            $table->boolean('ativo')->default(true)->comment('Status do ambiente');
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['empresa_id', 'codigo']);
            $table->index(['empresa_id']);
        });

        // Config Sites
        Schema::create('config_sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade')->comment('ID da empresa');
            $table->string('codigo', 50)->comment('Código único do site (ex: sistema, pdv, fidelidade)');
            $table->string('nome', 100)->comment('Nome de exibição do site');
            $table->text('descricao')->nullable()->comment('Descrição do site');
            $table->string('base_url_padrao', 255)->nullable()->comment('URL base padrão do site');
            $table->boolean('ativo')->default(true)->comment('Status do site');
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['empresa_id', 'codigo']);
            $table->index(['empresa_id']);
        });

        // Config Groups
        Schema::create('config_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade')->comment('ID da empresa');
            $table->string('codigo', 50)->comment('Código único do grupo');
            $table->string('nome', 100)->comment('Nome de exibição do grupo');
            $table->text('descricao')->nullable()->comment('Descrição do grupo');
            $table->foreignId('grupo_pai_id')->nullable()->constrained('config_groups')->onDelete('set null')->comment('ID do grupo pai para hierarquia');
            $table->string('icone_class', 50)->nullable()->comment('Classe de ícone para interface');
            $table->integer('ordem')->default(0)->comment('Ordem de exibição do grupo');
            $table->boolean('ativo')->default(true)->comment('Status do grupo');
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['empresa_id', 'codigo']);
            $table->index(['empresa_id']);
        });

        // Config Definitions
        Schema::create('config_definitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade')->comment('ID da empresa');
            $table->string('chave', 100)->comment('Nome da chave de configuração');
            $table->string('nome', 100)->comment('Nome amigável da configuração');
            $table->text('descricao')->nullable()->comment('Descrição da configuração');
            $table->enum('tipo', ['string', 'integer', 'float', 'boolean', 'array', 'json', 'url', 'email', 'password'])->default('string')->comment('Tipo de dado da configuração');
            $table->foreignId('grupo_id')->nullable()->constrained('config_groups')->onDelete('set null')->comment('Grupo ao qual pertence');
            $table->text('valor_padrao')->nullable()->comment('Valor padrão quando não definido');
            $table->boolean('obrigatorio')->default(false)->comment('Se a configuração é obrigatória');
            $table->integer('min_length')->nullable()->comment('Tamanho mínimo');
            $table->integer('max_length')->nullable()->comment('Tamanho máximo');
            $table->string('regex_validacao', 255)->nullable()->comment('Regex para validação');
            $table->text('opcoes')->nullable()->comment('Opções possíveis para seleção (JSON)');
            $table->boolean('editavel')->default(true)->comment('Se pode ser editado via interface');
            $table->boolean('avancado')->default(false)->comment('Se é uma configuração avançada');
            $table->integer('ordem')->default(0)->comment('Ordem de exibição');
            $table->text('dica')->nullable()->comment('Dica de ajuda na interface');
            $table->text('ajuda')->nullable()->comment('Texto de ajuda detalhado');
            $table->boolean('ativo')->default(true)->comment('Status da definição');
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['empresa_id', 'chave']);
            $table->index(['empresa_id']);
        });

        // Config Values
        Schema::create('config_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade')->comment('ID da empresa');
            $table->foreignId('config_id')->constrained('config_definitions')->onDelete('cascade')->comment('ID da definição da configuração');
            $table->foreignId('site_id')->nullable()->constrained('config_sites')->onDelete('cascade')->comment('ID do site específico ou NULL para global');
            $table->foreignId('ambiente_id')->nullable()->constrained('config_environments')->onDelete('cascade')->comment('ID do ambiente específico ou NULL para todos');
            $table->text('valor')->nullable()->comment('Valor da configuração');
            $table->unsignedBigInteger('usuario_id')->nullable()->comment('ID do usuário que fez a última alteração');
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['empresa_id', 'config_id', 'site_id', 'ambiente_id']);
            $table->index(['empresa_id']);
        });

        // Config History
        Schema::create('config_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade')->comment('ID da empresa');
            $table->foreignId('config_id')->constrained('config_definitions')->onDelete('cascade')->comment('ID da configuração');
            $table->foreignId('site_id')->nullable()->constrained('config_sites')->onDelete('set null')->comment('ID do site');
            $table->foreignId('ambiente_id')->nullable()->constrained('config_environments')->onDelete('set null')->comment('ID do ambiente');
            $table->enum('acao', ['create', 'update', 'delete'])->comment('Ação realizada');
            $table->text('valor_anterior')->nullable()->comment('Valor anterior');
            $table->text('valor_novo')->nullable()->comment('Novo valor');
            $table->unsignedBigInteger('usuario_id')->nullable()->comment('ID do usuário que fez a alteração');
            $table->string('usuario_nome', 100)->nullable()->comment('Nome do usuário');
            $table->string('ip', 45)->nullable()->comment('IP do usuário');
            $table->text('user_agent')->nullable()->comment('User-Agent do navegador');
            $table->text('contexto_info')->nullable()->comment('Informações de contexto');
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['empresa_id']);
            $table->index(['config_id']);
            $table->index(['usuario_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_history');
        Schema::dropIfExists('config_values');
        Schema::dropIfExists('config_definitions');
        Schema::dropIfExists('config_groups');
        Schema::dropIfExists('config_sites');
        Schema::dropIfExists('config_environments');
    }
};
