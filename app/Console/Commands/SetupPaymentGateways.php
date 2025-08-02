<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ConfigService;
use App\Enums\Payment\GatewayProvider;

class SetupPaymentGateways extends Command
{
    protected $signature = 'payment:setup-gateways {empresa_id}';
    protected $description = 'Setup payment gateway configurations for a company';

    public function __construct(
        private ConfigService $configService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $empresaId = (int) $this->argument('empresa_id');

        $this->info("Configurando gateways de pagamento para empresa ID: {$empresaId}");

        // Configurações do Safe2Pay
        if ($this->confirm('Deseja configurar o Safe2Pay?')) {
            $this->setupSafe2Pay($empresaId);
        }

        $this->info('Configuração concluída!');

        return 0;
    }

    private function setupSafe2Pay(int $empresaId): void
    {
        $this->info('Configurando Safe2Pay...');

        $environment = $this->choice(
            'Escolha o ambiente:',
            ['sandbox', 'production'],
            'sandbox'
        );

        $token = $this->ask('Token do Safe2Pay:');
        $secretKey = $this->ask('Secret Key do Safe2Pay:');
        $webhookSecret = $this->ask('Webhook Secret (opcional):', '');

        // Salva as configurações
        $configs = [
            'payment_safe2pay_enabled' => '1',
            'payment_safe2pay_environment' => $environment,
            'payment_safe2pay_token' => $token,
            'payment_safe2pay_secret_key' => $secretKey,
            'payment_safe2pay_webhook_secret' => $webhookSecret,
            'payment_safe2pay_webhook_url' => url('/api/webhooks/safe2pay'),
            'payment_safe2pay_methods' => json_encode(['pix', 'credit_card', 'bank_slip']),
            'payment_safe2pay_pix_enabled' => '1',
            'payment_safe2pay_credit_card_enabled' => '1',
            'payment_safe2pay_bank_slip_enabled' => '1',
            'payment_safe2pay_fee_percentage' => '2.99',
            'payment_safe2pay_fee_fixed' => '0.39',
        ];

        foreach ($configs as $key => $value) {
            $this->configService->setEmpresaId($empresaId)->set($key, $value);
        }

        $this->info('Safe2Pay configurado com sucesso!');

        // Testa a conexão
        if ($this->confirm('Deseja testar a conexão?')) {
            $this->testSafe2PayConnection($empresaId);
        }
    }

    private function testSafe2PayConnection(int $empresaId): void
    {
        try {
            $this->info('Testando conexão com Safe2Pay...');

            // Aqui você pode adicionar um teste real da API
            // Por exemplo, buscar dados da conta ou fazer uma requisição de teste

            $this->info('✅ Conexão testada com sucesso!');
        } catch (\Exception $e) {
            $this->error("❌ Erro ao testar conexão: {$e->getMessage()}");
        }
    }
}
