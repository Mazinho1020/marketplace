<?php $__env->startSection('title', 'Editar Pessoa'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Editar Pessoa</h1>
                    <p class="text-muted mb-0"><?php echo e($pessoa->nome); ?> <?php echo e($pessoa->sobrenome); ?></p>
                </div>
                <div>
                    <a href="<?php echo e(route('comerciantes.clientes.pessoas.show', $pessoa->id)); ?>" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <!-- Formulário -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?php echo e(route('comerciantes.clientes.pessoas.update', $pessoa->id)); ?>" method="POST" id="formPessoa">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <input type="hidden" name="empresa_id" value="<?php echo e($pessoa->empresa_id); ?>">

                                <div class="row">
                                    <!-- Informações Básicas -->
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-user"></i> Informações Básicas
                                        </h6>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="nome" name="nome" value="<?php echo e(old('nome', $pessoa->nome)); ?>" 
                                                   placeholder="Nome da pessoa" required>
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

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sobrenome" class="form-label">Sobrenome</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['sobrenome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="sobrenome" name="sobrenome" value="<?php echo e(old('sobrenome', $pessoa->sobrenome)); ?>" 
                                                   placeholder="Sobrenome">
                                            <?php $__errorArgs = ['sobrenome'];
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
                                            <label for="nome_social" class="form-label">Nome Social</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['nome_social'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="nome_social" name="nome_social" value="<?php echo e(old('nome_social', $pessoa->nome_social)); ?>" 
                                                   placeholder="Nome Social">
                                            <?php $__errorArgs = ['nome_social'];
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
                                            <label for="cpf_cnpj" class="form-label">CPF/CNPJ</label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['cpf_cnpj'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="cpf_cnpj" name="cpf_cnpj" value="<?php echo e(old('cpf_cnpj', $pessoa->cpf_cnpj)); ?>" 
                                                   placeholder="000.000.000-00">
                                            <?php $__errorArgs = ['cpf_cnpj'];
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
                                            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                            <input type="date" class="form-control <?php $__errorArgs = ['data_nascimento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="data_nascimento" name="data_nascimento" value="<?php echo e(old('data_nascimento', $pessoa->data_nascimento)); ?>">
                                            <?php $__errorArgs = ['data_nascimento'];
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
                                            <label for="genero" class="form-label">Gênero</label>
                                            <select class="form-select <?php $__errorArgs = ['genero'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="genero" name="genero">
                                                <option value="">Selecione o gênero</option>
                                                <option value="masculino" <?php echo e(old('genero', $pessoa->genero) == 'masculino' ? 'selected' : ''); ?>>Masculino</option>
                                                <option value="feminino" <?php echo e(old('genero', $pessoa->genero) == 'feminino' ? 'selected' : ''); ?>>Feminino</option>
                                                <option value="outros" <?php echo e(old('genero', $pessoa->genero) == 'outros' ? 'selected' : ''); ?>>Outros</option>
                                                <option value="nao_informar" <?php echo e(old('genero', $pessoa->genero) == 'nao_informar' ? 'selected' : ''); ?>>Não informar</option>
                                            </select>
                                            <?php $__errorArgs = ['genero'];
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

                                    <!-- Contato -->
                                    <div class="col-12 mt-3">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-phone"></i> Contato
                                        </h6>
                                    </div>

                                    <div class="col-md-6">
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
                                                   id="email" name="email" value="<?php echo e(old('email', $pessoa->email)); ?>" 
                                                   placeholder="email@exemplo.com">
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

                                    <div class="col-md-6">
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
                                                   id="telefone" name="telefone" value="<?php echo e(old('telefone', $pessoa->telefone)); ?>" 
                                                   placeholder="(00) 00000-0000">
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

                                    <!-- Tipo e Organização -->
                                    <div class="col-12 mt-3">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-tags"></i> Tipo e Organização
                                        </h6>
                                    </div>

                                    <?php
                                        $tiposAtuais = explode(',', $pessoa->tipo);
                                        $tiposAtuais = array_map('trim', $tiposAtuais);
                                    ?>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Tipo(s) de Pessoa <span class="text-danger">*</span></label>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="tipo_cliente" name="tipos[]" value="cliente"
                                                               <?php echo e((is_array(old('tipos')) && in_array('cliente', old('tipos'))) || in_array('cliente', $tiposAtuais) ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="tipo_cliente">
                                                            <i class="fas fa-user-friends text-success"></i> Cliente
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="tipo_funcionario" name="tipos[]" value="funcionario"
                                                               <?php echo e((is_array(old('tipos')) && in_array('funcionario', old('tipos'))) || in_array('funcionario', $tiposAtuais) ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="tipo_funcionario">
                                                            <i class="fas fa-user-tie text-primary"></i> Funcionário
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="tipo_fornecedor" name="tipos[]" value="fornecedor"
                                                               <?php echo e((is_array(old('tipos')) && in_array('fornecedor', old('tipos'))) || in_array('fornecedor', $tiposAtuais) ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="tipo_fornecedor">
                                                            <i class="fas fa-truck text-warning"></i> Fornecedor
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="tipo_entregador" name="tipos[]" value="entregador"
                                                               <?php echo e((is_array(old('tipos')) && in_array('entregador', old('tipos'))) || in_array('entregador', $tiposAtuais) ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="tipo_entregador">
                                                            <i class="fas fa-motorcycle text-info"></i> Entregador
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php $__errorArgs = ['tipos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>

                                    <!-- Campos condicionais para funcionário -->
                                    <div id="campos_funcionario" class="col-12" style="display: <?php echo e(in_array('funcionario', $tiposAtuais) ? 'block' : 'none'); ?>;">
                                        <div class="row">
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
                                                            id="departamento_id" name="departamento_id">
                                                        <option value="">Selecione...</option>
                                                        <?php $__currentLoopData = $configuracoes['departamentos'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($dept->id); ?>" <?php echo e(old('departamento_id', $pessoa->departamento_id) == $dept->id ? 'selected' : ''); ?>>
                                                                <?php echo e($dept->nome); ?>

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
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="cargo_id" class="form-label">Cargo</label>
                                                    <select class="form-select <?php $__errorArgs = ['cargo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                            id="cargo_id" name="cargo_id">
                                                        <option value="">Selecione...</option>
                                                        <?php $__currentLoopData = $configuracoes['cargos'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cargo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($cargo->id); ?>" <?php echo e(old('cargo_id', $pessoa->cargo_id) == $cargo->id ? 'selected' : ''); ?>>
                                                                <?php echo e($cargo->nome); ?>

                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                    <?php $__errorArgs = ['cargo_id'];
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
                                                    <label for="data_admissao" class="form-label">Data de Admissão</label>
                                                    <input type="date" class="form-control <?php $__errorArgs = ['data_admissao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                           id="data_admissao" name="data_admissao" value="<?php echo e(old('data_admissao', $pessoa->data_admissao)); ?>">
                                                    <?php $__errorArgs = ['data_admissao'];
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
                                                    <label for="salario_atual" class="form-label">Salário Atual</label>
                                                    <input type="number" step="0.01" class="form-control <?php $__errorArgs = ['salario_atual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                           id="salario_atual" name="salario_atual" value="<?php echo e(old('salario_atual', $pessoa->salario_atual)); ?>" 
                                                           placeholder="0.00">
                                                    <?php $__errorArgs = ['salario_atual'];
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

                                    <!-- Status -->
                                    <div class="col-md-6">
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
                                                <option value="ativo" <?php echo e(old('status', $pessoa->status) == 'ativo' ? 'selected' : ''); ?>>Ativo</option>
                                                <option value="inativo" <?php echo e(old('status', $pessoa->status) == 'inativo' ? 'selected' : ''); ?>>Inativo</option>
                                                <option value="suspenso" <?php echo e(old('status', $pessoa->status) == 'suspenso' ? 'selected' : ''); ?>>Suspenso</option>
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

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="observacoes" class="form-label">Observações</label>
                                            <textarea class="form-control <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                      id="observacoes" name="observacoes" rows="3" 
                                                      placeholder="Observações importantes..."><?php echo e(old('observacoes', $pessoa->observacoes)); ?></textarea>
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

                                <!-- Botões -->
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="<?php echo e(route('comerciantes.clientes.pessoas.show', $pessoa->id)); ?>" 
                                       class="btn btn-secondary">
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="btnSalvar">
                                        <i class="fas fa-save"></i> Atualizar Pessoa
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Informações Adicionais -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle"></i> Informações
                            </h6>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">
                                <p><strong>Última atualização:</strong><br>
                                <?php echo e(\Carbon\Carbon::parse($pessoa->updated_at)->format('d/m/Y H:i')); ?></p>

                                <p><strong>Criado em:</strong><br>
                                <?php echo e(\Carbon\Carbon::parse($pessoa->created_at)->format('d/m/Y H:i')); ?></p>

                                <p><strong>ID da pessoa:</strong> <?php echo e($pessoa->id); ?></p>
                                <p><strong>ID da empresa:</strong> <?php echo e($pessoa->empresa_id); ?></p>

                                <hr>

                                <p><strong>Dicas:</strong></p>
                                <ul>
                                    <li>Uma pessoa pode ter múltiplos tipos</li>
                                    <li>Campos de funcionário aparecem automaticamente</li>
                                    <li>CPF/CNPJ é validado automaticamente</li>
                                    <li>Email deve ser único no sistema</li>
                                </ul>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formPessoa');
    const btnSalvar = document.getElementById('btnSalvar');
    const checkboxes = document.querySelectorAll('input[name="tipos[]"]');
    const camposFuncionario = document.getElementById('campos_funcionario');
    
    // Validação do formulário
    form.addEventListener('submit', function(e) {
        btnSalvar.disabled = true;
        btnSalvar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
    });

    // Mostrar/ocultar campos de funcionário
    function toggleCamposFuncionario() {
        const funcionarioChecked = document.getElementById('tipo_funcionario').checked;
        camposFuncionario.style.display = funcionarioChecked ? 'block' : 'none';
    }

    // Event listeners para checkboxes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', toggleCamposFuncionario);
    });

    // Máscara para telefone
    const telefone = document.getElementById('telefone');
    telefone.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 11) {
            value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        } else if (value.length >= 6) {
            value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else if (value.length >= 2) {
            value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
        }
        e.target.value = value;
    });

    // Máscara para CPF/CNPJ
    const cpfCnpj = document.getElementById('cpf_cnpj');
    cpfCnpj.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 11) {
            // CPF
            value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        } else {
            // CNPJ
            value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
        }
        e.target.value = value;
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciante/pessoas/edit.blade.php ENDPATH**/ ?>