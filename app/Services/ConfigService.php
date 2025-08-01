<?php

namespace App\Services;

use App\Models\Config\ConfigDefinition;
use App\Models\Config\ConfigGroup;
use App\Models\Config\ConfigHistory;
use App\Models\Config\ConfigValue;
use Illuminate\Support\Facades\Cache;

class ConfigService
{
    protected $empresaId;
    protected $siteId;
    protected $usuarioId;
    protected $cachePrefix = 'config_';
    protected $cacheDuration = 60; // minutos

    public function __construct($empresaId = null, $siteId = null, $usuarioId = null)
    {
        $this->empresaId = $empresaId;
        $this->siteId = $siteId;
        $this->usuarioId = $usuarioId;
    }

    public function setEmpresaId($empresaId)
    {
        $this->empresaId = $empresaId;
        return $this;
    }

    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
        return $this;
    }

    public function setUsuarioId($usuarioId)
    {
        $this->usuarioId = $usuarioId;
        return $this;
    }

    /**
     * Obter uma configuração
     */
    public function get($chave, $default = null)
    {
        if (!$this->empresaId) {
            throw new \Exception("ID da empresa não definido");
        }

        $cacheKey = $this->getCacheKey($chave);

        return Cache::remember($cacheKey, $this->cacheDuration * 60, function () use ($chave, $default) {
            $definicao = ConfigDefinition::where('empresa_id', $this->empresaId)
                ->where('chave', $chave)
                ->where('ativo', true)
                ->first();

            if (!$definicao) {
                return $default;
            }

            $query = ConfigValue::where('empresa_id', $this->empresaId)
                ->where('config_id', $definicao->id);

            // Tenta por site específico primeiro
            if ($this->siteId) {
                $valor = $query->where('site_id', $this->siteId)->first();
                if ($valor) {
                    return $definicao->formatarValor($valor->valor);
                }
            }

            // Valor geral (sem site específico)
            $valor = $query->whereNull('site_id')->first();
            if ($valor) {
                return $definicao->formatarValor($valor->valor);
            }

            // Valor padrão da definição
            if ($definicao->valor_padrao !== null) {
                return $definicao->formatarValor($definicao->valor_padrao);
            }

            return $default;
        });
    }

    /**
     * Definir uma configuração
     */
    public function set($chave, $valor, $registrarHistorico = true)
    {
        if (!$this->empresaId) {
            throw new \Exception("ID da empresa não definido");
        }

        $definicao = ConfigDefinition::where('empresa_id', $this->empresaId)
            ->where('chave', $chave)
            ->first();

        if (!$definicao) {
            throw new \Exception("Configuração não encontrada: {$chave}");
        }

        // Validar valor de acordo com o tipo
        $this->validarValor($definicao, $valor);

        // Procurar valor existente ou criar novo
        $configValue = ConfigValue::firstOrNew([
            'empresa_id' => $this->empresaId,
            'config_id' => $definicao->id,
            'site_id' => $this->siteId,
        ]);

        $valorAntigo = $configValue->valor;
        $configValue->valor = $this->prepararValorParaSalvar($definicao, $valor);
        $configValue->usuario_id = $this->usuarioId;
        $configValue->save();

        // Registrar histórico
        if ($registrarHistorico) {
            $this->registrarHistorico($definicao->id, $valorAntigo, $configValue->valor, 'update');
        }

        // Limpar cache
        $this->clearConfigCache($chave);

        return true;
    }

    /**
     * Obter todas as configurações de um grupo
     */
    public function getByGroup($codigoGrupo)
    {
        $grupo = ConfigGroup::where('empresa_id', $this->empresaId)
            ->where('codigo', $codigoGrupo)
            ->where('ativo', true)
            ->first();

        if (!$grupo) {
            return [];
        }

        $configs = [];
        $definicoes = ConfigDefinition::where('empresa_id', $this->empresaId)
            ->where('grupo_id', $grupo->id)
            ->where('ativo', true)
            ->orderBy('ordem')
            ->get();

        foreach ($definicoes as $definicao) {
            $configs[$definicao->chave] = $this->get($definicao->chave);
        }

        return $configs;
    }

    /**
     * Validar valor de acordo com o tipo
     */
    protected function validarValor($definicao, $valor)
    {
        if ($definicao->obrigatorio && ($valor === null || $valor === '')) {
            throw new \Exception("O campo '{$definicao->nome}' é obrigatório");
        }

        if ($valor === null || $valor === '') {
            return true;
        }

        switch ($definicao->tipo) {
            case 'integer':
                if (!is_numeric($valor) || intval($valor) != $valor) {
                    throw new \Exception("O campo '{$definicao->nome}' deve ser um número inteiro");
                }
                break;

            case 'float':
                if (!is_numeric($valor)) {
                    throw new \Exception("O campo '{$definicao->nome}' deve ser um número");
                }
                break;

            case 'boolean':
                if (
                    !is_bool($valor) && $valor !== '0' && $valor !== '1' &&
                    $valor !== 0 && $valor !== 1 &&
                    strtolower($valor) !== 'true' && strtolower($valor) !== 'false'
                ) {
                    throw new \Exception("O campo '{$definicao->nome}' deve ser um valor booleano");
                }
                break;

            case 'email':
                if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("O campo '{$definicao->nome}' deve ser um email válido");
                }
                break;

            case 'url':
                if (!filter_var($valor, FILTER_VALIDATE_URL)) {
                    throw new \Exception("O campo '{$definicao->nome}' deve ser uma URL válida");
                }
                break;
        }

        return true;
    }

    /**
     * Preparar valor para salvar no banco
     */
    protected function prepararValorParaSalvar($definicao, $valor)
    {
        if ($valor === null) {
            return null;
        }

        switch ($definicao->tipo) {
            case 'json':
            case 'array':
                if (is_array($valor) || is_object($valor)) {
                    return json_encode($valor);
                }
                return $valor;

            case 'boolean':
                if (is_bool($valor)) {
                    return $valor ? '1' : '0';
                } elseif ($valor === '0' || $valor === '1' || $valor === 0 || $valor === 1) {
                    return $valor ? '1' : '0';
                } elseif (strtolower($valor) === 'true' || strtolower($valor) === 'false') {
                    return strtolower($valor) === 'true' ? '1' : '0';
                }
                return $valor;

            default:
                return (string) $valor;
        }
    }

    /**
     * Registrar histórico de alteração
     */
    protected function registrarHistorico($configId, $valorAntigo, $valorNovo, $acao = 'update')
    {
        $ip = request()->ip();
        $userAgent = request()->userAgent();

        ConfigHistory::create([
            'empresa_id' => $this->empresaId,
            'config_id' => $configId,
            'site_id' => $this->siteId,
            'acao' => $acao,
            'valor_anterior' => $valorAntigo,
            'valor_novo' => $valorNovo,
            'usuario_id' => $this->usuarioId,
            'ip' => $ip,
            'user_agent' => $userAgent
        ]);
    }

    /**
     * Gerenciamento de cache
     */
    protected function getCacheKey($chave)
    {
        return $this->cachePrefix . $this->empresaId . '_' . ($this->siteId ?: '0') . '_' . $chave;
    }

    protected function clearConfigCache($chave)
    {
        $cacheKey = $this->getCacheKey($chave);
        Cache::forget($cacheKey);
    }

    public function clearAllCache()
    {
        Cache::flush();
    }

    /**
     * Método simplificado para definir valor
     */
    public function setValue($chave, $valor, $siteId = null)
    {
        // Definir empresa padrão se não estiver definida
        if (!$this->empresaId) {
            $this->empresaId = 1; // ID padrão para desenvolvimento
        }

        // Definir site se fornecido
        if ($siteId) {
            $this->siteId = $siteId;
        }

        return $this->set($chave, $valor);
    }

    /**
     * Método simplificado para limpar cache
     */
    public function clearCache($chave = null)
    {
        if ($chave) {
            $cacheKey = $this->getCacheKey($chave);
            Cache::forget($cacheKey);
        } else {
            $this->clearAllCache();
        }
    }
}
