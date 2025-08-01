<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sem Acesso | Marketplace</title>
    <link href="{{ asset('Theme1/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('Theme1/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('Theme1/css/app.min.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="ri-lock-line text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="card-title">Acesso Não Autorizado</h4>
                        <p class="card-text text-muted">
                            Sua conta não possui um tipo de acesso válido definido.
                            Entre em contato com o administrador do sistema.
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               class="btn btn-primary">
                                Fazer Logout
                            </a>
                        </div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
