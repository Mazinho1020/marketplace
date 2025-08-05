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
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.horarios.index') }}">Horários</a></li>
                            <li class="breadcrumb-item active">Padrão</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('comerciantes.horarios.padrao.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Horário
                    </a>
                    <a href="{{ route('comerciantes.horarios.excecoes') }}" class="btn btn-outline-warning">
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
                                <a href="{{ route('comerciantes.horarios.padrao') }}" 
                                   class="btn btn-sm btn-outline-primary {{ !$sistema ? 'active' : '' }}">
                                    Todos
                                </a>
                                @foreach($sistemas as $sist)
                                <a href="{{ route('comerciantes.horarios.padrao', ['sistema' => $sist]) }}" 
                                   class="btn btn-sm btn-outline-primary {{ $sistema === $sist ? 'active' : '' }}">
                                    {{ $sist }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Horários -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-clock"></i> Horários Cadastrados
                @if($sistema)
                    - Sistema: {{ $sistema }}
                @endif
            </h5>
        </div>
        <div class="card-body">
            @if($horarios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Sistema</th>
                                <th>Dia da Semana</th>
                                <th>Status</th>
                                <th>Horário</th>
                                <th>Duração</th>
                                <th>Observações</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($horarios as $horario)
                                <tr>
                                    <td>
                                        <span class="badge badge-{{ $horario->sistema === 'TODOS' ? 'primary' : 'secondary' }}">
                                            {{ $horario->sistema }}
                                        </span>
                                    </td>
                                    <td>{{ $horario->diaSemana->nome }}</td>
                                    <td>
                                        @if($horario->aberto)
                                            <span class="badge badge-success">
                                                <i class="fas fa-unlock"></i> Aberto
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-lock"></i> Fechado
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($horario->aberto)
                                            {{ \Carbon\Carbon::parse($horario->hora_abertura)->format('H:i') }} às 
                                            {{ \Carbon\Carbon::parse($horario->hora_fechamento)->format('H:i') }}
                                        @else
                                            <span class="text-muted">Fechado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($horario->aberto)
                                            @php
                                                $abertura = \Carbon\Carbon::parse($horario->hora_abertura);
                                                $fechamento = \Carbon\Carbon::parse($horario->hora_fechamento);
                                                $duracao = $abertura->diff($fechamento);
                                            @endphp
                                            <small class="text-muted">
                                                {{ $duracao->format('%H:%I') }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($horario->observacoes)
                                            <small class="text-muted" title="{{ $horario->observacoes }}">
                                                {{ Str::limit($horario->observacoes, 30) }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('comerciantes.horarios.padrao.edit', $horario->id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmarExclusao({{ $horario->id }})"
                                                    title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Resumo -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Total de {{ $horarios->count() }} horário(s) cadastrado(s)
                        </small>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum horário cadastrado</h5>
                    <p class="text-muted">Comece criando seu primeiro horário de funcionamento.</p>
                    <a href="{{ route('comerciantes.horarios.padrao.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Criar Primeiro Horário
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este horário?</p>
                <p class="text-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Esta ação não poderá ser desfeita.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
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
    $('#deleteForm').attr('action', '/comerciantes/horarios/' + id);
    $('#confirmDeleteModal').modal('show');
}
</script>
@endsection
