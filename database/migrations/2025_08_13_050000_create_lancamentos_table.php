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
        if (!Schema::hasTable('lancamentos')) {
            Schema::create('lancamentos', function (Blueprint $table) {
                $table->id();
                
                // Relacionamentos principais
                $table->unsignedBigInteger('empresa_id');
                $table->unsignedBigInteger('usuario_id')->nullable();
                $table->unsignedBigInteger('cliente_id')->nullable();
                $table->unsignedBigInteger('funcionario_id')->nullable();
                $table->unsignedBigInteger('conta_gerencial_id')->nullable();
                
                // Campos básicos
                $table->string('descricao');
                $table->text('observacoes')->nullable();
                $table->decimal('valor', 15, 2);
                $table->date('data_vencimento');
                $table->date('data');
                
                // Status e situação
                $table->string('status', 50)->default('ativo');
                $table->unsignedBigInteger('tipo_id')->nullable();
                $table->string('parcela_referencia', 100)->nullable();
                
                // Campos de sincronização
                $table->timestamp('sync_data')->useCurrent();
                $table->string('sync_hash', 32)->nullable();
                $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
                
                $table->timestamps();
                
                // Índices básicos
                $table->index(['empresa_id']);
                $table->index(['usuario_id']);
                $table->index(['cliente_id']);
                $table->index(['funcionario_id']);
                $table->index(['conta_gerencial_id']);
                $table->index(['data_vencimento']);
                $table->index(['status']);
                $table->index(['sync_status', 'sync_data']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lancamentos');
    }
};