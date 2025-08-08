<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - MeuFinanceiro</title>
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
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        .login-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }
        .login-body {
            padding: 2rem;
        }
        .form-floating {
            margin-bottom: 1rem;
        }
        .form-floating input {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem 0.75rem;
            height: auto;
        }
        .form-floating input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .form-floating label {
            padding: 1rem 0.75rem;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1rem 0;
            font-size: 0.9rem;
        }
        .form-check {
            margin: 0;
        }
        .forgot-link {
            color: #667eea;
            text-decoration: none;
        }
        .forgot-link:hover {
            color: #5a6fd8;
            text-decoration: underline;
        }
        .alert {
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        .login-footer {
            text-align: center;
            padding: 1rem 2rem 2rem;
            color: #6c757d;
            font-size: 0.85rem;
        }
        .system-info {
            background: #f8f9fa;
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 8px;
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🏢 MeuFinanceiro</h1>
            <p>Sistema Administrativo</p>
        </div>
        
        <div class="login-body">
            @if($errors->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-circle me-2"></i>
                    {{ $errors->first('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <form action="/login" method="POST">
                @csrf
                
                <div class="form-floating">
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           placeholder="nome@exemplo.com" 
                           value="{{ old('email') }}" 
                           required>
                    <label for="email">
                        <i class="mdi mdi-email me-2"></i>Email
                    </label>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-floating">
                    <input type="password" 
                           class="form-control @error('senha') is-invalid @enderror" 
                           id="senha" 
                           name="senha" 
                           placeholder="Senha" 
                           required>
                    <label for="senha">
                        <i class="mdi mdi-lock me-2"></i>Senha
                    </label>
                    @error('senha')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="remember-forgot">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="lembrar" name="lembrar">
                        <label class="form-check-label" for="lembrar">
                            Lembrar de mim
                        </label>
                    </div>
                    <a href="/login/forgot" class="forgot-link">Esqueceu a senha?</a>
                </div>
                
                <button class="w-100 btn btn-primary btn-login" type="submit">
                    <i class="mdi mdi-login me-2"></i>Entrar
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            <div class="system-info">
                <div><strong>Tipos de Usuário:</strong></div>
                <div>🔹 Administrador - Acesso completo</div>
                <div>🔹 Gerente - Funções gerenciais</div>
                <div>🔹 Supervisor - Supervisão</div>
                <div>🔹 Operador - Operacional</div>
                <div>🔹 Consulta - Somente leitura</div>
            </div>
            
            <div class="mt-3">
                <p>&copy; MeuFinanceiro {{ date('Y') }}</p>
                <p class="mb-0">Versão 2.0 | {{ request()->getHost() }}</p>
            </div>
        </div>
    </div>
    
    <script src="/Theme1/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus no primeiro campo
        document.getElementById('email').focus();
        
        // Limpar alerts automaticamente após 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            });
        }, 5000);
    </script>
</body>
</html>
