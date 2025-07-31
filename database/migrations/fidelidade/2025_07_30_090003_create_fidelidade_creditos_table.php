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
        Schema::create('fidelidade_creditos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', ['comprado', 'cortesia', 'devolucao', 'premio', 'indicacao']);
            $table->decimal('valor_original', 10, 2);
            $table->decimal('valor_atual', 10, 2);
            $table->string('codigo_ativacao', 50)->nullable();
            $table->date('data_expiracao')->nullable();
            $table->integer('pedido_origem_id')->nullable();
            $table->text('observacoes')->nullable();
            $table->enum('status', ['ativo', 'usado', 'expirado', 'cancelado'])->default('ativo');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cliente_id']);
            $table->index(['codigo_ativacao']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fidelidade_creditos');
    }
};
