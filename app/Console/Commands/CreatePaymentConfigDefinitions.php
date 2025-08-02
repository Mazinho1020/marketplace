<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Config\ConfigDefinition;
use App\Models\Config\ConfigGroup;

class CreatePaymentConfigDefinitions extends Command
{
    protected $signature = 'payment:create-config-definitions';
    protected $description = 'Create payment configuration definitions in the database';

    public function handle(): int
    {
        $this->info('Criando definições de configuração para pagamentos...');

        // Busca ou cria o grupo de pagamentos
        $paymentGroup = ConfigGroup::firstOrCreate([
            'codigo' => 'payment',
            'empresa_id' => 1, // Usando empresa padrão
        ], [
            'nome' => 'Configurações de Pagamento',
            'descricao' => 'Configurações dos gateways de pagamento',
            'ativo' => true
        ]);

        $this->line("✓ Grupo 'payment' criado/encontrado (ID: {$paymentGroup->id})");

        // Definições de configuração para Safe2Pay
        $definitions = [
            // Configurações gerais Safe2Pay
            [
                'chave' => 'payment_safe2pay_enabled',
                'nome' => 'Safe2Pay Habilitado',
                'descricao' => 'Habilita o gateway Safe2Pay',
                'tipo' => 'boolean',
                'valor_padrao' => '0',
                'obrigatorio' => true,
                'categoria' => 'gateway'
            ],
            [
                'chave' => 'payment_safe2pay_environment',
                'nome' => 'Safe2Pay Ambiente',
                'descricao' => 'Ambiente do Safe2Pay (sandbox ou production)',
                'tipo' => 'string',
                'valor_padrao' => 'sandbox',
                'obrigatorio' => true,
                'categoria' => 'gateway'
            ],
            [
                'chave' => 'payment_safe2pay_token',
                'nome' => 'Safe2Pay Token',
                'descricao' => 'Token de autenticação do Safe2Pay',
                'tipo' => 'string',
                'valor_padrao' => '',
                'obrigatorio' => true,
                'categoria' => 'gateway'
            ],
            [
                'chave' => 'payment_safe2pay_secret_key',
                'nome' => 'Safe2Pay Secret Key',
                'descricao' => 'Chave secreta do Safe2Pay',
                'tipo' => 'string',
                'valor_padrao' => '',
                'obrigatorio' => true,
                'categoria' => 'gateway'
            ],
            [
                'chave' => 'payment_safe2pay_webhook_secret',
                'nome' => 'Safe2Pay Webhook Secret',
                'descricao' => 'Secret para validação de webhooks',
                'tipo' => 'string',
                'valor_padrao' => '',
                'obrigatorio' => false,
                'categoria' => 'webhook'
            ],
            [
                'chave' => 'payment_safe2pay_webhook_url',
                'nome' => 'Safe2Pay Webhook URL',
                'descricao' => 'URL para receber webhooks do Safe2Pay',
                'tipo' => 'string',
                'valor_padrao' => '',
                'obrigatorio' => false,
                'categoria' => 'webhook'
            ],

            // Métodos de pagamento
            [
                'chave' => 'payment_safe2pay_methods',
                'nome' => 'Safe2Pay Métodos',
                'descricao' => 'Métodos de pagamento habilitados (JSON)',
                'tipo' => 'json',
                'valor_padrao' => '["pix","credit_card","bank_slip"]',
                'obrigatorio' => true,
                'categoria' => 'methods'
            ],
            [
                'chave' => 'payment_safe2pay_pix_enabled',
                'nome' => 'Safe2Pay PIX Habilitado',
                'descricao' => 'Habilita pagamentos via PIX',
                'tipo' => 'boolean',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'categoria' => 'methods'
            ],
            [
                'chave' => 'payment_safe2pay_credit_card_enabled',
                'nome' => 'Safe2Pay Cartão Crédito',
                'descricao' => 'Habilita pagamentos via cartão de crédito',
                'tipo' => 'boolean',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'categoria' => 'methods'
            ],
            [
                'chave' => 'payment_safe2pay_bank_slip_enabled',
                'nome' => 'Safe2Pay Boleto',
                'descricao' => 'Habilita pagamentos via boleto',
                'tipo' => 'boolean',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'categoria' => 'methods'
            ],

            // Taxas
            [
                'chave' => 'payment_safe2pay_fee_percentage',
                'nome' => 'Safe2Pay Taxa Percentual',
                'descricao' => 'Taxa percentual cobrada pelo gateway',
                'tipo' => 'decimal',
                'valor_padrao' => '2.99',
                'obrigatorio' => false,
                'categoria' => 'fees'
            ],
            [
                'chave' => 'payment_safe2pay_fee_fixed',
                'nome' => 'Safe2Pay Taxa Fixa',
                'descricao' => 'Taxa fixa cobrada pelo gateway',
                'tipo' => 'decimal',
                'valor_padrao' => '0.39',
                'obrigatorio' => false,
                'categoria' => 'fees'
            ],

            // URLs
            [
                'chave' => 'payment_safe2pay_success_url',
                'nome' => 'Safe2Pay URL Sucesso',
                'descricao' => 'URL de redirecionamento após pagamento aprovado',
                'tipo' => 'string',
                'valor_padrao' => '',
                'obrigatorio' => false,
                'categoria' => 'urls'
            ],
            [
                'chave' => 'payment_safe2pay_cancel_url',
                'nome' => 'Safe2Pay URL Cancelamento',
                'descricao' => 'URL de redirecionamento após cancelamento',
                'tipo' => 'string',
                'valor_padrao' => '',
                'obrigatorio' => false,
                'categoria' => 'urls'
            ]
        ];

        $created = 0;
        foreach ($definitions as $def) {
            $existing = ConfigDefinition::where('chave', $def['chave'])->first();

            if (!$existing) {
                ConfigDefinition::create([
                    'empresa_id' => 1, // Empresa padrão
                    'chave' => $def['chave'],
                    'nome' => $def['nome'],
                    'descricao' => $def['descricao'],
                    'tipo' => $def['tipo'],
                    'grupo_id' => $paymentGroup->id,
                    'valor_padrao' => $def['valor_padrao'],
                    'obrigatorio' => $def['obrigatorio'],
                    'ordem' => $created + 1,
                    'ativo' => true
                ]);

                $this->line("✓ {$def['chave']}");
                $created++;
            } else {
                $this->line("- {$def['chave']} (já existe)");
            }
        }

        $this->info("✅ Criadas {$created} definições de configuração para pagamentos!");

        return 0;
    }
}
