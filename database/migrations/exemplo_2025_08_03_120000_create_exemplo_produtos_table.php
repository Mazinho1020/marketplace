<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration de exemplo seguindo PADRAO_BANCO_DADOS.md
 * 
 * Esta migration demonstra a implementação completa dos padrões
 * definidos para o marketplace
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exemplo_produtos', function (Blueprint $table) {
            // 1. CHAVE PRIMÁRIA (OBRIGATÓRIO)
            $table->id();

            // 2. MULTITENANCY (OBRIGATÓRIO)
            $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('ID da empresa (multitenancy)');

            // 3. CAMPOS ESPECÍFICOS DA TABELA
            $table->string('nome', 100)->comment('Nome do produto');
            $table->string('sku', 50)->nullable()->comment('Código SKU único');
            $table->text('descricao')->nullable()->comment('Descrição detalhada do produto');
            $table->decimal('preco', 8, 2)->comment('Preço de venda');
            $table->decimal('preco_promocional', 8, 2)->nullable()->comment('Preço promocional');
            $table->decimal('preco_custo', 8, 2)->nullable()->comment('Preço de custo');
            $table->integer('estoque')->default(0)->comment('Quantidade em estoque');
            $table->boolean('controla_estoque')->default(true)->comment('Se controla estoque');
            $table->enum('status', ['ativo', 'inativo', 'descontinuado'])->default('ativo');
            $table->boolean('is_active')->default(true)->comment('Status ativo/inativo');

            // Relacionamentos opcionais
            $table->foreignId('categoria_id')
                ->nullable()
                ->constrained('categorias')
                ->onDelete('set null')
                ->comment('Categoria do produto');

            $table->foreignId('marca_id')
                ->nullable()
                ->constrained('marcas')
                ->onDelete('set null')
                ->comment('Marca do produto');

            // Campos de SEO
            $table->string('slug', 150)->nullable()->comment('URL amigável');
            $table->string('meta_title', 150)->nullable()->comment('Título para SEO');
            $table->text('meta_description')->nullable()->comment('Descrição para SEO');

            // Dados adicionais
            $table->json('imagens')->nullable()->comment('URLs das imagens do produto');
            $table->json('atributos')->nullable()->comment('Atributos específicos do produto');
            $table->json('configuracoes')->nullable()->comment('Configurações específicas');

            // Controle de datas
            $table->date('data_lancamento')->nullable()->comment('Data de lançamento');
            $table->date('data_descontinuacao')->nullable()->comment('Data de descontinuação');

            // 4. SINCRONIZAÇÃO MULTI-SITES (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable()->comment('Hash MD5 para controle de sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])
                ->default('pending')
                ->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            // 5. TIMESTAMPS PADRÃO (OBRIGATÓRIO)
            $table->timestamps();
            $table->softDeletes();

            // 6. ÍNDICES OBRIGATÓRIOS
            $table->index(['empresa_id', 'is_active'], 'idx_empresa_active');
            $table->index('created_at', 'idx_created_at');
            $table->index('sync_status', 'idx_sync_status');
            $table->index('deleted_at', 'idx_deleted_at');
            $table->index(['empresa_id', 'sync_status', 'sync_data'], 'idx_sync_control');

            // 7. ÍNDICES ESPECÍFICOS
            $table->index(['empresa_id', 'status'], 'idx_empresa_status');
            $table->index(['categoria_id', 'status'], 'idx_categoria_status');
            $table->index('sku', 'idx_sku');
            $table->index('slug', 'idx_slug');
            $table->index(['preco', 'status'], 'idx_preco_status');

            // Índices únicos
            $table->unique(['empresa_id', 'sku'], 'unique_empresa_sku');
            $table->unique(['empresa_id', 'slug'], 'unique_empresa_slug');

            // Índice full-text para busca (MySQL 5.7+)
            $table->fullText(['nome', 'descricao'], 'ft_produtos_busca');
        });

        // 8. COMENTÁRIO DA TABELA
        DB::statement("ALTER TABLE exemplo_produtos COMMENT = 'Produtos do marketplace - Exemplo seguindo PADRAO_BANCO_DADOS.md'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exemplo_produtos');
    }
};
