<!DOCTYPE html>
<html>
<head>
    <title>Teste Sistema de Pagamentos</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Teste do Sistema de Pagamentos</h1>
        
        <div class="card">
            <div class="card-body">
                <h5>Teste da API de Formas de Pagamento</h5>
                <button class="btn btn-primary" onclick="testarFormasPagamento()">Testar Formas de Pagamento</button>
                <div id="resultado-formas" class="mt-3"></div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-body">
                <h5>Teste de Carregamento do Select</h5>
                <select class="form-select" id="forma_pagamento_test">
                    <option value="">Carregando...</option>
                </select>
                <button class="btn btn-secondary mt-2" onclick="carregarSelect()">Carregar Select</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const empresaId = 1; // Empresa de teste
        
        function testarFormasPagamento() {
            const url = `/comerciantes/empresas/${empresaId}/financeiro/api/formas-pagamento-saida`;
            const resultado = document.getElementById('resultado-formas');
            
            console.log('🔍 Testando URL:', url);
            resultado.innerHTML = '<div class="alert alert-info">Carregando...</div>';
            
            fetch(url)
                .then(response => {
                    console.log('📡 Response status:', response.status);
                    console.log('📡 Response:', response);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Dados recebidos:', data);
                    
                    if (Array.isArray(data) && data.length > 0) {
                        let html = '<div class="alert alert-success">✅ API funcionando!</div>';
                        html += '<h6>Formas encontradas:</h6><ul class="list-group">';
                        
                        data.forEach(forma => {
                            html += `<li class="list-group-item">ID: ${forma.id} - ${forma.nome} (${forma.tipo})</li>`;
                        });
                        html += '</ul>';
                        resultado.innerHTML = html;
                    } else {
                        resultado.innerHTML = '<div class="alert alert-warning">⚠️ API retornou dados vazios</div>';
                    }
                })
                .catch(error => {
                    console.error('❌ Erro:', error);
                    resultado.innerHTML = `<div class="alert alert-danger">❌ Erro: ${error.message}</div>`;
                });
        }
        
        function carregarSelect() {
            const select = document.getElementById('forma_pagamento_test');
            const url = `/comerciantes/empresas/${empresaId}/financeiro/api/formas-pagamento-saida`;
            
            select.innerHTML = '<option value="">Carregando...</option>';
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Carregando select com dados:', data);
                    select.innerHTML = '<option value="">Selecione...</option>';
                    
                    data.forEach(forma => {
                        const option = document.createElement('option');
                        option.value = forma.id;
                        option.textContent = forma.nome;
                        option.dataset.gateway = forma.gateway_method || '';
                        select.appendChild(option);
                    });
                    
                    console.log('✅ Select carregado com', data.length, 'opções');
                })
                .catch(error => {
                    console.error('❌ Erro ao carregar select:', error);
                    select.innerHTML = '<option value="">Erro ao carregar</option>';
                });
        }
        
        // Testar automaticamente
        window.addEventListener('load', function() {
            console.log('🚀 Página carregada, iniciando testes...');
            testarFormasPagamento();
            setTimeout(carregarSelect, 1000);
        });
    </script>
</body>
</html>
