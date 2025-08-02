<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('transaction_code', 50)->unique();

            // Empresa e origem
            $table->unsignedBigInteger('empresa_id')->index();
            $table->enum('source_type', ['pdv', 'delivery', 'site_client', 'site_merchant', 'subscription', 'plan'])->index();
            $table->unsignedBigInteger('source_id')->index();
            $table->string('source_reference', 100)->nullable();

            // Status e gateway
            $table->enum('status', ['pending', 'processing', 'approved', 'declined', 'cancelled', 'refunded', 'expired'])->default('pending')->index();
            $table->enum('gateway_provider', ['safe2pay', 'mercadopago', 'stripe', 'paypal'])->nullable()->index();
            $table->string('gateway_transaction_id', 100)->nullable()->index();

            // Valores
            $table->decimal('amount_original', 10, 2)->nullable(); // valor original
            $table->decimal('amount_discount', 10, 2)->nullable()->default(0); // desconto aplicado
            $table->decimal('amount_fees', 10, 2)->nullable()->default(0); // taxas
            $table->decimal('amount_final', 10, 2); // valor final a ser cobrado

            // Método de pagamento
            $table->enum('payment_method', ['pix', 'credit_card', 'debit_card', 'bank_slip', 'bank_transfer'])->index();
            $table->integer('installments')->default(1);

            // Dados do cliente
            $table->string('customer_name', 200);
            $table->string('customer_email', 150)->nullable();
            $table->string('customer_document', 20)->nullable();
            $table->string('customer_phone', 20)->nullable();

            // Descrição e metadados
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // dados extras específicos da origem
            $table->json('payment_data')->nullable(); // dados específicos do método de pagamento
            $table->json('gateway_request')->nullable(); // request enviado ao gateway
            $table->json('gateway_response')->nullable(); // response do gateway

            // URLs de retorno
            $table->string('success_url', 500)->nullable();
            $table->string('cancel_url', 500)->nullable();
            $table->string('notification_url', 500)->nullable();

            // Dados de pagamento (PIX, boleto, etc)
            $table->text('qr_code')->nullable(); // QR Code do PIX
            $table->string('bar_code', 200)->nullable(); // código de barras do boleto
            $table->string('digitable_line', 100)->nullable(); // linha digitável do boleto
            $table->string('payment_url', 500)->nullable(); // URL para pagamento

            // Timestamps específicos
            $table->timestamp('expires_at')->nullable(); // data de expiração
            $table->timestamp('processed_at')->nullable(); // quando foi processado pelo gateway
            $table->timestamp('approved_at')->nullable(); // quando foi aprovado
            $table->timestamp('cancelled_at')->nullable(); // quando foi cancelado

            // Auditoria
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();

            $table->timestamps();

            // Índices compostos para consultas otimizadas
            $table->index(['empresa_id', 'status'], 'pt_empresa_status_idx');
            $table->index(['empresa_id', 'source_type', 'source_id'], 'pt_empresa_source_idx');
            $table->index(['empresa_id', 'created_at'], 'pt_empresa_created_idx');
            $table->index(['status', 'created_at'], 'pt_status_created_idx');
            $table->index(['payment_method', 'status'], 'pt_method_status_idx');
            $table->index(['gateway_provider', 'gateway_transaction_id'], 'pt_gateway_transaction_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
