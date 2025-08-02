<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_transaction_id')->constrained()->onDelete('cascade');
            $table->string('event_type', 50); // status_changed, gateway_request, gateway_response, etc
            $table->text('description');
            $table->string('previous_status', 20)->nullable();
            $table->string('new_status', 20)->nullable();
            $table->enum('triggered_by', ['user', 'system', 'webhook', 'cron'])->default('system');
            $table->unsignedBigInteger('user_id')->nullable(); // se foi triggado por usuário
            $table->json('metadata')->nullable(); // dados extras do evento
            $table->timestamps();

            $table->index(['payment_transaction_id', 'event_type']);
            $table->index(['payment_transaction_id', 'created_at']);
            $table->index(['event_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_events');
    }
};
