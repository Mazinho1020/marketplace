@extends('layouts.comerciante')

@section('title', 'Exceções de Horário')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-calendar-alt text-warning"></i>
                        Exceções de Horário
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.horarios.index', $empresaId) }}">Horários</a></li>
                            <li class="breadcrumb-item active">Exceções</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('comerciantes.horarios.excecoes.create', $empresaId) }}" 
                       class="btn btn-warning">
                        <i class="fas fa-plus"></i> Nova Exceção
                    </a>
                    <a href="{{ route('comerciantes.horarios.index', $empresaId) }}" 
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
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

    <!-- Lista de Exceções -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Exceções Configuradas
                        <span class="badge bg-warning text-dark ms-2">{{ $excecoes->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($excecoes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Dia da Semana</th>
                                        <th>Sistema</th>
                                        <th>Status</th>
                                        <th>Horário</th>
                                        <th>Observações</th>
                                        <th width="120">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($excecoes as $excecao)
                                    @php
                                        $isPast = $excecao->data_excecao < now()->toDateString();
                                        $isToday = $excecao->data_excecao == now()->toDateString();
                                    @endphp
                                    <tr class="{{ $isPast ? 'table-secondary' : ($isToday ? 'table-warning' : '') }}">
                                        <td>
                                            <strong>{{ $excecao->data_excecao ? $excecao->data_excecao->format('d/m/Y') : 'N/A' }}</strong>
                                            @if($isToday)
                                                <span class="badge bg-warning text-dark ms-2">HOJE</span>
                                            @elseif($isPast)
                                                <span class="badge bg-secondary ms-2">PASSADO</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $excecao->data_excecao ? $excecao->data_excecao->translatedFormat('l') : 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                {{ $excecao->sistema }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($excecao->fechado)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times"></i> Fechado
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> Aberto
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $excecao->horario_formatado }}</strong>
                                        </td>
                                        <td>
                                            @if($excecao->observacoes)
                                                <small class="text-muted" title="{{ $excecao->observacoes }}">
                                                    {{ Str::limit($excecao->observacoes ?? '', 40) }}
                                                </small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <form method="POST" 
                                                      action="{{ route('comerciantes.horarios.destroy', [$empresaId, $excecao->id]) }}" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Tem certeza que deseja remover esta exceção?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Remover">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma exceção configurada</h5>
                            <p class="text-muted">Configure exceções para feriados e datas especiais.</p>
                            <a href="{{ route('comerciantes.horarios.excecoes.create', $empresaId) }}" 
                               class="btn btn-warning">
                                <i class="fas fa-plus"></i> Criar Primeira Exceção
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Dica -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <h6><i class="fas fa-lightbulb"></i> Sobre as Exceções:</h6>
                <ul class="mb-0">
                    <li>As exceções têm <strong>prioridade</strong> sobre os horários padrão</li>
                    <li>Use para configurar horários especiais em feriados, eventos, etc.</li>
                    <li>Você pode criar exceções futuras e do passado para histórico</li>
                    <li>Exceções passadas são mantidas para referência</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
