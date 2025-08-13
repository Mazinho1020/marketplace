<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tipo', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50);
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('value', 25)->nullable();
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
            $table->timestamp('sync_data')->useCurrent();
            $table->string('sync_hash', 32)->nullable();
            $table->timestamps();
            
            $table->unique(['nome', 'empresa_id']);
            $table->index(['sync_status', 'sync_data']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipo');
    }
};