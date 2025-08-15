<?php $__env->startSection('title', 'Registrar Recebimento'); ?>

<?php $__env->startSection('content'); ?>
<?php
    use Illuminate\Support\Facades\DB;
?>
<div class="container-fluid">
    <!-- Header com Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('comerciantes.dashboard', $empresa)); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('comerciantes.empresas.financeiro.dashboard', $empresa)); ?>">Financeiro</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.index', $empresa)); ?>">Contas a Receber</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Registrar Recebimento</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Registrar Recebimento</h1>
        </div>
        <div>
            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.index', $empresa)); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    <!-- Resumo da Conta -->
    <?php if(isset($contaReceber)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Descrição:</strong><br>
                        <span class="text-muted"><?php echo e($contaReceber->descricao); ?></span>
                    </div>
                    <div class="col-md-3">
                        <strong>Valor Total:</strong><br>
                        <span class="h6 text-primary">R$ <?php echo e(number_format($contaReceber->valor_final, 2, ',', '.')); ?></span>
                    </div>
                    <div class="col-md-3">
                        <strong>Saldo a Receber:</strong><br>
                        <?php
                            $valorRecebido = $contaReceber->recebimentos()->where('status_pagamento', 'confirmado')->sum('valor');
                            $saldoReceber = $contaReceber->valor_final - $valorRecebido;
                        ?>
                        <span class="h6 text-success" id="saldoReceber">R$ <?php echo e(number_format($saldoReceber, 2, ',', '.')); ?></span>
                    </div>
                    <div class="col-md-3">
                        <strong>Situação:</strong><br>
                        <?php if($contaReceber->situacao_financeira === 'quitado'): ?>
                            <span class="badge bg-success">Quitado</span>
                        <?php elseif($contaReceber->situacao_financeira === \App\Enums\SituacaoFinanceiraEnum::PARCIALMENTE_PAGO): ?>
                            <span class="badge bg-warning">Parcialmente Recebido</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Pendente</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Formulário Principal -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-dollar-sign me-2"></i>
                Dados do Recebimento
            </h5>
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <form id="formRecebimento" method="POST" action="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.recebimentos.store', ['empresa' => $empresa->id, 'id' => $contaReceber->id ?? 0])); ?>">
                <?php echo csrf_field(); ?>
                
                <!-- Dados Principais -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="forma_pagamento_id" class="form-label">
                                Forma de Pagamento <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="forma_pagamento_id" name="forma_pagamento_id" required aria-describedby="forma_pagamento_help">
                                <option value="">Selecione...</option>
                            </select>
                            <div id="forma_pagamento_help" class="form-text">Escolha a forma de recebimento utilizada</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bandeira_id" class="form-label">Bandeira</label>
                            <select class="form-select" id="bandeira_id" name="bandeira_id" aria-describedby="bandeira_help">
                                <option value="">Selecione uma forma de pagamento primeiro</option>
                            </select>
                            <div id="bandeira_help" class="form-text">Bandeira do cartão (se aplicável)</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="valor" class="form-label">
                                Valor do Recebimento <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="valor" name="valor" 
                                       step="0.01" min="0.01" max="<?php echo e($saldoReceber ?? 9999999); ?>" 
                                       value="<?php echo e(number_format($saldoReceber ?? 0, 2, '.', '')); ?>" required aria-describedby="valor_help">
                            </div>
                            <div id="valor_help" class="form-text">
                                <?php if(isset($saldoReceber)): ?>
                                    Máximo: R$ <?php echo e(number_format($saldoReceber, 2, ',', '.')); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="data_pagamento" class="form-label">
                                Data do Recebimento <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="data_pagamento" name="data_pagamento" 
                                   value="<?php echo e(date('Y-m-d')); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="data_compensacao" class="form-label">Data de Compensação</label>
                            <input type="date" class="form-control" id="data_compensacao" name="data_compensacao">
                        </div>
                    </div>
                </div>

                <!-- Valores Detalhados -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="valor_principal" class="form-label">Valor Principal</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="valor_principal" name="valor_principal" 
                                       step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="valor_juros" class="form-label">Juros</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="valor_juros" name="valor_juros" 
                                       step="0.01" min="0" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="valor_multa" class="form-label">Multa</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="valor_multa" name="valor_multa" 
                                       step="0.01" min="0" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="valor_desconto" class="form-label">Desconto</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="valor_desconto" name="valor_desconto" 
                                       step="0.01" min="0" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conta Bancária e Taxas -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="conta_bancaria_id" class="form-label">
                                Conta Bancária <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="conta_bancaria_id" name="conta_bancaria_id" required aria-describedby="conta_help">
                                <option value="1">Conta Principal</option>
                            </select>
                            <div id="conta_help" class="form-text">Conta onde será registrado o recebimento</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="taxa" class="form-label">Taxa (%)</label>
                            <input type="number" class="form-control" id="taxa" name="taxa" 
                                   step="0.01" min="0" max="100" value="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="valor_taxa" class="form-label">Valor da Taxa</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="valor_taxa" name="valor_taxa" 
                                       step="0.01" min="0" value="0" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observações e Referência -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="observacao" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacao" name="observacao" 
                                      rows="3" placeholder="Digite observações sobre o recebimento..."></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="referencia_externa" class="form-label">Referência Externa</label>
                            <input type="text" class="form-control" id="referencia_externa" name="referencia_externa" 
                                   placeholder="NSU, TID, etc..." maxlength="100">
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?php echo e(route('comerciantes.empresas.financeiro.contas-receber.index', $empresa)); ?>" 
                               class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" id="btnConfirmarRecebimento" class="btn btn-success">
                                <i class="fas fa-check me-1"></i>Confirmar Recebimento
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Função de teste para o console
window.testarRecebimento = function() {
    console.log('🧪 Iniciando teste de recebimento...');
    
    const dados = new FormData();
    dados.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    dados.append('forma_pagamento_id', '6');
    dados.append('conta_bancaria_id', '1');
    dados.append('valor', '300.00');
    dados.append('data_pagamento', '2025-08-15');
    dados.append('valor_principal', '300.00');
    dados.append('valor_juros', '0');
    dados.append('valor_multa', '0');
    dados.append('valor_desconto', '0');
    dados.append('taxa', '0');
    dados.append('valor_taxa', '0');
    
    console.log('📤 Dados preparados para teste');
    
    const empresaId = <?php echo e($empresa->id ?? 1); ?>;
    const lancamentoId = <?php echo e($lancamento->id ?? 1); ?>;
    
    fetch(`/comerciantes/empresas/${empresaId}/financeiro/contas-receber/${lancamentoId}/recebimentos`, {
        method: 'POST',
        body: dados,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('📡 Response:', response.status, response.statusText);
        if (!response.ok) {
            return response.text().then(text => {
                console.error('❌ Erro:', text);
                throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Sucesso:', data);
    })
    .catch(error => {
        console.error('❌ Erro final:', error);
    });
};

// Variáveis globais
const empresaId = <?php echo e($empresa->id ?? 1); ?>;
const lancamentoId = <?php echo e($contaReceber->id ?? 1); ?>;
let saldoReceber = <?php echo e($saldoReceber ?? 0); ?>;

// Carregar formas de pagamento
function carregarFormasPagamento() {
    const url = `/comerciantes/empresas/${empresaId}/financeiro/api/formas-pagamento`;
    
    console.log('🔍 Carregando formas de pagamento da URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('📡 Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Formas de pagamento carregadas:', data);
            const select = document.getElementById('forma_pagamento_id');
            select.innerHTML = '<option value="">Selecione...</option>';
            
            data.forEach(forma => {
                const option = document.createElement('option');
                option.value = forma.id;
                option.textContent = forma.nome;
                option.dataset.gateway = forma.gateway_method || '';
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('❌ Erro ao carregar formas de pagamento:', error);
            const select = document.getElementById('forma_pagamento_id');
            select.innerHTML = '<option value="">Erro ao carregar formas</option>';
        });
}

// Carregar bandeiras baseado na forma de pagamento
function carregarBandeiras(formaPagamentoId) {
    const url = `/comerciantes/empresas/${empresaId}/financeiro/api/formas-pagamento/${formaPagamentoId}/bandeiras`;
    
    console.log('🔍 Carregando bandeiras da URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('📡 Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Bandeiras carregadas:', data);
            const select = document.getElementById('bandeira_id');
            select.innerHTML = '<option value="">Nenhuma bandeira necessária</option>';
            
            data.forEach(bandeira => {
                const option = document.createElement('option');
                option.value = bandeira.id;
                option.textContent = bandeira.nome;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('❌ Erro ao carregar bandeiras:', error);
            const select = document.getElementById('bandeira_id');
            select.innerHTML = '<option value="">Erro ao carregar bandeiras</option>';
        });
}

// Calcular valor da taxa automaticamente
function calcularTaxa() {
    const taxa = parseFloat(document.getElementById('taxa').value) || 0;
    const valorPrincipal = parseFloat(document.getElementById('valor_principal').value) || 0;
    
    // Taxa deve ser calculada sobre o valor principal
    const valorTaxa = (valorPrincipal * taxa) / 100;
    
    console.log('💰 Calculando taxa:', {
        valorPrincipal: valorPrincipal,
        percentual: taxa,
        valorTaxa: valorTaxa,
        observacao: 'Taxa calculada sobre o valor principal'
    });
    
    document.getElementById('valor_taxa').value = valorTaxa.toFixed(2);
    
    // Após calcular a taxa, recalcular o total
    atualizarValorTotal();
}

// Atualizar valor total quando componentes mudam
function atualizarValorTotal() {
    const valorPrincipal = parseFloat(document.getElementById('valor_principal').value) || 0;
    const valorJuros = parseFloat(document.getElementById('valor_juros').value) || 0;
    const valorMulta = parseFloat(document.getElementById('valor_multa').value) || 0;
    const valorDesconto = parseFloat(document.getElementById('valor_desconto').value) || 0;
    const valorTaxa = parseFloat(document.getElementById('valor_taxa').value) || 0;
    
    // Cálculo: Principal + Juros + Multa - Desconto - Taxa = Total
    // TAXA SUBTRAI do valor do recebimento (diferente dos pagamentos)
    const valorTotal = valorPrincipal + valorJuros + valorMulta - valorDesconto - valorTaxa;
    document.getElementById('valor').value = Math.max(0, valorTotal).toFixed(2);
    
    console.log('💰 Valor total calculado:', {
        principal: valorPrincipal,
        juros: valorJuros,
        multa: valorMulta,
        desconto: valorDesconto,
        taxa: valorTaxa,
        total: valorTotal,
        observacao: 'Taxa SUBTRAI do valor do recebimento'
    });
}

// Função para calcular valor principal quando o total do recebimento muda
function atualizarValorPrincipal() {
    const valorTotal = parseFloat(document.getElementById('valor').value) || 0;
    const valorJuros = parseFloat(document.getElementById('valor_juros').value) || 0;
    const valorMulta = parseFloat(document.getElementById('valor_multa').value) || 0;
    const valorDesconto = parseFloat(document.getElementById('valor_desconto').value) || 0;
    const valorTaxa = parseFloat(document.getElementById('valor_taxa').value) || 0;
    
    // Cálculo reverso: Total - Juros - Multa + Taxa + Desconto = Principal
    // TAXA SUBTRAI do recebimento, então para calcular o principal precisamos SOMAR a taxa
    const valorPrincipal = valorTotal - valorJuros - valorMulta + valorTaxa + valorDesconto;
    document.getElementById('valor_principal').value = Math.max(0, valorPrincipal).toFixed(2);
    
    console.log('🔄 Valor principal calculado:', {
        total: valorTotal,
        juros: valorJuros,
        multa: valorMulta,
        desconto: valorDesconto,
        taxa: valorTaxa,
        principal: valorPrincipal,
        observacao: 'Taxa SUBTRAI do recebimento, então somamos para calcular o principal'
    });
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Carregar formas de pagamento quando a página carregar
    carregarFormasPagamento();
    
    // Inicializar valores zerados, exceto o valor principal que deve ser igual ao saldo
    document.getElementById('valor_juros').value = '0.00';
    document.getElementById('valor_multa').value = '0.00';
    document.getElementById('valor_desconto').value = '0.00';
    document.getElementById('taxa').value = '0.00';
    document.getElementById('valor_taxa').value = '0.00';
    
    // Inicializar valor principal com base no saldo a receber
    setTimeout(() => {
        console.log('🎯 Inicializando - Valor principal igual ao saldo a receber');
        const valorTotal = parseFloat(document.getElementById('valor').value) || 0;
        document.getElementById('valor_principal').value = valorTotal.toFixed(2);
        console.log('💰 Valor principal inicializado:', valorTotal);
    }, 100);

    // Event listener para mudança na forma de pagamento
    document.getElementById('forma_pagamento_id').addEventListener('change', function() {
        const formaPagamentoId = this.value;
        if (formaPagamentoId) {
            carregarBandeiras(formaPagamentoId);
        } else {
            document.getElementById('bandeira_id').innerHTML = '<option value="">Selecione uma forma de pagamento primeiro</option>';
        }
    });

    // Event listeners para cálculos automáticos
    
    // Eventos para cálculo do valor total quando componentes mudam
    document.getElementById('valor_juros').addEventListener('input', function() {
        console.log('📈 Juros alterado - recalculando total...');
        atualizarValorTotal();
    });
    
    document.getElementById('valor_multa').addEventListener('input', function() {
        console.log('⚠️ Multa alterada - recalculando total...');
        atualizarValorTotal();
    });
    
    document.getElementById('valor_desconto').addEventListener('input', function() {
        console.log('💸 Desconto alterado - recalculando total...');
        atualizarValorTotal();
    });
    
    // Evento para cálculo do valor principal quando total muda
    document.getElementById('valor').addEventListener('input', function() {
        console.log('💰 Valor do recebimento alterado - recalculando principal...');
        atualizarValorPrincipal();
    });
    
    // Evento para cálculo da taxa
    document.getElementById('taxa').addEventListener('input', function() {
        console.log('📊 Taxa alterada - recalculando...');
        calcularTaxa();
    });
    
    // Quando o valor principal é alterado diretamente, recalcular o total
    document.getElementById('valor_principal').addEventListener('input', function() {
        console.log('🎯 Principal alterado - recalculando total...');
        atualizarValorTotal();
    });

    // Submissão do formulário de recebimento
    document.getElementById('formRecebimento').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('btnConfirmarRecebimento');
        const originalText = submitBtn.innerHTML;
        
        // Mostrar loading
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processando...';
        submitBtn.disabled = true;
        
        const formData = new FormData(this);
        
        // Log dos dados que estão sendo enviados
        console.log('📤 Dados sendo enviados:');
        for (let [key, value] of formData.entries()) {
            console.log(`   ${key}: ${value}`);
        }
        console.log('📍 URL da requisição:', this.action);
        console.log('🔐 CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
        
        // Verificar se todos os dados necessários estão presentes
        const requiredFields = ['forma_pagamento_id', 'conta_bancaria_id', 'valor', 'data_pagamento'];
        const missingFields = requiredFields.filter(field => !formData.get(field));
        
        if (missingFields.length > 0) {
            console.error('❌ Campos obrigatórios faltando:', missingFields);
            alert('Campos obrigatórios faltando: ' + missingFields.join(', '));
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            return;
        }
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('📡 Response recebida:', {
                status: response.status,
                statusText: response.statusText,
                headers: response.headers,
                ok: response.ok
            });
            
            // Se não for OK, vamos ver o conteúdo como texto primeiro
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('❌ Response não OK:', text);
                    throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}...`);
                });
            }
            
            return response.json().then(data => ({ status: response.status, data }));
        })
        .then(({ status, data }) => {
            console.log('✅ Dados recebidos:', data);
            if (data.success) {
                // Mostrar sucesso e recarregar página
                const toast = document.createElement('div');
                toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
                toast.style.zIndex = '9999';
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-check me-2"></i>
                            ${data.message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                document.body.appendChild(toast);
                
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
                
                // Voltar para a lista após 2 segundos
                setTimeout(() => {
                    window.location.href = '<?php echo e(route("comerciantes.empresas.financeiro.contas-receber.index", $empresa)); ?>';
                }, 2000);
                
            } else {
                // Mostrar erro
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show';
                alert.innerHTML = `
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                const cardBody = document.querySelector('.card-body');
                cardBody.insertBefore(alert, cardBody.firstChild);
                
                // Restaurar botão
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('❌ Erro detalhado:', {
                message: error.message,
                stack: error.stack,
                name: error.name
            });
            
            // Mostrar erro com mais detalhes
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-dismissible fade show';
            alert.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Erro ao processar recebimento:</strong><br>
                ${error.message}<br>
                <small class="text-muted">Verifique o console do navegador para mais detalhes.</small>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const cardBody = document.querySelector('.card-body');
            cardBody.insertBefore(alert, cardBody.firstChild);
            
            // Restaurar botão
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.comerciante', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/financeiro/contas-receber/pagamento.blade.php ENDPATH**/ ?>