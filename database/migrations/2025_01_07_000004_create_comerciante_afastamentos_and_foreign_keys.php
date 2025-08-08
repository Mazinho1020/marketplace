<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Criar tabela pessoas_afastamentos
        if (!Schema::hasTable('pessoas_afastamentos')) {
            Schema::create('pessoas_afastamentos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pessoa_id');
                $table->enum('tipo', ['ferias', 'licenca_medica', 'licenca_maternidade', 'licenca_paternidade', 'falta_justificada', 'falta_injustificada', 'suspensao', 'afastamento_inss', 'outros'])->default('ferias');

                // Datas
                $table->date('data_inicio');
                $table->date('data_fim');
                $table->date('data_prevista_retorno')->nullable();
                $table->date('data_real_retorno')->nullable();
                $table->integer('dias_totais')->nullable();

                // Status
                $table->enum('status', ['programado', 'em_andamento', 'finalizado', 'cancelado'])->default('programado');

                // Documentação
                $table->string('numero_protocolo', 50)->nullable();
                $table->string('documento_url', 500)->nullable();
                $table->text('motivo');
                $table->text('observacoes')->nullable();

                // Autorização
                $table->string('autorizado_por', 100)->nullable();
                $table->timestamp('autorizado_em')->nullable();

                // Dados médicos (se aplicável)
                $table->string('cid', 10)->nullable();
                $table->string('medico_nome', 255)->nullable();
                $table->string('medico_crm', 20)->nullable();

                // Controle RH
                $table->boolean('desconta_salario')->default(false);
                $table->boolean('desconta_ferias')->default(false);
                $table->boolean('mantem_beneficios')->default(true);
                $table->decimal('percentual_salario', 5, 2)->default(100.00);

                // Auditoria
                $table->timestamps();
                $table->string('sync_hash', 64)->nullable();
                $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
                $table->timestamp('sync_data')->useCurrent();

                $table->index(['pessoa_id']);
                $table->index(['tipo']);
                $table->index(['status']);
                $table->index(['data_inicio']);
                $table->index(['data_fim']);
                $table->index(['data_prevista_retorno']);
                $table->index(['sync_status', 'sync_data']);

                $table->foreign('pessoa_id')->references('id')->on('pessoas')->onDelete('cascade');
            });
        }

        // Adicionar foreign keys nas tabelas pessoas para referências circulares
        Schema::table('pessoas', function (Blueprint $table) {
            $table->foreign('departamento_id')->references('id')->on('pessoas_departamentos')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('cargo_id')->references('id')->on('pessoas_cargos')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('superior_id')->references('id')->on('pessoas')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('endereco_principal_id')->references('id')->on('pessoas_enderecos')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('conta_bancaria_principal_id')->references('id')->on('pessoas_contas_bancarias')->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down()
    {
        // Remover foreign keys primeiro
        Schema::table('pessoas', function (Blueprint $table) {
            $table->dropForeign(['departamento_id']);
            $table->dropForeign(['cargo_id']);
            $table->dropForeign(['superior_id']);
            $table->dropForeign(['endereco_principal_id']);
            $table->dropForeign(['conta_bancaria_principal_id']);
        });

        Schema::dropIfExists('pessoas_afastamentos');
    }
};
