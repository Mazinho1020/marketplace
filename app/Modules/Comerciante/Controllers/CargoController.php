<?php

namespace App\Modules\Comerciante\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CargoController extends Controller
{
    /**
     * Lista cargos
     */
    public function index(Request $request)
    {
        $empresaId = $request->get('empresa_id', Auth::user()->empresa_id ?? 2);

        $query = DB::table('pessoas_cargos as c')
            ->leftJoin('pessoas_departamentos as d', 'c.departamento_id', '=', 'd.id')
            ->where('c.empresa_id', $empresaId)
            ->select(
                'c.*',
                'd.nome as departamento_nome'
            );

        // Filtros
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('c.nome', 'like', "%{$busca}%")
                    ->orWhere('c.codigo', 'like', "%{$busca}%")
                    ->orWhere('c.descricao', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('departamento_id')) {
            $query->where('c.departamento_id', $request->departamento_id);
        }

        if ($request->filled('ativo')) {
            $query->where('c.ativo', $request->ativo);
        }

        $cargos = $query->orderBy('d.nome')->orderBy('c.nome')->paginate(15);

        // Departamentos para filtro
        $departamentos = DB::table('pessoas_departamentos')
            ->where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        // Estatísticas
        $stats = [
            'total' => DB::table('pessoas_cargos')->where('empresa_id', $empresaId)->count(),
            'ativos' => DB::table('pessoas_cargos')->where('empresa_id', $empresaId)->where('ativo', true)->count(),
            'inativos' => DB::table('pessoas_cargos')->where('empresa_id', $empresaId)->where('ativo', false)->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $cargos,
                'stats' => $stats
            ]);
        }

        return view('comerciantes.cargos.index', compact('cargos', 'departamentos', 'stats', 'empresaId'));
    }

    /**
     * Mostra formulário de criação
     */
    public function create(Request $request)
    {
        $empresaId = $request->get('empresa_id', Auth::user()->empresa_id ?? 2);

        $departamentos = DB::table('pessoas_departamentos')
            ->where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        // Estatísticas para o sidebar
        $stats = [
            'total' => DB::table('pessoas_cargos')->where('empresa_id', $empresaId)->count(),
            'ativos' => DB::table('pessoas_cargos')->where('empresa_id', $empresaId)->where('ativo', true)->count(),
        ];

        return view('comerciantes.cargos.create', compact('empresaId', 'departamentos', 'stats'));
    }

    /**
     * Armazena novo cargo
     */
    public function store(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|integer',
            'codigo' => 'nullable|string|max:20',
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string|max:500',
            'departamento_id' => 'nullable|exists:pessoas_departamentos,id',
            'nivel_hierarquico' => 'nullable|integer|min:1|max:5',
            'salario_base' => 'nullable|numeric|min:0|max:999999.99',
            'ativo' => 'nullable|boolean'
        ]);

        try {
            DB::beginTransaction();

            $dados = [
                'empresa_id' => $request->empresa_id,
                'codigo' => $request->codigo,
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'departamento_id' => $request->departamento_id,
                'nivel_hierarquico' => $request->nivel_hierarquico,
                'salario_base' => $request->salario_base,
                'ativo' => $request->has('ativo') ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
                'sync_status' => 'pendente',
                'sync_data' => now()
            ];

            $cargoId = DB::table('pessoas_cargos')->insertGetId($dados);

            DB::commit();

            Log::info("Cargo criado com sucesso", [
                'cargo_id' => $cargoId,
                'cargo_nome' => $dados['nome'],
                'empresa_id' => $request->empresa_id
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cargo criado com sucesso!',
                    'data' => $cargoId,
                    'redirect' => url("/comerciantes/clientes/cargos/{$cargoId}")
                ]);
            }

            return redirect("/comerciantes/clientes/cargos/{$cargoId}")
                ->with('success', 'Cargo criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Erro ao criar cargo", [
                'dados' => $request->all(),
                'erro' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar cargo: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Erro ao criar cargo: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Mostra detalhes do cargo
     */
    public function show(Request $request, $id)
    {
        try {
            $cargo = DB::table('pessoas_cargos as c')
                ->leftJoin('pessoas_departamentos as d', 'c.departamento_id', '=', 'd.id')
                ->where('c.id', $id)
                ->select(
                    'c.*',
                    'd.nome as departamento_nome',
                    'd.codigo as departamento_codigo'
                )
                ->first();

            if (!$cargo) {
                Log::warning("Cargo não encontrado", ['id' => $id]);
                abort(404, 'Cargo não encontrado');
            }

            // Funcionários com este cargo - buscar na tabela pessoas
            $funcionarios = DB::table('pessoas as p')
                ->leftJoin('pessoas_departamentos as d', 'p.departamento_id', '=', 'd.id')
                ->where('p.empresa_id', $cargo->empresa_id)
                ->where('p.cargo_id', $id)
                ->whereIn('p.tipo', ['funcionario', 'pessoa_funcionario'])
                ->select(
                    'p.id',
                    'p.nome',
                    'p.sobrenome',
                    'p.email',
                    'p.status',
                    'p.data_admissao',
                    'p.salario_atual',
                    'd.nome as departamento_nome'
                )
                ->orderBy('p.nome')
                ->get();

            $stats = [
                'funcionarios' => $funcionarios->count(),
                'funcionarios_ativos' => $funcionarios->where('status', 'ativo')->count(),
                'salario_medio' => $funcionarios->where('salario_atual', '>', 0)->avg('salario_atual'),
            ];

            Log::info("Visualizando cargo", [
                'cargo_id' => $id,
                'cargo_nome' => $cargo->nome,
                'funcionarios_vinculados' => $stats['funcionarios']
            ]);

            return view('comerciantes.cargos.show', compact('cargo', 'funcionarios', 'stats'));
        } catch (\Exception $e) {
            Log::error("Erro ao visualizar cargo", [
                'cargo_id' => $id,
                'erro' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Erro ao carregar dados do cargo: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostra formulário de edição
     */
    public function edit(Request $request, $id)
    {
        try {
            $cargo = DB::table('pessoas_cargos')->where('id', $id)->first();

            if (!$cargo) {
                Log::warning("Cargo não encontrado para edição", ['id' => $id]);
                abort(404, 'Cargo não encontrado');
            }

            $departamentos = DB::table('pessoas_departamentos')
                ->where('empresa_id', $cargo->empresa_id)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get();

            // Estatísticas para o cargo
            $funcionarios = DB::table('pessoas')
                ->where('empresa_id', $cargo->empresa_id)
                ->where('cargo_id', $id)
                ->whereIn('tipo', ['funcionario', 'pessoa_funcionario']);

            $stats = [
                'funcionarios' => $funcionarios->count(),
                'funcionarios_ativos' => $funcionarios->where('status', 'ativo')->count(),
            ];

            Log::info("Editando cargo", [
                'cargo_id' => $id,
                'cargo_nome' => $cargo->nome,
                'funcionarios_vinculados' => $stats['funcionarios']
            ]);

            return view('comerciantes.cargos.edit', compact('cargo', 'departamentos', 'stats'));
        } catch (\Exception $e) {
            Log::error("Erro ao carregar formulário de edição", [
                'cargo_id' => $id,
                'erro' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Erro ao carregar formulário: ' . $e->getMessage()]);
        }
    }

    /**
     * Atualiza cargo
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'codigo' => 'nullable|string|max:20',
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string|max:500',
            'departamento_id' => 'nullable|exists:pessoas_departamentos,id',
            'nivel_hierarquico' => 'nullable|integer|min:1|max:5',
            'salario_base' => 'nullable|numeric|min:0|max:999999.99',
            'ativo' => 'nullable|boolean'
        ]);

        try {
            $cargo = DB::table('pessoas_cargos')->where('id', $id)->first();
            if (!$cargo) {
                throw new \Exception('Cargo não encontrado');
            }

            DB::beginTransaction();

            $dados = [
                'codigo' => $request->codigo,
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'departamento_id' => $request->departamento_id,
                'nivel_hierarquico' => $request->nivel_hierarquico,
                'salario_base' => $request->salario_base,
                'ativo' => $request->has('ativo') ? 1 : 0,
                'updated_at' => now(),
                'sync_status' => 'pendente',
                'sync_data' => now()
            ];

            DB::table('pessoas_cargos')->where('id', $id)->update($dados);

            DB::commit();

            Log::info("Cargo atualizado com sucesso", [
                'cargo_id' => $id,
                'cargo_nome' => $dados['nome'],
                'alteracoes' => $dados
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cargo atualizado com sucesso!',
                    'redirect' => url("/comerciantes/clientes/cargos/{$id}")
                ]);
            }

            return redirect("/comerciantes/clientes/cargos/{$id}")
                ->with('success', 'Cargo atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Erro ao atualizar cargo", [
                'cargo_id' => $id,
                'dados' => $request->all(),
                'erro' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar cargo: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Erro ao atualizar cargo: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove cargo
     */
    public function destroy(Request $request, $id)
    {
        try {
            $cargo = DB::table('pessoas_cargos')->where('id', $id)->first();

            if (!$cargo) {
                throw new \Exception('Cargo não encontrado');
            }

            // Verificar se há funcionários vinculados
            $funcionarios = DB::table('pessoas')
                ->where('cargo_id', $id)
                ->whereIn('tipo', ['funcionario', 'pessoa_funcionario'])
                ->count();

            if ($funcionarios > 0) {
                throw new \Exception("Não é possível excluir cargo com {$funcionarios} funcionário(s) vinculado(s)");
            }

            DB::beginTransaction();

            DB::table('pessoas_cargos')->where('id', $id)->delete();

            DB::commit();

            Log::info("Cargo excluído com sucesso", [
                'cargo_id' => $id,
                'cargo_nome' => $cargo->nome,
                'empresa_id' => $cargo->empresa_id
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cargo excluído com sucesso!',
                    'redirect' => url("/comerciantes/clientes/cargos?empresa_id={$cargo->empresa_id}")
                ]);
            }

            return redirect("/comerciantes/clientes/cargos?empresa_id={$cargo->empresa_id}")
                ->with('success', 'Cargo excluído com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Erro ao excluir cargo", [
                'cargo_id' => $id,
                'erro' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
