<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado - MeuFinanceiro</title>
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Theme1/css/icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .access-denied-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .access-denied-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
        }
        .access-denied-title {
            color: #dc3545;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .access-denied-message {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .user-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="access-denied-container">
        <div class="access-denied-icon">
            <i class="mdi mdi-shield-remove"></i>
        </div>
        
        <h1 class="access-denied-title">Acesso Negado</h1>
        
        <div class="access-denied-message">
            Você não tem permissão suficiente para acessar esta área do sistema.
            <br>
            Entre em contato com o administrador se você acredita que deveria ter acesso.
        </div>
        
        @if(session('usuario_nome'))
        <div class="user-info">
            <strong>Usuário:</strong> {{ session('usuario_nome') }}<br>
            <strong>Tipo:</strong> {{ session('tipo_nome', 'N/A') }}<br>
            <strong>Nível de Acesso:</strong> {{ session('nivel_acesso', 0) }}
        </div>
        @endif
        
        <div class="d-flex justify-content-center gap-3">
            <a href="/admin/dashboard" class="btn-back">
                <i class="mdi mdi-arrow-left me-2"></i>Voltar ao Dashboard
            </a>
            <a href="/logout" class="btn btn-outline-secondary">
                <i class="mdi mdi-logout me-2"></i>Sair
            </a>
        </div>
        
        <div class="mt-4">
            <small class="text-muted">
                Se você precisa de mais permissões, contate o administrador do sistema.
            </small>
        </div>
    </div>
    
    <script src="/Theme1/js/bootstrap.bundle.min.js"></script>
</body>
</html>
