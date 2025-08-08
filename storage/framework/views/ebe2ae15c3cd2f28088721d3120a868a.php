<?php $__env->startSection('title', 'Editar Cargo'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Editar Cargo</h1>
                    <p class="text-muted mb-0"><?php echo e($cargo->nome); ?></p>
                </div>
                <div>
                    <a href="/comerciantes/clientes/cargos/<?php echo e($cargo->id); ?>" 
                       class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmarExclusao(<?php echo e($cargo->id); ?>)">
                        <i class="fas fa-trash"></i> Excluir
                    </button>
                </div>
            </div>

            <!-- Formulário de Edição -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-edit"></i> Dados do Cargo
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if($errors->any()): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Erro!</strong> Corrija os problemas abaixo:
                                    <ul class="mb-0 mt-2">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="/comerciantes/clientes/cargos/<?php echo e($cargo->id); ?>">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <input type="hidden" name="empresa_id" value="<?php echo e($cargo->empresa_id); ?>">

                                <div class="row">
                                    <!-- Código -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="codigo" class="form-label">
                                                Código
                                            </label>
                                            <input type="text" 
                                                   class="form-control <?php $__errorArgs = ['codigo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="codigo" 
                                                   name="codigo" 
                                                   value="<?php echo e(old('codigo', $cargo->codigo)); ?>"
                                                   maxlength="20">
                                            <?php $__errorArgs = ['codigo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <small class="form-text text-muted">
                                                Código único para identificação (opcional)
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Nome -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nome" class="form-label">
                                                Nome <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control <?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="nome" 
                                                   name="nome" 
                                                   value="<?php echo e(old('nome', $cargo->nome)); ?>"
                                                   required 
                                                   maxlength="100">
                                            <?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>

                                    <!-- Descrição -->
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="descricao" class="form-label">Descrição</label>
                                            <textarea class="form-control <?php $__errorArgs = ['descricao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                      id="descricao" 
                                                      name="descricao" 
                                                      rows="3"
                                                      maxlength="500"><?php echo e(old('descricao', $cargo->descricao)); ?></textarea>
                                            <?php $__errorArgs = ['descricao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <small class="form-text text-muted">
                                                Descrição das responsabilidades e atividades do cargo
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Departamento -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="departamento_id" class="form-label">Departamento</label>
                                            <select class="form-select <?php $__errorArgs = ['departamento_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                    id="departamento_id" 
                                                    name="departamento_id">
                                                <option value="">Selecione um departamento</option>
                                                <?php $__currentLoopData = $departamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $departamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($departamento->id); ?>" 
                                                            <?php echo e(old('departamento_id', $cargo->departamento_id) == $departamento->id ? 'selected' : ''); ?>>
                                                        <?php echo e($departamento->nome); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <?php $__errorArgs = ['departamento_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>

                                    <!-- Nível Hierárquico -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nivel_hierarquico" class="form-label">Nível Hierárquico</label>
                                            <select class="form-select <?php $__errorArgs = ['nivel_hierarquico'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                    id="nivel_hierarquico" 
                                                    name="nivel_hierarquico">
                                                <option value="">Selecione o nível</option>
                                                <option value="1" <?php echo e(old('nivel_hierarquico', $cargo->nivel_hierarquico) == '1' ? 'selected' : ''); ?>>
                                                    Nível 1 - Operacional
                                                </option>
                                                <option value="2" <?php echo e(old('nivel_hierarquico', $cargo->nivel_hierarquico) == '2' ? 'selected' : ''); ?>>
                                                    Nível 2 - Supervisão
                                                </option>
                                                <option value="3" <?php echo e(old('nivel_hierarquico', $cargo->nivel_hierarquico) == '3' ? 'selected' : ''); ?>>
                                                    Nível 3 - Coordenação
                                                </option>
                                                <option value="4" <?php echo e(old('nivel_hierarquico', $cargo->nivel_hierarquico) == '4' ? 'selected' : ''); ?>>
                                                    Nível 4 - Gerência
                                                </option>
                                                <option value="5" <?php echo e(old('nivel_hierarquico', $cargo->nivel_hierarquico) == '5' ? 'selected' : ''); ?>>
                                                    Nível 5 - Diretoria
                                                </option>
                                            </select>
                                            <?php $__errorArgs = ['nivel_hierarquico'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>

                                    <!-- Salário Base -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="salario_base" class="form-label">Salário Base</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" 
                                                       class="form-control <?php $__errorArgs = ['salario_base'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                       id="salario_base" 
                                                       name="salario_base" 
                                                       value="<?php echo e(old('salario_base', $cargo->salario_base)); ?>"
                                                       min="0" 
                                                       step="0.01"
                                                       max="999999.99">
                                                <?php $__errorArgs = ['salario_base'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                            <small class="form-text text-muted">
                                                Valor base do salário para o cargo (opcional)
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Status Ativo -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input <?php $__errorArgs = ['ativo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                       type="checkbox" 
                                                       id="ativo" 
                                                       name="ativo" 
                                                       value="1"
                                                       <?php echo e(old('ativo', $cargo->ativo) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="ativo">
                                                    <span class="fw-bold text-success">Ativo</span>
                                                </label>
                                            </div>
                                            <?php $__errorArgs = ['ativo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <small class="form-text text-muted">
                                                Desmarque para desativar o cargo
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botões -->
                                <div class="row">
                                    <div class="col-12">
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <a href="/comerciantes/clientes/cargos/<?php echo e($cargo->id); ?>" 
                                               class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Salvar Alterações
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar com Informações -->
                <div class="col-lg-4">
                    <!-- Estatísticas -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle"></i> Informações
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <h4 class="text-primary"><?php echo e($stats['funcionarios']); ?></h4>
                                <p class="text-muted mb-1">Funcionários vinculados</p>
                                <small class="text-success"><?php echo e($stats['funcionarios_ativos']); ?> ativos</small>
                            </div>
                            <hr>
                            <small class="text-muted">
                                <strong>Criado em:</strong><br>
                                <?php echo e(\Carbon\Carbon::parse($cargo->created_at)->format('d/m/Y H:i')); ?>

                            </small>
                            <?php if($cargo->updated_at && $cargo->updated_at != $cargo->created_at): ?>
                            <br><br>
                            <small class="text-muted">
                                <strong>Última alteração:</strong><br>
                                <?php echo e(\Carbon\Carbon::parse($cargo->updated_at)->format('d/m/Y H:i')); ?>

                            </small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Alertas -->
                    <?php if($stats['funcionarios'] > 0): ?>
                    <div class="card mt-3">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-exclamation-triangle"></i> Atenção
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="small mb-0">
                                Este cargo possui funcionários vinculados. 
                                Alterações podem afetar os dados dos funcionários.
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Links Úteis -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-link"></i> Links Úteis
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="/comerciantes/clientes/cargos?empresa_id=<?php echo e($cargo->empresa_id); ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-list"></i> Todos os Cargos
                                </a>
                                <?php if($cargo->departamento_id): ?>
                                <a href="/comerciantes/clientes/departamentos/<?php echo e($cargo->departamento_id); ?>" 
                                   class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-building"></i> Ver Departamento
                                </a>
                                <?php endif; ?>
                                <a href="/comerciantes/clientes/pessoas?cargo_id=<?php echo e($cargo->id); ?>" 
                                   class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-users"></i> Ver Funcionários
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
                <p>Tem certeza que deseja excluir o cargo <strong><?php echo e($cargo->nome); ?></strong>?</p>
                <p class="text-warning"><strong>Atenção:</strong> Esta ação não pode ser desfeita.</p>
                <?php if($stats['funcionarios'] > 0): ?>
                <div class="alert alert-danger">
                    <strong>Aviso:</strong> Este cargo possui 
                    <?php echo e($stats['funcionarios']); ?> funcionário(s) vinculado(s).
                    Não será possível excluir enquanto houver vínculos.
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <?php if($stats['funcionarios'] == 0): ?>
                <form id="formExclusao" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
                <?php else: ?>
                <button type="button" class="btn btn-danger" disabled>
                    Não é possível excluir
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function confirmarExclusao(id) {
    const form = document.getElementById('formExclusao');
    if (form) {
        form.action = `/comerciantes/clientes/cargos/${id}`;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
    modal.show();
}

// Formatação do campo de salário
document.getElementById('salario_base').addEventListener('input', function(e) {
    let value = e.target.value;
    
    // Remove caracteres não numéricos, exceto ponto e vírgula
    value = value.replace(/[^\d.,]/g, '');
    
    // Substitui vírgula por ponto
    value = value.replace(',', '.');
    
    e.target.value = value;
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/cargos/edit.blade.php ENDPATH**/ ?>