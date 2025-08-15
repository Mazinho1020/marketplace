<?php if($produtos->count() > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Produto</th>
                    <th class="text-center">Estoque Atual</th>
                    <?php if($tipo !== 'normal'): ?>
                        <th class="text-center">Estoque Mínimo</th>
                    <?php endif; ?>
                    <th class="text-center">Status</th>
                    <th class="text-center">Última Atualização</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $produtos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if($produto->imagem_principal): ?>
                                    <img src="<?php echo e($produto->imagem_principal); ?>" 
                                         alt="<?php echo e($produto->nome); ?>" 
                                         class="rounded me-3"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h6 class="mb-1"><?php echo e($produto->nome); ?></h6>
                                    <div class="d-flex gap-2">
                                        <?php if($produto->categoria): ?>
                                            <span class="badge bg-info text-dark"><?php echo e($produto->categoria->nome); ?></span>
                                        <?php endif; ?>
                                        <?php if($produto->marca): ?>
                                            <span class="badge bg-secondary"><?php echo e($produto->marca->nome); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($produto->sku): ?>
                                        <small class="text-muted">SKU: <?php echo e($produto->sku); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="fs-5 fw-bold 
                                <?php if($tipo === 'zerado'): ?> text-danger
                                <?php elseif($tipo === 'baixo'): ?> text-warning
                                <?php elseif($tipo === 'critico'): ?> text-info
                                <?php else: ?> text-success
                                <?php endif; ?>">
                                <?php echo e($produto->quantidade_estoque ?? 0); ?>

                            </span>
                        </td>
                        <?php if($tipo !== 'normal'): ?>
                            <td class="text-center">
                                <span class="text-muted"><?php echo e($produto->estoque_minimo ?? '-'); ?></span>
                            </td>
                        <?php endif; ?>
                        <td class="text-center">
                            <?php if($tipo === 'zerado'): ?>
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i>Esgotado
                                </span>
                            <?php elseif($tipo === 'baixo'): ?>
                                <span class="badge bg-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Baixo
                                </span>
                            <?php elseif($tipo === 'critico'): ?>
                                <span class="badge bg-info">
                                    <i class="fas fa-exclamation me-1"></i>Crítico
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Normal
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center text-muted">
                            <?php echo e($produto->updated_at->format('d/m/Y H:i')); ?>

                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('comerciantes.produtos.show', $produto->id)); ?>" 
                                   class="btn btn-outline-primary btn-sm"
                                   title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('comerciantes.produtos.edit', $produto->id)); ?>" 
                                   class="btn btn-outline-secondary btn-sm"
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if($tipo !== 'normal'): ?>
                                    <button type="button" 
                                            class="btn btn-outline-success btn-sm"
                                            onclick="atualizarEstoque(<?php echo e($produto->id); ?>, '<?php echo e($produto->nome); ?>')"
                                            title="Atualizar Estoque">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <?php if($tipo === 'normal' && $produtos->count() >= 50): ?>
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle me-2"></i>
            Exibindo apenas os primeiros 50 produtos com estoque normal. 
            Para ver todos, acesse a <a href="<?php echo e(route('comerciantes.produtos.index')); ?>">lista completa de produtos</a>.
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-check-circle text-success fs-1 mb-3"></i>
        <h5 class="text-muted"><?php echo e($mensagemVazia); ?></h5>
    </div>
<?php endif; ?>

<!-- Modal para Atualizar Estoque -->
<div class="modal fade" id="modalAtualizarEstoque" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-cubes text-primary me-2"></i>
                    Atualizar Estoque
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAtualizarEstoque">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Produto:</strong> <span id="nomeProdutoEstoque"></span>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_movimentacao" class="form-label fw-semibold">Tipo de Movimentação *</label>
                        <select class="form-select" id="tipo_movimentacao" name="tipo_movimentacao" required>
                            <option value="">Selecione o tipo</option>
                            <option value="entrada">Entrada (Adicionar ao estoque)</option>
                            <option value="saida">Saída (Remover do estoque)</option>
                            <option value="ajuste">Ajuste (Definir quantidade exata)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantidade" class="form-label fw-semibold">Quantidade *</label>
                        <input type="number" 
                               class="form-control" 
                               id="quantidade" 
                               name="quantidade" 
                               min="0" 
                               step="1" 
                               required>
                        <small class="text-muted" id="ajuda-quantidade">Digite a quantidade a ser movimentada</small>
                    </div>

                    <div class="mb-3">
                        <label for="observacao" class="form-label fw-semibold">Observação</label>
                        <textarea class="form-control" 
                                  id="observacao" 
                                  name="observacao" 
                                  rows="2" 
                                  placeholder="Motivo da movimentação (opcional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Atualizar Estoque
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let produtoIdAtual = null;

function atualizarEstoque(produtoId, nomeProduto) {
    produtoIdAtual = produtoId;
    $('#nomeProdutoEstoque').text(nomeProduto);
    $('#formAtualizarEstoque')[0].reset();
    $('#modalAtualizarEstoque').modal('show');
}

// Alterar ajuda baseado no tipo selecionado
$('#tipo_movimentacao').on('change', function() {
    const tipo = $(this).val();
    const ajuda = $('#ajuda-quantidade');
    
    switch(tipo) {
        case 'entrada':
            ajuda.text('Digite a quantidade a ser adicionada ao estoque');
            break;
        case 'saida':
            ajuda.text('Digite a quantidade a ser removida do estoque');
            break;
        case 'ajuste':
            ajuda.text('Digite a quantidade final que o estoque deve ter');
            break;
        default:
            ajuda.text('Digite a quantidade a ser movimentada');
    }
});

// Submit do formulário de estoque
$('#formAtualizarEstoque').on('submit', function(e) {
    e.preventDefault();
    
    if (!produtoIdAtual) return;
    
    const formData = new FormData(this);
    const btn = $(this).find('button[type="submit"]');
    const originalText = btn.html();
    
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Atualizando...');
    
    $.ajax({
        url: `<?php echo e(url('comerciantes/produtos')); ?>/${produtoIdAtual}/estoque`,
        type: 'PATCH',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#modalAtualizarEstoque').modal('hide');
                location.reload();
            } else {
                toastr.error(response.message || 'Erro ao atualizar estoque');
            }
        },
        error: function(xhr) {
            let message = 'Erro ao atualizar estoque';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            toastr.error(message);
        },
        complete: function() {
            btn.prop('disabled', false).html(originalText);
        }
    });
});
</script>
<?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/produtos/partials/tabela-estoque.blade.php ENDPATH**/ ?>