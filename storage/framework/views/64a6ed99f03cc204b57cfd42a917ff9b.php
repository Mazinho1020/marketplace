<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Configuração - MeuFinanceiro</title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .main-content {
            background: rgba(255, 255, 255, 0.95);
            min-height: calc(100vh - 136px);
            border-radius: 15px;
            margin: 1rem;
            padding: 2rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 1.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg main-header">
        <div class="container-fluid">
            <a class="navbar-brand text-gradient fw-bold" href="/admin/dashboard">
                <i class="fas fa-chart-line me-2"></i>
                MeuFinanceiro
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/admin/config">
                    <i class="fas fa-arrow-left me-1"></i>
                    Voltar para Configurações
                </a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-gradient mb-1">
                    <i class="fas fa-plus me-2"></i>
                    Nova Configuração
                </h2>
                <p class="text-muted mb-0">Criar uma nova configuração do sistema</p>
            </div>
            <div>
                <a href="/admin/config" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Cancelar
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Dados da Configuração
                </h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.config.store')); ?>" method="POST" id="configForm">
                    <?php echo csrf_field(); ?>
                    
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Erros encontrados:</h6>
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Grupo -->
                        <div class="col-md-6 mb-3">
                            <label for="grupo_id" class="form-label">
                                <i class="fas fa-folder me-1"></i>Grupo *
                            </label>
                            <select class="form-select" id="grupo_id" name="grupo_id" required>
                                <option value="">Selecione um grupo</option>
                                <?php if(isset($grupos)): ?>
                                    <?php $__currentLoopData = $grupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($grupo->id); ?>" <?php echo e(old('grupo_id') == $grupo->id ? 'selected' : ''); ?>>
                                            <?php echo e($grupo->nome); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <option value="1">Sistema</option>
                                    <option value="2">Email</option>
                                    <option value="3">Fidelidade</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Site -->
                        <div class="col-md-6 mb-3">
                            <label for="site_id" class="form-label">
                                <i class="fas fa-globe me-1"></i>Site
                            </label>
                            <select class="form-select" id="site_id" name="site_id">
                                <option value="">Global (todos os sites)</option>
                                <?php if(isset($sites)): ?>
                                    <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($site->id); ?>" <?php echo e(old('site_id') == $site->id ? 'selected' : ''); ?>>
                                            <?php echo e($site->nome); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <option value="1">Site Principal</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Nome -->
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label">
                                <i class="fas fa-tag me-1"></i>Nome *
                            </label>
                            <input type="text" class="form-control" id="nome" name="nome" 
                                   value="<?php echo e(old('nome')); ?>" placeholder="Nome descritivo da configuração" required>
                        </div>

                        <!-- Chave -->
                        <div class="col-md-6 mb-3">
                            <label for="chave" class="form-label">
                                <i class="fas fa-key me-1"></i>Chave *
                            </label>
                            <input type="text" class="form-control" id="chave" name="chave" 
                                   value="<?php echo e(old('chave')); ?>" placeholder="chave_unica_configuracao" required>
                            <div class="form-text">Use apenas letras, números e underscores.</div>
                        </div>

                        <!-- Tipo -->
                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label">
                                <i class="fas fa-cog me-1"></i>Tipo de Dado *
                            </label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Selecione o tipo</option>
                                <?php if(isset($tipos)): ?>
                                    <?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e(old('tipo') == $key ? 'selected' : ''); ?>>
                                            <?php echo e($label); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <option value="string">Texto</option>
                                    <option value="text">Texto Longo</option>
                                    <option value="integer">Número Inteiro</option>
                                    <option value="boolean">Verdadeiro/Falso</option>
                                    <option value="email">Email</option>
                                    <option value="url">URL</option>
                                    <option value="json">JSON</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Valor Padrão -->
                        <div class="col-md-6 mb-3">
                            <label for="valor_padrao" class="form-label">
                                <i class="fas fa-code me-1"></i>Valor Padrão
                            </label>
                            <input type="text" class="form-control" id="valor_padrao" name="valor_padrao" 
                                   value="<?php echo e(old('valor_padrao')); ?>" placeholder="Valor padrão da configuração">
                        </div>

                        <!-- Descrição -->
                        <div class="col-12 mb-3">
                            <label for="descricao" class="form-label">
                                <i class="fas fa-info-circle me-1"></i>Descrição
                            </label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3" 
                                      placeholder="Descrição detalhada da configuração"><?php echo e(old('descricao')); ?></textarea>
                        </div>

                        <!-- Ordem -->
                        <div class="col-md-4 mb-3">
                            <label for="ordem" class="form-label">
                                <i class="fas fa-sort me-1"></i>Ordem
                            </label>
                            <input type="number" class="form-control" id="ordem" name="ordem" 
                                   value="<?php echo e(old('ordem', 0)); ?>" min="0" placeholder="0">
                        </div>

                        <!-- Opções de Configuração -->
                        <div class="col-md-8 mb-3">
                            <label class="form-label">
                                <i class="fas fa-sliders-h me-1"></i>Opções
                            </label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="obrigatorio" name="obrigatorio" value="1" 
                                               <?php echo e(old('obrigatorio') ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="obrigatorio">
                                            Obrigatório
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="visivel" name="visivel" value="1" 
                                               <?php echo e(old('visivel', true) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="visivel">
                                            Visível
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="editavel" name="editavel" value="1" 
                                               <?php echo e(old('editavel', true) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="editavel">
                                            Editável
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Valor Inicial -->
                        <div class="col-12 mb-3">
                            <label for="valor_inicial" class="form-label">
                                <i class="fas fa-play me-1"></i>Valor Inicial
                            </label>
                            <input type="text" class="form-control" id="valor_inicial" name="valor_inicial" 
                                   value="<?php echo e(old('valor_inicial')); ?>" placeholder="Valor inicial para esta configuração">
                            <div class="form-text">Se definido, será criado um valor específico além da definição.</div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex justify-content-between">
                        <a href="/admin/config" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Voltar
                        </a>
                        <div>
                            <button type="button" class="btn btn-outline-warning me-2" onclick="resetForm()">
                                <i class="fas fa-undo me-1"></i>
                                Limpar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Salvar Configuração
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-question-circle me-2"></i>
                    Ajuda
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6><i class="fas fa-info-circle me-1 text-info"></i>Dicas</h6>
                        <ul class="small text-muted">
                            <li>Use chaves descritivas e únicas</li>
                            <li>Mantenha nomes claros e concisos</li>
                            <li>Sempre adicione uma descrição</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="fas fa-cogs me-1 text-warning"></i>Tipos de Dados</h6>
                        <ul class="small text-muted">
                            <li><strong>String:</strong> Texto simples</li>
                            <li><strong>Boolean:</strong> true/false</li>
                            <li><strong>Integer:</strong> Números inteiros</li>
                            <li><strong>JSON:</strong> Dados estruturados</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="fas fa-examples me-1 text-success"></i>Exemplos</h6>
                        <ul class="small text-muted">
                            <li><strong>email_admin:</strong> admin@site.com</li>
                            <li><strong>sistema_ativo:</strong> true</li>
                            <li><strong>max_tentativas:</strong> 3</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/Theme1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Auto-generate chave from nome
        document.getElementById('nome').addEventListener('input', function() {
            const nome = this.value;
            const chave = nome.toLowerCase()
                             .replace(/[^a-z0-9]/g, '_')
                             .replace(/_+/g, '_')
                             .replace(/^_|_$/g, '');
            document.getElementById('chave').value = chave;
        });

        // Reset form
        function resetForm() {
            if (confirm('Tem certeza que deseja limpar todos os campos?')) {
                document.getElementById('configForm').reset();
            }
        }

        // Form validation
        document.getElementById('configForm').addEventListener('submit', function(e) {
            const nome = document.getElementById('nome').value.trim();
            const chave = document.getElementById('chave').value.trim();
            const grupoId = document.getElementById('grupo_id').value;
            const tipo = document.getElementById('tipo').value;

            if (!nome || !chave || !grupoId || !tipo) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Campos obrigatórios',
                    text: 'Por favor, preencha todos os campos obrigatórios marcados com *'
                });
                return;
            }

            // Validate chave format
            if (!/^[a-zA-Z0-9_]+$/.test(chave)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Chave inválida',
                    text: 'A chave deve conter apenas letras, números e underscores'
                });
                return;
            }

            // Show loading
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Salvando...';
        });

        // Success/error messages
        <?php if(session('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: '<?php echo e(session("success")); ?>',
                timer: 3000,
                timerProgressBar: true
            });
        <?php endif; ?>

        <?php if(session('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: '<?php echo e(session("error")); ?>'
            });
        <?php endif; ?>
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/config/create_simple.blade.php ENDPATH**/ ?>