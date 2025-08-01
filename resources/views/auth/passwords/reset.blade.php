<!DOCTYPE html>
<html lang="pt-BR" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg">

<head>
    <meta charset="utf-8" />
    <title>Redefinir Senha | Marketplace Sistema</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema de Marketplace" name="description" />
    <meta content="Marketplace" name="author" />
    
    <!-- Bootstrap Css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="auth-page-wrapper pt-5">
        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4">
                            <div>
                                <h3 class="text-primary">Marketplace Sistema</h3>
                            </div>
                            <p class="mt-3 fs-15 fw-medium text-muted">Redefinir Nova Senha</p>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Criar Nova Senha</h5>
                                    <p class="text-muted">Digite sua nova senha abaixo.</p>
                                </div>

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="p-2 mt-4">
                                    <form method="POST" action="{{ route('password.update') }}">
                                        @csrf

                                        <input type="hidden" name="token" value="{{ $token }}">
                                        <input type="hidden" name="email" value="{{ $email }}">

                                        <div class="mb-3">
                                            <label for="email_display" class="form-label">E-mail</label>
                                            <input type="email" class="form-control" 
                                                   id="email_display" 
                                                   value="{{ $email }}" 
                                                   readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="password">Nova Senha <span class="text-danger">*</span></label>
                                            <div class="position-relative auth-pass-inputgroup">
                                                <input type="password" class="form-control pe-5 password-input @error('password') is-invalid @enderror" 
                                                       placeholder="Digite sua nova senha" 
                                                       id="password" name="password" required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" 
                                                        type="button" onclick="togglePassword('password')">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-text">
                                                A senha deve ter pelo menos 8 caracteres com: 1 maiúscula, 1 minúscula, 1 número e 1 caractere especial.
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="password_confirmation">Confirmar Nova Senha <span class="text-danger">*</span></label>
                                            <div class="position-relative auth-pass-inputgroup">
                                                <input type="password" class="form-control pe-5 password-input" 
                                                       placeholder="Confirme sua nova senha" 
                                                       id="password_confirmation" name="password_confirmation" required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" 
                                                        type="button" onclick="togglePassword('password_confirmation')">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <button class="btn btn-success w-100" type="submit">
                                                Redefinir Senha
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="mt-4 text-center">
                                    <p class="mb-0">Lembrou da senha? 
                                        <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline">Fazer Login</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                <script>document.write(new Date().getFullYear())</script> Marketplace. 
                                Desenvolvido com <i class="mdi mdi-heart text-danger"></i> pela equipe de desenvolvimento.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'ri-eye-off-fill align-middle';
            } else {
                field.type = 'password';
                icon.className = 'ri-eye-fill align-middle';
            }
        }
    </script>
</body>

</html>
