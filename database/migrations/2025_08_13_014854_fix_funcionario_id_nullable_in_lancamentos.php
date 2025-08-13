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
        Schema::table('lancamentos', function (Blueprint $table) {
            // Tornar funcionario_id nullable
            $table->bigInteger('funcionario_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lancamentos', function (Blueprint $table) {
            // Voltar funcionario_id para NOT NULL
            $table->bigInteger('funcionario_id')->nullable(false)->change();
        });
    }
};
