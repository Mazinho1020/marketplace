@extends('layouts.app')

@section('title', 'Detalhes da Regra')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-eye text-primary me-2"></i>
                        Regra #{{ $regra->id }}
                    </h1>
                    <p class="text-muted mb-0">Detalhes da regra de cashback</p>
                </div>
                <div>
                    <a href="{{ route('fidelidade.regras.edit', $regra->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>
                        Editar
                    </a>
                    <a href="{{ route('fidelidade.regras.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informações Básicas -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Informações da Regra</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>ID:</strong><br>
                            <span class="text-muted">#{{ $regra->id }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Tipo de Regra:</strong><br>
                            @php
                            $tipoLabel = match($regra->tipo_regra) {
                            'categoria' => 'Por Categoria',
                            'produto' => 'Por Produto',
                            'dia_semana' => 'Dia da Semana',
                            'horario' => 'Por Horário',
                            'primeira_compra' => 'Primeira Compra',
                            default => 'Geral'
                            };
                            @endphp
                            <span class="badge bg-primary">{{ $tipoLabel }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Percentual de Cashback:</strong><br>
                            <span class="text-success fs-5 fw-bold">{{ number_format($regra->percentual_cashback, 2)
                                }}%</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Valor Máximo:</strong><br>
                            @if($regra->valor_maximo_cashback)
                            <span class="text-muted">R$ {{ number_format($regra->valor_maximo_cashback, 2, ',', '.')
                                }}</span>
                            @else
                            <span class="text-muted">Sem limite</span>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Status:</strong><br>
                            @if($regra->ativo)
                            <span class="badge bg-success">Ativa</span>
                            @else
                            <span class="badge bg-danger">Inativa</span>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Empresa ID:</strong><br>
                            <span class="text-muted">{{ $regra->empresa_id ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Condições Específicas -->
            @if($regra->dia_semana || $regra->horario_inicio || $regra->referencia_id)
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Condições Específicas</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($regra->dia_semana !== null)
                        <div class="col-md-6 mb-3">
                            <strong>Dia da Semana:</strong><br>
                            @php
                            $diasSemana = [
                            0 => 'Domingo', 1 => 'Segunda-feira', 2 => 'Terça-feira',
                            3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira', 6 => 'Sábado'
                            ];
                            @endphp
                            <span class="text-muted">{{ $diasSemana[$regra->dia_semana] ?? 'N/A' }}</span>
                        </div>
                        @endif

                        @if($regra->horario_inicio && $regra->horario_fim)
                        <div class="col-md-6 mb-3">
                            <strong>Horário:</strong><br>
                            <span class="text-muted">
                                {{ substr($regra->horario_inicio, 0, 5) }} às {{ substr($regra->horario_fim, 0, 5) }}
                            </span>
                        </div>
                        @endif

                        @if($regra->referencia_id)
                        <div class="col-md-6 mb-3">
                            <strong>ID de Referência:</strong><br>
                            <span class="text-muted">{{ $regra->referencia_id }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Estatísticas e Ações -->
        <div class="col-lg-4">
            <!-- Estatísticas de Uso -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Estatísticas de Uso</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Criada em:</strong><br>
                        <span class="text-muted">{{ \Carbon\Carbon::parse($regra->created_at)->format('d/m/Y H:i')
                            }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Última Atualização:</strong><br>
                        <span class="text-muted">{{ \Carbon\Carbon::parse($regra->updated_at)->format('d/m/Y H:i')
                            }}</span>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <strong>Total de Usos:</strong><br>
                        <span class="text-primary fs-4 fw-bold">{{ $estatisticasUso['total_usos'] ?? 0 }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Cashback Distribuído:</strong><br>
                        <span class="text-success fw-bold">R$ {{ number_format($estatisticasUso['cashback_distribuido']
                            ?? 0, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Ações</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($regra->ativo)
                        <button class="btn btn-warning" onclick="toggleRegra({{ $regra->id }}, false)">
                            <i class="fas fa-pause me-2"></i>Desativar Regra
                        </button>
                        @else
                        <button class="btn btn-success" onclick="toggleRegra({{ $regra->id }}, true)">
                            <i class="fas fa-play me-2"></i>Ativar Regra
                        </button>
                        @endif

                        <a href="{{ route('fidelidade.regras.edit', $regra->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Editar Regra
                        </a>

                        <button class="btn btn-info" onclick="testarRegra({{ $regra->id }})">
                            <i class="fas fa-flask me-2"></i>Testar Regra
                        </button>

                        <a href="{{ route('fidelidade.regras.duplicar', $regra->id) }}" class="btn btn-secondary">
                            <i class="fas fa-copy me-2"></i>Duplicar Regra
                        </a>

                        <hr>

                        <button class="btn btn-danger" onclick="confirmarExclusao({{ $regra->id }})">
                            <i class="fas fa-trash me-2"></i>Excluir Regra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Teste -->
<div class="modal fade" id="modalTeste" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Testar Regra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formTeste">
                    <div class="mb-3">
                        <label class="form-label">Valor da Compra</label>
                        <input type="number" class="form-control" id="valorCompra" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cliente ID (opcional)</label>
                        <input type="number" class="form-control" id="clienteId">
                    </div>
                </form>
                <div id="resultadoTeste" class="mt-3" style="display: none;">
                    <div class="alert alert-info">
                        <h6>Resultado do Teste:</h6>
                        <div id="resultadoTexto"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="executarTeste()">Testar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleRegra(id, ativar) {
    const acao = ativar ? 'ativar' : 'desativar';
    if (confirm(`Tem certeza que deseja ${acao} esta regra?`)) {
        const endpoint = ativar ? 'ativar' : 'desativar';
        fetch(`/fidelidade/regras/${id}/${endpoint}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(`Erro ao ${acao} regra`);
            }
        })
        .catch(error => {
            alert(`Erro ao ${acao} regra`);
            console.error('Error:', error);
        });
    }
}

function testarRegra(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalTeste'));
    modal.show();
}

function executarTeste() {
    const valorCompra = document.getElementById('valorCompra').value;
    const clienteId = document.getElementById('clienteId').value;
    
    if (!valorCompra) {
        alert('Por favor, informe o valor da compra');
        return;
    }

    fetch(`/fidelidade/regras/{{ $regra->id }}/testar`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            valor_compra: valorCompra,
            cliente_id: clienteId
        })
    })
    .then(response => response.json())
    .then(data => {
        const resultadoDiv = document.getElementById('resultadoTeste');
        const resultadoTexto = document.getElementById('resultadoTexto');
        
        if (data.success) {
            resultadoTexto.innerHTML = `
                <strong>Valor da Compra:</strong> R$ ${parseFloat(valorCompra).toFixed(2)}<br>
                <strong>Cashback Calculado:</strong> R$ ${data.cashback_calculado}<br>
                <strong>Percentual Aplicado:</strong> ${data.percentual_aplicado}%
            `;
        } else {
            resultadoTexto.innerHTML = `<span class="text-danger">Erro: ${data.message}</span>`;
        }
        
        resultadoDiv.style.display = 'block';
    })
    .catch(error => {
        alert('Erro ao testar regra');
        console.error('Error:', error);
    });
}

function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir esta regra? Esta ação não pode ser desfeita.')) {
        fetch(`/fidelidade/regras/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/fidelidade/regras';
            } else {
                alert('Erro ao excluir regra');
            }
        })
        .catch(error => {
            alert('Erro ao excluir regra');
            console.error('Error:', error);
        });
    }
}
</script>
@endsection