@extends('comerciantes.layouts.app')

@section('title', $empresa->nome)

@section('content')
<div class="container-fluid">
    <!-- Header da página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building me-2"></i>
                {{ $empresa->nome }}
            </h1>
            <p class="text-muted mb-0">
                @if($empresa->marca)
                    <i class="fas fa-tag me-1"></i>
                    {{ $empresa->marca->nome }}
                @endif
                <span class="badge bg-{{ $empresa->status == 'ativa' ? 'success' : ($empresa->status == 'inativa' ? 'secondary' : 'warning') }} ms-2">
                    {{ ucfirst($empresa->status) }}
                </span>
            </p>
        </div>
        <div class="btn-group">
            <a href="{{ route('comerciantes.empresas.edit', $empresa) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                Editar
            </a>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" 
                    data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="{{ route('comerciantes.dashboard.empresa', $empresa) }}">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('comerciantes.empresas.usuarios.index', $empresa) }}">
                        <i class="fas fa-users me-2"></i>
                        Gerenciar Usuários
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="#" onclick="confirmarExclusao()">
                        <i class="fas fa-trash me-2"></i>
                        Excluir Empresa
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <!-- Coluna principal -->
        <div class="col-lg-8">
            <!-- Informações gerais -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações Gerais
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-gray-700 mb-2">Nome da Empresa</h6>
                            <p class="mb-3">{{ $empresa->nome }}</p>

                            @if($empresa->nome_fantasia)
                                <h6 class="text-gray-700 mb-2">Nome Fantasia</h6>
                                <p class="mb-3">{{ $empresa->nome_fantasia }}</p>
                            @endif

                            @if($empresa->cnpj)
                                <h6 class="text-gray-700 mb-2">CNPJ</h6>
                                <p class="mb-3">{{ $empresa->cnpj }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($empresa->marca)
                                <h6 class="text-gray-700 mb-2">Marca</h6>
                                <p class="mb-3">
                                    <a href="{{ route('comerciantes.marcas.show', $empresa->marca) }}" 
                                       class="text-decoration-none">
                                        {{ $empresa->marca->nome }}
                                    </a>
                                </p>
                            @endif

                            <h6 class="text-gray-700 mb-2">Status</h6>
                            <p class="mb-3">
                                <span class="badge bg-{{ $empresa->status == 'ativa' ? 'success' : ($empresa->status == 'inativa' ? 'secondary' : 'warning') }}">
                                    {{ ucfirst($empresa->status) }}
                                </span>
                            </p>

                            <h6 class="text-gray-700 mb-2">Criada em</h6>
                            <p class="mb-3">{{ $empresa->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Endereço -->
            @if($empresa->endereco_logradouro || $empresa->endereco_cidade)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Endereço
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                @php
                                    $endereco = collect([
                                        $empresa->endereco_logradouro,
                                        $empresa->endereco_numero,
                                        $empresa->endereco_complemento
                                    ])->filter()->implode(', ');
                                    
                                    $localidade = collect([
                                        $empresa->endereco_bairro,
                                        $empresa->endereco_cidade,
                                        $empresa->endereco_estado
                                    ])->filter()->implode(', ');
                                @endphp

                                @if($endereco)
                                    <p class="mb-2">
                                        <strong>Logradouro:</strong> {{ $endereco }}
                                    </p>
                                @endif

                                @if($localidade)
                                    <p class="mb-2">
                                        <strong>Localidade:</strong> {{ $localidade }}
                                    </p>
                                @endif

                                @if($empresa->endereco_cep)
                                    <p class="mb-2">
                                        <strong>CEP:</strong> {{ $empresa->endereco_cep }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Contato -->
            @if($empresa->telefone || $empresa->email || $empresa->website)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-phone me-2"></i>
                            Informações de Contato
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($empresa->telefone)
                                <div class="col-md-4">
                                    <h6 class="text-gray-700 mb-2">Telefone</h6>
                                    <p class="mb-3">
                                        <a href="tel:{{ $empresa->telefone }}" class="text-decoration-none">
                                            {{ $empresa->telefone }}
                                        </a>
                                    </p>
                                </div>
                            @endif

                            @if($empresa->email)
                                <div class="col-md-4">
                                    <h6 class="text-gray-700 mb-2">Email</h6>
                                    <p class="mb-3">
                                        <a href="mailto:{{ $empresa->email }}" class="text-decoration-none">
                                            {{ $empresa->email }}
                                        </a>
                                    </p>
                                </div>
                            @endif

                            @if($empresa->website)
                                <div class="col-md-4">
                                    <h6 class="text-gray-700 mb-2">Website</h6>
                                    <p class="mb-3">
                                        <a href="{{ $empresa->website }}" target="_blank" class="text-decoration-none">
                                            {{ $empresa->website }}
                                            <i class="fas fa-external-link-alt ms-1"></i>
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Horário de funcionamento -->
            @if($empresa->horario_funcionamento)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-clock me-2"></i>
                            Horário de Funcionamento
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $horarios = $empresa->horario_funcionamento;
                            $diasSemana = [
                                'segunda' => 'Segunda-feira',
                                'terca' => 'Terça-feira',
                                'quarta' => 'Quarta-feira',
                                'quinta' => 'Quinta-feira',
                                'sexta' => 'Sexta-feira',
                                'sabado' => 'Sábado',
                                'domingo' => 'Domingo'
                            ];
                        @endphp

                        <div class="row">
                            @foreach($diasSemana as $dia => $nome)
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <strong>{{ $nome }}:</strong>
                                    @if(isset($horarios[$dia]) && $horarios[$dia]['abertura'] && $horarios[$dia]['fechamento'])
                                        <span class="text-success">
                                            {{ $horarios[$dia]['abertura'] }} às {{ $horarios[$dia]['fechamento'] }}
                                        </span>
                                    @else
                                        <span class="text-muted">Fechado</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Estatísticas rápidas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>
                        Estatísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="stat-item">
                                <div class="stat-number text-primary">{{ $empresa->usuarios_vinculados_count ?? 0 }}</div>
                                <div class="stat-label">Usuários</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-item">
                                <div class="stat-number text-success">{{ $empresa->produtos_count ?? 0 }}</div>
                                <div class="stat-label">Produtos</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <div class="stat-number text-info">{{ $empresa->pedidos_count ?? 0 }}</div>
                                <div class="stat-label">Pedidos</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <div class="stat-number text-warning">{{ $empresa->avaliacoes_count ?? 0 }}</div>
                                <div class="stat-label">Avaliações</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações rápidas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>
                        Ações Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('comerciantes.dashboard.empresa', $empresa) }}" class="btn btn-outline-primary">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Ver Dashboard
                        </a>
                        <a href="{{ route('comerciantes.empresas.usuarios.index', $empresa) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-users me-2"></i>
                            Gerenciar Usuários
                        </a>
                        <hr>
                        <a href="{{ route('comerciantes.empresas.edit', $empresa) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>
                            Editar Empresa
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informações do proprietário -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>
                        Proprietário
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            @if($empresa->proprietario->avatar)
                                <img src="{{ $empresa->proprietario->avatar }}" alt="Avatar" 
                                     class="rounded-circle" width="50" height="50">
                            @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    {{ substr($empresa->proprietario->nome, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $empresa->proprietario->nome }}</h6>
                            <p class="text-muted mb-0 small">{{ $empresa->proprietario->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botão voltar -->
    <div class="row mt-4">
        <div class="col-12">
            <a href="{{ route('comerciantes.empresas.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar para Lista
            </a>
        </div>
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="modalExclusao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a empresa <strong>{{ $empresa->nome }}</strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta ação não pode ser desfeita!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="{{ route('comerciantes.empresas.destroy', $empresa) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.stat-item {
    padding: 0.5rem 0;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 600;
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--bs-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.avatar {
    flex-shrink: 0;
}
</style>
@endpush

@push('scripts')
<script>
function confirmarExclusao() {
    const modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
    modal.show();
}
</script>
@endpush
@endsection
