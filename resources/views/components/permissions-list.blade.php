{{-- Componente reutilizável para lista de permissões --}}
<div class="mb-3">
    <label class="form-label">Permissões</label>
    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
        {{-- Empresas --}}
        <div class="mb-3">
            <h6 class="text-primary mb-2">
                <i class="fas fa-building me-1"></i>Empresas
            </h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="empresas.visualizar" id="{{ $prefix }}_perm_empresas_visualizar">
                        <label class="form-check-label" for="{{ $prefix }}_perm_empresas_visualizar">Visualizar Empresa</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="empresas.listar" id="{{ $prefix }}_perm_empresas_listar">
                        <label class="form-check-label" for="{{ $prefix }}_perm_empresas_listar">Listar Empresas</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="empresas.criar" id="{{ $prefix }}_perm_empresas_criar">
                        <label class="form-check-label" for="{{ $prefix }}_perm_empresas_criar">Criar Empresa</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="empresas.editar" id="{{ $prefix }}_perm_empresas_editar">
                        <label class="form-check-label" for="{{ $prefix }}_perm_empresas_editar">Editar Empresa</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="empresas.excluir" id="{{ $prefix }}_perm_empresas_excluir">
                        <label class="form-check-label" for="{{ $prefix }}_perm_empresas_excluir">Excluir Empresa</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="empresas.gerenciar" id="{{ $prefix }}_perm_empresas_gerenciar">
                        <label class="form-check-label" for="{{ $prefix }}_perm_empresas_gerenciar">Gerenciar Empresa</label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Usuários --}}
        <div class="mb-3">
            <h6 class="text-primary mb-2">
                <i class="fas fa-users me-1"></i>Usuários
            </h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="usuarios.criar" id="{{ $prefix }}_perm_usuarios_criar">
                        <label class="form-check-label" for="{{ $prefix }}_perm_usuarios_criar">Criar Usuário</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="usuarios.visualizar" id="{{ $prefix }}_perm_usuarios_visualizar">
                        <label class="form-check-label" for="{{ $prefix }}_perm_usuarios_visualizar">Visualizar Usuário</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="usuarios.editar" id="{{ $prefix }}_perm_usuarios_editar">
                        <label class="form-check-label" for="{{ $prefix }}_perm_usuarios_editar">Editar Usuário</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="usuarios.listar" id="{{ $prefix }}_perm_usuarios_listar">
                        <label class="form-check-label" for="{{ $prefix }}_perm_usuarios_listar">Listar Usuários</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="usuarios.excluir" id="{{ $prefix }}_perm_usuarios_excluir">
                        <label class="form-check-label" for="{{ $prefix }}_perm_usuarios_excluir">Excluir Usuário</label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dashboard --}}
        <div class="mb-3">
            <h6 class="text-primary mb-2">
                <i class="fas fa-chart-pie me-1"></i>Dashboard
            </h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="dashboard.visualizar" id="{{ $prefix }}_perm_dashboard_visualizar">
                        <label class="form-check-label" for="{{ $prefix }}_perm_dashboard_visualizar">Visualizar Dashboard</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="dashboard.relatorios" id="{{ $prefix }}_perm_dashboard_relatorios">
                        <label class="form-check-label" for="{{ $prefix }}_perm_dashboard_relatorios">Ver Relatórios no Dashboard</label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Marcas --}}
        <div class="mb-3">
            <h6 class="text-primary mb-2">
                <i class="fas fa-tags me-1"></i>Marcas
            </h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="marcas.visualizar" id="{{ $prefix }}_perm_marcas_visualizar">
                        <label class="form-check-label" for="{{ $prefix }}_perm_marcas_visualizar">Visualizar Marcas</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="marcas.gerenciar" id="{{ $prefix }}_perm_marcas_gerenciar">
                        <label class="form-check-label" for="{{ $prefix }}_perm_marcas_gerenciar">Gerenciar Marcas</label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Horários --}}
        <div class="mb-3">
            <h6 class="text-primary mb-2">
                <i class="fas fa-clock me-1"></i>Horários
            </h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="horarios.visualizar" id="{{ $prefix }}_perm_horarios_visualizar">
                        <label class="form-check-label" for="{{ $prefix }}_perm_horarios_visualizar">Visualizar Horários</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissoes[]" value="horarios.manage" id="{{ $prefix }}_perm_horarios_manage">
                        <label class="form-check-label" for="{{ $prefix }}_perm_horarios_manage">Gerenciar Horários</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-2">
        <small class="text-muted">
            <i class="fas fa-info-circle me-1"></i>
            <strong>Dica:</strong> Para administradores, selecione o perfil "Administrador" que marcará todas as permissões automaticamente
        </small>
    </div>
</div>
