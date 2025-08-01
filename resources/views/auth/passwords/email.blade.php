<!DOCTYPE html>
<html lang="pt-BR" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg">

<head>
    <meta charset="utf-8" />
    <title>Recuperar Senha | Marketplace Sistema</title>
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
                            <p class="mt-3 fs-15 fw-medium text-muted">Recuperação de Senha</p>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Esqueceu sua senha?</h5>
                                    <p class="text-muted">Digite seu e-mail e enviaremos um link para redefinir sua senha.</p>
                                </div>

                                @if (session('status'))
                                    <div class="alert alert-success text-center">
                                        {{ session('status') }}
                                    </div>
                                @endif

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
                                    <form method="POST" action="{{ route('password.email') }}">
                                        @csrf

                                        <div class="mb-4">
                                            <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" 
                                                   value="{{ old('email') }}" 
                                                   placeholder="Digite seu e-mail" 
                                                   required autofocus>
                                            @error('email')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="text-center">
                                            <button class="btn btn-success w-100" type="submit">
                                                Enviar Link de Recuperação
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
</body>

</html>
