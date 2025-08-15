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
    public function up(): void
    {
        // Alterar o ENUM para incluir 'parcialmente_pago'
        DB::statement("ALTER TABLE lancamentos MODIFY COLUMN situacao_financeira ENUM('pendente','pago','parcialmente_pago','vencido','cancelado','em_negociacao') NOT NULL DEFAULT 'pendente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Voltar ao ENUM original (remover 'parcialmente_pago')
        // Primeiro, atualizar registros que tenham 'parcialmente_pago' para 'pendente'
        DB::statement("UPDATE lancamentos SET situacao_financeira = 'pendente' WHERE situacao_financeira = 'parcialmente_pago'");

        // Depois alterar o ENUM
        DB::statement("ALTER TABLE lancamentos MODIFY COLUMN situacao_financeira ENUM('pendente','pago','vencido','cancelado','em_negociacao') NOT NULL DEFAULT 'pendente'");
    }
};
