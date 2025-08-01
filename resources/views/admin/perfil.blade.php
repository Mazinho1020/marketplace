<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - MeuFinanceiro</title>
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
        .user-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="page-header">
            <h1><i class="mdi mdi-account-circle"></i> Meu Perfil</h1>
            <p class="mb-0">Informações e configurações da sua conta</p>
        </div>
        
        <div class="content-card">
            <div class="row">
                <div class="col-md-8">
                    <h3>👤 Informações do Usuário</h3>
                    
                    @if(session('usuario_nome'))
                    <div class="user-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nome:</strong><br>
                                <span class="text-muted">{{ session('usuario_nome') }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong><br>
                                <span class="text-muted">{{ session('usuario_email') }}</span>
                            </div>
                            <div class="col-md-6 mt-3">
                                <strong>Tipo de Usuário:</strong><br>
                                <span class="badge bg-primary">{{ session('tipo_nome', 'N/A') }}</span>
                            </div>
                            <div class="col-md-6 mt-3">
                                <strong>Nível de Acesso:</strong><br>
                                <span class="badge bg-success">{{ session('nivel_acesso', 0) }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <p class="text-muted">
                        Esta área permite editar suas informações pessoais:
                    </p>
                    <ul class="text-muted">
                        <li>Alterar senha</li>
                        <li>Atualizar informações de contato</li>
                        <li>Configurar preferências</li>
                        <li>Gerenciar sessões ativas</li>
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
            
            @if(isset($message))
                <div class="alert alert-warning">
                    <i class="mdi mdi-alert"></i> {{ $message }}
                </div>
            @endif
            
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
