@extends('comerciantes.layout')

@section('title', 'Minha Assinatura')

@section('content')
<div class="container-fluid">
    <!-- Alertas -->
    @if(count($alertas) > 0)
        @foreach($alertas as $alerta)
            <div class="alert alert-{{ $alerta['tipo'] }} alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ $alerta['titulo'] }}
                </h6>
                <p class="mb-2">{{ $alerta['mensagem'] }}</p>
                <a href="{{ $alerta['acao'] }}" class="btn btn-sm btn-{{ $alerta['tipo'] }}">
                    Tomar Ação
                </a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endforeach
    @endif

    <!-- Header da Assinatura -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-{{ $assinatura ? $assinatura->plano->cor : 'secondary' }} text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-1">
                                <i class="fas fa-crown me-2"></i>
                                {{ $assinatura ? $assinatura->plano->nome : 'Sem Plano Ativo' }}
                            </h3>
                            <p class="mb-0 opacity-75">
                                @if($assinatura)
                                    Status: {{ ucfirst($assinatura->status) }} • 
                                    Ciclo: {{ ucfirst($assinatura->ciclo_cobranca) }} •
                                    Expira em: {{ $assinatura->expira_em->format('d/m/Y') }}
                                    ({{ $assinatura->dias_restantes }} dias restantes)
                                @else
                                    Escolha um plano para começar a usar o sistema
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h4 class="mb-0">
                                @if($assinatura)
                                    R$ {{ number_format($assinatura->valor, 2, ',', '.') }}
                                    <small>/{{ $assinatura->ciclo_cobranca === 'anual' ? 'ano' : 'mês' }}</small>
                                @else
                                    --
                                @endif
                            </h4>
                            <a href="{{ route('comerciantes.planos.planos') }}" class="btn btn-light btn-sm mt-2">
                                <i class="fas fa-arrow-up me-1"></i>
                                {{ $assinatura ? 'Alterar Plano' : 'Escolher Plano' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas de Uso -->
    @if($assinatura)
        <div class="row mb-4">
            @foreach($stats as $key => $stat)
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-1">
                                        {{ match($key) {
                                            'transacoes_mes' => 'Transações/Mês',
                                            'usuarios' => 'Usuários Ativos',
                                            'storage_mb' => 'Armazenamento (MB)',
                                            default => ucfirst($key)
                                        } }}
                                    </h6>
                                    <h4 class="mb-0">
                                        {{ $stat['usado'] }}
                                        @if($stat['limite'] > 0)
                                            <small class="text-muted">/ {{ $stat['limite'] }}</small>
                                        @else
                                            <small class="text-success">/ Ilimitado</small>
                                        @endif
                                    </h4>
                                </div>
                                <div class="text-end">
                                    @php
                                        $porcentagem = $stat['limite'] > 0 ? ($stat['usado'] / $stat['limite']) * 100 : 0;
                                        $cor = $porcentagem > 80 ? 'danger' : ($porcentagem > 60 ? 'warning' : 'success');
                                    @endphp
                                    <span class="badge bg-{{ $cor }}">
                                        {{ $stat['limite'] > 0 ? number_format($porcentagem, 0) . '%' : '∞' }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($stat['limite'] > 0)
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-{{ $cor }}" 
                                         style="width: {{ min($porcentagem, 100) }}%"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Transações Recentes e Ações Rápidas -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Transações Recentes
                    </h5>
                    <a href="{{ route('comerciantes.planos.historico') }}" class="btn btn-outline-primary btn-sm">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($transacoesRecentes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    @foreach($transacoesRecentes as $transacao)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="{{ $transacao->status_icone }} text-{{ $transacao->status_cor }} me-2"></i>
                                                    <div>
                                                        <div class="fw-bold">{{ $transacao->descricao }}</div>
                                                        <small class="text-muted">{{ $transacao->created_at->format('d/m/Y H:i') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="fw-bold">R$ {{ number_format($transacao->valor_final, 2, ',', '.') }}</div>
                                                <span class="badge bg-{{ $transacao->status_cor }}">
                                                    {{ ucfirst($transacao->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-receipt fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Nenhuma transação encontrada</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-rocket me-2"></i>Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$assinatura)
                            <a href="{{ route('comerciantes.planos.planos') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Escolher Plano
                            </a>
                        @else
                            <a href="{{ route('comerciantes.planos.planos') }}" class="btn btn-warning">
                                <i class="fas fa-arrow-up me-2"></i>Fazer Upgrade
                            </a>
                        @endif
                        
                        <a href="{{ route('comerciantes.planos.historico') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-history me-2"></i>Ver Histórico
                        </a>
                        
                        @if($assinatura && $assinatura->renovacao_automatica)
                            <button class="btn btn-outline-danger" onclick="toggleRenovacao(false)">
                                <i class="fas fa-pause me-2"></i>Pausar Renovação
                            </button>
                        @elseif($assinatura)
                            <button class="btn btn-outline-success" onclick="toggleRenovacao(true)">
                                <i class="fas fa-play me-2"></i>Ativar Renovação
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Suporte -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-headset me-2"></i>Suporte
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Precisa de ajuda? Entre em contato:</p>
                    
                    @if($assinatura && $assinatura->plano->hasFeature('suporte_24h'))
                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-success btn-sm">
                                <i class="fas fa-phone me-2"></i>Suporte 24h
                            </a>
                            <a href="#" class="btn btn-primary btn-sm">
                                <i class="fas fa-comments me-2"></i>Chat Online
                            </a>
                            <a href="mailto:suporte@marketplace.com" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-envelope me-2"></i>Email
                            </a>
                        </div>
                    @elseif($assinatura && $assinatura->plano->hasFeature('suporte_chat'))
                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-primary btn-sm">
                                <i class="fas fa-comments me-2"></i>Chat Online
                            </a>
                            <a href="mailto:suporte@marketplace.com" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-envelope me-2"></i>Email
                            </a>
                        </div>
                    @else
                        <a href="mailto:suporte@marketplace.com" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-envelope me-2"></i>Suporte por Email
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleRenovacao(ativar) {
    if (confirm(ativar ? 'Ativar renovação automática?' : 'Pausar renovação automática?')) {
        // Implementar AJAX para toggle de renovação
        fetch('{{ route("comerciantes.planos.toggle-renovacao") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ renovacao_automatica: ativar })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao alterar configuração');
            }
        });
    }
}
</script>
@endpush
