<?php

namespace App\Modules\Comerciante\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DepartamentoController extends Controller
{
    /**
     * Lista departamentos
     */
    public function index(Request $request)
    {
        $empresaId = $request->get('empresa_id', Auth::user()->empresa_id ?? 2);

        $query = DB::table('pessoas_departamentos')
            ->where('empresa_id', $empresaId);

        // Filtros
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                    ->orWhere('codigo', 'like', "%{$busca}%")
                    ->orWhere('descricao', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        $departamentos = $query->orderBy('nome')->paginate(15);

        // Estatísticas
        $stats = [
            'total' => DB::table('pessoas_departamentos')->where('empresa_id', $empresaId)->count(),
            'ativos' => DB::table('pessoas_departamentos')->where('empresa_id', $empresaId)->where('ativo', true)->count(),
            'inativos' => DB::table('pessoas_departamentos')->where('empresa_id', $empresaId)->where('ativo', false)->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $departamentos,
                'stats' => $stats
            ]);
        }

        return view('comerciantes.departamentos.index', compact('departamentos', 'stats', 'empresaId'));
    }

    /**
     * Mostra formulário de criação
     */
    public function create(Request $request)
    {
        $empresaId = $request->get('empresa_id', Auth::user()->empresa_id ?? 2);

        return view('comerciantes.departamentos.create', compact('empresaId'));
    }

    /**
     * Armazena novo departamento
     */
    public function store(Request $request)
    {
        // Debug dos dados recebidos
        Log::info('Dados recebidos no store:', $request->all());

        $request->validate([
            'empresa_id' => 'required|integer',
            'codigo' => 'nullable|string|max:20',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'responsavel_id' => 'nullable|integer',
            'responsavel_nome' => 'nullable|string|max:255',
            'responsavel_email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'centro_custo' => 'nullable|string|max:50',
            'localizacao' => 'nullable|string|max:255',
            'relacionado_producao' => 'boolean',
            'ativo' => 'boolean',
            'ordem' => 'integer|min:0'
        ]);

        try {
            // Apenas os campos que existem na tabela
            $dados = [
                'empresa_id' => $request->empresa_id,
                'codigo' => $request->codigo,
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'responsavel_id' => $request->responsavel_id,
                'centro_custo' => $request->centro_custo,
                'relacionado_producao' => $request->has('relacionado_producao') ? (bool)$request->relacionado_producao : false,
                'ativo' => $request->has('ativo') ? (bool)$request->ativo : true,
                'ordem' => $request->has('ordem') ? (int)$request->ordem : 0,
                'created_at' => now(),
                'updated_at' => now(),
                'sync_status' => 'pendente',
                'sync_data' => now()
            ];

            Log::info('Dados para inserção:', $dados);

            $departamento = DB::table('pessoas_departamentos')->insertGetId($dados);

            Log::info('Departamento criado com ID:', ['id' => $departamento]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Departamento criado com sucesso!',
                    'data' => $departamento
                ]);
            }

            return redirect("/comerciantes/clientes/departamentos?empresa_id=" . $request->empresa_id)
                ->with('success', 'Departamento criado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar departamento:', ['error' => $e->getMessage(), 'line' => $e->getLine()]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar departamento: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Erro ao criar departamento: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Mostra detalhes do departamento
     */
    public function show(Request $request, $id)
    {
        $departamento = DB::table('pessoas_departamentos')->where('id', $id)->first();

        if (!$departamento) {
            abort(404, 'Departamento não encontrado');
        }

        // Funcionários do departamento
        $funcionarios = DB::table('pessoas')
            ->where('empresa_id', $departamento->empresa_id)
            ->where('departamento_id', $id)
            ->where('tipo', 'like', '%funcionario%')
            ->select('id', 'nome', 'sobrenome', 'email', 'status')
            ->get();

        // Cargos do departamento
        $cargos = DB::table('pessoas_cargos')
            ->where('departamento_id', $id)
            ->select('id', 'nome', 'descricao', 'ativo')
            ->get();

        $stats = [
            'funcionarios' => $funcionarios->count(),
            'funcionarios_ativos' => $funcionarios->where('status', 'ativo')->count(),
            'cargos' => $cargos->count(),
            'cargos_ativos' => $cargos->where('ativo', true)->count()
        ];

        return view('comerciantes.departamentos.show', compact('departamento', 'funcionarios', 'cargos', 'stats'));
    }

    /**
     * Mostra formulário de edição
     */
    public function edit(Request $request, $id)
    {
        $departamento = DB::table('pessoas_departamentos')->where('id', $id)->first();

        if (!$departamento) {
            abort(404, 'Departamento não encontrado');
        }

        // Informações sobre vínculos para alertas
        $vinculosInfo = [
            'funcionarios' => DB::table('pessoas')
                ->where('departamento_id', $id)
                ->count(),
            'cargos' => DB::table('pessoas_cargos')
                ->where('departamento_id', $id)
                ->count()
        ];

        return view('comerciantes.departamentos.edit', compact('departamento', 'vinculosInfo'));
    }

    /**
     * Atualiza departamento
     */
    public function update(Request $request, $id)
    {
        Log::info('Dados recebidos no update:', $request->all());

        $request->validate([
            'codigo' => 'nullable|string|max:20',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'centro_custo' => 'nullable|string|max:50',
            'relacionado_producao' => 'boolean',
            'ativo' => 'boolean',
            'ordem' => 'integer|min:0'
        ]);

        try {
            $departamento = DB::table('pessoas_departamentos')->where('id', $id)->first();

            if (!$departamento) {
                throw new \Exception('Departamento não encontrado');
            }

            // Apenas os campos que existem na tabela
            $dados = [
                'codigo' => $request->codigo,
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'centro_custo' => $request->centro_custo,
                'relacionado_producao' => $request->has('relacionado_producao') ? (bool)$request->relacionado_producao : false,
                'ativo' => $request->has('ativo') ? (bool)$request->ativo : false,
                'ordem' => $request->has('ordem') ? (int)$request->ordem : 0,
                'updated_at' => now(),
                'sync_status' => 'pendente',
                'sync_data' => now()
            ];

            Log::info('Dados para atualização:', $dados);

            DB::table('pessoas_departamentos')->where('id', $id)->update($dados);

            Log::info('Departamento atualizado com sucesso:', ['id' => $id]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Departamento atualizado com sucesso!'
                ]);
            }

            return redirect("/comerciantes/clientes/departamentos/{$id}")
                ->with('success', 'Departamento atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar departamento:', ['error' => $e->getMessage(), 'line' => $e->getLine()]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar departamento: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Erro ao atualizar departamento: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove departamento
     */
    public function destroy(Request $request, $id)
    {
        try {
            $departamento = DB::table('pessoas_departamentos')->where('id', $id)->first();

            if (!$departamento) {
                throw new \Exception('Departamento não encontrado');
            }

            // Verificar se há funcionários vinculados
            $funcionarios = DB::table('pessoas')
                ->where('departamento_id', $id)
                ->count();

            if ($funcionarios > 0) {
                throw new \Exception('Não é possível excluir departamento com funcionários vinculados');
            }

            // Verificar se há cargos vinculados
            $cargos = DB::table('pessoas_cargos')
                ->where('departamento_id', $id)
                ->count();

            if ($cargos > 0) {
                throw new \Exception('Não é possível excluir departamento com cargos vinculados');
            }

            DB::table('pessoas_departamentos')->where('id', $id)->delete();

            Log::info('Departamento excluído com sucesso:', ['id' => $id]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Departamento excluído com sucesso!'
                ]);
            }

            return redirect("/comerciantes/clientes/departamentos?empresa_id=" . $departamento->empresa_id)
                ->with('success', 'Departamento excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir departamento:', ['error' => $e->getMessage(), 'id' => $id]);

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
