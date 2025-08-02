<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50); // safe2pay, mercadopago, etc
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->json('supported_methods'); // [pix, credit_card, bank_slip, etc]
            $table->json('supported_countries')->nullable(); // [BR, US, etc]
            $table->json('settings')->nullable(); // configurações específicas do gateway
            $table->decimal('fee_percentage', 5, 4)->nullable(); // taxa percentual
            $table->decimal('fee_fixed', 10, 2)->nullable(); // taxa fixa
            $table->string('webhook_url', 500)->nullable();
            $table->string('webhook_secret', 200)->nullable();
            $table->integer('priority')->default(0); // ordem de prioridade
            $table->timestamps();

            $table->index(['provider', 'active']);
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
