<?php

namespace App\Http\Controllers\Fidelidade;

use App\Http\Controllers\Controller;
use App\Models\Fidelidade\FidelidadeCupom;
use App\Models\Fidelidade\FidelidadeCupomUso;
use App\Models\Fidelidade\FidelidadeCarteira;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CuponsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->get('search');
            $status = $request->get('status');
            $tipo = $request->get('tipo');

            $cupons = FidelidadeCupom::query()
                ->when($search, function ($query, $search) {
                    return $query->where('codigo', 'like', "%{$search}%")
                        ->orWhere('descricao', 'like', "%{$search}%");
                })
                ->when($status, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->when($tipo, function ($query, $tipo) {
                    return $query->where('tipo_desconto', $tipo);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $totalCupons = FidelidadeCupom::count();
            $cuponsAtivos = FidelidadeCupom::where('status', 'ativo')->count();
            $cuponsUtilizados = FidelidadeCupomUso::count();

            $estatisticas = [
                'total_cupons' => $totalCupons,
                'cupons_ativos' => $cuponsAtivos,
                'cupons_utilizados' => $cuponsUtilizados,
                'taxa_conversao' => $totalCupons > 0 ? ($cuponsUtilizados / $totalCupons) * 100 : 0
            ];

            return view('fidelidade.cupons.index', compact('cupons', 'estatisticas'));
        } catch (\Exception $e) {
            return view('fidelidade.cupons.index', [
                'cupons' => FidelidadeCupom::paginate(20),
                'estatisticas' => [
                    'total_cupons' => 0,
                    'cupons_ativos' => 0,
                    'cupons_utilizados' => 0,
                    'taxa_conversao' => 0
                ]
            ]);
        }
    }

    public function create()
    {
        return view('fidelidade.cupons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'codigo' => 'nullable|string|unique:fidelidade_cupons,codigo',
            'tipo_desconto' => 'required|in:percentual,valor_fixo',
            'valor_desconto' => 'required|numeric|min:0',
            'valor_minimo_compra' => 'nullable|numeric|min:0',
            'quantidade_maxima' => 'nullable|integer|min:1',
            'limite_uso_cliente' => 'nullable|integer|min:1',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after:data_inicio',
            'empresa_id' => 'nullable|exists:businesses,id',
            'nivel_minimo_cliente' => 'nullable|string',
            'primeira_compra_apenas' => 'boolean',
            'acumulativo_cashback' => 'boolean'
        ]);

        // Gerar código se não foi fornecido
        $codigo = $request->codigo ?: $this->gerarCodigo();

        $cupom = FidelidadeCupom::create([
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'codigo' => $codigo,
            'tipo_desconto' => $request->tipo_desconto,
            'valor_desconto' => $request->valor_desconto,
            'valor_minimo_compra' => $request->valor_minimo_compra,
            'quantidade_maxima' => $request->quantidade_maxima,
            'quantidade_utilizada' => 0,
            'limite_uso_cliente' => $request->limite_uso_cliente,
            'data_inicio' => $request->data_inicio,
            'data_fim' => $request->data_fim,
            'empresa_id' => $request->empresa_id,
            'nivel_minimo_cliente' => $request->nivel_minimo_cliente,
            'primeira_compra_apenas' => $request->boolean('primeira_compra_apenas'),
            'acumulativo_cashback' => $request->boolean('acumulativo_cashback'),
            'status' => 'ativo'
        ]);

        return redirect()->route('fidelidade.cupons.show', $cupom->id)
            ->with('success', 'Cupom criado com sucesso!');
    }

    public function show($id)
    {
        $cupom = FidelidadeCupom::with(['empresa', 'usos.cliente'])
            ->findOrFail($id);

        $estatisticasUso = [
            'total_usos' => $cupom->usos->count(),
            'valor_total_descontos' => $cupom->usos->where('status', 'utilizado')->sum('valor_desconto'),
            'clientes_unicos' => $cupom->usos->where('status', 'utilizado')->groupBy('cliente_id')->count(),
            'uso_medio_mensal' => $cupom->usos->where('status', 'utilizado')
                ->where('data_uso', '>=', Carbon::now()->subMonth())
                ->count()
        ];

        $usosRecentes = $cupom->usos()
            ->with('cliente')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('fidelidade.cupons.show', compact('cupom', 'estatisticasUso', 'usosRecentes'));
    }

    public function edit($id)
    {
        $cupom = FidelidadeCupom::findOrFail($id);
        return view('fidelidade.cupons.edit', compact('cupom'));
    }

    public function update(Request $request, $id)
    {
        try {
            $cupom = FidelidadeCupom::findOrFail($id);

            // Se for apenas uma mudança de status via AJAX
            if ($request->has('status') && count($request->all()) <= 2) {
                $request->validate([
                    'status' => 'required|in:ativo,inativo,expirado'
                ]);

                $cupom->update(['status' => $request->status]);

                if ($request->wantsJson()) {
                    return response()->json(['success' => true, 'message' => 'Status atualizado com sucesso!']);
                }

                return redirect()->route('fidelidade.cupons.show', $cupom->id)
                    ->with('success', 'Status atualizado com sucesso!');
            }

            // Validação completa para formulário
            $request->validate([
                'titulo' => 'required|string|max:255',
                'descricao' => 'required|string',
                'tipo_desconto' => 'required|in:percentual,valor_fixo',
                'valor_desconto' => 'required|numeric|min:0',
                'valor_minimo_compra' => 'nullable|numeric|min:0',
                'quantidade_maxima' => 'nullable|integer|min:1',
                'limite_uso_cliente' => 'nullable|integer|min:1',
                'data_inicio' => 'required|date',
                'data_fim' => 'required|date|after:data_inicio',
                'nivel_minimo_cliente' => 'nullable|string',
                'primeira_compra_apenas' => 'boolean',
                'acumulativo_cashback' => 'boolean',
                'status' => 'required|in:ativo,inativo,expirado'
            ]);

            $cupom->update($request->only([
                'titulo',
                'descricao',
                'tipo_desconto',
                'valor_desconto',
                'valor_minimo_compra',
                'quantidade_maxima',
                'limite_uso_cliente',
                'data_inicio',
                'data_fim',
                'nivel_minimo_cliente',
                'primeira_compra_apenas',
                'acumulativo_cashback',
                'status'
            ]));

            return redirect()->route('fidelidade.cupons.show', $cupom->id)
                ->with('success', 'Cupom atualizado com sucesso!');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erro ao atualizar cupom: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar cupom: ' . $e->getMessage());
        }
    }

    public function validarCupom(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
            'cliente_id' => 'required|exists:users,id',
            'valor_compra' => 'required|numeric|min:0'
        ]);

        $cupom = FidelidadeCupom::where('codigo', $request->codigo)->first();

        if (!$cupom) {
            return response()->json([
                'valido' => false,
                'erro' => 'Cupom não encontrado'
            ]);
        }

        $validacao = $this->validarUsoCupom($cupom, $request->cliente_id, $request->valor_compra);

        if ($validacao['valido']) {
            $valorDesconto = $this->calcularDesconto($cupom, $request->valor_compra);

            return response()->json([
                'valido' => true,
                'cupom' => $cupom,
                'valor_desconto' => $valorDesconto,
                'valor_final' => $request->valor_compra - $valorDesconto
            ]);
        }

        return response()->json($validacao);
    }

    public function aplicarCupom(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
            'cliente_id' => 'required|exists:users,id',
            'valor_compra' => 'required|numeric|min:0',
            'pedido_id' => 'nullable|string'
        ]);

        $cupom = FidelidadeCupom::where('codigo', $request->codigo)->first();

        if (!$cupom) {
            return response()->json([
                'sucesso' => false,
                'erro' => 'Cupom não encontrado'
            ], 404);
        }

        $validacao = $this->validarUsoCupom($cupom, $request->cliente_id, $request->valor_compra);

        if (!$validacao['valido']) {
            return response()->json([
                'sucesso' => false,
                'erro' => $validacao['erro']
            ], 400);
        }

        $valorDesconto = $this->calcularDesconto($cupom, $request->valor_compra);

        DB::transaction(function () use ($cupom, $request, $valorDesconto) {
            // Registrar uso do cupom
            FidelidadeCupomUso::create([
                'cupom_id' => $cupom->id,
                'cliente_id' => $request->cliente_id,
                'pedido_id' => $request->pedido_id,
                'valor_desconto' => $valorDesconto,
                'valor_compra' => $request->valor_compra,
                'data_uso' => Carbon::now(),
                'status' => 'utilizado'
            ]);

            // Atualizar contador do cupom
            $cupom->increment('quantidade_utilizada');
        });

        return response()->json([
            'sucesso' => true,
            'valor_desconto' => $valorDesconto,
            'valor_final' => $request->valor_compra - $valorDesconto
        ]);
    }

    public function destroy($id)
    {
        try {
            $cupom = FidelidadeCupom::findOrFail($id);

            if ($cupom->usos()->exists()) {
                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Não é possível excluir um cupom que já foi utilizado.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Não é possível excluir um cupom que já foi utilizado.');
            }

            $cupom->delete();

            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Cupom excluído com sucesso!']);
            }

            return redirect()->route('fidelidade.cupons.index')
                ->with('success', 'Cupom excluído com sucesso!');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erro ao excluir cupom: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erro ao excluir cupom: ' . $e->getMessage());
        }
    }

    private function gerarCodigo()
    {
        do {
            $codigo = 'FIDELIDADE' . strtoupper(Str::random(6));
        } while (FidelidadeCupom::where('codigo', $codigo)->exists());

        return $codigo;
    }

    private function validarUsoCupom($cupom, $clienteId, $valorCompra)
    {
        // Verificar se o cupom está ativo
        if ($cupom->status !== 'ativo') {
            return ['valido' => false, 'erro' => 'Cupom inativo'];
        }

        // Verificar validade
        if (Carbon::now()->lt($cupom->data_inicio)) {
            return ['valido' => false, 'erro' => 'Cupom ainda não está válido'];
        }

        if (Carbon::now()->gt($cupom->data_fim)) {
            return ['valido' => false, 'erro' => 'Cupom expirado'];
        }

        // Verificar quantidade máxima
        if ($cupom->quantidade_maxima && $cupom->quantidade_utilizada >= $cupom->quantidade_maxima) {
            return ['valido' => false, 'erro' => 'Cupom esgotado'];
        }

        // Verificar valor mínimo da compra
        if ($cupom->valor_minimo_compra && $valorCompra < $cupom->valor_minimo_compra) {
            return ['valido' => false, 'erro' => "Valor mínimo da compra: R$ {$cupom->valor_minimo_compra}"];
        }

        // Verificar limite de uso por cliente
        if ($cupom->limite_uso_cliente) {
            $usosCliente = FidelidadeCupomUso::where('cupom_id', $cupom->id)
                ->where('cliente_id', $clienteId)
                ->where('status', 'utilizado')
                ->count();

            if ($usosCliente >= $cupom->limite_uso_cliente) {
                return ['valido' => false, 'erro' => 'Limite de uso por cliente excedido'];
            }
        }

        // Verificar nível mínimo do cliente
        if ($cupom->nivel_minimo_cliente) {
            $carteira = FidelidadeCarteira::where('cliente_id', $clienteId)->first();
            if (!$carteira || $carteira->nivel_atual < $cupom->nivel_minimo_cliente) {
                return ['valido' => false, 'erro' => 'Nível mínimo não atingido'];
            }
        }

        return ['valido' => true];
    }

    private function calcularDesconto($cupom, $valorCompra)
    {
        if ($cupom->tipo_desconto === 'percentual') {
            return ($valorCompra * $cupom->valor_desconto) / 100;
        }

        return min($cupom->valor_desconto, $valorCompra);
    }
}
