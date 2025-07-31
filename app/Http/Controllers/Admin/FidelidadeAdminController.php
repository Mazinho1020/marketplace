<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fidelidade\FidelidadeCarteira;
use App\Models\Fidelidade\FidelidadeCashbackTransacao;
use App\Models\Fidelidade\FidelidadeCupom;
use App\Models\Fidelidade\FidelidadeCupomUso;
use App\Models\Fidelidade\FidelidadeCredito;
use App\Models\Fidelidade\FidelidadeConquista;
use App\Models\Fidelidade\FidelidadeClienteConquista;
use App\Models\Fidelidade\FidelidadeCashbackRegra;
use App\Models\Fidelidade\FichaTecnicaCategoria;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FidelidadeAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $estatisticas = [
            'carteiras_total' => FidelidadeCarteira::count(),
            'carteiras_ativas' => FidelidadeCarteira::where('status', 'ativa')->count(),
            'carteiras_deletadas' => FidelidadeCarteira::onlyTrashed()->count(),
            'cupons_total' => FidelidadeCupom::count(),
            'cupons_ativos' => FidelidadeCupom::where('status', 'ativo')->count(),
            'cupons_deletados' => FidelidadeCupom::onlyTrashed()->count(),
            'creditos_total' => FidelidadeCredito::count(),
            'creditos_ativos' => FidelidadeCredito::where('status', 'ativo')->count(),
            'creditos_deletados' => FidelidadeCredito::onlyTrashed()->count(),
            'transacoes_total' => FidelidadeCashbackTransacao::count(),
            'transacoes_deletadas' => FidelidadeCashbackTransacao::onlyTrashed()->count(),
        ];

        return view('admin.fidelidade.index', compact('estatisticas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.fidelidade.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Implementar store conforme necessidade
        return redirect()->route('admin.fidelidade.index')->with('success', 'Criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Implementar show conforme necessidade
        return view('admin.fidelidade.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Implementar edit conforme necessidade
        return view('admin.fidelidade.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Implementar update conforme necessidade
        return redirect()->route('admin.fidelidade.index')->with('success', 'Atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Implementar soft delete conforme necessidade
        return redirect()->route('admin.fidelidade.index')->with('success', 'Deletado com sucesso!');
    }

    /**
     * Listar registros deletados
     */
    public function deletados(Request $request)
    {
        $tipo = $request->get('tipo', 'carteiras');

        $dados = [];

        switch ($tipo) {
            case 'carteiras':
                $dados = FidelidadeCarteira::onlyTrashed()->paginate(15);
                break;
            case 'cupons':
                $dados = FidelidadeCupom::onlyTrashed()->paginate(15);
                break;
            case 'creditos':
                $dados = FidelidadeCredito::onlyTrashed()->paginate(15);
                break;
            case 'conquistas':
                $dados = FidelidadeConquista::onlyTrashed()->paginate(15);
                break;
            case 'transacoes':
                $dados = FidelidadeCashbackTransacao::onlyTrashed()->paginate(15);
                break;
            case 'regras':
                $dados = FidelidadeCashbackRegra::onlyTrashed()->paginate(15);
                break;
            case 'categorias':
                $dados = FichaTecnicaCategoria::onlyTrashed()->paginate(15);
                break;
        }

        return view('admin.fidelidade.deletados', compact('dados', 'tipo'));
    }

    /**
     * Restaurar registro deletado
     */
    public function restaurar(Request $request)
    {
        $tipo = $request->get('tipo');
        $id = $request->get('id');

        $restored = false;
        $message = 'Registro não encontrado';

        switch ($tipo) {
            case 'carteira':
                $model = FidelidadeCarteira::withTrashed()->find($id);
                if ($model) {
                    $model->restore();
                    $restored = true;
                    $message = 'Carteira restaurada com sucesso';
                }
                break;
            case 'cupom':
                $model = FidelidadeCupom::withTrashed()->find($id);
                if ($model) {
                    $model->restore();
                    $restored = true;
                    $message = 'Cupom restaurado com sucesso';
                }
                break;
            case 'credito':
                $model = FidelidadeCredito::withTrashed()->find($id);
                if ($model) {
                    $model->restore();
                    $restored = true;
                    $message = 'Crédito restaurado com sucesso';
                }
                break;
            case 'conquista':
                $model = FidelidadeConquista::withTrashed()->find($id);
                if ($model) {
                    $model->restore();
                    $restored = true;
                    $message = 'Conquista restaurada com sucesso';
                }
                break;
            case 'transacao':
                $model = FidelidadeCashbackTransacao::withTrashed()->find($id);
                if ($model) {
                    $model->restore();
                    $restored = true;
                    $message = 'Transação restaurada com sucesso';
                }
                break;
        }

        return response()->json([
            'success' => $restored,
            'message' => $message
        ]);
    }

    /**
     * Deletar permanentemente
     */
    public function deletarPermanente(Request $request)
    {
        $tipo = $request->get('tipo');
        $id = $request->get('id');

        $deleted = false;
        $message = 'Registro não encontrado';

        switch ($tipo) {
            case 'carteira':
                $model = FidelidadeCarteira::withTrashed()->find($id);
                if ($model) {
                    $model->forceDelete();
                    $deleted = true;
                    $message = 'Carteira deletada permanentemente';
                }
                break;
            case 'cupom':
                $model = FidelidadeCupom::withTrashed()->find($id);
                if ($model) {
                    $model->forceDelete();
                    $deleted = true;
                    $message = 'Cupom deletado permanentemente';
                }
                break;
            case 'credito':
                $model = FidelidadeCredito::withTrashed()->find($id);
                if ($model) {
                    $model->forceDelete();
                    $deleted = true;
                    $message = 'Crédito deletado permanentemente';
                }
                break;
            case 'conquista':
                $model = FidelidadeConquista::withTrashed()->find($id);
                if ($model) {
                    $model->forceDelete();
                    $deleted = true;
                    $message = 'Conquista deletada permanentemente';
                }
                break;
            case 'transacao':
                $model = FidelidadeCashbackTransacao::withTrashed()->find($id);
                if ($model) {
                    $model->forceDelete();
                    $deleted = true;
                    $message = 'Transação deletada permanentemente';
                }
                break;
        }

        return response()->json([
            'success' => $deleted,
            'message' => $message
        ]);
    }
}
