<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSimplifiedLoginSystem extends Migration
{
    public function up()
    {
        // 1. Criar tabela de tipos de usuário administrativo
        Schema::create('empresa_usuario_tipos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->comment('Código único do tipo (admin, gerente, operador, etc)');
            $table->string('nome', 100)->comment('Nome de exibição');
            $table->text('descricao')->nullable()->comment('Descrição do tipo de usuário');
            $table->integer('nivel_acesso')->default(1)->comment('Nível hierárquico de acesso (1=mais baixo)');
            $table->timestamps();
            $table->softDeletes();
            $table->string('sync_status', 20)->default('pendente');
            $table->timestamp('sync_data')->useCurrent();

            $table->unique('codigo');
            $table->index(['sync_status', 'sync_data'], 'idx_sync');
        });

        // 2. Adicionar campo tipo_id à tabela empresa_usuarios existente (se não existir)
        if (Schema::hasTable('empresa_usuarios')) {
            Schema::table('empresa_usuarios', function (Blueprint $table) {
                if (!Schema::hasColumn('empresa_usuarios', 'tipo_id')) {
                    $table->unsignedBigInteger('tipo_id')->nullable()->after('perfil_id');
                    $table->foreign('tipo_id')->references('id')->on('empresa_usuario_tipos');
                }
            });
        }

        // 3. Criar tabela para redefinição de senha
        Schema::create('empresa_usuarios_password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('used')->default(false);

            $table->index('email');
            $table->index('expires_at');
        });

        // 4. Criar tabela para tentativas de login
        Schema::create('empresa_usuarios_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->boolean('success')->default(false);
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['email', 'created_at']);
        });

        // 5. Criar tabela para tokens "Lembrar de mim"
        Schema::create('empresa_usuarios_remember_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('token');
            $table->timestamp('expires_at');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('empresa_usuarios');
            $table->index(['user_id', 'expires_at']);
        });

        // 6. Criar tabela para log de atividades
        Schema::create('empresa_usuarios_activity_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('action', 50);
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('empresa_usuarios');
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });

        // 7. Popular com tipos básicos de usuário administrativo
        DB::table('empresa_usuario_tipos')->insert([
            [
                'codigo' => 'admin',
                'nome' => 'Administrador',
                'descricao' => 'Acesso completo ao sistema',
                'nivel_acesso' => 100,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'gerente',
                'nome' => 'Gerente',
                'descricao' => 'Acesso a funções gerenciais',
                'nivel_acesso' => 80,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'supervisor',
                'nome' => 'Supervisor',
                'descricao' => 'Acesso a funções de supervisão',
                'nivel_acesso' => 60,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'operador',
                'nome' => 'Operador',
                'descricao' => 'Acesso operacional básico',
                'nivel_acesso' => 40,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'consulta',
                'nome' => 'Consulta',
                'descricao' => 'Acesso somente leitura',
                'nivel_acesso' => 20,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 8. Atualizar usuários existentes para usar o tipo admin
        if (Schema::hasTable('empresa_usuarios')) {
            $adminTipoId = DB::table('empresa_usuario_tipos')->where('codigo', 'admin')->value('id');

            if ($adminTipoId) {
                DB::table('empresa_usuarios')
                    ->whereNull('tipo_id')
                    ->update(['tipo_id' => $adminTipoId]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('empresa_usuarios_activity_log');
        Schema::dropIfExists('empresa_usuarios_remember_tokens');
        Schema::dropIfExists('empresa_usuarios_login_attempts');
        Schema::dropIfExists('empresa_usuarios_password_resets');

        if (Schema::hasTable('empresa_usuarios')) {
            Schema::table('empresa_usuarios', function (Blueprint $table) {
                if (Schema::hasColumn('empresa_usuarios', 'tipo_id')) {
                    $table->dropForeign(['tipo_id']);
                    $table->dropColumn('tipo_id');
                }
            });
        }

        Schema::dropIfExists('empresa_usuario_tipos');
    }
}
