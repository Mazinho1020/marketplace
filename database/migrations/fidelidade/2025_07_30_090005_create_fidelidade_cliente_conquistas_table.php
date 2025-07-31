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
        Schema::create('fidelidade_cliente_conquistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('conquista_id')->constrained('fidelidade_conquistas')->onDelete('cascade');
            $table->datetime('data_desbloqueio')->useCurrent();
            $table->boolean('recompensa_resgatada')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['cliente_id', 'conquista_id'], 'uk_cliente_conquista');
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
