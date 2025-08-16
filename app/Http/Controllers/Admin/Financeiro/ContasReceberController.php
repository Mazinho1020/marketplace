<?php

namespace App\Http\Controllers\Admin\Financeiro;

use App\Http\Controllers\Controller;
use App\Services\Financeiro\LancamentoService;
use Illuminate\Http\Request;

class ContasReceberController extends Controller
{
    public function __construct(
        private LancamentoService $lancamentoService
    ) {}

    public function index(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $filtros = $request->only(['situacao_financeira', 'data_inicio', 'data_fim', 'pessoa_id']);
        
        $contasReceber = $this->lancamentoService->listarContasReceber($empresaId, $filtros);
        
        return view('admin.financeiro.contas-receber.index', compact('contasReceber'));
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

        $conta = $this->lancamentoService->criarContaReceber($validated);

        return redirect()
            ->route('admin.contas-receber.show', $conta)
            ->with('success', 'Conta a receber criada com sucesso!');
    }
}