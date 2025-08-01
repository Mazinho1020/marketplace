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
        // Criar tabela de grupos simples primeiro
        if (!Schema::hasTable('config_groups')) {
            Schema::create('config_groups', function (Blueprint $table) {
                $table->id();
                $table->string('codigo', 50);
                $table->string('nome', 100);
                $table->text('descricao')->nullable();
                $table->string('icone_class', 50)->nullable();
                $table->integer('ordem')->default(0);
                $table->boolean('ativo')->default(true);
                $table->timestamps();

                $table->unique(['codigo']);
            });
        }

        // Criar tabela de definições simples
        if (!Schema::hasTable('config_definitions')) {
            Schema::create('config_definitions', function (Blueprint $table) {
                $table->id();
                $table->string('chave', 100);
                $table->string('nome', 100);
                $table->text('descricao')->nullable();
                $table->enum('tipo', ['string', 'integer', 'float', 'boolean', 'array', 'json', 'url', 'email', 'password'])->default('string');
                $table->unsignedBigInteger('grupo_id')->nullable();
                $table->text('valor_padrao')->nullable();
                $table->boolean('obrigatorio')->default(false);
                $table->boolean('editavel')->default(true);
                $table->integer('ordem')->default(0);
                $table->text('dica')->nullable();
                $table->boolean('ativo')->default(true);
                $table->timestamps();

                $table->unique(['chave']);
                $table->index(['grupo_id']);
            });
        }

        // Criar tabela de valores simples
        if (!Schema::hasTable('config_values')) {
            Schema::create('config_values', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('config_id');
                $table->text('valor')->nullable();
                $table->timestamps();

                $table->unique(['config_id']);
                $table->index(['config_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_values');
        Schema::dropIfExists('config_definitions');
        Schema::dropIfExists('config_groups');
    }
};
