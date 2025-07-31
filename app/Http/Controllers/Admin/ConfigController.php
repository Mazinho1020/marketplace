<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('config_definitions as cd')
            ->leftJoin('config_groups as cg', 'cd.grupo_id', '=', 'cg.id')
            ->leftJoin('config_values as cv', 'cd.id', '=', 'cv.config_id')
            ->select([
                'cd.*',
                'cg.nome as grupo_nome',
                'cg.icone_class as grupo_icone',
                'cv.valor',
                'cv.site_id',
                'cv.ambiente_id'
            ]);

        // Filtros
        $searchFilter = $request->get('search', '');
        $groupFilter = $request->get('group', '');
        $siteFilter = $request->get('site', '');
        $environmentFilter = $request->get('environment', '');
        $typeFilter = $request->get('type', '');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($searchFilter) {
                $q->where('cd.nome', 'like', "%{$searchFilter}%")
                    ->orWhere('cd.chave', 'like', "%{$searchFilter}%")
                    ->orWhere('cd.descricao', 'like', "%{$searchFilter}%");
            });
        }

        if ($request->filled('group')) {
            $query->where('cd.grupo_id', $groupFilter);
        }

        if ($request->filled('site')) {
            $query->where('cv.site_id', $siteFilter);
        }

        if ($request->filled('environment')) {
            $query->where('cv.ambiente_id', $environmentFilter);
        }

        if ($request->filled('type')) {
            $query->where('cd.tipo', $typeFilter);
        }

        $configs = $query->orderBy('cg.ordem', 'asc')
            ->orderBy('cd.ordem', 'asc')
            ->paginate(15);

        // Buscar grupos para filtro (admin vê todas as empresas)
        $groups = DB::table('config_groups')
            ->where('ativo', true)
            ->orderBy('ordem')
            ->get();

        // Buscar sites e ambientes para filtro (admin vê todas as empresas)
        $sites = DB::table('config_sites')
            ->where('ativo', true)
            ->get();

        $environments = DB::table('config_environments')
            ->where('ativo', true)
            ->get();

        return view('admin.config.index', compact('configs', 'groups', 'sites', 'environments', 'searchFilter', 'groupFilter', 'siteFilter', 'environmentFilter', 'typeFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Admin vê todas as empresas
        $groups = DB::table('config_groups')
            ->where('ativo', true)
            ->orderBy('ordem')
            ->get();

        $sites = DB::table('config_sites')
            ->where('ativo', true)
            ->get();

        $ambientes = DB::table('config_environments')
            ->where('ativo', true)
            ->get();

        return view('admin.config.create', compact('groups', 'sites', 'ambientes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'chave' => 'required|string|max:100',
            'nome' => 'required|string|max:100',
            'tipo' => 'required|in:string,integer,float,boolean,array,json,url,email,password',
            'grupo_id' => 'nullable|exists:config_groups,id',
            'empresa_id' => 'required|integer' // Admin deve especificar a empresa
        ]);

        try {
            DB::beginTransaction();

            // Criar definição
            $configId = DB::table('config_definitions')->insertGetId([
                'empresa_id' => $request->empresa_id, // Usar empresa_id do formulário
                'chave' => $request->chave,
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'tipo' => $request->tipo,
                'grupo_id' => $request->grupo_id,
                'valor_padrao' => $request->valor_padrao,
                'obrigatorio' => $request->boolean('obrigatorio'),
                'ordem' => $request->ordem ?? 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Criar valor se fornecido
            if ($request->filled('valor')) {
                DB::table('config_values')->insert([
                    'empresa_id' => $request->empresa_id, // Usar empresa_id do formulário
                    'config_id' => $configId,
                    'site_id' => $request->site_id,
                    'ambiente_id' => $request->ambiente_id,
                    'valor' => $request->valor,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('admin.config.index')
                ->with('success', 'Configuração criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Erro ao criar configuração: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('admin.config.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Admin vê todas as empresas - remove filtro de empresa_id
        $config = DB::table('config_definitions')
            ->where('id', $id)
            ->first();

        if (!$config) {
            abort(404);
        }

        // Admin vê todas as empresas
        $groups = DB::table('config_groups')
            ->where('ativo', true)
            ->orderBy('ordem')
            ->get();

        $sites = DB::table('config_sites')
            ->where('ativo', true)
            ->get();

        $ambientes = DB::table('config_environments')
            ->where('ativo', true)
            ->get();

        $valor = DB::table('config_values')
            ->where('config_id', $id)
            ->first();

        return view('admin.config.edit', compact('config', 'groups', 'sites', 'ambientes', 'valor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'chave' => 'required|string|max:100',
            'nome' => 'required|string|max:100',
            'tipo' => 'required|in:string,integer,float,boolean,array,json,url,email,password',
            'grupo_id' => 'nullable|exists:config_groups,id'
        ]);

        try {
            DB::beginTransaction();

            // Admin pode atualizar qualquer configuração - remove filtro empresa_id
            DB::table('config_definitions')
                ->where('id', $id)
                ->update([
                    'chave' => $request->chave,
                    'nome' => $request->nome,
                    'descricao' => $request->descricao,
                    'tipo' => $request->tipo,
                    'grupo_id' => $request->grupo_id,
                    'valor_padrao' => $request->valor_padrao,
                    'obrigatorio' => $request->boolean('obrigatorio'),
                    'ordem' => $request->ordem ?? 0,
                    'updated_at' => now()
                ]);

            // Atualizar ou criar valor
            if ($request->filled('valor')) {
                // Buscar empresa_id da configuração original
                $config = DB::table('config_definitions')->where('id', $id)->first();

                DB::table('config_values')
                    ->updateOrInsert(
                        [
                            'config_id' => $id,
                            'site_id' => $request->site_id,
                            'ambiente_id' => $request->ambiente_id
                        ],
                        [
                            'empresa_id' => $config->empresa_id, // Usar empresa_id da configuração
                            'valor' => $request->valor,
                            'updated_at' => now()
                        ]
                    );
            }

            DB::commit();

            return redirect()->route('admin.config.index')
                ->with('success', 'Configuração atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Erro ao atualizar configuração: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            // Remover valores
            DB::table('config_values')
                ->where('config_id', $id)
                ->delete();

            // Admin pode remover qualquer configuração - remove filtro empresa_id
            DB::table('config_definitions')
                ->where('id', $id)
                ->delete();

            DB::commit();

            return redirect()->route('admin.config.index')
                ->with('success', 'Configuração removida com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erro ao remover configuração: ' . $e->getMessage());
        }
    }

    /**
     * Mostra histórico de alterações
     */
    public function history(string $id)
    {
        // Admin vê histórico de qualquer empresa
        $config = DB::table('config_definitions')
            ->where('id', $id)
            ->first();

        if (!$config) {
            abort(404);
        }

        // Admin vê todos os sites
        $sites = DB::table('config_sites')
            ->where('ativo', true)
            ->get();

        $environments = DB::table('config_environments')
            ->where('ativo', true)
            ->get();

        $history = DB::table('config_history as ch')
            ->leftJoin('config_sites as cs', 'ch.site_id', '=', 'cs.id')
            ->leftJoin('config_environments as ce', 'ch.ambiente_id', '=', 'ce.id')
            ->select(
                'ch.*',
                'cs.nome as site_nome',
                'cs.codigo as site_codigo',
                'ce.nome as ambiente_nome',
                'ce.codigo as ambiente_codigo'
            )
            ->where('ch.config_id', $id)
            ->orderBy('ch.created_at', 'desc')
            ->paginate(20);

        return view('admin.config.history', compact('config', 'sites', 'environments', 'history'));
    }

    /**
     * Retorna detalhes de um registro específico do histórico
     */
    public function historyDetail(string $configId, string $historyId)
    {
        $config = DB::table('config_definitions')
            ->where('id', $configId)
            ->first();

        if (!$config) {
            abort(404);
        }

        $historyEntry = DB::table('config_history')
            ->where('id', $historyId)
            ->where('config_id', $configId)
            ->first();

        if (!$historyEntry) {
            abort(404);
        }

        return response()->json([
            'success' => true,
            'history' => $historyEntry,
            'config' => $config
        ]);
    }

    /**
     * Restaura um valor do histórico
     */
    public function restoreValue(Request $request, string $configId)
    {
        $request->validate([
            'history_id' => 'required|integer|exists:config_history,id'
        ]);

        $config = DB::table('config_definitions')
            ->where('id', $configId)
            ->first();

        if (!$config) {
            abort(404);
        }

        $historyEntry = DB::table('config_history')
            ->where('id', $request->history_id)
            ->where('config_id', $configId)
            ->first();

        if (!$historyEntry) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de histórico não encontrado'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Atualizar ou inserir o valor atual
            DB::table('config_values')->updateOrInsert(
                [
                    'config_id' => $configId,
                    'empresa_id' => $config->empresa_id
                ],
                [
                    'valor' => $historyEntry->valor_novo,
                    'updated_at' => now()
                ]
            );

            // Registrar a restauração no histórico
            DB::table('config_history')->insert([
                'config_id' => $configId,
                'empresa_id' => $config->empresa_id,
                'acao' => 'restored',
                'valor_anterior' => $historyEntry->valor_anterior,
                'valor_novo' => $historyEntry->valor_novo,
                'usuario_id' => 1, // auth()->id() quando implementar autenticação
                'usuario_nome' => 'Sistema',
                'observacoes' => "Valor restaurado do histórico #{$request->history_id}",
                'created_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Valor restaurado com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao restaurar valor: ' . $e->getMessage()
            ], 500);
        }
    }
}
