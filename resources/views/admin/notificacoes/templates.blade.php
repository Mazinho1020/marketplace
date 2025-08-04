@extends('layouts.admin')

@section('title', 'Templates de Notificação')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.notificacoes.index') }}">Notificações</a></li>
                        <li class="breadcrumb-item active">Templates</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-file-document-edit"></i> Templates de Notificação
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
                                    <select class="form-control" id="filtro-canal">
                                        <option value="">Todos os Canais</option>
                                        <option value="email">Email</option>
                                        <option value="sms">SMS</option>
                                        <option value="push">Push</option>
                                        <option value="in_app">In-App</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="filtro-tipo">
                                        <option value="">Todos os Tipos</option>
                                        <option value="pedido_criado">Pedido Criado</option>
                                        <option value="pagamento_aprovado">Pagamento Aprovado</option>
                                        <option value="produto_baixo_estoque">Baixo Estoque</option>
                                        <option value="cliente_novo">Cliente Novo</option>
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
                                    <input type="text" class="form-control" id="filtro-busca" placeholder="Buscar...">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-end">
                            <button class="btn btn-primary" onclick="abrirModalTemplate()">
                                <i class="mdi mdi-plus"></i> Novo Template
                            </button>
                            <button class="btn btn-outline-secondary" onclick="exportarTemplates()">
                                <i class="mdi mdi-download"></i> Exportar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Templates -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tabela-templates">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Canal</th>
                                    <th>Tipo de Evento</th>
                                    <th>Assunto/Título</th>
                                    <th>Status</th>
                                    <th>Variáveis</th>
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
                            <span class="text-muted">Mostrando <span id="info-inicio">1</span> a <span id="info-fim">10</span> de <span id="info-total">0</span> templates</span>
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

<!-- Modal Template -->
<div class="modal fade" id="modalTemplate" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-file-document-edit"></i> 
                    <span id="modal-titulo">Novo Template</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-template">
                    <input type="hidden" id="template-id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nome do Template</label>
                                <input type="text" class="form-control" id="template-nome" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Canal</label>
                                <select class="form-control" id="template-canal" required>
                                    <option value="">Selecione...</option>
                                    <option value="email">Email</option>
                                    <option value="sms">SMS</option>
                                    <option value="push">Push Notification</option>
                                    <option value="in_app">In-App</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipo de Evento</label>
                                <select class="form-control" id="template-tipo-evento" required>
                                    <option value="">Selecione...</option>
                                    <!-- Carregado via AJAX -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" id="template-ativo">
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assunto/Título</label>
                        <input type="text" class="form-control" id="template-assunto" placeholder="Ex: Novo pedido #{pedido_numero}">
                        <small class="form-text text-muted">Use variáveis entre chaves: {nome_usuario}, {pedido_numero}, etc.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Conteúdo do Template</label>
                        <textarea class="form-control" id="template-conteudo" rows="8" placeholder="Digite o conteúdo do template aqui..."></textarea>
                        <small class="form-text text-muted">
                            Variáveis disponíveis: {nome_usuario}, {email_usuario}, {pedido_numero}, {valor_total}, {data_pedido}
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Prioridade</label>
                                <select class="form-control" id="template-prioridade">
                                    <option value="baixa">Baixa</option>
                                    <option value="normal" selected>Normal</option>
                                    <option value="alta">Alta</option>
                                    <option value="critica">Crítica</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Limite de Tentativas</label>
                                <input type="number" class="form-control" id="template-tentativas" value="3" min="1" max="10">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="template-permite-reenvio">
                            <label class="form-check-label" for="template-permite-reenvio">
                                Permitir reenvio automático
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="testarTemplate()">
                    <i class="mdi mdi-test-tube"></i> Testar
                </button>
                <button type="button" class="btn btn-primary" onclick="salvarTemplate()">
                    <i class="mdi mdi-content-save"></i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    carregarTemplates();
    carregarTiposEvento();
    
    // Filtros
    document.getElementById('filtro-canal').addEventListener('change', carregarTemplates);
    document.getElementById('filtro-tipo').addEventListener('change', carregarTemplates);
    document.getElementById('filtro-status').addEventListener('change', carregarTemplates);
    document.getElementById('filtro-busca').addEventListener('input', debounce(carregarTemplates, 500));
});

function carregarTemplates(pagina = 1) {
    const filtros = {
        canal: document.getElementById('filtro-canal').value,
        tipo: document.getElementById('filtro-tipo').value,
        status: document.getElementById('filtro-status').value,
        busca: document.getElementById('filtro-busca').value,
        pagina: pagina
    };

    fetch('/admin/notificacoes/api/templates?' + new URLSearchParams(filtros))
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#tabela-templates tbody');
            tbody.innerHTML = '';
            
            data.data.forEach(template => {
                const row = `
                    <tr>
                        <td>
                            <strong>${template.nome}</strong>
                            <br><small class="text-muted">${template.descricao || ''}</small>
                        </td>
                        <td>
                            <span class="badge bg-${getCorCanal(template.canal)}">${template.canal.toUpperCase()}</span>
                        </td>
                        <td>${template.tipo_evento_nome}</td>
                        <td>${template.assunto || template.titulo || '-'}</td>
                        <td>
                            <span class="badge bg-${template.ativo ? 'success' : 'danger'}">
                                ${template.ativo ? 'Ativo' : 'Inativo'}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-info" onclick="verVariaveis(${template.id})">
                                <i class="mdi mdi-code-braces"></i> ${template.variaveis_count || 0}
                            </button>
                        </td>
                        <td>${formatarData(template.created_at)}</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary" onclick="editarTemplate(${template.id})">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" onclick="clonarTemplate(${template.id})">
                                    <i class="mdi mdi-content-copy"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" onclick="testarTemplate(${template.id})">
                                    <i class="mdi mdi-test-tube"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="excluirTemplate(${template.id})">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
            
            atualizarPaginacao(data);
        })
        .catch(error => console.error('Erro ao carregar templates:', error));
}

function carregarTiposEvento() {
    fetch('/admin/notificacoes/api/tipos-evento')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('template-tipo-evento');
            select.innerHTML = '<option value="">Selecione...</option>';
            
            data.forEach(tipo => {
                select.innerHTML += `<option value="${tipo.id}">${tipo.nome}</option>`;
            });
        });
}

function abrirModalTemplate(id = null) {
    document.getElementById('template-id').value = id || '';
    document.getElementById('modal-titulo').textContent = id ? 'Editar Template' : 'Novo Template';
    
    if (id) {
        carregarTemplate(id);
    } else {
        document.getElementById('form-template').reset();
    }
    
    new bootstrap.Modal(document.getElementById('modalTemplate')).show();
}

function carregarTemplate(id) {
    fetch(`/admin/notificacoes/api/templates/${id}`)
        .then(response => response.json())
        .then(template => {
            document.getElementById('template-nome').value = template.nome;
            document.getElementById('template-canal').value = template.canal;
            document.getElementById('template-tipo-evento').value = template.notificacao_tipo_evento_id;
            document.getElementById('template-ativo').value = template.ativo ? '1' : '0';
            document.getElementById('template-assunto').value = template.assunto || template.titulo || '';
            document.getElementById('template-conteudo').value = template.conteudo;
            document.getElementById('template-prioridade').value = template.prioridade || 'normal';
            document.getElementById('template-tentativas').value = template.max_tentativas || 3;
            document.getElementById('template-permite-reenvio').checked = template.permite_reenvio || false;
        });
}

function salvarTemplate() {
    const id = document.getElementById('template-id').value;
    const url = id ? `/admin/notificacoes/api/templates/${id}` : '/admin/notificacoes/api/templates';
    const method = id ? 'PUT' : 'POST';
    
    const dados = {
        nome: document.getElementById('template-nome').value,
        canal: document.getElementById('template-canal').value,
        notificacao_tipo_evento_id: document.getElementById('template-tipo-evento').value,
        ativo: document.getElementById('template-ativo').value === '1',
        assunto: document.getElementById('template-assunto').value,
        conteudo: document.getElementById('template-conteudo').value,
        prioridade: document.getElementById('template-prioridade').value,
        max_tentativas: document.getElementById('template-tentativas').value,
        permite_reenvio: document.getElementById('template-permite-reenvio').checked
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
            bootstrap.Modal.getInstance(document.getElementById('modalTemplate')).hide();
            carregarTemplates();
            mostrarAlerta('Template salvo com sucesso!', 'success');
        } else {
            mostrarAlerta('Erro ao salvar template: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarAlerta('Erro ao salvar template', 'danger');
    });
}

function editarTemplate(id) {
    abrirModalTemplate(id);
}

function clonarTemplate(id) {
    fetch(`/admin/notificacoes/api/templates/${id}/clonar`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            carregarTemplates();
            mostrarAlerta('Template clonado com sucesso!', 'success');
        }
    });
}

function testarTemplate(id = null) {
    const templateId = id || document.getElementById('template-id').value;
    if (!templateId) {
        mostrarAlerta('Salve o template antes de testá-lo', 'warning');
        return;
    }
    
    window.open(`/admin/notificacoes/templates/${templateId}/teste`, '_blank');
}

function excluirTemplate(id) {
    if (confirm('Tem certeza que deseja excluir este template?')) {
        fetch(`/admin/notificacoes/api/templates/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                carregarTemplates();
                mostrarAlerta('Template excluído com sucesso!', 'success');
            }
        });
    }
}

function verVariaveis(id) {
    window.open(`/admin/notificacoes/templates/${id}/variaveis`, '_blank');
}

function exportarTemplates() {
    window.location.href = '/admin/notificacoes/templates/exportar';
}

function getCorCanal(canal) {
    const cores = {
        'email': 'primary',
        'sms': 'success',
        'push': 'warning',
        'in_app': 'info'
    };
    return cores[canal] || 'secondary';
}

function formatarData(data) {
    return new Date(data).toLocaleDateString('pt-BR');
}

function atualizarPaginacao(data) {
    document.getElementById('info-inicio').textContent = data.from || 0;
    document.getElementById('info-fim').textContent = data.to || 0;
    document.getElementById('info-total').textContent = data.total || 0;
    
    // Implementar paginação aqui se necessário
}

function mostrarAlerta(mensagem, tipo) {
    // Implementar sistema de alertas
    alert(mensagem);
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
@endpush
@endsection
