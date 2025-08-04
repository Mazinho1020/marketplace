<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light p-3 rounded">
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-decoration-none">
                    <i class="mdi mdi-home"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('admin.notificacoes.index')); ?>" class="text-decoration-none">
                    Notificações
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Usuários</li>
        </ol>
    </nav>

    <!-- Título da Página -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h3><i class="mdi mdi-account-group"></i> Gerenciar Usuários</h3>
            <p class="text-muted">Gerencie usuários e suas preferências de notificação</p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary" onclick="adicionarUsuario()">
                <i class="mdi mdi-plus"></i> Novo Usuário
            </button>
            <button class="btn btn-outline-secondary" onclick="exportarDados()">
                <i class="mdi mdi-download"></i> Exportar
            </button>
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-account-group float-end text-primary"></i>
                    <h6 class="text-uppercase mt-0">Total de Usuários</h6>
                    <h2 class="m-b-20" id="stats-total-usuarios">0</h2>
                    <span class="badge bg-primary">cadastrados</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-account-check float-end text-success"></i>
                    <h6 class="text-uppercase mt-0">Usuários Ativos</h6>
                    <h2 class="m-b-20" id="stats-usuarios-ativos">0</h2>
                    <span class="badge bg-success">online</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-bell float-end text-info"></i>
                    <h6 class="text-uppercase mt-0">Com Notificações</h6>
                    <h2 class="m-b-20" id="stats-com-notificacoes">0</h2>
                    <span class="badge bg-info">engajados</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-chart-line float-end text-warning"></i>
                    <h6 class="text-uppercase mt-0">Taxa de Ativação</h6>
                    <h2 class="m-b-20" id="stats-taxa-ativacao">0%</h2>
                    <span class="badge bg-warning">conversão</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros e Busca -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Buscar Usuários</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="busca-usuario" placeholder="Nome, email ou telefone...">
                            <button class="btn btn-outline-secondary" type="button" onclick="buscarUsuario()">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-2">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-select" id="filtro-status" onchange="filtrarUsuarios()">
                            <option value="">Todos</option>
                            <option value="ativo">Ativos</option>
                            <option value="inativo">Inativos</option>
                            <option value="bloqueado">Bloqueados</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-2">
                    <div class="form-group">
                        <label>Tipo</label>
                        <select class="form-select" id="filtro-tipo" onchange="filtrarUsuarios()">
                            <option value="">Todos</option>
                            <option value="admin">Administrador</option>
                            <option value="cliente">Cliente</option>
                            <option value="empresa">Empresa</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-2">
                    <div class="form-group">
                        <label>Preferências</label>
                        <select class="form-select" id="filtro-preferencias" onchange="filtrarUsuarios()">
                            <option value="">Todas</option>
                            <option value="email">Email habilitado</option>
                            <option value="sms">SMS habilitado</option>
                            <option value="push">Push habilitado</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="d-grid">
                            <button class="btn btn-secondary" onclick="limparFiltros()">
                                <i class="mdi mdi-filter-remove"></i> Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Usuários -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="card-title mb-0">Lista de Usuários</h6>
                </div>
                <div class="col-auto">
                    <small class="text-muted">
                        Exibindo <span id="info-inicio">0</span> - <span id="info-fim">0</span> de <span id="info-total">0</span> usuários
                    </small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Usuário</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Preferências</th>
                            <th>Notificações</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="usuarios-tbody">
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="text-muted mt-2">Carregando usuários...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <nav aria-label="Paginação">
                <ul class="pagination pagination-sm mb-0" id="paginacao">
                    <!-- Paginação será carregada via JavaScript -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Modal Detalhes do Usuário -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUsuarioLabel">
                    <i class="mdi mdi-account-details"></i> Detalhes do Usuário
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-usuario-body">
                <!-- Conteúdo carregado via JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Enviar Notificação -->
<div class="modal fade" id="modalNotificacao" tabindex="-1" aria-labelledby="modalNotificacaoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNotificacaoLabel">
                    <i class="mdi mdi-send"></i> Enviar Notificação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-notificacao" onsubmit="event.preventDefault(); enviarNotificacao();">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Título *</label>
                                <input type="text" class="form-control" name="titulo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Tipo *</label>
                                <select class="form-select" name="tipo" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="info">Informação</option>
                                    <option value="warning">Aviso</option>
                                    <option value="success">Sucesso</option>
                                    <option value="error">Erro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Mensagem *</label>
                        <textarea class="form-control" name="mensagem" rows="4" required placeholder="Digite a mensagem da notificação..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Canais de Envio</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="canais[]" value="email" id="canal-email">
                                    <label class="form-check-label" for="canal-email">
                                        <i class="mdi mdi-email"></i> Email
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="canais[]" value="sms" id="canal-sms">
                                    <label class="form-check-label" for="canal-sms">
                                        <i class="mdi mdi-cellphone"></i> SMS
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="canais[]" value="push" id="canal-push">
                                    <label class="form-check-label" for="canal-push">
                                        <i class="mdi mdi-bell"></i> Push Notification
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agendar">
                                    <label class="form-check-label" for="agendar">
                                        <i class="mdi mdi-clock"></i> Agendar envio
                                    </label>
                                </div>
                                <div id="data-agendamento" class="d-none mt-2">
                                    <input type="datetime-local" class="form-control" name="agendamento">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="mdi mdi-information"></i>
                        <strong>Atenção:</strong> A notificação será enviada apenas pelos canais que o usuário tem habilitado em suas preferências.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-send"></i> Enviar Notificação
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let usuarioAtual = null;
let paginaAtual = 1;
let porPagina = 10;

document.addEventListener('DOMContentLoaded', function() {
    carregarUsuarios();
    carregarEstatisticas();
    
    // Eventos
    document.getElementById('busca-usuario').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            buscarUsuario();
        }
    });

    // Eventos de filtro
    document.getElementById('filtro-status')?.addEventListener('change', filtrarUsuarios);
    document.getElementById('filtro-busca')?.addEventListener('input', function() {
        clearTimeout(this.timeout);
        this.timeout = setTimeout(filtrarUsuarios, 500);
    });
    
    document.getElementById('agendar').addEventListener('change', function() {
        const dataDiv = document.getElementById('data-agendamento');
        if (this.checked) {
            dataDiv.classList.remove('d-none');
        } else {
            dataDiv.classList.add('d-none');
        }
    });
});

function carregarUsuarios(pagina = 1) {
    // Construir parâmetros de filtro
    const filtros = {
        status: document.getElementById('filtro-status')?.value || '',
        busca: document.getElementById('busca-usuario')?.value || '',
        pagina: pagina
    };

    const params = new URLSearchParams(filtros).toString();

    fetch(`/admin/notificacoes/api/usuarios?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Erro ao carregar usuários:', data.message);
                mostrarMensagem('Erro ao carregar usuários', 'danger');
                return;
            }

            const tbody = document.getElementById('usuarios-tbody');
            tbody.innerHTML = '';

            if (data.data && data.data.length > 0) {
                data.data.forEach(usuario => {
                    renderizarUsuario(usuario, tbody);
                });
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="mdi mdi-account-search h4 text-muted"></i>
                            <p class="text-muted">Nenhum usuário encontrado</p>
                        </td>
                    </tr>
                `;
            }

            // Atualizar informações de paginação
            atualizarPaginacao(data);
        })
        .catch(error => {
            console.error('Erro ao carregar usuários:', error);
            mostrarMensagem('Erro ao conectar com o servidor', 'danger');
        });
}

function carregarEstatisticas() {
    fetch('/admin/notificacoes/api/usuarios/estatisticas')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Erro ao carregar estatísticas:', data.message);
                return;
            }

            // Atualizar cards de estatísticas
            document.getElementById('stats-total-usuarios').textContent = data.total_usuarios;
            document.getElementById('stats-usuarios-ativos').textContent = data.usuarios_ativos;
            document.getElementById('stats-com-notificacoes').textContent = data.usuarios_com_notificacoes;
            document.getElementById('stats-taxa-ativacao').textContent = `${data.taxa_ativacao}%`;

            console.log('Estatísticas carregadas:', data);
        })
        .catch(error => {
            console.error('Erro ao carregar estatísticas:', error);
        });
}

// Função para gerar avatar com iniciais
function gerarAvatar(nome, tipo, status) {
    const coresAtivo = {
        'admin': '#dc3545',
        'empresa': '#28a745', 
        'cliente': '#007bff'
    };
    
    const iniciais = nome.split(' ')
        .map(palavra => palavra.charAt(0).toUpperCase())
        .slice(0, 2)
        .join('');
        
    let cor = coresAtivo[tipo] || '#007bff';
    
    // Usar cor cinza para usuários inativos ou bloqueados
    if (status === 'inativo' || status === 'bloqueado') {
        cor = '#6c757d';
    }
    
    return `<div class="avatar-circle bg-color rounded-circle me-2 d-flex align-items-center justify-content-center text-white" 
                 style="width: 40px; height: 40px; background-color: ${cor}; font-size: 14px; font-weight: bold;">
                ${iniciais}
            </div>`;
}

function renderizarUsuario(usuario, tbody) {
    const statusColors = {
        'ativo': 'success',
        'inativo': 'warning', 
        'bloqueado': 'danger',
        'pendente': 'info'
    };
    
    const tipoColors = {
        'admin': 'danger',
        'empresa': 'primary',
        'cliente': 'info'
    };

    // Criar badges das preferências
    const preferenciasHtml = Object.entries(usuario.preferencias || {})
        .filter(([canal, ativo]) => ativo)
        .map(([canal, ativo]) => {
            const canalIcons = {
                'email': 'email',
                'sms': 'cellphone',
                'push': 'bell',
                'in_app': 'application'
            };
            return `<span class="badge bg-secondary me-1" title="${canal.toUpperCase()}">
                        <i class="mdi mdi-${canalIcons[canal]}"></i>
                    </span>`;
        }).join('');

    const row = `
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    ${gerarAvatar(usuario.nome, usuario.tipo, usuario.status)}
                    <div>
                        <h6 class="mb-0">${usuario.nome}</h6>
                        <small class="text-muted">${usuario.email}</small>
                        ${usuario.telefone ? `<br><small class="text-muted">${usuario.telefone}</small>` : ''}
                    </div>
                </div>
            </td>
            <td>
                <span class="badge bg-${tipoColors[usuario.tipo] || 'secondary'}">${usuario.tipo}</span>
                ${usuario.cargo ? `<br><small class="text-muted">${usuario.cargo}</small>` : ''}
            </td>
            <td>
                <span class="badge bg-${statusColors[usuario.status] || 'secondary'}">${usuario.status}</span>
                <br><small class="text-muted">${usuario.ultimo_acesso || 'Nunca'}</small>
            </td>
            <td>
                ${preferenciasHtml || '<span class="text-muted">Nenhuma</span>'}
            </td>
            <td>
                <small>
                    <i class="mdi mdi-send text-primary"></i> ${usuario.notificacoes?.enviadas || 0}<br>
                    <i class="mdi mdi-eye text-success"></i> ${usuario.notificacoes?.lidas || 0}<br>
                    <i class="mdi mdi-clock text-warning"></i> ${usuario.notificacoes?.pendentes || 0}
                </small>
            </td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" onclick="verDetalhes(${usuario.id})" title="Ver Detalhes">
                        <i class="mdi mdi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="enviarNotificacaoUsuario(${usuario.id})" title="Enviar Notificação">
                        <i class="mdi mdi-send"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="editarPreferencias(${usuario.id})" title="Editar Preferências">
                        <i class="mdi mdi-cog"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
    
    tbody.innerHTML += row;
}

function verDetalhes(usuarioId) {
    fetch(`/admin/notificacoes/api/usuarios/detalhes/${usuarioId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                mostrarMensagem('Erro ao carregar detalhes do usuário', 'danger');
                return;
            }

            const usuario = data.usuario;
            const iniciais = usuario.nome.split(' ')
                .map(palavra => palavra.charAt(0).toUpperCase())
                .slice(0, 2)
                .join('');

            let detalhes = `
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="avatar-circle bg-primary text-white rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; font-size: 2rem; font-weight: bold;">
                            ${iniciais}
                        </div>
                        <h5>${usuario.nome}</h5>
                        <span class="badge bg-success mb-3">${usuario.status || 'ativo'}</span>
                        ${usuario.cargo ? `<p class="text-muted">${usuario.cargo}</p>` : ''}
                    </div>
                    <div class="col-md-8">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6><i class="mdi mdi-email"></i> Email</h6>
                                <p class="text-muted">${usuario.email}</p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="mdi mdi-phone"></i> Telefone</h6>
                                <p class="text-muted">${usuario.telefone || 'Não informado'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('modal-usuario-body').innerHTML = detalhes;
            new bootstrap.Modal(document.getElementById('modalUsuario')).show();
        })
        .catch(error => {
            console.error('Erro ao carregar detalhes:', error);
            mostrarMensagem('Erro ao conectar com o servidor', 'danger');
        });
}

function enviarNotificacaoUsuario(usuarioId) {
    usuarioAtual = usuarioId;
    new bootstrap.Modal(document.getElementById('modalNotificacao')).show();
}

function enviarNotificacao() {
    const form = document.getElementById('form-notificacao');
    const formData = new FormData(form);
    
    // Simular envio
    mostrarMensagem('Notificação enviada com sucesso!', 'success');
    bootstrap.Modal.getInstance(document.getElementById('modalNotificacao')).hide();
    form.reset();
}

function buscarUsuario() {
    carregarUsuarios(1);
}

function filtrarUsuarios() {
    carregarUsuarios(1);
}

function limparFiltros() {
    document.getElementById('filtro-status').value = '';
    document.getElementById('filtro-tipo').value = '';
    document.getElementById('filtro-preferencias').value = '';
    document.getElementById('busca-usuario').value = '';
    carregarUsuarios(1);
}

function mostrarMensagem(mensagem, tipo = 'info') {
    const cores = {
        'success': 'success',
        'danger': 'danger', 
        'warning': 'warning',
        'info': 'info'
    };
    
    const popup = document.createElement('div');
    popup.className = `alert alert-${cores[tipo]} alert-dismissible fade show position-fixed`;
    popup.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
    popup.innerHTML = `
        ${mensagem}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(popup);
    
    setTimeout(() => {
        if (popup.parentNode) {
            popup.parentNode.removeChild(popup);
        }
    }, 5000);
}

function atualizarPaginacao(data) {
    const infoInicio = document.getElementById('info-inicio');
    const infoFim = document.getElementById('info-fim');
    const infoTotal = document.getElementById('info-total');
    
    if (infoInicio) infoInicio.textContent = data.from || 0;
    if (infoFim) infoFim.textContent = data.to || 0;
    if (infoTotal) infoTotal.textContent = data.total || 0;
}

function adicionarUsuario() {
    mostrarMensagem('Funcionalidade em desenvolvimento', 'info');
}

function exportarDados() {
    mostrarMensagem('Preparando exportação...', 'info');
}

function editarPreferencias(usuarioId) {
    mostrarMensagem('Funcionalidade em desenvolvimento', 'info');
}
</script>

<style>
.tilebox-one {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.avatar-circle {
    flex-shrink: 0;
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/notificacoes/usuarios.blade.php ENDPATH**/ ?>