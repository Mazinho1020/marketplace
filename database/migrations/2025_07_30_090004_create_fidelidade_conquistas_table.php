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
        Schema::create('fidelidade_conquistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('businesses')->onDelete('cascade');
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->string('icone', 50)->nullable();
            $table->integer('xp_recompensa')->default(0);
            $table->decimal('credito_recompensa', 10, 2)->default(0.00);
            $table->string('tipo_requisito', 50)->nullable();
            $table->integer('valor_requisito')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();
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
