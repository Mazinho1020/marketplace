<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QuickSetupSafe2Pay extends Command
{
    protected $signature = 'payment:quick-setup-safe2pay {empresa_id}';
    protected $description = 'Quick setup Safe2Pay with demo credentials';

    public function handle(): int
    {
        $empresaId = (int) $this->argument('empresa_id');

        $this->info("Setup rápido Safe2Pay para empresa {$empresaId}");

        // Busca grupo de pagamento ou usa grupo padrão
        $grupoId = DB::table('config_groups')->where('codigo', 'payment')->value('id') ?? 1;

        // Configurações essenciais
        $configs = [
            'payment_safe2pay_enabled' => ['Safe2Pay Habilitado', 'boolean', '1'],
            'payment_safe2pay_environment' => ['Safe2Pay Ambiente', 'string', 'sandbox'],
            'payment_safe2pay_token' => ['Safe2Pay Token', 'string', 'E8FA28B86AAD45589B80294D01639AE0'],
            'payment_safe2pay_secret_key' => ['Safe2Pay Secret', 'string', '84165F50AFDB402FBD5EF8A83109ADC79E6C8B8FD15C40E483E31317B6F7E5BB'],
        ];

        foreach ($configs as $chave => $info) {
            [$nome, $tipo, $valor] = $info;

            // Verifica se já existe
            $exists = DB::table('config_definitions')
                ->where('empresa_id', $empresaId)
                ->where('chave', $chave)
                ->exists();

            if (!$exists) {
                // Insere definição
                DB::table('config_definitions')->insert([
                    'empresa_id' => $empresaId,
                    'chave' => $chave,
                    'nome' => $nome,
                    'descricao' => $nome,
                    'tipo' => $tipo,
                    'grupo_id' => $grupoId,
                    'valor_padrao' => $valor,
                    'obrigatorio' => 1,
                    'editavel' => 1,
                    'ativo' => 1,
                    'ordem' => 1,
                    'sync_status' => 'pendente',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->line("✓ Definição criada: {$chave}");
            } else {
                $this->line("- Definição existe: {$chave}");
            }

            // Busca o ID da definição criada
            $configId = DB::table('config_definitions')
                ->where('empresa_id', $empresaId)
                ->where('chave', $chave)
                ->value('id');

            if ($configId) {
                // Insere/atualiza valor
                DB::table('config_values')->updateOrInsert([
                    'empresa_id' => $empresaId,
                    'config_id' => $configId,
                ], [
                    'valor' => $valor,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]);

                $this->line("✓ Valor definido: {$chave} = {$valor}");
            }
        }

        $this->info('✅ Safe2Pay configurado com sucesso!');

        // Teste
        $this->testConfig($empresaId);

        return 0;
    }

    private function testConfig(int $empresaId): void
    {
        try {
            $configService = app(\App\Services\ConfigService::class)->setEmpresaId($empresaId);
            $token = $configService->get('payment_safe2pay_token');

            $this->info("🔗 Token carregado: " . substr($token, 0, 8) . "...");
            $this->info("✅ Sistema pronto para processar pagamentos!");
        } catch (\Exception $e) {
            $this->error("❌ Erro: {$e->getMessage()}");
        }
    }
}
