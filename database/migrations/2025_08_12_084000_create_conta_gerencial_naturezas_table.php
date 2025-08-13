<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conta_gerencial_naturezas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_gerencial_id')->constrained('conta_gerencial')->onDelete('cascade');
            $table->foreignId('natureza_id')->constrained('conta_gerencial_natureza')->onDelete('cascade');
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');

            // Sync fields
            $table->timestamp('sync_data')->useCurrent();
            $table->string('sync_hash', 32)->nullable();
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
            $table->timestamps();

            $table->unique(['conta_gerencial_id', 'natureza_id'], 'uk_conta_natureza');
            $table->index(['empresa_id']);
            $table->index(['sync_status', 'sync_data']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('conta_gerencial_naturezas');
    }
};
