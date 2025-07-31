@extends('layouts.app')

@section('title', 'Detalhes da Transação')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Detalhes da Transação #{{ $transacao->id }}</h1>
                <div>
                    <a href="{{ route('fidelidade.transacoes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="{{ route('fidelidade.transacoes.edit', $transacao) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Informações da Transação -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-receipt"></i> Informações da Transação
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">ID da Transação:</label>
                                        <div class="text-muted">#{{ $transacao->id }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status:</label>
                                        <div>
                                            @if($transacao->status == 'pendente')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i> Pendente
                                            </span>
                                            @elseif($transacao->status == 'processada')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Processada
                                            </span>
                                            @elseif($transacao->status == 'cancelada')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times"></i> Cancelada
                                            </span>
                                            @else
                                            <span class="badge bg-secondary">{{ $transacao->status }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tipo:</label>
                                        <div class="text-muted">
                                            @if($transacao->tipo == 'credito')
                                            <i class="fas fa-plus-circle text-success"></i> Crédito
                                            @else
                                            <i class="fas fa-minus-circle text-danger"></i> Débito
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Valor:</label>
                                        <div class="text-muted fs-5 fw-bold">
                                            R$ {{ number_format($transacao->valor, 2, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Data/Hora:</label>
                                        <div class="text-muted">
                                            {{ $transacao->created_at->format('d/m/Y H:i:s') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Última Atualização:</label>
                                        <div class="text-muted">
                                            {{ $transacao->updated_at->format('d/m/Y H:i:s') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($transacao->descricao)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Descrição:</label>
                                <div class="text-muted">{{ $transacao->descricao }}</div>
                            </div>
                            @endif

                            @if($transacao->observacoes)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Observações:</label>
                                <div class="text-muted">{{ $transacao->observacoes }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informações do Cliente -->
                <div class="col-md-4">
                    @if($transacao->cliente)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user"></i> Cliente
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nome:</label>
                                <div class="text-muted">{{ $transacao->cliente->nome ?? 'N/A' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email:</label>
                                <div class="text-muted">{{ $transacao->cliente->email ?? 'N/A' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Telefone:</label>
                                <div class="text-muted">{{ $transacao->cliente->telefone ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($transacao->empresa)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-building"></i> Empresa
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Razão Social:</label>
                                <div class="text-muted">{{ $transacao->empresa->razao_social ?? 'N/A' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nome Fantasia:</label>
                                <div class="text-muted">{{ $transacao->empresa->nome_fantasia ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Ações -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-cogs"></i> Ações
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2 flex-wrap">
                                @if($transacao->status == 'pendente')
                                <form action="{{ route('fidelidade.transacoes.processar', $transacao) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Processar
                                    </button>
                                </form>
                                @endif

                                @if($transacao->status != 'cancelada')
                                <form action="{{ route('fidelidade.transacoes.cancelar', $transacao) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Tem certeza que deseja cancelar esta transação?')">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fas fa-ban"></i> Cancelar
                                    </button>
                                </form>
                                @endif

                                @if($transacao->status == 'processada' && $transacao->tipo == 'credito')
                                <form action="{{ route('fidelidade.transacoes.estornar', $transacao) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Tem certeza que deseja estornar esta transação?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-undo"></i> Estornar
                                    </button>
                                </form>
                                @endif

                                <a href="{{ route('fidelidade.transacoes.edit', $transacao) }}"
                                    class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Editar
                                </a>

                                <form action="{{ route('fidelidade.transacoes.destroy', $transacao) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Tem certeza que deseja excluir esta transação?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Excluir
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Atualizar página a cada 30 segundos se a transação estiver pendente
    @if($transacao->status == 'pendente')
        setTimeout(function() {
            location.reload();
        }, 30000);
    @endif
</script>
@endpush