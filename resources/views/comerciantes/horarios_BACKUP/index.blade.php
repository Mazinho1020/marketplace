@extends('comerciantes.layout')

@section('title', 'Horários de Funcionamento')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-clock mr-2"></i>Horários de Funcionamento</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Horários</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('comerciantes.horarios.padrao.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Horário
                    </a>
                    <a href="{{ route('comerciantes.horarios.excecoes.create') }}" class="btn btn-outline-warning">
                        <i class="fas fa-calendar-alt"></i> Nova Exceção
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Atual dos Sistemas -->
    <div class="row mb-4">
        @foreach($relatorioStatus as $sistema => $dados)
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-{{ $dados['status_hoje']['aberto'] ? 'success' : 'danger' }}">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                {{ $sistema }}
                            </div>
                            <div class="h6 mb-0 font-weight-bold {{ $dados['status_hoje']['aberto'] ? 'text-success' : 'text-danger' }}">
                                <i class="fas {{ $dados['status_hoje']['aberto'] ? 'fa-unlock' : 'fa-lock' }} mr-1"></i>
                                {{ $dados['status_hoje']['aberto'] ? 'Aberto' : 'Fechado' }}
                            </div>
                            <small class="text-muted">{{ $dados['status_hoje']['mensagem'] }}</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-{{ $sistema === 'PDV' ? 'cash-register' : ($sistema === 'ONLINE' ? 'globe' : ($sistema === 'FINANCEIRO' ? 'chart-line' : 'building')) }} fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    @if($dados['proximo_funcionamento'])
                    <small class="text-info">
                        <i class="fas fa-clock"></i> Próximo: {{ $dados['proximo_funcionamento']['mensagem'] }}
                    </small>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="form-inline">
                        <label class="mr-2">Filtrar por sistema:</label>
                        <div class="btn-group mr-3" role="group">
                            <a href="{{ route('comerciantes.horarios.index') }}" 
                               class="btn btn-sm btn-outline-primary {{ !$sistema ? 'active' : '' }}">
                                Todos
                            </a>
                            @foreach($sistemas as $sist)
                            <a href="{{ route('comerciantes.horarios.index', ['sistema' => $sist]) }}" 
                               class="btn btn-sm btn-outline-primary {{ $sistema === $sist ? 'active' : '' }}">
                                {{ $sist }}
                            </a>
                            @endforeach
                        </div>
                        
                        <div class="btn-group" role="group">
                            <a href="{{ route('comerciantes.horarios.padrao') }}" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-calendar-week"></i> Horários Padrão
                            </a>
                            <a href="{{ route('comerciantes.horarios.excecoes') }}" class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-calendar-alt"></i> Exceções
                            </a>
                            <a href="{{ route('comerciantes.horarios.relatorio') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-chart-bar"></i> Relatório
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Horários Padrão -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-week"></i> Horários Padrão da Semana
                        @if($sistema)
                            - Sistema: {{ $sistema }}
                        @endif
                    </h5>
                    <a href="{{ route('comerciantes.horarios.padrao') }}" class="btn btn-sm btn-outline-primary">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    @if($horariosPadrao->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Sistema</th>
                                        <th>Dia da Semana</th>
                                        <th>Status</th>
                                        <th>Horário</th>
                                        <th>Observações</th>
                                        <th width="120">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($horariosPadrao->take(10) as $horario)
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
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($horario->observacoes)
                                                <small title="{{ $horario->observacoes }}">
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
                                                        onclick="confirmarExclusao({{ $horario->id }}, 'horário')"
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
                        @if($horariosPadrao->count() > 10)
                            <div class="text-center">
                                <a href="{{ route('comerciantes.horarios.padrao') }}" class="btn btn-outline-primary">
                                    Ver todos os {{ $horariosPadrao->count() }} horários padrão
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-week fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum horário padrão configurado</h5>
                            <p class="text-muted">Configure os horários de funcionamento da sua empresa.</p>
                            <a href="{{ route('comerciantes.horarios.padrao.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Criar Primeiro Horário
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Exceções Futuras -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt"></i> Próximas Exceções
                        @if($sistema)
                            - Sistema: {{ $sistema }}
                        @endif
                    </h5>
                    <a href="{{ route('comerciantes.horarios.excecoes') }}" class="btn btn-sm btn-outline-warning">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    @if($excecoesFuturas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Sistema</th>
                                        <th>Data</th>
                                        <th>Status</th>
                                        <th>Horário</th>
                                        <th>Descrição</th>
                                        <th width="120">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($excecoesFuturas as $excecao)
                                    <tr>
                                        <td>
                                            <span class="badge badge-warning">{{ $excecao->sistema }}</span>
                                        </td>
                                        <td>{{ $excecao->data_excecao->format('d/m/Y') }}</td>
                                        <td>
                                            @if($excecao->aberto)
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
                                            @if($excecao->aberto)
                                                {{ \Carbon\Carbon::parse($excecao->hora_abertura)->format('H:i') }} às 
                                                {{ \Carbon\Carbon::parse($excecao->hora_fechamento)->format('H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $excecao->descricao_excecao }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('comerciantes.horarios.excecoes.edit', $excecao->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmarExclusao({{ $excecao->id }}, 'exceção')"
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
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma exceção programada</h5>
                            <p class="text-muted">Configure exceções para feriados ou eventos especiais.</p>
                            <a href="{{ route('comerciantes.horarios.excecoes.create') }}" class="btn btn-warning">
                                <i class="fas fa-plus"></i> Criar Primeira Exceção
                            </a>
                        </div>
                    @endif
                </div>
            </div>
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
                <p>Tem certeza que deseja excluir este <span id="tipoItem"></span>?</p>
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
function confirmarExclusao(id, tipo) {
    $('#tipoItem').text(tipo);
    $('#deleteForm').attr('action', '/comerciantes/horarios/' + id);
    $('#confirmDeleteModal').modal('show');
}

// Auto-refresh do status a cada 60 segundos
setInterval(function() {
    location.reload();
}, 60000);
</script>
@endsection
