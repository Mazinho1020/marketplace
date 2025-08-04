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
        Schema::create('empresa_usuarios', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->nullable()->unique();
            $table->string('username', 100)->nullable()->unique();
            $table->string('nome', 255);
            $table->string('email', 255)->unique();
            $table->string('senha', 255);
            $table->datetime('data_cadastro')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('empresa_id')->nullable();
            $table->integer('perfil_id')->nullable();
            $table->enum('status', ['ativo', 'inativo', 'pendente', 'bloqueado'])->default('pendente');
            $table->timestamp('last_login')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->string('two_factor_secret', 255)->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('avatar', 255)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('cargo', 100)->nullable();
            $table->boolean('require_password_change')->default(false);
            $table->timestamp('sync_data')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('sync_hash', 32)->nullable();
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_usuarios');
    }
};
