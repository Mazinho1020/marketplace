<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Criar tabela pessoas_enderecos
        if (!Schema::hasTable('pessoas_enderecos')) {
            Schema::create('pessoas_enderecos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pessoa_id');
                $table->enum('tipo', ['residencial', 'comercial', 'correspondencia', 'entrega', 'cobranca'])->default('residencial');
                $table->string('descricao', 100)->nullable();

                // Dados do endereço
                $table->string('cep', 10)->nullable();
                $table->string('logradouro', 255);
                $table->string('numero', 20)->nullable();
                $table->string('complemento', 100)->nullable();
                $table->string('bairro', 100);
                $table->string('cidade', 100);
                $table->string('estado', 2);
                $table->string('pais', 50)->default('Brasil');

                // Coordenadas geográficas
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();

                // Referências
                $table->string('ponto_referencia', 255)->nullable();
                $table->text('observacoes')->nullable();

                // Status
                $table->boolean('principal')->default(false);
                $table->boolean('ativo')->default(true);

                // Auditoria
                $table->timestamps();
                $table->string('sync_hash', 64)->nullable();
                $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
                $table->timestamp('sync_data')->useCurrent();

                $table->index(['pessoa_id']);
                $table->index(['tipo']);
                $table->index(['cep']);
                $table->index(['cidade', 'estado']);
                $table->index(['principal']);
                $table->index(['ativo']);
                $table->index(['sync_status', 'sync_data']);

                $table->foreign('pessoa_id')->references('id')->on('pessoas')->onDelete('cascade');
            });
        }

        // Criar tabela pessoas_contas_bancarias
        if (!Schema::hasTable('pessoas_contas_bancarias')) {
            Schema::create('pessoas_contas_bancarias', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pessoa_id');
                $table->enum('tipo', ['corrente', 'poupanca', 'salario', 'investimento'])->default('corrente');
                $table->string('descricao', 100)->nullable();

                // Dados bancários
                $table->string('banco_codigo', 10);
                $table->string('banco_nome', 100);
                $table->string('agencia', 20);
                $table->string('agencia_dv', 2)->nullable();
                $table->string('conta', 30);
                $table->string('conta_dv', 2)->nullable();
                $table->enum('tipo_conta', ['fisica', 'juridica'])->default('fisica');

                // PIX
                $table->string('chave_pix', 200)->nullable();
                $table->enum('tipo_chave_pix', ['cpf', 'cnpj', 'email', 'telefone', 'aleatoria'])->nullable();

                // Status e configurações
                $table->boolean('principal')->default(false);
                $table->boolean('ativo')->default(true);
                $table->boolean('usar_para_recebimento')->default(true);
                $table->boolean('usar_para_pagamento')->default(false);

                // Limites
                $table->decimal('limite_diario_pix', 10, 2)->nullable();
                $table->decimal('limite_mensal_pix', 10, 2)->nullable();

                // Auditoria
                $table->timestamps();
                $table->string('sync_hash', 64)->nullable();
                $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
                $table->timestamp('sync_data')->useCurrent();

                $table->index(['pessoa_id']);
                $table->index(['tipo']);
                $table->index(['banco_codigo']);
                $table->index(['principal']);
                $table->index(['ativo']);
                $table->index(['chave_pix']);
                $table->index(['sync_status', 'sync_data']);

                $table->foreign('pessoa_id')->references('id')->on('pessoas')->onDelete('cascade');
            });
        }

        // Criar tabela pessoas_documentos
        if (!Schema::hasTable('pessoas_documentos')) {
            Schema::create('pessoas_documentos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pessoa_id');
                $table->enum('tipo', ['cpf', 'rg', 'cnh', 'passaporte', 'cnpj', 'certidao_nascimento', 'certidao_casamento', 'comprovante_residencia', 'comprovante_renda', 'contrato_trabalho', 'outros'])->default('cpf');
                $table->string('descricao', 100)->nullable();

                // Dados do documento
                $table->string('numero', 50);
                $table->string('orgao_emissor', 50)->nullable();
                $table->date('data_emissao')->nullable();
                $table->date('data_vencimento')->nullable();
                $table->string('local_emissao', 100)->nullable();

                // Arquivos
                $table->string('arquivo_frente_url', 500)->nullable();
                $table->string('arquivo_verso_url', 500)->nullable();
                $table->string('arquivo_adicional_url', 500)->nullable();

                // Status
                $table->enum('status', ['pendente', 'aprovado', 'rejeitado', 'vencido'])->default('pendente');
                $table->text('observacoes')->nullable();
                $table->boolean('principal')->default(false);

                // Validação
                $table->timestamp('validado_em')->nullable();
                $table->string('validado_por', 100)->nullable();

                // Auditoria
                $table->timestamps();
                $table->string('sync_hash', 64)->nullable();
                $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
                $table->timestamp('sync_data')->useCurrent();

                $table->index(['pessoa_id']);
                $table->index(['tipo']);
                $table->index(['numero']);
                $table->index(['status']);
                $table->index(['data_vencimento']);
                $table->index(['principal']);
                $table->index(['sync_status', 'sync_data']);

                $table->foreign('pessoa_id')->references('id')->on('pessoas')->onDelete('cascade');
            });
        }

        // Criar tabela pessoas_dependentes
        if (!Schema::hasTable('pessoas_dependentes')) {
            Schema::create('pessoas_dependentes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pessoa_id');
                $table->enum('tipo', ['filho', 'conjuge', 'pai', 'mae', 'irmao', 'avo', 'outros'])->default('filho');

                // Dados básicos
                $table->string('nome', 255);
                $table->string('sobrenome', 255)->nullable();
                $table->date('data_nascimento')->nullable();
                $table->string('cpf', 14)->nullable();
                $table->string('rg', 25)->nullable();
                $table->enum('genero', ['masculino', 'feminino', 'outros'])->nullable();

                // Relacionamento
                $table->string('grau_parentesco', 50);
                $table->text('observacoes')->nullable();

                // Dados de contato
                $table->string('telefone', 30)->nullable();
                $table->string('email', 100)->nullable();

                // Benefícios
                $table->boolean('plano_saude')->default(false);
                $table->boolean('vale_alimentacao')->default(false);
                $table->boolean('vale_transporte')->default(false);
                $table->boolean('dependente_ir')->default(false);

                // Status
                $table->boolean('ativo')->default(true);

                // Auditoria
                $table->timestamps();
                $table->string('sync_hash', 64)->nullable();
                $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
                $table->timestamp('sync_data')->useCurrent();

                $table->index(['pessoa_id']);
                $table->index(['tipo']);
                $table->index(['cpf']);
                $table->index(['ativo']);
                $table->index(['sync_status', 'sync_data']);

                $table->foreign('pessoa_id')->references('id')->on('pessoas')->onDelete('cascade');
            });
        }

        // Criar tabela pessoas_historico_cargos
        if (!Schema::hasTable('pessoas_historico_cargos')) {
            Schema::create('pessoas_historico_cargos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pessoa_id');
                $table->unsignedBigInteger('departamento_id');
                $table->unsignedBigInteger('cargo_id');
                $table->unsignedBigInteger('superior_id')->nullable();

                // Dados da movimentação
                $table->enum('tipo_movimentacao', ['admissao', 'promocao', 'transferencia', 'rebaixamento', 'demissao', 'afastamento', 'retorno'])->default('admissao');
                $table->date('data_inicio');
                $table->date('data_fim')->nullable();
                $table->decimal('salario', 10, 2)->nullable();
                $table->text('motivo')->nullable();
                $table->text('observacoes')->nullable();

                // Responsável pela movimentação
                $table->string('autorizado_por', 100)->nullable();
                $table->timestamp('autorizado_em')->nullable();

                // Status
                $table->boolean('ativo')->default(true);

                // Auditoria
                $table->timestamps();
                $table->string('sync_hash', 64)->nullable();
                $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
                $table->timestamp('sync_data')->useCurrent();

                $table->index(['pessoa_id']);
                $table->index(['departamento_id']);
                $table->index(['cargo_id']);
                $table->index(['tipo_movimentacao']);
                $table->index(['data_inicio']);
                $table->index(['data_fim']);
                $table->index(['ativo']);
                $table->index(['sync_status', 'sync_data']);

                $table->foreign('pessoa_id')->references('id')->on('pessoas')->onDelete('cascade');
                $table->foreign('departamento_id')->references('id')->on('pessoas_departamentos')->onUpdate('cascade');
                $table->foreign('cargo_id')->references('id')->on('pessoas_cargos')->onUpdate('cascade');
                $table->foreign('superior_id')->references('id')->on('pessoas')->onUpdate('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('pessoas_historico_cargos');
        Schema::dropIfExists('pessoas_dependentes');
        Schema::dropIfExists('pessoas_documentos');
        Schema::dropIfExists('pessoas_contas_bancarias');
        Schema::dropIfExists('pessoas_enderecos');
    }
};
