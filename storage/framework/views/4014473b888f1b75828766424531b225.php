<?php $__env->startSection('title', 'Editar Empresa - ' . ($empresa->nome_fantasia ?: $empresa->razao_social)); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Editar Empresa</h1>
        <p class="text-muted mb-0"><?php echo e($empresa->nome_fantasia ?: $empresa->razao_social); ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('comerciantes.empresas.show', $empresa)); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Voltar
        </a>
        <a href="<?php echo e(route('comerciantes.empresas.index')); ?>" class="btn btn-outline-primary">
            <i class="fas fa-list me-1"></i>
            Todas as Empresas
        </a>
    </div>
</div>

<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <h6><i class="fas fa-exclamation-triangle me-2"></i>Existem erros no formulário:</h6>
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('comerciantes.empresas.update', $empresa)); ?>" class="needs-validation" novalidate>
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    
    <div class="row">
        <!-- Informações Básicas -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Informações Básicas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="razao_social" class="form-label">Razão Social <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['razao_social'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="razao_social" 
                                   name="razao_social" 
                                   value="<?php echo e(old('razao_social', $empresa->razao_social)); ?>" 
                                   required>
                            <?php $__errorArgs = ['razao_social'];
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
                        <div class="col-md-4 mb-3">
                            <label for="marca_id" class="form-label">Marca</label>
                            <select class="form-select <?php $__errorArgs = ['marca_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="marca_id" name="marca_id">
                                <option value="">Selecione uma marca</option>
                                <?php if(isset($marcas) && $marcas->count() > 0): ?>
                                    <?php $__currentLoopData = $marcas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $marca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($marca->id); ?>" <?php echo e(old('marca_id', $empresa->marca_id) == $marca->id ? 'selected' : ''); ?>>
                                            <?php echo e($marca->nome); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <option value="" disabled>Nenhuma marca disponível</option>
                                <?php endif; ?>
                            </select>
                            <?php $__errorArgs = ['marca_id'];
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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['nome_fantasia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="nome_fantasia" 
                                   name="nome_fantasia" 
                                   value="<?php echo e(old('nome_fantasia', $empresa->nome_fantasia)); ?>">
                            <?php $__errorArgs = ['nome_fantasia'];
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
                        <div class="col-md-6 mb-3">
                            <label for="cnpj" class="form-label">CNPJ</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['cnpj'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="cnpj" 
                                   name="cnpj" 
                                   value="<?php echo e(old('cnpj', $empresa->cnpj)); ?>"
                                   placeholder="00.000.000/0001-00">
                            <?php $__errorArgs = ['cnpj'];
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
                        
                        <div class="col-md-6 mb-3">
                            <label for="inscricao_estadual" class="form-label">Inscrição Estadual</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['inscricao_estadual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="inscricao_estadual" 
                                   name="inscricao_estadual" 
                                   value="<?php echo e(old('inscricao_estadual', $empresa->inscricao_estadual)); ?>"
                                   placeholder="000.000.000.000">
                            <?php $__errorArgs = ['inscricao_estadual'];
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

            <!-- Endereço -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        Endereço
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['cep'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="cep" 
                                   name="cep" 
                                   value="<?php echo e(old('cep', $empresa->cep)); ?>"
                                   placeholder="00000-000">
                            <?php $__errorArgs = ['cep'];
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
                        <div class="col-md-6 mb-3">
                            <label for="logradouro" class="form-label">Logradouro</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['logradouro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="logradouro" 
                                   name="logradouro" 
                                   value="<?php echo e(old('logradouro', $empresa->logradouro)); ?>"
                                   placeholder="Rua, Avenida, etc.">
                            <?php $__errorArgs = ['logradouro'];
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
                        <div class="col-md-3 mb-3">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['numero'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="numero" 
                                   name="numero" 
                                   value="<?php echo e(old('numero', $empresa->numero)); ?>"
                                   placeholder="123">
                            <?php $__errorArgs = ['numero'];
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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['complemento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="complemento" 
                                   name="complemento" 
                                   value="<?php echo e(old('complemento', $empresa->complemento)); ?>"
                                   placeholder="Apartamento, Sala, etc.">
                            <?php $__errorArgs = ['complemento'];
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
                        <div class="col-md-6 mb-3">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['bairro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="bairro" 
                                   name="bairro" 
                                   value="<?php echo e(old('bairro', $empresa->bairro)); ?>">
                            <?php $__errorArgs = ['bairro'];
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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['cidade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="cidade" 
                                   name="cidade" 
                                   value="<?php echo e(old('cidade', $empresa->cidade)); ?>">
                            <?php $__errorArgs = ['cidade'];
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
                        <div class="col-md-6 mb-3">
                            <label for="uf" class="form-label">Estado</label>
                            <select class="form-select <?php $__errorArgs = ['uf'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="uf" name="uf">
                                <option value="">Selecione o estado</option>
                                <option value="AC" <?php echo e(old('uf', $empresa->uf) == 'AC' ? 'selected' : ''); ?>>Acre</option>
                                <option value="AL" <?php echo e(old('uf', $empresa->uf) == 'AL' ? 'selected' : ''); ?>>Alagoas</option>
                                <option value="AP" <?php echo e(old('uf', $empresa->uf) == 'AP' ? 'selected' : ''); ?>>Amapá</option>
                                <option value="AM" <?php echo e(old('uf', $empresa->uf) == 'AM' ? 'selected' : ''); ?>>Amazonas</option>
                                <option value="BA" <?php echo e(old('uf', $empresa->uf) == 'BA' ? 'selected' : ''); ?>>Bahia</option>
                                <option value="CE" <?php echo e(old('uf', $empresa->uf) == 'CE' ? 'selected' : ''); ?>>Ceará</option>
                                <option value="DF" <?php echo e(old('uf', $empresa->uf) == 'DF' ? 'selected' : ''); ?>>Distrito Federal</option>
                                <option value="ES" <?php echo e(old('uf', $empresa->uf) == 'ES' ? 'selected' : ''); ?>>Espírito Santo</option>
                                <option value="GO" <?php echo e(old('uf', $empresa->uf) == 'GO' ? 'selected' : ''); ?>>Goiás</option>
                                <option value="MA" <?php echo e(old('uf', $empresa->uf) == 'MA' ? 'selected' : ''); ?>>Maranhão</option>
                                <option value="MT" <?php echo e(old('uf', $empresa->uf) == 'MT' ? 'selected' : ''); ?>>Mato Grosso</option>
                                <option value="MS" <?php echo e(old('uf', $empresa->uf) == 'MS' ? 'selected' : ''); ?>>Mato Grosso do Sul</option>
                                <option value="MG" <?php echo e(old('uf', $empresa->uf) == 'MG' ? 'selected' : ''); ?>>Minas Gerais</option>
                                <option value="PA" <?php echo e(old('uf', $empresa->uf) == 'PA' ? 'selected' : ''); ?>>Pará</option>
                                <option value="PB" <?php echo e(old('uf', $empresa->uf) == 'PB' ? 'selected' : ''); ?>>Paraíba</option>
                                <option value="PR" <?php echo e(old('uf', $empresa->uf) == 'PR' ? 'selected' : ''); ?>>Paraná</option>
                                <option value="PE" <?php echo e(old('uf', $empresa->uf) == 'PE' ? 'selected' : ''); ?>>Pernambuco</option>
                                <option value="PI" <?php echo e(old('uf', $empresa->uf) == 'PI' ? 'selected' : ''); ?>>Piauí</option>
                                <option value="RJ" <?php echo e(old('uf', $empresa->uf) == 'RJ' ? 'selected' : ''); ?>>Rio de Janeiro</option>
                                <option value="RN" <?php echo e(old('uf', $empresa->uf) == 'RN' ? 'selected' : ''); ?>>Rio Grande do Norte</option>
                                <option value="RS" <?php echo e(old('uf', $empresa->uf) == 'RS' ? 'selected' : ''); ?>>Rio Grande do Sul</option>
                                <option value="RO" <?php echo e(old('uf', $empresa->uf) == 'RO' ? 'selected' : ''); ?>>Rondônia</option>
                                <option value="RR" <?php echo e(old('uf', $empresa->uf) == 'RR' ? 'selected' : ''); ?>>Roraima</option>
                                <option value="SC" <?php echo e(old('uf', $empresa->uf) == 'SC' ? 'selected' : ''); ?>>Santa Catarina</option>
                                <option value="SP" <?php echo e(old('uf', $empresa->uf) == 'SP' ? 'selected' : ''); ?>>São Paulo</option>
                                <option value="SE" <?php echo e(old('uf', $empresa->uf) == 'SE' ? 'selected' : ''); ?>>Sergipe</option>
                                <option value="TO" <?php echo e(old('uf', $empresa->uf) == 'TO' ? 'selected' : ''); ?>>Tocantins</option>
                            </select>
                            <?php $__errorArgs = ['uf'];
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

            <!-- Contato -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-phone text-primary me-2"></i>
                        Contato
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['telefone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="telefone" 
                                   name="telefone" 
                                   value="<?php echo e(old('telefone', $empresa->telefone)); ?>"
                                   placeholder="(00) 0000-0000">
                            <?php $__errorArgs = ['telefone'];
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
                        
                        <div class="col-md-4 mb-3">
                            <label for="celular" class="form-label">Celular</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['celular'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="celular" 
                                   name="celular" 
                                   value="<?php echo e(old('celular', $empresa->celular)); ?>"
                                   placeholder="(00) 90000-0000">
                            <?php $__errorArgs = ['celular'];
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
                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" 
                                   class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo e(old('email', $empresa->email)); ?>"
                                   placeholder="contato@empresa.com">
                            <?php $__errorArgs = ['email'];
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
                        <div class="col-md-4 mb-3">
                            <label for="site" class="form-label">Website</label>
                            <input type="url" 
                                   class="form-control <?php $__errorArgs = ['site'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="site" 
                                   name="site" 
                                   value="<?php echo e(old('site', $empresa->site)); ?>"
                                   placeholder="https://www.empresa.com">
                            <?php $__errorArgs = ['site'];
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

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status e Configurações -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cog text-primary me-2"></i>
                        Status e Configurações
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="status" name="status">
                            <option value="ativo" <?php echo e(old('status', $empresa->status) == 'ativo' ? 'selected' : ''); ?>>Ativo</option>
                            <option value="inativo" <?php echo e(old('status', $empresa->status) == 'inativo' ? 'selected' : ''); ?>>Inativo</option>
                            <option value="suspenso" <?php echo e(old('status', $empresa->status) == 'suspenso' ? 'selected' : ''); ?>>Suspenso</option>
                            <option value="bloqueado" <?php echo e(old('status', $empresa->status) == 'bloqueado' ? 'selected' : ''); ?>>Bloqueado</option>
                        </select>
                        <?php $__errorArgs = ['status'];
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

                    <div class="text-muted small">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Criada em:</span>
                            <span><?php echo e($empresa->created_at->format('d/m/Y')); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Atualizada em:</span>
                            <span><?php echo e($empresa->updated_at->format('d/m/Y')); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Horário de Funcionamento -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-clock text-primary me-2"></i>
                        Horário de Funcionamento
                    </h6>
                    <a href="<?php echo e(route('comerciantes.horarios.index', $empresa->id)); ?>" 
                       class="btn btn-sm btn-outline-primary"
                       title="Gerenciar horários completos">
                        <i class="fas fa-cog"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info border-0" style="background-color: #e7f3ff;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            <div>
                                <small class="text-primary fw-medium">Sistema Avançado de Horários</small>
                                <div class="small text-muted mt-1">
                                    Configure horários por sistema (PDV, Online, Financeiro) e exceções especiais através do módulo especializado.
                                </div>
                                <a href="<?php echo e(route('comerciantes.horarios.index', $empresa->id)); ?>" 
                                   class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-external-link-alt me-1"></i>
                                    Gerenciar Horários Completos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save me-1"></i>
                        Salvar Alterações
                    </button>
                    <a href="<?php echo e(route('comerciantes.empresas.show', $empresa)); ?>" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.form-label {
    font-weight: 500;
    color: #374151;
}

.card-header {
    background-color: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}

.card-title {
    font-size: 0.95rem;
    color: #374151;
}

.text-danger {
    color: #dc2626 !important;
}

.input-group-text {
    background-color: #f3f4f6;
    border-color: #d1d5db;
    color: #6b7280;
    font-size: 0.875rem;
}

.form-control:focus,
.form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Validação do formulário
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Máscara para CNPJ
document.getElementById('cnpj').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 14) {
        value = value.replace(/^(\d{2})(\d)/, '$1.$2');
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
        this.value = value;
    }
});

// Máscara para telefone
document.getElementById('telefone').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 11) {
        if (value.length <= 10) {
            value = value.replace(/^(\d{2})(\d{4})(\d)/, '($1) $2-$3');
        } else {
            value = value.replace(/^(\d{2})(\d{5})(\d)/, '($1) $2-$3');
        }
        this.value = value;
    }
});

// Máscara para celular
document.getElementById('celular').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 11) {
        if (value.length <= 10) {
            value = value.replace(/^(\d{2})(\d{4})(\d)/, '($1) $2-$3');
        } else {
            value = value.replace(/^(\d{2})(\d{5})(\d)/, '($1) $2-$3');
        }
        this.value = value;
    }
});
document.getElementById('telefone').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 11) {
        if (value.length <= 10) {
            value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        } else {
            value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
        }
        this.value = value;
    }
});

// Máscara para CEP
document.getElementById('cep').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 8) {
        value = value.replace(/^(\d{5})(\d)/, '$1-$2');
        this.value = value;
    }
});

// Buscar endereço pelo CEP
document.getElementById('cep').addEventListener('blur', function() {
    const cep = this.value.replace(/\D/g, '');
    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('logradouro').value = data.logradouro;
                    document.getElementById('bairro').value = data.bairro;
                    document.getElementById('cidade').value = data.localidade;
                    document.getElementById('uf').value = data.uf;
                    document.getElementById('numero').focus();
                }
            })
            .catch(error => console.log('Erro ao buscar CEP:', error));
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/empresas/edit.blade.php ENDPATH**/ ?>