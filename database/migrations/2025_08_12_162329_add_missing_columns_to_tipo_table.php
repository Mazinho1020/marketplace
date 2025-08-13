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
        Schema::table('tipo', function (Blueprint $table) {
            // Adicionar colunas básicas de controle
            $table->boolean('ativo')->default(true)->after('nome');
            $table->text('descricao')->nullable()->after('ativo');
            $table->integer('ordem_exibicao')->default(0)->after('descricao');

            // Campos de configuração
            $table->string('cor', 7)->nullable()->after('ordem_exibicao');
            $table->string('icone', 50)->nullable()->after('cor');

            // Índices para performance
            $table->index(['empresa_id', 'ativo']);
            $table->index(['ordem_exibicao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipo', function (Blueprint $table) {
            // Removendo índices
            $table->dropIndex(['empresa_id', 'ativo']);
            $table->dropIndex(['ordem_exibicao']);

            // Removendo colunas
            $table->dropColumn([
                'ativo',
                'descricao',
                'ordem_exibicao',
                'cor',
                'icone'
            ]);
        });
    }
};
