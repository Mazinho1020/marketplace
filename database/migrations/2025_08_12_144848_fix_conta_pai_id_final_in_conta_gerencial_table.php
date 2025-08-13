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
        // Vamos usar SQL direto para corrigir isso
        DB::statement('ALTER TABLE conta_gerencial MODIFY COLUMN conta_pai_id INT NULL');
        DB::statement('ALTER TABLE conta_gerencial ADD CONSTRAINT conta_gerencial_conta_pai_id_foreign FOREIGN KEY (conta_pai_id) REFERENCES conta_gerencial(id) ON DELETE SET NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE conta_gerencial DROP FOREIGN KEY conta_gerencial_conta_pai_id_foreign');
    }
};
