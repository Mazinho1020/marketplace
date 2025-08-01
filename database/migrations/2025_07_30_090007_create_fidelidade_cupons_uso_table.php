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
        Schema::create('fidelidade_cupons_uso', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cupom_id');
            $table->unsignedBigInteger('cliente_id');
            $table->integer('pedido_id')->nullable();
            $table->decimal('valor_desconto_aplicado', 10, 2)->nullable();
            $table->datetime('data_uso')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index(['cupom_id']);
            $table->index(['cliente_id']);
            $table->index(['pedido_id']);
            $table->index(['data_uso']);
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
