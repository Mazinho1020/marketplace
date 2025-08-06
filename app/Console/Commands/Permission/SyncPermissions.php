<?php

namespace App\Console\Commands\Permission;

use Illuminate\Console\Command;
use App\Models\Permission\EmpresaPermissao;
use App\Models\Permission\EmpresaPapel;
use App\Models\Business\Business;

class SyncPermissions extends Command
{
    protected $signature = 'permissions:sync {--empresa=}';
    protected $description = 'Sincroniza permissões do sistema';

    public function handle()
    {
        $empresaId = $this->option('empresa');

        $this->info('Sincronizando permissões...');

        // Definir permissões padrão do sistema
        $systemPermissions = [
            'dashboard' => [
                'dashboard.visualizar' => 'Visualizar Dashboard',
                'dashboard.relatorios' => 'Ver Relatórios no Dashboard'
            ],
            'usuarios' => [
                'usuarios.listar' => 'Listar Usuários',
                'usuarios.visualizar' => 'Ver Usuário',
                'usuarios.criar' => 'Criar Usuário',
                'usuarios.editar' => 'Editar Usuário',
                'usuarios.excluir' => 'Excluir Usuário',
                'usuarios.gerenciar_papeis' => 'Gerenciar Papéis',
                'usuarios.gerenciar_permissoes' => 'Gerenciar Permissões'
            ],
            'pdv' => [
                'pdv.acessar' => 'Acessar PDV',
                'pdv.iniciar_venda' => 'Iniciar Venda',
                'pdv.finalizar_venda' => 'Finalizar Venda',
                'pdv.cancelar_venda' => 'Cancelar Venda',
                'pdv.aplicar_desconto' => 'Aplicar Descontos'
            ],
            'produtos' => [
                'produtos.listar' => 'Listar Produtos',
                'produtos.visualizar' => 'Ver Produto',
                'produtos.criar' => 'Criar Produto',
                'produtos.editar' => 'Editar Produto',
                'produtos.excluir' => 'Excluir Produto',
                'produtos.importar' => 'Importar Produtos'
            ],
            'vendas' => [
                'vendas.listar' => 'Listar Vendas',
                'vendas.visualizar' => 'Ver Venda',
                'vendas.criar' => 'Criar Venda',
                'vendas.cancelar' => 'Cancelar Venda',
                'vendas.relatorios' => 'Relatórios de Vendas'
            ],
            'estoque' => [
                'estoque.visualizar' => 'Ver Estoque',
                'estoque.ajustar' => 'Ajustar Estoque',
                'estoque.transferir' => 'Transferir Estoque',
                'estoque.relatorios' => 'Relatórios de Estoque'
            ],
            'financeiro' => [
                'financeiro.visualizar' => 'Ver Financeiro',
                'financeiro.fluxo_caixa' => 'Fluxo de Caixa',
                'financeiro.contas_pagar' => 'Contas a Pagar',
                'financeiro.contas_receber' => 'Contas a Receber',
                'financeiro.relatorios' => 'Relatórios Financeiros'
            ],
            'configuracoes' => [
                'configuracoes.empresa' => 'Configurações da Empresa',
                'configuracoes.sistema' => 'Configurações do Sistema',
                'configuracoes.seguranca' => 'Configurações de Segurança',
                'configuracoes.backup' => 'Backup e Restauração'
            ],
            'sistema' => [
                'sistema.admin' => 'Administrador do Sistema',
                'sistema.logs' => 'Ver Logs do Sistema',
                'sistema.manutencao' => 'Modo Manutenção'
            ]
        ];

        $created = 0;
        $updated = 0;

        // Criar permissões do sistema
        foreach ($systemPermissions as $categoria => $permissions) {
            foreach ($permissions as $codigo => $nome) {
                $permission = EmpresaPermissao::updateOrCreate([
                    'codigo' => $codigo,
                    'is_sistema' => true,
                    'empresa_id' => null
                ], [
                    'nome' => $nome,
                    'categoria' => $categoria,
                    'sync_status' => 'sincronizado'
                ]);

                if ($permission->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }
        }

        // Criar papéis padrão do sistema
        $this->createSystemRoles();

        $this->info("✅ Sincronização concluída!");
        $this->info("📝 Criadas: {$created} permissões");
        $this->info("🔄 Atualizadas: {$updated} permissões");
    }

    private function createSystemRoles()
    {
        $systemRoles = [
            [
                'codigo' => 'super_admin',
                'nome' => 'Super Administrador',
                'descricao' => 'Controle total do sistema, incluindo todas as empresas',
                'nivel_acesso' => 100,
                'is_sistema' => true,
                'empresa_id' => null
            ],
            [
                'codigo' => 'admin',
                'nome' => 'Administrador',
                'descricao' => 'Administrador da empresa com controle total',
                'nivel_acesso' => 90,
                'is_sistema' => true,
                'empresa_id' => null
            ],
            [
                'codigo' => 'gerente',
                'nome' => 'Gerente',
                'descricao' => 'Gerente com acesso a relatórios e configurações',
                'nivel_acesso' => 70,
                'is_sistema' => true,
                'empresa_id' => null
            ],
            [
                'codigo' => 'vendedor',
                'nome' => 'Vendedor',
                'descricao' => 'Vendedor com acesso ao PDV e produtos',
                'nivel_acesso' => 50,
                'is_sistema' => true,
                'empresa_id' => null
            ],
            [
                'codigo' => 'operador',
                'nome' => 'Operador',
                'descricao' => 'Operador com acesso básico ao sistema',
                'nivel_acesso' => 30,
                'is_sistema' => true,
                'empresa_id' => null
            ]
        ];

        foreach ($systemRoles as $roleData) {
            EmpresaPapel::updateOrCreate([
                'codigo' => $roleData['codigo'],
                'is_sistema' => true,
                'empresa_id' => null
            ], $roleData);
        }
    }
}
