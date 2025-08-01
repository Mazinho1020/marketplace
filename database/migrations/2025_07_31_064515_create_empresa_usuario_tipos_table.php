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
        Schema::create('empresa_usuario_tipos', function (Blueprint $table) {
            // 1. CHAVE PRIMÁRIA
            $table->id();

            // 2. CAMPOS ESPECÍFICOS
            $table->string('codigo', 50)->unique()->comment('Código único do tipo (cliente, admin, comerciante)');
            $table->string('nome', 100)->comment('Nome de exibição');
            $table->text('descricao')->nullable()->comment('Descrição do tipo de usuário');
            $table->integer('nivel_acesso')->default(1)->comment('Nível hierárquico de acesso (1=mais baixo)');
            $table->boolean('is_active')->default(true)->comment('Status ativo/inativo');

            // 3. SINCRONIZAÇÃO MULTI-SITES (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable()->comment('Hash MD5 para controle de sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])
                ->default('pending')
                ->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            // 4. TIMESTAMPS PADRÃO (OBRIGATÓRIO)
            $table->timestamps();
            $table->softDeletes();

            // 5. ÍNDICES OBRIGATÓRIOS
            $table->index('codigo', 'idx_codigo');
            $table->index('nivel_acesso', 'idx_nivel_acesso');
            $table->index('is_active', 'idx_is_active');
            $table->index('created_at', 'idx_created_at');
            $table->index('sync_status', 'idx_sync_status');
            $table->index('deleted_at', 'idx_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_usuario_tipos');
    }
};
