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
        Schema::create('cartoes_fidelidade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programa_fidelidade_id')->constrained('programas_fidelidade')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('codigo')->unique();
            $table->integer('saldo_pontos')->default(0);
            $table->integer('pontos_acumulados')->default(0);
            $table->integer('pontos_resgatados')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamp('data_ativacao')->nullable();
            $table->timestamp('data_ultima_transacao')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['programa_fidelidade_id', 'user_id']);
            $table->index(['codigo']);
            $table->index(['ativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartoes_fidelidade');
    }
};
