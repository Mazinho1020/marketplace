<?php

namespace App\Comerciantes\Services;

use App\Comerciantes\Models\EmpresaUsuario;
use App\Comerciantes\Models\Marca;
use App\Comerciantes\Models\Empresa;

/**
 * Service responsável pela lógica de negócio dos comerciantes
 * Centraliza as operações complexas e cálculos
 */
class ComercianteService
{
    /**
     * Retorna dados estatísticos para o dashboard do comerciante
     * 
     * @param EmpresaUsuario $user
     * @return array
     */
    public function getDashboardData(EmpresaUsuario $user): array
    {
        // Estatísticas básicas
        $marcas = $user->marcasProprietario()->count();
        $empresas = $user->todas_empresas->count();
        $empresasAtivas = $user->todas_empresas->where('status', 'ativa')->count();

        // Conta usuários vinculados (todos os usuários das empresas do comerciante)
        $usuariosVinculados = 0;
        foreach ($user->todas_empresas as $empresa) {
            $usuariosVinculados += $empresa->usuariosAtivos()->count();
        }

        // Marcas recentes (últimas 5)
        $marcasRecentes = $user->marcasProprietario()
            ->with(['empresas' => function ($query) {
                $query->select('id', 'marca_id', 'nome_fantasia', 'status');
            }])
            ->latest()
            ->limit(5)
            ->get();

        // Empresas recentes (últimas 5)
        $empresasRecentes = $user->todas_empresas
            ->sortByDesc('created_at')
            ->take(5);

        // Estatísticas de status das empresas
        $empresasPorStatus = [
            'ativa' => $user->todas_empresas->where('status', 'ativa')->count(),
            'inativa' => $user->todas_empresas->where('status', 'inativa')->count(),
            'suspensa' => $user->todas_empresas->where('status', 'suspensa')->count(),
        ];

        // Verifica se é primeira vez (não tem marcas nem empresas)
        $isPrimeiraVez = $marcas === 0 && $empresas === 0;

        return [
            'estatisticas' => [
                'total_marcas' => $marcas,
                'total_empresas' => $empresas,
                'empresas_ativas' => $empresasAtivas,
                'usuarios_vinculados' => $usuariosVinculados,
            ],
            'marcas_recentes' => $marcasRecentes,
            'empresas_recentes' => $empresasRecentes,
            'empresas_por_status' => $empresasPorStatus,
            'is_primeira_vez' => $isPrimeiraVez,
            'progresso_configuracao' => $this->calcularProgressoConfiguracao($user),
        ];
    }

    /**
     * Calcula o progresso de configuração do perfil do comerciante
     * 
     * @param EmpresaUsuario $user
     * @return array
     */
    public function calcularProgressoConfiguracao(EmpresaUsuario $user): array
    {
        $itens = [
            'perfil_completo' => !empty($user->nome) && !empty($user->email) && !empty($user->telefone),
            'tem_marca' => $user->marcasProprietario()->count() > 0,
            'tem_empresa' => $user->todas_empresas->count() > 0,
            'empresa_com_endereco' => $user->todas_empresas->where('endereco_cidade', '!=', null)->count() > 0,
            'empresa_com_horario' => $user->todas_empresas->where('horario_funcionamento', '!=', null)->count() > 0,
        ];

        $completos = array_filter($itens);
        $porcentagem = count($itens) > 0 ? (count($completos) / count($itens)) * 100 : 0;

        return [
            'itens' => $itens,
            'completos' => count($completos),
            'total' => count($itens),
            'porcentagem' => round($porcentagem),
        ];
    }

    /**
     * Retorna sugestões de ações para o comerciante
     * 
     * @param EmpresaUsuario $user
     * @return array
     */
    public function getSugestoesAcoes(EmpresaUsuario $user): array
    {
        $sugestoes = [];

        // Se não tem marca, sugere criar
        if ($user->marcasProprietario()->count() === 0) {
            $sugestoes[] = [
                'tipo' => 'marca',
                'titulo' => 'Crie sua primeira marca',
                'descricao' => 'Comece criando uma marca para organizar suas empresas',
                'icone' => 'fas fa-tags',
                'url' => route('comerciantes.marcas.create'),
                'prioridade' => 1
            ];
        }

        // Se tem marca mas não tem empresa, sugere criar
        if ($user->marcasProprietario()->count() > 0 && $user->todas_empresas->count() === 0) {
            $sugestoes[] = [
                'tipo' => 'empresa',
                'titulo' => 'Adicione sua primeira empresa',
                'descricao' => 'Crie uma empresa/unidade para começar a vender',
                'icone' => 'fas fa-building',
                'url' => route('comerciantes.empresas.create'),
                'prioridade' => 2
            ];
        }

        // Se tem empresas sem endereço completo
        $empresasSemEndereco = $user->todas_empresas->where('endereco_cidade', null)->count();
        if ($empresasSemEndereco > 0) {
            $sugestoes[] = [
                'tipo' => 'endereco',
                'titulo' => 'Complete o endereço das empresas',
                'descricao' => "{$empresasSemEndereco} empresa(s) sem endereço completo",
                'icone' => 'fas fa-map-marker-alt',
                'url' => route('comerciantes.empresas.index'),
                'prioridade' => 3
            ];
        }

        // Se tem empresas sem horário de funcionamento
        $empresasSemHorario = $user->todas_empresas->where('horario_funcionamento', null)->count();
        if ($empresasSemHorario > 0) {
            $sugestoes[] = [
                'tipo' => 'horario',
                'titulo' => 'Configure horários de funcionamento',
                'descricao' => "{$empresasSemHorario} empresa(s) sem horário definido",
                'icone' => 'fas fa-clock',
                'url' => route('comerciantes.empresas.index'),
                'prioridade' => 4
            ];
        }

        // Ordena por prioridade
        usort($sugestoes, function ($a, $b) {
            return $a['prioridade'] <=> $b['prioridade'];
        });

        return array_slice($sugestoes, 0, 3); // Máximo 3 sugestões
    }

    /**
     * Verifica se o usuário pode acessar uma empresa específica
     * 
     * @param EmpresaUsuario $user
     * @param int $empresaId
     * @return bool
     */
    public function podeAcessarEmpresa(EmpresaUsuario $user, int $empresaId): bool
    {
        return $user->temPermissaoEmpresa($empresaId);
    }

    /**
     * Retorna as empresas do usuário com suas estatísticas
     * 
     * @param EmpresaUsuario $user
     * @return \Illuminate\Support\Collection
     */
    public function getEmpresasComEstatisticas(EmpresaUsuario $user)
    {
        return $user->todas_empresas->map(function ($empresa) {
            return [
                'empresa' => $empresa,
                'usuarios_vinculados' => $empresa->usuariosAtivos()->count(),
                'esta_funcionando' => $empresa->esta_funcionando,
                'configuracao_completa' => $this->empresaConfiguracaoCompleta($empresa),
            ];
        });
    }

    /**
     * Verifica se a configuração da empresa está completa
     * 
     * @param Empresa $empresa
     * @return bool
     */
    private function empresaConfiguracaoCompleta(Empresa $empresa): bool
    {
        return !empty($empresa->endereco_cidade) &&
            !empty($empresa->telefone) &&
            !empty($empresa->horario_funcionamento);
    }
}
