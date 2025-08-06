@extends('comerciantes.layout')

@section('title', 'Horários Padrão')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-calendar-week mr-2"></i>Horários Padrão</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.horarios.index', $empresaId) }}">Horários</a></li>
                            <li class="breadcrumb-item active">Padrão</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('comerciantes.horarios.padrao.create', $empresaId) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Horário
                    </a>
                    <a href="{{ route('comerciantes.horarios.excecoes.index', $empresaId) }}" class="btn btn-outline-warning">
                        <i class="fas fa-calendar-alt"></i> Exceções
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Atual -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <h6 class="text-primary">Status PDV Atual</h6>
                            <div class="d-flex align-items-center">
                                <i class="fas {{ $statusPDV['aberto'] ? 'fa-unlock text-success' : 'fa-lock text-danger' }} mr-2"></i>
                                <span class="{{ $statusPDV['aberto'] ? 'text-success' : 'text-danger' }}">
                                    {{ $statusPDV['mensagem'] }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="btn-group" role="group">
                                <a href="{{ route('comerciantes.horarios.padrao.index', $empresaId) }}" 
                                   class="btn btn-primary btn-sm active">
                                    <i class="fas fa-calendar-week"></i> Padrão
                                </a>
                                <a href="{{ route('comerciantes.horarios.excecoes.index', $empresaId) }}" 
                                   class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-calendar-alt"></i> Exceções
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('sucesso'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('sucesso') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('erro'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('erro') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Horários por Sistema -->
    @if($horarios->count() > 0)
        @foreach($sistemas as $sistema)
            @php
                $horariosDoSistema = $horarios->where('sistema', $sistema);
            @endphp
            
            @if($horariosDoSistema->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cog"></i> Sistema {{ $sistema }}
                            <span class="badge bg-primary ms-2">{{ $horariosDoSistema->count() }} horários</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Dia da Semana</th>
                                        <th>Horário</th>
                                        <th>Status</th>
                                        <th>Observações</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($horariosDoSistema as $horario)
                                        <tr>
                                            <td>
                                                <strong>{{ $diasSemana[$horario->dia_semana_id] ?? 'N/A' }}</strong>
                                            </td>
                                            <td>
                                                @if($horario->aberto)
                                                    <span class="text-success">
                                                        <i class="fas fa-clock"></i>
                                                        {{ $horario->hora_abertura }} às {{ $horario->hora_fechamento }}
                                                    </span>
                                                @else
                                                    <span class="text-danger">
                                                        <i class="fas fa-times"></i> Fechado
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($horario->aberto)
                                                    <span class="badge bg-success">Aberto</span>
                                                @else
                                                    <span class="badge bg-danger">Fechado</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $horario->observacoes ?? '-' }}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('comerciantes.horarios.padrao.edit', [$empresaId, $horario->id]) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmarExclusao({{ $horario->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @else
        <!-- Estado Vazio -->
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-week fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Nenhum Horário Padrão Cadastrado</h4>
                <p class="text-muted mb-4">
                    Configure os horários de funcionamento padrão para cada dia da semana e sistema.
                </p>
                <a href="{{ route('comerciantes.horarios.padrao.create', $empresaId) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Cadastrar Primeiro Horário
                </a>
            </div>
        </div>
    @endif

    <!-- Informações Adicionais -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informações</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><i class="fas fa-check text-success"></i> Os horários padrão se aplicam a todos os dias da semana especificados</li>
                        <li><i class="fas fa-check text-success"></i> Você pode criar exceções para datas específicas</li>
                        <li><i class="fas fa-check text-success"></i> Cada sistema pode ter horários diferentes</li>
                        <li><i class="fas fa-check text-success"></i> Use o status "Fechado" para marcar dias sem funcionamento</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Resumo</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">{{ $horarios->count() }}</h4>
                            <small class="text-muted">Total Horários</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $horarios->where('aberto', true)->count() }}</h4>
                            <small class="text-muted">Dias Abertos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    <i class="fas fa-exclamation-triangle"></i>
                    Tem certeza que deseja excluir este horário? Esta ação não poderá ser desfeita.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmarExclusao(id) {
    $('#deleteForm').attr('action', '/comerciantes/empresas/{{ $empresaId }}/horarios/' + id);
    $('#confirmDeleteModal').modal('show');
}
</script>
@endsection
