<?php
    // Função helper para obter o nome do usuário de forma segura
    function getNomeUsuario($usuario) {
        if (!$usuario) return 'Nome não disponível';
        
        // Tentar diferentes campos possíveis
        $campos = ['username', 'nome', 'name', 'first_name', 'nome_completo'];
        
        foreach ($campos as $campo) {
            if (isset($usuario->$campo) && !empty($usuario->$campo)) {
                return $usuario->$campo;
            }
        }
        
        return 'Nome não disponível';
    }
    
    // Função helper para obter a inicial do usuário
    function getInicialUsuario($usuario) {
        $nome = getNomeUsuario($usuario);
        return strtoupper(substr($nome, 0, 1));
    }
?>



<?php $__env->startSection('title', 'Gerenciar Usuários - ' . ($empresa->nome_fantasia ?? 'Empresa')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('comerciantes.dashboard')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('comerciantes.empresas.index')); ?>">Empresas</a></li>
            <li class="breadcrumb-item active">Usuários - <?php echo e($empresa->nome_fantasia ?? 'Empresa'); ?></li>
        </ol>
    </nav>

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gerenciar Usuários</h1>
            <p class="text-muted mb-0"><?php echo e($empresa->nome_fantasia ?? 'Empresa não identificada'); ?></p>
        </div>
        <a href="<?php echo e(route('comerciantes.empresas.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Voltar
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo e($empresa->usuariosVinculados ? $empresa->usuariosVinculados->count() : 0); ?></h4>
                    <small class="text-muted">Total de Usuários</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Usuários -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">
                <i class="fas fa-users me-2"></i>
                Usuários Vinculados (<?php echo e($empresa->usuariosVinculados ? $empresa->usuariosVinculados->count() : 0); ?>)
            </h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdicionarUsuario">
                    <i class="fas fa-user-plus me-1"></i>
                    Vincular Usuário
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCriarUsuario">
                    <i class="fas fa-plus me-1"></i>
                    Criar Novo
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            
            <?php if(request()->has('debug')): ?>
                <div class="alert alert-info m-3">
                    <h6>DEBUG INFO:</h6>
                    <p><strong>Empresa ID:</strong> <?php echo e($empresa->id ?? 'N/A'); ?></p>
                    <p><strong>Nome:</strong> <?php echo e($empresa->nome_fantasia ?? 'N/A'); ?></p>
                    <p><strong>usuariosVinculados definido:</strong> <?php echo e(isset($empresa->usuariosVinculados) ? 'SIM' : 'NÃO'); ?></p>
                    <?php if(isset($empresa->usuariosVinculados)): ?>
                        <p><strong>Tipo:</strong> <?php echo e(get_class($empresa->usuariosVinculados)); ?></p>
                        <p><strong>Count:</strong> <?php echo e($empresa->usuariosVinculados->count()); ?></p>
                        <?php if($empresa->usuariosVinculados->count() > 0): ?>
                            <p><strong>Primeiro usuário (raw):</strong> <pre><?php echo e(print_r($empresa->usuariosVinculados->first()->toArray(), true)); ?></pre></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Usuário</th>
                            <th>Perfil</th>
                            <th>Status</th>
                            <th>Data Vínculo</th>
                            <th width="120" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($empresa->usuariosVinculados) && is_object($empresa->usuariosVinculados) && $empresa->usuariosVinculados->count() > 0): ?>
                            <?php $__currentLoopData = $empresa->usuariosVinculados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vinculo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-3">
                                            <?php echo e(getInicialUsuario($vinculo)); ?>

                                        </div>
                                        <div>
                                            <div class="fw-medium"><?php echo e(getNomeUsuario($vinculo)); ?></div>
                                            <small class="text-muted"><?php echo e($vinculo->email ?? 'Email não disponível'); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if(isset($vinculo->pivot) && isset($vinculo->pivot->perfil)): ?>
                                        <span class="badge bg-<?php echo e($vinculo->pivot->perfil === 'proprietario' ? 'danger' : ($vinculo->pivot->perfil === 'administrador' ? 'warning' : 'info')); ?>">
                                            <?php echo e(ucfirst($vinculo->pivot->perfil)); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Indefinido</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(isset($vinculo->pivot) && isset($vinculo->pivot->status)): ?>
                                        <span class="badge bg-<?php echo e($vinculo->pivot->status === 'ativo' ? 'success' : 'secondary'); ?>">
                                            <?php echo e(ucfirst($vinculo->pivot->status)); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Indefinido</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(isset($vinculo->pivot) && isset($vinculo->pivot->data_vinculo)): ?>
                                        <small><?php echo e(\Carbon\Carbon::parse($vinculo->pivot->data_vinculo)->format('d/m/Y H:i')); ?></small>
                                    <?php else: ?>
                                        <small class="text-muted">Data não disponível</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if(!isset($vinculo->pivot) || $vinculo->pivot->perfil !== 'proprietario'): ?>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEditarUsuario"
                                                data-user-id="<?php echo e($vinculo->id ?? ''); ?>"
                                                data-user-nome="<?php echo e(getNomeUsuario($vinculo)); ?>"
                                                data-user-perfil="<?php echo e($vinculo->pivot->perfil ?? ''); ?>"
                                                data-user-status="<?php echo e($vinculo->pivot->status ?? ''); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm"
                                                onclick="confirmarRemocao(<?php echo e($vinculo->id ?? 0); ?>, '<?php echo e(addslashes(getNomeUsuario($vinculo))); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <?php else: ?>
                                    <small class="text-muted">Proprietário</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum usuário vinculado encontrado</h5>
                                <p class="text-muted mb-3">Clique em "Criar Novo" ou "Vincular Usuário" para adicionar usuários</p>
                                <?php if(request()->has('debug')): ?>
                                    <div class="alert alert-warning mt-3">
                                        <strong>Debug Info:</strong><br>
                                        usuariosVinculados: <?php echo e(isset($empresa->usuariosVinculados) ? 'Definido' : 'Não definido'); ?><br>
                                        Tipo: <?php echo e(isset($empresa->usuariosVinculados) ? get_class($empresa->usuariosVinculados) : 'N/A'); ?><br>
                                        Count: <?php echo e(isset($empresa->usuariosVinculados) ? $empresa->usuariosVinculados->count() : 'N/A'); ?>

                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
}

.card-hover {
    transition: transform 0.2s;
}

.card-hover:hover {
    transform: translateY(-2px);
}
</style>

<script>
function confirmarRemocao(userId, userName) {
    if (confirm(`Tem certeza que deseja remover o usuário "${userName}" desta empresa?`)) {
        // Aqui você implementaria a lógica de remoção
        console.log('Removendo usuário:', userId);
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('comerciantes.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/empresas/usuarios_safe.blade.php ENDPATH**/ ?>