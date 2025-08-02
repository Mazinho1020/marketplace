<?php $__env->startSection('title', 'Detalhes do Gateway'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-credit-card me-2"></i>
                    <?php echo e($gateway->name); ?>

                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.payments.dashboard')); ?>">Pagamentos</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.payments.gateways')); ?>">Gateways</a></li>
                        <li class="breadcrumb-item active"><?php echo e($gateway->name); ?></li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="<?php echo e(route('admin.payments.gateways')); ?>" class="btn btn-outline-secondary">
                    <i class="uil uil-arrow-left me-1"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informações do Gateway -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-info-circle me-2"></i>
                        Informações do Gateway
                    </h5>
                    <div>
                        <?php if($gateway->is_active): ?>
                            <span class="badge bg-success">
                                <i class="uil uil-check me-1"></i>Ativo
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger">
                                <i class="uil uil-times me-1"></i>Inativo
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nome:</strong></td>
                                    <td><?php echo e($gateway->name); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Código:</strong></td>
                                    <td><code><?php echo e($gateway->code); ?></code></td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo:</strong></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo e(ucfirst($gateway->type)); ?></span>
                                    </td>
                                </tr>
                                <?php if($gateway->description): ?>
                                <tr>
                                    <td><strong>Descrição:</strong></td>
                                    <td><?php echo e($gateway->description); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Taxa Fixa:</strong></td>
                                    <td>
                                        <?php if($gateway->fee_fixed): ?>
                                            <span class="text-danger">R$ <?php echo e(number_format($gateway->fee_fixed, 2, ',', '.')); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">R$ 0,00</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Taxa Percentual:</strong></td>
                                    <td>
                                        <?php if($gateway->fee_percentage): ?>
                                            <span class="text-danger"><?php echo e(number_format($gateway->fee_percentage, 2, ',', '.')); ?>%</span>
                                        <?php else: ?>
                                            <span class="text-muted">0%</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Valor Mínimo:</strong></td>
                                    <td>
                                        <?php if($gateway->min_amount): ?>
                                            R$ <?php echo e(number_format($gateway->min_amount, 2, ',', '.')); ?>

                                        <?php else: ?>
                                            <span class="text-muted">Sem mínimo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Valor Máximo:</strong></td>
                                    <td>
                                        <?php if($gateway->max_amount): ?>
                                            R$ <?php echo e(number_format($gateway->max_amount, 2, ',', '.')); ?>

                                        <?php else: ?>
                                            <span class="text-muted">Sem máximo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas do Gateway -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50">Total Transações</h6>
                                    <h3 class="mb-0"><?php echo e(number_format($stats['total_transactions'])); ?></h3>
                                </div>
                                <i class="uil uil-transaction text-white-50" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50">Volume Total</h6>
                                    <h3 class="mb-0">R$ <?php echo e(number_format($stats['total_volume'], 0, ',', '.')); ?></h3>
                                </div>
                                <i class="uil uil-money-bill text-white-50" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50">Taxa de Sucesso</h6>
                                    <h3 class="mb-0"><?php echo e(number_format($stats['success_rate'], 1)); ?>%</h3>
                                </div>
                                <i class="uil uil-chart-success text-white-50" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50">Ticket Médio</h6>
                                    <h3 class="mb-0">R$ <?php echo e(number_format($stats['avg_transaction'], 0, ',', '.')); ?></h3>
                                </div>
                                <i class="uil uil-calculator text-white-50" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Performance -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-chart-line me-2"></i>
                        Performance dos Últimos 30 Dias
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="120"></canvas>
                </div>
            </div>

            <!-- Transações Recentes -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-history me-2"></i>
                        Transações Recentes
                    </h5>
                    <a href="<?php echo e(route('admin.payments.transactions')); ?>?gateway=<?php echo e($gateway->id); ?>" class="btn btn-sm btn-outline-primary">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    <?php if($recentTransactions->count()): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Método</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <code>#<?php echo e($transaction->external_id ?? $transaction->id); ?></code>
                                    </td>
                                    <td>
                                        <span class="text-success">R$ <?php echo e(number_format($transaction->amount, 2, ',', '.')); ?></span>
                                    </td>
                                    <td>
                                        <?php if($transaction->status === 'approved'): ?>
                                            <span class="badge bg-success">Aprovada</span>
                                        <?php elseif($transaction->status === 'pending'): ?>
                                            <span class="badge bg-warning">Pendente</span>
                                        <?php elseif($transaction->status === 'rejected'): ?>
                                            <span class="badge bg-danger">Rejeitada</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo e(ucfirst($transaction->status)); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($transaction->payment_method === 'credit_card'): ?>
                                            <i class="uil uil-credit-card me-1"></i>Cartão
                                        <?php elseif($transaction->payment_method === 'pix'): ?>
                                            <i class="uil uil-qrcode-scan me-1"></i>PIX
                                        <?php elseif($transaction->payment_method === 'bank_slip'): ?>
                                            <i class="uil uil-bill me-1"></i>Boleto
                                        <?php else: ?>
                                            <?php echo e(ucfirst($transaction->payment_method)); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($transaction->created_at->format('d/m/Y H:i')); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('admin.payments.transaction-details', $transaction->id)); ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="uil uil-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="uil uil-history text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Nenhuma transação encontrada</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status e Ações -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-setting me-2"></i>
                        Ações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if($gateway->is_active): ?>
                        <button type="button" class="btn btn-warning" onclick="toggleGateway(false)">
                            <i class="uil uil-pause me-1"></i>
                            Desativar Gateway
                        </button>
                        <?php else: ?>
                        <button type="button" class="btn btn-success" onclick="toggleGateway(true)">
                            <i class="uil uil-play me-1"></i>
                            Ativar Gateway
                        </button>
                        <?php endif; ?>
                        
                        <button type="button" class="btn btn-outline-primary" onclick="testConnection()">
                            <i class="uil uil-link me-1"></i>
                            Testar Conexão
                        </button>
                        
                        <button type="button" class="btn btn-outline-info" onclick="exportData()">
                            <i class="uil uil-export me-1"></i>
                            Exportar Dados
                        </button>
                        
                        <a href="<?php echo e(route('admin.payments.transactions')); ?>?gateway=<?php echo e($gateway->id); ?>" class="btn btn-outline-secondary">
                            <i class="uil uil-transaction me-1"></i>
                            Ver Transações
                        </a>
                    </div>
                </div>
            </div>

            <!-- Configurações -->
            <?php if($gateway->settings): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-cog me-2"></i>
                        Configurações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Ambiente:</small>
                        <div>
                            <?php if(isset($gateway->settings['environment']) && $gateway->settings['environment'] === 'production'): ?>
                                <span class="badge bg-success">Produção</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Sandbox</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if(isset($gateway->settings['webhook_url'])): ?>
                    <div class="mb-3">
                        <small class="text-muted">URL do Webhook:</small>
                        <div class="small"><code><?php echo e($gateway->settings['webhook_url']); ?></code></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(isset($gateway->settings['timeout'])): ?>
                    <div class="mb-3">
                        <small class="text-muted">Timeout:</small>
                        <div><?php echo e($gateway->settings['timeout']); ?>s</div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(isset($gateway->settings['retry_attempts'])): ?>
                    <div class="mb-3">
                        <small class="text-muted">Tentativas de Retry:</small>
                        <div><?php echo e($gateway->settings['retry_attempts']); ?>x</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Últimos Webhooks -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-webhook me-2"></i>
                        Últimos Webhooks
                    </h5>
                    <a href="<?php echo e(route('admin.payments.webhooks')); ?>?gateway=<?php echo e($gateway->id); ?>" class="btn btn-sm btn-outline-primary">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    <?php if($recentWebhooks->count()): ?>
                    <?php $__currentLoopData = $recentWebhooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $webhook): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                        <div>
                            <small class="text-muted"><?php echo e($webhook->event_type ?? 'N/A'); ?></small>
                            <div class="small"><?php echo e($webhook->created_at->format('d/m H:i')); ?></div>
                        </div>
                        <div>
                            <?php if($webhook->status === 'processed'): ?>
                                <span class="badge bg-success">OK</span>
                            <?php elseif($webhook->status === 'failed'): ?>
                                <span class="badge bg-danger">Erro</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Pendente</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                    <div class="text-center py-3">
                        <i class="uil uil-webhook text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2 mb-0">Nenhum webhook encontrado</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Performance
const performanceCtx = document.getElementById('performanceChart').getContext('2d');
new Chart(performanceCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($performanceData['labels'], 15, 512) ?>,
        datasets: [{
            label: 'Transações',
            data: <?php echo json_encode($performanceData['transactions'], 15, 512) ?>,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1
        }, {
            label: 'Volume (R$)',
            data: <?php echo json_encode($performanceData['volume'], 15, 512) ?>,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            tension: 0.1,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false,
                },
            }
        }
    }
});

function toggleGateway(activate) {
    const action = activate ? 'ativar' : 'desativar';
    if (confirm(`Tem certeza que deseja ${action} este gateway?`)) {
        fetch(`/admin/payments/gateways/<?php echo e($gateway->id); ?>/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ active: activate })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Gateway ${action}do com sucesso`);
                location.reload();
            } else {
                alert('Erro ao alterar status do gateway: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao comunicar com o servidor');
        });
    }
}

function testConnection() {
    alert('Testando conexão...');
    fetch(`/admin/payments/gateways/<?php echo e($gateway->id); ?>/test`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Conexão testada com sucesso!');
        } else {
            alert('Erro na conexão: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao testar conexão');
    });
}

function exportData() {
    const data = <?php echo json_encode($gateway, 15, 512) ?>;
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `gateway-${data.code}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/payments/gateway-details.blade.php ENDPATH**/ ?>