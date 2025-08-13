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
            // Alterar o tipo da coluna conta_pai_id para ser compatível com id
            $table->unsignedInteger('conta_pai_id')->change();

            // Agora adicionar a foreign key
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

            // Reverter para bigint unsigned
            $table->unsignedBigInteger('conta_pai_id')->change();
        });
    }
};
