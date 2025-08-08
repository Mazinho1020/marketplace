<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo de Permissões por Plano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">
                    <i class="fas fa-shield-alt text-primary"></i>
                    Sistema de Permissões por Plano
                </h1>
                
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> Como Funciona</h5>
                    <p>Este sistema controla o acesso às funcionalidades baseado no plano do usuário:</p>
                    <ul>
                        <li><strong>Plano Básico:</strong> Funcionalidades essenciais</li>
                        <li><strong>Plano Profissional:</strong> Funcionalidades avançadas + básicas</li>
                        <li><strong>Plano Enterprise:</strong> Todas as funcionalidades</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Funcionalidades Básicas -->
            <div class="col-md-4">
                <div class="card border-success mb-4">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-check-circle"></i> Funcionalidades Básicas</h5>
                        <small>Todos os planos</small>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/comerciantes/relatorios/vendas" class="btn btn-outline-success">
                                <i class="fas fa-chart-line"></i> Relatórios de Vendas
                            </a>
                            <a href="/comerciantes/relatorios/clientes" class="btn btn-outline-success">
                                <i class="fas fa-users"></i> Relatórios de Clientes
                            </a>
                            <a href="/comerciantes/marcas" class="btn btn-outline-success">
                                <i class="fas fa-tags"></i> Gestão de Marcas
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Funcionalidades Profissionais -->
            <div class="col-md-4">
                <div class="card border-warning mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5><i class="fas fa-star"></i> Funcionalidades Profissionais</h5>
                        <small>Planos Profissional e Enterprise</small>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/comerciantes/relatorios/analytics" class="btn btn-outline-warning">
                                <i class="fas fa-chart-pie"></i> Analytics Avançado
                            </a>
                            <a href="/comerciantes/api/tokens" class="btn btn-outline-warning">
                                <i class="fas fa-key"></i> Acesso à API
                            </a>
                            <a href="/comerciantes/bulk/importar" class="btn btn-outline-warning">
                                <i class="fas fa-upload"></i> Operações em Lote
                            </a>
                            <a href="/comerciantes/empresas" class="btn btn-outline-warning">
                                <i class="fas fa-building"></i> Gestão de Empresas
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Funcionalidades Enterprise -->
            <div class="col-md-4">
                <div class="card border-danger mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5><i class="fas fa-crown"></i> Funcionalidades Enterprise</h5>
                        <small>Apenas Plano Enterprise</small>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/comerciantes/auditoria" class="btn btn-outline-danger">
                                <i class="fas fa-search"></i> Auditoria e Logs
                            </a>
                            <a href="/comerciantes/campos-personalizados" class="btn btn-outline-danger">
                                <i class="fas fa-cogs"></i> Campos Personalizados
                            </a>
                            <a href="/comerciantes/permissoes/gerenciar" class="btn btn-outline-danger">
                                <i class="fas fa-user-shield"></i> Permissões Avançadas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-secondary">
                    <h5><i class="fas fa-code"></i> Como Implementar</h5>
                    <p>Para proteger uma rota, use o middleware <code>plan:</code> seguido da funcionalidade:</p>
                    <pre><code>Route::get('/rota', [Controller::class, 'method'])->middleware('plan:advanced_reports');</code></pre>
                    
                    <p class="mt-3">Para grupos de rotas:</p>
                    <pre><code>Route::middleware('plan:api_access')->group(function () {
    Route::get('/api/data', [ApiController::class, 'data']);
    Route::post('/api/create', [ApiController::class, 'create']);
});</code></pre>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 text-center">
                <a href="/comerciantes/planos" class="btn btn-primary btn-lg">
                    <i class="fas fa-diamond"></i> Gerenciar Planos
                </a>
                <a href="/comerciantes/clientes/dashboard" class="btn btn-secondary btn-lg ms-3">
                    <i class="fas fa-home"></i> Voltar ao Dashboard
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
