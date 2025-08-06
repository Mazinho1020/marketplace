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
        // Criar tabela pivot para relacionamento usuários-empresas
        Schema::create('empresa_user_vinculos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('perfil', ['proprietario', 'administrador', 'gerente', 'colaborador'])->default('colaborador');
            $table->enum('status', ['ativo', 'inativo', 'suspenso'])->default('ativo');
            $table->json('permissoes')->nullable();
            $table->timestamp('data_vinculo')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            $table->unique(['empresa_id', 'user_id']);
            $table->index('empresa_id');
            $table->index('user_id');
        });

        // Inserir dados de teste
        DB::table('empresa_user_vinculos')->insert([
            [
                'empresa_id' => 1,
                'user_id' => 1,
                'perfil' => 'colaborador',
                'status' => 'ativo',
                'permissoes' => json_encode(['produtos.view', 'vendas.view']),
                'data_vinculo' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'user_id' => 2,
                'perfil' => 'administrador',
                'status' => 'ativo',
                'permissoes' => json_encode(['produtos.view', 'produtos.create', 'vendas.view', 'relatorios.view', 'usuarios.manage']),
                'data_vinculo' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'user_id' => 3,
                'perfil' => 'proprietario',
                'status' => 'ativo',
                'permissoes' => json_encode(['*']),
                'data_vinculo' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'user_id' => 5,
                'perfil' => 'gerente',
                'status' => 'ativo',
                'permissoes' => json_encode(['produtos.view', 'produtos.create', 'vendas.view', 'relatorios.view']),
                'data_vinculo' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'user_id' => 6,
                'perfil' => 'colaborador',
                'status' => 'ativo',
                'permissoes' => json_encode(['produtos.view']),
                'data_vinculo' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_user_vinculos');
    }
};
