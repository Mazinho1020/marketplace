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
        Schema::create('recebimentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lancamento_id');
            $table->unsignedBigInteger('forma_pagamento_id');
            $table->unsignedBigInteger('bandeira_id')->nullable();
            $table->unsignedBigInteger('conta_bancaria_id');
            $table->unsignedBigInteger('tipo_id');
            $table->decimal('valor', 15, 2);
            $table->decimal('valor_principal', 15, 2)->default(0);
            $table->decimal('valor_juros', 15, 2)->default(0);
            $table->decimal('valor_multa', 15, 2)->default(0);
            $table->decimal('valor_desconto', 15, 2)->default(0);
            $table->date('data_recebimento');
            $table->date('data_compensacao')->nullable();
            $table->text('observacao')->nullable();
            $table->text('comprovante_recebimento')->nullable();
            $table->decimal('taxa', 5, 2)->default(0);
            $table->decimal('valor_taxa', 15, 2)->default(0);
            $table->string('referencia_externa', 100)->nullable();
            $table->unsignedBigInteger('usuario_id');
            $table->enum('status_recebimento', ['processando', 'confirmado', 'estornado'])->default('confirmado');
            $table->text('motivo_estorno')->nullable();
            $table->timestamp('data_estorno')->nullable();
            $table->unsignedBigInteger('usuario_estorno_id')->nullable();
            $table->timestamps();

            // Índices
            $table->index('lancamento_id');
            $table->index('forma_pagamento_id');
            $table->index('data_recebimento');
            $table->index('status_recebimento');
            $table->index(['lancamento_id', 'status_recebimento']);

            // Foreign keys (comentadas por enquanto para evitar problemas)
            // $table->foreign('lancamento_id')->references('id')->on('lancamentos_financeiros')->onDelete('cascade');
            // $table->foreign('forma_pagamento_id')->references('id')->on('formas_pagamento')->onDelete('restrict');
            // $table->foreign('bandeira_id')->references('id')->on('forma_pag_bandeiras')->onDelete('set null');
            // $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recebimentos');
    }
};
