<?php $__env->startSection('title', 'Produtos'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-box text-primary me-2"></i>
                Produtos
            </h1>
            <p class="text-muted mb-0">Gerencie o catálogo de produtos da sua empresa</p>
        </div>
        <div>
            <a href="<?php echo e(route('comerciantes.produtos.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Novo Produto
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('comerciantes.produtos.index')); ?>" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="busca" class="form-control" 
                           placeholder="Nome, SKU ou código de barras..." 
                           value="<?php echo e(request('busca')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Categoria</label>
                    <select name="categoria_id" class="form-select">
                        <option value="">Todas as categorias</option>
                        <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($categoria->id); ?>" 
                                    <?php echo e(request('categoria_id') == $categoria->id ? 'selected' : ''); ?>>
                                <?php echo e($categoria->nome); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="disponivel" <?php echo e(request('status') == 'disponivel' ? 'selected' : ''); ?>>Disponível</option>
                        <option value="indisponivel" <?php echo e(request('status') == 'indisponivel' ? 'selected' : ''); ?>>Indisponível</option>
                        <option value="pausado" <?php echo e(request('status') == 'pausado' ? 'selected' : ''); ?>>Pausado</option>
                        <option value="esgotado" <?php echo e(request('status') == 'esgotado' ? 'selected' : ''); ?>>Esgotado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Filtros</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="estoque_baixo" 
                               id="estoque_baixo" <?php echo e(request('estoque_baixo') ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="estoque_baixo">
                            Estoque baixo
                        </label>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Links rápidos -->
    <div class="row mb-4">
        <div class="col-md-3">
            <a href="<?php echo e(route('comerciantes.produtos.categorias.index')); ?>" class="card text-decoration-none border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-tags fa-2x text-primary mb-2"></i>
                    <h6 class="card-title mb-0">Categorias</h6>
                    <small class="text-muted">Gerenciar categorias</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?php echo e(route('comerciantes.produtos.marcas.index')); ?>" class="card text-decoration-none border-success">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-2x text-success mb-2"></i>
                    <h6 class="card-title mb-0">Marcas</h6>
                    <small class="text-muted">Gerenciar marcas</small>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <h6 class="card-title mb-0">Estoque Baixo</h6>
                    <small class="text-muted">
                        <?php echo e($produtos->where('estoque_baixo', true)->count()); ?> produtos
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                    <h6 class="card-title mb-0">Total</h6>
                    <small class="text-muted"><?php echo e($produtos->total()); ?> produtos</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Lista de Produtos
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if($produtos->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="80">Imagem</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>SKU</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Status</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $produtos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo e($produto->url_imagem_principal); ?>" 
                                             alt="<?php echo e($produto->nome); ?>" 
                                             class="img-thumbnail" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?php echo e($produto->nome); ?></strong>
                                            <?php if($produto->destaque): ?>
                                                <span class="badge bg-warning ms-1">Destaque</span>
                                            <?php endif; ?>
                                            <?php if($produto->estoque_baixo): ?>
                                                <span class="badge bg-danger ms-1">Estoque Baixo</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if($produto->descricao_curta): ?>
                                            <small class="text-muted"><?php echo e(\Str::limit($produto->descricao_curta, 50)); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($produto->categoria): ?>
                                            <span class="badge bg-secondary"><?php echo e($produto->categoria->nome); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code><?php echo e($produto->sku ?: '-'); ?></code>
                                    </td>
                                    <td>
                                        <strong class="text-success"><?php echo e($produto->preco_venda_formatado); ?></strong>
                                    </td>
                                    <td>
                                        <?php if($produto->controla_estoque): ?>
                                            <span class="badge <?php echo e($produto->estoque_baixo ? 'bg-danger' : 'bg-success'); ?>">
                                                <?php echo e(number_format($produto->estoque_atual, 0)); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">Não controlado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo $produto->status_badge; ?>

                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo e(route('comerciantes.produtos.show', $produto)); ?>" 
                                               class="btn btn-outline-primary" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('comerciantes.produtos.edit', $produto)); ?>" 
                                               class="btn btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    title="Excluir" data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal<?php echo e($produto->id); ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Modal de Exclusão -->
                                        <div class="modal fade" id="deleteModal<?php echo e($produto->id); ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Excluir Produto</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Tem certeza que deseja excluir o produto <strong><?php echo e($produto->nome); ?></strong>?</p>
                                                        <p class="text-muted small">Esta ação não pode ser desfeita.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <form action="<?php echo e(route('comerciantes.produtos.destroy', $produto)); ?>" 
                                                              method="POST" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-danger">Excluir</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="card-footer">
                    <?php echo e($produtos->appends(request()->query())->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum produto encontrado</h5>
                    <p class="text-muted">Que tal começar criando seu primeiro produto?</p>
                    <a href="<?php echo e(route('comerciantes.produtos.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Criar Primeiro Produto
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // Auto-submit do formulário de filtros quando mudar categoria ou status
    document.querySelectorAll('select[name="categoria_id"], select[name="status"]').forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('comerciantes.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/produtos/index.blade.php ENDPATH**/ ?>