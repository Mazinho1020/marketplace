<?php $__env->startSection('title', 'Editar Conta Gerencial'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('comerciantes.dashboard.empresa', $empresa)); ?>">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('comerciantes.empresas.financeiro.dashboard', $empresa)); ?>">Financeiro</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.index', $empresa)); ?>">Contas Gerenciais</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.show', ['empresa' => $empresa, 'id' => $conta->id])); ?>"><?php echo e($conta->nome); ?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-edit"></i> Editar Conta Gerencial
        </h1>
        <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.show', ['empresa' => $empresa, 'id' => $conta->id])); ?>" 
           class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Alertas -->
    <?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Formulário -->
    <form action="<?php echo e(route('comerciantes.empresas.financeiro.contas.update', ['empresa' => $empresa, 'id' => $conta->id])); ?>" 
          method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="row">
            <div class="col-md-8">
                <!-- Informações Básicas -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Informações Básicas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="nome" class="form-label">Nome da Conta *</label>
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
                                           value="<?php echo e(old('nome', $conta->nome)); ?>" 
                                           required>
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

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="codigo" class="form-label">Código</label>
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
                                           value="<?php echo e(old('codigo', $conta->codigo)); ?>" 
                                           placeholder="Ex: 1.1.01">
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
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
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
                                      rows="3"><?php echo e(old('descricao', $conta->descricao)); ?></textarea>
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
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="natureza" class="form-label">Natureza *</label>
                                    <select class="form-control <?php $__errorArgs = ['natureza'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="natureza" 
                                            name="natureza" 
                                            required>
                                        <option value="">Selecione...</option>
                                        <?php $__currentLoopData = $naturezas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $natureza): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($natureza->value); ?>" 
                                                <?php echo e(old('natureza', $conta->natureza->value) === $natureza->value ? 'selected' : ''); ?>>
                                            <?php echo e($natureza->label()); ?> (<?php echo e($natureza->value); ?>)
                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['natureza'];
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

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nivel" class="form-label">Nível</label>
                                    <input type="number" 
                                           class="form-control <?php $__errorArgs = ['nivel'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="nivel" 
                                           name="nivel" 
                                           value="<?php echo e(old('nivel', $conta->nivel)); ?>" 
                                           min="0"
                                           max="10">
                                    <?php $__errorArgs = ['nivel'];
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
                        </div>
                    </div>
                </div>

                <!-- Relacionamentos -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-sitemap"></i> Relacionamentos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="categoria_id" class="form-label">Categoria</label>
                                    <select class="form-control <?php $__errorArgs = ['categoria_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="categoria_id" 
                                            name="categoria_id">
                                        <option value="">Nenhuma categoria</option>
                                        <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($categoria->id); ?>" 
                                                <?php echo e(old('categoria_id', $conta->categoria_id) == $categoria->id ? 'selected' : ''); ?>>
                                            <?php echo e($categoria->nome); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['categoria_id'];
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

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="conta_pai_id" class="form-label">Conta Pai</label>
                                    <select class="form-control <?php $__errorArgs = ['conta_pai_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="conta_pai_id" 
                                            name="conta_pai_id">
                                        <option value="">Conta raiz (sem pai)</option>
                                        <?php $__currentLoopData = $contasPai; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contaPai): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($contaPai->id); ?>" 
                                                <?php echo e(old('conta_pai_id', $conta->conta_pai_id) == $contaPai->id ? 'selected' : ''); ?>>
                                            <?php echo e($contaPai->nome); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['conta_pai_id'];
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
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="classificacao_dre_id" class="form-label">Classificação DRE</label>
                                    <select class="form-control <?php $__errorArgs = ['classificacao_dre_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="classificacao_dre_id" 
                                            name="classificacao_dre_id">
                                        <option value="">Nenhuma classificação</option>
                                        <?php $__currentLoopData = $classificacoesDre; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classificacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($classificacao->id); ?>" 
                                                <?php echo e(old('classificacao_dre_id', $conta->classificacao_dre_id) == $classificacao->id ? 'selected' : ''); ?>>
                                            <?php echo e($classificacao->nome); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['classificacao_dre_id'];
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

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tipo_id" class="form-label">Tipo</label>
                                    <select class="form-control <?php $__errorArgs = ['tipo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="tipo_id" 
                                            name="tipo_id">
                                        <option value="">Nenhum tipo</option>
                                        <?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($tipo->id); ?>" 
                                                <?php echo e(old('tipo_id', $conta->tipo_id) == $tipo->id ? 'selected' : ''); ?>>
                                            <?php echo e($tipo->nome); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['tipo_id'];
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
                        </div>
                    </div>
                </div>

                <!-- Configurações Avançadas -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs"></i> Configurações Avançadas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="grupo_dre" class="form-label">Grupo DRE</label>
                                    <input type="text" 
                                           class="form-control <?php $__errorArgs = ['grupo_dre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="grupo_dre" 
                                           name="grupo_dre" 
                                           value="<?php echo e(old('grupo_dre', $conta->grupo_dre)); ?>" 
                                           placeholder="Ex: Receitas Operacionais">
                                    <?php $__errorArgs = ['grupo_dre'];
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

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ordem_exibicao" class="form-label">Ordem de Exibição</label>
                                    <input type="number" 
                                           class="form-control <?php $__errorArgs = ['ordem_exibicao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="ordem_exibicao" 
                                           name="ordem_exibicao" 
                                           value="<?php echo e(old('ordem_exibicao', $conta->ordem_exibicao)); ?>" 
                                           min="0">
                                    <?php $__errorArgs = ['ordem_exibicao'];
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
                        </div>

                        <!-- Switches de Configuração -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input type="hidden" name="ativo" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="ativo" 
                                           name="ativo" 
                                           value="1"
                                           <?php echo e(old('ativo', $conta->ativo) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="ativo">
                                        Conta Ativa
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="hidden" name="aceita_lancamento" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="aceita_lancamento" 
                                           name="aceita_lancamento" 
                                           value="1"
                                           <?php echo e(old('aceita_lancamento', $conta->aceita_lancamento) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="aceita_lancamento">
                                        Aceita Lançamentos
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="hidden" name="e_sintetica" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="e_sintetica" 
                                           name="e_sintetica" 
                                           value="1"
                                           <?php echo e(old('e_sintetica', $conta->e_sintetica) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="e_sintetica">
                                        É Conta Sintética
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input type="hidden" name="e_custo" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="e_custo" 
                                           name="e_custo" 
                                           value="1"
                                           <?php echo e(old('e_custo', $conta->e_custo) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="e_custo">
                                        É Conta de Custo
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="hidden" name="e_despesa" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="e_despesa" 
                                           name="e_despesa" 
                                           value="1"
                                           <?php echo e(old('e_despesa', $conta->e_despesa) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="e_despesa">
                                        É Conta de Despesa
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="hidden" name="e_receita" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="e_receita" 
                                           name="e_receita" 
                                           value="1"
                                           <?php echo e(old('e_receita', $conta->e_receita) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="e_receita">
                                        É Conta de Receita
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Visual -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-palette"></i> Personalização Visual
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="cor" class="form-label">Cor</label>
                            <input type="color" 
                                   class="form-control form-control-color <?php $__errorArgs = ['cor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="cor" 
                                   name="cor" 
                                   value="<?php echo e(old('cor', $conta->cor ?? '#007bff')); ?>"
                                   title="Escolha uma cor">
                            <?php $__errorArgs = ['cor'];
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

                        <div class="form-group mb-3">
                            <label for="icone" class="form-label">Ícone</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['icone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="icone" 
                                   name="icone" 
                                   value="<?php echo e(old('icone', $conta->icone)); ?>" 
                                   placeholder="Ex: fas fa-money-bill">
                            <small class="form-text text-muted">
                                Use classes do Font Awesome. <a href="https://fontawesome.com/icons" target="_blank">Ver ícones</a>
                            </small>
                            <?php $__errorArgs = ['icone'];
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

                        <?php if(old('icone', $conta->icone)): ?>
                        <div class="text-center">
                            <i class="<?php echo e(old('icone', $conta->icone)); ?>" style="font-size: 2rem;"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Ações -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-save"></i> Ações
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                            
                            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas.show', ['empresa' => $empresa, 'id' => $conta->id])); ?>" 
                               class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview do ícone
    const iconeInput = document.getElementById('icone');
    const previewContainer = document.querySelector('.text-center');
    
    if (iconeInput) {
        iconeInput.addEventListener('input', function() {
            const icone = this.value.trim();
            if (icone && previewContainer) {
                previewContainer.innerHTML = `<i class="${icone}" style="font-size: 2rem;"></i>`;
            } else if (previewContainer) {
                previewContainer.innerHTML = '';
            }
        });
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/financeiro/contas/edit.blade.php ENDPATH**/ ?>