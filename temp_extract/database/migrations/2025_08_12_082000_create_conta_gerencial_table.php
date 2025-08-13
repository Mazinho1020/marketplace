<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conta_gerencial', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->nullable();
            $table->foreignId('conta_pai_id')->nullable()->constrained('conta_gerencial');
            $table->integer('nivel')->default(1);
            $table->string('nome', 255);
            $table->string('descricao', 255)->nullable();
            $table->boolean('ativo')->default(true);
            $table->integer('ordem_exibicao')->default(0);
            $table->boolean('permite_lancamento')->default(true);
            $table->enum('natureza_conta', ['debito', 'credito'])->nullable();
            $table->json('configuracoes')->nullable();
            
            // Relacionamentos
            $table->foreignId('usuario_id')->nullable()->constrained('users');
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->foreignId('classificacao_dre_id')->nullable()->constrained('classificacoes_dre');
            $table->foreignId('tipo_id')->nullable()->constrained('tipo');
            
            // Sync fields
            $table->timestamp('sync_data')->useCurrent();
            $table->string('sync_hash', 32)->nullable();
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
            $table->timestamps();
            
            // Índices
            $table->index(['empresa_id', 'ativo']);
            $table->index(['codigo', 'empresa_id']);
            $table->index(['conta_pai_id']);
            $table->index(['classificacao_dre_id']);
            $table->index(['sync_status', 'sync_data']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('conta_gerencial');
    }
};