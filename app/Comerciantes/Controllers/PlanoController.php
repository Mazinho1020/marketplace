<?php

namespace App\Comerciantes\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AfiPlanPlanos;
use App\Models\AfiPlanAssinaturas;
use App\Models\AfiPlanTransacoes;
use App\Models\AfiPlanGateways;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PlanoController extends Controller
{
    /**
     * Dashboard principal do plano
     */
    public function dashboard()
    {
        $empresaId = Auth::guard('comerciante')->user()->empresa_id;
        $userId = Auth::guard('comerciante')->user()->id;

        // Buscar assinatura atual
        $assinatura = AfiPlanAssinaturas::with('plano')
            ->where('empresa_id', $empresaId)
            ->where('funforcli_id', $userId)
            ->whereIn('status', ['trial', 'ativo'])
            ->orderBy('created_at', 'desc')
            ->first();

        // Buscar transações recentes usando tabela existente
        $transacoesRecentes = AfiPlanTransacoes::where('empresa_id', $empresaId)
            ->whereIn('tipo_origem', ['nova_assinatura', 'renovacao_assinatura'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $stats = $this->getUsageStats($empresaId);
        $alertas = $this->getAlertas($assinatura);

        return view('comerciantes.planos.dashboard', compact(
            'assinatura',
            'transacoesRecentes',
            'stats',
            'alertas'
        ));
    }

    /**
     * Página para escolher/alterar planos
     */
    public function planos()
    {
        $empresaId = Auth::guard('comerciante')->user()->empresa_id;
        $userId = Auth::guard('comerciante')->user()->id;

        // Buscar todos os planos disponíveis (globais)
        $planos = AfiPlanPlanos::global()
            ->ativo()
            ->orderBy('preco_mensal')
            ->get();

        // Plano atual
        $assinaturaAtual = AfiPlanAssinaturas::with('plano')
            ->where('empresa_id', $empresaId)
            ->where('funforcli_id', $userId)
            ->whereIn('status', ['trial', 'ativo'])
            ->orderBy('created_at', 'desc')
            ->first();

        return view('comerciantes.planos.planos', compact('planos', 'assinaturaAtual'));
    }

    /**
     * Processar alteração de plano usando sistema existente
     */
    public function alterarPlano(Request $request)
    {
        $request->validate([
            'plano_id' => 'required|exists:afi_plan_planos,id',
            'ciclo_cobranca' => 'required|in:mensal,anual',
            'forma_pagamento' => 'required|in:pix,credit_card,bank_slip'
        ]);

        $empresaId = Auth::guard('comerciante')->user()->empresa_id;
        $userId = Auth::guard('comerciante')->user()->id;
        $user = Auth::guard('comerciante')->user();

        $novoPlano = AfiPlanPlanos::findOrFail($request->plano_id);

        try {
            DB::beginTransaction();

            // Definir valor baseado no ciclo
            $valor = $request->ciclo_cobranca === 'anual'
                ? $novoPlano->preco_anual
                : $novoPlano->preco_mensal;

            // Buscar gateway ativo
            $gateway = AfiPlanGateways::global()
                ->ativo()
                ->where('provedor', $request->forma_pagamento === 'pix' ? 'pix' : 'boleto')
                ->first();

            if (!$gateway) {
                throw new \Exception('Gateway de pagamento não disponível');
            }

            // Cancelar assinatura atual se houver
            AfiPlanAssinaturas::where('empresa_id', $empresaId)
                ->where('funforcli_id', $userId)
                ->whereIn('status', ['trial', 'ativo'])
                ->update([
                    'status' => 'cancelado',
                    'cancelado_em' => now()
                ]);

            // Criar nova assinatura (ainda inativa)
            $expiraEm = $request->ciclo_cobranca === 'anual'
                ? now()->addYear()
                : now()->addMonth();

            $assinatura = AfiPlanAssinaturas::create([
                'empresa_id' => $empresaId,
                'funforcli_id' => $userId,
                'plano_id' => $novoPlano->id,
                'ciclo_cobranca' => $request->ciclo_cobranca,
                'valor' => $valor,
                'status' => 'trial', // Fica em trial até pagamento
                'trial_expira_em' => now()->addDays(1),
                'expira_em' => $expiraEm,
                'proxima_cobranca_em' => $expiraEm,
                'renovacao_automatica' => true
            ]);

            // Criar transação usando sistema existente
            $transacao = AfiPlanTransacoes::create([
                'uuid' => Str::uuid(),
                'empresa_id' => $empresaId,
                'codigo_transacao' => 'TXN_' . $userId . '_' . date('YmdHi'),
                'cliente_id' => $userId,
                'gateway_id' => $gateway->id,
                'tipo_origem' => 'nova_assinatura',
                'id_origem' => $assinatura->id,
                'valor_original' => $valor,
                'valor_final' => $valor,
                'moeda' => 'BRL',
                'forma_pagamento' => $request->forma_pagamento,
                'status' => 'pendente',
                'cliente_nome' => $user->nome,
                'cliente_email' => $user->email,
                'descricao' => "Assinatura do plano {$novoPlano->nome} - {$request->ciclo_cobranca}",
                'metadados' => [
                    'plano_id' => $novoPlano->id,
                    'ciclo_cobranca' => $request->ciclo_cobranca,
                    'assinatura_id' => $assinatura->id,
                    'user_id' => $userId
                ],
                'expira_em' => now()->addMinutes(30) // PIX expira em 30 min
            ]);

            DB::commit();

            return redirect()->route('comerciantes.planos.checkout', $transacao->uuid)
                ->with('success', 'Plano selecionado! Finalize o pagamento para ativar.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Erro ao processar alteração de plano: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Página de checkout usando sistema existente
     */
    public function checkout($transactionUuid)
    {
        try {
            $empresaId = Auth::guard('comerciante')->user()->empresa_id;

            // Buscar transação do sistema existente
            $transaction = AfiPlanTransacoes::where('uuid', $transactionUuid)
                ->where('empresa_id', $empresaId)
                ->firstOrFail();

            $plano = AfiPlanPlanos::find($transaction->metadados['plano_id'] ?? null);

            return view('comerciantes.planos.checkout', compact('transaction', 'plano'));
        } catch (\Exception $e) {
            return redirect()->route('comerciantes.planos.planos')
                ->with('error', 'Transação não encontrada');
        }
    }

    /**
     * Confirmar pagamento usando sistema existente
     * NOTA: Este método é apenas para simulação/teste
     * Na produção, o pagamento seria confirmado via webhook automático
     */
    public function confirmarPagamento(Request $request, $transactionUuid)
    {
        try {
            $empresaId = Auth::guard('comerciante')->user()->empresa_id;

            // Buscar transação do sistema existente
            $transaction = AfiPlanTransacoes::where('uuid', $transactionUuid)
                ->where('empresa_id', $empresaId)
                ->where('status', 'pendente')
                ->firstOrFail();

            DB::beginTransaction();

            // Atualizar status da transação
            $transaction->update([
                'status' => 'aprovado',
                'aprovado_em' => now(),
                'processado_em' => now(),
                'gateway_transacao_id' => 'SIMULATED_' . Str::random(10)
            ]);

            // Ativar assinatura relacionada
            if ($transaction->id_origem) {
                $assinatura = AfiPlanAssinaturas::find($transaction->id_origem);

                if ($assinatura) {
                    $assinatura->update([
                        'status' => 'ativo',
                        'iniciado_em' => now(),
                        'ultima_cobranca_em' => now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pagamento confirmado com sucesso!',
                'redirect' => route('comerciantes.planos.sucesso')
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar pagamento: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Histórico de pagamentos usando sistema existente
     */
    public function historico(Request $request)
    {
        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        // Usar AfiPlanTransacoes do sistema existente
        $query = AfiPlanTransacoes::where('empresa_id', $empresaId)
            ->whereIn('tipo_origem', ['nova_assinatura', 'renovacao_assinatura']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('forma_pagamento')) {
            $query->where('forma_pagamento', $request->forma_pagamento);
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('created_at', [
                $request->data_inicio . ' 00:00:00',
                $request->data_fim . ' 23:59:59'
            ]);
        }

        $transacoes = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estatísticas usando sistema existente
        $statsHistorico = [
            'total_pago' => AfiPlanTransacoes::where('empresa_id', $empresaId)
                ->whereIn('tipo_origem', ['nova_assinatura', 'renovacao_assinatura'])
                ->where('status', 'aprovado')
                ->sum('valor_final'),
            'total_transacoes' => AfiPlanTransacoes::where('empresa_id', $empresaId)
                ->whereIn('tipo_origem', ['nova_assinatura', 'renovacao_assinatura'])
                ->count(),
            'pendentes' => AfiPlanTransacoes::where('empresa_id', $empresaId)
                ->whereIn('tipo_origem', ['nova_assinatura', 'renovacao_assinatura'])
                ->where('status', 'pendente')
                ->count()
        ];

        return view('comerciantes.planos.historico', compact('transacoes', 'statsHistorico'));
    }

    /**
     * Obter estatísticas de uso baseadas no sistema existente
     */
    private function getUsageStats($empresaId): array
    {
        // Buscar dados de uso baseado no sistema existente
        return [
            'transacoes_mes' => [
                'usado' => AfiPlanTransacoes::where('empresa_id', $empresaId)
                    ->whereIn('tipo_origem', ['nova_assinatura', 'renovacao_assinatura'])
                    ->whereMonth('created_at', now()->month)
                    ->count(),
                'limite' => 1000 // Isso viria do plano ativo
            ],
            'usuarios' => [
                'usado' => DB::table('empresa_usuarios')
                    ->where('empresa_id', $empresaId)
                    ->where('status', 'ativo')
                    ->count(),
                'limite' => 3
            ],
            'storage_mb' => [
                'usado' => 250, // Implementar cálculo real
                'limite' => 500
            ]
        ];
    }

    /**
     * Gerar alertas baseados na assinatura
     */
    private function getAlertas($assinatura): array
    {
        $alertas = [];

        if (!$assinatura) {
            $alertas[] = [
                'tipo' => 'danger',
                'titulo' => 'Sem Plano Ativo',
                'mensagem' => 'Você não possui um plano ativo. Escolha um plano para continuar usando o sistema.',
                'acao' => route('comerciantes.planos.planos')
            ];
        } elseif ($assinatura->isProximoVencimento()) {
            $alertas[] = [
                'tipo' => 'warning',
                'titulo' => 'Plano Expirando',
                'mensagem' => "Seu plano expira em {$assinatura->dias_restantes} dias. Renove para não perder o acesso.",
                'acao' => route('comerciantes.planos.planos')
            ];
        }

        return $alertas;
    }

    /**
     * Toggle renovação automática
     */
    public function toggleRenovacao(Request $request)
    {
        $request->validate([
            'renovacao_automatica' => 'required|boolean'
        ]);

        try {
            $empresaId = Auth::guard('comerciante')->user()->empresa_id;
            $userId = Auth::guard('comerciante')->user()->id;

            // Buscar assinatura ativa
            $assinatura = AfiPlanAssinaturas::where('empresa_id', $empresaId)
                ->where('funforcli_id', $userId)
                ->whereIn('status', ['trial', 'ativo'])
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$assinatura) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma assinatura ativa encontrada'
                ], 404);
            }

            // Atualizar renovação automática
            $assinatura->update([
                'renovacao_automatica' => $request->renovacao_automatica
            ]);

            $mensagem = $request->renovacao_automatica
                ? 'Renovação automática ativada com sucesso!'
                : 'Renovação automática desativada com sucesso!';

            return response()->json([
                'success' => true,
                'message' => $mensagem,
                'renovacao_automatica' => $assinatura->renovacao_automatica
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar configuração: ' . $e->getMessage()
            ], 500);
        }
    }
}
