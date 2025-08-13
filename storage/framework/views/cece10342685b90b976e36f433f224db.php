<?php $__env->startSection('title', 'Configurações de Produtos'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('comerciantes.dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('comerciantes.produtos.index')); ?>">Produtos</a></li>
                    <li class="breadcrumb-item active">Configurações</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="fas fa-cog me-2"></i>
                Configurações de Produtos
            </h1>
            <p class="text-muted mb-0">Gerencie tamanhos, sabores, ingredientes e outras personalizações</p>
        </div>
        <div class="col-auto">
            <a href="<?php echo e(route('comerciantes.produtos.configuracoes.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nova Configuração
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>
                Filtros
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('comerciantes.produtos.configuracoes.index')); ?>">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" 
                               name="search" 
                               id="search"
                               class="form-control" 
                               placeholder="Nome da configuração ou produto..."
                               value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="tipo_configuracao" class="form-label">Tipo</label>
                        <select name="tipo_configuracao" id="tipo_configuracao" class="form-select">
                            <option value="">Todos os tipos</option>
                            <?php $__currentLoopData = $tiposConfiguracao; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(request('tipo_configuracao') == $value ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="produto_id" class="form-label">Produto</label>
                        <select name="produto_id" id="produto_id" class="form-select">
                            <option value="">Todos os produtos</option>
                            <?php $__currentLoopData = $produtos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($produto->id); ?>" <?php echo e(request('produto_id') == $produto->id ? 'selected' : ''); ?>>
                                    <?php echo e($produto->nome); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-grid gap-2 d-md-flex w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>
                                Filtrar
                            </button>
                            <a href="<?php echo e(route('comerciantes.produtos.configuracoes.index')); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Configurações -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Configurações Cadastradas
                <span class="badge bg-primary ms-2"><?php echo e($configuracoes->total()); ?></span>
            </h5>
        </div>
        <div class="card-body">
            <?php if($configuracoes->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Configuração</th>
                                <th>Tipo</th>
                                <th>Itens</th>
                                <th>Status</th>
                                <th>Obrigatório</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $configuracoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $configuracao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-box text-muted me-2"></i>
                                            <div>
                                                <div class="fw-medium"><?php echo e($configuracao->produto->nome); ?></div>
                                                <small class="text-muted"><?php echo e($configuracao->produto->sku ?? 'Sem SKU'); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-medium"><?php echo e($configuracao->nome); ?></div>
                                            <?php if($configuracao->descricao): ?>
                                                <small class="text-muted"><?php echo e(Str::limit($configuracao->descricao, 50)); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo e($configuracao->tipo_descricao); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo e($configuracao->quantidade_itens_ativos); ?> ativos</span>
                                        <?php if($configuracao->quantidade_itens != $configuracao->quantidade_itens_ativos): ?>
                                            <span class="badge bg-secondary ms-1"><?php echo e($configuracao->quantidade_itens - $configuracao->quantidade_itens_ativos); ?> inativos</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo $configuracao->status_badge; ?>

                                    </td>
                                    <td>
                                        <?php echo $configuracao->obrigatorio_badge; ?>

                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo e(route('comerciantes.produtos.configuracoes.show', $configuracao)); ?>" 
                                               class="btn btn-outline-primary" 
                                               title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('comerciantes.produtos.configuracoes.edit', $configuracao)); ?>" 
                                               class="btn btn-outline-warning" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-<?php echo e($configuracao->ativo ? 'secondary' : 'success'); ?>" 
                                                    onclick="toggleAtivo(<?php echo e($configuracao->id); ?>)"
                                                    title="<?php echo e($configuracao->ativo ? 'Desativar' : 'Ativar'); ?>">
                                                <i class="fas fa-<?php echo e($configuracao->ativo ? 'pause' : 'play'); ?>"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    onclick="excluir(<?php echo e($configuracao->id); ?>, '<?php echo e($configuracao->nome); ?>')"
                                                    title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Mostrando <?php echo e($configuracoes->firstItem() ?? 0); ?> até <?php echo e($configuracoes->lastItem() ?? 0); ?> 
                        de <?php echo e($configuracoes->total()); ?> configurações
                    </div>
                    <?php echo e($configuracoes->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma configuração encontrada</h5>
                    <p class="text-muted mb-4">
                        <?php if(request()->hasAny(['search', 'tipo_configuracao', 'produto_id'])): ?>
                            Tente ajustar os filtros de busca.
                        <?php else: ?>
                            Comece criando uma configuração para seus produtos.
                        <?php endif; ?>
                    </p>
                    <a href="<?php echo e(route('comerciantes.produtos.configuracoes.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Criar Primeira Configuração
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a configuração <strong id="nomeConfiguracao"></strong>?</p>
                <p class="text-danger small mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta ação não pode ser desfeita.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExcluir" method="POST" style="display: inline;">
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
    function excluir(id, nome) {
        document.getElementById('nomeConfiguracao').textContent = nome;
        document.getElementById('formExcluir').action = `/comerciantes/produtos/configuracoes/${id}`;
        new bootstrap.Modal(document.getElementById('modalExcluir')).show();
    }

    function toggleAtivo(id) {
        fetch(`/comerciantes/produtos/configuracoes/${id}/toggle-ativo`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recarregar a página para atualizar o status
                window.location.reload();
            } else {
                alert('Erro ao alterar status: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao processar solicitação');
        });
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/produtos/configuracoes/index.blade.php ENDPATH**/ ?>