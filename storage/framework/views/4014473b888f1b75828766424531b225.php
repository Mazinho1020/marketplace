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
                            <label for="nome_fantasia" class="form-label">Nome da Empresa <span class="text-danger">*</span></label>
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
                                   value="<?php echo e(old('nome_fantasia', $empresa->nome_fantasia)); ?>" 
                                   required>
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
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">URL Amigável <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><?php echo e(url('/')); ?>/loja/</span>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="slug" 
                                   name="slug" 
                                   value="<?php echo e(old('slug', $empresa->slug)); ?>" 
                                   required>
                        </div>
                        <div class="form-text">URL única para sua loja online. Use apenas letras, números e hífens.</div>
                        <?php $__errorArgs = ['slug'];
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
                            <label for="endereco_cep" class="form-label">CEP</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['endereco_cep'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="endereco_cep" 
                                   name="endereco_cep" 
                                   value="<?php echo e(old('endereco_cep', $empresa->endereco_cep)); ?>"
                                   placeholder="00000-000">
                            <?php $__errorArgs = ['endereco_cep'];
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
                            <label for="endereco_logradouro" class="form-label">Logradouro</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['endereco_logradouro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="endereco_logradouro" 
                                   name="endereco_logradouro" 
                                   value="<?php echo e(old('endereco_logradouro', $empresa->endereco_logradouro)); ?>"
                                   placeholder="Rua, Avenida, etc.">
                            <?php $__errorArgs = ['endereco_logradouro'];
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
                            <label for="endereco_numero" class="form-label">Número</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['endereco_numero'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="endereco_numero" 
                                   name="endereco_numero" 
                                   value="<?php echo e(old('endereco_numero', $empresa->endereco_numero)); ?>"
                                   placeholder="123">
                            <?php $__errorArgs = ['endereco_numero'];
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
                            <label for="endereco_complemento" class="form-label">Complemento</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['endereco_complemento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="endereco_complemento" 
                                   name="endereco_complemento" 
                                   value="<?php echo e(old('endereco_complemento', $empresa->endereco_complemento)); ?>"
                                   placeholder="Apartamento, Sala, etc.">
                            <?php $__errorArgs = ['endereco_complemento'];
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
                            <label for="endereco_bairro" class="form-label">Bairro</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['endereco_bairro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="endereco_bairro" 
                                   name="endereco_bairro" 
                                   value="<?php echo e(old('endereco_bairro', $empresa->endereco_bairro)); ?>">
                            <?php $__errorArgs = ['endereco_bairro'];
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
                            <label for="endereco_cidade" class="form-label">Cidade</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['endereco_cidade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="endereco_cidade" 
                                   name="endereco_cidade" 
                                   value="<?php echo e(old('endereco_cidade', $empresa->endereco_cidade)); ?>">
                            <?php $__errorArgs = ['endereco_cidade'];
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
                            <label for="endereco_estado" class="form-label">Estado</label>
                            <select class="form-select <?php $__errorArgs = ['endereco_estado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="endereco_estado" name="endereco_estado">
                                <option value="">Selecione o estado</option>
                                <option value="AC" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'AC' ? 'selected' : ''); ?>>Acre</option>
                                <option value="AL" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'AL' ? 'selected' : ''); ?>>Alagoas</option>
                                <option value="AP" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'AP' ? 'selected' : ''); ?>>Amapá</option>
                                <option value="AM" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'AM' ? 'selected' : ''); ?>>Amazonas</option>
                                <option value="BA" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'BA' ? 'selected' : ''); ?>>Bahia</option>
                                <option value="CE" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'CE' ? 'selected' : ''); ?>>Ceará</option>
                                <option value="DF" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'DF' ? 'selected' : ''); ?>>Distrito Federal</option>
                                <option value="ES" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'ES' ? 'selected' : ''); ?>>Espírito Santo</option>
                                <option value="GO" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'GO' ? 'selected' : ''); ?>>Goiás</option>
                                <option value="MA" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'MA' ? 'selected' : ''); ?>>Maranhão</option>
                                <option value="MT" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'MT' ? 'selected' : ''); ?>>Mato Grosso</option>
                                <option value="MS" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'MS' ? 'selected' : ''); ?>>Mato Grosso do Sul</option>
                                <option value="MG" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'MG' ? 'selected' : ''); ?>>Minas Gerais</option>
                                <option value="PA" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'PA' ? 'selected' : ''); ?>>Pará</option>
                                <option value="PB" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'PB' ? 'selected' : ''); ?>>Paraíba</option>
                                <option value="PR" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'PR' ? 'selected' : ''); ?>>Paraná</option>
                                <option value="PE" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'PE' ? 'selected' : ''); ?>>Pernambuco</option>
                                <option value="PI" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'PI' ? 'selected' : ''); ?>>Piauí</option>
                                <option value="RJ" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'RJ' ? 'selected' : ''); ?>>Rio de Janeiro</option>
                                <option value="RN" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'RN' ? 'selected' : ''); ?>>Rio Grande do Norte</option>
                                <option value="RS" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'RS' ? 'selected' : ''); ?>>Rio Grande do Sul</option>
                                <option value="RO" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'RO' ? 'selected' : ''); ?>>Rondônia</option>
                                <option value="RR" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'RR' ? 'selected' : ''); ?>>Roraima</option>
                                <option value="SC" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'SC' ? 'selected' : ''); ?>>Santa Catarina</option>
                                <option value="SP" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'SP' ? 'selected' : ''); ?>>São Paulo</option>
                                <option value="SE" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'SE' ? 'selected' : ''); ?>>Sergipe</option>
                                <option value="TO" <?php echo e(old('endereco_estado', $empresa->endereco_estado) == 'TO' ? 'selected' : ''); ?>>Tocantins</option>
                            </select>
                            <?php $__errorArgs = ['endereco_estado'];
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
                            <label for="website" class="form-label">Website</label>
                            <input type="url" 
                                   class="form-control <?php $__errorArgs = ['website'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="website" 
                                   name="website" 
                                   value="<?php echo e(old('website', $empresa->website)); ?>"
                                   placeholder="https://www.empresa.com">
                            <?php $__errorArgs = ['website'];
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
                            <option value="ativa" <?php echo e(old('status', $empresa->status) == 'ativa' ? 'selected' : ''); ?>>Ativa</option>
                            <option value="inativa" <?php echo e(old('status', $empresa->status) == 'inativa' ? 'selected' : ''); ?>>Inativa</option>
                            <option value="suspensa" <?php echo e(old('status', $empresa->status) == 'suspensa' ? 'selected' : ''); ?>>Suspensa</option>
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

// Auto-gerar slug a partir do nome
document.getElementById('nome').addEventListener('input', function() {
    const nome = this.value;
    const slug = nome
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
    
    document.getElementById('slug').value = slug;
});

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
document.getElementById('endereco_cep').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 8) {
        value = value.replace(/^(\d{5})(\d)/, '$1-$2');
        this.value = value;
    }
});

// Buscar endereço pelo CEP
document.getElementById('endereco_cep').addEventListener('blur', function() {
    const cep = this.value.replace(/\D/g, '');
    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('endereco_logradouro').value = data.logradouro;
                    document.getElementById('endereco_bairro').value = data.bairro;
                    document.getElementById('endereco_cidade').value = data.localidade;
                    document.getElementById('endereco_estado').value = data.uf;
                    document.getElementById('endereco_numero').focus();
                }
            })
            .catch(error => console.log('Erro ao buscar CEP:', error));
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/empresas/edit.blade.php ENDPATH**/ ?>