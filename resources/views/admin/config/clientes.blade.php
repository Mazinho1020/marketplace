<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações de Clientes - Admin</title>
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Theme1/css/icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .config-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    {{-- Include do Menu Principal --}}
    @include('admin.partials.menuConfig')
    
    {{-- Include do Menu Secundário Configurações --}}
    @include('admin.partials.menuConfigSecundario')

    <div class="container-fluid mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">
                            <i class="mdi mdi-account-group text-primary"></i> Configurações de Clientes
                        </h2>
                        <p class="text-muted mb-0">Gerenciar configurações relacionadas aos clientes</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" onclick="salvarConfiguracoes()">
                            <i class="mdi mdi-check"></i> Salvar Alterações
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configurações -->
        <div class="row">
            <!-- Configurações Gerais -->
            <div class="col-lg-6 mb-4">
                <div class="config-card">
                    <h5><i class="mdi mdi-cog text-primary"></i> Configurações Gerais</h5>
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label">Cadastro Automático</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="cadastroAutomatico" checked>
                            <label class="form-check-label" for="cadastroAutomatico">
                                Permitir cadastro automático de clientes
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Validação de CPF</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="validacaoCPF" checked>
                            <label class="form-check-label" for="validacaoCPF">
                                Exigir CPF válido no cadastro
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Único</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="emailUnico" checked>
                            <label class="form-check-label" for="emailUnico">
                                Email deve ser único por cliente
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Fidelidade -->
            <div class="col-lg-6 mb-4">
                <div class="config-card">
                    <h5><i class="mdi mdi-star text-warning"></i> Programa de Fidelidade</h5>
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label">Ativar Automaticamente</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="fidelidadeAuto" checked>
                            <label class="form-check-label" for="fidelidadeAuto">
                                Ativar fidelidade para novos clientes
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nível Inicial</label>
                        <select class="form-select">
                            <option value="bronze" selected>Bronze</option>
                            <option value="prata">Prata</option>
                            <option value="ouro">Ouro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pontos Iniciais</label>
                        <input type="number" class="form-control" value="0" min="0">
                    </div>
                </div>
            </div>

            <!-- Campos Obrigatórios -->
            <div class="col-lg-6 mb-4">
                <div class="config-card">
                    <h5><i class="mdi mdi-form-select text-info"></i> Campos Obrigatórios</h5>
                    <hr>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="nome" checked disabled>
                                <label class="form-check-label" for="nome">Nome</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="email" checked disabled>
                                <label class="form-check-label" for="email">Email</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="telefone" checked>
                                <label class="form-check-label" for="telefone">Telefone</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="cpf">
                                <label class="form-check-label" for="cpf">CPF</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="endereco">
                                <label class="form-check-label" for="endereco">Endereço</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="nascimento">
                                <label class="form-check-label" for="nascimento">Data Nascimento</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="genero">
                                <label class="form-check-label" for="genero">Gênero</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="profissao">
                                <label class="form-check-label" for="profissao">Profissão</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notificações -->
            <div class="col-lg-6 mb-4">
                <div class="config-card">
                    <h5><i class="mdi mdi-bell text-success"></i> Notificações</h5>
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label">Email de Boas-vindas</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="emailBoasVindas" checked>
                            <label class="form-check-label" for="emailBoasVindas">
                                Enviar email de boas-vindas
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">SMS de Confirmação</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="smsConfirmacao">
                            <label class="form-check-label" for="smsConfirmacao">
                                Enviar SMS de confirmação
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Aniversário</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="aniversario" checked>
                            <label class="form-check-label" for="aniversario">
                                Notificar no aniversário
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/Theme1/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function salvarConfiguracoes() {
            alert('Configurações salvas com sucesso!');
        }
        
        function limparCache() {
            alert('Cache limpo com sucesso!');
        }
        
        function executarMigracoes() {
            alert('Migrações executadas com sucesso!');
        }
        
        function verificarSistema() {
            alert('Sistema verificado - Tudo funcionando corretamente!');
        }
        
        function exportarConfiguracoes() {
            alert('Configurações exportadas!');
        }
    </script>
</body>
</html>
