@extends('financial.layout')

@section('financial-title', 'Detalhes da Conta a Receber')

@section('financial-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-eye text-success"></i>
                        Detalhes da Conta a Receber
                    </h5>
                    <div>
                        <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.index', $empresa) }}" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        @if($lancamento->situacao->value !== 'RECEBIDO')
                            <a href="{{ route('comerciantes.empresas.financeiro.contas-receber.edit', [$empresa, $lancamento]) }}" 
                               class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" 
                                    class="btn btn-success btn-sm" 
                                    onclick="marcarComoRecebido({{ $lancamento->id }})">
                                <i class="fas fa-check"></i> Marcar como Recebido
                            </button>
                            <button type="button" 
                                    class="btn btn-primary btn-sm" 
                                    onclick="gerarBoleto({{ $lancamento->id }})">
                                <i class="fas fa-barcode"></i> Gerar Boleto
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <!-- Informações Principais -->
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Informações Principais</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Descrição:</strong><br>
                                        {{ $lancamento->descricao }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Código:</strong><br>
                                        {{ $lancamento->codigo_lancamento ?: '-' }}
                                    </div>
                                </div>
                                
                                @if($lancamento->observacoes)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <strong>Observações:</strong><br>
                                        {{ $lancamento->observacoes }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Valores e Datas -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Valores e Datas</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Valor Total:</strong><br>
                                        <span class="h5 text-success">R$ {{ number_format($lancamento->valor_total, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Valor Recebido:</strong><br>
                                        <span class="h5 text-success">R$ {{ number_format($lancamento->valor_recebido, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Saldo a Receber:</strong><br>
                                        <span class="h5 {{ ($lancamento->valor_total - $lancamento->valor_recebido) > 0 ? 'text-warning' : 'text-success' }}">
                                            R$ {{ number_format($lancamento->valor_total - $lancamento->valor_recebido, 2, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Data de Vencimento:</strong><br>
                                        {{ \Carbon\Carbon::parse($lancamento->data_vencimento)->format('d/m/Y') }}
                                        @if($lancamento->isVencido())
                                            <br><span class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Vencida há {{ $lancamento->diasParaVencimento() * -1 }} dias
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Data de Competência:</strong><br>
                                        {{ $lancamento->data_competencia ? \Carbon\Carbon::parse($lancamento->data_competencia)->format('d/m/Y') : '-' }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Data de Recebimento:</strong><br>
                                        {{ $lancamento->data_recebimento ? \Carbon\Carbon::parse($lancamento->data_recebimento)->format('d/m/Y H:i') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Parcelamento -->
                        @if($lancamento->numero_parcelas > 1)
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Informações de Parcelamento</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Parcela Atual:</strong><br>
                                        {{ $lancamento->parcela_atual }}/{{ $lancamento->numero_parcelas }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Valor da Parcela:</strong><br>
                                        R$ {{ number_format($lancamento->valor_parcela ?? 0, 2, ',', '.') }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Frequência:</strong><br>
                                        {{ $lancamento->frequencia_recorrencia ? \App\Enums\FrequenciaRecorrenciaEnum::from($lancamento->frequencia_recorrencia)->label() : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Relacionamentos e Status -->
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Status</h6>
                            </div>
                            <div class="card-body text-center">
                                <span class="badge badge-{{ $lancamento->situacao->color() }} p-2" style="font-size: 1.1em;">
                                    <i class="{{ $lancamento->situacao->icon() }}"></i>
                                    {{ $lancamento->situacao->label() }}
                                </span>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Relacionamentos</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Cliente:</strong><br>
                                    @if($lancamento->cliente)
                                        <i class="fas fa-user text-info"></i>
                                        {{ $lancamento->cliente->nome }}
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Funcionário Responsável:</strong><br>
                                    @if($lancamento->funcionario)
                                        <i class="fas fa-user-tie text-primary"></i>
                                        {{ $lancamento->funcionario->nome }}
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Conta Gerencial:</strong><br>
                                    @if($lancamento->conta)
                                        <i class="fas fa-chart-line text-success"></i>
                                        {{ $lancamento->conta->nome }}
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </div>
                                
                                <div>
                                    <strong>Categoria:</strong><br>
                                    @if($lancamento->categoria)
                                        <i class="fas fa-tags text-warning"></i>
                                        {{ $lancamento->categoria->nome }}
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Configurações de Cobrança -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Configurações de Cobrança</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Cobrança Automática:</strong><br>
                                    @if($lancamento->cobranca_automatica)
                                        <span class="text-success">
                                            <i class="fas fa-check-circle"></i> Ativada
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-times-circle"></i> Desativada
                                        </span>
                                    @endif
                                </div>
                                
                                @if($lancamento->juros_multa_config)
                                <div>
                                    <strong>Configuração de Juros/Multa:</strong><br>
                                    {{ $lancamento->juros_multa_config }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Histórico de Recebimentos -->
                @if($lancamento->valor_recebido > 0)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Histórico de Recebimentos</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    Valor recebido: <strong>R$ {{ number_format($lancamento->valor_recebido, 2, ',', '.') }}</strong>
                                    @if($lancamento->data_recebimento)
                                        em {{ \Carbon\Carbon::parse($lancamento->data_recebimento)->format('d/m/Y H:i') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function marcarComoRecebido(lancamentoId) {
    const url = `{{ url('/') }}/comerciantes/empresas/{{ $empresa->id }}/financeiro/contas-receber/${lancamentoId}/receber`;
    
    if (confirm('Confirma o recebimento deste lançamento?')) {
        // Criar e submeter form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function gerarBoleto(lancamentoId) {
    const url = `{{ url('/') }}/comerciantes/empresas/{{ $empresa->id }}/financeiro/contas-receber/${lancamentoId}/gerar-boleto`;
    
    if (confirm('Deseja gerar um boleto para este lançamento?')) {
        // Criar e submeter form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
