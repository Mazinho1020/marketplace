<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Renomear tabela para ser mais clara
        Schema::rename('conta_gerencial_natureza', 'categorias_conta');
        
        // Adicionar campos úteis na categoria
        Schema::table('categorias_conta', function (Blueprint $table) {
            $table->string('cor', 7)->default('#007bff')->after('nome_completo');
            $table->string('icone', 50)->nullable()->after('cor');
            $table->boolean('e_custo')->default(false)->after('icone');
            $table->boolean('e_despesa')->default(false)->after('e_custo');
            $table->boolean('e_receita')->default(false)->after('e_despesa');
            $table->boolean('ativo')->default(true)->after('e_receita');
            $table->text('descricao')->nullable()->after('ativo');
        });

        // Adicionar relacionamento direto na conta_gerencial - 1:N ao invés de N:N
        Schema::table('conta_gerencial', function (Blueprint $table) {
            $table->unsignedBigInteger('categoria_id')->nullable()->after('tipo_id');
            $table->enum('natureza_conta', ['debito', 'credito'])->nullable()->after('categoria_id');
            
            // Adicionar foreign key para categorias_conta
            $table->foreign('categoria_id')->references('id')->on('categorias_conta')->onDelete('set null');
        });

        // Migrar dados existentes da tabela intermediária
        $this->migrarDados();

        // Atualizar tipos de categoria baseado nos nomes existentes
        $this->atualizarTiposCategorias();

        // Remover tabela intermediária desnecessária
        Schema::dropIfExists('conta_gerencial_naturezas');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Recriar tabela intermediária
        Schema::create('conta_gerencial_naturezas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conta_gerencial_id');
            $table->unsignedBigInteger('natureza_id');
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->timestamp('sync_data')->useCurrent();
            $table->string('sync_hash', 32)->nullable();
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
            $table->timestamps();
            
            $table->index(['conta_gerencial_id', 'natureza_id']);
            $table->index('natureza_id');
        });

        // Remover colunas adicionadas na conta_gerencial
        Schema::table('conta_gerencial', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn(['categoria_id', 'natureza_conta']);
        });

        // Remover colunas adicionadas nas categorias
        Schema::table('categorias_conta', function (Blueprint $table) {
            $table->dropColumn(['cor', 'icone', 'e_custo', 'e_despesa', 'e_receita', 'ativo', 'descricao']);
        });

        // Renomear tabela de volta
        Schema::rename('categorias_conta', 'conta_gerencial_natureza');
    }

    /**
     * Migrar dados da tabela intermediária para relacionamento direto
     */
    private function migrarDados()
    {
        // Buscar relacionamentos existentes na tabela intermediária
        $relacionamentos = DB::table('conta_gerencial_naturezas')->get();
        
        foreach ($relacionamentos as $rel) {
            // Atualizar conta_gerencial com a categoria_id
            DB::table('conta_gerencial')
              ->where('id', $rel->conta_gerencial_id)
              ->update(['categoria_id' => $rel->natureza_id]);
        }
    }

    /**
     * Atualizar tipos de categorias baseado nos nomes
     */
    private function atualizarTiposCategorias()
    {
        // Marcar categorias de custo
        DB::table('categorias_conta')
          ->where('nome', 'LIKE', '%custo%')
          ->update(['e_custo' => true]);

        // Marcar categorias de despesa
        DB::table('categorias_conta')
          ->where('nome', 'LIKE', '%despesa%')
          ->update(['e_despesa' => true]);

        // Marcar categorias de receita
        DB::table('categorias_conta')
          ->where('nome', 'LIKE', '%receita%')
          ->orWhere('nome_completo', 'LIKE', '%receita%')
          ->update(['e_receita' => true]);

        // Definir cores padrão por tipo
        DB::table('categorias_conta')
          ->where('e_custo', true)
          ->update(['cor' => '#dc3545']); // Vermelho para custos

        DB::table('categorias_conta')
          ->where('e_despesa', true)
          ->update(['cor' => '#fd7e14']); // Laranja para despesas

        DB::table('categorias_conta')
          ->where('e_receita', true)
          ->update(['cor' => '#28a745']); // Verde para receitas
    }
};