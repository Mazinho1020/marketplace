<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Dashboard
                </h2>
                <p class="text-muted mb-0">Bem-vindo, <strong><?php echo e($user->nome); ?></strong>!</p>
                <small class="text-muted">Último acesso: <?php echo e($user->last_login ? $user->last_login->format('d/m/Y H:i') : 'Primeiro acesso'); ?></small>
            </div>
            
            <?php if($user->todas_empresas->count() > 0): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-building me-2"></i>
                        <?php if(session('empresa_atual_id')): ?>
                            <?php
                                $empresaAtual = $user->todas_empresas->firstWhere('id', session('empresa_atual_id'));
                            ?>
                            <?php echo e($empresaAtual ? Str::limit(($empresaAtual->nome_fantasia ?: $empresaAtual->razao_social) ?? 'Empresa', 20) : 'Todas as Empresas'); ?>

                        <?php else: ?>
                            Todas as Empresas
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item <?php echo e(!session('empresa_atual_id') ? 'active' : ''); ?>" 
                               href="<?php echo e(route('comerciantes.dashboard.limpar')); ?>">
                                <i class="fas fa-list me-2"></i>
                                Todas as Empresas
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <?php $__currentLoopData = $user->todas_empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <a class="dropdown-item <?php echo e(session('empresa_atual_id') == $empresa->id ? 'active' : ''); ?>" 
                                   href="<?php echo e(route('comerciantes.dashboard.empresa', $empresa->id)); ?>">
                                    <i class="fas fa-building me-2"></i>
                                    <?php echo e(Str::limit(($empresa->nome_fantasia ?: $empresa->razao_social) ?? 'Empresa', 30)); ?>

                                    <span class="badge badge-<?php echo e($empresa->status == 'ativa' ? 'ativa' : 'inativa'); ?> ms-2">
                                        <?php echo e(ucfirst($empresa->status)); ?>

                                    </span>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Cards Estatísticos -->
<div class="row mb-4">
    <!-- Total de Marcas -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card border-left-primary">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="stats-label">Total de Marcas</div>
                    <div class="stats-value"><?php echo e($dashboardData['estatisticas']['total_marcas']); ?></div>
                </div>
                <div class="col-auto">
                    <div class="stats-icon bg-primary">
                        <i class="fas fa-tags"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total de Empresas -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card border-left-success">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="stats-label">Total de Empresas</div>
                    <div class="stats-value"><?php echo e($dashboardData['estatisticas']['total_empresas']); ?></div>
                </div>
                <div class="col-auto">
                    <div class="stats-icon bg-success">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empresas Ativas -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card border-left-info">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="stats-label">Empresas Ativas</div>
                    <div class="stats-value"><?php echo e($dashboardData['estatisticas']['empresas_ativas']); ?></div>
                </div>
                <div class="col-auto">
                    <div class="stats-icon bg-info">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Usuários Vinculados -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card border-left-warning">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="stats-label">Usuários Vinculados</div>
                    <div class="stats-value"><?php echo e($dashboardData['estatisticas']['usuarios_vinculados']); ?></div>
                </div>
                <div class="col-auto">
                    <div class="stats-icon bg-warning">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Informações do Plano Atual -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-gem me-2"></i>
                    Seu Plano Atual
                </h6>
                <a href="<?php echo e(route('comerciantes.planos.planos')); ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-up me-1"></i>
                    Fazer Upgrade
                </a>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="plan-icon mb-2">
                                <i class="fas fa-gem fa-3x text-primary"></i>
                            </div>
                            <h5 class="mb-0"><?php echo e($planoAtual['nome']); ?></h5>
                            <small class="text-muted">Status: 
                                <span class="badge bg-<?php echo e($planoAtual['status'] === 'ativo' ? 'success' : ($planoAtual['status'] === 'trial' ? 'warning' : 'secondary')); ?>">
                                    <?php echo e(ucfirst($planoAtual['status'])); ?>

                                </span>
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-info">∞</div>
                                    <small class="text-muted">Marcas</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-success">∞</div>
                                    <small class="text-muted">Empresas</small>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-warning">∞</div>
                                    <small class="text-muted">Usuários</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-primary">5GB</div>
                                    <small class="text-muted">Armazenamento</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <?php if($planoAtual['vencimento']): ?>
                                <div class="mb-2">
                                    <small class="text-muted">Próxima renovação:</small>
                                    <div class="h6 mb-0"><?php echo e($planoAtual['vencimento']); ?></div>
                                </div>
                            <?php else: ?>
                                <div class="mb-2">
                                    <small class="text-muted">Renovação:</small>
                                    <div class="h6 mb-0"><?php echo e($planoAtual['renovacao_automatica'] ? 'Automática' : 'Manual'); ?></div>
                                </div>
                            <?php endif; ?>
                            <div class="d-grid gap-2">
                                <a href="<?php echo e(route('comerciantes.planos.dashboard')); ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-cog me-1"></i>
                                    Gerenciar
                                </a>
                                <a href="<?php echo e(route('comerciantes.planos.historico')); ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-history me-1"></i>
                                    Histórico
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progresso de Configuração -->
<?php if($dashboardData['progresso_configuracao']['porcentagem'] < 100): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tasks me-2"></i>
                    Progresso de Configuração
                </h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <p class="mb-2">Complete seu perfil para aproveitar todas as funcionalidades:</p>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: <?php echo e($dashboardData['progresso_configuracao']['porcentagem']); ?>%">
                                <?php echo e($dashboardData['progresso_configuracao']['porcentagem']); ?>%
                            </div>
                        </div>
                        <small class="text-muted">
                            <?php echo e($dashboardData['progresso_configuracao']['completos']); ?> de <?php echo e($dashboardData['progresso_configuracao']['total']); ?> itens completos
                        </small>
                    </div>
                    <div class="col-md-4">
                        <div class="checklist">
                            <?php $__currentLoopData = $dashboardData['progresso_configuracao']['itens']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $completo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas <?php echo e($completo ? 'fa-check-circle text-success' : 'fa-circle text-muted'); ?> me-2"></i>
                                    <small class="<?php echo e($completo ? 'text-success' : 'text-muted'); ?>">
                                        <?php switch($key):
                                            case ('perfil_completo'): ?>
                                                Perfil completo
                                                <?php break; ?>
                                            <?php case ('tem_marca'): ?>
                                                Marca criada
                                                <?php break; ?>
                                            <?php case ('tem_empresa'): ?>
                                                Empresa criada
                                                <?php break; ?>
                                            <?php case ('empresa_com_endereco'): ?>
                                                Endereço configurado
                                                <?php break; ?>
                                            <?php case ('empresa_com_horario'): ?>
                                                Horário configurado
                                                <?php break; ?>
                                        <?php endswitch; ?>
                                    </small>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Sugestões de Ações ou Ações Rápidas -->
<?php if(!empty($sugestoes)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-lightbulb me-2"></i>
                    Sugestões para Você
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php $__currentLoopData = $sugestoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sugestao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <a href="<?php echo e($sugestao['url']); ?>" class="quick-action-btn">
                                <div class="quick-action-icon">
                                    <i class="<?php echo e($sugestao['icone']); ?>"></i>
                                </div>
                                <strong><?php echo e($sugestao['titulo']); ?></strong>
                                <small class="text-muted text-center"><?php echo e($sugestao['descricao']); ?></small>
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-rocket me-2"></i>
                    Ações Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="<?php echo e(route('comerciantes.planos.dashboard')); ?>" class="quick-action-btn">
                            <div class="quick-action-icon">
                                <i class="fas fa-gem"></i>
                            </div>
                            <strong>Meus Planos</strong>
                            <small class="text-muted">Gerenciar assinatura</small>
                        </a>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="<?php echo e(route('comerciantes.produtos.kits.index')); ?>" class="quick-action-btn">
                            <div class="quick-action-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <strong>Kits/Combos</strong>
                            <small class="text-muted">Criar ofertas combinadas</small>
                        </a>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="<?php echo e(route('comerciantes.marcas.create')); ?>" class="quick-action-btn">
                            <div class="quick-action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <strong>Nova Marca</strong>
                            <small class="text-muted">Criar uma nova marca</small>
                        </a>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="<?php echo e(route('comerciantes.empresas.create')); ?>" class="quick-action-btn">
                            <div class="quick-action-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <strong>Nova Empresa</strong>
                            <small class="text-muted">Adicionar nova empresa</small>
                        </a>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="<?php echo e(route('comerciantes.planos.planos')); ?>" class="quick-action-btn">
                            <div class="quick-action-icon">
                                <i class="fas fa-gem"></i>
                            </div>
                            <strong>Upgrade de Plano</strong>
                            <small class="text-muted">Mais recursos para sua empresa</small>
                        </a>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="#" class="quick-action-btn">
                            <div class="quick-action-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <strong>Relatórios</strong>
                            <small class="text-muted">Ver relatórios</small>
                        </a>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="#" class="quick-action-btn">
                            <div class="quick-action-icon">
                                <i class="fas fa-cog"></i>
                            </div>
                            <strong>Configurações</strong>
                            <small class="text-muted">Configurar conta</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Suas Marcas -->
<?php if($dashboardData['marcas_recentes']->count() > 0): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tags me-2"></i>
                    Suas Marcas Recentes
                </h6>
                <a href="<?php echo e(route('comerciantes.marcas.index')); ?>" class="btn btn-sm btn-outline-primary">
                    Ver Todas
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php $__currentLoopData = $dashboardData['marcas_recentes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $marca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <?php if($marca->logo_url): ?>
                                            <img src="<?php echo e($marca->logo_url_completo); ?>" alt="<?php echo e($marca->nome); ?>" 
                                                 class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-primary rounded me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-tags text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-0"><?php echo e($marca->nome); ?></h6>
                                            <small class="text-muted">
                                                <span class="badge badge-<?php echo e($marca->status == 'ativa' ? 'ativa' : 'inativa'); ?>">
                                                    <?php echo e(ucfirst($marca->status)); ?>

                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-building me-1"></i>
                                            <?php echo e($marca->empresas->count()); ?> empresa(s)
                                        </small>
                                    </div>
                                    
                                    <?php if($marca->descricao): ?>
                                        <p class="text-muted small mb-3"><?php echo e(Str::limit($marca->descricao ?? '', 80)); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex gap-2">
                                        <a href="<?php echo e(route('comerciantes.marcas.show', $marca)); ?>" 
                                           class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="fas fa-eye me-1"></i>
                                            Ver
                                        </a>
                                        <a href="<?php echo e(route('comerciantes.marcas.edit', $marca)); ?>" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Estado Vazio - Primeira vez -->
<?php if($dashboardData['is_primeira_vez']): ?>
<div class="row">
    <div class="col-12">
        <div class="card text-center">
            <div class="card-body py-5">
                <div class="mb-4">
                    <i class="fas fa-store fa-4x text-muted mb-3"></i>
                    <h4>Bem-vindo ao seu Marketplace!</h4>
                    <p class="text-muted mb-4">
                        Comece criando sua primeira marca para organizar suas empresas e produtos.
                        <br>
                        Depois adicione suas unidades/lojas e configure tudo para começar a vender.
                    </p>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="<?php echo e(route('comerciantes.marcas.create')); ?>" 
                                   class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-tags me-2"></i>
                                    Criar Primeira Marca
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="#" class="btn btn-outline-primary btn-lg w-100">
                                    <i class="fas fa-question-circle me-2"></i>
                                    Ver Tutorial
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <small class="text-muted">
                        <i class="fas fa-lightbulb me-1"></i>
                        Dica: Uma marca pode ter várias empresas. Por exemplo, "Pizzaria Tradição" pode ter unidades em diferentes bairros.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Atualizar estatísticas a cada 30 segundos
    setInterval(function() {
        fetch('<?php echo e(route("comerciantes.dashboard")); ?>/estatisticas')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar os valores nos cards
                    document.querySelector('.stats-card:nth-child(1) .stats-value').textContent = data.data.total_marcas;
                    document.querySelector('.stats-card:nth-child(2) .stats-value').textContent = data.data.total_empresas;
                    document.querySelector('.stats-card:nth-child(3) .stats-value').textContent = data.data.empresas_ativas;
                    document.querySelector('.stats-card:nth-child(4) .stats-value').textContent = data.data.usuarios_vinculados;
                }
            })
            .catch(error => console.log('Erro ao atualizar estatísticas:', error));
    }, 30000);

    // Animação dos cards ao carregar
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.stats-card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.4s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100);
            }, index * 100);
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('comerciantes.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/dashboard/index.blade.php ENDPATH**/ ?>