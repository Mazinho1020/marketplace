<?php $__env->startSection('title', 'Editar Configuração'); ?>

<?php $__env->startSection('content'); ?>
<?php if(!isset($config)): ?>
    <div class="alert alert-danger">
        Erro: Configuração não encontrada. <a href="<?php echo e(route('admin.config.index')); ?>">Voltar à lista</a>
    </div>
<?php else: ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.config.index')); ?>">Configurações</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="uil uil-edit me-1"></i>
                    Editar Configuração: <?php echo e($config->chave ?? 'N/A'); ?>

                </h4>
            </div>
        </div>
    </div>

    <!-- Formulário -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('admin.config.update', $config->id ?? 0)); ?>" 
                        id="configForm" novalidate>
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <div class="row">
                            <!-- Coluna esquerda -->
                            <div class="col-md-8">
                                <!-- Informações básicas -->
                                <div class="mb-4">
                                    <h5 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="uil uil-info-circle me-2"></i>
                                        Informações Básicas
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="chave" class="form-label">
                                                    Chave <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" 
                                                    class="form-control <?php $__errorArgs = ['chave'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                    id="chave" name="chave" 
                                                    value="<?php echo e(old('chave', $config->chave ?? '')); ?>" 
                                                    placeholder="ex: app_name, debug_mode"
                                                    required>
                                                <?php $__errorArgs = ['chave'];
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
                                                    Use apenas letras, números e underscore. Ex: app_name
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="grupo_id" class="form-label">
                                                    Grupo <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select <?php $__errorArgs = ['grupo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                    id="grupo_id" name="grupo_id" required>
                                                    <option value="">Selecione um grupo</option>
                                                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($group->id); ?>" 
                                                            <?php echo e(old('grupo_id', $config->grupo_id ?? '') == $group->id ? 'selected' : ''); ?>>
                                                            <?php echo e($group->nome); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <?php $__errorArgs = ['grupo_id'];
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
                                        <label for="nome" class="form-label">
                                            Nome Amigável <span class="text-danger">*</span>
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
                                            id="nome" name="nome" 
                                            value="<?php echo e(old('nome', $config->nome ?? '')); ?>" 
                                            placeholder="ex: Nome da Aplicação"
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
                                            id="descricao" name="descricao" rows="3" 
                                            placeholder="Descreva o propósito desta configuração..."><?php echo e(old('descricao', $config->descricao ?? '')); ?></textarea>
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

                                <!-- Tipo e Valor -->
                                <div class="mb-4">
                                    <h5 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="uil uil-sliders-v me-2"></i>
                                        Tipo e Valor
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="tipo" class="form-label">
                                                    Tipo <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                    id="tipo" name="tipo" required>
                                                    <option value="">Selecione o tipo</option>
                                                    <option value="string" <?php echo e(old('tipo', $config->tipo ?? '') === 'string' ? 'selected' : ''); ?>>Texto</option>
                                                    <option value="integer" <?php echo e(old('tipo', $config->tipo ?? '') === 'integer' ? 'selected' : ''); ?>>Número Inteiro</option>
                                                    <option value="float" <?php echo e(old('tipo', $config->tipo ?? '') === 'float' ? 'selected' : ''); ?>>Número Decimal</option>
                                                    <option value="boolean" <?php echo e(old('tipo', $config->tipo ?? '') === 'boolean' ? 'selected' : ''); ?>>Verdadeiro/Falso</option>
                                                    <option value="array" <?php echo e(old('tipo', $config->tipo ?? '') === 'array' ? 'selected' : ''); ?>>Lista (Array)</option>
                                                    <option value="json" <?php echo e(old('tipo', $config->tipo ?? '') === 'json' ? 'selected' : ''); ?>>JSON</option>
                                                    <option value="url" <?php echo e(old('tipo', $config->tipo ?? '') === 'url' ? 'selected' : ''); ?>>URL</option>
                                                    <option value="email" <?php echo e(old('tipo', $config->tipo ?? '') === 'email' ? 'selected' : ''); ?>>E-mail</option>
                                                    <option value="password" <?php echo e(old('tipo', $config->tipo ?? '') === 'password' ? 'selected' : ''); ?>>Senha</option>
                                                </select>
                                                <?php $__errorArgs = ['tipo'];
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
                                                <label for="valor_padrao" class="form-label">Valor Padrão</label>
                                                <div id="valor-input-container">
                                                    <!-- Será preenchido via JavaScript -->
                                                </div>
                                                <?php $__errorArgs = ['valor_padrao'];
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

                                    <!-- Opções (para tipos select) -->
                                    <div class="mb-3" id="opcoes-container" style="display: none;">
                                        <label for="opcoes" class="form-label">Opções Disponíveis</label>
                                        <textarea class="form-control <?php $__errorArgs = ['opcoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="opcoes" name="opcoes" rows="3" 
                                            placeholder="Digite uma opção por linha ou separadas por vírgula"><?php echo e(old('opcoes', 
                                                (function() use ($config) {
                                                    if (!isset($config) || !$config) return '';
                                                    $opcoes = is_string($config->opcoes ?? '') ? json_decode($config->opcoes, true) : ($config->opcoes ?? '');
                                                    return is_array($opcoes) ? implode("\n", $opcoes) : ($config->opcoes ?? '');
                                                })()
                                            )); ?></textarea>
                                        <?php $__errorArgs = ['opcoes'];
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
                                            Digite uma opção por linha ou separadas por vírgula
                                        </div>
                                    </div>
                                </div>

                                <!-- Validação -->
                                <div class="mb-4">
                                    <h5 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="uil uil-shield-check me-2"></i>
                                        Validação
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="min_length" class="form-label">Tamanho Mínimo</label>
                                                <input type="number" 
                                                    class="form-control <?php $__errorArgs = ['min_length'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                    id="min_length" name="min_length" 
                                                    value="<?php echo e(old('min_length', $config->min_length ?? '')); ?>" 
                                                    min="0">
                                                <?php $__errorArgs = ['min_length'];
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
                                                <label for="max_length" class="form-label">Tamanho Máximo</label>
                                                <input type="number" 
                                                    class="form-control <?php $__errorArgs = ['max_length'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                    id="max_length" name="max_length" 
                                                    value="<?php echo e(old('max_length', $config->max_length ?? '')); ?>" 
                                                    min="1">
                                                <?php $__errorArgs = ['max_length'];
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
                                        <label for="regex_validacao" class="form-label">Regex de Validação</label>
                                        <input type="text" 
                                            class="form-control <?php $__errorArgs = ['regex_validacao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="regex_validacao" name="regex_validacao" 
                                            value="<?php echo e(old('regex_validacao', $config->regex_validacao ?? '')); ?>" 
                                            placeholder="ex: /^[a-zA-Z0-9]+$/">
                                        <?php $__errorArgs = ['regex_validacao'];
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
                                            Expressão regular para validação customizada (opcional)
                                        </div>
                                    </div>
                                </div>

                                <!-- Dicas e Ajuda -->
                                <div class="mb-4">
                                    <h5 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="uil uil-lightbulb me-2"></i>
                                        Dicas e Ajuda
                                    </h5>

                                    <div class="mb-3">
                                        <label for="dica" class="form-label">Dica</label>
                                        <input type="text" 
                                            class="form-control <?php $__errorArgs = ['dica'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="dica" name="dica" 
                                            value="<?php echo e(old('dica', $config->dica ?? '')); ?>" 
                                            placeholder="ex: Deixe em branco para usar o padrão">
                                        <?php $__errorArgs = ['dica'];
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
                                        <label for="ajuda" class="form-label">Texto de Ajuda</label>
                                        <textarea class="form-control <?php $__errorArgs = ['ajuda'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="ajuda" name="ajuda" rows="3" 
                                            placeholder="Explique como usar esta configuração, exemplos, etc..."><?php echo e(old('ajuda', $config->ajuda ?? '')); ?></textarea>
                                        <?php $__errorArgs = ['ajuda'];
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

                            <!-- Coluna direita -->
                            <div class="col-md-4">
                                <!-- Status atual -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="uil uil-info me-2"></i>
                                            Status Atual
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <small class="text-muted">Criado em:</small><br>
                                            <span><?php echo e($config->created_at ? \Carbon\Carbon::parse($config->created_at)->format('d/m/Y H:i') : 'N/A'); ?></span>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Última alteração:</small><br>
                                            <span><?php echo e($config->updated_at ? \Carbon\Carbon::parse($config->updated_at)->format('d/m/Y H:i') : 'N/A'); ?></span>
                                        </div>
                                        <?php if($valor): ?>
                                            <div class="mb-2">
                                                <small class="text-muted">Valor atual definido:</small><br>
                                                <span class="badge bg-info">Sim</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Configurações -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="uil uil-setting me-2"></i>
                                            Configurações
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                    id="obrigatorio" name="obrigatorio" value="1"
                                                    <?php echo e(old('obrigatorio', $config->obrigatorio ?? false) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="obrigatorio">
                                                    Campo Obrigatório
                                                </label>
                                            </div>
                                            <div class="form-text">
                                                Sistema irá requerer um valor para esta configuração
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                    id="avancado" name="avancado" value="1"
                                                    <?php echo e(old('avancado', $config->avancado ?? false) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="avancado">
                                                    Configuração Avançada
                                                </label>
                                            </div>
                                            <div class="form-text">
                                                Será exibida apenas no modo avançado
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                    id="ativo" name="ativo" value="1"
                                                    <?php echo e(old('ativo', $config->ativo ?? true) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="ativo">
                                                    Ativo
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="ordem" class="form-label">Ordem de Exibição</label>
                                            <input type="number" 
                                                class="form-control <?php $__errorArgs = ['ordem'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                id="ordem" name="ordem" 
                                                value="<?php echo e(old('ordem', $config->ordem)); ?>" 
                                                min="0">
                                            <?php $__errorArgs = ['ordem'];
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

                                <!-- Ações -->
                                <div class="d-grid gap-2 mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="uil uil-check me-1"></i>
                                        Salvar Alterações
                                    </button>
                                    <a href="<?php echo e(route('admin.config.index')); ?>" class="btn btn-secondary">
                                        <i class="uil uil-arrow-left me-1"></i>
                                        Voltar
                                    </a>
                                    <hr>
                                    <a href="<?php echo e(route('admin.config.history', $config->id)); ?>" class="btn btn-info">
                                        <i class="uil uil-history me-1"></i>
                                        Ver Histórico
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="deleteConfig()">
                                        <i class="uil uil-trash me-1"></i>
                                        Excluir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a configuração <strong><?php echo e($config->chave); ?></strong>?</p>
                <p class="text-warning">
                    <i class="uil uil-exclamation-triangle me-1"></i>
                    Esta ação não pode ser desfeita e todos os valores associados serão perdidos.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="<?php echo e(route('admin.config.destroy', $config->id)); ?>" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo');
    const valorContainer = document.getElementById('valor-input-container');
    const opcoesContainer = document.getElementById('opcoes-container');
    
    // Valor atual da configuração
    const currentValue = <?php echo json_encode(old('valor_padrao', $config->valor_padrao), 512) ?>;

    tipoSelect.addEventListener('change', function() {
        updateValueInput(this.value);
    });

    // Atualizar na inicialização
    updateValueInput(tipoSelect.value);

    function updateValueInput(tipo) {
        // Limpar container
        valorContainer.innerHTML = '';
        opcoesContainer.style.display = 'none';

        let input;

        switch (tipo) {
            case 'boolean':
                input = `
                    <select class="form-select" id="valor_padrao" name="valor_padrao">
                        <option value="">Selecione um valor</option>
                        <option value="1" ${currentValue === '1' || currentValue === true ? 'selected' : ''}>Verdadeiro</option>
                        <option value="0" ${currentValue === '0' || currentValue === false ? 'selected' : ''}>Falso</option>
                    </select>
                `;
                break;

            case 'integer':
                input = `
                    <input type="number" 
                        class="form-control" 
                        id="valor_padrao" 
                        name="valor_padrao" 
                        value="${currentValue || ''}" 
                        placeholder="Digite um número inteiro..."
                        step="1">
                `;
                break;

            case 'float':
                input = `
                    <input type="number" 
                        class="form-control" 
                        id="valor_padrao" 
                        name="valor_padrao" 
                        value="${currentValue || ''}" 
                        placeholder="Digite um número decimal..."
                        step="0.01">
                `;
                break;

            case 'array':
                const arrayValue = Array.isArray(currentValue) ? currentValue.join('\n') : (currentValue || '');
                input = `
                    <textarea class="form-control" 
                        id="valor_padrao" 
                        name="valor_padrao" 
                        rows="3" 
                        placeholder="item1,item2,item3 ou um por linha">${arrayValue}</textarea>
                `;
                break;

            case 'json':
                const jsonValue = typeof currentValue === 'object' ? JSON.stringify(currentValue, null, 2) : (currentValue || '');
                input = `
                    <textarea class="form-control" 
                        id="valor_padrao" 
                        name="valor_padrao" 
                        rows="4" 
                        placeholder='{"chave": "valor", "outra": 123}'>${jsonValue}</textarea>
                `;
                break;

            case 'password':
                input = `
                    <input type="password" 
                        class="form-control" 
                        id="valor_padrao" 
                        name="valor_padrao" 
                        value="${currentValue || ''}" 
                        placeholder="Digite a senha...">
                `;
                break;

            case 'url':
                input = `
                    <input type="url" 
                        class="form-control" 
                        id="valor_padrao" 
                        name="valor_padrao" 
                        value="${currentValue || ''}" 
                        placeholder="https://exemplo.com">
                `;
                break;

            case 'email':
                input = `
                    <input type="email" 
                        class="form-control" 
                        id="valor_padrao" 
                        name="valor_padrao" 
                        value="${currentValue || ''}" 
                        placeholder="usuario@exemplo.com">
                `;
                break;

            default:
                input = `
                    <input type="text" 
                        class="form-control" 
                        id="valor_padrao" 
                        name="valor_padrao" 
                        value="${currentValue || ''}" 
                        placeholder="Digite o valor padrão...">
                `;
        }

        valorContainer.innerHTML = input;
    }
});

function deleteConfig() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/config/edit.blade.php ENDPATH**/ ?>