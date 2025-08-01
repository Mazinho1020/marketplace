<!DOCTYPE html>
<html>
<head>
    <title>Teste Criar Config</title>
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h3>Teste - Nova Configuração</h3>
    
    <form action="{{ route('admin.config.store') }}" method="POST" class="mt-4">
        @csrf
        
        <div class="mb-3">
            <label>Nome:</label>
            <input type="text" name="nome" class="form-control" value="Teste Config" required>
        </div>
        
        <div class="mb-3">
            <label>Chave:</label>
            <input type="text" name="chave" class="form-control" value="teste_config_{{ time() }}" required>
        </div>
        
        <div class="mb-3">
            <label>Tipo:</label>
            <select name="tipo" class="form-control" required>
                <option value="string">Texto</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label>Grupo ID:</label>
            <input type="number" name="grupo_id" class="form-control" value="1" required>
        </div>
        
        <input type="hidden" name="visivel" value="1">
        <input type="hidden" name="editavel" value="1">
        <input type="hidden" name="obrigatorio" value="0">
        <input type="hidden" name="ordem" value="0">
        
        <button type="submit" class="btn btn-primary">Criar</button>
        <a href="/admin/config" class="btn btn-secondary">Voltar</a>
    </form>
    
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</body>
</html>
