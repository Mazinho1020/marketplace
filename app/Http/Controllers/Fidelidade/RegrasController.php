<?php

namespace App\Http\Controllers\Fidelidade;

use App\Http\Controllers\Controller;
use App\Models\Fidelidade\FidelidadeCashbackRegra;
use App\Models\Fidelidade\ProgramaFidelidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegrasController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->get('search');
            $status = $request->get('status');
            $tipo = $request->get('tipo');

            $regras = FidelidadeCashbackRegra::query()
                ->when($search, function ($query, $search) {
                    return $query->where('tipo_regra', 'like', "%{$search}%");
                })
                ->when($status, function ($query, $status) {
                    return $query->where('ativo', $status === 'ativa' ? 1 : 0);
                })
                ->when($tipo, function ($query, $tipo) {
                    return $query->where('tipo_regra', $tipo);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $estatisticas = [
                'total_regras' => FidelidadeCashbackRegra::count(),
                'regras_ativas' => FidelidadeCashbackRegra::where('ativo', 1)->count(),
                'regras_pausadas' => FidelidadeCashbackRegra::where('ativo', 0)->count(),
                'cashback_medio' => FidelidadeCashbackRegra::where('ativo', 1)->avg('percentual_cashback') ?? 0
            ];

            return view('fidelidade.regras.index', compact('regras', 'estatisticas'));
        } catch (\Exception $e) {
            return view('fidelidade.regras.index', [
                'regras' => FidelidadeCashbackRegra::paginate(20),
                'estatisticas' => [
                    'total_regras' => 0,
                    'regras_ativas' => 0,
                    'regras_pausadas' => 0,
                    'cashback_medio' => 0
                ]
            ]);
        }
    }

    public function create()
    {
        return view('fidelidade.regras.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'tipo_regra' => 'required|in:cashback,pontos,categoria,produto,valor_minimo,primeira_compra,aniversario',
            'percentual_cashback' => 'nullable|numeric|min:0|max:100',
            'pontos_por_real' => 'nullable|numeric|min:0',
            'valor_minimo' => 'nullable|numeric|min:0',
            'valor_maximo' => 'nullable|numeric|min:0',
            'categoria_produto' => 'nullable|string',
            'produto_especifico' => 'nullable|string',
            'nivel_cliente_minimo' => 'nullable|string',
            'nivel_cliente_maximo' => 'nullable|string',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after:data_inicio',
            'limite_uso_diario' => 'nullable|integer|min:1',
            'limite_uso_mensal' => 'nullable|integer|min:1',
            'empresa_id' => 'nullable|exists:businesses,id',
            'programa_id' => 'nullable|exists:programas_fidelidade,id',
            'acumulativo' => 'boolean',
            'apenas_primeira_compra' => 'boolean'
        ]);

        // Validar que pelo menos um tipo de recompensa foi definido
        if (!$request->percentual_cashback && !$request->pontos_por_real) {
            return redirect()->back()
                ->withErrors(['erro' => 'Defina pelo menos um valor de cashback ou pontos por real'])
                ->withInput();
        }

        $regra = FidelidadeCashbackRegra::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'tipo_regra' => $request->tipo_regra,
            'percentual_cashback' => $request->percentual_cashback ?: 0,
            'pontos_por_real' => $request->pontos_por_real ?: 0,
            'valor_minimo' => $request->valor_minimo,
            'valor_maximo' => $request->valor_maximo,
            'categoria_produto' => $request->categoria_produto,
            'produto_especifico' => $request->produto_especifico,
            'nivel_cliente_minimo' => $request->nivel_cliente_minimo,
            'nivel_cliente_maximo' => $request->nivel_cliente_maximo,
            'data_inicio' => $request->data_inicio,
            'data_fim' => $request->data_fim,
            'limite_uso_diario' => $request->limite_uso_diario,
            'limite_uso_mensal' => $request->limite_uso_mensal,
            'empresa_id' => $request->empresa_id,
            'programa_id' => $request->programa_id,
            'acumulativo' => $request->boolean('acumulativo'),
            'apenas_primeira_compra' => $request->boolean('apenas_primeira_compra'),
            'status' => 'ativa',
            'usado_count' => 0
        ]);

        return redirect()->route('fidelidade.regras.show', $regra->id)
            ->with('success', 'Regra criada com sucesso!');
    }

    public function show($id)
    {
        try {
            $regra = FidelidadeCashbackRegra::findOrFail($id);

            // Estatísticas básicas de uso da regra
            $estatisticasUso = [
                'total_usos' => 0,
                'cashback_distribuido' => 0,
                'clientes_beneficiados' => 0,
                'uso_ultimo_mes' => 0
            ];

            return view('fidelidade.regras.show', compact('regra', 'estatisticasUso'));
        } catch (\Exception $e) {
            return redirect()->route('fidelidade.regras.index')
                ->with('error', 'Regra não encontrada.');
        }
    }

    public function edit($id)
    {
        try {
            $regra = FidelidadeCashbackRegra::findOrFail($id);
            return view('fidelidade.regras.edit', compact('regra'));
        } catch (\Exception $e) {
            return redirect()->route('fidelidade.regras.index')
                ->with('error', 'Regra não encontrada.');
        }
    }

    public function update(Request $request, $id)
    {
        $regra = FidelidadeCashbackRegra::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'tipo_regra' => 'required|in:cashback,pontos,categoria,produto,valor_minimo,primeira_compra,aniversario',
            'percentual_cashback' => 'nullable|numeric|min:0|max:100',
            'pontos_por_real' => 'nullable|numeric|min:0',
            'valor_minimo' => 'nullable|numeric|min:0',
            'valor_maximo' => 'nullable|numeric|min:0',
            'categoria_produto' => 'nullable|string',
            'produto_especifico' => 'nullable|string',
            'nivel_cliente_minimo' => 'nullable|string',
            'nivel_cliente_maximo' => 'nullable|string',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after:data_inicio',
            'limite_uso_diario' => 'nullable|integer|min:1',
            'limite_uso_mensal' => 'nullable|integer|min:1',
            'acumulativo' => 'boolean',
            'apenas_primeira_compra' => 'boolean',
            'status' => 'required|in:ativa,inativa,pausada'
        ]);

        // Validar que pelo menos um tipo de recompensa foi definido
        if (!$request->percentual_cashback && !$request->pontos_por_real) {
            return redirect()->back()
                ->withErrors(['erro' => 'Defina pelo menos um valor de cashback ou pontos por real'])
                ->withInput();
        }

        $regra->update($request->only([
            'nome',
            'descricao',
            'tipo_regra',
            'percentual_cashback',
            'pontos_por_real',
            'valor_minimo',
            'valor_maximo',
            'categoria_produto',
            'produto_especifico',
            'nivel_cliente_minimo',
            'nivel_cliente_maximo',
            'data_inicio',
            'data_fim',
            'limite_uso_diario',
            'limite_uso_mensal',
            'acumulativo',
            'apenas_primeira_compra',
            'status'
        ]));

        return redirect()->route('fidelidade.regras.show', $regra->id)
            ->with('success', 'Regra atualizada com sucesso!');
    }

    public function duplicar($id)
    {
        $regra = FidelidadeCashbackRegra::findOrFail($id);

        $novaRegra = $regra->replicate();
        $novaRegra->nome = $regra->nome . ' (Cópia)';
        $novaRegra->status = 'inativa';
        $novaRegra->usado_count = 0;
        $novaRegra->save();

        return redirect()->route('fidelidade.regras.edit', $novaRegra->id)
            ->with('success', 'Regra duplicada com sucesso! Ajuste as configurações necessárias.');
    }

    public function ativar($id)
    {
        $regra = FidelidadeCashbackRegra::findOrFail($id);
        $regra->update(['status' => 'ativa']);

        return redirect()->back()->with('success', 'Regra ativada com sucesso!');
    }

    public function desativar($id)
    {
        $regra = FidelidadeCashbackRegra::findOrFail($id);
        $regra->update(['status' => 'inativa']);

        return redirect()->back()->with('success', 'Regra desativada com sucesso!');
    }

    public function ordenar(Request $request)
    {
        $request->validate([
            'regras' => 'required|array',
            'regras.*.id' => 'required|exists:fidelidade_cashback_regras,id'
        ]);

        return response()->json(['success' => true, 'message' => 'Ordenação não disponível no momento']);
    }

    public function testarRegra(Request $request, $id)
    {
        $regra = FidelidadeCashbackRegra::findOrFail($id);

        $request->validate([
            'valor_compra' => 'required|numeric|min:0',
            'cliente_nivel' => 'nullable|string',
            'categoria_produto' => 'nullable|string',
            'primeira_compra' => 'boolean'
        ]);

        $resultado = $this->calcularRecompensa(
            $regra,
            $request->valor_compra,
            $request->cliente_nivel,
            $request->categoria_produto,
            $request->boolean('primeira_compra')
        );

        return response()->json($resultado);
    }

    public function destroy($id)
    {
        $regra = FidelidadeCashbackRegra::findOrFail($id);

        if ($regra->usado_count > 0) {
            return redirect()->back()->with('error', 'Não é possível excluir uma regra que já foi utilizada.');
        }

        $regra->delete();

        return redirect()->route('fidelidade.regras.index')
            ->with('success', 'Regra excluída com sucesso!');
    }

    private function calcularRecompensa($regra, $valorCompra, $clienteNivel = null, $categoriaProduto = null, $primeiraCompra = false)
    {
        $aplicavel = true;
        $motivos = [];

        // Verificar valor mínimo
        if ($regra->valor_minimo && $valorCompra < $regra->valor_minimo) {
            $aplicavel = false;
            $motivos[] = "Valor mínimo não atingido (R$ {$regra->valor_minimo})";
        }

        // Verificar valor máximo
        if ($regra->valor_maximo && $valorCompra > $regra->valor_maximo) {
            $aplicavel = false;
            $motivos[] = "Valor máximo excedido (R$ {$regra->valor_maximo})";
        }

        // Verificar nível do cliente
        if ($regra->nivel_cliente_minimo && $clienteNivel < $regra->nivel_cliente_minimo) {
            $aplicavel = false;
            $motivos[] = "Nível mínimo não atingido ({$regra->nivel_cliente_minimo})";
        }

        // Verificar categoria do produto
        if ($regra->categoria_produto && $categoriaProduto !== $regra->categoria_produto) {
            $aplicavel = false;
            $motivos[] = "Categoria do produto não corresponde";
        }

        // Verificar primeira compra
        if ($regra->apenas_primeira_compra && !$primeiraCompra) {
            $aplicavel = false;
            $motivos[] = "Regra válida apenas para primeira compra";
        }

        $cashback = 0;
        $pontos = 0;

        if ($aplicavel) {
            if ($regra->percentual_cashback > 0) {
                $cashback = ($valorCompra * $regra->percentual_cashback) / 100;
            }

            if ($regra->pontos_por_real > 0) {
                $pontos = $valorCompra * $regra->pontos_por_real;
            }
        }

        return [
            'aplicavel' => $aplicavel,
            'motivos' => $motivos,
            'cashback' => $cashback,
            'pontos' => $pontos,
            'valor_final' => $valorCompra - $cashback
        ];
    }
}
