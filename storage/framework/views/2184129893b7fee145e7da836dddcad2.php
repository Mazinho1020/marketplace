<?php $__env->startSection('title', 'Visualizar Pessoa'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0"><?php echo e($pessoa->nome); ?> <?php echo e($pessoa->sobrenome); ?></h1>
                    <p class="text-muted mb-0">
                        <?php
                            $tipos = explode(',', $pessoa->tipo);
                        ?>
                        <?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge bg-primary me-1"><?php echo e(ucfirst(trim($tipo))); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($pessoa->cargo_nome): ?>
                            <span class="badge bg-info"><?php echo e($pessoa->cargo_nome); ?></span>
                        <?php endif; ?>
                        <?php if($pessoa->departamento_nome): ?>
                            <span class="badge bg-secondary"><?php echo e($pessoa->departamento_nome); ?></span>
                        <?php endif; ?>
                        Status: 
                        <?php if($pessoa->status == 'ativo'): ?>
                            <span class="badge bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-warning"><?php echo e(ucfirst($pessoa->status)); ?></span>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <a href="/comerciantes/clientes/pessoas?empresa_id=<?php echo e($pessoa->empresa_id); ?>" 
                       class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="/comerciantes/clientes/pessoas/<?php echo e($pessoa->id); ?>/edit" 
                       class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmarExclusao(<?php echo e($pessoa->id); ?>)">
                        <i class="fas fa-trash"></i> Excluir
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- Informações Pessoais -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user"></i> Informações Pessoais
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nome Completo:</label>
                                        <p class="form-control-plaintext">
                                            <?php echo e($pessoa->nome); ?> <?php echo e($pessoa->sobrenome); ?>

                                            <?php if($pessoa->nome_social): ?>
                                                <small class="text-muted">"<?php echo e($pessoa->nome_social); ?>"</small>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">CPF/CNPJ:</label>
                                        <p class="form-control-plaintext"><?php echo e($pessoa->cpf_cnpj ?? 'Não informado'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email:</label>
                                        <p class="form-control-plaintext">
                                            <?php if($pessoa->email): ?>
                                                <a href="mailto:<?php echo e($pessoa->email); ?>"><?php echo e($pessoa->email); ?></a>
                                            <?php else: ?>
                                                Não informado
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Telefone:</label>
                                        <p class="form-control-plaintext">
                                            <?php if($pessoa->telefone): ?>
                                                <a href="tel:<?php echo e($pessoa->telefone); ?>"><?php echo e($pessoa->telefone); ?></a>
                                            <?php else: ?>
                                                Não informado
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Data de Nascimento:</label>
                                        <p class="form-control-plaintext">
                                            <?php if($pessoa->data_nascimento): ?>
                                                <?php echo e(\Carbon\Carbon::parse($pessoa->data_nascimento)->format('d/m/Y')); ?>

                                                <small class="text-muted">
                                                    (<?php echo e(\Carbon\Carbon::parse($pessoa->data_nascimento)->age); ?> anos)
                                                </small>
                                            <?php else: ?>
                                                Não informado
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                                                                <label class="form-label fw-bold">Gênero:</label>
                                        <div class="form-control-plaintext">
                                            <?php if($pessoa->genero): ?>
                                                <?php echo e(ucfirst($pessoa->genero)); ?>

                                            <?php else: ?>
                                                <span class="text-muted">Não informado</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if($pessoa->observacoes): ?>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Observações:</label>
                                        <p class="form-control-plaintext"><?php echo e($pessoa->observacoes); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Informações Profissionais -->
                    <?php if(str_contains($pessoa->tipo, 'funcionario')): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-briefcase"></i> Informações Profissionais
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Departamento:</label>
                                        <p class="form-control-plaintext">
                                            <?php if($pessoa->departamento_nome && $pessoa->departamento_id): ?>
                                                <a href="/comerciantes/clientes/departamentos/<?php echo e($pessoa->departamento_id); ?>" 
                                                   class="text-decoration-none">
                                                    <?php echo e($pessoa->departamento_nome); ?>

                                                </a>
                                            <?php else: ?>
                                                Não definido
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Cargo:</label>
                                        <p class="form-control-plaintext">
                                            <?php if($pessoa->cargo_nome && $pessoa->cargo_id): ?>
                                                <a href="/comerciantes/clientes/cargos/<?php echo e($pessoa->cargo_id); ?>" 
                                                   class="text-decoration-none">
                                                    <?php echo e($pessoa->cargo_nome); ?>

                                                </a>
                                            <?php else: ?>
                                                Não definido
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Data de Admissão:</label>
                                        <p class="form-control-plaintext">
                                            <?php if($pessoa->data_admissao): ?>
                                                <?php echo e(\Carbon\Carbon::parse($pessoa->data_admissao)->format('d/m/Y')); ?>

                                                <small class="text-muted">
                                                    (<?php echo e(\Carbon\Carbon::parse($pessoa->data_admissao)->diffForHumans()); ?>)
                                                </small>
                                            <?php else: ?>
                                                Não informado
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Salário Atual:</label>
                                        <p class="form-control-plaintext">
                                            <?php if($pessoa->salario_atual): ?>
                                                R$ <?php echo e(number_format($pessoa->salario_atual, 2, ',', '.')); ?>

                                            <?php else: ?>
                                                Não informado
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status:</label>
                                        <p class="form-control-plaintext">
                                            <?php if($pessoa->status == 'ativo'): ?>
                                                <span class="badge bg-success">Ativo</span>
                                            <?php elseif($pessoa->status == 'inativo'): ?>
                                                <span class="badge bg-secondary">Inativo</span>
                                            <?php elseif($pessoa->status == 'afastado'): ?>
                                                <span class="badge bg-warning">Afastado</span>
                                            <?php elseif($pessoa->status == 'demitido'): ?>
                                                <span class="badge bg-danger">Demitido</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark"><?php echo e(ucfirst($pessoa->status)); ?></span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Endereço -->
                    <?php if($pessoa->endereco_principal_id): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-map-marker-alt"></i> Endereço
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Endereço Principal:</label>
                                        <p class="form-control-plaintext">
                                            <a href="/comerciantes/enderecos/<?php echo e($pessoa->endereco_principal_id); ?>" 
                                               class="text-decoration-none">
                                                Ver endereço completo
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Estatísticas -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle"></i> Informações
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <small class="text-muted">
                                <strong>Cadastrado em:</strong><br>
                                <?php echo e(\Carbon\Carbon::parse($pessoa->created_at)->format('d/m/Y H:i')); ?>

                            </small>
                            <?php if($pessoa->updated_at && $pessoa->updated_at != $pessoa->created_at): ?>
                            <br><br>
                            <small class="text-muted">
                                <strong>Última alteração:</strong><br>
                                <?php echo e(\Carbon\Carbon::parse($pessoa->updated_at)->format('d/m/Y H:i')); ?>

                            </small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Ações Rápidas -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-bolt"></i> Ações Rápidas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <?php if($pessoa->email): ?>
                                <a href="mailto:<?php echo e($pessoa->email); ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-envelope"></i> Enviar Email
                                </a>
                                <?php endif; ?>
                                <?php if($pessoa->telefone): ?>
                                <a href="tel:<?php echo e($pessoa->telefone); ?>" 
                                   class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-phone"></i> Ligar
                                </a>
                                <?php endif; ?>
                                <?php if($pessoa->departamento_id): ?>
                                <a href="/comerciantes/clientes/departamentos/<?php echo e($pessoa->departamento_id); ?>" 
                                   class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-building"></i> Ver Departamento
                                </a>
                                <?php endif; ?>
                                <?php if($pessoa->cargo_id): ?>
                                <a href="/comerciantes/clientes/cargos/<?php echo e($pessoa->cargo_id); ?>" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-user-tie"></i> Ver Cargo
                                </a>
                                <?php endif; ?>
                                <a href="/comerciantes/clientes/pessoas/create?empresa_id=<?php echo e($pessoa->empresa_id); ?>" 
                                   class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-plus"></i> Nova Pessoa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalExclusao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir <strong><?php echo e($pessoa->nome); ?> <?php echo e($pessoa->sobrenome); ?></strong>?</p>
                <p class="text-warning"><strong>Atenção:</strong> Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExclusao" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalExclusao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir <strong><?php echo e($pessoa->nome); ?> <?php echo e($pessoa->sobrenome); ?></strong>?</p>
                <p class="text-warning"><strong>Atenção:</strong> Esta ação não pode ser desfeita e pode afetar outros registros relacionados.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExclusao" method="POST" style="display: inline;">
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
function confirmarExclusao(id) {
    const form = document.getElementById('formExclusao');
    form.action = `/comerciantes/clientes/pessoas/${id}`;
    
    const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
    modal.show();
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciante/pessoas/show.blade.php ENDPATH**/ ?>