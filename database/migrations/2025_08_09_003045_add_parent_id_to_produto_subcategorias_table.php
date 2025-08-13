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
        Schema::table('produto_subcategorias', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('categoria_id');
            // Foreign key será adicionada depois se necessário
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produto_subcategorias', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
};
