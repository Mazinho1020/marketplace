<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_transaction_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('gateway_provider', ['safe2pay', 'mercadopago', 'stripe', 'paypal'])->index();
            $table->string('event_type', 100); // tipo do evento recebido
            $table->string('gateway_transaction_id', 100)->nullable()->index();
            $table->json('payload'); // dados completos recebidos do webhook
            $table->string('signature', 500)->nullable(); // assinatura para validação
            $table->boolean('processed')->default(false)->index();
            $table->timestamp('processed_at')->nullable();
            $table->integer('processing_attempts')->default(0);
            $table->text('processing_error')->nullable();
            $table->json('processing_response')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();

            $table->index(['gateway_provider', 'event_type']);
            $table->index(['processed', 'processing_attempts']);
            $table->index(['gateway_provider', 'gateway_transaction_id']);
            $table->index(['received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhooks');
    }
};
