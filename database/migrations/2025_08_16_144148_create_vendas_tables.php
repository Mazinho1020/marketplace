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
        // Tabela de Vendas
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('lancamento_id')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable();
            
            $table->string('numero_venda', 20)->unique();
            $table->enum('tipo_venda', ['balcao', 'delivery', 'online', 'telefone'])->default('balcao');
            
            $table->decimal('valor_total', 10, 2)->default(0);
            $table->decimal('valor_desconto', 10, 2)->default(0);
            $table->decimal('valor_liquido', 10, 2)->default(0);
            
            $table->enum('status', ['pendente', 'confirmada', 'cancelada', 'entregue'])->default('pendente');
            $table->timestamp('data_venda');
            
            $table->text('observacoes')->nullable();
            $table->json('metadados')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['empresa_id', 'data_venda']);
            $table->index(['empresa_id', 'status']);
            $table->index(['empresa_id', 'numero_venda']);
            $table->index(['usuario_id']);
            $table->index(['cliente_id']);
            
            // Foreign keys (comentadas pois as tabelas podem não existir ainda)
            // $table->foreign('empresa_id')->references('id')->on('empresas');
            // $table->foreign('usuario_id')->references('id')->on('users');
            // $table->foreign('lancamento_id')->references('id')->on('lancamentos');
            // $table->foreign('cliente_id')->references('id')->on('pessoas');
        });

        // Tabela de Itens de Venda
        Schema::create('venda_itens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venda_id');
            $table->unsignedBigInteger('produto_id');
            $table->unsignedBigInteger('produto_variacao_id')->nullable();
            
            $table->decimal('quantidade', 10, 4);
            $table->decimal('valor_unitario', 10, 4);
            $table->decimal('valor_total', 10, 2);
            $table->decimal('desconto_unitario', 10, 4)->default(0);
            $table->decimal('desconto_total', 10, 2)->default(0);
            
            $table->text('observacoes')->nullable();
            $table->json('metadados')->nullable();
            $table->unsignedBigInteger('empresa_id');
            
            $table->timestamps();
            
            // Índices
            $table->index(['venda_id']);
            $table->index(['produto_id']);
            $table->index(['empresa_id']);
            
            // Foreign keys (comentadas pois as tabelas podem não existir ainda)
            // $table->foreign('venda_id')->references('id')->on('vendas')->onDelete('cascade');
            // $table->foreign('produto_id')->references('id')->on('produtos');
            // $table->foreign('produto_variacao_id')->references('id')->on('produto_variacao_combinacoes');
            // $table->foreign('empresa_id')->references('id')->on('empresas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venda_itens');
        Schema::dropIfExists('vendas');
    }
};
