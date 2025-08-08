<?php

namespace App\Modules\Comerciante\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Modules\Comerciante\Config\ConfigManager;
use App\Modules\Comerciante\Models\Config\ConfigGroup;
use App\Modules\Comerciante\Models\Config\ConfigDefinition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    protected $configManager;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * Exibe página principal de configurações
     */
    public function index()
    {
        $empresaId = auth()->user()->empresa_id ?? 2;

        $grupos = ConfigGroup::arvoreCompleta($empresaId);

        return view('comerciante.config.index', compact('grupos'));
    }

    /**
     * Obtém configurações de um grupo
     */
    public function getGroup($codigoGrupo)
    {
        try {
            $configuracoes = $this->configManager->getGroup($codigoGrupo);

            return response()->json([
                'success' => true,
                'data' => $configuracoes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obtém valor de uma configuração específica
     */
    public function getValue($chave)
    {
        try {
            $valor = $this->configManager->get($chave);

            return response()->json([
                'success' => true,
                'data' => [
                    'chave' => $chave,
                    'valor' => $valor
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Atualiza valor de uma configuração
     */
    public function setValue(Request $request, $chave)
    {
        try {
            $validator = Validator::make($request->all(), [
                'valor' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $this->configManager->set($chave, $request->valor, auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Configuração atualizada com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Atualiza múltiplas configurações
     */
    public function setMultiple(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'configuracoes' => 'required|array',
                'configuracoes.*.chave' => 'required|string',
                'configuracoes.*.valor' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::transaction(function () use ($request) {
                foreach ($request->configuracoes as $config) {
                    $this->configManager->set($config['chave'], $config['valor'], auth()->id());
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Configurações atualizadas com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Exporta todas as configurações
     */
    public function export()
    {
        try {
            $configuracoes = $this->configManager->exportAll();

            return response()->json([
                'success' => true,
                'data' => $configuracoes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Limpa cache de configurações
     */
    public function clearCache()
    {
        try {
            $this->configManager->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Cache limpo com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obtém histórico de alterações
     */
    public function getHistory($chave = null)
    {
        try {
            $empresaId = auth()->user()->empresa_id ?? 2;

            $query = \App\Modules\Comerciante\Models\Config\ConfigHistory::empresa($empresaId)
                ->with(['definition', 'usuario'])
                ->orderBy('created_at', 'desc');

            if ($chave) {
                $query->configuracao($chave, $empresaId);
            }

            $historico = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $historico
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Valida valor antes de salvar
     */
    public function validateValue(Request $request, $chave)
    {
        try {
            $empresaId = auth()->user()->empresa_id ?? 2;

            $definition = ConfigDefinition::where('chave', $chave)
                ->where('empresa_id', $empresaId)
                ->first();

            if (!$definition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuração não encontrada'
                ], 404);
            }

            $errors = $definition->validarValor($request->valor);

            return response()->json([
                'success' => empty($errors),
                'errors' => $errors,
                'valor_convertido' => $definition->convertValue($request->valor)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Restaura valor padrão
     */
    public function restoreDefault($chave)
    {
        try {
            $empresaId = auth()->user()->empresa_id ?? 2;

            $definition = ConfigDefinition::where('chave', $chave)
                ->where('empresa_id', $empresaId)
                ->first();

            if (!$definition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuração não encontrada'
                ], 404);
            }

            if ($definition->valor_padrao === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta configuração não possui valor padrão'
                ], 400);
            }

            $this->configManager->set($chave, $definition->valor_padrao, auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Valor padrão restaurado com sucesso',
                'valor' => $definition->convertValue($definition->valor_padrao)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
