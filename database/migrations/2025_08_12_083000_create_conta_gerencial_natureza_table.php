<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conta_gerencial_natureza', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50);
            $table->string('nome_completo', 100);
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');

            // Sync fields
            $table->timestamp('sync_data')->useCurrent();
            $table->string('sync_hash', 32)->nullable();
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
            $table->timestamps();

            $table->index(['empresa_id', 'ativo']);
            $table->index(['sync_status', 'sync_data']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('conta_gerencial_natureza');
    }
};
