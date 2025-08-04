@extends('layouts.admin')

@section('title', 'Aplicações')

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
                        <li class="breadcrumb-item active">Aplicações</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-application"></i> Aplicações
                </h4>
            </div>
        </div>
    </div>

    <!-- Informações sobre Aplicações -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="mdi mdi-information"></i> Sobre as Aplicações</h5>
                <p class="mb-0">
                    As aplicações representam os diferentes sistemas ou módulos que podem enviar notificações. 
                    Cada aplicação tem suas próprias configurações e permissões para usar o sistema de notificações.
                </p>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-application float-end text-primary"></i>
                    <h6 class="text-uppercase mt-0">Total Aplicações</h6>
                    <h2 class="m-b-20" id="stats-total">5</h2>
                    <span class="badge bg-primary">ativas</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-send float-end text-success"></i>
                    <h6 class="text-uppercase mt-0">Notificações Hoje</h6>
                    <h2 class="m-b-20" id="stats-hoje">1,234</h2>
                    <span class="badge bg-success">+12%</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-crown float-end text-warning"></i>
                    <h6 class="text-uppercase mt-0">Mais Ativa</h6>
                    <h2 class="m-b-20" id="stats-mais-ativa">E-commerce</h2>
                    <span class="badge bg-warning">567 envios</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-check-circle float-end text-info"></i>
                    <h6 class="text-uppercase mt-0">Taxa Sucesso</h6>
                    <h2 class="m-b-20" id="stats-sucesso">98.5%</h2>
                    <span class="badge bg-info">excelente</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Aplicações -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-view-list"></i> Aplicações Registradas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row" id="aplicacoes-container">
                        <!-- Carregado via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Configurações de Canal -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-settings"></i> Configurações por Canal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-email h2 text-primary"></i>
                                    <h5>Email</h5>
                                    <p class="text-muted">SMTP configurado</p>
                                    <span class="badge bg-success">Ativo</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-primary" onclick="configurarCanal('email')">
                                            Configurar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-cellphone h2 text-success"></i>
                                    <h5>SMS</h5>
                                    <p class="text-muted">Gateway pendente</p>
                                    <span class="badge bg-warning">Pendente</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-success" onclick="configurarCanal('sms')">
                                            Configurar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-bell h2 text-warning"></i>
                                    <h5>Push</h5>
                                    <p class="text-muted">Firebase configurado</p>
                                    <span class="badge bg-success">Ativo</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-warning" onclick="configurarCanal('push')">
                                            Configurar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <i class="mdi mdi-monitor h2 text-info"></i>
                                    <h5>In-App</h5>
                                    <p class="text-muted">WebSocket ativo</p>
                                    <span class="badge bg-success">Ativo</span>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-info" onclick="configurarCanal('in_app')">
                                            Configurar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs de Atividade -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-history"></i> Atividade Recente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline" id="timeline-atividade">
                        <!-- Carregado via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    carregarAplicacoes();
    carregarAtividade();
    carregarEstatisticas();
    
    // Atualizar dados a cada 30 segundos
    setInterval(function() {
        carregarAtividade();
        carregarEstatisticas();
    }, 30000);
});

function carregarEstatisticas() {
    fetch('/admin/notificacoes/api/aplicacoes-data')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stats = data.data.estatisticas;
                
                document.getElementById('stats-total').textContent = stats.total;
                document.getElementById('stats-hoje').textContent = stats.notificacoes_hoje.toLocaleString();
                document.getElementById('stats-mais-ativa').textContent = stats.mais_ativa;
                document.getElementById('stats-sucesso').textContent = stats.taxa_sucesso + '%';
                
                // Atualizar badge da aplicação mais ativa
                const badge = document.querySelector('#stats-mais-ativa').nextElementSibling;
                if (badge) {
                    badge.textContent = stats.envios_mais_ativa + ' envios';
                }
            }
        })
        .catch(error => {
            console.error('Erro ao carregar estatísticas:', error);
        });
}

function carregarAplicacoes() {
    fetch('/admin/notificacoes/api/aplicacoes-data')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarAplicacoes(data.data.aplicacoes);
            } else {
                mostrarAlerta('Erro ao carregar aplicações: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar aplicações:', error);
            mostrarAlerta('Erro ao carregar aplicações', 'danger');
        });
}

function renderizarAplicacoes(aplicacoes) {
    const container = document.getElementById('aplicacoes-container');
    container.innerHTML = '';

    if (aplicacoes.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="mdi mdi-application h1 text-muted"></i>
                    <h5 class="text-muted">Nenhuma aplicação encontrada</h5>
                    <p class="text-muted">Cadastre aplicações para começar a enviar notificações.</p>
                </div>
            </div>
        `;
        return;
    }

    aplicacoes.forEach(app => {
        const statusBadge = app.status === 'ativo' ? 'success' : 'secondary';
        const statusTexto = app.status === 'ativo' ? 'Ativo' : 'Inativo';
        
        const canaisHtml = app.canais_habilitados.map(canal => {
            const icones = {
                'email': 'email',
                'sms': 'cellphone',
                'push': 'bell',
                'in_app': 'monitor'
            };
            return `<i class="mdi mdi-${icones[canal]} text-${app.cor}" title="${canal.toUpperCase()}"></i>`;
        }).join(' ');

        const cardHtml = `
            <div class="col-lg-6 col-xl-4">
                <div class="card border-${app.cor}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="flex-1">
                                <h5 class="card-title">
                                    <i class="mdi mdi-application text-${app.cor}"></i>
                                    ${app.nome}
                                </h5>
                                <p class="card-text text-muted">${app.descricao}</p>
                            </div>
                            <div>
                                <span class="badge bg-${statusBadge}">${statusTexto}</span>
                            </div>
                        </div>
                        
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <h4 class="text-${app.cor}">${app.notificacoes_hoje}</h4>
                                <small class="text-muted">Hoje</small>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">
                                    ${canaisHtml}
                                </div>
                                <small class="text-muted">Canais</small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="mdi mdi-clock-outline"></i>
                                ${app.ultima_atividade}
                            </small>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-${app.cor}" onclick="verDetalhes(${app.id})" title="Ver Detalhes">
                                    <i class="mdi mdi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="configurarApp(${app.id})" title="Configurar">
                                    <i class="mdi mdi-cog"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" onclick="testarApp(${app.id})" title="Testar">
                                    <i class="mdi mdi-test-tube"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-2">
                            <small class="text-muted">
                                <strong>API Key:</strong> 
                                <code>${app.api_key}</code>
                                <button class="btn btn-sm btn-link p-0 ms-1" onclick="copiarApiKey('${app.api_key}')" title="Copiar">
                                    <i class="mdi mdi-content-copy"></i>
                                </button>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.innerHTML += cardHtml;
    });
}

function carregarAtividade() {
    fetch('/admin/notificacoes/api/atividade-recente')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarAtividade(data.data);
            }
        })
        .catch(error => {
            console.error('Erro ao carregar atividade:', error);
        });
}

function renderizarAtividade(atividades) {
    const timeline = document.getElementById('timeline-atividade');
    timeline.innerHTML = '';

    if (atividades.length === 0) {
        timeline.innerHTML = `
            <div class="text-center py-4">
                <i class="mdi mdi-history h3 text-muted"></i>
                <p class="text-muted">Nenhuma atividade recente</p>
            </div>
        `;
        return;
    }

    atividades.forEach((atividade, index) => {
        const timelineItem = `
            <div class="timeline-item">
                <div class="timeline-marker bg-${atividade.tipo}"></div>
                <div class="timeline-content">
                    <h6 class="mb-1">${atividade.app}</h6>
                    <p class="mb-1">${atividade.acao}</p>
                    <small class="text-muted">
                        <i class="mdi mdi-clock-outline"></i>
                        ${atividade.tempo}
                    </small>
                </div>
            </div>
        `;
        timeline.innerHTML += timelineItem;
    });
}

function verDetalhes(appId) {
    window.open(`/admin/notificacoes/aplicacoes/${appId}/detalhes`, '_blank');
}

function configurarApp(appId) {
    window.open(`/admin/notificacoes/aplicacoes/${appId}/configurar`, '_blank');
}

function testarApp(appId) {
    window.open(`/admin/notificacoes/aplicacoes/${appId}/teste`, '_blank');
}

function configurarCanal(canal) {
    window.open(`/admin/notificacoes/canais/${canal}/configurar`, '_blank');
}

function copiarApiKey(apiKey) {
    navigator.clipboard.writeText(apiKey.replace('***********', 'full_key_here')).then(function() {
        mostrarAlerta('API Key copiada para a área de transferência!', 'success');
    }).catch(function() {
        mostrarAlerta('Erro ao copiar API Key', 'danger');
    });
}

function mostrarAlerta(mensagem, tipo) {
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
</script>

<style>
.timeline {
    position: relative;
}

.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 12px;
    bottom: -20px;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    border-left: 3px solid #dee2e6;
}
</style>
@endpush
@endsection
