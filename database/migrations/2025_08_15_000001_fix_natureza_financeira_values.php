<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Corrige valores incorretos no campo natureza_financeira
     */
    public function up()
    {
        // Corrigir valores incorretos na natureza_financeira
        DB::table('lancamentos_financeiros')
            ->whereIn('natureza_financeira', ['despesa', 'custo', 'investimento'])
            ->update(['natureza_financeira' => 'pagar']);

        // Corrigir valores incorretos que deveriam ser 'receber'
        DB::table('lancamentos_financeiros')
            ->whereIn('natureza_financeira', ['receita', 'vendas', 'entrada'])
            ->update(['natureza_financeira' => 'receber']);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Não há rollback para esta correção
        // pois os valores antigos eram incorretos
    }
};
