<?php

namespace App\Http\Controllers\Admin\Financeiro;

use App\Http\Controllers\Controller;
use App\Services\Financeiro\LancamentoService;
use Illuminate\Http\Request;

class ContasPagarController extends Controller
{
    public function __construct(
        private LancamentoService $lancamentoService
    ) {}

    public function index(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $filtros = $request->only(['situacao_financeira', 'data_inicio', 'data_fim', 'pessoa_id']);
        
        $contasPagar = $this->lancamentoService->listarContasPagar($empresaId, $filtros);
        
        return view('admin.financeiro.contas-pagar.index', compact('contasPagar'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'pessoa_id' => 'nullable|integer',
            'pessoa_tipo' => 'nullable|in:cliente,fornecedor,funcionario',
            'conta_gerencial_id' => 'nullable|exists:contas_gerenciais,id',
            'valor_bruto' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'data_emissao' => 'required|date',
            'data_competencia' => 'required|date',
            'descricao' => 'required|string|max:500',
            'observacoes' => 'nullable|string',
        ]);

        $conta = $this->lancamentoService->criarContaPagar($validated);

        return redirect()
            ->route('admin.contas-pagar.show', $conta)
            ->with('success', 'Conta a pagar criada com sucesso!');
    }
}