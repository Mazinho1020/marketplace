<?php $__env->startSection('title', 'Tipos de Evento'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.notificacoes.index')); ?>">Notificações</a></li>
                        <li class="breadcrumb-item active">Tipos de Evento</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-format-list-bulleted"></i> Tipos de Evento
                </h4>
            </div>
        </div>
    </div>

    <!-- Filtros e Ações -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-md-3">
                                    <select class="form-control" id="filtro-categoria">
                                        <option value="">Todas as Categorias</option>
                                        <option value="pedido">Pedidos</option>
                                        <option value="pagamento">Pagamentos</option>
                                        <option value="usuario">Usuários</option>
                                        <option value="sistema">Sistema</option>
                                        <option value="marketing">Marketing</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="filtro-status">
                                        <option value="">Todos os Status</option>
                                        <option value="1">Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="filtro-prioridade">
                                        <option value="">Todas as Prioridades</option>
                                        <option value="baixa">Baixa</option>
                                        <option value="normal">Normal</option>
                                        <option value="alta">Alta</option>
                                        <option value="critica">Crítica</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="filtro-busca" placeholder="Buscar tipos...">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-end">
                            <button class="btn btn-primary" onclick="abrirModalTipo()">
                                <i class="mdi mdi-plus"></i> Novo Tipo
                            </button>
                            <button class="btn btn-outline-secondary" onclick="exportarTipos()">
                                <i class="mdi mdi-download"></i> Exportar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-format-list-bulleted float-end text-primary"></i>
                    <h6 class="text-uppercase mt-0">Total de Tipos</h6>
                    <h2 class="m-b-20" id="stats-total">0</h2>
                    <span class="badge bg-primary" id="badge-total">carregando...</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-check-circle float-end text-success"></i>
                    <h6 class="text-uppercase mt-0">Tipos Ativos</h6>
                    <h2 class="m-b-20" id="stats-ativos">0</h2>
                    <span class="badge bg-success" id="badge-ativos">carregando...</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-fire float-end text-warning"></i>
                    <h6 class="text-uppercase mt-0">Mais Usado</h6>
                    <h2 class="m-b-20" id="stats-mais-usado">-</h2>
                    <span class="badge bg-warning" id="badge-mais-usado">carregando...</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-file-document float-end text-info"></i>
                    <h6 class="text-uppercase mt-0">Templates Vinculados</h6>
                    <h2 class="m-b-20" id="stats-templates">0</h2>
                    <span class="badge bg-info" id="badge-templates">carregando...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Tipos -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tabela-tipos">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th>Descrição</th>
                                    <th>Prioridade</th>
                                    <th>Templates</th>
                                    <th>Status</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Carregado via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginação -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <span class="text-muted">Mostrando <span id="info-inicio">1</span> a <span id="info-fim">10</span> de <span id="info-total">0</span> tipos</span>
                        </div>
                        <nav>
                            <ul class="pagination mb-0" id="paginacao">
                                <!-- Paginação via AJAX -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tipo de Evento -->
<div class="modal fade" id="modalTipo" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-format-list-bulleted"></i> 
                    <span id="modal-titulo">Novo Tipo de Evento</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-tipo">
                    <input type="hidden" id="tipo-id">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Nome do Tipo</label>
                                <input type="text" class="form-control" id="tipo-nome" required placeholder="Ex: Pedido Criado">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" id="tipo-ativo">
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Categoria</label>
                                <select class="form-control" id="tipo-categoria" required>
                                    <option value="">Selecione...</option>
                                    <option value="pedido">Pedidos</option>
                                    <option value="pagamento">Pagamentos</option>
                                    <option value="usuario">Usuários</option>
                                    <option value="produto">Produtos</option>
                                    <option value="sistema">Sistema</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="suporte">Suporte</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Prioridade Padrão</label>
                                <select class="form-control" id="tipo-prioridade">
                                    <option value="baixa">Baixa</option>
                                    <option value="normal" selected>Normal</option>
                                    <option value="alta">Alta</option>
                                    <option value="critica">Crítica</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" id="tipo-descricao" rows="3" placeholder="Descreva quando este tipo de evento deve ser usado"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Chave do Evento (código único)</label>
                        <input type="text" class="form-control" id="tipo-chave" placeholder="pedido_criado, pagamento_aprovado, etc." pattern="[a-z_]+" title="Use apenas letras minúsculas e underscore">
                        <small class="form-text text-muted">Use apenas letras minúsculas e underscore. Ex: pedido_criado</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Canais Permitidos</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="canal-email" value="email" checked>
                                    <label class="form-check-label" for="canal-email">Email</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="canal-sms" value="sms">
                                    <label class="form-check-label" for="canal-sms">SMS</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="canal-push" value="push">
                                    <label class="form-check-label" for="canal-push">Push Notification</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="canal-in-app" value="in_app">
                                    <label class="form-check-label" for="canal-in-app">In-App</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Configurações</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo-permite-agendamento">
                                    <label class="form-check-label" for="tipo-permite-agendamento">
                                        Permite agendamento
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo-permite-retry">
                                    <label class="form-check-label" for="tipo-permite-retry">
                                        Permite reenvio automático
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tipo-requer-confirmacao">
                                    <label class="form-check-label" for="tipo-requer-confirmacao">
                                        Requer confirmação
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Variáveis Disponíveis</label>
                        <textarea class="form-control" id="tipo-variaveis" rows="3" placeholder="Lista das variáveis disponíveis separadas por vírgula: {nome_usuario}, {pedido_numero}, {valor_total}"></textarea>
                        <small class="form-text text-muted">Use chaves para as variáveis. Ex: {nome_usuario}, {pedido_numero}</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="previewTipo()">
                    <i class="mdi mdi-eye"></i> Preview
                </button>
                <button type="button" class="btn btn-primary" onclick="salvarTipo()">
                    <i class="mdi mdi-content-save"></i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    carregarTipos();
    carregarEstatisticas();
    
    // Filtros
    document.getElementById('filtro-categoria').addEventListener('change', carregarTipos);
    document.getElementById('filtro-status').addEventListener('change', carregarTipos);
    document.getElementById('filtro-prioridade').addEventListener('change', carregarTipos);
    document.getElementById('filtro-busca').addEventListener('input', debounce(carregarTipos, 500));
    
    // Auto-gerar chave baseada no nome
    document.getElementById('tipo-nome').addEventListener('input', function() {
        const nome = this.value.toLowerCase()
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, '_');
        document.getElementById('tipo-chave').value = nome;
    });
});

function carregarTipos(pagina = 1) {
    const filtros = {
        categoria: document.getElementById('filtro-categoria').value,
        status: document.getElementById('filtro-status').value,
        prioridade: document.getElementById('filtro-prioridade').value,
        busca: document.getElementById('filtro-busca').value,
        pagina: pagina
    };

    fetch('/admin/notificacoes/api/tipos-evento?' + new URLSearchParams(filtros))
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#tabela-tipos tbody');
            tbody.innerHTML = '';
            
            if (data.data && data.data.length > 0) {
                data.data.forEach(tipo => {
                    const row = `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-${getIconeCategoria(tipo.categoria)} me-2 text-${getCorCategoria(tipo.categoria)}"></i>
                                    <div>
                                        <strong>${tipo.nome}</strong>
                                        <br><small class="text-muted">${tipo.chave || ''}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-${getCorCategoria(tipo.categoria)}">${tipo.categoria}</span>
                            </td>
                            <td>
                                <small>${tipo.descricao || '-'}</small>
                            </td>
                            <td>
                                <span class="badge bg-${getCorPrioridade(tipo.prioridade_padrao)}">${tipo.prioridade_padrao}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">${tipo.templates_count || 0}</span>
                            </td>
                            <td>
                                <span class="badge bg-${tipo.ativo ? 'success' : 'danger'}">
                                    ${tipo.ativo ? 'Ativo' : 'Inativo'}
                                </span>
                            </td>
                            <td>${formatarData(tipo.created_at)}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editarTipo(${tipo.id})" title="Editar">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="clonarTipo(${tipo.id})" title="Clonar">
                                        <i class="mdi mdi-content-copy"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="verTemplates(${tipo.id})" title="Ver Templates">
                                        <i class="mdi mdi-file-document"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="excluirTipo(${tipo.id})" title="Excluir">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="mdi mdi-information h4 text-muted"></i>
                            <p class="text-muted">Nenhum tipo de evento encontrado</p>
                        </td>
                    </tr>
                `;
            }
            
            atualizarPaginacao(data);
        })
        .catch(error => {
            console.error('Erro ao carregar tipos:', error);
            mostrarAlerta('Erro ao carregar tipos de evento', 'danger');
        });
}

function carregarEstatisticas() {
    fetch('/admin/notificacoes/api/tipos-evento/estatisticas')
        .then(response => response.json())
        .then(data => {
            document.getElementById('stats-total').textContent = data.total || 0;
            document.getElementById('stats-ativos').textContent = data.ativos || 0;
            document.getElementById('stats-mais-usado').textContent = data.mais_usado?.nome || '-';
            document.getElementById('stats-templates').textContent = data.total_templates || 0;
            
            document.getElementById('badge-total').textContent = 'tipos cadastrados';
            document.getElementById('badge-ativos').textContent = 'em uso';
            document.getElementById('badge-mais-usado').textContent = data.mais_usado?.uso_count || '0' + ' usos';
            document.getElementById('badge-templates').textContent = 'vinculados';
        })
        .catch(error => {
            console.error('Erro ao carregar estatísticas:', error);
        });
}

function abrirModalTipo(id = null) {
    document.getElementById('tipo-id').value = id || '';
    document.getElementById('modal-titulo').textContent = id ? 'Editar Tipo de Evento' : 'Novo Tipo de Evento';
    
    if (id) {
        carregarTipo(id);
    } else {
        document.getElementById('form-tipo').reset();
        // Marcar email como padrão
        document.getElementById('canal-email').checked = true;
    }
    
    new bootstrap.Modal(document.getElementById('modalTipo')).show();
}

function carregarTipo(id) {
    fetch(`/admin/notificacoes/api/tipos-evento/${id}`)
        .then(response => response.json())
        .then(tipo => {
            document.getElementById('tipo-nome').value = tipo.nome;
            document.getElementById('tipo-categoria').value = tipo.categoria;
            document.getElementById('tipo-prioridade').value = tipo.prioridade_padrao;
            document.getElementById('tipo-ativo').value = tipo.ativo ? '1' : '0';
            document.getElementById('tipo-descricao').value = tipo.descricao || '';
            document.getElementById('tipo-chave').value = tipo.chave || '';
            document.getElementById('tipo-variaveis').value = tipo.variaveis_disponiveis || '';
            
            // Canais permitidos
            const canais = tipo.canais_permitidos || ['email'];
            document.getElementById('canal-email').checked = canais.includes('email');
            document.getElementById('canal-sms').checked = canais.includes('sms');
            document.getElementById('canal-push').checked = canais.includes('push');
            document.getElementById('canal-in-app').checked = canais.includes('in_app');
            
            // Configurações
            document.getElementById('tipo-permite-agendamento').checked = tipo.permite_agendamento || false;
            document.getElementById('tipo-permite-retry').checked = tipo.permite_retry || false;
            document.getElementById('tipo-requer-confirmacao').checked = tipo.requer_confirmacao || false;
        })
        .catch(error => {
            console.error('Erro ao carregar tipo:', error);
            mostrarAlerta('Erro ao carregar dados do tipo', 'danger');
        });
}

function salvarTipo() {
    const id = document.getElementById('tipo-id').value;
    const url = id ? `/admin/notificacoes/api/tipos-evento/${id}` : '/admin/notificacoes/api/tipos-evento';
    const method = id ? 'PUT' : 'POST';
    
    // Coletar canais selecionados
    const canais = [];
    if (document.getElementById('canal-email').checked) canais.push('email');
    if (document.getElementById('canal-sms').checked) canais.push('sms');
    if (document.getElementById('canal-push').checked) canais.push('push');
    if (document.getElementById('canal-in-app').checked) canais.push('in_app');
    
    const dados = {
        nome: document.getElementById('tipo-nome').value,
        categoria: document.getElementById('tipo-categoria').value,
        prioridade_padrao: document.getElementById('tipo-prioridade').value,
        ativo: document.getElementById('tipo-ativo').value === '1',
        descricao: document.getElementById('tipo-descricao').value,
        chave: document.getElementById('tipo-chave').value,
        variaveis_disponiveis: document.getElementById('tipo-variaveis').value,
        canais_permitidos: canais,
        permite_agendamento: document.getElementById('tipo-permite-agendamento').checked,
        permite_retry: document.getElementById('tipo-permite-retry').checked,
        requer_confirmacao: document.getElementById('tipo-requer-confirmacao').checked
    };
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(dados)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalTipo')).hide();
            carregarTipos();
            carregarEstatisticas();
            mostrarAlerta('Tipo de evento salvo com sucesso!', 'success');
        } else {
            mostrarAlerta('Erro ao salvar tipo: ' + (data.message || 'Erro desconhecido'), 'danger');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarAlerta('Erro ao salvar tipo de evento', 'danger');
    });
}

function editarTipo(id) {
    abrirModalTipo(id);
}

function clonarTipo(id) {
    if (confirm('Deseja clonar este tipo de evento?')) {
        fetch(`/admin/notificacoes/api/tipos-evento/${id}/clonar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                carregarTipos();
                carregarEstatisticas();
                mostrarAlerta('Tipo clonado com sucesso!', 'success');
            } else {
                mostrarAlerta('Erro ao clonar tipo: ' + (data.message || 'Erro desconhecido'), 'danger');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarAlerta('Erro ao clonar tipo', 'danger');
        });
    }
}

function excluirTipo(id) {
    if (confirm('Tem certeza que deseja excluir este tipo de evento? Esta ação não pode ser desfeita.')) {
        fetch(`/admin/notificacoes/api/tipos-evento/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                carregarTipos();
                carregarEstatisticas();
                mostrarAlerta('Tipo excluído com sucesso!', 'success');
            } else {
                mostrarAlerta('Erro ao excluir tipo: ' + (data.message || 'Erro desconhecido'), 'danger');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarAlerta('Erro ao excluir tipo', 'danger');
        });
    }
}

function verTemplates(id) {
    window.open(`/admin/notificacoes/templates?tipo=${id}`, '_blank');
}

function previewTipo() {
    const nome = document.getElementById('tipo-nome').value;
    const categoria = document.getElementById('tipo-categoria').value;
    const descricao = document.getElementById('tipo-descricao').value;
    
    if (!nome) {
        mostrarAlerta('Preencha ao menos o nome para visualizar', 'warning');
        return;
    }
    
    const preview = `
        <div class="alert alert-info">
            <h6><i class="mdi mdi-${getIconeCategoria(categoria)}"></i> ${nome}</h6>
            <p><strong>Categoria:</strong> ${categoria}</p>
            <p><strong>Descrição:</strong> ${descricao || 'Sem descrição'}</p>
        </div>
    `;
    
    // Aqui você pode implementar um modal de preview ou mostrar inline
    mostrarAlerta('Preview: ' + nome + ' (' + categoria + ')', 'info');
}

function exportarTipos() {
    window.location.href = '/admin/notificacoes/tipos-evento/exportar';
}

function getIconeCategoria(categoria) {
    const icones = {
        'pedido': 'cart',
        'pagamento': 'credit-card',
        'usuario': 'account',
        'produto': 'cube',
        'sistema': 'cog',
        'marketing': 'bullhorn',
        'suporte': 'help-circle'
    };
    return icones[categoria] || 'circle';
}

function getCorCategoria(categoria) {
    const cores = {
        'pedido': 'primary',
        'pagamento': 'success',
        'usuario': 'info',
        'produto': 'warning',
        'sistema': 'secondary',
        'marketing': 'danger',
        'suporte': 'dark'
    };
    return cores[categoria] || 'secondary';
}

function getCorPrioridade(prioridade) {
    const cores = {
        'baixa': 'secondary',
        'normal': 'primary',
        'alta': 'warning',
        'critica': 'danger'
    };
    return cores[prioridade] || 'secondary';
}

function formatarData(data) {
    return new Date(data).toLocaleDateString('pt-BR');
}

function atualizarPaginacao(data) {
    document.getElementById('info-inicio').textContent = data.from || 0;
    document.getElementById('info-fim').textContent = data.to || 0;
    document.getElementById('info-total').textContent = data.total || 0;
}

function mostrarAlerta(mensagem, tipo) {
    // Implementar sistema de alertas
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

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/notificacoes/tipos.blade.php ENDPATH**/ ?>