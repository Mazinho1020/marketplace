<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\LancamentoFinanceiro;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;
use App\Models\Empresa;
use App\Models\Financial\ContaGerencial;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ContasReceberController extends Controller
{
    public function index(Request $request, $empresa)
    {
        $empresa = Empresa::findOrFail($empresa);
        $empresaId = $empresa->id;

        $query = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
            ->with(['empresa', 'contaGerencial', 'pessoa'])
            ->orderBy('data_vencimento', 'asc');

        // Filtros
        if ($request->filled('situacao')) {
            $query->where('situacao_financeira', $request->situacao);
        }

        if ($request->filled('data_inicio')) {
            $query->where('data_vencimento', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->where('data_vencimento', '<=', $request->data_fim);
        }

        if ($request->filled('pessoa_id')) {
            $query->where('pessoa_id', $request->pessoa_id);
        }

        if ($request->filled('conta_gerencial_id')) {
            $query->where('conta_gerencial_id', $request->conta_gerencial_id);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('descricao', 'like', "%{$busca}%")
                    ->orWhere('numero_documento', 'like', "%{$busca}%")
                    ->orWhere('observacoes', 'like', "%{$busca}%");
            });
        }

        // Filtro por grupo de parcelas
        if ($request->filled('grupo')) {
            $query->where('grupo_parcelas', $request->grupo);
        }

        $contasReceber = $query->paginate(20);

        // Estatísticas
        $estatisticas = $this->calcularEstatisticas($empresaId);

        // Dados para filtros
        $pessoas = Cliente::where('empresa_id', $empresaId)
            ->clientes()
            ->ativos()
            ->select('id', 'nome', 'cpf_cnpj')
            ->orderBy('nome')
            ->get();

        $contasGerenciais = ContaGerencial::where('empresa_id', $empresaId)
            ->ativos()
            ->select('id', 'nome', 'codigo')
            ->orderBy('nome')
            ->get();

        return view('comerciantes.financeiro.contas-receber.index', compact(
            'contasReceber',
            'empresa',
            'estatisticas'
        ));
    }

    public function create($empresa)
    {
        $empresa = Empresa::findOrFail($empresa);
        $empresaId = $empresa->id;

        $pessoas = Cliente::where('empresa_id', $empresaId)
            ->clientes()
            ->ativos()
            ->select('id', 'nome', 'cpf_cnpj')
            ->orderBy('nome')
            ->get();

        $contasGerenciais = ContaGerencial::where('empresa_id', $empresaId)
            ->ativos()
            ->select('id', 'nome', 'codigo')
            ->orderBy('nome')
            ->get();

        return view('comerciantes.financeiro.contas-receber.create', compact('pessoas', 'contasGerenciais', 'empresa'));
    }

    public function store(Request $request, $empresa)
    {
        $empresa = Empresa::findOrFail($empresa);
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor_original' => 'nullable|numeric|min:0.01',
            'valor_total' => 'nullable|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'data_emissao' => 'nullable|date',
            'data_competencia' => 'nullable|date',
            'pessoa_id' => 'nullable|exists:pessoas,id',
            'cliente_id' => 'nullable|exists:pessoas,id',
            'conta_gerencial_id' => 'nullable|exists:conta_gerencial,id',
            'numero_documento' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|max:1000',
            'desconto' => 'nullable|numeric|min:0',
            'valor_desconto' => 'nullable|numeric|min:0',
            'valor_acrescimo' => 'nullable|numeric|min:0',
            'juros' => 'nullable|numeric|min:0',
            'multa' => 'nullable|numeric|min:0',
            'situacao_financeira' => 'nullable|in:pendente,pago,cancelado',
            'natureza_financeira' => 'nullable|in:receber',
            'e_recorrente' => 'nullable|boolean',
            'parcelado' => 'boolean',
            'tem_parcelamento' => 'boolean',
            'numero_parcelas' => 'nullable|integer|min:1|max:360',
            'intervalo_parcelas' => 'nullable|integer|min:1',
            'cobranca_automatica' => 'boolean',
            'gerar_boleto' => 'boolean',
        ], [
            'valor_original.required_without' => 'O valor é obrigatório',
            'valor_total.required_without' => 'O valor é obrigatório',
        ]);

        DB::beginTransaction();

        try {
            $empresaId = Auth::user()->empresa_id ?? 1;

            // Se for parcelado, criar múltiplos lançamentos
            $numeroParcelas = (int)$request->numero_parcelas;
            $isParcelado = $request->has('habilitarParcelamento') || $request->has('is_parcelado') || $numeroParcelas > 1;

            if ($isParcelado && $numeroParcelas > 1) {
                $this->criarLancamentosParcelados($request, $empresaId);
            } else {
                // Criar lançamento único
                $this->criarLancamentoUnico($request, $empresaId);
            }

            DB::commit();

            return redirect()->route('comerciantes.empresas.financeiro.contas-receber.index', $empresa)
                ->with('success', 'Conta a receber criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withInput()
                ->with('error', 'Erro ao criar conta a receber: ' . $e->getMessage());
        }
    }

    public function show($empresa, $id)
    {
        $empresa = Empresa::findOrFail($empresa);
        $empresaId = $empresa->id;

        $contaReceber = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('id', $id)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
            ->with(['empresa', 'contaGerencial', 'pessoa', 'parcelasRelacionadas'])
            ->firstOrFail();

        // Carregar recebimentos realizados
        $recebimentos = $contaReceber->recebimentos()
            ->where('status_pagamento', 'confirmado')
            ->with(['formaPagamento', 'bandeira', 'contaBancaria'])
            ->orderBy('data_pagamento', 'desc')
            ->get();

        // Calcular resumo dos recebimentos
        $valorTotal = $contaReceber->valor_final;
        $valorRecebido = $recebimentos->sum('valor');
        $saldoDevedor = $valorTotal - $valorRecebido;

        $resumoRecebimentos = [
            'valor_total' => $valorTotal,
            'valor_recebido' => $valorRecebido,
            'saldo_devedor' => $saldoDevedor,
            'percentual_recebido' => $valorTotal > 0 ? round(($valorRecebido / $valorTotal) * 100, 2) : 0,
            'total_recebimentos' => $recebimentos->count(),
            'situacao_atual' => $saldoDevedor <= 0 ? 'Quitado' : 'Pendente'
        ];

        return view('comerciantes.financeiro.contas-receber.show', compact('contaReceber', 'empresa', 'recebimentos', 'resumoRecebimentos'));
    }

    public function edit($empresa, $id)
    {
        $empresa = Empresa::findOrFail($empresa);
        $empresaId = $empresa->id;

        $contaReceber = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('id', $id)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
            ->firstOrFail();

        // Não permitir edição se já foi recebida
        if ($contaReceber->situacao_financeira === SituacaoFinanceiraEnum::PAGO) {
            return redirect()->route('comerciantes.empresas.financeiro.contas-receber.show', [$empresa, $id])
                ->with('error', 'Não é possível editar uma conta que já foi recebida.');
        }

        $pessoas = Cliente::where('empresa_id', $empresaId)
            ->clientes()
            ->ativos()
            ->select('id', 'nome', 'cpf_cnpj')
            ->orderBy('nome')
            ->get();

        $contasGerenciais = ContaGerencial::where('empresa_id', $empresaId)
            ->ativos()
            ->select('id', 'nome', 'codigo')
            ->orderBy('nome')
            ->get();

        return view('comerciantes.financeiro.contas-receber.edit', compact('contaReceber', 'pessoas', 'contasGerenciais', 'empresa'));
    }

    public function update(Request $request, $empresa, $id)
    {
        $empresa = Empresa::findOrFail($empresa);
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor_original' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'data_emissao' => 'nullable|date',
            'data_competencia' => 'nullable|date',
            'pessoa_id' => 'nullable|exists:pessoas,id',
            'conta_gerencial_id' => 'nullable|exists:conta_gerencial,id',
            'numero_documento' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|max:1000',
            'desconto' => 'nullable|numeric|min:0',
            'valor_desconto' => 'nullable|numeric|min:0',
            'valor_acrescimo' => 'nullable|numeric|min:0',
            'juros' => 'nullable|numeric|min:0',
            'multa' => 'nullable|numeric|min:0',
            'situacao_financeira' => 'nullable|in:pendente,pago,cancelado',
            'natureza_financeira' => 'nullable|in:receber',
            'e_recorrente' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $empresaId = Auth::user()->empresa_id ?? 1;

            $contaReceber = LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('id', $id)
                ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
                ->firstOrFail();

            // Não permitir edição se já foi recebida
            if ($contaReceber->situacao_financeira === SituacaoFinanceiraEnum::PAGO) {
                return back()->with('error', 'Não é possível editar uma conta que já foi recebida.');
            }

            // Calcular valores com desconto, juros e multa
            $valorOriginal = $request->valor_original;
            $valorDesconto = $request->valor_desconto ?? (($request->desconto ?? 0) / 100) * $valorOriginal;
            $valorAcrescimo = $request->valor_acrescimo ?? 0;
            $valorJuros = ($request->juros ?? 0) > 0 ? (($request->juros / 100) * $valorOriginal) : 0;
            $valorMulta = ($request->multa ?? 0) > 0 ? (($request->multa / 100) * $valorOriginal) : 0;
            $valorTotal = $valorOriginal - $valorDesconto + $valorAcrescimo + $valorJuros + $valorMulta;

            // Determinar as datas
            $dataEmissao = $request->data_emissao ? Carbon::parse($request->data_emissao) : null;
            $dataCompetencia = $request->data_competencia ? Carbon::parse($request->data_competencia) : $dataEmissao;

            $contaReceber->update([
                'descricao' => $request->descricao,
                'valor_original' => $valorOriginal,
                'valor' => $valorTotal,
                'valor_final' => $valorTotal,
                'valor_desconto' => $valorDesconto,
                'valor_acrescimo' => $valorAcrescimo,
                'valor_juros' => $valorJuros,
                'valor_multa' => $valorMulta,
                'data' => $dataEmissao,
                'data_emissao' => $dataEmissao ? $dataEmissao->toDateString() : null,
                'data_competencia' => $dataCompetencia ? $dataCompetencia->toDateString() : null,
                'data_vencimento' => $request->data_vencimento,
                'pessoa_id' => $request->pessoa_id,
                'pessoa_tipo' => $request->pessoa_id ? 'cliente' : null,
                'conta_gerencial_id' => $request->conta_gerencial_id,
                'numero_documento' => $request->numero_documento,
                'observacoes' => $request->observacoes,
                'situacao_financeira' => $request->situacao_financeira ?? SituacaoFinanceiraEnum::PENDENTE,
                'natureza_financeira' => $request->natureza_financeira ?? NaturezaFinanceiraEnum::RECEBER,
                'e_recorrente' => $request->has('e_recorrente') ? true : false,
            ]);

            DB::commit();

            return redirect()->route('comerciantes.empresas.financeiro.contas-receber.show', [$empresa, $id])
                ->with('success', 'Conta a receber atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar conta a receber: ' . $e->getMessage());
        }
    }

    public function destroy($empresa, $id)
    {
        DB::beginTransaction();

        try {
            $empresaId = Auth::user()->empresa_id ?? 1;

            $contaReceber = LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('id', $id)
                ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
                ->first();

            if (!$contaReceber) {
                if (request()->ajax()) {
                    return response()->json(['error' => 'Conta a receber não encontrada ou já foi excluída.'], 404);
                }
                return redirect()->route('comerciantes.empresas.financeiro.contas-receber.index', $empresa)
                    ->with('error', 'Conta a receber não encontrada ou já foi excluída.');
            }

            // Não permitir exclusão se já foi recebida
            if ($contaReceber->situacao_financeira === SituacaoFinanceiraEnum::PAGO) {
                if (request()->ajax()) {
                    return response()->json(['error' => 'Não é possível excluir uma conta que já foi recebida.'], 422);
                }
                return redirect()->route('comerciantes.empresas.financeiro.contas-receber.index', $empresa)
                    ->with('error', 'Não é possível excluir uma conta que já foi recebida.');
            }

            $contaReceber->delete();

            DB::commit();

            // Se for uma requisição AJAX, retornar JSON
            if (request()->ajax()) {
                return response()->json(['success' => 'Conta a receber excluída com sucesso!']);
            }

            // Se for uma requisição normal (form submit), redirecionar
            return redirect()->route('comerciantes.empresas.financeiro.contas-receber.index', $empresa)
                ->with('success', 'Conta a receber excluída com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();

            if (request()->ajax()) {
                return response()->json(['error' => 'Erro ao excluir conta a receber: ' . $e->getMessage()], 500);
            }

            return redirect()->route('comerciantes.empresas.financeiro.contas-receber.index', $empresa)
                ->with('error', 'Erro ao excluir conta a receber: ' . $e->getMessage());
        }
    }

    public function gerarBoleto($empresa, $id)
    {
        $empresaId = Auth::user()->empresa_id ?? 1;

        $contaReceber = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('id', $id)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
            ->with(['empresa', 'pessoa'])
            ->firstOrFail();

        if ($contaReceber->situacao_financeira === SituacaoFinanceiraEnum::PAGO) {
            return back()->with('error', 'Não é possível gerar boleto para conta já recebida.');
        }

        // Aqui você implementaria a integração com o banco para gerar o boleto
        // Por enquanto, vamos apenas marcar que o boleto foi gerado
        $contaReceber->update([
            'boleto_gerado' => true,
            'boleto_nosso_numero' => 'BOL' . str_pad($contaReceber->id, 8, '0', STR_PAD_LEFT),
            'boleto_data_geracao' => now(),
        ]);

        return redirect()->route('comerciantes.empresas.financeiro.contas-receber.show', [$empresa, $id])
            ->with('success', 'Boleto gerado com sucesso!');
    }

    // Métodos privados auxiliares

    private function criarLancamentoUnico(Request $request, int $empresaId)
    {
        // Calcular valores com desconto e acréscimo
        $valorOriginal = $request->valor_original ?? $request->valor_total ?? 0;
        $valorDesconto = $request->valor_desconto ?? 0;
        $valorAcrescimo = $request->valor_acrescimo ?? 0;
        $valorTotal = $valorOriginal - $valorDesconto + $valorAcrescimo;

        // Determinar pessoa_id (pode vir como pessoa_id ou cliente_id)
        $pessoaId = $request->pessoa_id ?? $request->cliente_id ?? null;

        // Determinar as datas
        $dataEmissao = $request->data_emissao ? Carbon::parse($request->data_emissao) : now();
        $dataCompetencia = $request->data_competencia ? Carbon::parse($request->data_competencia) : $dataEmissao->copy();

        return LancamentoFinanceiro::create([
            'empresa_id' => $empresaId,
            'natureza_financeira' => $request->natureza_financeira ?? NaturezaFinanceiraEnum::RECEBER,
            'situacao_financeira' => $request->situacao_financeira ?? SituacaoFinanceiraEnum::PENDENTE,
            'descricao' => $request->descricao,
            'valor' => $valorTotal,
            'valor_original' => $valorOriginal,
            'valor_desconto' => $valorDesconto,
            'valor_acrescimo' => $valorAcrescimo,
            'valor_juros' => 0,
            'valor_multa' => 0,
            'valor_final' => $valorTotal,
            'data' => $dataEmissao,
            'data_emissao' => $dataEmissao->toDateString(),
            'data_competencia' => $dataCompetencia->toDateString(),
            'data_vencimento' => $request->data_vencimento,
            'pessoa_id' => $pessoaId,
            'pessoa_tipo' => $pessoaId ? 'cliente' : null,
            'conta_gerencial_id' => $request->conta_gerencial_id,
            'numero_documento' => $request->numero_documento,
            'observacoes' => $request->observacoes,
            'e_recorrente' => $request->has('e_recorrente') ? true : false,
            'usuario_id' => Auth::id(),
        ]);
    }
    private function criarLancamentosParcelados(Request $request, int $empresaId)
    {
        // Calcular valores com desconto, juros e multa
        $valorOriginal = $request->valor_original;
        $desconto = $request->desconto ?? 0;
        $juros = $request->juros ?? 0;
        $multa = $request->multa ?? 0;

        $valorDesconto = $valorOriginal * ($desconto / 100);
        $valorJuros = $valorOriginal * ($juros / 100);
        $valorMulta = $valorOriginal * ($multa / 100);
        $valorTotal = $valorOriginal - $valorDesconto + $valorJuros + $valorMulta;

        $valorParcela = $valorTotal / $request->numero_parcelas;
        $dataVencimento = Carbon::parse($request->data_vencimento);
        $grupoParcelas = uniqid('CR_' . $empresaId . '_'); // Gerar referência única para o grupo
        $dataEmissao = $request->data_emissao ? Carbon::parse($request->data_emissao) : now();

        for ($i = 1; $i <= $request->numero_parcelas; $i++) {
            LancamentoFinanceiro::create([
                'empresa_id' => $empresaId,
                'natureza_financeira' => $request->natureza_financeira ?? NaturezaFinanceiraEnum::RECEBER,
                'situacao_financeira' => $request->situacao_financeira ?? SituacaoFinanceiraEnum::PENDENTE,
                'descricao' => $request->descricao . " (Parcela {$i}/{$request->numero_parcelas})",
                'valor' => round($valorParcela, 2),
                'valor_original' => round($valorOriginal / $request->numero_parcelas, 2),
                'valor_desconto' => round($valorDesconto / $request->numero_parcelas, 2),
                'valor_acrescimo' => round(($request->valor_acrescimo ?? 0) / $request->numero_parcelas, 2),
                'valor_juros' => round($valorJuros / $request->numero_parcelas, 2),
                'valor_multa' => round($valorMulta / $request->numero_parcelas, 2),
                'valor_final' => round($valorParcela, 2),
                'data' => $dataEmissao,
                'data_emissao' => $dataEmissao->toDateString(),
                'data_competencia' => $dataEmissao->toDateString(),
                'data_vencimento' => $dataVencimento->copy()->toDateString(),
                'pessoa_id' => $request->pessoa_id,
                'pessoa_tipo' => $request->pessoa_id ? 'cliente' : null,
                'conta_gerencial_id' => $request->conta_gerencial_id,
                'numero_documento' => $request->numero_documento,
                'observacoes' => $request->observacoes,
                'parcela_atual' => $i,
                'total_parcelas' => $request->numero_parcelas,
                'grupo_parcelas' => $grupoParcelas,
                'intervalo_parcelas' => $request->intervalo_parcelas ?? 30,
                'e_recorrente' => $request->has('e_recorrente') ? true : false,
                'usuario_id' => Auth::id(),
            ]);

            // Calcular próxima data de vencimento baseado no intervalo em dias
            if ($i < $request->numero_parcelas) {
                $intervaloDias = $request->intervalo_parcelas ?? 30; // Default 30 dias
                $dataVencimento->addDays($intervaloDias);
            }
        }
    }

    private function calcularEstatisticas(int $empresaId): array
    {
        $hoje = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        $query = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER);

        return [
            'total_aberto' => $query->clone()->where('situacao_financeira', 'pendente')->sum('valor'),
            'total_recebido' => $query->clone()->where('situacao_financeira', 'pago')
                ->whereBetween('data_vencimento', [$inicioMes, $fimMes])
                ->sum('valor'),
            'vencendo_hoje' => $query->clone()->whereDate('data_vencimento', '=', $hoje)
                ->where('situacao_financeira', '!=', 'pago')->sum('valor'),
            'em_atraso' => $query->clone()->where('data_vencimento', '<', $hoje)
                ->where('situacao_financeira', '!=', 'pago')->sum('valor'),
            'quantidade_pendente' => $query->clone()->where('situacao_financeira', 'pendente')->count(),
            'quantidade_vencidas' => $query->clone()->where('data_vencimento', '<', $hoje)
                ->where('situacao_financeira', '!=', 'pago')->count(),
        ];
    }
}
