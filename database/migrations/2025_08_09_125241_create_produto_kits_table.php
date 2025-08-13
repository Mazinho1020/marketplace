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
        Schema::create('produto_kits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('produto_principal_id'); // O produto que é o kit
            $table->unsignedBigInteger('produto_item_id'); // Os produtos que fazem parte do kit
            $table->decimal('quantidade', 10, 3)->default(1); // Quantidade do item no kit
            $table->decimal('preco_item', 10, 2)->nullable(); // Preço específico no kit (opcional)
            $table->decimal('desconto_percentual', 5, 2)->nullable(); // Desconto em % (opcional)
            $table->boolean('obrigatorio')->default(true); // Se o item é obrigatório no kit
            $table->boolean('substituivel')->default(false); // Se pode ser substituído por outro
            $table->integer('ordem')->default(0); // Ordem de exibição
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            // Índices
            $table->index(['empresa_id', 'produto_principal_id']);
            $table->index(['empresa_id', 'produto_item_id']);
            $table->index(['produto_principal_id', 'ativo']);

            // Chaves estrangeiras
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('produto_principal_id')->references('id')->on('produtos')->onDelete('cascade');
            $table->foreign('produto_item_id')->references('id')->on('produtos')->onDelete('cascade');

            // Unique constraint para evitar duplicação
            $table->unique(['produto_principal_id', 'produto_item_id'], 'unique_kit_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_kits');
    }
};
