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

class ContasPagarController extends Controller
{
    public function index(Request $request, $empresa)
    {
        $empresa = Empresa::findOrFail($empresa);
        $empresaId = $empresa->id;

        $query = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR)
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

        $contasPagar = $query->paginate(20);

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

        return view('comerciantes.financeiro.contas-pagar.index', compact(
            'contasPagar',
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

        return view('comerciantes.financeiro.contas-pagar.create', compact('pessoas', 'contasGerenciais', 'empresa'));
    }

    public function store(Request $request, $empresa)
    {
        $empresa = Empresa::findOrFail($empresa);
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor_original' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'pessoa_id' => 'nullable|exists:pessoas,id',
            'conta_gerencial_id' => 'nullable|exists:conta_gerencial,id',
            'numero_documento' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|max:1000',
            'parcelado' => 'boolean',
            'numero_parcelas' => 'nullable|integer|min:1|max:360',
            'intervalo_parcelas' => 'nullable|in:mensal,quinzenal,semanal,diario',
            'cobranca_automatica' => 'boolean',
            'gerar_boleto' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $empresaId = Auth::user()->empresa_id ?? 1;

            // Se for parcelado, criar múltiplos lançamentos
            if ($request->parcelado && $request->numero_parcelas > 1) {
                $this->criarLancamentosParcelados($request, $empresaId);
            } else {
                // Criar lançamento único
                $this->criarLancamentoUnico($request, $empresaId);
            }

            DB::commit();

            return redirect()->route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa)
                ->with('success', 'Conta a pagar criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withInput()
                ->with('error', 'Erro ao criar conta a pagar: ' . $e->getMessage());
        }
    }

    public function show($empresa, $id)
    {
        $empresa = Empresa::findOrFail($empresa);
        $empresaId = $empresa->id;

        $contaPagar = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('id', $id)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR)
            ->with(['empresa', 'contaGerencial', 'pessoa', 'parcelasRelacionadas'])
            ->firstOrFail();

        return view('comerciantes.financeiro.contas-pagar.show', compact('contaPagar', 'empresa'));
    }

    public function edit($empresa, $id)
    {
        $empresa = Empresa::findOrFail($empresa);
        $empresaId = $empresa->id;

        $contaPagar = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('id', $id)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR)
            ->firstOrFail();

        // Não permitir edição se já foi paga
        if ($contaPagar->situacao_financeira === SituacaoFinanceiraEnum::PAGO) {
            return redirect()->route('comerciantes.empresas.financeiro.contas-pagar.show', [$empresa, $id])
                ->with('error', 'Não é possível editar uma conta que já foi paga.');
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

        return view('comerciantes.financeiro.contas-pagar.edit', compact('contaPagar', 'pessoas', 'contasGerenciais', 'empresa'));
    }

    public function update(Request $request, $empresa, $id)
    {
        $empresa = Empresa::findOrFail($empresa);
        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor_original' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'pessoa_id' => 'nullable|exists:pessoas,id',
            'conta_gerencial_id' => 'nullable|exists:conta_gerencial,id',
            'numero_documento' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $empresaId = Auth::user()->empresa_id ?? 1;

            $contaPagar = LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('id', $id)
                ->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR)
                ->firstOrFail();

            // Não permitir edição se já foi paga
            if ($contaPagar->situacao_financeira === SituacaoFinanceiraEnum::PAGO) {
                return back()->with('error', 'Não é possível editar uma conta que já foi paga.');
            }

            $contaPagar->update([
                'descricao' => $request->descricao,
                'valor_original' => $request->valor_original,
                'data_vencimento' => $request->data_vencimento,
                'pessoa_id' => $request->pessoa_id,
                'conta_gerencial_id' => $request->conta_gerencial_id,
                'numero_documento' => $request->numero_documento,
                'observacoes' => $request->observacoes,
            ]);

            DB::commit();

            return redirect()->route('comerciantes.empresas.financeiro.contas-pagar.show', [$empresa, $id])
                ->with('success', 'Conta a pagar atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar conta a pagar: ' . $e->getMessage());
        }
    }

    public function destroy($empresa, $id)
    {
        DB::beginTransaction();

        try {
            $empresa = Empresa::findOrFail($empresa);
            $empresaId = $empresa->id;

            $contaPagar = LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('id', $id)
                ->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR)
                ->firstOrFail();

            // Não permitir exclusão se já foi paga
            if ($contaPagar->situacao_financeira === SituacaoFinanceiraEnum::PAGO) {
                return response()->json(['error' => 'Não é possível excluir uma conta que já foi paga.'], 422);
            }

            $contaPagar->delete();

            DB::commit();

            return response()->json(['success' => 'Conta a pagar excluída com sucesso!']);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => 'Erro ao excluir conta a pagar: ' . $e->getMessage()], 500);
        }
    }

    public function pagar(Request $request, $empresa, $id)
    {
        $empresa = Empresa::findOrFail($empresa);

        $request->validate([
            'data_pagamento' => 'required|date',
            'valor_pago' => 'required|numeric|min:0.01',
            'desconto' => 'nullable|numeric|min:0',
            'juros' => 'nullable|numeric|min:0',
            'multa' => 'nullable|numeric|min:0',
            'observacoes_pagamento' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $empresaId = Auth::user()->empresa_id ?? 1;

            $contaPagar = LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('id', $id)
                ->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR)
                ->firstOrFail();

            if ($contaPagar->situacao_financeira === SituacaoFinanceiraEnum::PAGO) {
                return back()->with('error', 'Esta conta já foi paga.');
            }

            $valorFinal = $request->valor_pago + ($request->juros ?? 0) + ($request->multa ?? 0) - ($request->desconto ?? 0);

            $contaPagar->update([
                'situacao_financeira' => SituacaoFinanceiraEnum::PAGO,
                'data_pagamento' => $request->data_pagamento,
                'valor_pago' => $request->valor_pago,
                'valor_desconto' => $request->desconto ?? 0,
                'valor_juros' => $request->juros ?? 0,
                'valor_multa' => $request->multa ?? 0,
                'valor_final' => $valorFinal,
                'observacoes_pagamento' => $request->observacoes_pagamento,
                'usuario_pagamento_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('comerciantes.empresas.financeiro.contas-pagar.show', [$empresa, $id])
                ->with('success', 'Pagamento registrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withInput()
                ->with('error', 'Erro ao registrar pagamento: ' . $e->getMessage());
        }
    }

    // Métodos privados auxiliares

    private function criarLancamentoUnico(Request $request, int $empresaId)
    {
        return LancamentoFinanceiro::create([
            'empresa_id' => $empresaId,
            'natureza_financeira' => NaturezaFinanceiraEnum::PAGAR,
            'situacao_financeira' => SituacaoFinanceiraEnum::PENDENTE,
            'descricao' => $request->descricao,
            'valor' => $request->valor_original, // Corrigido: adicionar valor obrigatório
            'valor_original' => $request->valor_original,
            'data_vencimento' => $request->data_vencimento,
            'pessoa_id' => $request->pessoa_id,
            'conta_gerencial_id' => $request->conta_gerencial_id,
            'numero_documento' => $request->numero_documento,
            'observacoes' => $request->observacoes,
            'usuario_id' => Auth::id(),
        ]);
    }

    private function criarLancamentosParcelados(Request $request, int $empresaId)
    {
        $valorParcela = $request->valor_original / $request->numero_parcelas;
        $dataVencimento = Carbon::parse($request->data_vencimento);
        $parcelaReferencia = uniqid('CP_' . $empresaId . '_'); // Gerar referência única

        for ($i = 1; $i <= $request->numero_parcelas; $i++) {
            LancamentoFinanceiro::create([
                'empresa_id' => $empresaId,
                'natureza_financeira' => NaturezaFinanceiraEnum::PAGAR,
                'situacao_financeira' => SituacaoFinanceiraEnum::PENDENTE,
                'descricao' => $request->descricao . " (Parcela {$i}/{$request->numero_parcelas})",
                'valor' => round($valorParcela, 2), // Corrigido: adicionar valor obrigatório
                'valor_original' => round($valorParcela, 2),
                'data_vencimento' => $dataVencimento->copy(),
                'pessoa_id' => $request->pessoa_id,
                'conta_gerencial_id' => $request->conta_gerencial_id,
                'numero_documento' => $request->numero_documento,
                'observacoes' => $request->observacoes,
                'parcela_atual' => $i,
                'total_parcelas' => $request->numero_parcelas,
                'parcela_referencia' => $parcelaReferencia, // Adicionar referência
                'usuario_id' => Auth::id(),
            ]);

            // Calcular próxima data de vencimento
            switch ($request->intervalo_parcelas) {
                case 'mensal':
                    $dataVencimento->addMonth();
                    break;
                case 'quinzenal':
                    $dataVencimento->addDays(15);
                    break;
                case 'semanal':
                    $dataVencimento->addWeek();
                    break;
                case 'diario':
                    $dataVencimento->addDay();
                    break;
                default:
                    $dataVencimento->addMonth();
            }
        }
    }

    private function calcularEstatisticas(int $empresaId): array
    {
        $hoje = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        $query = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR);

        return [
            'total_pendente' => $query->clone()->where('situacao_financeira', 'pendente')->sum('valor_final'),
            'total_pago' => $query->clone()->where('situacao_financeira', 'pago')->sum('valor_final'),
            'vencidas' => $query->clone()->where('data_vencimento', '<', $hoje)
                ->where('situacao_financeira', '!=', 'pago')->count(),
            'este_mes' => $query->clone()->whereBetween('data_vencimento', [$inicioMes, $fimMes])
                ->where('situacao_financeira', '!=', 'pago')->sum('valor_final'),
            'quantidade_pendente' => $query->clone()->where('situacao_financeira', 'pendente')->count(),
            'proximos_vencimentos' => $query->clone()->where('situacao_financeira', '!=', 'pago')
                ->whereBetween('data_vencimento', [$hoje, $hoje->copy()->addDays(7)])
                ->count(),
        ];
    }
}
