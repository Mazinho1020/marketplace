<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fidelidade_conquistas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->string('icone', 50)->nullable();
            $table->integer('pontos_recompensa')->default(0);
            $table->decimal('credito_recompensa', 10, 2)->default(0.00);
            $table->string('tipo_requisito', 50)->nullable();
            $table->integer('valor_requisito')->nullable();
            $table->boolean('ativo')->default(true);
            $table->datetime('criado_em')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index(['ativo']);
            $table->index(['tipo_requisito']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fidelidade_conquistas');
    }
};
