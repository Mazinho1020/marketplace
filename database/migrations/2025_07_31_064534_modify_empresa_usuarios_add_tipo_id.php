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
        Schema::table('empresa_usuarios', function (Blueprint $table) {
            // Adicionar campo tipo_id (sem foreign key por enquanto)
            $table->bigInteger('tipo_id')
                ->nullable()
                ->after('perfil_id')
                ->comment('ID do tipo principal do usuário');

            // Adicionar índice
            $table->index('tipo_id', 'idx_tipo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresa_usuarios', function (Blueprint $table) {
            // $table->dropForeign(['tipo_id']); // Comentado pois não criamos foreign key
            $table->dropIndex('idx_tipo_id');
            $table->dropColumn('tipo_id');
        });
    }
};
