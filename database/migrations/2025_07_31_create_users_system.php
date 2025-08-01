<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela de tipos de usuário
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('ativo')->default(true);
            $table->integer('nivel_acesso')->default(1); // 1=cliente, 2=comerciante, 3=admin, 4=super
            $table->timestamps();
        });

        // Tabela principal de usuários
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('user_type_id');
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->string('status', 20)->default('ativo'); // ativo, inativo, bloqueado, pendente
            $table->string('telefone', 20)->nullable();
            $table->string('documento', 20)->nullable();
            $table->text('avatar')->nullable();
            $table->json('metadata')->nullable(); // dados extras específicos por tipo
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->string('remember_token')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_type_id')->references('id')->on('user_types');
            $table->index(['empresa_id']);
            $table->index(['status']);
            $table->index(['user_type_id']);
        });

        // Tabela de sessões de usuário
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('data');
            $table->integer('last_activity');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
            $table->index('last_activity');
        });

        // Tabela de logs de atividade
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('action', 100);
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->timestamp('created_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });

        // Tabela de tokens de autenticação (API, reset password, etc)
        Schema::create('user_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type', 50); // api, password_reset, email_verification
            $table->string('token')->unique();
            $table->json('abilities')->nullable(); // para tokens de API
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('revoked')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'type']);
            $table->index('token');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tokens');
        Schema::dropIfExists('user_activity_logs');
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_types');
    }
};
