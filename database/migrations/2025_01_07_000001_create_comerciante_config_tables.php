<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Criar tabela config_groups se não existir
        if (!Schema::hasTable('config_groups')) {
            Schema::create('config_groups', function (Blueprint $table) {
                $table->id();
                $table->integer('empresa_id');
                $table->string('codigo', 50);
                $table->string('nome', 100);
                $table->text('descricao')->nullable();
                $table->unsignedBigInteger('grupo_pai_id')->nullable();
                $table->string('icone', 50)->nullable();
                $table->string('icone_class', 100)->nullable();
                $table->integer('ordem')->default(0);
                $table->boolean('ativo')->default(true);
                $table->timestamps();

                $table->unique(['empresa_id', 'codigo']);
                $table->index(['empresa_id', 'ativo']);
                $table->foreign('grupo_pai_id')->references('id')->on('config_groups');
            });
        }

        // Criar tabela config_definitions se não existir
        if (!Schema::hasTable('config_definitions')) {
            Schema::create('config_definitions', function (Blueprint $table) {
                $table->id();
                $table->integer('empresa_id');
                $table->string('chave', 100);
                $table->string('nome', 100);
                $table->text('descricao')->nullable();
                $table->enum('tipo', ['string', 'integer', 'float', 'boolean', 'json', 'select', 'text']);
                $table->unsignedBigInteger('grupo_id');
                $table->text('valor_padrao')->nullable();
                $table->json('opcoes')->nullable();
                $table->string('validacao', 255)->nullable();
                $table->boolean('obrigatorio')->default(false);
                $table->boolean('editavel')->default(true);
                $table->integer('ordem')->default(0);
                $table->string('dica', 255)->nullable();
                $table->string('categoria', 50)->nullable();
                $table->boolean('ativo')->default(true);
                $table->timestamps();

                $table->unique(['empresa_id', 'chave']);
                $table->index(['empresa_id', 'grupo_id']);
                $table->foreign('grupo_id')->references('id')->on('config_groups');
            });
        }

        // Criar tabela config_values se não existir
        if (!Schema::hasTable('config_values')) {
            Schema::create('config_values', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('config_definition_id');
                $table->text('valor');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->unique('config_definition_id');
                $table->foreign('config_definition_id')->references('id')->on('config_definitions');
            });
        }

        // Criar tabela config_history se não existir
        if (!Schema::hasTable('config_history')) {
            Schema::create('config_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('config_definition_id');
                $table->text('valor_anterior')->nullable();
                $table->text('valor_novo');
                $table->unsignedBigInteger('usuario_id')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->text('observacoes')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['config_definition_id', 'created_at']);
                $table->foreign('config_definition_id')->references('id')->on('config_definitions');
            });
        }

        // Inserir grupos de configuração
        $this->insertConfigGroups();

        // Inserir definições de configuração
        $this->insertConfigDefinitions();
    }

    public function down()
    {
        Schema::dropIfExists('config_history');
        Schema::dropIfExists('config_values');
        Schema::dropIfExists('config_definitions');
        Schema::dropIfExists('config_groups');
    }

    private function insertConfigGroups()
    {
        $grupos = [
            // Configurações Principais
            [
                'empresa_id' => 2,
                'codigo' => 'comerciante_pessoas',
                'nome' => 'Gestão de Pessoas',
                'descricao' => 'Configurações do sistema de pessoas',
                'grupo_pai_id' => null,
                'icone' => 'fas fa-users',
                'icone_class' => 'fas fa-users',
                'ordem' => 1,
                'ativo' => true
            ],
            [
                'empresa_id' => 2,
                'codigo' => 'comerciante_rh',
                'nome' => 'Recursos Humanos',
                'descricao' => 'Configurações de RH e folha de pagamento',
                'grupo_pai_id' => null,
                'icone' => 'fas fa-user-tie',
                'icone_class' => 'fas fa-user-tie',
                'ordem' => 2,
                'ativo' => true
            ],
            [
                'empresa_id' => 2,
                'codigo' => 'comerciante_vendas',
                'nome' => 'Vendas',
                'descricao' => 'Configurações do sistema de vendas',
                'grupo_pai_id' => null,
                'icone' => 'fas fa-shopping-cart',
                'icone_class' => 'fas fa-shopping-cart',
                'ordem' => 3,
                'ativo' => true
            ]
        ];

        foreach ($grupos as $grupo) {
            DB::table('config_groups')->updateOrInsert(
                ['empresa_id' => $grupo['empresa_id'], 'codigo' => $grupo['codigo']],
                array_merge($grupo, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Inserir subgrupos
        $pessoasGrupoId = DB::table('config_groups')
            ->where('empresa_id', 2)
            ->where('codigo', 'comerciante_pessoas')
            ->value('id');

        $subgrupos = [
            [
                'empresa_id' => 2,
                'codigo' => 'pessoas_validacao',
                'nome' => 'Validação',
                'descricao' => 'Regras de validação de pessoas',
                'grupo_pai_id' => $pessoasGrupoId,
                'icone_class' => 'fas fa-check-circle',
                'ordem' => 1,
                'ativo' => true
            ],
            [
                'empresa_id' => 2,
                'codigo' => 'pessoas_defaults',
                'nome' => 'Padrões',
                'descricao' => 'Valores padrão para pessoas',
                'grupo_pai_id' => $pessoasGrupoId,
                'icone_class' => 'fas fa-cog',
                'ordem' => 2,
                'ativo' => true
            ]
        ];

        foreach ($subgrupos as $subgrupo) {
            DB::table('config_groups')->updateOrInsert(
                ['empresa_id' => $subgrupo['empresa_id'], 'codigo' => $subgrupo['codigo']],
                array_merge($subgrupo, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function insertConfigDefinitions()
    {
        $validacaoGrupoId = DB::table('config_groups')
            ->where('empresa_id', 2)
            ->where('codigo', 'pessoas_validacao')
            ->value('id');

        $padroesGrupoId = DB::table('config_groups')
            ->where('empresa_id', 2)
            ->where('codigo', 'pessoas_defaults')
            ->value('id');

        $configuracoes = [
            // Validação
            [
                'empresa_id' => 2,
                'chave' => 'pessoas_cpf_obrigatorio',
                'nome' => 'CPF Obrigatório',
                'descricao' => 'Exigir CPF no cadastro de pessoas',
                'tipo' => 'boolean',
                'grupo_id' => $validacaoGrupoId,
                'valor_padrao' => 'true',
                'obrigatorio' => true,
                'editavel' => true,
                'ordem' => 1,
                'dica' => 'CPF será obrigatório para clientes e funcionários',
                'ativo' => true
            ],
            [
                'empresa_id' => 2,
                'chave' => 'pessoas_email_obrigatorio',
                'nome' => 'Email Obrigatório',
                'descricao' => 'Exigir email no cadastro',
                'tipo' => 'boolean',
                'grupo_id' => $validacaoGrupoId,
                'valor_padrao' => 'false',
                'obrigatorio' => false,
                'editavel' => true,
                'ordem' => 2,
                'dica' => 'Email será obrigatório no cadastro',
                'ativo' => true
            ],
            // Padrões
            [
                'empresa_id' => 2,
                'chave' => 'pessoas_limite_credito_padrao',
                'nome' => 'Limite Crédito Padrão',
                'descricao' => 'Limite de crédito padrão para clientes',
                'tipo' => 'float',
                'grupo_id' => $padroesGrupoId,
                'valor_padrao' => '500.00',
                'obrigatorio' => false,
                'editavel' => true,
                'ordem' => 1,
                'dica' => 'Valor padrão para limite de crédito de novos clientes',
                'ativo' => true
            ]
        ];

        foreach ($configuracoes as $config) {
            DB::table('config_definitions')->updateOrInsert(
                ['empresa_id' => $config['empresa_id'], 'chave' => $config['chave']],
                array_merge($config, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
};
