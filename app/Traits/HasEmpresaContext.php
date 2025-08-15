<?php

namespace App\Traits;

use App\Comerciantes\Models\Empresa;
use Illuminate\Support\Facades\Auth;

/**
 * Trait para facilitar o trabalho com contexto de empresa
 */
trait HasEmpresaContext
{
    /**
     * Retorna o ID da empresa atual da sessão
     */
    public function getEmpresaAtualId(): ?int
    {
        return session('empresa_atual_id');
    }

    /**
     * Retorna a empresa atual da sessão
     */
    public function getEmpresaAtual(): ?Empresa
    {
        $empresaId = $this->getEmpresaAtualId();
        
        if (!$empresaId) {
            return null;
        }

        return Empresa::find($empresaId);
    }

    /**
     * Define a empresa atual na sessão
     */
    public function setEmpresaAtual(int $empresaId): void
    {
        session(['empresa_atual_id' => $empresaId]);
    }

    /**
     * Remove a empresa atual da sessão
     */
    public function clearEmpresaAtual(): void
    {
        session()->forget('empresa_atual_id');
    }

    /**
     * Verifica se o usuário atual pode acessar a empresa especificada
     */
    public function podeAcessarEmpresa(int $empresaId): bool
    {
        $user = Auth::guard('comerciante')->user();
        
        if (!$user) {
            return false;
        }

        return $user->temPermissaoEmpresa($empresaId);
    }

    /**
     * Retorna todas as empresas do usuário atual
     */
    public function getEmpresasUsuario()
    {
        $user = Auth::guard('comerciante')->user();
        
        if (!$user) {
            return collect();
        }

        return $user->todas_empresas ?? collect();
    }

    /**
     * Gera URL para uma rota financeira com a empresa atual
     */
    public function routeFinanceiro(string $routeName, array $parameters = []): string
    {
        $empresaId = $this->getEmpresaAtualId();
        
        if (!$empresaId) {
            // Se não há empresa selecionada, redireciona para seleção
            return route('comerciantes.empresas.index');
        }

        // Adiciona a empresa aos parâmetros
        $parameters = array_merge(['empresa' => $empresaId], $parameters);
        
        return route($routeName, $parameters);
    }
}
