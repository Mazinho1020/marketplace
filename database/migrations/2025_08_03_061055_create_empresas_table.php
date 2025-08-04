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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();

            // Informações básicas
            $table->string('nome_fantasia');
            $table->string('razao_social');
            $table->string('cnpj', 14)->unique();
            $table->string('email')->unique();
            $table->string('telefone', 20)->nullable();

            // Endereço
            $table->string('endereco')->nullable();
            $table->string('cep', 8)->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();

            // Informações comerciais
            $table->enum('plano', ['basico', 'pro', 'premium', 'enterprise'])->default('basico');
            $table->enum('status', ['ativo', 'inativo', 'suspenso', 'bloqueado'])->default('ativo');
            $table->decimal('valor_mensalidade', 10, 2)->nullable();
            $table->date('data_vencimento')->nullable();

            // Observações
            $table->text('observacoes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
