<!DOCTYPE html>
<html>
<head>
    <title>Teste Rota Buscar Produto</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Teste da Rota Buscar Produto</h1>
    
    <div>
        <label>Termo de busca:</label>
        <input type="text" id="searchTerm" value="ref" />
        <button onclick="buscarProdutos()">Buscar</button>
    </div>
    
    <div id="result" style="margin-top: 20px; padding: 10px; border: 1px solid #ccc;"></div>

    <script>
        function buscarProdutos() {
            const term = document.getElementById('searchTerm').value;
            const resultDiv = document.getElementById('result');
            
            resultDiv.innerHTML = 'Buscando...';
            
            // Primeira tentativa: com headers completos
            fetch('/comerciantes/produtos/kits/buscar-produto?term=' + encodeURIComponent(term), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                return response.text(); // Primeiro como texto para debug
            })
            .then(text => {
                console.log('Response text:', text);
                
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed JSON:', data);
                    
                    if (Array.isArray(data)) {
                        let html = `<h3>Produtos encontrados: ${data.length}</h3><ul>`;
                        data.forEach(produto => {
                            html += `<li>${produto.nome} (SKU: ${produto.sku}) - R$ ${produto.preco_venda}</li>`;
                        });
                        html += '</ul>';
                        resultDiv.innerHTML = html;
                    } else if (data.error) {
                        resultDiv.innerHTML = `<div style="color: red;">Erro: ${data.error}</div>`;
                    } else {
                        resultDiv.innerHTML = `<div>Resposta inesperada: ${JSON.stringify(data)}</div>`;
                    }
                } catch (e) {
                    resultDiv.innerHTML = `<div style="color: orange;">Resposta não é JSON válido: ${text}</div>`;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                resultDiv.innerHTML = `<div style="color: red;">Erro: ${error.message}</div>`;
            });
        }
        
        // Teste automático ao carregar
        window.onload = function() {
            console.log('Página carregada, testando busca automática...');
            buscarProdutos();
        };
    </script>
</body>
</html>
