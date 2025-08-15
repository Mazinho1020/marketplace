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
        // Migrar dados da tabela recebimentos para pagamentos
        if (Schema::hasTable('recebimentos') && Schema::hasTable('pagamentos')) {
            DB::statement("
                INSERT INTO pagamentos (
                    lancamento_id,
                    numero_parcela_pagamento,
                    tipo_id,
                    forma_pagamento_id,
                    bandeira_id,
                    valor,
                    valor_principal,
                    valor_juros,
                    valor_multa,
                    valor_desconto,
                    data_pagamento,
                    data_compensacao,
                    observacao,
                    comprovante_pagamento,
                    status_pagamento,
                    referencia_externa,
                    created_at,
                    updated_at,
                    conta_bancaria_id,
                    taxa,
                    empresa_id,
                    caixa_id,
                    usuario_id,
                    valor_taxa,
                    sync_status,
                    sync_data
                )
                SELECT 
                    r.lancamento_id,
                    1 as numero_parcela_pagamento, -- Default para recebimentos
                    2 as tipo_id, -- 2 = recebimento
                    r.forma_pagamento_id,
                    r.bandeira_id,
                    r.valor,
                    r.valor_principal,
                    r.valor_juros,
                    r.valor_multa,
                    r.valor_desconto,
                    r.data_recebimento as data_pagamento,
                    r.data_compensacao,
                    r.observacao,
                    r.comprovante_recebimento as comprovante_pagamento,
                    r.status_recebimento as status_pagamento,
                    r.referencia_externa,
                    r.created_at,
                    r.updated_at,
                    r.conta_bancaria_id,
                    r.taxa,
                    1 as empresa_id, -- Default empresa
                    NULL as caixa_id,
                    r.usuario_id,
                    r.valor_taxa,
                    'pendente' as sync_status,
                    NOW() as sync_data
                FROM recebimentos r
                WHERE NOT EXISTS (
                    SELECT 1 FROM pagamentos p 
                    WHERE p.lancamento_id = r.lancamento_id 
                    AND p.valor = r.valor 
                    AND p.data_pagamento = r.data_recebimento
                    AND p.tipo_id = 2
                )
            ");
        }

        // Remover a tabela recebimentos
        Schema::dropIfExists('recebimentos');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recriar a tabela recebimentos
        Schema::create('recebimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lancamento_id')->constrained('lancamentos')->onDelete('cascade');
            $table->integer('numero_parcela_recebimento')->default(1);
            $table->integer('tipo_id')->default(2);
            $table->foreignId('forma_pagamento_id')->constrained('formas_pagamento');
            $table->unsignedBigInteger('bandeira_id')->nullable();
            $table->decimal('valor', 10, 2);
            $table->decimal('valor_principal', 15, 2)->default(0);
            $table->decimal('valor_juros', 15, 2)->default(0);
            $table->decimal('valor_multa', 15, 2)->default(0);
            $table->decimal('valor_desconto', 15, 2)->default(0);
            $table->date('data_recebimento');
            $table->date('data_compensacao')->nullable();
            $table->text('observacao')->nullable();
            $table->text('comprovante_recebimento')->nullable();
            $table->enum('status_recebimento', ['processando', 'confirmado', 'cancelado', 'estornado'])->default('confirmado');
            $table->string('referencia_externa', 100)->nullable();
            $table->timestamps();
            $table->integer('conta_bancaria_id');
            $table->decimal('taxa', 5, 2)->nullable();
            $table->integer('empresa_id');
            $table->integer('caixa_id')->nullable();
            $table->integer('usuario_id')->nullable();
            $table->decimal('valor_taxa', 10, 2)->nullable();
        });

        // Migrar dados de volta dos pagamentos para recebimentos
        if (Schema::hasTable('pagamentos')) {
            DB::statement("
                INSERT INTO recebimentos (
                    lancamento_id,
                    numero_parcela_recebimento,
                    tipo_id,
                    forma_pagamento_id,
                    bandeira_id,
                    valor,
                    valor_principal,
                    valor_juros,
                    valor_multa,
                    valor_desconto,
                    data_recebimento,
                    data_compensacao,
                    observacao,
                    comprovante_recebimento,
                    status_recebimento,
                    referencia_externa,
                    created_at,
                    updated_at,
                    conta_bancaria_id,
                    taxa,
                    empresa_id,
                    caixa_id,
                    usuario_id,
                    valor_taxa
                )
                SELECT 
                    p.lancamento_id,
                    p.numero_parcela_pagamento,
                    p.tipo_id,
                    p.forma_pagamento_id,
                    p.bandeira_id,
                    p.valor,
                    p.valor_principal,
                    p.valor_juros,
                    p.valor_multa,
                    p.valor_desconto,
                    p.data_pagamento,
                    p.data_compensacao,
                    p.observacao,
                    p.comprovante_pagamento,
                    p.status_pagamento,
                    p.referencia_externa,
                    p.created_at,
                    p.updated_at,
                    p.conta_bancaria_id,
                    p.taxa,
                    p.empresa_id,
                    p.caixa_id,
                    p.usuario_id,
                    p.valor_taxa
                FROM pagamentos p
                WHERE p.tipo_id = 2
            ");
        }
    }
};
