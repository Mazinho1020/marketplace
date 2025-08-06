<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Horários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Sistema de Horários - Teste</h1>
        <div class="alert alert-success">
            <h4>Dados recebidos:</h4>
            <ul>
                <li><strong>Empresa ID:</strong> {{ $empresaId }}</li>
                <li><strong>Empresa Nome:</strong> {{ $empresa->nome_fantasia ?? $empresa->razao_social ?? 'N/A' }}</li>
                <li><strong>Horários Padrão:</strong> {{ $horariosPadrao->count() }}</li>
                <li><strong>Exceções:</strong> {{ $proximasExcecoes->count() }}</li>
                <li><strong>Sistemas:</strong> {{ implode(', ', $sistemas) }}</li>
            </ul>
        </div>
        
        <div class="alert alert-info">
            <h5>Sistema funcionando!</h5>
            <p>Se você vê esta página, o controller está funcionando corretamente.</p>
        </div>
        
        <a href="{{ route('comerciantes.empresas.index') }}" class="btn btn-secondary">
            Voltar para Empresas
        </a>
    </div>
</body>
</html>
