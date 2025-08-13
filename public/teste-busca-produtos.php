<?php
// Simular ambiente Laravel básico para teste
$csrfToken = 'teste-token-' . time();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrfToken; ?>">
    <title>Teste Busca de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <h1>🔍 Teste Busca de Produtos - Debug</h1>

        <div class="alert alert-info">
            <strong>⚠️ Importante:</strong> Este teste requer que você esteja logado no sistema comerciante.
            <br>Se não estiver logado, receberá erro 401/403.
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Busca Manual (AJAX)</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="termoBusca" class="form-label">Digite o termo de busca:</label>
                            <input type="text" class="form-control" id="termoBusca" placeholder="Nome ou SKU do produto">
                            <small class="text-muted">Mínimo 2 caracteres</small>
                        </div>
                        <button type="button" class="btn btn-primary" id="btnBuscar">🔍 Buscar</button>
                        <div id="resultados" class="mt-3"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Log de Debug</h5>
                    </div>
                    <div class="card-body">
                        <div id="debugLog" class="border p-3 bg-dark text-light" style="height: 300px; overflow-y: auto; font-family: monospace;">
                            <div class="text-success">[INFO] Sistema iniciado</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>🔗 Links Úteis</h5>
                    </div>
                    <div class="card-body">
                        <a href="http://192.168.1.127/marketplace/public/comerciantes/login" class="btn btn-success me-2">
                            🔐 Fazer Login
                        </a>
                        <a href="http://192.168.1.127/marketplace/public/comerciantes/produtos/8/relacionados" class="btn btn-primary me-2">
                            📦 Ir para Produtos Relacionados
                        </a>
                        <a href="http://192.168.1.127/marketplace/public/comerciantes/produtos" class="btn btn-info">
                            📋 Lista de Produtos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const debugLog = document.getElementById('debugLog');

        function log(message, type = 'INFO') {
            const timestamp = new Date().toLocaleTimeString();
            const colorClass = {
                'INFO': 'text-info',
                'SUCCESS': 'text-success',
                'WARNING': 'text-warning',
                'ERROR': 'text-danger'
            } [type] || 'text-light';

            const div = document.createElement('div');
            div.className = colorClass;
            div.textContent = `[${timestamp}] [${type}] ${message}`;
            debugLog.appendChild(div);
            debugLog.scrollTop = debugLog.scrollHeight;
        }

        $(document).ready(function() {
            log('jQuery carregado');
            log('CSRF Token: ' + $('meta[name="csrf-token"]').attr('content'));

            // URL da busca
            const produtoId = 8;
            const buscarUrl = `http://192.168.1.127/marketplace/public/comerciantes/produtos/${produtoId}/relacionados/buscar`;
            log('URL de busca: ' + buscarUrl);

            $('#btnBuscar').click(function() {
                const termo = $('#termoBusca').val().trim();

                if (termo.length < 2) {
                    log('Termo muito curto (mínimo 2 caracteres)', 'WARNING');
                    $('#resultados').html('<div class="alert alert-warning">Digite pelo menos 2 caracteres</div>');
                    return;
                }

                log('Iniciando busca para: "' + termo + '"');
                $('#resultados').html('<div class="text-center"><div class="spinner-border" role="status"></div> Buscando...</div>');

                $.ajax({
                    url: buscarUrl,
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: {
                        q: termo
                    },
                    success: function(response) {
                        log('✅ Busca bem-sucedida', 'SUCCESS');
                        log('Resposta: ' + JSON.stringify(response));

                        if (response.results && response.results.length > 0) {
                            let html = `<div class="alert alert-success">Encontrados ${response.results.length} produto(s)</div>`;
                            html += '<div class="list-group">';
                            response.results.forEach(function(produto) {
                                html += `<div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">ID: ${produto.id}</h6>
                                    </div>
                                    <p class="mb-1">${produto.text}</p>
                                </div>`;
                            });
                            html += '</div>';
                            $('#resultados').html(html);
                        } else {
                            $('#resultados').html('<div class="alert alert-info">Nenhum produto encontrado para "' + termo + '"</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        log('❌ Erro na busca: ' + xhr.status + ' - ' + xhr.statusText, 'ERROR');
                        log('Detalhes do erro: ' + error, 'ERROR');
                        log('Resposta do servidor: ' + xhr.responseText, 'ERROR');

                        let errorMsg = 'Erro desconhecido';
                        let errorClass = 'alert-danger';

                        switch (xhr.status) {
                            case 401:
                                errorMsg = 'Não autorizado - Faça login primeiro';
                                errorClass = 'alert-warning';
                                break;
                            case 403:
                                errorMsg = 'Acesso negado - Sem permissão';
                                break;
                            case 404:
                                errorMsg = 'Rota não encontrada - Verifique a URL';
                                break;
                            case 500:
                                errorMsg = 'Erro interno do servidor';
                                break;
                            default:
                                errorMsg = `Erro ${xhr.status}: ${xhr.statusText}`;
                        }

                        $('#resultados').html(`
                            <div class="alert ${errorClass}">
                                <strong>${errorMsg}</strong><br>
                                <small>Status: ${xhr.status} | Erro: ${error}</small>
                            </div>
                        `);
                    }
                });
            });

            // Buscar ao pressionar Enter
            $('#termoBusca').keypress(function(e) {
                if (e.which === 13) {
                    $('#btnBuscar').click();
                }
            });
        });
    </script>
</body>

</html>