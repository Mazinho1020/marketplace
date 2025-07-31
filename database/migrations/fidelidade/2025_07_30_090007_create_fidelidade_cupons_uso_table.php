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
        Schema::create('fidelidade_cupons_uso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cupom_id')->constrained('fidelidade_cupons')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
            $table->integer('pedido_id')->nullable();
            $table->decimal('valor_desconto_aplicado', 10, 2)->nullable();
            $table->datetime('data_uso')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cupom_id']);
            $table->index(['cliente_id']);
            $table->index(['pedido_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fidelidade_cupons_uso');
    }
};
