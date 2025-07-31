@extends('layouts.app')

@section('title', 'Detalhes do Cupom - ' . $cupom->titulo)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-ticket-alt text-primary"></i>
                        Cupom: {{ $cupom->titulo }}
                    </h1>
                    <p class="text-muted mb-0">
                        Código: <strong>{{ $cupom->codigo }}</strong> |
                        Status:
                        @if($cupom->status === 'ativo')
                        <span class="badge bg-success">Ativo</span>
                        @elseif($cupom->status === 'inativo')
                        <span class="badge bg-secondary">Inativo</span>
                        @else
                        <span class="badge bg-warning">{{ ucfirst($cupom->status) }}</span>
                        @endif
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('fidelidade.cupons.edit', $cupom->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('fidelidade.cupons.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <!-- Informações do Cupom -->
            <div class="row">
                <!-- Card Principal do Cupom -->
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle"></i>
                                Informações do Cupom
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Descrição</h6>
                                    <p>{{ $cupom->descricao }}</p>

                                    <h6 class="text-muted">Tipo de Desconto</h6>
                                    <p>
                                        @if($cupom->tipo_desconto === 'percentual')
                                        <i class="fas fa-percentage text-info"></i> Percentual
                                        @else
                                        <i class="fas fa-dollar-sign text-success"></i> Valor Fixo
                                        @endif
                                    </p>

                                    <h6 class="text-muted">Valor do Desconto</h6>
                                    <p class="h5 text-primary">
                                        @if($cupom->tipo_desconto === 'percentual')
                                        {{ number_format($cupom->valor_desconto, 1) }}%
                                        @else
                                        R$ {{ number_format($cupom->valor_desconto, 2, ',', '.') }}
                                        @endif
                                    </p>

                                    @if($cupom->valor_minimo_compra)
                                    <h6 class="text-muted">Valor Mínimo da Compra</h6>
                                    <p>R$ {{ number_format($cupom->valor_minimo_compra, 2, ',', '.') }}</p>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <h6 class="text-muted">Período de Validade</h6>
                                    <p>
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ \Carbon\Carbon::parse($cupom->data_inicio)->format('d/m/Y H:i') }} até
                                        {{ \Carbon\Carbon::parse($cupom->data_validade)->format('d/m/Y H:i') }}
                                    </p>

                                    <h6 class="text-muted">Limites de Uso</h6>
                                    <ul class="list-unstyled">
                                        <li>
                                            <i class="fas fa-hashtag"></i>
                                            Total: {{ $cupom->quantidade_maxima ?
                                            number_format($cupom->quantidade_maxima) : 'Ilimitado' }}
                                        </li>
                                        <li>
                                            <i class="fas fa-user"></i>
                                            Por cliente: {{ $cupom->limite_uso_cliente ?
                                            number_format($cupom->limite_uso_cliente) : 'Ilimitado' }}
                                        </li>
                                    </ul>

                                    @if($cupom->nivel_minimo_cliente)
                                    <h6 class="text-muted">Nível Mínimo</h6>
                                    <p>
                                        @if($cupom->nivel_minimo_cliente === 'bronze')
                                        <span class="badge bg-warning">🥉 Bronze</span>
                                        @elseif($cupom->nivel_minimo_cliente === 'prata')
                                        <span class="badge bg-secondary">🥈 Prata</span>
                                        @elseif($cupom->nivel_minimo_cliente === 'ouro')
                                        <span class="badge bg-warning">🥇 Ouro</span>
                                        @elseif($cupom->nivel_minimo_cliente === 'diamond')
                                        <span class="badge bg-info">💎 Diamond</span>
                                        @endif
                                    </p>
                                    @endif

                                    <h6 class="text-muted">Configurações Especiais</h6>
                                    <ul class="list-unstyled">
                                        <li>
                                            <i
                                                class="fas fa-{{ $cupom->primeira_compra_apenas ? 'check' : 'times' }}"></i>
                                            Primeira compra apenas: {{ $cupom->primeira_compra_apenas ? 'Sim' : 'Não' }}
                                        </li>
                                        <li>
                                            <i
                                                class="fas fa-{{ $cupom->acumulativo_cashback ? 'check' : 'times' }}"></i>
                                            Acumula com cashback: {{ $cupom->acumulativo_cashback ? 'Sim' : 'Não' }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estatísticas de Uso -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar"></i>
                                Estatísticas de Uso
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-ticket-alt fa-2x text-primary mb-2"></i>
                                        <h4 class="text-primary">{{ number_format($estatisticasUso['total_usos']) }}
                                        </h4>
                                        <small class="text-muted">Total de Usos</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                        <h4 class="text-success">R$ {{
                                            number_format($estatisticasUso['valor_total_descontos'], 2, ',', '.') }}
                                        </h4>
                                        <small class="text-muted">Total em Descontos</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-users fa-2x text-info mb-2"></i>
                                        <h4 class="text-info">{{ number_format($estatisticasUso['clientes_unicos']) }}
                                        </h4>
                                        <small class="text-muted">Clientes Únicos</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-calendar-month fa-2x text-warning mb-2"></i>
                                        <h4 class="text-warning">{{ number_format($estatisticasUso['uso_medio_mensal'])
                                            }}</h4>
                                        <small class="text-muted">Usos no Último Mês</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Progresso do Cupom -->
                            @if($cupom->quantidade_maxima)
                            <div class="mt-4">
                                <h6>Progresso de Utilização</h6>
                                @php
                                $percentual = ($cupom->quantidade_utilizada / $cupom->quantidade_maxima) * 100;
                                @endphp
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $percentual }}%"
                                        aria-valuenow="{{ $percentual }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($percentual, 1) }}%
                                    </div>
                                </div>
                                <small class="text-muted">
                                    {{ number_format($cupom->quantidade_utilizada) }} de {{
                                    number_format($cupom->quantidade_maxima) }} usos
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Histórico de Usos Recentes -->
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-history"></i>
                                Usos Recentes
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($usosRecentes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Data/Hora</th>
                                            <th>Valor Desconto</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($usosRecentes as $uso)
                                        <tr>
                                            <td>
                                                <i class="fas fa-user text-muted"></i>
                                                Cliente ID: {{ $uso->cliente_id }}
                                            </td>
                                            <td>
                                                <i class="fas fa-calendar"></i>
                                                {{ $uso->data_uso ? \Carbon\Carbon::parse($uso->data_uso)->format('d/m/Y
                                                H:i') : 'Pendente' }}
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    R$ {{ number_format($uso->valor_desconto, 2, ',', '.') }}
                                                </strong>
                                            </td>
                                            <td>
                                                @if($uso->status === 'utilizado')
                                                <span class="badge bg-success">Utilizado</span>
                                                @elseif($uso->status === 'cancelado')
                                                <span class="badge bg-danger">Cancelado</span>
                                                @else
                                                <span class="badge bg-warning">{{ ucfirst($uso->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum uso registrado</h5>
                                <p class="text-muted">Este cupom ainda não foi utilizado por nenhum cliente.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar com Ações e Informações -->
                <div class="col-lg-4">
                    <!-- Preview do Cupom -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-eye"></i>
                                Preview do Cupom
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="border rounded p-3 bg-light">
                                <h5 class="text-primary">{{ $cupom->titulo }}</h5>
                                <div class="bg-primary text-white rounded p-2 my-2">
                                    <h4 class="mb-0">{{ $cupom->codigo }}</h4>
                                </div>
                                <p class="small text-muted">
                                    @if($cupom->tipo_desconto === 'percentual')
                                    {{ number_format($cupom->valor_desconto, 1) }}% de desconto
                                    @else
                                    R$ {{ number_format($cupom->valor_desconto, 2, ',', '.') }} de desconto
                                    @endif
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i>
                                    Válido até: {{ \Carbon\Carbon::parse($cupom->data_validade)->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Ações Rápidas -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">
                                <i class="fas fa-tools"></i>
                                Ações Rápidas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('fidelidade.cupons.edit', $cupom->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar Cupom
                                </a>

                                @if($cupom->status === 'ativo')
                                <button type="button" class="btn btn-secondary"
                                    onclick="alterarStatus('{{ $cupom->id }}', 'inativo')">
                                    <i class="fas fa-pause"></i> Desativar Cupom
                                </button>
                                @else
                                <button type="button" class="btn btn-success"
                                    onclick="alterarStatus('{{ $cupom->id }}', 'ativo')">
                                    <i class="fas fa-play"></i> Ativar Cupom
                                </button>
                                @endif

                                <button type="button" class="btn btn-danger" onclick="excluirCupom('{{ $cupom->id }}')">
                                    <i class="fas fa-trash"></i> Excluir Cupom
                                </button>

                                <button type="button" class="btn btn-info" onclick="copiarCodigo()">
                                    <i class="fas fa-copy"></i> Copiar Código
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Informações Adicionais -->
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-info"></i>
                                Informações Adicionais
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>Criado em:</strong><br>
                                    <small class="text-muted">{{ $cupom->created_at->format('d/m/Y H:i') }}</small>
                                </li>
                                <li class="mb-2">
                                    <strong>Última atualização:</strong><br>
                                    <small class="text-muted">{{ $cupom->updated_at->format('d/m/Y H:i') }}</small>
                                </li>
                                @if($cupom->empresa_id)
                                <li class="mb-2">
                                    <strong>Empresa:</strong><br>
                                    <small class="text-muted">ID: {{ $cupom->empresa_id }}</small>
                                </li>
                                @endif
                                <li class="mb-2">
                                    <strong>Dias restantes:</strong><br>
                                    <small class="text-muted">
                                        @php
                                        $diasRestantes =
                                        \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($cupom->data_validade),
                                        false);
                                        @endphp
                                        @if($diasRestantes > 0)
                                        {{ $diasRestantes }} dias
                                        @elseif($diasRestantes === 0)
                                        Expira hoje
                                        @else
                                        Expirado há {{ abs($diasRestantes) }} dias
                                        @endif
                                    </small>
                                </li>
                            </ul>
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
    function copiarCodigo() {
    const codigo = '{{ $cupom->codigo }}';
    navigator.clipboard.writeText(codigo).then(function() {
        alert('Código copiado para a área de transferência!');
    }, function(err) {
        console.error('Erro ao copiar código: ', err);
        alert('Erro ao copiar código');
    });
}

function alterarStatus(id, novoStatus) {
    const acao = novoStatus === 'ativo' ? 'ativar' : 'desativar';
    if (confirm(`Tem certeza que deseja ${acao} este cupom?`)) {
        fetch(`/fidelidade/cupons/${id}`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                status: novoStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao alterar status do cupom');
            }
        })
        .catch(error => {
            alert('Erro ao alterar status do cupom');
            console.error('Error:', error);
        });
    }
}

function excluirCupom(id) {
    if (confirm('Tem certeza que deseja EXCLUIR este cupom?\n\nEsta ação não pode ser desfeita!')) {
        fetch(`/fidelidade/cupons/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.href = '/fidelidade/cupons';
            } else {
                return response.json().then(data => {
                    alert(data.message || 'Erro ao excluir cupom');
                });
            }
        })
        .catch(error => {
            alert('Erro ao excluir cupom');
            console.error('Error:', error);
        });
    }
}
</script>
@endpush