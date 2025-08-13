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
        Schema::create('classificacoes_dre', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->nullable();
            $table->integer('nivel')->default(1);
            $table->foreignId('classificacao_pai_id')->nullable()->constrained('classificacoes_dre');
            $table->string('nome', 255);
            $table->text('descricao')->nullable();
            $table->foreignId('tipo_id')->nullable()->constrained('tipo');
            $table->boolean('ativo')->default(true);
            $table->integer('ordem_exibicao')->default(0);
            $table->foreignId('empresa_id')->constrained('empresas');

            // Sync fields
            $table->string('sync_hash', 64)->nullable();
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
            $table->timestamp('sync_data')->useCurrent();
            $table->timestamps();

            // Índices
            $table->index(['empresa_id', 'ativo']);
            $table->index(['nivel', 'classificacao_pai_id']);
            $table->index(['sync_status', 'sync_data']);
            $table->index(['codigo', 'empresa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classificacoes_dre');
    }
};
