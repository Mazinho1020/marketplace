<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Config\ConfigManager;
use App\Models\Business\Business;

class ConfigAdminController extends Controller
{
    /**
     * Dashboard de configurações para empresa desenvolvedora
     */
    public function index()
    {
        $config = ConfigManager::getInstance();
        $isDevCompany = $config->isDeveloperCompany();

        // Se for a empresa desenvolvedora
        if ($isDevCompany) {
            // Obter lista de clientes
            $clients = [];
            $empresas = Business::where('id', '!=', 1)->get();

            foreach ($empresas as $empresa) {
                $clients[] = [
                    'id' => $empresa->id,
                    'nome' => $empresa->nome,
                    'config' => client_config($empresa->id)
                ];
            }

            return view('config.admin', [
                'appName' => system_config('app_name'),
                'appVersion' => system_config('app_version', '1.0.0'),
                'clientes' => $clients,
                'isDevCompany' => true
            ]);
        }

        // Se for cliente
        return view('config.client', [
            'appName' => $config->get('app_name'),
            'sistemConfigs' => $config->getGroup('sistema'),
            'isDevCompany' => false
        ]);
    }

    /**
     * Gerenciar cliente específico (só para desenvolvedora)
     */
    public function manageClient($clientId)
    {
        $config = ConfigManager::getInstance();
        if (!$config->isDeveloperCompany()) {
            return abort(403, 'Acesso negado');
        }

        $clientInfo = client_config($clientId);

        return view('config.manage-client', [
            'client' => $clientInfo,
            'planos' => json_decode(system_config('licenca_planos', '["basic","standard","premium"]'), true)
        ]);
    }

    /**
     * Atualizar configurações de um cliente
     */
    public function updateClient(Request $request, $clientId)
    {
        $config = ConfigManager::getInstance();
        if (!$config->isDeveloperCompany()) {
            return abort(403, 'Acesso negado');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'plano' => 'required|string|in:basic,standard,premium',
            'ativo' => 'boolean',
            'max_usuarios' => 'required|integer|min:1|max:1000',
            'data_expiracao' => 'required|date',
            'trial_days' => 'integer|min:0',
            'modules' => 'array'
        ]);

        // Salvar configurações do cliente
        $config->useDeveloperContext();

        $clientPrefix = "cliente_{$clientId}_";
        $config->set("{$clientPrefix}nome", $request->nome);
        $config->set("{$clientPrefix}plano", $request->plano);
        $config->set("{$clientPrefix}ativo", $request->boolean('ativo', true));
        $config->set("{$clientPrefix}usuarios_max", $request->max_usuarios);
        $config->set("{$clientPrefix}data_expiracao", $request->data_expiracao);
        $config->set("{$clientPrefix}trial_days", $request->trial_days ?? 0);
        $config->set("{$clientPrefix}modules", json_encode($request->modules ?? []));

        $config->restoreOriginalContext();

        return redirect()
            ->route('config.manage-client', $clientId)
            ->with('success', 'Configurações do cliente atualizadas com sucesso!');
    }

    /**
     * Status geral do sistema (só para desenvolvedora)
     */
    public function systemStatus()
    {
        $config = ConfigManager::getInstance();
        if (!$config->isDeveloperCompany()) {
            return abort(403, 'Acesso negado');
        }

        $totalClients = Business::where('id', '!=', 1)->count();
        $activeClients = 0;
        $expiredClients = 0;

        foreach (Business::where('id', '!=', 1)->get() as $empresa) {
            if (client_is_active($empresa->id)) {
                $activeClients++;
            } else {
                $expiredClients++;
            }
        }

        return view('config.system-status', [
            'totalClients' => $totalClients,
            'activeClients' => $activeClients,
            'expiredClients' => $expiredClients,
            'systemVersion' => system_config('app_version', '1.0.0'),
            'maxClients' => system_config('max_clientes', 100)
        ]);
    }
}
