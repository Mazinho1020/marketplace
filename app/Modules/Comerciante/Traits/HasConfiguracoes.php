<?php

namespace App\Modules\Comerciante\Traits;

use App\Modules\Comerciante\Config\ConfigManager;
use App\Modules\Comerciante\Config\PessoasConfig;
use Illuminate\Support\Facades\Auth;

trait HasConfiguracoes
{
    protected $configManager;
    protected $pessoasConfig;

    /**
     * Obtém instância do ConfigManager
     */
    public function getConfigManager()
    {
        if (!$this->configManager) {
            $empresaId = $this->empresa_id ?? Auth::user()->empresa_id ?? 2;
            $this->configManager = new ConfigManager($empresaId);
        }

        return $this->configManager;
    }

    /**
     * Obtém instância do PessoasConfig
     */
    public function getConfig()
    {
        if (!$this->pessoasConfig) {
            $this->pessoasConfig = new PessoasConfig($this->getConfigManager());
        }

        return $this->pessoasConfig;
    }

    /**
     * Obtém valor de configuração
     */
    public function config($chave, $default = null)
    {
        return $this->getConfigManager()->get($chave, $default);
    }

    /**
     * Define valor de configuração
     */
    public function setConfig($chave, $valor, $usuarioId = null)
    {
        return $this->getConfigManager()->set($chave, $valor, $usuarioId);
    }

    /**
     * Obtém configurações de um grupo
     */
    public function configGroup($grupo)
    {
        return $this->getConfigManager()->getGroup($grupo);
    }
}
