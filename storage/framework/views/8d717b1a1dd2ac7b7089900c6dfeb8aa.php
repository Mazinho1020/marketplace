<?php $__env->startSection('title', 'Nova Empresa'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header da página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus-circle me-2"></i>
                Nova Empresa
            </h1>
            <p class="text-muted mb-0">Cadastre uma nova unidade de negócio</p>
        </div>
        <a href="<?php echo e(route('comerciantes.empresas.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    <form method="POST" action="<?php echo e(route('comerciantes.empresas.store')); ?>" class="needs-validation" novalidate>
        <?php echo csrf_field(); ?>
        
        <div class="row">
            <!-- Coluna principal -->
            <div class="col-lg-8">
                <!-- Informações básicas -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle me-2"></i>
                            Informações Básicas
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nome_fantasia" class="form-label">Nome da Empresa *</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['nome_fantasia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="nome_fantasia" name="nome_fantasia" value="<?php echo e(old('nome_fantasia')); ?>" 
                                           placeholder="Ex: Pizzaria Tradição Concórdia" required>
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
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="marca_id" class="form-label">Marca</label>
                                    <select class="form-select <?php $__errorArgs = ['marca_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="marca_id" name="marca_id">
                                        <option value="">Selecione uma marca</option>
                                        <?php if(isset($marcas) && $marcas->count() > 0): ?>
                                            <?php $__currentLoopData = $marcas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $marca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($marca->id); ?>" 
                                                        <?php echo e(old('marca_id') == $marca->id ? 'selected' : ''); ?>>
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
                                    <div class="form-text">
                                        <a href="<?php echo e(route('comerciantes.marcas.create')); ?>" target="_blank">
                                            <i class="fas fa-plus me-1"></i>Criar nova marca
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['nome_fantasia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="nome_fantasia" name="nome_fantasia" value="<?php echo e(old('nome_fantasia')); ?>" 
                                           placeholder="Se diferente do nome principal">
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
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cnpj" class="form-label">CNPJ</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['cnpj'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="cnpj" name="cnpj" value="<?php echo e(old('cnpj')); ?>" 
                                           placeholder="00.000.000/0000-00" data-mask="00.000.000/0000-00">
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
                        </div>
                    </div>
                </div>

                <!-- Endereço -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Endereço
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="endereco_cep" class="form-label">CEP</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['endereco_cep'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="endereco_cep" name="endereco_cep" value="<?php echo e(old('endereco_cep')); ?>" 
                                           placeholder="00000-000" data-mask="00000-000">
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
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="endereco_logradouro" class="form-label">Logradouro</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['endereco_logradouro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="endereco_logradouro" name="endereco_logradouro" value="<?php echo e(old('endereco_logradouro')); ?>" 
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
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="endereco_numero" class="form-label">Número</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['endereco_numero'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="endereco_numero" name="endereco_numero" value="<?php echo e(old('endereco_numero')); ?>" 
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
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="endereco_complemento" class="form-label">Complemento</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['endereco_complemento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="endereco_complemento" name="endereco_complemento" value="<?php echo e(old('endereco_complemento')); ?>" 
                                           placeholder="Apto, Sala, etc.">
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
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="endereco_bairro" class="form-label">Bairro</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['endereco_bairro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="endereco_bairro" name="endereco_bairro" value="<?php echo e(old('endereco_bairro')); ?>" 
                                           placeholder="Nome do bairro">
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
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="endereco_cidade" class="form-label">Cidade</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['endereco_cidade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="endereco_cidade" name="endereco_cidade" value="<?php echo e(old('endereco_cidade')); ?>" 
                                           placeholder="Nome da cidade">
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
                            </div>
                            <div class="col-md-1">
                                <div class="mb-3">
                                    <label for="endereco_estado" class="form-label">UF</label>
                                    <select class="form-select <?php $__errorArgs = ['endereco_estado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="endereco_estado" name="endereco_estado">
                                        <option value="">UF</option>
                                        <option value="AC" <?php echo e(old('endereco_estado') == 'AC' ? 'selected' : ''); ?>>AC</option>
                                        <option value="AL" <?php echo e(old('endereco_estado') == 'AL' ? 'selected' : ''); ?>>AL</option>
                                        <option value="AP" <?php echo e(old('endereco_estado') == 'AP' ? 'selected' : ''); ?>>AP</option>
                                        <option value="AM" <?php echo e(old('endereco_estado') == 'AM' ? 'selected' : ''); ?>>AM</option>
                                        <option value="BA" <?php echo e(old('endereco_estado') == 'BA' ? 'selected' : ''); ?>>BA</option>
                                        <option value="CE" <?php echo e(old('endereco_estado') == 'CE' ? 'selected' : ''); ?>>CE</option>
                                        <option value="DF" <?php echo e(old('endereco_estado') == 'DF' ? 'selected' : ''); ?>>DF</option>
                                        <option value="ES" <?php echo e(old('endereco_estado') == 'ES' ? 'selected' : ''); ?>>ES</option>
                                        <option value="GO" <?php echo e(old('endereco_estado') == 'GO' ? 'selected' : ''); ?>>GO</option>
                                        <option value="MA" <?php echo e(old('endereco_estado') == 'MA' ? 'selected' : ''); ?>>MA</option>
                                        <option value="MT" <?php echo e(old('endereco_estado') == 'MT' ? 'selected' : ''); ?>>MT</option>
                                        <option value="MS" <?php echo e(old('endereco_estado') == 'MS' ? 'selected' : ''); ?>>MS</option>
                                        <option value="MG" <?php echo e(old('endereco_estado') == 'MG' ? 'selected' : ''); ?>>MG</option>
                                        <option value="PA" <?php echo e(old('endereco_estado') == 'PA' ? 'selected' : ''); ?>>PA</option>
                                        <option value="PB" <?php echo e(old('endereco_estado') == 'PB' ? 'selected' : ''); ?>>PB</option>
                                        <option value="PR" <?php echo e(old('endereco_estado') == 'PR' ? 'selected' : ''); ?>>PR</option>
                                        <option value="PE" <?php echo e(old('endereco_estado') == 'PE' ? 'selected' : ''); ?>>PE</option>
                                        <option value="PI" <?php echo e(old('endereco_estado') == 'PI' ? 'selected' : ''); ?>>PI</option>
                                        <option value="RJ" <?php echo e(old('endereco_estado') == 'RJ' ? 'selected' : ''); ?>>RJ</option>
                                        <option value="RN" <?php echo e(old('endereco_estado') == 'RN' ? 'selected' : ''); ?>>RN</option>
                                        <option value="RS" <?php echo e(old('endereco_estado') == 'RS' ? 'selected' : ''); ?>>RS</option>
                                        <option value="RO" <?php echo e(old('endereco_estado') == 'RO' ? 'selected' : ''); ?>>RO</option>
                                        <option value="RR" <?php echo e(old('endereco_estado') == 'RR' ? 'selected' : ''); ?>>RR</option>
                                        <option value="SC" <?php echo e(old('endereco_estado') == 'SC' ? 'selected' : ''); ?>>SC</option>
                                        <option value="SP" <?php echo e(old('endereco_estado') == 'SP' ? 'selected' : ''); ?>>SP</option>
                                        <option value="SE" <?php echo e(old('endereco_estado') == 'SE' ? 'selected' : ''); ?>>SE</option>
                                        <option value="TO" <?php echo e(old('endereco_estado') == 'TO' ? 'selected' : ''); ?>>TO</option>
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
                </div>

                <!-- Contato -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-phone me-2"></i>
                            Informações de Contato
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="telefone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['telefone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="telefone" name="telefone" value="<?php echo e(old('telefone')); ?>" 
                                           placeholder="(00) 0000-0000" data-mask="(00) 0000-0000">
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
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="email" name="email" value="<?php echo e(old('email')); ?>" 
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
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" class="form-control <?php $__errorArgs = ['website'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="website" name="website" value="<?php echo e(old('website')); ?>" 
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
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-toggle-on me-2"></i>
                            Status e Configurações
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status da Empresa</label>
                            <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="status" name="status">
                                <option value="ativa" <?php echo e(old('status', 'ativa') == 'ativa' ? 'selected' : ''); ?>>
                                    Ativa
                                </option>
                                <option value="inativa" <?php echo e(old('status') == 'inativa' ? 'selected' : ''); ?>>
                                    Inativa
                                </option>
                                <option value="suspensa" <?php echo e(old('status') == 'suspensa' ? 'selected' : ''); ?>>
                                    Suspensa
                                </option>
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
                    </div>
                </div>

                <!-- Horário de funcionamento -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-clock me-2"></i>
                            Horário de Funcionamento
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-text mb-3">
                            Configure os horários de funcionamento da empresa. Deixe em branco os dias que não funciona.
                        </div>
                        
                        <?php
                            $diasSemana = [
                                'segunda' => 'Segunda-feira',
                                'terca' => 'Terça-feira',
                                'quarta' => 'Quarta-feira',
                                'quinta' => 'Quinta-feira',
                                'sexta' => 'Sexta-feira',
                                'sabado' => 'Sábado',
                                'domingo' => 'Domingo'
                            ];
                        ?>

                        <?php $__currentLoopData = $diasSemana; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dia => $nome): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mb-2">
                                <label class="form-label small"><?php echo e($nome); ?></label>
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="time" class="form-control form-control-sm" 
                                               name="horario[<?php echo e($dia); ?>][abertura]" 
                                               value="<?php echo e(old("horario.{$dia}.abertura")); ?>" 
                                               placeholder="Abertura">
                                    </div>
                                    <div class="col">
                                        <input type="time" class="form-control form-control-sm" 
                                               name="horario[<?php echo e($dia); ?>][fechamento]" 
                                               value="<?php echo e(old("horario.{$dia}.fechamento")); ?>" 
                                               placeholder="Fechamento">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <!-- Ações -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Salvar Empresa
                            </button>
                            <a href="<?php echo e(route('comerciantes.empresas.index')); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.form-control:focus, .form-select:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

.card-header {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
    border-bottom: 1px solid rgba(var(--bs-primary-rgb), 0.2);
}

.form-text a {
    color: var(--bs-primary);
    text-decoration: none;
}

.form-text a:hover {
    text-decoration: underline;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Máscaras de input
    $('#cnpj').mask('00.000.000/0000-00');
    $('#endereco_cep').mask('00000-000');
    $('#telefone').mask('(00) 0000-0000');

    // Busca CEP automática
    $('#endereco_cep').on('blur', function() {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length === 8) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('endereco_logradouro').value = data.logradouro || '';
                        document.getElementById('endereco_bairro').value = data.bairro || '';
                        document.getElementById('endereco_cidade').value = data.localidade || '';
                        document.getElementById('endereco_estado').value = data.uf || '';
                        
                        // Foca no campo número
                        document.getElementById('endereco_numero').focus();
                    }
                })
                .catch(error => console.log('Erro ao buscar CEP:', error));
        }
    });

    // Geração automática de slug
    $('#nome').on('input', function() {
        // Implementar se necessário
    });

    // Validação do formulário
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('comerciantes.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/empresas/create.blade.php ENDPATH**/ ?>