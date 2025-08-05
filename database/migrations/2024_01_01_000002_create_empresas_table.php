<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela de empresas (unidades/lojas)
     * Cada empresa pertence a uma marca
     * Cada empresa tem um proprietário (pessoa física)
     */
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();

            // Dados básicos da empresa
            $table->string('nome', 200);                    // "Pizzaria Tradição Concórdia"
            $table->string('nome_fantasia', 200)->nullable(); // Nome fantasia se diferente
            $table->string('cnpj', 18)->unique()->nullable(); // CNPJ da unidade
            $table->string('slug', 200)->unique();          // "pizzaria-tradicao-concordia"

            // Relacionamentos
            $table->unsignedBigInteger('marca_id')->nullable();      // FK para marcas.id
            $table->unsignedBigInteger('proprietario_id');           // FK para empresa_usuarios.id

            // Endereço completo
            $table->string('endereco_cep', 9)->nullable();
            $table->string('endereco_logradouro', 300)->nullable();
            $table->string('endereco_numero', 20)->nullable();
            $table->string('endereco_complemento', 100)->nullable();
            $table->string('endereco_bairro', 100)->nullable();
            $table->string('endereco_cidade', 100)->nullable();
            $table->string('endereco_estado', 2)->nullable();

            // Contato da empresa
            $table->string('telefone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('website', 300)->nullable();

            // Status e configurações
            $table->enum('status', ['ativa', 'inativa', 'suspensa'])->default('ativa');
            $table->json('configuracoes')->nullable();       // Configurações específicas da empresa
            $table->json('horario_funcionamento')->nullable(); // {"segunda": "08:00-18:00", "terca": "08:00-18:00"}

            $table->timestamps();

            // Relacionamentos
            $table->foreign('marca_id')->references('id')->on('marcas')->onDelete('set null');
            $table->foreign('proprietario_id')->references('id')->on('empresa_usuarios')->onDelete('cascade');

            // Índices para performance
            $table->index(['marca_id', 'status']);
            $table->index(['proprietario_id', 'status']);
            $table->index('cnpj');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
