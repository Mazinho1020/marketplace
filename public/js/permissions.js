/**
 * SISTEMA DE PERMISSÕES - FRONTEND JAVASCRIPT
 * 
 * Este arquivo demonstra como trabalhar com permissões no frontend
 * do marketplace, incluindo verificações dinâmicas e UI responsiva.
 */

class PermissionManager {
    constructor() {
        this.permissions = [];
        this.roles = [];
        this.loaded = false;
        this.loadPermissions();
    }

    /**
     * Carrega permissões do usuário via API
     */
    async loadPermissions() {
        try {
            const response = await fetch('/api/admin/my-permissions');
            const data = await response.json();
            
            this.permissions = data.permissions || [];
            this.roles = data.roles || [];
            this.effectivePermissions = data.effective_permissions || {};
            this.loaded = true;
            
            // Disparar evento personalizado
            document.dispatchEvent(new CustomEvent('permissionsLoaded', {
                detail: { permissions: this.permissions, roles: this.roles }
            }));
            
            // Aplicar permissões na UI
            this.applyPermissionsToUI();
            
        } catch (error) {
            console.error('Erro ao carregar permissões:', error);
        }
    }

    /**
     * Verifica se usuário tem uma permissão específica
     */
    hasPermission(permission) {
        return this.permissions.includes(permission);
    }

    /**
     * Verifica se usuário tem alguma das permissões
     */
    hasAnyPermission(permissions) {
        return permissions.some(permission => this.hasPermission(permission));
    }

    /**
     * Verifica se usuário tem todas as permissões
     */
    hasAllPermissions(permissions) {
        return permissions.every(permission => this.hasPermission(permission));
    }

    /**
     * Verifica se usuário tem um papel específico
     */
    hasRole(role) {
        return this.roles.includes(role);
    }

    /**
     * Aplica permissões na interface do usuário
     */
    applyPermissionsToUI() {
        // Elementos com atributo data-permission
        document.querySelectorAll('[data-permission]').forEach(element => {
            const permission = element.getAttribute('data-permission');
            if (!this.hasPermission(permission)) {
                element.style.display = 'none';
                element.classList.add('permission-hidden');
            }
        });

        // Elementos com atributo data-role
        document.querySelectorAll('[data-role]').forEach(element => {
            const role = element.getAttribute('data-role');
            if (!this.hasRole(role)) {
                element.style.display = 'none';
                element.classList.add('role-hidden');
            }
        });

        // Elementos com atributo data-any-permission
        document.querySelectorAll('[data-any-permission]').forEach(element => {
            const permissions = element.getAttribute('data-any-permission').split(',');
            if (!this.hasAnyPermission(permissions)) {
                element.style.display = 'none';
                element.classList.add('permission-hidden');
            }
        });

        // Botões que devem ser desabilitados
        document.querySelectorAll('[data-permission-disable]').forEach(element => {
            const permission = element.getAttribute('data-permission-disable');
            if (!this.hasPermission(permission)) {
                element.disabled = true;
                element.classList.add('permission-disabled');
                element.title = 'Sem permissão para esta ação';
            }
        });

        // Aplicar permissões específicas do menu
        this.updateMenuBasedOnPermissions();
        
        // Aplicar permissões específicas do dashboard
        this.updateDashboardBasedOnPermissions();
    }

    /**
     * Atualiza menu baseado nas permissões
     */
    updateMenuBasedOnPermissions() {
        const menuItems = {
            'menu-usuarios': ['usuarios.listar'],
            'menu-produtos': ['produtos.listar'],
            'menu-vendas': ['vendas.listar', 'pdv.acessar'],
            'menu-financeiro': ['financeiro.visualizar'],
            'menu-estoque': ['estoque.visualizar'],
            'menu-relatorios': ['vendas.relatorios', 'financeiro.relatorios', 'estoque.relatorios'],
            'menu-configuracoes': ['configuracoes.empresa', 'configuracoes.sistema'],
            'menu-admin': ['sistema.admin']
        };

        Object.entries(menuItems).forEach(([menuId, requiredPermissions]) => {
            const menuElement = document.getElementById(menuId);
            if (menuElement) {
                if (!this.hasAnyPermission(requiredPermissions)) {
                    menuElement.style.display = 'none';
                }
            }
        });
    }

    /**
     * Atualiza widgets do dashboard baseado nas permissões
     */
    updateDashboardBasedOnPermissions() {
        const dashboardWidgets = {
            'widget-vendas': 'vendas.relatorios',
            'widget-financeiro': 'financeiro.relatorios',
            'widget-estoque': 'estoque.visualizar',
            'widget-usuarios': 'usuarios.listar',
            'widget-admin': 'sistema.admin'
        };

        Object.entries(dashboardWidgets).forEach(([widgetId, permission]) => {
            const widget = document.getElementById(widgetId);
            if (widget && !this.hasPermission(permission)) {
                widget.style.display = 'none';
            }
        });
    }

    /**
     * Mostra modal de permissão negada
     */
    showPermissionDeniedModal(requiredPermission) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Acesso Negado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Você não tem permissão para executar esta ação.</p>
                        <p><strong>Permissão necessária:</strong> ${requiredPermission}</p>
                        <p>Entre em contato com o administrador se necessário.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        // Remover modal após fechar
        modal.addEventListener('hidden.bs.modal', () => {
            document.body.removeChild(modal);
        });
    }

    /**
     * Intercepta cliques em elementos protegidos
     */
    setupProtectedElementsHandler() {
        document.addEventListener('click', (event) => {
            const element = event.target.closest('[data-permission-required]');
            if (element) {
                const requiredPermission = element.getAttribute('data-permission-required');
                if (!this.hasPermission(requiredPermission)) {
                    event.preventDefault();
                    event.stopPropagation();
                    this.showPermissionDeniedModal(requiredPermission);
                }
            }
        });
    }

    /**
     * Utilidades para formulários com permissões
     */
    setupFormPermissions() {
        // Desabilitar campos de formulário baseado em permissões
        document.querySelectorAll('[data-edit-permission]').forEach(field => {
            const permission = field.getAttribute('data-edit-permission');
            if (!this.hasPermission(permission)) {
                field.disabled = true;
                field.classList.add('permission-disabled');
            }
        });

        // Interceptar submissão de formulários
        document.querySelectorAll('form[data-submit-permission]').forEach(form => {
            const permission = form.getAttribute('data-submit-permission');
            form.addEventListener('submit', (event) => {
                if (!this.hasPermission(permission)) {
                    event.preventDefault();
                    this.showPermissionDeniedModal(permission);
                }
            });
        });
    }
}

// Funções auxiliares globais
window.PermissionHelpers = {
    /**
     * Verifica permissão antes de executar ação
     */
    checkPermissionAndExecute: function(permission, callback, errorCallback = null) {
        if (permissionManager.hasPermission(permission)) {
            callback();
        } else {
            if (errorCallback) {
                errorCallback();
            } else {
                permissionManager.showPermissionDeniedModal(permission);
            }
        }
    },

    /**
     * Carrega conteúdo condicionalmente baseado em permissão
     */
    loadConditionalContent: function(permission, elementId, contentUrl) {
        if (permissionManager.hasPermission(permission)) {
            fetch(contentUrl)
                .then(response => response.text())
                .then(html => {
                    document.getElementById(elementId).innerHTML = html;
                })
                .catch(error => console.error('Erro ao carregar conteúdo:', error));
        }
    },

    /**
     * Atualiza badge de permissões no header
     */
    updatePermissionBadge: function() {
        const badge = document.getElementById('permission-badge');
        if (badge) {
            const totalPermissions = permissionManager.permissions.length;
            const roles = permissionManager.roles;
            
            badge.innerHTML = `
                <span class="badge bg-info">${totalPermissions} permissões</span>
                <span class="badge bg-success">${roles.length} papéis</span>
            `;
        }
    }
};

// Exemplos de uso prático
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar gerenciador de permissões
    window.permissionManager = new PermissionManager();
    
    // Aguardar carregamento das permissões
    document.addEventListener('permissionsLoaded', function(event) {
        console.log('Permissões carregadas:', event.detail);
        
        // Configurar handlers após carregar permissões
        permissionManager.setupProtectedElementsHandler();
        permissionManager.setupFormPermissions();
        
        // Atualizar badge de permissões
        PermissionHelpers.updatePermissionBadge();
        
        // Exemplos de verificações
        if (permissionManager.hasPermission('pdv.acessar')) {
            console.log('Usuário pode acessar PDV');
        }
        
        if (permissionManager.hasRole('admin')) {
            console.log('Usuário é administrador');
            // Carregar conteúdo específico de admin
            PermissionHelpers.loadConditionalContent(
                'sistema.admin', 
                'admin-panel', 
                '/api/admin/admin-panel-content'
            );
        }
    });
});

/**
 * EXEMPLOS DE USO NO HTML:
 * 
 * <!-- Botão que só aparece se tiver permissão -->
 * <button data-permission="usuarios.criar" class="btn btn-primary">
 *     Criar Usuário
 * </button>
 * 
 * <!-- Botão que fica desabilitado se não tiver permissão -->
 * <button data-permission-disable="usuarios.excluir" class="btn btn-danger">
 *     Excluir
 * </button>
 * 
 * <!-- Div que só aparece para admins -->
 * <div data-role="admin">
 *     Conteúdo apenas para administradores
 * </div>
 * 
 * <!-- Elemento que precisa de qualquer uma das permissões -->
 * <div data-any-permission="vendas.relatorios,financeiro.relatorios">
 *     Seção de Relatórios
 * </div>
 * 
 * <!-- Formulário protegido -->
 * <form data-submit-permission="produtos.criar">
 *     <!-- campos do formulário -->
 * </form>
 * 
 * <!-- Campo que fica desabilitado -->
 * <input data-edit-permission="configuracoes.empresa" type="text" />
 * 
 * <!-- Link protegido -->
 * <a href="/admin/users/delete/1" data-permission-required="usuarios.excluir">
 *     Excluir Usuário
 * </a>
 */
