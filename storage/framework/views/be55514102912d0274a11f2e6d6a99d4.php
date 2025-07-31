<?php $__env->startSection('title', 'Configurações do Sistema'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Configurações</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="uil uil-cog me-1"></i>
                    Configurações do Sistema
                </h4>
            </div>
        </div>
    </div>

    <!-- Filtros e Ações -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('admin.config.index')); ?>" class="row g-3">
                        <div class="col-md-3">
                            <label for="group" class="form-label">Grupo</label>
                            <select name="group" id="group" class="form-select">
                                <option value="">Todos os grupos</option>
                                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($group->codigo); ?>" 
                                        <?php echo e(($groupFilter ?? '') === ($group->codigo ?? '') ? 'selected' : ''); ?>>
                                        <?php echo e($group->nome); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="site" class="form-label">Site</label>
                            <select name="site" id="site" class="form-select">
                                <option value="">Todos os sites</option>
                                <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($site->codigo); ?>" 
                                        <?php echo e(($siteFilter ?? '') === ($site->codigo ?? '') ? 'selected' : ''); ?>>
                                        <?php echo e($site->nome); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="environment" class="form-label">Ambiente</label>
                            <select name="environment" id="environment" class="form-select">
                                <option value="">Todos os ambientes</option>
                                <?php $__currentLoopData = $environments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $environment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($environment->codigo); ?>" 
                                        <?php echo e(($environmentFilter ?? '') === ($environment->codigo ?? '') ? 'selected' : ''); ?>>
                                        <?php echo e($environment->nome); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="search" class="form-label">Buscar</label>
                            <div class="input-group">
                                <input type="text" name="search" id="search" 
                                    class="form-control" placeholder="Chave ou descrição..."
                                    value="<?php echo e($searchFilter ?? ''); ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="uil uil-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="<?php echo e(route('admin.config.create')); ?>" 
                                    class="btn btn-success">
                                    <i class="uil uil-plus me-1"></i>
                                    Nova Configuração
                                </a>
                                
                                <button type="button" class="btn btn-warning" 
                                    onclick="clearCache()">
                                    <i class="uil uil-refresh me-1"></i>
                                    Limpar Cache
                                </button>
                                
                                <a href="<?php echo e(route('admin.config.export')); ?>" 
                                    class="btn btn-info">
                                    <i class="uil uil-export me-1"></i>
                                    Exportar
                                </a>

                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" 
                                        type="button" data-bs-toggle="dropdown">
                                        <i class="uil uil-apps me-1"></i>
                                        Grupos Rápidos
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <a class="dropdown-item" 
                                                    href="<?php echo e(route('admin.config.group', $group->codigo)); ?>">
                                                    <i class="<?php echo e($group->icone_class); ?> me-2"></i>
                                                    <?php echo e($group->nome); ?>

                                                </a>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Configurações -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <?php if($configs->count() > 0): ?>
                        <!-- Agrupar por grupo -->
                        <?php
                            $configsByGroup = $configs->groupBy('group.nome');
                        ?>

                        <?php $__currentLoopData = $configsByGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $groupConfigs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="config-group mb-4">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="<?php echo e($groupConfigs->first()->group->icone_class ?? 'uil uil-folder'); ?> me-2"></i>
                                    <?php echo e($groupName); ?>

                                </h5>

                                <div class="row">
                                    <?php $__currentLoopData = $groupConfigs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card border h-100">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="card-title mb-1">
                                                            <?php echo e($config->chave); ?>

                                                            <?php if($config->obrigatorio): ?>
                                                                <span class="badge bg-danger ms-1">Obrigatório</span>
                                                            <?php endif; ?>
                                                            <?php if($config->avancado): ?>
                                                                <span class="badge bg-warning ms-1">Avançado</span>
                                                            <?php endif; ?>
                                                        </h6>
                                                        
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-light" 
                                                                type="button" data-bs-toggle="dropdown">
                                                                <i class="uil uil-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item" 
                                                                        href="<?php echo e(route('admin.config.edit', $config->id)); ?>">
                                                                        <i class="uil uil-edit me-2"></i>
                                                                        Editar
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" 
                                                                        href="<?php echo e(route('admin.config.history', $config->id)); ?>">
                                                                        <i class="uil uil-history me-2"></i>
                                                                        Histórico
                                                                    </a>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <button class="dropdown-item text-danger" 
                                                                        onclick="deleteConfig(<?php echo e($config->id); ?>)">
                                                                        <i class="uil uil-trash me-2"></i>
                                                                        Excluir
                                                                    </button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <?php if($config->descricao): ?>
                                                        <p class="text-muted small mb-2"><?php echo e($config->descricao); ?></p>
                                                    <?php endif; ?>

                                                    <div class="mb-2">
                                                        <small class="text-muted">Tipo: </small>
                                                        <span class="badge bg-secondary"><?php echo e(ucfirst($config->tipo)); ?></span>
                                                    </div>

                                                    <!-- Formulário de edição rápida -->
                                                    <form class="config-form" data-config-id="<?php echo e($config->id); ?>">
                                                        <div class="mb-2">
                                                            <label class="form-label small">Valor:</label>
                                                            <?php if($config->tipo === 'boolean'): ?>
                                                                <select class="form-select form-select-sm" name="valor">
                                                                    <option value="0">Não</option>
                                                                    <option value="1">Sim</option>
                                                                </select>
                                                            <?php elseif(!empty($config->opcoes)): ?>
                                                                <select class="form-select form-select-sm" name="valor">
                                                                    <?php
                                                                        $opcoes = is_string($config->opcoes) ? json_decode($config->opcoes, true) : $config->opcoes;
                                                                    ?>
                                                                    <?php if(is_array($opcoes)): ?>
                                                                        <?php $__currentLoopData = $opcoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opcao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <option value="<?php echo e($opcao); ?>"><?php echo e($opcao); ?></option>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php endif; ?>
                                                                </select>
                                                            <?php elseif($config->tipo === 'array'): ?>
                                                                <textarea class="form-control form-control-sm" 
                                                                    name="valor" rows="2" 
                                                                    placeholder="item1,item2,item3"></textarea>
                                                            <?php elseif($config->tipo === 'json'): ?>
                                                                <textarea class="form-control form-control-sm" 
                                                                    name="valor" rows="3" 
                                                                    placeholder='{"chave": "valor"}'></textarea>
                                                            <?php else: ?>
                                                                <input type="text" class="form-control form-control-sm" 
                                                                    name="valor" placeholder="Digite o valor...">
                                                            <?php endif; ?>
                                                        </div>

                                                        <div class="row g-2 mb-2">
                                                            <div class="col">
                                                                <select class="form-select form-select-sm" name="site_id">
                                                                    <option value="">Todos os sites</option>
                                                                    <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <option value="<?php echo e($site->id); ?>"><?php echo e($site->nome); ?></option>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </select>
                                                            </div>
                                                            <div class="col">
                                                                <select class="form-select form-select-sm" name="ambiente_id">
                                                                    <option value="">Todos os ambientes</option>
                                                                    <?php $__currentLoopData = $environments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $environment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <option value="<?php echo e($environment->id); ?>"><?php echo e($environment->nome); ?></option>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                                            <i class="uil uil-check me-1"></i>
                                                            Salvar
                                                        </button>
                                                    </form>

                                                    <?php if($config->dica): ?>
                                                        <div class="mt-2">
                                                            <small class="text-info">
                                                                <i class="uil uil-info-circle me-1"></i>
                                                                <?php echo e($config->dica); ?>

                                                            </small>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center">
                            <?php echo e($configs->withQueryString()->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <div class="text-muted">
                                <i class="uil uil-search display-4 d-block mb-3"></i>
                                <h5>Nenhuma configuração encontrada</h5>
                                <p>Tente ajustar os filtros ou criar uma nova configuração.</p>
                                <a href="<?php echo e(route('admin.config.create')); ?>" class="btn btn-primary">
                                    <i class="uil uil-plus me-1"></i>
                                    Criar Configuração
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta configuração?</p>
                <p class="text-warning">
                    <i class="uil uil-exclamation-triangle me-1"></i>
                    Esta ação não pode ser desfeita.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Salvar configuração via AJAX
document.querySelectorAll('.config-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const configId = this.dataset.configId;
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Salvando...';
        
        try {
            const response = await fetch('<?php echo e(route("admin.config.set-value")); ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: new URLSearchParams({
                    config_id: configId,
                    valor: formData.get('valor'),
                    site_id: formData.get('site_id'),
                    ambiente_id: formData.get('ambiente_id')
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showSuccess(result.message);
                submitBtn.innerHTML = '<i class="uil uil-check me-1"></i> Salvo!';
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
        const response = await fetch('<?php echo e(route("admin.config.clear-cache")); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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

// Excluir configuração
function deleteConfig(configId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `<?php echo e(route('admin.config.index')); ?>/${configId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Funções de notificação (usando SweetAlert2 conforme padrão)
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/config/index.blade.php ENDPATH**/ ?>