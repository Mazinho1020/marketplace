<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TesteTimezone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teste:timezone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa se o timezone está sendo aplicado corretamente nas inserções';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== TESTE DE TIMEZONE ===');

        try {
            // Testa inserção com now() (função helper do Laravel)
            $testId = DB::table('notificacao_enviadas')->insertGetId([
                'empresa_id' => 1, // Adicionando empresa_id obrigatório
                'aplicacao_id' => 1, // Adicionando aplicacao_id obrigatório
                'email_destinatario' => 'teste@timezone.com',
                'canal' => 'email',
                'titulo' => 'Teste de Timezone',
                'mensagem' => 'Teste para verificar se o horário está sendo salvo corretamente',
                'status' => 'enviado',
                'tentativas' => 1,
                'enviado_em' => now(), // Usando helper do Laravel
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->info("Registro inserido com ID: $testId");

            // Recupera o registro recém inserido
            $registro = DB::table('notificacao_enviadas')->where('id', $testId)->first();

            $this->info('=== DADOS DO REGISTRO INSERIDO ===');
            $this->line("ID: " . $registro->id);
            $this->line("Email: " . $registro->email_destinatario);
            $this->line("Enviado em: " . $registro->enviado_em);
            $this->line("Created at: " . $registro->created_at);
            $this->line("Updated at: " . $registro->updated_at);

            $this->info('=== COMPARAÇÃO DE HORÁRIOS ===');
            $this->line("Horário atual do sistema (PHP): " . date('Y-m-d H:i:s'));
            $this->line("Horário atual (Laravel now()): " . now()->format('Y-m-d H:i:s'));
            $this->line("Horário atual (Carbon Cuiabá): " . Carbon::now('America/Cuiaba')->format('Y-m-d H:i:s'));
            $this->line("Horário atual (Carbon UTC): " . Carbon::now('UTC')->format('Y-m-d H:i:s'));

            // Testa inserção com Carbon explícito
            $testId2 = DB::table('notificacao_enviadas')->insertGetId([
                'empresa_id' => 1, // Adicionando empresa_id obrigatório
                'aplicacao_id' => 1, // Adicionando aplicacao_id obrigatório
                'email_destinatario' => 'teste2@timezone.com',
                'canal' => 'email',
                'titulo' => 'Teste de Timezone com Carbon',
                'mensagem' => 'Teste com Carbon explícito',
                'status' => 'enviado',
                'tentativas' => 1,
                'enviado_em' => Carbon::now('America/Cuiaba')->format('Y-m-d H:i:s'),
                'created_at' => Carbon::now('America/Cuiaba')->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now('America/Cuiaba')->format('Y-m-d H:i:s')
            ]);

            $registro2 = DB::table('notificacao_enviadas')->where('id', $testId2)->first();

            $this->info('=== SEGUNDO TESTE (CARBON EXPLÍCITO) ===');
            $this->line("ID: " . $registro2->id);
            $this->line("Email: " . $registro2->email_destinatario);
            $this->line("Enviado em: " . $registro2->enviado_em);
            $this->line("Created at: " . $registro2->created_at);
            $this->line("Updated at: " . $registro2->updated_at);

            $this->info('=== CONCLUSÃO ===');
            if ($registro->enviado_em === $registro2->enviado_em) {
                $this->info("✅ SUCESSO: Ambos os métodos estão produzindo o mesmo horário!");
                $this->info("✅ O timezone America/Cuiaba está sendo aplicado corretamente.");
            } else {
                $this->warn("⚠️  ATENÇÃO: Os métodos estão produzindo horários diferentes:");
                $this->line("   now(): " . $registro->enviado_em);
                $this->line("   Carbon explícito: " . $registro2->enviado_em);
            }

            // Limpa os registros de teste
            DB::table('notificacao_enviadas')->whereIn('id', [$testId, $testId2])->delete();
            $this->info("Registros de teste removidos.");
        } catch (\Exception $e) {
            $this->error("ERRO: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
        }

        return 0;
    }
}
