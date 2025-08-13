<?php

namespace App\Services\Financial;

use App\Models\Financial\LancamentoFinanceiro;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CobrancaAutomaticaService
{
    /**
     * Processar todas as cobranças automáticas diárias
     */
    public function processarCobrancasDiarias(): array
    {
        $resultado = [
            'avisos_vencimento' => 0,
            'cobrancas_vencidas' => 0,
            'contas_atualizadas' => 0,
            'emails_enviados' => 0,
            'erros' => []
        ];

        try {
            // 1. Processar vencimentos (atualizar status)
            $resultado['contas_atualizadas'] = $this->processarVencimentos();

            // 2. Avisos de vencimento (7, 3, 1 dia antes)
            $resultado['avisos_vencimento'] = $this->enviarAvisosVencimento();

            // 3. Cobranças de contas vencidas
            $resultado['cobrancas_vencidas'] = $this->enviarCobrancasVencidas();

            // 4. Processar recorrências
            $this->processarRecorrencias();

            Log::info('Cobrança automática processada com sucesso', $resultado);

        } catch (\Exception $e) {
            $resultado['erros'][] = $e->getMessage();
            Log::error('Erro no processamento de cobrança automática: ' . $e->getMessage());
        }

        return $resultado;
    }

    /**
     * Atualizar status de contas vencidas
     */
    public function processarVencimentos(): int
    {
        $contasVencidas = LancamentoFinanceiro::where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)
            ->where('data_vencimento', '<', now()->startOfDay())
            ->get();

        foreach ($contasVencidas as $conta) {
            $conta->situacao_financeira = SituacaoFinanceiraEnum::VENCIDO;
            $conta->save();

            // Calcular encargos se configurado
            $conta->calcularEncargos();
        }

        return $contasVencidas->count();
    }

    /**
     * Enviar avisos de vencimento
     */
    public function enviarAvisosVencimento(): int
    {
        $avisos = 0;
        $diasAvisos = [7, 3, 1]; // Dias antes do vencimento

        foreach ($diasAvisos as $dias) {
            $dataLimite = now()->addDays($dias)->format('Y-m-d');
            
            $contas = LancamentoFinanceiro::where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)
                ->whereDate('data_vencimento', $dataLimite)
                ->with(['pessoa', 'empresa'])
                ->get();

            foreach ($contas as $conta) {
                if ($this->shouldSendAlert($conta, 'vencimento', $dias)) {
                    $this->enviarAvisoVencimento($conta, $dias);
                    $avisos++;
                }
            }
        }

        return $avisos;
    }

    /**
     * Enviar cobranças escalonadas para contas vencidas
     */
    public function enviarCobrancasVencidas(): int
    {
        $cobrancas = 0;
        $diasCobranca = [15, 30, 60, 90]; // Dias após vencimento

        foreach ($diasCobranca as $dias) {
            $dataLimite = now()->subDays($dias)->format('Y-m-d');
            
            $contas = LancamentoFinanceiro::where('situacao_financeira', SituacaoFinanceiraEnum::VENCIDO)
                ->whereDate('data_vencimento', $dataLimite)
                ->with(['pessoa', 'empresa'])
                ->get();

            foreach ($contas as $conta) {
                if ($this->shouldSendAlert($conta, 'cobranca', $dias)) {
                    $this->enviarCobrancaVencida($conta, $dias);
                    $cobrancas++;
                }
            }
        }

        return $cobrancas;
    }

    /**
     * Processar recorrências automáticas
     */
    public function processarRecorrencias(): int
    {
        $recorrencias = 0;

        $contasRecorrentes = LancamentoFinanceiro::where('e_recorrente', true)
            ->where('situacao_financeira', SituacaoFinanceiraEnum::PAGO)
            ->whereNotNull('frequencia_recorrencia')
            ->where(function ($query) {
                $query->whereNull('proxima_recorrencia')
                    ->orWhere('proxima_recorrencia', '<=', now());
            })
            ->get();

        foreach ($contasRecorrentes as $conta) {
            try {
                $novaConta = $conta->gerarProximaRecorrencia();
                if ($novaConta) {
                    $recorrencias++;
                    
                    // Atualizar próxima recorrência
                    $conta->proxima_recorrencia = $conta->frequencia_recorrencia
                        ->calcularProximaData(Carbon::parse($conta->proxima_recorrencia ?? $conta->data_vencimento));
                    $conta->save();
                }
            } catch (\Exception $e) {
                Log::error("Erro ao processar recorrência da conta {$conta->id}: " . $e->getMessage());
            }
        }

        return $recorrencias;
    }

    /**
     * Verificar se deve enviar alerta baseado na configuração
     */
    private function shouldSendAlert(LancamentoFinanceiro $conta, string $tipo, int $dias): bool
    {
        if (!$conta->config_alertas) {
            return false;
        }

        $configAlertas = $conta->config_alertas;
        
        // Verificar se há configuração para este tipo de alerta
        foreach ($configAlertas as $alerta) {
            if ($alerta['tipo'] === $tipo && $alerta['dias'] === $dias) {
                return $alerta['ativo'] ?? true;
            }
        }

        // Configuração padrão se não especificada
        return in_array($dias, [7, 3, 1, 15, 30, 60, 90]);
    }

    /**
     * Enviar aviso de vencimento
     */
    private function enviarAvisoVencimento(LancamentoFinanceiro $conta, int $dias): void
    {
        try {
            $dadosEmail = [
                'conta' => $conta,
                'dias_vencimento' => $dias,
                'empresa' => $conta->empresa,
                'pessoa' => $conta->pessoa,
            ];

            // Verificar se a pessoa tem email
            if (!$conta->pessoa->email ?? null) {
                Log::warning("Conta {$conta->id} sem email para envio de aviso");
                return;
            }

            // Aqui você implementaria o envio do email
            // Mail::to($conta->pessoa->email)->send(new AvisoVencimentoMail($dadosEmail));
            
            Log::info("Aviso de vencimento enviado para conta {$conta->id}");

        } catch (\Exception $e) {
            Log::error("Erro ao enviar aviso de vencimento para conta {$conta->id}: " . $e->getMessage());
        }
    }

    /**
     * Enviar cobrança para conta vencida
     */
    private function enviarCobrancaVencida(LancamentoFinanceiro $conta, int $diasAtraso): void
    {
        try {
            $dadosEmail = [
                'conta' => $conta,
                'dias_atraso' => $diasAtraso,
                'empresa' => $conta->empresa,
                'pessoa' => $conta->pessoa,
                'valor_atualizado' => $conta->valor_final + $conta->valor_juros + $conta->valor_multa,
            ];

            // Verificar se a pessoa tem email
            if (!$conta->pessoa->email ?? null) {
                Log::warning("Conta {$conta->id} sem email para envio de cobrança");
                return;
            }

            // Determinar template de cobrança baseado nos dias de atraso
            $template = $this->getTemplateCobranca($diasAtraso);

            // Aqui você implementaria o envio do email
            // Mail::to($conta->pessoa->email)->send(new CobrancaVencidaMail($dadosEmail, $template));
            
            Log::info("Cobrança enviada para conta {$conta->id} com {$diasAtraso} dias de atraso");

        } catch (\Exception $e) {
            Log::error("Erro ao enviar cobrança para conta {$conta->id}: " . $e->getMessage());
        }
    }

    /**
     * Determinar template de cobrança baseado nos dias de atraso
     */
    private function getTemplateCobranca(int $diasAtraso): string
    {
        return match (true) {
            $diasAtraso <= 15 => 'cobranca_leve',
            $diasAtraso <= 30 => 'cobranca_moderada',
            $diasAtraso <= 60 => 'cobranca_firme',
            default => 'cobranca_final'
        };
    }

    /**
     * Obter resumo de cobranças por empresa
     */
    public function getResumoCobrancas(int $empresaId): array
    {
        $baseQuery = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER);

        return [
            'avisos_proximos' => $baseQuery->clone()
                ->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)
                ->whereBetween('data_vencimento', [now(), now()->addDays(7)])
                ->count(),
            
            'vencidas_ate_30' => $baseQuery->clone()
                ->where('situacao_financeira', SituacaoFinanceiraEnum::VENCIDO)
                ->whereBetween('data_vencimento', [now()->subDays(30), now()])
                ->count(),
            
            'vencidas_acima_30' => $baseQuery->clone()
                ->where('situacao_financeira', SituacaoFinanceiraEnum::VENCIDO)
                ->where('data_vencimento', '<', now()->subDays(30))
                ->count(),
            
            'valor_total_cobranca' => $baseQuery->clone()
                ->where('situacao_financeira', SituacaoFinanceiraEnum::VENCIDO)
                ->sum('valor_final'),
        ];
    }

    /**
     * Pausar cobrança automática para uma conta específica
     */
    public function pausarCobranca(int $contaId, string $motivo = null): bool
    {
        try {
            $conta = LancamentoFinanceiro::findOrFail($contaId);
            
            $configAlertas = $conta->config_alertas ?? [];
            $configAlertas['pausada'] = true;
            $configAlertas['motivo_pausa'] = $motivo;
            $configAlertas['data_pausa'] = now();
            
            $conta->config_alertas = $configAlertas;
            $conta->save();

            Log::info("Cobrança pausada para conta {$contaId}: {$motivo}");
            return true;

        } catch (\Exception $e) {
            Log::error("Erro ao pausar cobrança para conta {$contaId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retomar cobrança automática para uma conta específica
     */
    public function retomarCobranca(int $contaId): bool
    {
        try {
            $conta = LancamentoFinanceiro::findOrFail($contaId);
            
            $configAlertas = $conta->config_alertas ?? [];
            unset($configAlertas['pausada'], $configAlertas['motivo_pausa'], $configAlertas['data_pausa']);
            
            $conta->config_alertas = $configAlertas;
            $conta->save();

            Log::info("Cobrança retomada para conta {$contaId}");
            return true;

        } catch (\Exception $e) {
            Log::error("Erro ao retomar cobrança para conta {$contaId}: " . $e->getMessage());
            return false;
        }
    }
}