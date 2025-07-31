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
        // Adicionar deleted_at às tabelas de fidelidade
        $tables = [
            'fidelidade_carteiras',
            'fidelidade_cashback_regras',
            'fidelidade_cashback_transacoes',
            'fidelidade_cliente_conquistas',
            'fidelidade_conquistas',
            'fidelidade_creditos',
            'fidelidade_cupons',
            'fidelidade_cupons_uso',
            'ficha_tecnica_categorias'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'deleted_at')) {
                        $table->softDeletes();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover deleted_at das tabelas de fidelidade
        $tables = [
            'fidelidade_carteiras',
            'fidelidade_cashback_regras',
            'fidelidade_cashback_transacoes',
            'fidelidade_cliente_conquistas',
            'fidelidade_conquistas',
            'fidelidade_creditos',
            'fidelidade_cupons',
            'fidelidade_cupons_uso',
            'ficha_tecnica_categorias'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'deleted_at')) {
                        $table->dropSoftDeletes();
                    }
                });
            }
        }
    }
};
