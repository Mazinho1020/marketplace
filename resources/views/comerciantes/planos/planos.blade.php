@extends('comerciantes.layout')

@section('title', 'Nossos Planos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-primary">Escolha o Plano Ideal</h1>
            <p class="lead text-muted">Planos flexíveis que crescem com o seu negócio</p>
        </div>
    </div>

    <!-- Toggle Mensal/Anual -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <div class="btn-group" role="group" aria-label="Ciclo de cobrança">
                <input type="radio" class="btn-check" name="ciclo" id="mensal" value="mensal" checked>
                <label class="btn btn-outline-primary" for="mensal">
                    <i class="fas fa-calendar-day me-2"></i>Mensal
                </label>

                <input type="radio" class="btn-check" name="ciclo" id="anual" value="anual">
                <label class="btn btn-outline-primary" for="anual">
                    <i class="fas fa-calendar-year me-2"></i>Anual
                    <span class="badge bg-success ms-1">Economize até 17%</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Planos -->
    <div class="row justify-content-center">
        @foreach($planos as $plano)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 {{ $plano->is_popular ? 'border-warning' : '' }} position-relative">
                    @if($plano->is_popular)
                        <div class="position-absolute top-0 start-50 translate-middle">
                            <span class="badge bg-warning text-dark px-3 py-2">
                                <i class="fas fa-star me-1"></i>Mais Popular
                            </span>
                        </div>
                    @endif

                    @if($assinaturaAtual && $assinaturaAtual->plano_id == $plano->id)
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>Plano Atual
                            </span>
                        </div>
                    @endif

                    <div class="card-header text-center bg-{{ $plano->cor }} text-white">
                        <h4 class="mb-0">{{ $plano->nome }}</h4>
                        <p class="mb-0 opacity-75">{{ $plano->descricao }}</p>
                    </div>

                    <div class="card-body text-center">
                        <!-- Preço Mensal -->
                        <div class="precio-mensal">
                            <h2 class="text-{{ $plano->cor }} mb-0">
                                R$ {{ number_format($plano->preco_mensal, 2, ',', '.') }}
                            </h2>
                            <small class="text-muted periodo-mensal">/mês</small>
                        </div>

                        <!-- Preço Anual (oculto inicialmente) -->
                        <div class="precio-anual d-none">
                            <h2 class="text-{{ $plano->cor }} mb-0">
                                R$ {{ number_format($plano->preco_anual, 2, ',', '.') }}
                            </h2>
                            <small class="text-muted periodo-anual">/ano</small>
                            @if($plano->desconto_anual > 0)
                                <div class="mt-1">
                                    <span class="badge bg-success">
                                        Economize {{ $plano->desconto_anual }}%
                                    </span>
                                </div>
                            @endif
                        </div>

                        <hr class="my-4">

                        <!-- Recursos do Plano -->
                        <div class="text-start">
                            @php
                                $recursosExibir = [
                                    'pdv_enabled' => 'PDV Completo',
                                    'relatorios_basicos' => 'Relatórios Básicos',
                                    'relatorios_avancados' => 'Relatórios Avançados',
                                    'api_access' => 'Acesso à API',
                                    'webhook_enabled' => 'Webhooks',
                                    'multi_gateway' => 'Múltiplos Gateways',
                                    'suporte_email' => 'Suporte por Email',
                                    'suporte_chat' => 'Suporte por Chat',
                                    'suporte_24h' => 'Suporte 24h',
                                    'backup_automatico' => 'Backup Automático',
                                    'white_label' => 'White Label'
                                ];
                            @endphp

                            @foreach($recursosExibir as $recurso => $nome)
                                <div class="mb-2">
                                    @if($plano->hasFeature($recurso))
                                        <i class="fas fa-check text-success me-2"></i>
                                        <span>{{ $nome }}</span>
                                    @else
                                        <i class="fas fa-times text-muted me-2"></i>
                                        <span class="text-muted">{{ $nome }}</span>
                                    @endif
                                </div>
                            @endforeach

                            <!-- Limites -->
                            <hr class="my-3">
                            <small class="text-muted">
                                <strong>Limites:</strong><br>
                                • {{ $plano->getLimit('transacoes_mes') == -1 ? 'Ilimitadas' : number_format($plano->getLimit('transacoes_mes')) }} transações/mês<br>
                                • {{ $plano->getLimit('usuarios') == -1 ? 'Ilimitados' : $plano->getLimit('usuarios') }} usuários<br>
                                • {{ $plano->getLimit('storage_mb') == -1 ? 'Ilimitado' : $plano->getLimit('storage_mb') . 'MB' }} armazenamento
                            </small>
                        </div>
                    </div>

                    <div class="card-footer text-center">
                        @if($assinaturaAtual && $assinaturaAtual->plano_id == $plano->id)
                            <button class="btn btn-outline-success w-100" disabled>
                                <i class="fas fa-check me-2"></i>Plano Atual
                            </button>
                        @else
                            <button class="btn btn-{{ $plano->cor }} w-100 btn-selecionar-plano"
                                    data-plano-id="{{ $plano->id }}"
                                    data-plano-nome="{{ $plano->nome }}"
                                    data-preco-mensal="{{ $plano->preco_mensal }}"
                                    data-preco-anual="{{ $plano->preco_anual }}">
                                <i class="fas fa-rocket me-2"></i>
                                {{ $assinaturaAtual ? 'Alterar para este Plano' : 'Escolher Plano' }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="modalConfirmarPlano" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Alteração de Plano</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('comerciantes.planos.alterar') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="plano_id" id="modal_plano_id">
                    
                    <div class="text-center mb-4">
                        <h4>Plano <span id="modal_plano_nome"></span></h4>
                        <h3 class="text-primary">
                            R$ <span id="modal_valor"></span>
                            <small>/<span id="modal_ciclo"></span></small>
                        </h3>
                    </div>

                    <!-- Ciclo de Cobrança -->
                    <div class="mb-3">
                        <label class="form-label">Ciclo de Cobrança</label>
                        <select class="form-select" name="ciclo_cobranca" id="ciclo_cobranca" required>
                            <option value="mensal">Mensal</option>
                            <option value="anual">Anual (Economia de até 17%)</option>
                        </select>
                    </div>

                    <!-- Forma de Pagamento -->
                    <div class="mb-3">
                        <label class="form-label">Forma de Pagamento</label>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="forma_pagamento" 
                                           id="pix" value="pix" checked>
                                    <label class="form-check-label" for="pix">
                                        <i class="fas fa-qrcode text-primary me-1"></i>PIX
                                    </label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="forma_pagamento" 
                                           id="credit_card" value="credit_card">
                                    <label class="form-check-label" for="credit_card">
                                        <i class="fas fa-credit-card text-success me-1"></i>Cartão
                                    </label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="forma_pagamento" 
                                           id="bank_slip" value="bank_slip">
                                    <label class="form-check-label" for="bank_slip">
                                        <i class="fas fa-barcode text-warning me-1"></i>Boleto
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Após a confirmação, você será redirecionado para finalizar o pagamento.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-credit-card me-2"></i>Confirmar e Pagar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle between monthly/annual pricing
    $('input[name="ciclo"]').change(function() {
        const isAnual = $(this).val() === 'anual';
        
        if (isAnual) {
            $('.precio-anual, .periodo-anual').removeClass('d-none');
            $('.precio-mensal, .periodo-mensal').addClass('d-none');
        } else {
            $('.precio-mensal, .periodo-mensal').removeClass('d-none');
            $('.precio-anual, .periodo-anual').addClass('d-none');
        }
    });

    // Handle plan selection
    $('.btn-selecionar-plano').click(function() {
        const planoId = $(this).data('plano-id');
        const planoNome = $(this).data('plano-nome');
        const precoMensal = $(this).data('preco-mensal');
        const precoAnual = $(this).data('preco-anual');
        
        $('#modal_plano_id').val(planoId);
        $('#modal_plano_nome').text(planoNome);
        
        updateModalPricing(precoMensal, precoAnual);
        
        $('#modalConfirmarPlano').modal('show');
    });

    // Update modal pricing when cycle changes
    $('#ciclo_cobranca').change(function() {
        const planoId = $('#modal_plano_id').val();
        const button = $(`[data-plano-id="${planoId}"]`);
        const precoMensal = button.data('preco-mensal');
        const precoAnual = button.data('preco-anual');
        
        updateModalPricing(precoMensal, precoAnual);
    });

    function updateModalPricing(precoMensal, precoAnual) {
        const ciclo = $('#ciclo_cobranca').val();
        const isAnual = ciclo === 'anual';
        
        const valor = isAnual ? precoAnual : precoMensal;
        const cicloTexto = isAnual ? 'Anual' : 'Mensal';
        
        $('#modal_valor').text(valor.toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
        $('#modal_ciclo').text(cicloTexto);
    }
});
</script>
@endpush
