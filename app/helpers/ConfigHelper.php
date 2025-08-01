<?php

namespace App\Helpers;

use App\Services\ConfigService;
use Illuminate\Support\Facades\Auth;

class ConfigHelper
{
    protected static $instance = null;
    protected $configService;
    protected $empresaId;
    protected $siteId;

    private function __construct()
    {
        // Inicializar com valores do contexto atual se disponível
        $this->empresaId = session('empresa_id');
        $this->siteId = session('site_id');

        $this->configService = new ConfigService(
            $this->empresaId,
            $this->siteId,
            Auth::check() ? Auth::user()->id : null
        );
    }

    // Singleton para garantir uma única instância
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // Método estático para obter configuração
    public static function get($chave, $default = null)
    {
        return self::getInstance()->configService->get($chave, $default);
    }

    // Método estático para definir configuração
    public static function set($chave, $valor)
    {
        return self::getInstance()->configService->set($chave, $valor);
    }

    // Método estático para obter configurações de um grupo
    public static function getGroup($codigoGrupo)
    {
        return self::getInstance()->configService->getByGroup($codigoGrupo);
    }

    // Método para definir contexto
    public static function context($empresaId = null, $siteId = null)
    {
        $instance = self::getInstance();

        if ($empresaId !== null) {
            $instance->empresaId = $empresaId;
            $instance->configService->setEmpresaId($empresaId);
        }

        if ($siteId !== null) {
            $instance->siteId = $siteId;
            $instance->configService->setSiteId($siteId);
        }

        return $instance;
    }

    // Configurações específicas do fidelidade que podem ser utilizadas
    public static function fidelidade($chave = null, $default = null)
    {
        if ($chave === null) {
            return self::getGroup('FIDELIDADE');
        }

        return self::get("fidelidade.{$chave}", $default);
    }

    // Configurações de email
    public static function email($chave = null, $default = null)
    {
        if ($chave === null) {
            return self::getGroup('EMAIL');
        }

        return self::get("email.{$chave}", $default);
    }

    // Configurações de API
    public static function api($chave = null, $default = null)
    {
        if ($chave === null) {
            return self::getGroup('API');
        }

        return self::get("api.{$chave}", $default);
    }

    // Configurações de pagamento
    public static function pagamento($chave = null, $default = null)
    {
        if ($chave === null) {
            return self::getGroup('PAGAMENTO');
        }

        return self::get("pagamento.{$chave}", $default);
    }

    // Configurações gerais do sistema
    public static function sistema($chave = null, $default = null)
    {
        if ($chave === null) {
            return self::getGroup('SISTEMA');
        }

        return self::get("sistema.{$chave}", $default);
    }

    // Método para renovar a instância (útil para mudança de contexto)
    public static function refresh()
    {
        self::$instance = null;
        return self::getInstance();
    }

    // Limpar cache
    public static function clearCache()
    {
        return self::getInstance()->configService->clearAllCache();
    }
}

// Função helper global para facilitar o uso
if (!function_exists('config_get')) {
    function config_get($chave, $default = null)
    {
        return \App\Helpers\ConfigHelper::get($chave, $default);
    }
}

if (!function_exists('config_set')) {
    function config_set($chave, $valor)
    {
        return \App\Helpers\ConfigHelper::set($chave, $valor);
    }
}

if (!function_exists('config_fidelidade')) {
    function config_fidelidade($chave = null, $default = null)
    {
        return \App\Helpers\ConfigHelper::fidelidade($chave, $default);
    }
}

if (!function_exists('config_email')) {
    function config_email($chave = null, $default = null)
    {
        return \App\Helpers\ConfigHelper::email($chave, $default);
    }
}

if (!function_exists('config_api')) {
    function config_api($chave = null, $default = null)
    {
        return \App\Helpers\ConfigHelper::api($chave, $default);
    }
}
