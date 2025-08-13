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
        Schema::table('conta_gerencial', function (Blueprint $table) {
            // Primeiro, definir todos os valores para NULL onde há problema
            DB::table('conta_gerencial')->whereNotNull('conta_pai_id')->update(['conta_pai_id' => null]);

            // Alterar para integer nullable
            $table->unsignedInteger('conta_pai_id')->nullable()->change();

            // Adicionar a foreign key
            $table->foreign('conta_pai_id')->references('id')->on('conta_gerencial')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conta_gerencial', function (Blueprint $table) {
            // Remover foreign key
            $table->dropForeign(['conta_pai_id']);
        });
    }
};
