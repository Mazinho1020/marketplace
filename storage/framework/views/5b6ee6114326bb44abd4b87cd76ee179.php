<?php $__env->startSection('title', 'Finalizar Pagamento'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Resumo do Pedido -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Resumo do Pedido
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Plano Selecionado</h6>
                            <p class="mb-1"><?php echo e($plano->nome ?? 'Plano'); ?></p>
                            <small class="text-muted"><?php echo e($plano->descricao ?? ''); ?></small>
                        </div>
                        <div class="col-md-3">
                            <h6>Ciclo</h6>
                            <p class="mb-0"><?php echo e(ucfirst($transaction->metadados['ciclo_cobranca'] ?? 'mensal')); ?></p>
                        </div>
                        <div class="col-md-3">
                            <h6>Valor</h6>
                            <h4 class="text-primary mb-0">
                                R$ <?php echo e(number_format($transaction->valor_final, 2, ',', '.')); ?>

                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status da Transação -->
            <?php if($transaction->status === 'pendente' && $transaction->forma_pagamento): ?>
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>Pagamento Pendente
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if($transaction->forma_pagamento === 'pix'): ?>
                            <!-- PIX -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Pagamento via PIX</h6>
                                    <p class="mb-3">Use o QR Code abaixo ou copie o código PIX:</p>
                                    
                                    <!-- QR Code Simulado -->
                                    <div class="text-center mb-3">
                                        <div class="border p-3" style="width: 200px; height: 200px; margin: 0 auto; background: #f8f9fa;">
                                            <i class="fas fa-qrcode fa-8x text-muted"></i>
                                        </div>
                                        <small class="text-muted">QR Code PIX</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Código PIX Copia e Cola:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" 
                                                   value="00020126580014BR.GOV.BCB.PIX013662dc64..." readonly>
                                            <button class="btn btn-outline-secondary" type="button" 
                                                    onclick="copiarPix()">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle me-2"></i>Como pagar:</h6>
                                        <ol class="mb-0">
                                            <li>Abra o app do seu banco</li>
                                            <li>Escolha a opção PIX</li>
                                            <li>Escaneie o QR Code ou cole o código</li>
                                            <li>Confirme o pagamento</li>
                                        </ol>
                                    </div>

                                    <?php if($transaction->expira_em): ?>
                                        <div class="alert alert-warning">
                                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Atenção:</h6>
                                            <p class="mb-1">Este PIX expira em:</p>
                                            <h5 id="countdown" class="text-danger"></h5>
                                        </div>
                                    <?php endif; ?>

                                    <button class="btn btn-success w-100" onclick="confirmarPagamento('<?php echo e($transaction->uuid); ?>')">
                                        <i class="fas fa-check me-2"></i>Já Paguei
                                    </button>
                                </div>
                            </div>

                        <?php elseif($transaction->forma_pagamento === 'bank_slip'): ?>
                            <!-- Boleto -->
                            <div class="text-center">
                                <h6>Pagamento via Boleto Bancário</h6>
                                <p class="mb-3">Seu boleto foi gerado com sucesso!</p>
                                
                                <div class="mb-4">
                                    <i class="fas fa-barcode fa-4x text-primary mb-3"></i>
                                    <br>
                                    <a href="#" class="btn btn-primary me-2">
                                        <i class="fas fa-download me-2"></i>Baixar Boleto PDF
                                    </a>
                                    <a href="#" class="btn btn-outline-primary">
                                        <i class="fas fa-print me-2"></i>Imprimir
                                    </a>
                                </div>

                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Informações:</h6>
                                    <p class="mb-1">• Vencimento: <?php echo e(now()->addDays(3)->format('d/m/Y')); ?></p>
                                    <p class="mb-1">• Após o pagamento, pode levar até 2 dias úteis para compensar</p>
                                    <p class="mb-0">• Você receberá um email de confirmação</p>
                                </div>

                                <button class="btn btn-success" onclick="confirmarPagamento('<?php echo e($transaction->uuid); ?>')">
                                    <i class="fas fa-check me-2"></i>Já Paguei
                                </button>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>

            <?php elseif($transaction->status === 'aprovado'): ?>
                <!-- Pagamento Aprovado -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>Pagamento Aprovado!
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h4>Plano Ativado com Sucesso!</h4>
                        <p class="mb-4">Seu plano <?php echo e($plano->nome ?? ''); ?> está ativo e você já pode aproveitar todos os recursos.</p>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="<?php echo e(route('comerciantes.planos.dashboard')); ?>" class="btn btn-primary">
                                <i class="fas fa-tachometer-alt me-2"></i>Ir para Dashboard
                            </a>
                            <a href="<?php echo e(route('comerciantes.dashboard')); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i>Página Inicial
                            </a>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- Outros Status -->
                <div class="card">
                    <div class="card-header bg-<?php echo e($transaction->status_cor); ?>">
                        <h5 class="mb-0 text-white">
                            <i class="<?php echo e($transaction->status_icone); ?> me-2"></i>
                            Status: <?php echo e(ucfirst($transaction->status)); ?>

                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <?php if($transaction->status === 'cancelado'): ?>
                            <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                            <h4>Pagamento Cancelado</h4>
                            <p class="mb-4">Esta transação foi cancelada. Você pode tentar novamente.</p>
                            
                            <a href="<?php echo e(route('comerciantes.planos.planos')); ?>" class="btn btn-primary">
                                <i class="fas fa-redo me-2"></i>Tentar Novamente
                            </a>
                        <?php else: ?>
                            <i class="fas fa-spinner fa-4x text-primary mb-3"></i>
                            <h4>Processando Pagamento</h4>
                            <p class="mb-4">Aguarde enquanto processamos seu pagamento...</p>
                            
                            <button class="btn btn-primary" onclick="location.reload()">
                                <i class="fas fa-sync me-2"></i>Atualizar Status
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar com Ajuda -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>Precisa de Ajuda?
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Dúvidas sobre o pagamento?</h6>
                        <p class="text-muted small">Entre em contato com nosso suporte:</p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="mailto:suporte@marketplace.com" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-envelope me-2"></i>suporte@marketplace.com
                        </a>
                        <a href="https://wa.me/5511999999999" class="btn btn-outline-success btn-sm">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp
                        </a>
                    </div>

                    <hr>

                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Pagamento 100% seguro
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function confirmarPagamento(uuid) {
    if (confirm('Você confirma que já realizou o pagamento?')) {
        fetch(`<?php echo e(route('comerciantes.planos.confirmar-pagamento', '')); ?>/${uuid}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pagamento confirmado! Redirecionando...');
                window.location.href = data.redirect || '<?php echo e(route("comerciantes.planos.dashboard")); ?>';
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao confirmar pagamento');
        });
    }
}

function copiarPix() {
    const input = document.querySelector('input[readonly]');
    input.select();
    document.execCommand('copy');
    
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i>';
    
    setTimeout(() => {
        btn.innerHTML = originalText;
    }, 2000);
}

// Countdown para PIX
<?php if($transaction->expira_em ?? false): ?>
const expiresAt = new Date('<?php echo e($transaction->expira_em->toISOString()); ?>');
const countdownElement = document.getElementById('countdown');

if (countdownElement) {
    setInterval(function() {
        const now = new Date().getTime();
        const distance = expiresAt.getTime() - now;

        if (distance > 0) {
            const hours = Math.floor(distance / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.innerHTML = `${hours}h ${minutes}m ${seconds}s`;
        } else {
            countdownElement.innerHTML = "EXPIRADO";
            countdownElement.parentElement.classList.add('alert-danger');
            countdownElement.parentElement.classList.remove('alert-warning');
        }
    }, 1000);
}
<?php endif; ?>
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/planos/checkout.blade.php ENDPATH**/ ?>