<!DOCTYPE html>
<html lang="pt-BR" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>Login | Marketplace Sistema</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema de Marketplace" name="description" />
    <meta content="Marketplace" name="author" />
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('Theme1/images/favicon.ico') }}">

    <!-- Layout config Js -->
    <script src="{{ asset('Theme1/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('Theme1/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('Theme1/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Custom Css-->
    <style>
        .auth-page-wrapper {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .auth-one-bg-position {
            position: relative;
        }
        .bg-overlay {
            background: rgba(0,0,0,0.3);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>

</head>

<body>

    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>
            
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <a href="{{ url('/') }}" class="d-inline-block auth-logo">
                                    <img src="{{ asset('Theme1/images/logo-light.png') }}" alt="" height="20">
                                </a>
                            </div>
                            <p class="mt-3 fs-15 fw-medium">Sistema de Marketplace Premium</p>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">
                            
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Bem-vindo de volta!</h5>
                                    <p class="text-muted">Faça login para continuar no Marketplace.</p>
                                </div>
                                
                                <!-- Exibir mensagens de erro ou sucesso -->
                                @if (session('status'))
                                    <div class="alert alert-success text-center">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success text-center">
                                        {{ session('success') }}
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
                                    <form method="POST" action="{{ route('login.post') }}">
                                        @csrf

                                        <div class="mb-3">
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

                                        <div class="mb-3">
                                            <div class="float-end">
                                                <a href="{{ route('password.request') }}" class="text-muted">Esqueceu a senha?</a>
                                            </div>
                                            <label class="form-label" for="password">Senha <span class="text-danger">*</span></label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" class="form-control pe-5 password-input @error('password') is-invalid @enderror" 
                                                       placeholder="Digite sua senha" 
                                                       id="password" name="password" required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" 
                                                        type="button" id="password-addon">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                                @error('password')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                                            <label class="form-check-label" for="remember">
                                                Lembrar-me
                                            </label>
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="submit">Entrar</button>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <div class="signin-other-title">
                                                <h5 class="fs-13 mb-4 title">Ou entre com</h5>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-primary btn-icon waves-effect waves-light">
                                                    <i class="ri-facebook-fill fs-16"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-icon waves-effect waves-light">
                                                    <i class="ri-google-fill fs-16"></i>
                                                </button>
                                                <button type="button" class="btn btn-dark btn-icon waves-effect waves-light">
                                                    <i class="ri-github-fill fs-16"></i>
                                                </button>
                                                <button type="button" class="btn btn-info btn-icon waves-effect waves-light">
                                                    <i class="ri-twitter-fill fs-16"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                        <div class="mt-4 text-center">
                            <p class="mb-0">Não tem uma conta? 
                                <a href="{{ route('register') }}" class="fw-semibold text-primary text-decoration-underline">Registre-se</a>
                            </p>
                        </div>

                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

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
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="{{ asset('Theme1/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('Theme1/js/plugins.js') }}"></script>
    <script src="{{ asset('Theme1/js/pages/password-addon.init.js') }}"></script>

    <script>
        // Auto-focus no primeiro campo com erro
        document.addEventListener('DOMContentLoaded', function() {
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.focus();
            }
        });
    </script>

</body>

</html>
