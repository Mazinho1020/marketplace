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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->comment('Nome da empresa');
            $table->string('razao_social', 150)->nullable()->comment('Razão social');
            $table->string('cnpj', 18)->unique()->nullable()->comment('CNPJ da empresa');
            $table->string('email', 100)->nullable()->comment('Email principal');
            $table->string('telefone', 20)->nullable()->comment('Telefone principal');
            $table->string('endereco', 255)->nullable()->comment('Endereço completo');
            $table->string('cidade', 100)->nullable()->comment('Cidade');
            $table->string('estado', 2)->nullable()->comment('Estado (UF)');
            $table->string('cep', 9)->nullable()->comment('CEP');
            $table->boolean('ativo')->default(true)->comment('Status da empresa');
            $table->json('configuracoes')->nullable()->comment('Configurações específicas da empresa');
            $table->string('sync_hash', 64)->nullable()->comment('Hash para sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['ativo']);
            $table->index(['sync_status']);
            $table->index(['cnpj']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
