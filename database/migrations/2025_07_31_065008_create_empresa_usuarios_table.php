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
        Schema::create('empresa_usuarios', function (Blueprint $table) {
            // 1. CHAVE PRIMÁRIA
            $table->id();

            // 2. CAMPO DE EMPRESA (MULTITENANCY)
            $table->bigInteger('empresa_id')->default(1)->comment('ID da empresa (multitenancy)');
            // TODO: Adicionar foreign key quando criar tabela empresas
            /*
            $table->foreign('empresa_id', 'fk_empresa_usuarios_empresa_id')
                ->references('id')
                ->on('empresas')
                ->onDelete('cascade');
            */

            // 3. CAMPOS DE IDENTIFICAÇÃO
            $table->string('nome', 100)->comment('Nome completo do usuário');
            $table->string('email')->unique()->comment('Email único para login');
            $table->timestamp('email_verified_at')->nullable()->comment('Data de verificação do email');
            $table->string('password')->comment('Senha criptografada');
            $table->rememberToken()->comment('Token para "lembrar de mim"');

            // 4. CAMPOS ADICIONAIS
            $table->string('telefone', 20)->nullable()->comment('Telefone de contato');
            $table->date('data_nascimento')->nullable()->comment('Data de nascimento');
            $table->enum('sexo', ['M', 'F', 'O'])->nullable()->comment('Sexo: M=Masculino, F=Feminino, O=Outro');
            $table->string('cpf', 14)->nullable()->unique()->comment('CPF do usuário');
            $table->string('avatar')->nullable()->comment('URL da foto de perfil');

            // 5. CAMPOS DE STATUS E CONTROLE
            $table->enum('status', ['ativo', 'inativo', 'suspenso', 'bloqueado'])
                ->default('ativo')
                ->comment('Status do usuário');

            $table->foreignId('perfil_id')
                ->nullable()
                ->comment('ID do perfil de permissões (legacy)');

            // 6. CAMPOS DE AUDITORIA
            $table->timestamp('ultimo_login')->nullable()->comment('Data do último login');
            $table->string('ultimo_ip', 45)->nullable()->comment('Último IP de acesso');
            $table->integer('tentativas_login')->default(0)->comment('Contador de tentativas de login');
            $table->timestamp('bloqueado_ate')->nullable()->comment('Bloqueio temporário até');

            // 7. SINCRONIZAÇÃO MULTI-SITES (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable()->comment('Hash MD5 para controle de sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])
                ->default('pending')
                ->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            // 8. TIMESTAMPS PADRÃO (OBRIGATÓRIO)
            $table->timestamps();
            $table->softDeletes();

            // 9. ÍNDICES OBRIGATÓRIOS
            $table->index(['empresa_id', 'status'], 'idx_empresa_status');
            $table->index('email', 'idx_email');
            $table->index('cpf', 'idx_cpf');
            $table->index('telefone', 'idx_telefone');
            $table->index('ultimo_login', 'idx_ultimo_login');
            $table->index('created_at', 'idx_created_at');
            $table->index('sync_status', 'idx_sync_status');
            $table->index('deleted_at', 'idx_deleted_at');
            $table->index(['empresa_id', 'sync_status', 'sync_data'], 'idx_sync_control');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_usuarios');
    }
};
