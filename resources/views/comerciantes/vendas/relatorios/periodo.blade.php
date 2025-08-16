@extends('comerciantes.layout')

@section('title', 'Relatório de Vendas por Período')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Relatório de Vendas por Período</h1>
                    <p class="text-muted">Análise detalhada das vendas por período específico</p>
                </div>
                <div>
                    <a href="{{ route('comerciantes.empresas.vendas.dashboard', $empresa) }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar"></i> Dashboard
                    </a>
                    <a href="{{ route('comerciantes.empresas.vendas.gerenciar.index', $empresa) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> Gerenciar Vendas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros do Relatório</h5>
        </div>
        <div class="card-body">
            <form id="formRelatorio" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <label for="data_inicio" class="form-label">Data Início *</label>
                        <input type="date" id="data_inicio" name="data_inicio" class="form-control" 
                               value="{{ request('data_inicio', date('Y-m-01')) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="data_fim" class="form-label">Data Fim *</label>
                        <input type="date" id="data_fim" name="data_fim" class="form-control" 
                               value="{{ request('data_fim', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">Todos</option>
                            <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="confirmada" {{ request('status') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                            <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            <option value="entregue" {{ request('status') == 'entregue' ? 'selected' : '' }}>Entregue</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="tipo_venda" class="form-label">Tipo</label>
                        <select id="tipo_venda" name="tipo_venda" class="form-control">
                            <option value="">Todos</option>
                            <option value="balcao" {{ request('tipo_venda') == 'balcao' ? 'selected' : '' }}>Balcão</option>
                            <option value="delivery" {{ request('tipo_venda') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                            <option value="online" {{ request('tipo_venda') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="telefone" {{ request('tipo_venda') == 'telefone' ? 'selected' : '' }}>Telefone</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="w-100">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Gerar Relatório
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPeriodo('hoje')">Hoje</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPeriodo('ontem')">Ontem</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPeriodo('semana')">Esta Semana</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPeriodo('mes')">Este Mês</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setPeriodo('mes_passado')">Mês Passado</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resultado do Relatório -->
    <div id="resultadoRelatorio">
        <div class="text-center py-5">
            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
            <h5>Selecione o período e clique em "Gerar Relatório"</h5>
            <p class="text-muted">Configure os filtros acima e gere seu relatório personalizado</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Definir períodos predefinidos
function setPeriodo(periodo) {
    const hoje = new Date();
    let dataInicio, dataFim;
    
    switch(periodo) {
        case 'hoje':
            dataInicio = dataFim = hoje.toISOString().split('T')[0];
            break;
            
        case 'ontem':
            const ontem = new Date(hoje);
            ontem.setDate(hoje.getDate() - 1);
            dataInicio = dataFim = ontem.toISOString().split('T')[0];
            break;
            
        case 'semana':
            const inicioSemana = new Date(hoje);
            inicioSemana.setDate(hoje.getDate() - hoje.getDay());
            dataInicio = inicioSemana.toISOString().split('T')[0];
            dataFim = hoje.toISOString().split('T')[0];
            break;
            
        case 'mes':
            dataInicio = new Date(hoje.getFullYear(), hoje.getMonth(), 1).toISOString().split('T')[0];
            dataFim = hoje.toISOString().split('T')[0];
            break;
            
        case 'mes_passado':
            const mesPassado = new Date(hoje.getFullYear(), hoje.getMonth() - 1, 1);
            const ultimoDiaMesPassado = new Date(hoje.getFullYear(), hoje.getMonth(), 0);
            dataInicio = mesPassado.toISOString().split('T')[0];
            dataFim = ultimoDiaMesPassado.toISOString().split('T')[0];
            break;
    }
    
    document.getElementById('data_inicio').value = dataInicio;
    document.getElementById('data_fim').value = dataFim;
}

// Gerar relatório via AJAX
document.getElementById('formRelatorio').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const params = new URLSearchParams(formData).toString();
    
    // Mostrar loading
    document.getElementById('resultadoRelatorio').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Carregando...</span>
            </div>
            <p class="mt-3">Gerando relatório...</p>
        </div>
    `;
    
    // Simular carregamento de dados (em produção seria uma requisição AJAX real)
    setTimeout(() => {
        gerarRelatorioDemo(formData);
    }, 1500);
});

// Função demo para gerar relatório (substituir por AJAX real)
function gerarRelatorioDemo(formData) {
    const dataInicio = formData.get('data_inicio');
    const dataFim = formData.get('data_fim');
    const status = formData.get('status') || 'Todos';
    const tipoVenda = formData.get('tipo_venda') || 'Todos';
    
    // Dados simulados (em produção viria do backend)
    const dados = {
        totalVendas: Math.floor(Math.random() * 100) + 50,
        valorTotal: Math.random() * 50000 + 10000,
        ticketMedio: 0,
        vendasPorDia: [],
        vendasPorStatus: {
            confirmada: Math.floor(Math.random() * 40) + 30,
            pendente: Math.floor(Math.random() * 15) + 5,
            cancelada: Math.floor(Math.random() * 10) + 2,
            entregue: Math.floor(Math.random() * 20) + 10
        },
        vendasPorTipo: {
            balcao: Math.floor(Math.random() * 30) + 20,
            delivery: Math.floor(Math.random() * 25) + 15,
            online: Math.floor(Math.random() * 20) + 10,
            telefone: Math.floor(Math.random() * 15) + 5
        }
    };
    
    dados.ticketMedio = dados.valorTotal / dados.totalVendas;
    
    // Gerar dados por dia (simulado)
    const inicio = new Date(dataInicio);
    const fim = new Date(dataFim);
    const diasDiferenca = Math.ceil((fim - inicio) / (1000 * 60 * 60 * 24)) + 1;
    
    for (let i = 0; i < diasDiferenca; i++) {
        const data = new Date(inicio);
        data.setDate(inicio.getDate() + i);
        dados.vendasPorDia.push({
            data: data.toLocaleDateString('pt-BR'),
            vendas: Math.floor(Math.random() * 20) + 5,
            valor: Math.random() * 5000 + 1000
        });
    }
    
    mostrarRelatorio(dados, dataInicio, dataFim, status, tipoVenda);
}

function mostrarRelatorio(dados, dataInicio, dataFim, status, tipoVenda) {
    const html = `
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Relatório de ${new Date(dataInicio).toLocaleDateString('pt-BR')} a ${new Date(dataFim).toLocaleDateString('pt-BR')}</h5>
                        <div>
                            <button onclick="exportarRelatorio('excel')" class="btn btn-sm btn-success">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                            <button onclick="exportarRelatorio('pdf')" class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <h4 class="text-primary">${dados.totalVendas}</h4>
                                <p class="text-muted">Total de Vendas</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-success">R$ ${dados.valorTotal.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</h4>
                                <p class="text-muted">Valor Total</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-info">R$ ${dados.ticketMedio.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</h4>
                                <p class="text-muted">Ticket Médio</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-warning">${dados.vendasPorStatus.confirmada}</h4>
                                <p class="text-muted">Vendas Confirmadas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Vendas por Dia</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Qtd Vendas</th>
                                        <th>Valor Total</th>
                                        <th>Ticket Médio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${dados.vendasPorDia.map(dia => `
                                        <tr>
                                            <td>${dia.data}</td>
                                            <td>${dia.vendas}</td>
                                            <td>R$ ${dia.valor.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                                            <td>R$ ${(dia.valor / dia.vendas).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Vendas por Status</h5>
                    </div>
                    <div class="card-body">
                        ${Object.entries(dados.vendasPorStatus).map(([status, total]) => `
                            <div class="d-flex justify-content-between mb-2">
                                <span>${status.charAt(0).toUpperCase() + status.slice(1)}:</span>
                                <strong>${total}</strong>
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Vendas por Tipo</h5>
                    </div>
                    <div class="card-body">
                        ${Object.entries(dados.vendasPorTipo).map(([tipo, total]) => `
                            <div class="d-flex justify-content-between mb-2">
                                <span>${tipo.charAt(0).toUpperCase() + tipo.slice(1)}:</span>
                                <strong>${total}</strong>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('resultadoRelatorio').innerHTML = html;
}

function exportarRelatorio(formato) {
    const dataInicio = document.getElementById('data_inicio').value;
    const dataFim = document.getElementById('data_fim').value;
    const status = document.getElementById('status').value;
    const tipoVenda = document.getElementById('tipo_venda').value;
    
    const params = new URLSearchParams({
        formato,
        data_inicio: dataInicio,
        data_fim: dataFim,
        status,
        tipo_venda: tipoVenda
    });
    
    const url = `{{ route('comerciantes.empresas.vendas.relatorio.exportar', $empresa) }}?${params}`;
    window.open(url, '_blank');
}

// Carregar relatório do mês atual ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    // Se tem parâmetros na URL, gerar o relatório automaticamente
    if (window.location.search) {
        document.getElementById('formRelatorio').dispatchEvent(new Event('submit'));
    }
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .card-header .btn,
    .btn-group,
    form {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endpush