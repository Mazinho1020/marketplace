@php
    // Função helper para obter o nome do usuário de forma segura
    function getNomeUsuario($usuario) {
        if (!$usuario) return 'Nome não disponível';
        
        // Tentar diferentes campos possíveis baseado na estrutura real da tabela
        $campos = ['username', 'name', 'email', 'nome', 'first_name', 'nome_completo'];
        
        foreach ($campos as $campo) {
            if (isset($usuario->$campo) && !empty($usuario->$campo)) {
                return $usuario->$campo;
            }
        }
        
        return 'Nome não disponível';
    }
    
    // Função helper para obter a inicial do usuário
    function getInicialUsuario($usuario) {
        $nome = getNomeUsuario($usuario);
        return strtoupper(substr($nome, 0, 1));
    }
@endphp

@extends('comerciantes.layouts.app')

@section('title', 'Gerenciar Usuários - ' . ($empresa->nome_fantasia ?? 'Empresa'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('comerciantes.empresas.index') }}">Empresas</a></li>
            <li class="breadcrumb-item active">Usuários - {{ $empresa->nome_fantasia ?? 'Empresa' }}</li>
        </ol>
    </nav>

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gerenciar Usuários</h1>
            <p class="text-muted mb-0">{{ $empresa->nome_fantasia ?? 'Empresa não identificada' }}</p>
        </div>
        <a href="{{ route('comerciantes.empresas.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Voltar
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1">{{ $empresa->usuariosVinculados ? $empresa->usuariosVinculados->count() : 0 }}</h4>
                    <small class="text-muted">Total de Usuários</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Usuários -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">
                <i class="fas fa-users me-2"></i>
                Usuários Vinculados ({{ $empresa->usuariosVinculados ? $empresa->usuariosVinculados->count() : 0 }})
            </h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdicionarUsuario">
                    <i class="fas fa-user-plus me-1"></i>
                    Vincular Usuário
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCriarUsuario">
                    <i class="fas fa-plus me-1"></i>
                    Criar Novo
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            {{-- DEBUG: Mostrar informações quando solicitado --}}
            @if(request()->has('debug'))
                <div class="alert alert-info m-3">
                    <h6>DEBUG INFO:</h6>
                    <p><strong>Empresa ID:</strong> {{ $empresa->id ?? 'N/A' }}</p>
                    <p><strong>Nome:</strong> {{ $empresa->nome_fantasia ?? 'N/A' }}</p>
                    <p><strong>usuariosVinculados definido:</strong> {{ isset($empresa->usuariosVinculados) ? 'SIM' : 'NÃO' }}</p>
                    @if(isset($empresa->usuariosVinculados))
                        <p><strong>Tipo:</strong> {{ get_class($empresa->usuariosVinculados) }}</p>
                        <p><strong>Count:</strong> {{ $empresa->usuariosVinculados->count() }}</p>
                        @if($empresa->usuariosVinculados->count() > 0)
                            <p><strong>Primeiro usuário (raw):</strong> <pre>{{ print_r($empresa->usuariosVinculados->first()->toArray(), true) }}</pre></p>
                        @endif
                    @endif
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Usuário</th>
                            <th>Perfil</th>
                            <th>Status</th>
                            <th>Data Vínculo</th>
                            <th width="120" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($empresa->usuariosVinculados) && is_object($empresa->usuariosVinculados) && $empresa->usuariosVinculados->count() > 0)
                            @foreach($empresa->usuariosVinculados as $vinculo)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-3">
                                            {{ getInicialUsuario($vinculo) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ getNomeUsuario($vinculo) }}</div>
                                            <small class="text-muted">{{ $vinculo->email ?? 'Email não disponível' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if(isset($vinculo->pivot) && isset($vinculo->pivot->perfil))
                                        <span class="badge bg-{{ $vinculo->pivot->perfil === 'proprietario' ? 'danger' : ($vinculo->pivot->perfil === 'administrador' ? 'warning' : 'info') }}">
                                            {{ ucfirst($vinculo->pivot->perfil) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Indefinido</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($vinculo->pivot) && isset($vinculo->pivot->status))
                                        <span class="badge bg-{{ $vinculo->pivot->status === 'ativo' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($vinculo->pivot->status) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Indefinido</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($vinculo->pivot) && isset($vinculo->pivot->data_vinculo))
                                        <small>{{ \Carbon\Carbon::parse($vinculo->pivot->data_vinculo)->format('d/m/Y H:i') }}</small>
                                    @else
                                        <small class="text-muted">Data não disponível</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!isset($vinculo->pivot) || $vinculo->pivot->perfil !== 'proprietario')
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEditarUsuario"
                                                data-user-id="{{ $vinculo->id ?? '' }}"
                                                data-user-nome="{{ getNomeUsuario($vinculo) }}"
                                                data-user-perfil="{{ $vinculo->pivot->perfil ?? '' }}"
                                                data-user-status="{{ $vinculo->pivot->status ?? '' }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm"
                                                onclick="confirmarRemocao({{ $vinculo->id ?? 0 }}, '{{ addslashes(getNomeUsuario($vinculo)) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @else
                                    <small class="text-muted">Proprietário</small>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum usuário vinculado encontrado</h5>
                                <p class="text-muted mb-3">Clique em "Criar Novo" ou "Vincular Usuário" para adicionar usuários</p>
                                @if(request()->has('debug'))
                                    <div class="alert alert-warning mt-3">
                                        <strong>Debug Info:</strong><br>
                                        usuariosVinculados: {{ isset($empresa->usuariosVinculados) ? 'Definido' : 'Não definido' }}<br>
                                        Tipo: {{ isset($empresa->usuariosVinculados) ? get_class($empresa->usuariosVinculados) : 'N/A' }}<br>
                                        Count: {{ isset($empresa->usuariosVinculados) ? $empresa->usuariosVinculados->count() : 'N/A' }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Resto do arquivo permanece igual... --}}
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
}

.card-hover {
    transition: transform 0.2s;
}

.card-hover:hover {
    transform: translateY(-2px);
}
</style>

<script>
function confirmarRemocao(userId, userName) {
    if (confirm(`Tem certeza que deseja remover o usuário "${userName}" desta empresa?`)) {
        // Aqui você implementaria a lógica de remoção
        console.log('Removendo usuário:', userId);
    }
}
</script>
@endsection
