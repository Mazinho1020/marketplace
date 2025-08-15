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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lancamento_id');
            $table->integer('numero_parcela_pagamento')->default(1);
            $table->unsignedBigInteger('tipo_id');
            $table->unsignedBigInteger('forma_pagamento_id');
            $table->unsignedBigInteger('bandeira_id')->nullable();
            $table->decimal('valor', 10, 2);
            $table->decimal('valor_principal', 15, 2)->default(0.00);
            $table->decimal('valor_juros', 15, 2)->default(0.00);
            $table->decimal('valor_multa', 15, 2)->default(0.00);
            $table->decimal('valor_desconto', 15, 2)->default(0.00);
            $table->date('data_pagamento');
            $table->date('data_compensacao')->nullable();
            $table->text('observacao')->nullable();
            $table->text('comprovante_pagamento')->nullable();
            $table->enum('status_pagamento', ['processando', 'confirmado', 'cancelado', 'estornado'])->default('confirmado');
            $table->string('referencia_externa', 100)->nullable();
            $table->unsignedBigInteger('conta_bancaria_id');
            $table->decimal('taxa', 5, 2)->nullable();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('caixa_id')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->decimal('valor_taxa', 10, 2)->nullable();
            $table->string('sync_hash', 32)->nullable()->comment('Hash MD5 dos dados');
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente')->comment('Status da sincronização');
            $table->enum('sync_status_copy', ['pendente', 'sincronizado', 'erro'])->default('pendente');
            $table->timestamp('sync_data')->useCurrent();
            $table->timestamps();

            // Índices para performance
            $table->index(['lancamento_id', 'data_pagamento'], 'idx_lancamento_data');
            $table->index(['empresa_id', 'data_pagamento'], 'idx_empresa_data_pagamento');
            $table->index(['forma_pagamento_id', 'data_pagamento'], 'idx_forma_data');
            $table->index('status_pagamento', 'idx_status_pagamento');
            $table->index('referencia_externa', 'idx_referencia_externa');

            // Foreign key
            $table->foreign('lancamento_id')->references('id')->on('lancamentos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
