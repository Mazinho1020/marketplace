<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela de marcas
     * Uma marca pode ter várias empresas (unidades)
     * Um usuário (pessoa física) pode ter várias marcas
     */
    public function up(): void
    {
        Schema::create('marcas', function (Blueprint $table) {
            $table->id();

            // Dados básicos da marca
            $table->string('nome', 200);                    // "Pizzaria Tradição"
            $table->string('slug', 200)->unique();          // "pizzaria-tradicao"
            $table->text('descricao')->nullable();          // Descrição da marca
            $table->string('logo_url')->nullable();         // URL do logo

            // Identidade visual (cores, fontes, etc)
            $table->json('identidade_visual')->nullable();  // {"cor_primaria": "#ff0000", "cor_secundaria": "#000000"}

            // Relacionamento: Qual pessoa física é dona desta marca
            $table->unsignedBigInteger('pessoa_fisica_id'); // FK para empresa_usuarios.id

            // Status e configurações
            $table->enum('status', ['ativa', 'inativa', 'suspensa'])->default('ativa');
            $table->json('configuracoes')->nullable();      // Configurações específicas da marca

            $table->timestamps();

            // Relacionamentos
            $table->foreign('pessoa_fisica_id')->references('id')->on('empresa_usuarios')->onDelete('cascade');

            // Índices para performance
            $table->index(['pessoa_fisica_id', 'status']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marcas');
    }
};
