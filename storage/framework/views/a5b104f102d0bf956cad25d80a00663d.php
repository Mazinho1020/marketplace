<?php $__env->startSection('title', 'Admin Fidelidade - MeuFinanceiro'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="container">
        <!-- Estatísticas Gerais -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-gift text-success" style="font-size: 2.5rem;"></i>
                        <h3 class="mt-2 text-success" id="total-programas">0</h3>
                        <p class="text-muted mb-0">Programas Ativos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-account-group text-primary" style="font-size: 2.5rem;"></i>
                        <h3 class="mt-2 text-primary" id="total-clientes">0</h3>
                        <p class="text-muted mb-0">Clientes Cadastrados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-credit-card text-warning" style="font-size: 2.5rem;"></i>
                        <h3 class="mt-2 text-warning" id="total-cartoes">0</h3>
                        <p class="text-muted mb-0">Cartões Emitidos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="mdi mdi-currency-usd text-info" style="font-size: 2.5rem;"></i>
                        <h3 class="mt-2 text-info" id="total-cashback">R$ 0</h3>
                        <p class="text-muted mb-0">Cashback Total</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Visão Geral das Tabelas -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="mdi mdi-gift"></i> Programas de Fidelidade
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="programas-table">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Carregando...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <a href="/admin/fidelidade/programas" class="btn btn-sm btn-success">
                                <i class="mdi mdi-plus"></i> Gerenciar Programas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="mdi mdi-account-group"></i> Clientes Recentes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Pontos</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="clientes-table">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Carregando...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <a href="/admin/fidelidade/clientes" class="btn btn-sm btn-primary">
                                <i class="mdi mdi-eye"></i> Ver Todos Clientes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="mdi mdi-cash-multiple"></i> Transações Recentes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Pontos</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody id="transacoes-table">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Carregando...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <a href="/admin/fidelidade/transacoes" class="btn btn-sm btn-info">
                                <i class="mdi mdi-eye"></i> Ver Todas Transações
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="mdi mdi-ticket-percent"></i> Cupons Ativos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Código</th>
                                        <th>Desconto</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="cupons-table">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Carregando...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <a href="/admin/fidelidade/cupons" class="btn btn-sm btn-warning">
                                <i class="mdi mdi-plus"></i> Gerenciar Cupons
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script>
// Carregar dados das tabelas via AJAX
document.addEventListener('DOMContentLoaded', function() {
carregarEstatisticas();
carregarTabelasPrevias();
});
function carregarEstatisticas() {
// Simulação - substituir por AJAX real
document.getElementById('total-programas').textContent = '5';
document.getElementById('total-clientes').textContent = '1,234';
document.getElementById('total-cartoes').textContent = '987';
document.getElementById('total-cashback').textContent = 'R$ 15,430';
}
function carregarTabelasPrevias() {
// Programas
document.getElementById('programas-table').innerHTML = `
<tr>
<td>1</td>
<td>Programa Padrão</td>
<td><span class="badge bg-success">Ativo</span></td>
<td>
<button class="btn btn-action btn-outline-primary btn-sm">
<i class="mdi mdi-eye"></i>
</button>
<button class="btn btn-action btn-outline-warning btn-sm">
<i class="mdi mdi-pencil"></i>
</button>
</td>
</tr>
`;
// Clientes
document.getElementById('clientes-table').innerHTML = `
<tr>
<td>1</td>
<td>João Silva</td>
<td>450</td>
<td>
<button class="btn btn-action btn-outline-primary btn-sm">
<i class="mdi mdi-eye"></i>
</button>
</td>
</tr>
`;
// Transações
document.getElementById('transacoes-table').innerHTML = `
<tr>
<td>1</td>
<td>João Silva</td>
<td>+50</td>
<td>Hoje</td>
</tr>
`;
// Cupons
document.getElementById('cupons-table').innerHTML = `
<tr>
<td>1</td>
<td>DESC10</td>
<td>10%</td>
<td><span class="badge bg-success">Ativo</span></td>
</tr>
`;
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.fidelidade', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/admin/fidelidade/dashboard.blade.php ENDPATH**/ ?>