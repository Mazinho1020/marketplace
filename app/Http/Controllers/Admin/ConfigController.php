<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Config\ConfigDefinition;
use App\Models\Config\ConfigGroup;
use App\Models\Config\ConfigValue;
use App\Models\Config\ConfigSite;
use App\Models\Config\ConfigHistory;
use App\Services\ConfigService;
use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConfigController extends Controller
{
    protected $configService;
    protected $empresaId;

    public function __construct()
    {
        // Configuração será feita em cada método conforme necessário
        $this->empresaId = session('empresa_id', 1); // Default empresa ID
    }

    /**
     * Initialize ConfigService with current context
     */
    protected function initConfigService($siteId = null)
    {
        $siteId = $siteId ?? session('site_id');

        return new ConfigService(
            $this->empresaId,
            $siteId,
            Auth::user()->id ?? null
        );
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Buscar grupos ativos
        $grupos = ConfigGroup::where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->orderBy('ordem')
            ->with(['definicoes' => function ($query) {
                $query->where('ativo', true)->orderBy('ordem');
            }])
            ->get();

        // Buscar sites para filtros
        $sites = ConfigSite::where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        // Filtros
        $filtros = [
            'search' => $request->get('search', ''),
            'group' => $request->get('group', ''),
            'site' => $request->get('site', ''),
            'environment' => $request->get('environment', ''),
            'type' => $request->get('type', ''),
        ];

        // Aplicar filtros se necessário
        if ($request->hasAny(['search', 'group', 'site', 'environment', 'type'])) {
            $grupos = $this->aplicarFiltros($grupos, $filtros);
        }

        // Buscar todas as configurações atuais para exibição nos formulários
        $configService = $this->initConfigService();
        $configs = [];
        $configsByGroup = [];

        // Carregar valores atuais para todas as definições
        foreach ($grupos as $grupo) {
            $groupConfigs = [];
            foreach ($grupo->definicoes as $definicao) {
                $valorAtual = $configService->get($definicao->chave);
                $configs[$definicao->chave] = $valorAtual;

                // Criar estrutura para o grupo
                $groupConfigs[] = (object) [
                    'id' => $definicao->id,
                    'chave' => $definicao->chave,
                    'nome' => $definicao->nome,
                    'tipo' => $definicao->tipo,
                    'valor' => $valorAtual,
                    'descricao' => $definicao->descricao,
                    'valor_padrao' => $definicao->valor_padrao,
                    'obrigatorio' => $definicao->obrigatorio,
                    'avancado' => $definicao->avancado,
                    'opcoes' => $definicao->opcoes,
                    'editavel' => $definicao->editavel ?? true,
                    'dica' => $definicao->dica,
                    'grupo_nome' => $grupo->nome,
                    'grupo_icone' => $grupo->icone_class ?? 'fas fa-cog'
                ];
            }

            if (!empty($groupConfigs)) {
                $configsByGroup[$grupo->nome] = $groupConfigs;
            }
        }

        return view('admin.config.index', compact('grupos', 'sites', 'filtros', 'configs', 'configsByGroup'))
            ->with([
                'groupFilter' => $filtros['group'],
                'siteFilter' => $filtros['site'],
                'searchFilter' => $filtros['search'],
                'typeFilter' => $filtros['type']
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $empresaId = $this->empresaId ?? 1;

            $grupos = ConfigGroup::where('empresa_id', $empresaId)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get();

            $sites = ConfigSite::where('empresa_id', $empresaId)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get();

            $tipos = [
                'string' => 'Texto',
                'text' => 'Texto Longo',
                'integer' => 'Número Inteiro',
                'float' => 'Número Decimal',
                'boolean' => 'Verdadeiro/Falso',
                'json' => 'JSON',
                'array' => 'Array',
                'email' => 'Email',
                'url' => 'URL',
                'date' => 'Data',
                'datetime' => 'Data e Hora',
                'password' => 'Senha'
            ];

            try {
                return view('admin.config.create', compact('grupos', 'sites', 'tipos'));
            } catch (\Exception $e) {
                // Fallback para view simplificada se layout admin falhar
                return view('admin.config.create_simple', compact('grupos', 'sites', 'tipos'));
            }
        } catch (\Exception $e) {
            // Log do erro e fallback total
            Log::error('Erro no ConfigController@create: ' . $e->getMessage());

            // Dados mínimos para fallback
            $grupos = collect([]);
            $sites = collect([]);
            $tipos = [
                'string' => 'Texto',
                'boolean' => 'Verdadeiro/Falso',
                'integer' => 'Número',
                'email' => 'Email'
            ];

            return view('admin.config.create_simple', compact('grupos', 'sites', 'tipos'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'grupo_id' => 'required|exists:config_groups,id',
            'nome' => 'required|string|max:255',
            'chave' => 'required|string|max:255|unique:config_definitions,chave,NULL,id,empresa_id,' . $this->empresaId,
            'tipo' => 'required|in:string,text,integer,float,boolean,json,array,email,url,date,datetime,password',
            'valor_padrao' => 'nullable|string',
            'descricao' => 'nullable|string',
            'obrigatorio' => 'boolean',
            'visivel' => 'boolean',
            'editavel' => 'boolean',
            'ordem' => 'integer|min:0',
            'opcoes' => 'nullable|string',
            'valor_inicial' => 'nullable|string',
            'site_id' => 'nullable|exists:config_sites,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Criar definição
            $definicao = ConfigDefinition::create([
                'empresa_id' => $this->empresaId,
                'grupo_id' => $request->grupo_id,
                'nome' => $request->nome,
                'chave' => $request->chave,
                'tipo' => $request->tipo,
                'valor_padrao' => $request->valor_padrao,
                'descricao' => $request->descricao,
                'obrigatorio' => $request->boolean('obrigatorio'),
                'visivel' => $request->boolean('visivel', true),
                'editavel' => $request->boolean('editavel', true),
                'ordem' => $request->integer('ordem', 0),
                'opcoes' => $request->opcoes,
                'ativo' => true
            ]);

            // Criar valor inicial se fornecido
            if ($request->filled('valor_inicial')) {
                $configService = $this->initConfigService($request->site_id);
                $configService->set(
                    $request->chave,
                    $request->valor_inicial
                );
            }

            DB::commit();

            return redirect()->route('admin.config.index')
                ->with('success', 'Configuração criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erro ao criar configuração: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ConfigDefinition $config)
    {
        $config->load(['grupo', 'valores.site', 'historico.usuario']);

        return view('admin.config.show', compact('config'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ConfigDefinition $config)
    {
        $grupos = ConfigGroup::where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        $sites = ConfigSite::where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        $tipos = [
            'string' => 'Texto',
            'text' => 'Texto Longo',
            'integer' => 'Número Inteiro',
            'float' => 'Número Decimal',
            'boolean' => 'Verdadeiro/Falso',
            'json' => 'JSON',
            'array' => 'Array',
            'email' => 'Email',
            'url' => 'URL',
            'date' => 'Data',
            'datetime' => 'Data e Hora',
            'password' => 'Senha'
        ];

        // Buscar valores atuais para diferentes contextos  
        $valores = ConfigValue::where('config_id', $config->id)
            ->where('empresa_id', $this->empresaId)
            ->with(['site'])
            ->get()
            ->keyBy(function ($valor) {
                return ($valor->site_id ?: '0') . '_0';
            });

        return view('admin.config.edit', compact('config', 'grupos', 'sites', 'tipos', 'valores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ConfigDefinition $config)
    {
        $validator = Validator::make($request->all(), [
            'grupo_id' => 'required|exists:config_groups,id',
            'nome' => 'required|string|max:255',
            'chave' => 'required|string|max:255|unique:config_definitions,chave,' . $config->id . ',id,empresa_id,' . $this->empresaId,
            'tipo' => 'required|in:string,text,integer,float,boolean,json,array,email,url,date,datetime,password',
            'valor_padrao' => 'nullable|string',
            'descricao' => 'nullable|string',
            'obrigatorio' => 'boolean',
            'visivel' => 'boolean',
            'editavel' => 'boolean',
            'ordem' => 'integer|min:0',
            'opcoes' => 'nullable|string',
            'ativo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $config->update([
                'grupo_id' => $request->grupo_id,
                'nome' => $request->nome,
                'chave' => $request->chave,
                'tipo' => $request->tipo,
                'valor_padrao' => $request->valor_padrao,
                'descricao' => $request->descricao,
                'obrigatorio' => $request->boolean('obrigatorio'),
                'visivel' => $request->boolean('visivel'),
                'editavel' => $request->boolean('editavel'),
                'ordem' => $request->integer('ordem'),
                'opcoes' => $request->opcoes,
                'ativo' => $request->boolean('ativo')
            ]);

            return redirect()->route('admin.config.index')
                ->with('success', 'Configuração atualizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar configuração: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConfigDefinition $config)
    {
        try {
            $config->delete();

            return redirect()->route('admin.config.index')
                ->with('success', 'Configuração removida com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao remover configuração: ' . $e->getMessage());
        }
    }

    /**
     * Update value for a specific configuration by key
     */
    public function setValue(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chave' => 'required|string',
            'valor' => 'nullable|string',
            'site_id' => 'nullable|exists:config_sites,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            // Buscar a definição da configuração
            $config = ConfigDefinition::where('empresa_id', $this->empresaId)
                ->where('chave', $request->chave)
                ->first();

            if (!$config) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuração não encontrada.'
                ], 404);
            }

            // Configurar contexto do serviço
            $configService = $this->initConfigService($request->site_id);

            $configService->set($request->chave, $request->valor);

            return response()->json([
                'success' => true,
                'message' => 'Valor atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar valor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update value for a specific configuration
     */
    public function updateValue(Request $request, ConfigDefinition $config)
    {
        $validator = Validator::make($request->all(), [
            'valor' => 'nullable|string',
            'site_id' => 'nullable|exists:config_sites,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            // Configurar contexto do serviço
            $configService = $this->initConfigService($request->site_id);

            $configService->set($config->chave, $request->valor);

            return response()->json([
                'success' => true,
                'message' => 'Valor atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar valor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current value for a configuration
     */
    public function getValue(Request $request, ConfigDefinition $config)
    {
        try {
            $configService = $this->initConfigService($request->site_id);
            $valor = $configService->get($config->chave);

            return response()->json([
                'success' => true,
                'valor' => $valor
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter valor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear configuration cache
     */
    public function clearCache(Request $request)
    {
        try {
            ConfigHelper::clearCache();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cache de configurações limpo com sucesso!'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Cache de configurações limpo com sucesso!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao limpar cache: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Erro ao limpar cache: ' . $e->getMessage());
        }
    }

    /**
     * Export configurations
     */
    public function export(Request $request)
    {
        try {
            $configs = ConfigDefinition::where('empresa_id', $this->empresaId)
                ->with(['grupo', 'valores'])
                ->get();

            $configService = $this->initConfigService();
            $export = [];
            foreach ($configs as $config) {
                $export[] = [
                    'grupo' => $config->grupo->codigo ?? null,
                    'chave' => $config->chave,
                    'valor' => $configService->get($config->chave),
                    'tipo' => $config->tipo,
                    'nome' => $config->nome,
                    'descricao' => $config->descricao
                ];
            }

            return response()->json($export)
                ->header('Content-Disposition', 'attachment; filename="configuracoes.json"');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao exportar configurações: ' . $e->getMessage());
        }
    }

    /**
     * Import configurations
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'arquivo' => 'required|file|mimes:json'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            $conteudo = file_get_contents($request->file('arquivo')->getRealPath());
            $configs = json_decode($conteudo, true);

            if (!is_array($configs)) {
                throw new \Exception('Formato de arquivo inválido');
            }

            DB::beginTransaction();
            $configService = $this->initConfigService();

            foreach ($configs as $configData) {
                if (!isset($configData['chave']) || !isset($configData['valor'])) {
                    continue;
                }

                $configService->set($configData['chave'], $configData['valor']);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Configurações importadas com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Erro ao importar configurações: ' . $e->getMessage());
        }
    }

    /**
     * View configuration history
     */
    public function history(ConfigDefinition $config)
    {
        $historico = ConfigHistory::where('config_id', $config->id)
            ->where('empresa_id', $this->empresaId)
            ->with(['usuario', 'site'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.config.history', compact('config', 'historico'));
    }

    /**
     * Apply filters to groups
     */
    protected function aplicarFiltros($grupos, $filtros)
    {
        return $grupos->filter(function ($grupo) use ($filtros) {
            // Filtrar por grupo
            if ($filtros['group'] && $grupo->codigo !== $filtros['group']) {
                return false;
            }

            // Filtrar definições dentro do grupo
            $grupo->definicoes = $grupo->definicoes->filter(function ($definicao) use ($filtros) {
                // Filtro de busca
                if ($filtros['search']) {
                    $search = strtolower($filtros['search']);
                    if (
                        strpos(strtolower($definicao->nome), $search) === false &&
                        strpos(strtolower($definicao->chave), $search) === false &&
                        strpos(strtolower($definicao->descricao ?? ''), $search) === false
                    ) {
                        return false;
                    }
                }

                // Filtro de tipo
                if ($filtros['type'] && $definicao->tipo !== $filtros['type']) {
                    return false;
                }

                return true;
            });

            // Retornar apenas grupos que têm definições após filtros
            return $grupo->definicoes->count() > 0;
        });
    }
}
