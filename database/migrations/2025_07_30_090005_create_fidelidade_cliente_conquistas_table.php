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
        Schema::create('fidelidade_cliente_conquistas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('conquista_id');
            $table->datetime('data_desbloqueio')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('recompensa_resgatada')->default(false);

            $table->unique(['cliente_id', 'conquista_id'], 'uk_cliente_conquista');
            $table->index(['cliente_id']);
            $table->index(['conquista_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fidelidade_cliente_conquistas');
    }
};
