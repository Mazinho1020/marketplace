<?php $__env->startSection('title', 'Editar Conta a Pagar'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('comerciantes.dashboard.empresa', $empresa)); ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('comerciantes.empresas.financeiro.dashboard', $empresa)); ?>">Financeiro</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa)); ?>">Contas a Pagar</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-pagar.show', ['empresa' => $empresa, 'id' => $contaPagar->id])); ?>">
                    <?php echo e($contaPagar->descricao); ?>

                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Editar Conta a Pagar</h1>
        <div class="btn-group">
            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-pagar.show', ['empresa' => $empresa, 'id' => $contaPagar->id])); ?>" 
               class="btn btn-info">
                <i class="fas fa-eye"></i> Visualizar
            </a>
            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa)); ?>" 
               class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Formulário -->
    <form method="POST" action="<?php echo e(route('comerciantes.empresas.financeiro.contas-pagar.update', ['empresa' => $empresa, 'id' => $contaPagar->id])); ?>">
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
                                           value="<?php echo e(old('descricao', $contaPagar->descricao)); ?>" required>
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
                                                    <?php echo e(old('conta_gerencial_id', $contaPagar->conta_gerencial_id) == $conta->id ? 'selected' : ''); ?>>
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
                                    <label for="pessoa_id" class="form-label">Fornecedor/Pessoa</label>
                                    <select name="pessoa_id" id="pessoa_id" 
                                            class="form-control <?php $__errorArgs = ['pessoa_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Selecione uma pessoa</option>
                                        <?php $__currentLoopData = $pessoas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pessoa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($pessoa->id); ?>" 
                                                    <?php echo e(old('pessoa_id', $contaPagar->pessoa_id) == $pessoa->id ? 'selected' : ''); ?>>
                                                <?php echo e($pessoa->nome); ?> (<?php echo e($pessoa->tipo_pessoa); ?>)
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
                                           value="<?php echo e(old('numero_documento', $contaPagar->numero_documento)); ?>" 
                                           placeholder="Ex: NF-001, Boleto 123">
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
                                      rows="3"><?php echo e(old('observacoes', $contaPagar->observacoes)); ?></textarea>
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

                <!-- Valores e Datas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-dollar-sign"></i> Valores e Datas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="valor_original" class="form-label">Valor Original *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" name="valor_original" id="valor_original" 
                                               class="form-control <?php $__errorArgs = ['valor_original'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               step="0.01" value="<?php echo e(old('valor_original', $contaPagar->valor_original)); ?>" required>
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
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="data_vencimento" class="form-label">Data Vencimento *</label>
                                    <input type="date" name="data_vencimento" id="data_vencimento" 
                                           class="form-control <?php $__errorArgs = ['data_vencimento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('data_vencimento', $contaPagar->data_vencimento?->format('Y-m-d'))); ?>" required>
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="data_emissao" class="form-label">Data Emissão</label>
                                    <input type="date" name="data_emissao" id="data_emissao" 
                                           class="form-control <?php $__errorArgs = ['data_emissao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('data_emissao', $contaPagar->data_emissao?->format('Y-m-d'))); ?>">
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
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="desconto" class="form-label">Desconto</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" name="desconto" id="desconto" 
                                               class="form-control <?php $__errorArgs = ['desconto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               step="0.01" value="<?php echo e(old('desconto', $contaPagar->desconto)); ?>">
                                        <?php $__errorArgs = ['desconto'];
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="juros" class="form-label">Juros</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" name="juros" id="juros" 
                                               class="form-control <?php $__errorArgs = ['juros'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               step="0.01" value="<?php echo e(old('juros', $contaPagar->juros)); ?>">
                                        <?php $__errorArgs = ['juros'];
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="multa" class="form-label">Multa</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" name="multa" id="multa" 
                                               class="form-control <?php $__errorArgs = ['multa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               step="0.01" value="<?php echo e(old('multa', $contaPagar->multa)); ?>">
                                        <?php $__errorArgs = ['multa'];
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
                </div>

                <!-- Parcelamento -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt"></i> Parcelamento
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="numero_parcelas" class="form-label">Número de Parcelas</label>
                                    <input type="number" name="numero_parcelas" id="numero_parcelas" 
                                           class="form-control <?php $__errorArgs = ['numero_parcelas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           min="1" value="<?php echo e(old('numero_parcelas', $contaPagar->numero_parcelas)); ?>">
                                    <?php $__errorArgs = ['numero_parcelas'];
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
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="parcela_atual" class="form-label">Parcela Atual</label>
                                    <input type="number" name="parcela_atual" id="parcela_atual" 
                                           class="form-control <?php $__errorArgs = ['parcela_atual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           min="1" value="<?php echo e(old('parcela_atual', $contaPagar->parcela_atual)); ?>">
                                    <?php $__errorArgs = ['parcela_atual'];
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
                                    <label for="documento_numero" class="form-label">Número do Documento</label>
                                    <input type="text" name="documento_numero" id="documento_numero" 
                                           class="form-control <?php $__errorArgs = ['documento_numero'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('documento_numero', $contaPagar->documento_numero)); ?>">
                                    <?php $__errorArgs = ['documento_numero'];
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
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Resumo -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calculator"></i> Resumo
                        </h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-6">Valor Original:</dt>
                            <dd class="col-6" id="resumo-original">R$ <?php echo e(number_format($contaPagar->valor_original, 2, ',', '.')); ?></dd>
                            
                            <dt class="col-6">Desconto:</dt>
                            <dd class="col-6 text-success" id="resumo-desconto">- R$ <?php echo e(number_format($contaPagar->desconto, 2, ',', '.')); ?></dd>
                            
                            <dt class="col-6">Juros:</dt>
                            <dd class="col-6 text-warning" id="resumo-juros">+ R$ <?php echo e(number_format($contaPagar->juros, 2, ',', '.')); ?></dd>
                            
                            <dt class="col-6">Multa:</dt>
                            <dd class="col-6 text-danger" id="resumo-multa">+ R$ <?php echo e(number_format($contaPagar->multa, 2, ',', '.')); ?></dd>
                            
                            <hr>
                            
                            <dt class="col-6"><strong>Total:</strong></dt>
                            <dd class="col-6"><strong id="resumo-total">R$ <?php echo e(number_format($contaPagar->valor_total, 2, ',', '.')); ?></strong></dd>
                        </dl>
                    </div>
                </div>

                <!-- Configurações -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog"></i> Configurações
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="situacao" class="form-label">Situação</label>
                            <select name="situacao_financeira" id="situacao_financeira" 
                                    class="form-control <?php $__errorArgs = ['situacao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="pendente" <?php echo e(old('situacao_financeira', $contaPagar->situacao_financeira->value) == 'pendente' ? 'selected' : ''); ?>>
                                    Pendente
                                </option>
                                <option value="pago" <?php echo e(old('situacao_financeira', $contaPagar->situacao_financeira->value) == 'pago' ? 'selected' : ''); ?>>
                                    Pago
                                </option>
                                <option value="cancelado" <?php echo e(old('situacao_financeira', $contaPagar->situacao_financeira->value) == 'cancelado' ? 'selected' : ''); ?>>
                                    Cancelado
                                </option>
                            </select>
                            <?php $__errorArgs = ['situacao'];
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
                            <label for="natureza" class="form-label">Natureza</label>
                            <select name="natureza_financeira" id="natureza_financeira" 
                                    class="form-control <?php $__errorArgs = ['natureza_financeira'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="pagar" <?php echo e(old('natureza_financeira', $contaPagar->natureza_financeira->value) == 'pagar' ? 'selected' : ''); ?>>
                                    Conta a Pagar
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

                        <div class="form-check">
                            <input type="checkbox" name="e_recorrente" id="e_recorrente" 
                                   class="form-check-input <?php $__errorArgs = ['e_recorrente'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   value="1" <?php echo e(old('e_recorrente', $contaPagar->e_recorrente) ? 'checked' : ''); ?>>
                            <label for="e_recorrente" class="form-check-label">
                                É Recorrente
                            </label>
                            <?php $__errorArgs = ['e_recorrente'];
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

                <!-- Status Atual -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info"></i> Status Atual
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Situação:</strong> 
                            <span class="badge badge-<?php echo e($contaPagar->situacao_financeira->value == 'pago' ? 'success' : 'warning'); ?>">
                                <?php echo e($contaPagar->situacao_financeira->label()); ?>

                            </span>
                        </p>
                        
                        <?php if($contaPagar->valor_pago > 0): ?>
                        <p><strong>Valor Pago:</strong> R$ <?php echo e(number_format($contaPagar->valor_pago, 2, ',', '.')); ?></p>
                        <p><strong>Saldo:</strong> R$ <?php echo e(number_format($contaPagar->valor_total - $contaPagar->valor_pago, 2, ',', '.')); ?></p>
                        <?php endif; ?>

                        <small class="text-muted">
                            Criado em: <?php echo e($contaPagar->created_at->format('d/m/Y H:i')); ?>

                        </small>
                    </div>
                </div>

                <!-- Botões -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-pagar.show', ['empresa' => $empresa, 'id' => $contaPagar->id])); ?>" 
                               class="btn btn-info">
                                <i class="fas fa-eye"></i> Visualizar
                            </a>
                            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-pagar.index', $empresa)); ?>" 
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
function formatarMoeda(valor) {
    return 'R$ ' + parseFloat(valor).toLocaleString('pt-BR', { 
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2 
    });
}

function atualizarResumo() {
    const original = parseFloat(document.getElementById('valor_original').value) || 0;
    const desconto = parseFloat(document.getElementById('desconto').value) || 0;
    const juros = parseFloat(document.getElementById('juros').value) || 0;
    const multa = parseFloat(document.getElementById('multa').value) || 0;
    
    const total = original - desconto + juros + multa;
    
    document.getElementById('resumo-original').textContent = formatarMoeda(original);
    document.getElementById('resumo-desconto').textContent = '- ' + formatarMoeda(desconto);
    document.getElementById('resumo-juros').textContent = '+ ' + formatarMoeda(juros);
    document.getElementById('resumo-multa').textContent = '+ ' + formatarMoeda(multa);
    document.getElementById('resumo-total').textContent = formatarMoeda(total);
}

// Eventos para atualizar o resumo
document.getElementById('valor_original').addEventListener('input', atualizarResumo);
document.getElementById('desconto').addEventListener('input', atualizarResumo);
document.getElementById('juros').addEventListener('input', atualizarResumo);
document.getElementById('multa').addEventListener('input', atualizarResumo);

// Controlar parcela atual baseado no número de parcelas
document.getElementById('numero_parcelas').addEventListener('input', function() {
    const numeroParcelas = parseInt(this.value) || 1;
    const parcelaAtual = document.getElementById('parcela_atual');
    
    parcelaAtual.max = numeroParcelas;
    
    if (parseInt(parcelaAtual.value) > numeroParcelas) {
        parcelaAtual.value = numeroParcelas;
    }
});
</script>
<?php $__env->stopPush(); ?>









<?php echo $__env->make('layouts.comerciante', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/financeiro/contas-pagar/edit.blade.php ENDPATH**/ ?>