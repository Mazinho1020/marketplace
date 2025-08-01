<!DOCTYPE html>
<html lang="pt-BR" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>Registro | Marketplace Sistema</title>
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
                                    <h5 class="text-primary">Criar Nova Conta</h5>
                                    <p class="text-muted">Registre-se para acessar o Marketplace.</p>
                                </div>
                                
                                <!-- Exibir mensagens de erro -->
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
                                    <form method="POST" action="{{ route('register.post') }}" id="registerForm">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                                                   id="nome" name="nome" 
                                                   value="{{ old('nome') }}" 
                                                   placeholder="Digite seu nome completo" 
                                                   required autofocus>
                                            @error('nome')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" 
                                                   value="{{ old('email') }}" 
                                                   placeholder="Digite seu e-mail" 
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="telefone" class="form-label">Telefone</label>
                                                    <input type="tel" class="form-control @error('telefone') is-invalid @enderror" 
                                                           id="telefone" name="telefone" 
                                                           value="{{ old('telefone') }}" 
                                                           placeholder="(11) 99999-9999">
                                                    @error('telefone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="cpf_cnpj" class="form-label">CPF/CNPJ</label>
                                                    <input type="text" class="form-control @error('cpf_cnpj') is-invalid @enderror" 
                                                           id="cpf_cnpj" name="cpf_cnpj" 
                                                           value="{{ old('cpf_cnpj') }}" 
                                                           placeholder="000.000.000-00">
                                                    @error('cpf_cnpj')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="empresa_id" class="form-label">Empresa <span class="text-danger">*</span></label>
                                            <select class="form-select @error('empresa_id') is-invalid @enderror" 
                                                    id="empresa_id" name="empresa_id" required>
                                                <option value="">Selecione uma empresa</option>
                                                @foreach($empresas as $empresa)
                                                    <option value="{{ $empresa->id }}" 
                                                            {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
                                                        {{ $empresa->nome }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('empresa_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="tipo_usuario" class="form-label">Tipo de Usuário <span class="text-danger">*</span></label>
                                            <select class="form-select @error('tipo_usuario') is-invalid @enderror" 
                                                    id="tipo_usuario" name="tipo_usuario" required>
                                                <option value="">Selecione o tipo</option>
                                                @foreach($tiposUsuario as $tipo)
                                                    <option value="{{ $tipo->codigo }}" 
                                                            {{ old('tipo_usuario') == $tipo->codigo ? 'selected' : '' }}>
                                                        {{ $tipo->nome }} - {{ $tipo->descricao }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('tipo_usuario')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="password">Senha <span class="text-danger">*</span></label>
                                            <div class="position-relative auth-pass-inputgroup">
                                                <input type="password" class="form-control pe-5 password-input @error('password') is-invalid @enderror" 
                                                       placeholder="Digite sua senha" 
                                                       id="password" name="password" required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" 
                                                        type="button" id="password-addon">
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
                                            <label class="form-label" for="password_confirmation">Confirmar Senha <span class="text-danger">*</span></label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" class="form-control pe-5 password-input" 
                                                       placeholder="Confirme sua senha" 
                                                       id="password_confirmation" name="password_confirmation" required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" 
                                                        type="button">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <p class="mb-0 fs-12 text-muted fst-italic">
                                                Ao registrar-se, você aceita nossos 
                                                <a href="#" class="text-primary text-decoration-underline fst-normal fw-medium">Termos de Uso</a> 
                                                e 
                                                <a href="#" class="text-primary text-decoration-underline fst-normal fw-medium">Política de Privacidade</a>
                                            </p>
                                            
                                            <div class="form-check mt-2">
                                                <input class="form-check-input @error('aceito_termos') is-invalid @enderror" 
                                                       type="checkbox" value="1" id="aceito_termos" name="aceito_termos" required>
                                                <label class="form-check-label" for="aceito_termos">
                                                    Aceito os termos de uso
                                                </label>
                                                @error('aceito_termos')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="form-check">
                                                <input class="form-check-input @error('aceito_privacidade') is-invalid @enderror" 
                                                       type="checkbox" value="1" id="aceito_privacidade" name="aceito_privacidade" required>
                                                <label class="form-check-label" for="aceito_privacidade">
                                                    Aceito a política de privacidade
                                                </label>
                                                @error('aceito_privacidade')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="submit" id="submitBtn">
                                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                Criar Conta
                                            </button>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <div class="signin-other-title">
                                                <h5 class="fs-13 mb-4 title">Ou registre-se com</h5>
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
                            <p class="mb-0">Já tem uma conta? 
                                <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline">Faça login</a>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Máscaras para campos
            const telefoneInput = document.getElementById('telefone');
            const cpfCnpjInput = document.getElementById('cpf_cnpj');
            
            // Máscara de telefone
            if (telefoneInput) {
                telefoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length <= 11) {
                        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                    }
                    e.target.value = value;
                });
            }
            
            // Máscara de CPF/CNPJ
            if (cpfCnpjInput) {
                cpfCnpjInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length <= 11) {
                        // CPF
                        value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                    } else {
                        // CNPJ
                        value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
                    }
                    e.target.value = value;
                });
            }
            
            // Loading no submit
            const form = document.getElementById('registerForm');
            const submitBtn = document.getElementById('submitBtn');
            
            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Criando conta...';
                });
            }
        });
    </script>

</body>

</html>
