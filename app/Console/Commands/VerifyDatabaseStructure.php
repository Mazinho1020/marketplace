<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyDatabaseStructure extends Command
{
    protected $signature = 'db:verify-structure';
    protected $description = 'Verificar estrutura e dados das tabelas do sistema';

    public function handle()
    {
        $this->info('=== VERIFICAÇÃO DOS DADOS DO BANCO ===');
        $this->newLine();

        try {
            // 1. Verificar estrutura dos planos
            $this->info('1. ESTRUTURA DOS PLANOS:');
            $planos = DB::select('SELECT id, codigo, nome, recursos, limites FROM afi_plan_planos LIMIT 3');

            foreach ($planos as $plano) {
                $this->line("  Plano {$plano->id} ({$plano->codigo}): {$plano->nome}");
                $this->line("    Recursos: " . ($plano->recursos ? 'CONFIGURADO' : 'NULL'));
                $this->line("    Limites: " . ($plano->limites ? 'CONFIGURADO' : 'NULL'));

                if ($plano->recursos) {
                    $recursos = is_string($plano->recursos) ? json_decode($plano->recursos, true) : $plano->recursos;
                    if ($recursos && is_array($recursos)) {
                        $this->line("    Recursos válidos: " . implode(', ', array_keys($recursos)));
                    }
                }
                $this->newLine();
            }

            // 2. Verificar gateways
            $this->info('2. GATEWAYS DE PAGAMENTO:');
            $gateways = DB::select('SELECT id, nome, provedor, credenciais, ativo FROM afi_plan_gateways LIMIT 3');

            foreach ($gateways as $gateway) {
                $this->line("  Gateway {$gateway->id}: {$gateway->nome} ({$gateway->provedor})");
                $this->line("    Status: " . ($gateway->ativo ? 'ATIVO' : 'INATIVO'));
                $this->line("    Credenciais: " . ($gateway->credenciais ? 'CONFIGURADAS' : 'NÃO CONFIGURADAS'));

                if ($gateway->credenciais) {
                    $creds = is_string($gateway->credenciais) ? json_decode($gateway->credenciais, true) : $gateway->credenciais;
                    if ($creds && is_array($creds)) {
                        $this->line("    Chaves: " . implode(', ', array_keys($creds)));
                    }
                }
                $this->newLine();
            }

            // 3. Verificar configurações
            $this->info('3. CONFIGURAÇÕES DO SISTEMA:');
            $configCount = DB::selectOne('SELECT COUNT(*) as total FROM afi_plan_configuracoes');
            $this->line("  Total de configurações: {$configCount->total}");

            if ($configCount->total > 0) {
                $configs = DB::select('SELECT chave, valor, empresa_id FROM afi_plan_configuracoes LIMIT 5');
                $this->line("  Configurações de exemplo:");
                foreach ($configs as $config) {
                    $this->line("    {$config->chave} (empresa {$config->empresa_id}): " . substr($config->valor, 0, 50) . "...");
                }
            }

            // 4. Verificar views
            $this->newLine();
            $this->info('4. VIEWS DO SISTEMA:');
            $viewTests = [
                'merchants' => 'SELECT COUNT(*) as total FROM merchants',
                'payment_transactions' => 'SELECT COUNT(*) as total FROM payment_transactions',
                'subscription_plans' => 'SELECT COUNT(*) as total FROM subscription_plans'
            ];

            foreach ($viewTests as $viewName => $query) {
                try {
                    $result = DB::selectOne($query);
                    $this->line("  View {$viewName}: {$result->total} registros");
                } catch (\Exception $e) {
                    $this->error("  View {$viewName}: ERRO - " . $e->getMessage());
                }
            }

            $this->newLine();
            $this->info('=== VERIFICAÇÃO CONCLUÍDA ===');
        } catch (\Exception $e) {
            $this->error("ERRO: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
