<?php $__env->startSection('title', 'Painel de Notificações'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Notificações</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-bell-ring-outline"></i> Painel de Notificações
                </h4>
            </div>
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-send float-end text-muted"></i>
                    <h6 class="text-uppercase mt-0">Enviadas Hoje</h6>
                    <h2 class="m-b-20" id="stats-hoje"><?php echo e($stats['hoje'] ?? 0); ?></h2>
                    <span class="badge bg-success"> +<?php echo e($stats['crescimento_hoje'] ?? '0%'); ?> </span>
                    <span class="text-muted">vs ontem</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-clock-outline float-end text-muted"></i>
                    <h6 class="text-uppercase mt-0">Pendentes</h6>
                    <h2 class="m-b-20" id="stats-pendentes"><?php echo e($stats['pendentes'] ?? 0); ?></h2>
                    <span class="badge bg-warning"> <?php echo e($stats['agendadas'] ?? 0); ?> agendadas </span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-check-circle float-end text-muted"></i>
                    <h6 class="text-uppercase mt-0">Taxa de Sucesso</h6>
                    <h2 class="m-b-20" id="stats-sucesso"><?php echo e($stats['taxa_sucesso'] ?? '98.5%'); ?></h2>
                    <span class="badge bg-success"> Último 7 dias </span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-account-multiple float-end text-muted"></i>
                    <h6 class="text-uppercase mt-0">Templates Ativos</h6>
                    <h2 class="m-b-20" id="stats-templates"><?php echo e($stats['templates_ativos'] ?? 0); ?></h2>
                    <span class="badge bg-info"> <?php echo e($stats['total_templates'] ?? 0); ?> total </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu de Ações -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-view-dashboard"></i> Painel de Controle
                    </h4>
                    
                    <div class="row">
                        <!-- Gerenciamento -->
                        <div class="col-lg-4 col-md-6">
                            <div class="border rounded p-3 mb-3">
                                <h5 class="text-primary">
                                    <i class="mdi mdi-cog"></i> Gerenciamento
                                </h5>
                                <div class="d-grid gap-2">
                                    <a href="<?php echo e(route('admin.notificacoes.templates')); ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="mdi mdi-file-document-edit"></i> Templates
                                    </a>
                                    <a href="<?php echo e(route('admin.notificacoes.tipos')); ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="mdi mdi-format-list-bulleted"></i> Tipos de Evento
                                    </a>
                                    <a href="<?php echo e(route('admin.notificacoes.aplicacoes')); ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="mdi mdi-application"></i> Aplicações
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Monitoramento -->
                        <div class="col-lg-4 col-md-6">
                            <div class="border rounded p-3 mb-3">
                                <h5 class="text-success">
                                    <i class="mdi mdi-monitor"></i> Monitoramento
                                </h5>
                                <div class="d-grid gap-2">
                                    <a href="<?php echo e(route('admin.notificacoes.enviadas')); ?>" class="btn btn-outline-success btn-sm">
                                        <i class="mdi mdi-send"></i> Enviadas
                                    </a>
                                    <a href="<?php echo e(route('admin.notificacoes.estatisticas')); ?>" class="btn btn-outline-success btn-sm">
                                        <i class="mdi mdi-chart-line"></i> Estatísticas
                                    </a>
                                    <a href="<?php echo e(route('admin.notificacoes.logs')); ?>" class="btn btn-outline-success btn-sm">
                                        <i class="mdi mdi-file-document"></i> Logs
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Ferramentas -->
                        <div class="col-lg-4 col-md-6">
                            <div class="border rounded p-3 mb-3">
                                <h5 class="text-warning">
                                    <i class="mdi mdi-tools"></i> Ferramentas
                                </h5>
                                <div class="d-grid gap-2">
                                    <a href="<?php echo e(route('admin.notificacoes.teste')); ?>" class="btn btn-outline-warning btn-sm">
                                        <i class="mdi mdi-test-tube"></i> Página de Teste
                                    </a>
                                    <a href="<?php echo e(route('admin.notificacoes.diagnostico')); ?>" class="btn btn-outline-warning btn-sm">
                                        <i class="mdi mdi-stethoscope"></i> Diagnóstico
                                    </a>
                                    <a href="<?php echo e(route('admin.notificacoes.configuracoes')); ?>" class="btn btn-outline-warning btn-sm">
                                        <i class="mdi mdi-settings"></i> Configurações
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Atividade -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-chart-areaspline"></i> Atividade das Notificações (Últimos 7 dias)
                    </h4>
                    <div style="position: relative; height: 300px;">
                        <canvas id="grafico-atividade"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-pie-chart"></i> Por Canal
                    </h4>
                    <div style="position: relative; height: 300px;">
                        <canvas id="grafico-canais"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notificações Recentes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-history"></i> Notificações Recentes
                    </h4>
                    
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Canal</th>
                                    <th>Destinatário</th>
                                    <th>Título</th>
                                    <th>Status</th>
                                    <th>Enviado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="notificacoes-recentes">
                                <!-- Carregado via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aguardar o Chart.js carregar completamente
    function inicializar() {
        if (typeof Chart !== 'undefined') {
            console.log('Chart.js carregado com sucesso');
            
            // Configurar gráficos
            configurarGraficos();
            
            // Carregar dados iniciais
            carregarNotificacaoRecentes();
            
            // Atualizar dados a cada 30 segundos
            setInterval(function() {
                atualizarEstatisticas();
                carregarNotificacaoRecentes();
            }, 30000);
        } else {
            console.log('Aguardando Chart.js carregar...');
            setTimeout(inicializar, 100);
        }
    }
    
    inicializar();
});

function configurarGraficos() {
    try {
        // Verificar se o Chart.js está carregado
        if (typeof Chart === 'undefined') {
            console.error('Chart.js não está carregado');
            return;
        }

        // Gráfico de Atividade
        const canvasAtividade = document.getElementById('grafico-atividade');
        if (canvasAtividade) {
            const ctxAtividade = canvasAtividade.getContext('2d');
            
            new Chart(ctxAtividade, {
                type: 'line',
                data: {
                    labels: ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'],
                    datasets: [{
                        label: 'Enviadas',
                        data: [120, 190, 300, 500, 200, 300, 450],
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } else {
            console.error('Canvas grafico-atividade não encontrado');
        }

        // Gráfico de Canais
        const canvasCanais = document.getElementById('grafico-canais');
        if (canvasCanais) {
            const ctxCanais = canvasCanais.getContext('2d');
            
            new Chart(ctxCanais, {
                type: 'doughnut',
                data: {
                    labels: ['Email', 'SMS', 'Push', 'In-App'],
                    datasets: [{
                        data: [45, 25, 20, 10],
                        backgroundColor: [
                            '#ff6384',
                            '#36a2eb',
                            '#cc65fe',
                            '#ffce56'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    }
                }
            });
        } else {
            console.error('Canvas grafico-canais não encontrado');
        }
    } catch (error) {
        console.error('Erro ao configurar gráficos:', error);
    }
}

function carregarNotificacaoRecentes() {
    fetch('/admin/notificacoes/api/recentes')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const tbody = document.getElementById('notificacoes-recentes');
            if (tbody) {
                tbody.innerHTML = '';
                
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(notificacao => {
                        const row = `
                            <tr>
                                <td><span class="badge bg-${getCorTipo(notificacao.tipo)}">${notificacao.tipo}</span></td>
                                <td><i class="mdi mdi-${getIconeCanal(notificacao.canal)}"></i> ${notificacao.canal}</td>
                                <td>${notificacao.destinatario}</td>
                                <td>${notificacao.titulo}</td>
                                <td><span class="badge bg-${getCorStatus(notificacao.status)}">${notificacao.status}</span></td>
                                <td>${formatarData(notificacao.enviado_em)}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="verDetalhes(${notificacao.id})">
                                        <i class="mdi mdi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nenhuma notificação recente encontrada</td></tr>';
                }
            }
        })
        .catch(error => {
            console.error('Erro ao carregar notificações:', error);
            const tbody = document.getElementById('notificacoes-recentes');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Erro ao carregar dados</td></tr>';
            }
        });
}

function atualizarEstatisticas() {
    fetch('/admin/notificacoes/api/estatisticas')
        .then(response => response.json())
        .then(data => {
            document.getElementById('stats-hoje').textContent = data.hoje || 0;
            document.getElementById('stats-pendentes').textContent = data.pendentes || 0;
            document.getElementById('stats-sucesso').textContent = data.taxa_sucesso || '0%';
            document.getElementById('stats-templates').textContent = data.templates_ativos || 0;
        })
        .catch(error => console.error('Erro ao atualizar estatísticas:', error));
}

function getCorTipo(tipo) {
    const cores = {
        'pedido_criado': 'primary',
        'pagamento_aprovado': 'success',
        'produto_baixo_estoque': 'warning',
        'cliente_novo': 'info'
    };
    return cores[tipo] || 'secondary';
}

function getIconeCanal(canal) {
    const icones = {
        'email': 'email',
        'sms': 'cellphone',
        'push': 'bell',
        'in_app': 'application'
    };
    return icones[canal] || 'circle';
}

function getCorStatus(status) {
    const cores = {
        'enviado': 'success',
        'pendente': 'warning',
        'falhou': 'danger',
        'processando': 'info'
    };
    return cores[status] || 'secondary';
}

function formatarData(data) {
    return new Date(data).toLocaleString('pt-BR');
}

function verDetalhes(id) {
    window.location.href = `/admin/notificacoes/enviadas/${id}`;
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/notificacoes/index.blade.php ENDPATH**/ ?>