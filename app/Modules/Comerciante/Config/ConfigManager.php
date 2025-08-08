<?php

namespace App\Modules\Comerciante\Config;

use App\Modules\Comerciante\Models\Config\ConfigDefinition;
use App\Modules\Comerciante\Models\Config\ConfigGroup;
use App\Modules\Comerciante\Models\Config\ConfigValue;
use App\Modules\Comerciante\Models\Config\ConfigHistory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ConfigManager
{
    protected $empresaId;
    protected $cachePrefix = 'comerciante_config_';
    protected $cacheTtl = 3600; // 1 hora

    public function __construct($empresaId = null)
    {
        $this->empresaId = $empresaId ?? optional(Auth::user())->empresa_id ?? 2;
    }

    /**
     * Obter uma configuração específica
     */
    public function get($chave, $grupo = 'geral', $default = null)
    {
        $cacheKey = $this->getCacheKey($chave, $grupo);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($chave, $grupo, $default) {
            $definition = $this->getDefinition($chave, $grupo);

            if (!$definition) {
                return $default;
            }

            $configValue = ConfigValue::where('config_definition_id', $definition->id)->first();

            if (!$configValue) {
                return $definition->valor_padrao ?? $default;
            }

            return $this->convertValue($configValue->valor, $definition->tipo);
        });
    }

    /**
     * Definir uma configuração
     */
    public function set($chave, $valor, $grupo = 'geral', $usuarioId = null)
    {
        DB::transaction(function () use ($chave, $valor, $grupo, $usuarioId) {
            $definition = $this->getOrCreateDefinition($chave, $grupo, gettype($valor));

            // Validar o valor
            $this->validateValue($valor, $definition->tipo);

            $configValue = ConfigValue::where('config_definition_id', $definition->id)->first();
            $valorAnterior = $configValue ? $configValue->valor : null;

            if ($configValue) {
                $configValue->update([
                    'valor' => $valor,
                    'updated_by' => $usuarioId ?? Auth::id()
                ]);
            } else {
                ConfigValue::create([
                    'config_definition_id' => $definition->id,
                    'valor' => $valor,
                    'created_by' => $usuarioId ?? Auth::id(),
                    'updated_by' => $usuarioId ?? Auth::id()
                ]);
            }

            // Registrar no histórico
            ConfigHistory::create([
                'config_definition_id' => $definition->id,
                'valor_anterior' => $valorAnterior,
                'valor_novo' => $valor,
                'usuario_id' => $usuarioId ?? Auth::id(),
                'motivo' => 'Alteração via ConfigManager'
            ]);

            // Limpar cache
            $this->clearCache($chave, $grupo);
        });

        return $this;
    }

    /**
     * Obter todas as configurações de um grupo
     */
    public function getGroup($grupo)
    {
        $cacheKey = $this->getCacheKey('_group_' . $grupo);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($grupo) {
            $definitions = ConfigDefinition::whereHas('grupo', function ($q) use ($grupo) {
                $q->where('nome', $grupo)->where('empresa_id', $this->empresaId);
            })->with('valor')->get();

            $configs = [];
            foreach ($definitions as $definition) {
                $valor = $definition->valor ? $definition->valor->valor : $definition->valor_padrao;
                $configs[$definition->chave] = $this->convertValue($valor, $definition->tipo);
            }

            return $configs;
        });
    }

    /**
     * Listar todos os grupos
     */
    public function listarGrupos()
    {
        return ConfigGroup::where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->orderBy('ordem')
            ->get()
            ->toArray();
    }

    /**
     * Exportar configurações
     */
    public function exportar($grupo = null)
    {
        $query = ConfigDefinition::whereHas('grupo', function ($q) use ($grupo) {
            $q->where('empresa_id', $this->empresaId);
            if ($grupo) {
                $q->where('nome', $grupo);
            }
        })->with(['grupo', 'valor']);

        $definitions = $query->get();
        $export = [];

        foreach ($definitions as $definition) {
            $valor = $definition->valor ? $definition->valor->valor : $definition->valor_padrao;
            $export[$definition->grupo->nome][$definition->chave] = [
                'valor' => $this->convertValue($valor, $definition->tipo),
                'tipo' => $definition->tipo,
                'descricao' => $definition->descricao,
                'valor_padrao' => $definition->valor_padrao
            ];
        }

        return $export;
    }

    /**
     * Importar configurações
     */
    public function importar($dados, $usuarioId = null)
    {
        DB::transaction(function () use ($dados, $usuarioId) {
            foreach ($dados as $grupo => $configs) {
                foreach ($configs as $chave => $config) {
                    $this->set($chave, $config['valor'], $grupo, $usuarioId);
                }
            }
        });

        return $this;
    }

    /**
     * Obter ou criar definição
     */
    protected function getOrCreateDefinition($chave, $grupo, $tipo = 'string')
    {
        $grupoModel = ConfigGroup::firstOrCreate([
            'nome' => $grupo,
            'empresa_id' => $this->empresaId
        ], [
            'descricao' => ucfirst($grupo) . ' - Criado automaticamente',
            'ativo' => true,
            'ordem' => ConfigGroup::where('empresa_id', $this->empresaId)->max('ordem') + 1
        ]);

        return ConfigDefinition::firstOrCreate([
            'chave' => $chave,
            'config_group_id' => $grupoModel->id
        ], [
            'nome' => ucfirst(str_replace('_', ' ', $chave)),
            'descricao' => 'Configuração criada automaticamente',
            'tipo' => $tipo,
            'valor_padrao' => null,
            'obrigatorio' => false,
            'visivel' => true,
            'editavel' => true,
            'ordem' => ConfigDefinition::where('config_group_id', $grupoModel->id)->max('ordem') + 1
        ]);
    }

    /**
     * Obter definição
     */
    protected function getDefinition($chave, $grupo)
    {
        return ConfigDefinition::whereHas('grupo', function ($q) use ($grupo) {
            $q->where('nome', $grupo)->where('empresa_id', $this->empresaId);
        })->where('chave', $chave)->first();
    }

    /**
     * Validar valor conforme tipo
     */
    protected function validateValue($valor, $tipo)
    {
        switch ($tipo) {
            case 'integer':
                if (!is_numeric($valor)) {
                    throw new \InvalidArgumentException("Valor deve ser numérico para tipo integer");
                }
                break;
            case 'boolean':
                if (!is_bool($valor) && !in_array($valor, ['0', '1', 'true', 'false'])) {
                    throw new \InvalidArgumentException("Valor deve ser booleano");
                }
                break;
            case 'json':
                if (is_string($valor)) {
                    json_decode($valor);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \InvalidArgumentException("Valor deve ser JSON válido");
                    }
                }
                break;
        }
    }

    /**
     * Converter valor conforme tipo
     */
    protected function convertValue($valor, $tipo)
    {
        switch ($tipo) {
            case 'integer':
                return (int) $valor;
            case 'boolean':
                return in_array($valor, [1, '1', 'true', true]);
            case 'json':
                return is_string($valor) ? json_decode($valor, true) : $valor;
            case 'float':
                return (float) $valor;
            default:
                return $valor;
        }
    }

    /**
     * Gerar chave de cache
     */
    protected function getCacheKey($chave, $grupo = 'geral')
    {
        return $this->cachePrefix . $this->empresaId . '_' . $grupo . '_' . $chave;
    }

    /**
     * Limpar cache
     */
    protected function clearCache($chave = null, $grupo = null)
    {
        if ($chave && $grupo) {
            Cache::forget($this->getCacheKey($chave, $grupo));
        } else {
            // Limpar todo cache do prefixo
            Cache::flush(); // Em produção, usar tags ou um método mais específico
        }
    }
}
