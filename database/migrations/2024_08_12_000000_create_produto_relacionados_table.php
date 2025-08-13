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
        Schema::create('produto_relacionados', function (Blueprint $table) {
            $table->id();
            $table->integer('empresa_id')->default(0);
            $table->unsignedInteger('produto_id');
            $table->unsignedInteger('produto_relacionado_id');
            $table->enum('tipo_relacao', [
                'similar',
                'complementar',
                'acessorio',
                'substituto',
                'kit',
                'cross-sell',
                'up-sell'
            ])->default('similar');
            $table->integer('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
            $table->timestamp('sync_data')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->string('sync_hash', 32)->nullable();

            // Índices
            $table->unique(['produto_id', 'produto_relacionado_id', 'tipo_relacao'], 'uk_produto_relacionado');
            $table->index('empresa_id', 'idx_empresa');
            $table->index('produto_id', 'idx_produto');
            $table->index('produto_relacionado_id', 'idx_relacionado');
            $table->index('tipo_relacao', 'idx_tipo');
            $table->index(['sync_status', 'sync_data'], 'idx_sync');

            // Foreign keys
            $table->foreign('produto_id', 'fk_relacionados_produto')
                ->references('id')->on('produtos')->onDelete('cascade');
            $table->foreign('produto_relacionado_id', 'fk_relacionados_produto_rel')
                ->references('id')->on('produtos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_relacionados');
    }
};
