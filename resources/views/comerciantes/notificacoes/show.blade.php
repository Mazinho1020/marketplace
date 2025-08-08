@extends('comerciantes.layouts.app')

@section('title', 'Detalhes da Notificação')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-bell me-2"></i>
                        Detalhes da Notificação
                    </h1>
                    <p class="text-muted mb-0">Informações detalhadas sobre a notificação</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.notificacoes.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Conteúdo Principal -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Conteúdo da Notificação</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h4 class="mb-3">{{ $notificacao->titulo }}</h4>
                        <div class="alert alert-light border">
                            {{ $notificacao->mensagem }}
                        </div>
                    </div>

                    @if($notificacao->dados_processados)
                        <div class="mb-4">
                            <h5>Dados Adicionais</h5>
                            <div class="alert alert-info">
                                <pre>{{ json_encode(json_decode($notificacao->dados_processados), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informações da Notificação -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informações</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-5"><strong>Canal:</strong></div>
                        <div class="col-sm-7">
                            <span class="badge badge-{{ $notificacao->canal == 'email' ? 'primary' : ($notificacao->canal == 'sms' ? 'success' : 'info') }}">
                                {{ ucfirst($notificacao->canal) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-5"><strong>Status:</strong></div>
                        <div class="col-sm-7">
                            <span class="badge badge-{{ $notificacao->status == 'entregue' ? 'success' : ($notificacao->status == 'erro' ? 'danger' : 'warning') }}">
                                {{ ucfirst($notificacao->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-5"><strong>Prioridade:</strong></div>
                        <div class="col-sm-7">
                            <span class="badge badge-{{ ($notificacao->prioridade ?? 'normal') == 'alta' ? 'danger' : (($notificacao->prioridade ?? 'normal') == 'media' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($notificacao->prioridade ?? 'Normal') }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-5"><strong>Enviado em:</strong></div>
                        <div class="col-sm-7">{{ \Carbon\Carbon::parse($notificacao->created_at)->format('d/m/Y H:i:s') }}</div>
                    </div>

                    @if($notificacao->entregue_em)
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>Entregue em:</strong></div>
                            <div class="col-sm-7">{{ \Carbon\Carbon::parse($notificacao->entregue_em)->format('d/m/Y H:i:s') }}</div>
                        </div>
                    @endif

                    @if($notificacao->lido_em)
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>Lido em:</strong></div>
                            <div class="col-sm-7">{{ \Carbon\Carbon::parse($notificacao->lido_em)->format('d/m/Y H:i:s') }}</div>
                        </div>
                    @else
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>Status de Leitura:</strong></div>
                            <div class="col-sm-7">
                                <span class="badge badge-warning">Não Lida</span>
                            </div>
                        </div>
                    @endif

                    @if($notificacao->tentativas)
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>Tentativas:</strong></div>
                            <div class="col-sm-7">{{ $notificacao->tentativas }}</div>
                        </div>
                    @endif

                    @if($notificacao->mensagem_erro)
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>Erro:</strong></div>
                            <div class="col-sm-7">
                                <small class="text-danger">{{ $notificacao->mensagem_erro }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ações -->
            @if(is_null($notificacao->lido_em))
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Ações</h6>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-primary btn-block" onclick="marcarComoLida({{ $notificacao->id }})">
                            <i class="fas fa-check"></i> Marcar como Lida
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function marcarComoLida(id) {
    fetch(`/comerciantes/notificacoes/${id}/marcar-lida`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao marcar como lida');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao marcar como lida');
    });
}
</script>
@endpush
@endsection
