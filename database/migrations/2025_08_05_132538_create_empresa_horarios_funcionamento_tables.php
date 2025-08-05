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
        // 1. Criar tabela de dias da semana
        Schema::create('empresa_dias_semana', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 20);
            $table->string('nome_curto', 3);
            $table->integer('numero')->unique();
            $table->boolean('ativo')->default(true);
        });

        // Inserir dados dos dias da semana
        DB::table('empresa_dias_semana')->insert([
            ['nome' => 'Segunda-feira', 'nome_curto' => 'SEG', 'numero' => 1, 'ativo' => true],
            ['nome' => 'Terça-feira', 'nome_curto' => 'TER', 'numero' => 2, 'ativo' => true],
            ['nome' => 'Quarta-feira', 'nome_curto' => 'QUA', 'numero' => 3, 'ativo' => true],
            ['nome' => 'Quinta-feira', 'nome_curto' => 'QUI', 'numero' => 4, 'ativo' => true],
            ['nome' => 'Sexta-feira', 'nome_curto' => 'SEX', 'numero' => 5, 'ativo' => true],
            ['nome' => 'Sábado', 'nome_curto' => 'SAB', 'numero' => 6, 'ativo' => true],
            ['nome' => 'Domingo', 'nome_curto' => 'DOM', 'numero' => 7, 'ativo' => true],
        ]);

        // 2. Criar tabela principal de horários
        Schema::create('empresa_horarios_funcionamento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('dia_semana_id')->nullable()->comment('NULL para exceções');
            $table->enum('sistema', ['TODOS', 'PDV', 'FINANCEIRO', 'ONLINE'])->default('TODOS');
            $table->boolean('aberto')->default(true);
            $table->time('hora_abertura')->nullable();
            $table->time('hora_fechamento')->nullable();
            $table->boolean('is_excecao')->default(false);
            $table->date('data_excecao')->nullable()->comment('Usado apenas para exceções');
            $table->string('descricao_excecao')->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            // Índices
            $table->index(['empresa_id', 'sistema']);
            $table->index(['empresa_id', 'dia_semana_id']);
            $table->index(['empresa_id', 'is_excecao', 'data_excecao']);
            $table->index(['ativo']);

            // Foreign keys
            $table->foreign('dia_semana_id')->references('id')->on('empresa_dias_semana')->onDelete('set null');
        });

        // 3. Criar tabela de logs de auditoria
        Schema::create('empresa_horarios_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('horario_id')->nullable();
            $table->enum('acao', ['CREATE', 'UPDATE', 'DELETE', 'VIEW']);
            $table->json('dados_anteriores')->nullable();
            $table->json('dados_novos')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->string('usuario_nome', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Índices
            $table->index(['empresa_id', 'acao']);
            $table->index(['horario_id']);
            $table->index(['created_at']);
        });

        // 4. Inserir dados de exemplo para empresa ID 1
        $horarios_exemplo = [
            // Horários PDV Segunda a Sexta
            ['empresa_id' => 1, 'dia_semana_id' => 1, 'sistema' => 'PDV', 'aberto' => true, 'hora_abertura' => '08:00:00', 'hora_fechamento' => '18:00:00', 'observacoes' => 'Horário comercial padrão'],
            ['empresa_id' => 1, 'dia_semana_id' => 2, 'sistema' => 'PDV', 'aberto' => true, 'hora_abertura' => '08:00:00', 'hora_fechamento' => '18:00:00', 'observacoes' => 'Horário comercial padrão'],
            ['empresa_id' => 1, 'dia_semana_id' => 3, 'sistema' => 'PDV', 'aberto' => true, 'hora_abertura' => '08:00:00', 'hora_fechamento' => '18:00:00', 'observacoes' => 'Horário comercial padrão'],
            ['empresa_id' => 1, 'dia_semana_id' => 4, 'sistema' => 'PDV', 'aberto' => true, 'hora_abertura' => '08:00:00', 'hora_fechamento' => '18:00:00', 'observacoes' => 'Horário comercial padrão'],
            ['empresa_id' => 1, 'dia_semana_id' => 5, 'sistema' => 'PDV', 'aberto' => true, 'hora_abertura' => '08:00:00', 'hora_fechamento' => '18:00:00', 'observacoes' => 'Horário comercial padrão'],
            
            // Sábado meio período
            ['empresa_id' => 1, 'dia_semana_id' => 6, 'sistema' => 'PDV', 'aberto' => true, 'hora_abertura' => '08:00:00', 'hora_fechamento' => '12:00:00', 'observacoes' => 'Meio período aos sábados'],
            
            // Domingo fechado
            ['empresa_id' => 1, 'dia_semana_id' => 7, 'sistema' => 'PDV', 'aberto' => false, 'hora_abertura' => null, 'hora_fechamento' => null, 'observacoes' => 'Fechado aos domingos'],
            
            // Sistema Online 24h
            ['empresa_id' => 1, 'dia_semana_id' => 1, 'sistema' => 'ONLINE', 'aberto' => true, 'hora_abertura' => '00:00:00', 'hora_fechamento' => '23:59:59', 'observacoes' => 'Sistema online 24h'],
            ['empresa_id' => 1, 'dia_semana_id' => 2, 'sistema' => 'ONLINE', 'aberto' => true, 'hora_abertura' => '00:00:00', 'hora_fechamento' => '23:59:59', 'observacoes' => 'Sistema online 24h'],
            ['empresa_id' => 1, 'dia_semana_id' => 3, 'sistema' => 'ONLINE', 'aberto' => true, 'hora_abertura' => '00:00:00', 'hora_fechamento' => '23:59:59', 'observacoes' => 'Sistema online 24h'],
            ['empresa_id' => 1, 'dia_semana_id' => 4, 'sistema' => 'ONLINE', 'aberto' => true, 'hora_abertura' => '00:00:00', 'hora_fechamento' => '23:59:59', 'observacoes' => 'Sistema online 24h'],
            ['empresa_id' => 1, 'dia_semana_id' => 5, 'sistema' => 'ONLINE', 'aberto' => true, 'hora_abertura' => '00:00:00', 'hora_fechamento' => '23:59:59', 'observacoes' => 'Sistema online 24h'],
            ['empresa_id' => 1, 'dia_semana_id' => 6, 'sistema' => 'ONLINE', 'aberto' => true, 'hora_abertura' => '00:00:00', 'hora_fechamento' => '23:59:59', 'observacoes' => 'Sistema online 24h'],
            ['empresa_id' => 1, 'dia_semana_id' => 7, 'sistema' => 'ONLINE', 'aberto' => true, 'hora_abertura' => '00:00:00', 'hora_fechamento' => '23:59:59', 'observacoes' => 'Sistema online 24h'],
            
            // Sistema Financeiro
            ['empresa_id' => 1, 'dia_semana_id' => 1, 'sistema' => 'FINANCEIRO', 'aberto' => true, 'hora_abertura' => '09:00:00', 'hora_fechamento' => '17:00:00', 'observacoes' => 'Horário administrativo'],
            ['empresa_id' => 1, 'dia_semana_id' => 2, 'sistema' => 'FINANCEIRO', 'aberto' => true, 'hora_abertura' => '09:00:00', 'hora_fechamento' => '17:00:00', 'observacoes' => 'Horário administrativo'],
            ['empresa_id' => 1, 'dia_semana_id' => 3, 'sistema' => 'FINANCEIRO', 'aberto' => true, 'hora_abertura' => '09:00:00', 'hora_fechamento' => '17:00:00', 'observacoes' => 'Horário administrativo'],
            ['empresa_id' => 1, 'dia_semana_id' => 4, 'sistema' => 'FINANCEIRO', 'aberto' => true, 'hora_abertura' => '09:00:00', 'hora_fechamento' => '17:00:00', 'observacoes' => 'Horário administrativo'],
            ['empresa_id' => 1, 'dia_semana_id' => 5, 'sistema' => 'FINANCEIRO', 'aberto' => true, 'hora_abertura' => '09:00:00', 'hora_fechamento' => '17:00:00', 'observacoes' => 'Horário administrativo'],
            ['empresa_id' => 1, 'dia_semana_id' => 6, 'sistema' => 'FINANCEIRO', 'aberto' => false, 'hora_abertura' => null, 'hora_fechamento' => null, 'observacoes' => 'Fechado aos sábados'],
            ['empresa_id' => 1, 'dia_semana_id' => 7, 'sistema' => 'FINANCEIRO', 'aberto' => false, 'hora_abertura' => null, 'hora_fechamento' => null, 'observacoes' => 'Fechado aos domingos'],
        ];

        foreach ($horarios_exemplo as $horario) {
            DB::table('empresa_horarios_funcionamento')->insert(array_merge($horario, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 5. Inserir exceção de exemplo (Natal)
        DB::table('empresa_horarios_funcionamento')->insert([
            'empresa_id' => 1,
            'dia_semana_id' => null,
            'sistema' => 'TODOS',
            'aberto' => false,
            'is_excecao' => true,
            'data_excecao' => '2024-12-25',
            'descricao_excecao' => 'Feriado de Natal',
            'observacoes' => 'Empresa fechada para o feriado de Natal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_horarios_logs');
        Schema::dropIfExists('empresa_horarios_funcionamento');
        Schema::dropIfExists('empresa_dias_semana');
    }
};
