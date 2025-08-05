<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Marketplace Comerciante</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Padrão de Cores do Marketplace -->
    <link rel="stylesheet" href="{{ asset('estilos/cores.css') }}">
    
    <style>
        body {
            background: var(--gradient-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: var(--surface);
            border-radius: 20px;
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        
        .login-left {
            background: var(--gradient-primary);
            padding: 3rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            min-height: 500px;
        }
        
        .login-right {
            padding: 3rem;
            background: var(--surface);
        }
        
        .logo {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .welcome-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .welcome-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .login-form-title {
            color: var(--text-primary);
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .login-form-subtitle {
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 1rem;
            font-size: 1rem;
            color: var(--text-primary);
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
            background: var(--surface);
        }
        
        .form-label {
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        .btn-login {
            background: var(--primary);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.2s ease;
        }
        
        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }
        
        .form-check-input {
            border: 2px solid var(--border);
            border-radius: 4px;
        }
        
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .forgot-password:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .features-list {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
        }
        
        .features-list li {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        
        .features-list i {
            margin-right: 12px;
            font-size: 1.2rem;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .theme-toggle-login {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 8px 12px;
            color: white;
            cursor: pointer;
            backdrop-filter: blur(10px);
            transition: all 0.2s ease;
        }
        
        .theme-toggle-login:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        /* Modo escuro */
        [data-theme="dark"] .login-container {
            background: var(--surface);
        }
        
        [data-theme="dark"] .login-right {
            background: var(--surface);
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
            .login-left {
                display: none;
            }
            
            .login-right {
                padding: 2rem 1.5rem;
            }
            
            .welcome-title {
                font-size: 1.5rem;
            }
            
            .login-form-title {
                font-size: 1.5rem;
            }
        }
        
        /* Animações */
        .login-container {
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .feature-item {
            animation: fadeInLeft 0.6s ease-out;
            animation-fill-mode: both;
        }
        
        .feature-item:nth-child(1) { animation-delay: 0.1s; }
        .feature-item:nth-child(2) { animation-delay: 0.2s; }
        .feature-item:nth-child(3) { animation-delay: 0.3s; }
        .feature-item:nth-child(4) { animation-delay: 0.4s; }
        
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <!-- Toggle de Tema -->
    <button class="theme-toggle-login" onclick="toggleTheme()" title="Alternar tema">
        <i class="fas fa-moon" id="theme-icon"></i>
    </button>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="login-container row g-0">
                    <!-- Lado Esquerdo - Informativo -->
                    <div class="col-md-6 login-left">
                        <div>
                            <div class="logo">
                                <i class="fas fa-store"></i>
                            </div>
                            <h1 class="welcome-title">Bem-vindo de volta!</h1>
                            <p class="welcome-subtitle">
                                Gerencie suas marcas e empresas de forma simples e eficiente
                            </p>
                            
                            <ul class="features-list">
                                <li class="feature-item">
                                    <i class="fas fa-chart-line"></i>
                                    Dashboard completo com métricas em tempo real
                                </li>
                                <li class="feature-item">
                                    <i class="fas fa-tags"></i>
                                    Gestão completa de marcas e empresas
                                </li>
                                <li class="feature-item">
                                    <i class="fas fa-users"></i>
                                    Controle de usuários e permissões
                                </li>
                                <li class="feature-item">
                                    <i class="fas fa-mobile-alt"></i>
                                    Interface responsiva e moderna
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Lado Direito - Formulário de Login -->
                    <div class="col-md-6 login-right">
                        <div>
                            <h2 class="login-form-title">Fazer Login</h2>
                            <p class="login-form-subtitle">Acesse sua conta de comerciante</p>
                            
                            <!-- Alertas de Erro -->
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    @if($errors->has('email'))
                                        {{ $errors->first('email') }}
                                    @else
                                        {{ $errors->first() }}
                                    @endif
                                </div>
                            @endif
                            
                            @if(session('success'))
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ session('success') }}
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    {{ session('error') }}
                                </div>
                            @endif
                            
                            <!-- Formulário de Login -->
                            <form method="POST" action="{{ route('comerciantes.login') }}" id="loginForm">
                                @csrf
                                
                                <!-- Email -->
                                <div class="form-floating">
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           placeholder="seu@email.com"
                                           value="{{ old('email') }}"
                                           required>
                                    <label for="email">
                                        <i class="fas fa-envelope me-2"></i>
                                        Email
                                    </label>
                                </div>
                                
                                <!-- Senha -->
                                <div class="form-floating">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Sua senha"
                                           required>
                                    <label for="password">
                                        <i class="fas fa-lock me-2"></i>
                                        Senha
                                    </label>
                                </div>
                                
                                <!-- Lembrar de mim -->
                                <div class="remember-me">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Lembrar de mim
                                    </label>
                                </div>
                                
                                <!-- Botão de Login -->
                                <button type="submit" class="btn btn-login" id="loginBtn">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Entrar
                                </button>
                            </form>
                            
                            <!-- Links Adicionais -->
                            <div class="text-center mt-4">
                                <a href="#" class="forgot-password">
                                    <i class="fas fa-question-circle me-1"></i>
                                    Esqueci minha senha
                                </a>
                            </div>
                            
                            <!-- Rodapé -->
                            <div class="text-center mt-4 pt-4" style="border-top: 1px solid var(--border);">
                                <small class="text-muted">
                                    © {{ date('Y') }} Marketplace. Todos os direitos reservados.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sistema de alternância de tema (mesmo do layout principal)
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            
            const icon = document.getElementById('theme-icon');
            icon.className = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            
            localStorage.setItem('theme', newTheme);
        }
        
        function loadSavedTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');
            
            html.setAttribute('data-theme', savedTheme);
            icon.className = savedTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
        }
        
        // Inicializa o tema
        document.addEventListener('DOMContentLoaded', function() {
            loadSavedTheme();
        });
        
        // Loading no botão de login
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Entrando...';
            btn.disabled = true;
            
            // Em caso de erro, volta o texto original após 3 segundos
            setTimeout(() => {
                if (btn.disabled) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            }, 3000);
        });
        
        // Foco automático no campo email
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });
        
        // Enter para submeter form
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').submit();
            }
        });
    </script>
</body>
</html>
