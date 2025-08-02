<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - MeuFinanceiro</title>
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Theme1/css/icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            border: none;
            padding: 2rem;
            margin: 2rem 0;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="page-header">
            <h1><i class="mdi mdi-account-multiple"></i> Gerenciamento de Usuários</h1>
            <p class="mb-0">Controle e administração dos usuários do sistema</p>
        </div>
        
        <div class="content-card">
            <div class="row">
                <div class="col-md-8">
                    <h3>👥 Área de Usuários</h3>
                    <p class="text-muted">
                        Esta área permite gerenciar todos os usuários do sistema, incluindo:
                    </p>
                    <ul class="text-muted">
                        <li>Criar novos usuários</li>
                        <li>Editar informações existentes</li>
                        <li>Definir níveis de acesso</li>
                        <li>Ativar/desativar contas</li>
                        <li>Visualizar logs de atividade</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information"></i>
                        <strong>Em Desenvolvimento</strong><br>
                        Esta funcionalidade está sendo implementada.
                    </div>
                </div>
            </div>
            
            <?php if(isset($message)): ?>
                <div class="alert alert-warning">
                    <i class="mdi mdi-alert"></i> <?php echo e($message); ?>

                </div>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="/admin/dashboard" class="btn btn-back">
                    <i class="mdi mdi-arrow-left"></i> Voltar ao Dashboard
                </a>
            </div>
        </div>
    </div>
    
    <script src="/Theme1/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/usuarios.blade.php ENDPATH**/ ?>