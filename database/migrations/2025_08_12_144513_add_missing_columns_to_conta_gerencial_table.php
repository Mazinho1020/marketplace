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
        Schema::table('conta_gerencial', function (Blueprint $table) {
            // Adicionando colunas básicas de controle
            $table->string('codigo', 20)->nullable()->after('nome');
            $table->boolean('ativo')->default(true)->after('descricao');
            $table->integer('nivel')->default(0)->after('ativo');
            $table->integer('ordem_exibicao')->default(0)->after('nivel');

            // Relacionamentos hierárquicos
            $table->unsignedBigInteger('conta_pai_id')->nullable()->after('categoria_id');
            $table->foreign('conta_pai_id')->references('id')->on('conta_gerencial')->onDelete('set null');

            // Campos de natureza e classificação
            $table->enum('natureza', ['D', 'C'])->default('D')->after('conta_pai_id');
            $table->boolean('aceita_lancamento')->default(true)->after('natureza');
            $table->boolean('e_sintetica')->default(false)->after('aceita_lancamento');

            // Campos de configuração
            $table->string('cor', 7)->nullable()->after('e_sintetica');
            $table->string('icone', 50)->nullable()->after('cor');

            // Campos de controle financeiro
            $table->boolean('e_custo')->default(false)->after('icone');
            $table->boolean('e_despesa')->default(false)->after('e_custo');
            $table->boolean('e_receita')->default(false)->after('e_despesa');

            // Campo para DRE
            $table->string('grupo_dre', 100)->nullable()->after('e_receita');

            // Índices para performance
            $table->index(['empresa_id', 'ativo']);
            $table->index(['empresa_id', 'conta_pai_id']);
            $table->index(['codigo']);
            $table->index(['natureza']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conta_gerencial', function (Blueprint $table) {
            // Removendo foreign keys primeiro
            $table->dropForeign(['conta_pai_id']);

            // Removendo índices
            $table->dropIndex(['empresa_id', 'ativo']);
            $table->dropIndex(['empresa_id', 'conta_pai_id']);
            $table->dropIndex(['codigo']);
            $table->dropIndex(['natureza']);

            // Removendo colunas
            $table->dropColumn([
                'codigo',
                'ativo',
                'nivel',
                'ordem_exibicao',
                'conta_pai_id',
                'natureza',
                'aceita_lancamento',
                'e_sintetica',
                'cor',
                'icone',
                'e_custo',
                'e_despesa',
                'e_receita',
                'grupo_dre'
            ]);
        });
    }
};
