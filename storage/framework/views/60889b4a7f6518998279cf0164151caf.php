<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações Simples - MeuFinanceiro</title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Theme1/css/icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 1.5rem;
        }
        .config-item {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: white;
        }
        .config-header {
            display: flex;
            justify-content-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .config-title {
            font-weight: 600;
            color: #2d3748;
        }
        .config-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Configurações Simples do Sistema
                </h4>
            </div>
            <div class="card-body">
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo e($error); ?>

                    </div>
                <?php endif; ?>

                <?php if(isset($debug)): ?>
                    <div class="alert alert-info">
                        <h6>Debug Info:</h6>
                        <ul class="mb-0">
                            <?php $__currentLoopData = $debug; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><strong><?php echo e($key); ?>:</strong> <?php echo e($value); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-warning" onclick="clearCache()">
                            <i class="fas fa-refresh me-1"></i>
                            Limpar Cache
                        </button>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="/admin/config" class="btn btn-info">
                            <i class="fas fa-cogs me-1"></i>
                            Config Completa
                        </a>
                        <a href="/teste-layout" class="btn btn-secondary">
                            <i class="fas fa-home me-1"></i>
                            Início
                        </a>
                    </div>
                </div>

                <?php if(isset($configs) && $configs->count() > 0): ?>
                    <?php $__currentLoopData = $configs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="config-item">
                            <div class="config-header">
                                <div class="config-title">
                                    <?php echo e($config->chave); ?>

                                    <?php if($config->obrigatorio): ?>
                                        <span class="badge bg-danger ms-1">Obrigatório</span>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-secondary"><?php echo e($config->tipo_dado); ?></span>
                            </div>
                            
                            <?php if($config->descricao): ?>
                                <div class="config-description">
                                    <?php echo e($config->descricao); ?>

                                </div>
                            <?php endif; ?>

                            <form class="config-form" data-config-chave="<?php echo e($config->chave); ?>">
                                <div class="row">
                                    <div class="col-md-8">
                                        <?php if($config->tipo_dado === 'boolean'): ?>
                                            <select class="form-select" name="valor">
                                                <option value="0" <?php echo e($config->formatarValor() == '0' ? 'selected' : ''); ?>>Não (false)</option>
                                                <option value="1" <?php echo e($config->formatarValor() == '1' ? 'selected' : ''); ?>>Sim (true)</option>
                                            </select>
                                        <?php elseif($config->tipo_dado === 'integer'): ?>
                                            <input type="number" class="form-control" name="valor" 
                                                   value="<?php echo e($config->formatarValor()); ?>" 
                                                   placeholder="0">
                                        <?php elseif($config->tipo_dado === 'email'): ?>
                                            <input type="email" class="form-control" name="valor" 
                                                   value="<?php echo e($config->formatarValor()); ?>" 
                                                   placeholder="exemplo@dominio.com">
                                        <?php elseif($config->tipo_dado === 'url'): ?>
                                            <input type="url" class="form-control" name="valor" 
                                                   value="<?php echo e($config->formatarValor()); ?>" 
                                                   placeholder="https://exemplo.com">
                                        <?php elseif($config->tipo_dado === 'json'): ?>
                                            <textarea class="form-control" name="valor" rows="3" 
                                                      placeholder='{"chave": "valor"}'><?php echo e($config->formatarValor()); ?></textarea>
                                        <?php else: ?>
                                            <input type="text" class="form-control" name="valor" 
                                                   value="<?php echo e($config->formatarValor()); ?>" 
                                                   placeholder="Digite o valor...">
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-save me-1"></i>
                                            Salvar
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <?php if($config->valor_padrao): ?>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <strong>Valor Padrão:</strong> 
                                        <code class="bg-light px-1 rounded"><?php echo e($config->valor_padrao); ?></code>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Nenhuma configuração encontrada.
                    </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        Total: <?php echo e(isset($configs) ? $configs->count() : 0); ?> configurações carregadas
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="/Theme1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Salvar configuração via AJAX
        document.querySelectorAll('.config-form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const configChave = this.dataset.configChave;
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                // Loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Salvando...';
                
                try {
                    const response = await fetch('/admin/config-simple/set-value', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams({
                            chave: configChave,
                            valor: formData.get('valor')
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showSuccess(result.message);
                        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i> Salvo!';
                        setTimeout(() => {
                            submitBtn.innerHTML = originalText;
                        }, 2000);
                    } else {
                        showError(result.message);
                    }
                } catch (error) {
                    showError('Erro ao salvar configuração.');
                    console.error('Erro:', error);
                } finally {
                    submitBtn.disabled = false;
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                    }, 2000);
                }
            });
        });

        // Limpar cache
        async function clearCache() {
            try {
                const response = await fetch('/admin/config-simple/clear-cache', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showSuccess(result.message);
                } else {
                    showError(result.message);
                }
            } catch (error) {
                showError('Erro ao limpar cache.');
                console.error('Erro:', error);
            }
        }

        // Funções de notificação
        function showSuccess(message) {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: message,
                confirmButtonColor: '#0acf97',
                timer: 3000,
                timerProgressBar: true
            });
        }

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: message,
                confirmButtonColor: '#fa5c7c'
            });
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/config/simple.blade.php ENDPATH**/ ?>