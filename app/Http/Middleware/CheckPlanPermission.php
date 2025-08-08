<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AfiPlanAssinaturas;

class CheckPlanPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $feature
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $feature)
    {
        // Verificar se o usuário está autenticado
        if (!Auth::guard('comerciante')->check()) {
            return redirect()->route('comerciantes.login');
        }

        $user = Auth::guard('comerciante')->user();
        $empresaId = $user->empresa_id;

        // Buscar assinatura ativa
        $assinatura = AfiPlanAssinaturas::with('plano')
            ->where('empresa_id', $empresaId)
            ->whereIn('status', ['trial', 'ativo'])
            ->orderBy('created_at', 'desc')
            ->first();

        // Se não tem assinatura, redirecionar para planos
        if (!$assinatura) {
            return redirect()->route('comerciantes.planos.planos')
                ->with('warning', 'Você precisa ter um plano ativo para acessar esta funcionalidade.');
        }

        // Verificar se o plano tem acesso à funcionalidade
        if (!$assinatura->plano->hasFeature($feature)) {
            return redirect()->route('comerciantes.planos.planos')
                ->with('error', "Seu plano atual não inclui a funcionalidade: {$this->getFeatureName($feature)}. Faça upgrade para acessar!");
        }

        return $next($request);
    }

    /**
     * Traduzir nomes de features para português
     */
    private function getFeatureName(string $feature): string
    {
        $features = [
            'advanced_reports' => 'Relatórios Avançados',
            'unlimited_users' => 'Usuários Ilimitados',
            'api_access' => 'Acesso à API',
            'custom_branding' => 'Marca Personalizada',
            'priority_support' => 'Suporte Prioritário',
            'advanced_integrations' => 'Integrações Avançadas',
            'bulk_operations' => 'Operações em Lote',
            'custom_fields' => 'Campos Personalizados',
            'audit_logs' => 'Logs de Auditoria',
            'white_label' => 'White Label',
            'multi_company' => 'Multi-empresas',
            'advanced_permissions' => 'Permissões Avançadas'
        ];

        return $features[$feature] ?? ucfirst(str_replace('_', ' ', $feature));
    }
}
