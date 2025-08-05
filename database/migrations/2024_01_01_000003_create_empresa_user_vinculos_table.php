<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela de vínculos entre usuários e empresas
     * Permite que um usuário trabalhe em várias empresas
     * Define o perfil/cargo do usuário em cada empresa
     */
    public function up(): void
    {
        Schema::create('empresa_user_vinculos', function (Blueprint $table) {
            $table->id();

            // Relacionamentos
            $table->unsignedBigInteger('empresa_id');        // FK para empresas.id
            $table->unsignedBigInteger('user_id');           // FK para empresa_usuarios.id

            // Perfil do usuário nesta empresa
            $table->enum('perfil', [
                'proprietario',    // Dono da empresa
                'administrador',   // Administrador geral
                'gerente',        // Gerente da unidade
                'colaborador'     // Funcionário comum
            ])->default('colaborador');

            // Status do vínculo
            $table->enum('status', ['ativo', 'inativo', 'suspenso'])->default('ativo');

            // Permissões específicas (JSON)
            $table->json('permissoes')->nullable();          // ["produtos.create", "vendas.view", etc]

            // Data do vínculo
            $table->timestamp('data_vinculo')->useCurrent();

            $table->timestamps();

            // Relacionamentos
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('empresa_usuarios')->onDelete('cascade');

            // Índices únicos (um usuário não pode ter dois vínculos na mesma empresa)
            $table->unique(['empresa_id', 'user_id'], 'empresa_user_vinculo_unique');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_user_vinculos');
    }
};
