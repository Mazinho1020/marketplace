<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ConfigService;

class SetupSafe2PayDemo extends Command
{
    protected $signature = 'payment:setup-safe2pay-demo {empresa_id}';
    protected $description = 'Setup Safe2Pay demo credentials for testing';

    public function __construct(
        private ConfigService $configService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $empresaId = (int) $this->argument('empresa_id');

        $this->info("Configurando Safe2Pay DEMO para empresa ID: {$empresaId}");

        // Credenciais de demonstração fornecidas
        $configs = [
            'payment_safe2pay_enabled' => '1',
            'payment_safe2pay_environment' => 'sandbox',
            'payment_safe2pay_token' => 'E8FA28B86AAD45589B80294D01639AE0',
            'payment_safe2pay_secret_key' => '84165F50AFDB402FBD5EF8A83109ADC79E6C8B8FD15C40E483E31317B6F7E5BB',
            'payment_safe2pay_webhook_secret' => 'webhook_secret_demo_123',
            'payment_safe2pay_webhook_url' => url('/api/webhooks/safe2pay'),
            'payment_safe2pay_methods' => json_encode(['pix', 'credit_card', 'bank_slip']),
            'payment_safe2pay_pix_enabled' => '1',
            'payment_safe2pay_credit_card_enabled' => '1',
            'payment_safe2pay_bank_slip_enabled' => '1',
            'payment_safe2pay_fee_percentage' => '2.99',
            'payment_safe2pay_fee_fixed' => '0.39',
            'payment_safe2pay_success_url' => url('/payment/success'),
            'payment_safe2pay_cancel_url' => url('/payment/cancel'),
        ];

        $this->info('Salvando configurações...');

        foreach ($configs as $key => $value) {
            $this->configService->setEmpresaId($empresaId)->set($key, $value);
            $this->line("✓ {$key}: {$value}");
        }

        $this->info('✅ Safe2Pay DEMO configurado com sucesso!');

        // Teste da configuração
        if ($this->confirm('Deseja testar a configuração agora?')) {
            $this->testSafe2PayConnection($empresaId);
        }

        return 0;
    }

    private function testSafe2PayConnection(int $empresaId): void
    {
        try {
            $this->info('🔄 Testando conexão com Safe2Pay...');

            // Instancia o serviço de configuração
            $configService = app(\App\Services\Payment\PaymentConfigService::class)
                ->setEmpresaId($empresaId);

            $config = $configService->getGatewayConfig(\App\Enums\Payment\GatewayProvider::SAFE2PAY);

            $this->info('📋 Configurações carregadas:');
            $this->line("- Ambiente: {$config['environment']}");
            $this->line("- Token: " . substr($config['credentials']['token'], 0, 8) . "...");
            $this->line("- Métodos: " . implode(', ', $config['methods']));

            $this->info('✅ Configuração válida! Sistema pronto para processar pagamentos.');
        } catch (\Exception $e) {
            $this->error("❌ Erro ao testar configuração: {$e->getMessage()}");
        }
    }
}
