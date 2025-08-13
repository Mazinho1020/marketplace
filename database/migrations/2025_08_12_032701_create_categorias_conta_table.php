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
        Schema::create('categorias_conta', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50);
            $table->string('nome_completo', 100);
            $table->text('descricao')->nullable();
            $table->string('cor', 7)->default('#007bff');
            $table->string('icone', 50)->nullable();
            $table->boolean('e_custo')->default(false);
            $table->boolean('e_despesa')->default(false);
            $table->boolean('e_receita')->default(false);
            $table->boolean('ativo')->default(true);
            $table->unsignedBigInteger('empresa_id')->nullable();

            // Sync fields
            $table->timestamp('sync_data')->useCurrent();
            $table->string('sync_hash', 32)->nullable();
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
            $table->timestamps();

            $table->index(['empresa_id', 'ativo']);
            $table->index(['sync_status', 'sync_data']);
            $table->index(['e_custo', 'e_despesa', 'e_receita']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_conta');
    }
};
