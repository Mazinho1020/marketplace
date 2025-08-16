<?php $__env->startSection('title', 'Editar Conta a Receber'); ?>

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
                <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.index', $empresa)); ?>">Contas a Receber</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.show', ['empresa' => $empresa, 'id' => $contaReceber->id])); ?>"><?php echo e($contaReceber->descricao); ?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Editar Conta a Receber</h1>
        <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.show', ['empresa' => $empresa, 'id' => $contaReceber->id])); ?>" 
           class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Alertas -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Formulário -->
    <form method="POST" action="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.update', ['empresa' => $empresa, 'id' => $contaReceber->id])); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Dados Principais -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Dados Principais
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="descricao" class="form-label">Descrição *</label>
                                    <input type="text" name="descricao" id="descricao" 
                                           class="form-control <?php $__errorArgs = ['descricao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('descricao', $contaReceber->descricao)); ?>" 
                                           placeholder="Ex: Venda de produto/serviço">
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
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="conta_gerencial_id" class="form-label">Conta Gerencial</label>
                                    <select name="conta_gerencial_id" id="conta_gerencial_id" 
                                            class="form-control <?php $__errorArgs = ['conta_gerencial_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Selecione uma conta gerencial</option>
                                        <?php $__currentLoopData = $contasGerenciais; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($conta->id); ?>" 
                                                    <?php echo e(old('conta_gerencial_id', $contaReceber->conta_gerencial_id) == $conta->id ? 'selected' : ''); ?>>
                                                <?php echo e($conta->codigo); ?> - <?php echo e($conta->nome); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['conta_gerencial_id'];
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
                                <div class="mb-3">
                                    <label for="pessoa_id" class="form-label">Cliente</label>
                                    <select name="pessoa_id" id="pessoa_id" 
                                            class="form-control <?php $__errorArgs = ['pessoa_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Selecione um cliente</option>
                                        <?php $__currentLoopData = $pessoas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pessoa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($pessoa->id); ?>" 
                                                    <?php echo e(old('pessoa_id', $contaReceber->pessoa_id) == $pessoa->id ? 'selected' : ''); ?>>
                                                <?php echo e($pessoa->nome); ?> 
                                                <?php if($pessoa->cpf_cnpj): ?>
                                                    (<?php echo e($pessoa->cpf_cnpj); ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['pessoa_id'];
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
                                <div class="mb-3">
                                    <label for="numero_documento" class="form-label">Número do Documento</label>
                                    <input type="text" name="numero_documento" id="numero_documento" 
                                           class="form-control <?php $__errorArgs = ['numero_documento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('numero_documento', $contaReceber->numero_documento)); ?>" 
                                           placeholder="Ex: NF-001">
                                    <?php $__errorArgs = ['numero_documento'];
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
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="observacoes" class="form-label">Observações</label>
                                    <textarea name="observacoes" id="observacoes" 
                                              class="form-control <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                              rows="2" 
                                              placeholder="Informações adicionais"><?php echo e(old('observacoes', $contaReceber->observacoes)); ?></textarea>
                                    <?php $__errorArgs = ['observacoes'];
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

                <!-- Valores e Datas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-dollar-sign"></i> Valores e Datas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valor_original" class="form-label">Valor Original *</label>
                                    <input type="number" step="0.01" name="valor_original" id="valor_original" 
                                           class="form-control <?php $__errorArgs = ['valor_original'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('valor_original', $contaReceber->valor_bruto)); ?>" 
                                           placeholder="0,00">
                                    <?php $__errorArgs = ['valor_original'];
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
                                <div class="mb-3">
                                    <label for="data_vencimento" class="form-label">Data de Vencimento *</label>
                                    <input type="date" name="data_vencimento" id="data_vencimento" 
                                           class="form-control <?php $__errorArgs = ['data_vencimento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('data_vencimento', $contaReceber->data_vencimento->format('Y-m-d'))); ?>">
                                    <?php $__errorArgs = ['data_vencimento'];
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
                                <div class="mb-3">
                                    <label for="data_emissao" class="form-label">Data de Emissão</label>
                                    <input type="date" name="data_emissao" id="data_emissao" 
                                           class="form-control <?php $__errorArgs = ['data_emissao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('data_emissao', $contaReceber->data_emissao ? $contaReceber->data_emissao->format('Y-m-d') : '')); ?>">
                                    <?php $__errorArgs = ['data_emissao'];
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
                                <div class="mb-3">
                                    <label for="data_competencia" class="form-label">Data de Competência</label>
                                    <input type="date" name="data_competencia" id="data_competencia" 
                                           class="form-control <?php $__errorArgs = ['data_competencia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('data_competencia', $contaReceber->data_competencia ? $contaReceber->data_competencia->format('Y-m-d') : '')); ?>">
                                    <?php $__errorArgs = ['data_competencia'];
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="valor_desconto" class="form-label">Desconto</label>
                                    <input type="number" step="0.01" name="valor_desconto" id="valor_desconto" 
                                           class="form-control <?php $__errorArgs = ['valor_desconto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('valor_desconto', $contaReceber->valor_desconto)); ?>" 
                                           placeholder="0,00">
                                    <?php $__errorArgs = ['valor_desconto'];
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
                                <div class="mb-3">
                                    <label for="valor_acrescimo" class="form-label">Acréscimo</label>
                                    <input type="number" step="0.01" name="valor_acrescimo" id="valor_acrescimo" 
                                           class="form-control <?php $__errorArgs = ['valor_acrescimo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('valor_acrescimo', $contaReceber->valor_acrescimo)); ?>" 
                                           placeholder="0,00">
                                    <?php $__errorArgs = ['valor_acrescimo'];
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
                                <div class="mb-3">
                                    <label for="valor_final" class="form-label">Valor Final</label>
                                    <input type="number" step="0.01" name="valor_final" id="valor_final" 
                                           class="form-control" 
                                           value="<?php echo e(old('valor_final', $contaReceber->valor_liquido)); ?>" 
                                           readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Configurações -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog"></i> Configurações
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="natureza_financeira" class="form-label">Natureza</label>
                            <select name="natureza_financeira" id="natureza_financeira" 
                                    class="form-control <?php $__errorArgs = ['natureza_financeira'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="entrada" <?php echo e(old('natureza_financeira', $contaReceber->natureza_financeira) == 'entrada' ? 'selected' : ''); ?>>
                                    Conta a Receber
                                </option>
                            </select>
                            <?php $__errorArgs = ['natureza_financeira'];
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

                        <div class="mb-3">
                            <label for="situacao_financeira" class="form-label">Situação</label>
                            <select name="situacao_financeira" id="situacao_financeira" 
                                    class="form-control <?php $__errorArgs = ['situacao_financeira'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="pendente" <?php echo e(old('situacao_financeira', $contaReceber->situacao_financeira->value) == 'pendente' ? 'selected' : ''); ?>>
                                    Pendente
                                </option>
                                <option value="pago" <?php echo e(old('situacao_financeira', $contaReceber->situacao_financeira->value) == 'pago' ? 'selected' : ''); ?>>
                                    Recebido
                                </option>
                                <option value="cancelado" <?php echo e(old('situacao_financeira', $contaReceber->situacao_financeira->value) == 'cancelado' ? 'selected' : ''); ?>>
                                    Cancelado
                                </option>
                            </select>
                            <?php $__errorArgs = ['situacao_financeira'];
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

                        <?php if($contaReceber->situacao_financeira->value == 'pago'): ?>
                            <div class="mb-3">
                                <label for="data_pagamento" class="form-label">Data do Recebimento</label>
                                <input type="datetime-local" name="data_pagamento" id="data_pagamento" 
                                       class="form-control <?php $__errorArgs = ['data_pagamento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e(old('data_pagamento', $contaReceber->data_pagamento ? $contaReceber->data_pagamento->format('Y-m-d\TH:i') : '')); ?>">
                                <?php $__errorArgs = ['data_pagamento'];
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
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Status Atual -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Status Atual
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <span class="badge badge-<?php echo e($contaReceber->situacao_financeira->value == 'pago' ? 'success' : 'warning'); ?> fs-6 mb-3">
                                <?php echo e($contaReceber->situacao_financeira->label()); ?>

                            </span>
                            
                            <h4 class="text-primary mb-2">
                                R$ <?php echo e(number_format($contaReceber->valor_liquido ?? 0, 2, ',', '.')); ?>

                            </h4>
                            
                            <p class="text-muted mb-0">
                                Vencimento: <?php echo e($contaReceber->data_vencimento->format('d/m/Y')); ?>

                            </p>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.show', ['empresa' => $empresa, 'id' => $contaReceber->id])); ?>" 
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos
    const valorOriginal = document.getElementById('valor_original');
    const valorDesconto = document.getElementById('valor_desconto');
    const valorAcrescimo = document.getElementById('valor_acrescimo');
    const valorFinal = document.getElementById('valor_final');

    // Função para calcular valores
    function calcularValores() {
        const original = parseFloat(valorOriginal.value) || 0;
        const desconto = parseFloat(valorDesconto.value) || 0;
        const acrescimo = parseFloat(valorAcrescimo.value) || 0;
        const total = original - desconto + acrescimo;

        valorFinal.value = total.toFixed(2);
    }

    // Eventos
    valorOriginal.addEventListener('input', calcularValores);
    valorDesconto.addEventListener('input', calcularValores);
    valorAcrescimo.addEventListener('input', calcularValores);

    // Calcular valores iniciais
    calcularValores();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.comerciante', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/financeiro/contas-receber/edit.blade.php ENDPATH**/ ?>