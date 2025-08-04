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
        Schema::create('notificacao_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id')->comment('ID da empresa (multitenancy)');
            $table->unsignedBigInteger('tipo_evento_id')->comment('ID do tipo de evento');
            $table->unsignedBigInteger('aplicacao_id')->comment('ID da aplicação alvo');
            $table->string('nome', 100)->comment('Nome do template');
            $table->string('categoria', 50)->default('geral')->comment('Categoria do template');

            // Conteúdo da notificação
            $table->string('titulo', 255)->comment('Título da notificação');
            $table->text('mensagem')->comment('Corpo da mensagem');
            $table->string('subtitulo', 255)->nullable()->comment('Subtítulo opcional');
            $table->string('texto_acao', 100)->nullable()->comment('Texto do botão/ação (ex: Ver Pedido)');
            $table->string('url_acao', 500)->nullable()->comment('URL da ação (pode ter variáveis {{pedido_id}})');

            // Configurações específicas
            $table->json('canais')->comment('Canais permitidos ["websocket", "push", "email", "sms", "in_app"]');
            $table->enum('prioridade', ['baixa', 'media', 'alta', 'urgente'])->default('media')->comment('Prioridade da notificação');
            $table->integer('expira_em_minutos')->nullable()->comment('Minutos para expirar (NULL = nunca expira)');

            // Variáveis e condições
            $table->json('variaveis')->nullable()->comment('Variáveis específicas deste template');
            $table->json('condicoes')->nullable()->comment('Condições para usar este template');
            $table->json('segmentos_usuario')->nullable()->comment('Segmentos de usuário aplicáveis');

            // Personalização visual
            $table->string('icone_classe', 100)->nullable()->comment('Classe do ícone (fas fa-shopping-cart)');
            $table->string('cor_hex', 7)->nullable()->comment('Cor tema (#28a745)');
            $table->string('arquivo_som', 100)->nullable()->comment('Arquivo de som personalizado');
            $table->string('url_imagem', 500)->nullable()->comment('URL da imagem da notificação');

            // Controle e versionamento
            $table->boolean('ativo')->default(true)->comment('Se o template está ativo');
            $table->boolean('padrao')->default(false)->comment('Se é o template padrão para o evento+app');
            $table->integer('versao')->default(1)->comment('Versão do template (para A/B testing)');
            $table->decimal('percentual_ab_test', 5, 2)->nullable()->comment('Percentual para A/B test (0.00-100.00)');

            // Estatísticas
            $table->integer('total_uso')->default(0)->comment('Quantas vezes foi usado');
            $table->decimal('taxa_conversao', 5, 2)->nullable()->comment('Taxa de conversão (%)');
            $table->timestamp('ultimo_uso_em')->nullable()->comment('Última vez que foi usado');

            // Sincronização Multi-Sites (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable()->comment('Hash MD5 para controle de sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            // Timestamps Laravel
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['empresa_id', 'tipo_evento_id', 'aplicacao_id'], 'idx_empresa_evento_app');
            $table->index(['empresa_id', 'ativo'], 'idx_empresa_ativo');
            $table->index(['empresa_id', 'padrao'], 'idx_empresa_padrao');
            $table->index(['empresa_id', 'percentual_ab_test'], 'idx_ab_test');
            $table->index(['total_uso', 'taxa_conversao'], 'idx_estatisticas_uso');
            $table->index('ultimo_uso_em', 'idx_ultimo_uso');
            $table->index('sync_status', 'idx_sync_status');
            $table->index('sync_data', 'idx_sync_data');

            // Chave única para template padrão
            $table->unique(['empresa_id', 'tipo_evento_id', 'aplicacao_id', 'padrao', 'deleted_at'], 'unique_template_padrao');

            // Chaves estrangeiras
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('tipo_evento_id')->references('id')->on('notificacao_tipos_evento')->onDelete('cascade');
            $table->foreign('aplicacao_id')->references('id')->on('notificacao_aplicacoes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacao_templates');
    }
};
