<?php $__env->startSection('title', 'Configurações de Pagamento'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="page-title mb-0">
                    <i class="uil uil-setting me-2"></i>
                    Configurações de Pagamento
                </h4>
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.payments.dashboard')); ?>">Pagamentos</a></li>
                        <li class="breadcrumb-item active">Configurações</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-primary" onclick="saveAllSettings()">
                    <i class="uil uil-save me-1"></i>
                    Salvar Todas
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Configurações Gerais -->
        <div class="col-lg-8">
            <!-- Configurações de Sistema -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-cog me-2"></i>
                        Configurações do Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <form id="systemSettingsForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Timeout Padrão (segundos)</label>
                                    <input type="number" class="form-control" name="default_timeout" value="30" min="10" max="300">
                                    <small class="form-text text-muted">Tempo limite para requisições aos gateways</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tentativas de Retry</label>
                                    <input type="number" class="form-control" name="retry_attempts" value="3" min="1" max="10">
                                    <small class="form-text text-muted">Número de tentativas em caso de falha</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ambiente Padrão</label>
                                    <select class="form-select" name="default_environment">
                                        <option value="sandbox">Sandbox</option>
                                        <option value="production">Produção</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Moeda Padrão</label>
                                    <select class="form-select" name="default_currency">
                                        <option value="BRL" selected>Real (BRL)</option>
                                        <option value="USD">Dólar (USD)</option>
                                        <option value="EUR">Euro (EUR)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="auto_capture" id="autoCapture" checked>
                                <label class="form-check-label" for="autoCapture">
                                    Captura automática de transações
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="webhook_verification" id="webhookVerification" checked>
                                <label class="form-check-label" for="webhookVerification">
                                    Verificação de assinatura nos webhooks
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Configurações de Gateways -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-credit-card me-2"></i>
                        Gateways de Pagamento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php
                        $availableGateways = [
                            'mercadopago' => ['name' => 'Mercado Pago', 'icon' => 'uil-shopping-cart', 'color' => 'info'],
                            'pagseguro' => ['name' => 'PagSeguro', 'icon' => 'uil-shield', 'color' => 'warning'],
                            'picpay' => ['name' => 'PicPay', 'icon' => 'uil-mobile-android', 'color' => 'success'],
                            'asaas' => ['name' => 'Asaas', 'icon' => 'uil-bill', 'color' => 'primary'],
                            'stripe' => ['name' => 'Stripe', 'icon' => 'uil-credit-card-search', 'color' => 'dark'],
                            'paypal' => ['name' => 'PayPal', 'icon' => 'uil-paypal', 'color' => 'info']
                        ];
                        ?>
                        
                        <?php $__currentLoopData = $availableGateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="uil <?php echo e($gateway['icon']); ?> text-<?php echo e($gateway['color']); ?> me-2" style="font-size: 1.5rem;"></i>
                                            <h6 class="mb-0"><?php echo e($gateway['name']); ?></h6>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="<?php echo e($code); ?>Enabled" checked>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="form-label small">Prioridade</label>
                                        <input type="number" class="form-control form-control-sm" value="1" min="1" max="10">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label small">Taxa Fixa</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" class="form-control" value="0.00" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">Taxa %</label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control" value="0.00" step="0.01">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2 w-100" onclick="configureGateway('<?php echo e($code); ?>')">
                                        <i class="uil uil-setting me-1"></i>
                                        Configurar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <!-- Métodos de Pagamento -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-money-bill me-2"></i>
                        Métodos de Pagamento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php
                        $paymentMethods = [
                            'credit_card' => ['name' => 'Cartão de Crédito', 'icon' => 'uil-credit-card', 'color' => 'primary'],
                            'debit_card' => ['name' => 'Cartão de Débito', 'icon' => 'uil-credit-card-search', 'color' => 'info'],
                            'pix' => ['name' => 'PIX', 'icon' => 'uil-qrcode-scan', 'color' => 'success'],
                            'bank_slip' => ['name' => 'Boleto Bancário', 'icon' => 'uil-bill', 'color' => 'warning'],
                            'bank_transfer' => ['name' => 'Transferência', 'icon' => 'uil-exchange', 'color' => 'secondary'],
                            'digital_wallet' => ['name' => 'Carteira Digital', 'icon' => 'uil-mobile-android', 'color' => 'dark']
                        ];
                        ?>
                        
                        <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <i class="uil <?php echo e($method['icon']); ?> text-<?php echo e($method['color']); ?> mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="mb-2"><?php echo e($method['name']); ?></h6>
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="<?php echo e($code); ?>Method" checked>
                                    </div>
                                    
                                    <?php if($code === 'credit_card'): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">Parcelas máx:</small>
                                        <input type="number" class="form-control form-control-sm mt-1" value="12" min="1" max="24">
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if($code === 'bank_slip'): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">Dias vencimento:</small>
                                        <input type="number" class="form-control form-control-sm mt-1" value="3" min="1" max="30">
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <!-- Webhooks -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-webhook me-2"></i>
                        Configurações de Webhook
                    </h5>
                </div>
                <div class="card-body">
                    <form id="webhookSettingsForm">
                        <div class="mb-3">
                            <label class="form-label">URL Base dos Webhooks</label>
                            <input type="url" class="form-control" name="webhook_base_url" value="<?php echo e(url('/')); ?>/webhooks/payments">
                            <small class="form-text text-muted">URL base para recebimento de webhooks</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Timeout para Webhooks (segundos)</label>
                                    <input type="number" class="form-control" name="webhook_timeout" value="10" min="5" max="60">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Máx. tentativas de reprocessamento</label>
                                    <input type="number" class="form-control" name="webhook_max_retries" value="5" min="1" max="10">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="webhook_logging" id="webhookLogging" checked>
                                <label class="form-check-label" for="webhookLogging">
                                    Log detalhado de webhooks
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="webhook_auto_retry" id="webhookAutoRetry" checked>
                                <label class="form-check-label" for="webhookAutoRetry">
                                    Reprocessamento automático em caso de falha
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status do Sistema -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-heart me-2"></i>
                        Status do Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Sistema de Pagamentos</span>
                            <span class="badge bg-success">Online</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Webhooks</span>
                            <span class="badge bg-success">Funcionando</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Queue de Jobs</span>
                            <span class="badge bg-warning">Verificando</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Cache Redis</span>
                            <span class="badge bg-success">Conectado</span>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="checkSystemHealth()">
                        <i class="uil uil-refresh me-1"></i>
                        Verificar Status
                    </button>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-rocket me-2"></i>
                        Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-warning" onclick="clearCache()">
                            <i class="uil uil-trash me-1"></i>
                            Limpar Cache
                        </button>
                        
                        <button type="button" class="btn btn-outline-info" onclick="testGateways()">
                            <i class="uil uil-link me-1"></i>
                            Testar Gateways
                        </button>
                        
                        <button type="button" class="btn btn-outline-secondary" onclick="exportSettings()">
                            <i class="uil uil-export me-1"></i>
                            Exportar Config
                        </button>
                        
                        <button type="button" class="btn btn-outline-primary" onclick="importSettings()">
                            <i class="uil uil-import me-1"></i>
                            Importar Config
                        </button>
                    </div>
                </div>
            </div>

            <!-- Backup e Logs -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="uil uil-file-alt me-2"></i>
                        Backup e Logs
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small">Último backup:</label>
                        <div class="text-muted"><?php echo e(now()->subHours(2)->format('d/m/Y H:i')); ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small">Tamanho dos logs:</label>
                        <div class="text-muted">45.2 MB</div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="createBackup()">
                            <i class="uil uil-save me-1"></i>
                            Criar Backup
                        </button>
                        
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="viewLogs()">
                            <i class="uil uil-file-alt me-1"></i>
                            Ver Logs
                        </button>
                        
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearLogs()">
                            <i class="uil uil-trash me-1"></i>
                            Limpar Logs
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Configuração de Gateway -->
<div class="modal fade" id="gatewayConfigModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configurar Gateway</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="gatewayConfigForm">
                    <div class="mb-3">
                        <label class="form-label">Nome do Gateway</label>
                        <input type="text" class="form-control" id="gatewayName" readonly>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Client ID / API Key</label>
                                <input type="text" class="form-control" name="client_id">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Client Secret</label>
                                <input type="password" class="form-control" name="client_secret">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Webhook URL</label>
                        <input type="url" class="form-control" name="webhook_url">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ambiente</label>
                                <select class="form-select" name="environment">
                                    <option value="sandbox">Sandbox</option>
                                    <option value="production">Produção</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Timeout (segundos)</label>
                                <input type="number" class="form-control" name="timeout" value="30">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveGatewayConfig()">Salvar</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function saveAllSettings() {
    // Implementar salvamento de todas as configurações
    alert('Configurações salvas com sucesso!');
}

function configureGateway(gatewayCode) {
    const gatewayNames = {
        'mercadopago': 'Mercado Pago',
        'pagseguro': 'PagSeguro',
        'picpay': 'PicPay',
        'asaas': 'Asaas',
        'stripe': 'Stripe',
        'paypal': 'PayPal'
    };
    
    document.getElementById('gatewayName').value = gatewayNames[gatewayCode];
    new bootstrap.Modal(document.getElementById('gatewayConfigModal')).show();
}

function saveGatewayConfig() {
    // Implementar salvamento da configuração do gateway
    alert('Configuração do gateway salva com sucesso!');
    bootstrap.Modal.getInstance(document.getElementById('gatewayConfigModal')).hide();
}

function checkSystemHealth() {
    alert('Verificando status do sistema...');
    // Implementar verificação de saúde do sistema
}

function clearCache() {
    if (confirm('Tem certeza que deseja limpar o cache?')) {
        alert('Cache limpo com sucesso!');
    }
}

function testGateways() {
    alert('Testando conexão com todos os gateways...');
    // Implementar teste de gateways
}

function exportSettings() {
    // Implementar exportação de configurações
    const settings = {
        system: new FormData(document.getElementById('systemSettingsForm')),
        webhooks: new FormData(document.getElementById('webhookSettingsForm'))
    };
    
    const blob = new Blob([JSON.stringify(settings, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'payment-settings.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function importSettings() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.json';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const settings = JSON.parse(e.target.result);
                    alert('Configurações importadas com sucesso!');
                    // Implementar aplicação das configurações importadas
                } catch (error) {
                    alert('Erro ao importar configurações: arquivo inválido');
                }
            };
            reader.readAsText(file);
        }
    };
    input.click();
}

function createBackup() {
    alert('Criando backup das configurações...');
    // Implementar criação de backup
}

function viewLogs() {
    // Implementar visualização de logs
    window.open('/admin/logs/payments', '_blank');
}

function clearLogs() {
    if (confirm('Tem certeza que deseja limpar todos os logs?')) {
        alert('Logs limpos com sucesso!');
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/payments/settings.blade.php ENDPATH**/ ?>