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
        Schema::create('empresa_usuario_tipo_rel', function (Blueprint $table) {
            // 1. CHAVE PRIMÁRIA
            $table->id();

            // 2. CHAVES ESTRANGEIRAS
            $table->unsignedBigInteger('usuario_id')->comment('ID do usuário');
            $table->unsignedBigInteger('tipo_id')->comment('ID do tipo de usuário');

            $table->foreign('usuario_id', 'fk_user_tipo_rel_usuario_id')
                ->references('id')
                ->on('empresa_usuarios')
                ->onDelete('cascade');

            $table->foreign('tipo_id', 'fk_user_tipo_rel_tipo_id')
                ->references('id')
                ->on('empresa_usuario_tipos')
                ->onDelete('cascade');

            // 3. CAMPOS ESPECÍFICOS
            $table->boolean('is_primary')->default(false)->comment('Indica se é o tipo principal');

            // 4. SINCRONIZAÇÃO MULTI-SITES (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable()->comment('Hash MD5 para controle de sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])
                ->default('pending')
                ->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            // 5. TIMESTAMPS PADRÃO (OBRIGATÓRIO)
            $table->timestamps();
            $table->softDeletes();

            // 6. ÍNDICES E CONSTRAINTS
            $table->unique(['usuario_id', 'tipo_id'], 'unique_usuario_tipo');
            $table->index('usuario_id', 'idx_usuario_id');
            $table->index('tipo_id', 'idx_tipo_id');
            $table->index('is_primary', 'idx_is_primary');
            $table->index('sync_status', 'idx_sync_status');
            $table->index('deleted_at', 'idx_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_usuario_tipo_rel');
    }
};
