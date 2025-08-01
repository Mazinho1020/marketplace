<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Config\ConfigDefinition;
use App\Services\ConfigService;

class ConfigSimpleController extends Controller
{
    protected $configService;

    public function __construct()
    {
        // Removendo injeção de dependência temporariamente para debug
        // $this->configService = $configService;
    }

    public function index()
    {
        try {
            // Debug: Verificar se chegou aqui
            $debug = [
                'step1' => 'Controller chamado',
                'time' => now()->format('Y-m-d H:i:s')
            ];

            // Teste 1: Verificar se a classe existe
            if (!class_exists('App\Models\Config\ConfigDefinition')) {
                return view('admin.config.simple', [
                    'configs' => collect(),
                    'error' => 'Classe ConfigDefinition não encontrada',
                    'debug' => $debug
                ]);
            }

            $debug['step2'] = 'Classe ConfigDefinition encontrada';

            // Teste 2: Conexão básica
            $count = ConfigDefinition::count();
            $debug['step3'] = "Total de configurações: {$count}";

            // Teste 3: Query simples
            $configs = ConfigDefinition::orderBy('id')->limit(5)->get();
            $debug['step4'] = "Configurações carregadas: " . $configs->count();

            return view('admin.config.simple', compact('configs', 'debug'));
        } catch (\Exception $e) {
            return view('admin.config.simple', [
                'configs' => collect(),
                'error' => 'Erro: ' . $e->getMessage() . ' | Linha: ' . $e->getLine() . ' | Arquivo: ' . $e->getFile(),
                'debug' => $debug ?? ['error' => 'Debug não inicializado']
            ]);
        }
    }

    public function setValue(Request $request)
    {
        try {
            $chave = $request->input('chave');
            $valor = $request->input('valor');
            $siteId = $request->input('site_id', null);

            if (!$chave) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chave da configuração é obrigatória'
                ], 400);
            }

            // Versão simplificada sem ConfigService
            return response()->json([
                'success' => true,
                'message' => 'Configuração salva com sucesso! (modo debug)',
                'data' => [
                    'chave' => $chave,
                    'valor' => $valor,
                    'site_id' => $siteId
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearCache()
    {
        try {
            // Versão simplificada sem ConfigService
            return response()->json([
                'success' => true,
                'message' => 'Cache limpo com sucesso! (modo debug)'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar cache: ' . $e->getMessage()
            ], 500);
        }
    }
}
