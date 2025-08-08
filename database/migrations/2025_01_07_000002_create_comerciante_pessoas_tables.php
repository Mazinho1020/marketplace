<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Criar tabela pessoas_departamentos
        if (!Schema::hasTable('pessoas_departamentos')) {
            Schema::create('pessoas_departamentos', function (Blueprint $table) {
                $table->id();
                $table->integer('empresa_id');
                $table->string('codigo', 20)->nullable();
                $table->string('nome', 255);
                $table->text('descricao')->nullable();
                $table->integer('responsavel_id')->nullable();
                $table->string('centro_custo', 50)->nullable();
                $table->boolean('relacionado_producao')->default(false);
                $table->boolean('ativo')->default(true);
                $table->integer('ordem')->default(0);
                $table->timestamps();
                $table->string('sync_hash', 64)->nullable();
                $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
                $table->timestamp('sync_data')->useCurrent();

                $table->unique(['empresa_id', 'codigo']);
                $table->unique(['empresa_id', 'nome']);
                $table->index(['empresa_id']);
                $table->index(['responsavel_id']);
                $table->index(['ativo']);
                $table->index(['sync_status', 'sync_data']);
            });
        }

        // Criar tabela pessoas_cargos
        if (!Schema::hasTable('pessoas_cargos')) {
            Schema::create('pessoas_cargos', function (Blueprint $table) {
                $table->id();
                $table->integer('empresa_id');
                $table->unsignedBigInteger('departamento_id');
                $table->string('codigo', 20)->nullable();
                $table->string('nome', 255);
                $table->text('descricao')->nullable();
                $table->decimal('salario_base', 10, 2)->nullable();
                $table->decimal('salario_maximo', 10, 2)->nullable();
                $table->integer('nivel_hierarquico')->default(1);
                $table->boolean('requer_superior')->default(false);
                $table->integer('carga_horaria_semanal')->default(44);
                $table->boolean('ativo')->default(true);
                $table->integer('ordem')->default(0);
                $table->timestamps();
                $table->string('sync_hash', 64)->nullable();
                $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
                $table->timestamp('sync_data')->useCurrent();

                $table->unique(['departamento_id', 'codigo']);
                $table->unique(['departamento_id', 'nome']);
                $table->index(['empresa_id']);
                $table->index(['departamento_id']);
                $table->index(['nivel_hierarquico']);
                $table->index(['ativo']);
                $table->index(['sync_status', 'sync_data']);

                $table->foreign('departamento_id')->references('id')->on('pessoas_departamentos')->onUpdate('cascade');
            });
        }

        // Criar tabela pessoas
        if (!Schema::hasTable('pessoas')) {
            Schema::create('pessoas', function (Blueprint $table) {
                $table->id();
                $table->integer('empresa_id');
                $table->string('codigo', 20)->nullable();

                // Tipos (usando SET para permitir múltiplos tipos)
                $table->set('tipo', ['cliente', 'funcionario', 'fornecedor', 'entregador']);

                // Dados básicos
                $table->string('nome', 255);
                $table->string('sobrenome', 255)->nullable();
                $table->string('nome_social', 255)->nullable();
                $table->date('data_nascimento')->nullable();
                $table->string('cpf_cnpj', 25)->nullable();
                $table->string('rg', 25)->nullable();
                $table->string('orgao_emissor', 20)->nullable();
                $table->enum('estado_civil', ['solteiro', 'casado', 'divorciado', 'viuvo', 'uniao_estavel'])->nullable();
                $table->enum('genero', ['masculino', 'feminino', 'outros', 'nao_informar'])->nullable();
                $table->string('nacionalidade', 50)->default('Brasileira');

                // Contatos
                $table->string('telefone', 30)->nullable();
                $table->string('whatsapp', 30)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('email_secundario', 100)->nullable();

                // Status geral
                $table->enum('status', ['ativo', 'inativo', 'suspenso', 'bloqueado'])->default('ativo');
                $table->text('observacoes')->nullable();
                $table->string('foto_url', 500)->nullable();

                // Dados específicos de funcionários
                $table->unsignedBigInteger('departamento_id')->nullable();
                $table->unsignedBigInteger('cargo_id')->nullable();
                $table->unsignedBigInteger('superior_id')->nullable();
                $table->date('data_admissao')->nullable();
                $table->string('numero_registro', 20)->nullable();
                $table->decimal('salario_atual', 10, 2)->nullable();
                $table->integer('dia_vencimento_salario')->nullable();
                $table->enum('tipo_contratacao', ['CLT', 'PJ', 'Diarista', 'Terceirizado', 'Estagiario', 'Entregador', 'Freelancer'])->nullable();
                $table->date('data_demissao')->nullable();
                $table->text('motivo_demissao')->nullable();
                $table->enum('situacao_trabalhista', ['ativo', 'afastado', 'ferias', 'licenca', 'demitido'])->default('ativo');

                // Dados específicos de clientes/fornecedores
                $table->boolean('pessoa_juridica')->default(false);
                $table->string('inscricao_estadual', 30)->nullable();
                $table->string('inscricao_municipal', 30)->nullable();
                $table->string('nome_fantasia', 255)->nullable();
                $table->string('website', 255)->nullable();

                // Limites comerciais
                $table->decimal('limite_credito', 10, 2)->default(0.00);
                $table->decimal('limite_fiado', 10, 2)->default(0.00);
                $table->integer('prazo_pagamento_padrao')->nullable();
                $table->enum('rating', ['A', 'B', 'C', 'D', 'E'])->nullable();
                $table->unsignedBigInteger('categoria_id')->nullable();

                // Dados bancários principais
                $table->unsignedBigInteger('conta_bancaria_principal_id')->nullable();
                $table->string('chave_pix', 200)->nullable();
                $table->enum('tipo_chave_pix', ['cpf', 'cnpj', 'email', 'telefone', 'aleatoria'])->nullable();

                // Sistema de afiliados (para clientes)
                $table->string('afiliado_codigo', 20)->nullable();
                $table->enum('afiliado_nivel', ['afiliado', 'bronze', 'prata', 'ouro'])->nullable();
                $table->decimal('afiliado_taxa_comissao', 5, 2)->nullable();
                $table->decimal('afiliado_total_vendas', 15, 2)->default(0.00);
                $table->decimal('afiliado_total_comissoes', 15, 2)->default(0.00);
                $table->decimal('afiliado_total_pago', 15, 2)->default(0.00);

                // Planos/assinatura (para clientes)
                $table->bigInteger('plano_atual_id')->nullable();
                $table->enum('plano_status', ['trial', 'ativo', 'suspenso', 'cancelado'])->nullable();
                $table->timestamp('plano_inicio')->nullable();
                $table->timestamp('plano_vencimento')->nullable();
                $table->timestamp('plano_trial_expira')->nullable();
                $table->string('chave_licenca', 100)->nullable();
                $table->string('chave_api', 100)->nullable();

                // Endereço principal
                $table->unsignedBigInteger('endereco_principal_id')->nullable();

                // Auditoria e sincronização
                $table->timestamps();
                $table->softDeletes();
                $table->string('sync_hash', 64)->nullable();
                $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
                $table->timestamp('sync_data')->useCurrent();

                $table->index(['empresa_id']);
                $table->index(['tipo']);
                $table->index(['status']);
                $table->index(['cpf_cnpj']);
                $table->index(['email']);
                $table->index(['departamento_id']);
                $table->index(['cargo_id']);
                $table->index(['superior_id']);
                $table->index(['sync_status', 'sync_data']);
            });
        }

        // Inserir alguns departamentos e cargos básicos
        $this->insertBasicData();
    }

    public function down()
    {
        Schema::dropIfExists('pessoas');
        Schema::dropIfExists('pessoas_cargos');
        Schema::dropIfExists('pessoas_departamentos');
    }

    private function insertBasicData()
    {
        // Inserir departamentos básicos
        $departamentos = [
            [
                'empresa_id' => 2,
                'codigo' => 'ADM',
                'nome' => 'Administração',
                'descricao' => 'Departamento administrativo',
                'relacionado_producao' => false,
                'ativo' => true,
                'ordem' => 1
            ],
            [
                'empresa_id' => 2,
                'codigo' => 'VEN',
                'nome' => 'Vendas',
                'descricao' => 'Departamento de vendas',
                'relacionado_producao' => false,
                'ativo' => true,
                'ordem' => 2
            ],
            [
                'empresa_id' => 2,
                'codigo' => 'FIN',
                'nome' => 'Financeiro',
                'descricao' => 'Departamento financeiro',
                'relacionado_producao' => false,
                'ativo' => true,
                'ordem' => 3
            ]
        ];

        foreach ($departamentos as $dept) {
            DB::table('pessoas_departamentos')->updateOrInsert(
                ['empresa_id' => $dept['empresa_id'], 'codigo' => $dept['codigo']],
                array_merge($dept, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Inserir cargos básicos
        $admDeptId = DB::table('pessoas_departamentos')
            ->where('empresa_id', 2)
            ->where('codigo', 'ADM')
            ->value('id');

        $venDeptId = DB::table('pessoas_departamentos')
            ->where('empresa_id', 2)
            ->where('codigo', 'VEN')
            ->value('id');

        $cargos = [
            [
                'empresa_id' => 2,
                'departamento_id' => $admDeptId,
                'codigo' => 'GER',
                'nome' => 'Gerente',
                'descricao' => 'Gerente do departamento',
                'salario_base' => 3000.00,
                'salario_maximo' => 5000.00,
                'nivel_hierarquico' => 3,
                'requer_superior' => false,
                'ativo' => true,
                'ordem' => 1
            ],
            [
                'empresa_id' => 2,
                'departamento_id' => $venDeptId,
                'codigo' => 'VEN',
                'nome' => 'Vendedor',
                'descricao' => 'Vendedor',
                'salario_base' => 1500.00,
                'salario_maximo' => 3000.00,
                'nivel_hierarquico' => 1,
                'requer_superior' => true,
                'ativo' => true,
                'ordem' => 1
            ]
        ];

        foreach ($cargos as $cargo) {
            DB::table('pessoas_cargos')->updateOrInsert(
                ['departamento_id' => $cargo['departamento_id'], 'codigo' => $cargo['codigo']],
                array_merge($cargo, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
};
