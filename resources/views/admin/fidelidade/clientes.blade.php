@extends('layouts.admin')

@section('title', 'Clientes Fidelidade')

@php
    $pageTitle = 'Clientes Fidelidade';
    $breadcrumbs = [
        ['title' => 'Admin', 'url' => route('admin.dashboard')],
        ['title' => 'Fidelidade', 'url' => route('admin.fidelidade.dashboard')],
        ['title' => 'Clientes', 'url' => '#']
    ];
@endphp

@section('content')
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">
                    <i class="mdi mdi-account-group text-primary"></i> Sistema de Clientes
                </h2>
                <p class="text-muted mb-0">Visualização geral de todos os clientes do programa de fidelidade</p>
            </div>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovoCliente">
                    <i class="mdi mdi-plus"></i> Novo Cliente
                </button>
            </div>
        </div>
    </div>
</div>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total de Clientes</h6>
                            <h4 class="mb-0">{{ $stats['total_clientes'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-account-group text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Clientes Ativos</h6>
                            <h4 class="mb-0">{{ $stats['clientes_ativos'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Clientes Inativos</h6>
                            <h4 class="mb-0">{{ $stats['clientes_inativos'] }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-clock text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Saldo Total</h6>
                            <h4 class="mb-0">R$ {{ number_format($stats['saldo_total'], 2, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-currency-usd text-danger" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="table-container">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" class="form-control" placeholder="Buscar cliente...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option value="">Todos os Status</option>
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option value="">Todos os Níveis</option>
                        <option value="bronze">Bronze</option>
                        <option value="prata">Prata</option>
                        <option value="ouro">Ouro</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="nome">Ordenar por Nome</option>
                        <option value="data">Ordenar por Data</option>
                        <option value="saldo">Ordenar por Saldo</option>
                        <option value="xp">Ordenar por XP</option>
                    </select>
                </div>
            </div>

            <!-- Tabela de Clientes -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="select-all">
                            </th>
                            <th>Cliente</th>
                            <th>Nível</th>
                            <th>Saldo/XP</th>
                            <th>Status</th>
                            <th>Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                        <tr>
                            <td><input type="checkbox" class="form-check-input"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar">
                                        <i class="mdi mdi-account-circle text-white" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="ms-2">
                                        <strong>{{ $cliente->nome }} {{ $cliente->sobrenome }}</strong>
                                        <br><small class="text-muted">ID: {{ $cliente->id }}</small>
                                        @if($cliente->email)
                                            <br><small class="text-muted">{{ $cliente->email }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="nivel-badge {{ strtolower($cliente->nivel_atual ?? 'bronze') }}">
                                    @if(($cliente->nivel_atual ?? 'bronze') == 'bronze')
                                        <i class="mdi mdi-medal"></i>
                                    @elseif($cliente->nivel_atual == 'prata')
                                        <i class="mdi mdi-medal"></i>
                                    @elseif($cliente->nivel_atual == 'ouro')
                                        <i class="mdi mdi-medal"></i>
                                    @endif
                                    {{ ucfirst($cliente->nivel_atual ?? 'Bronze') }}
                                </div>
                            </td>
                            <td>
                                <strong class="valor-positivo">{{ $cliente->xp_total ?? 0 }} XP</strong>
                                <br><small class="text-muted">R$ {{ number_format($cliente->saldo_total_disponivel ?? 0, 2, ',', '.') }}</small>
                            </td>
                            <td>
                                @if($cliente->ativo)
                                    <span class="badge bg-success badge-status">Ativo</span>
                                @else
                                    <span class="badge bg-warning badge-status">Inativo</span>
                                @endif
                                
                                @if($cliente->status_carteira == 'ativa')
                                    <br><small class="badge bg-info badge-status">Fidelidade</small>
                                @elseif($cliente->status_carteira)
                                    <br><small class="badge bg-secondary badge-status">{{ ucfirst($cliente->status_carteira) }}</small>
                                @endif
                            </td>
                            <td><small>{{ \Carbon\Carbon::parse($cliente->created_at)->format('d/m/Y') }}</small></td>
                            <td>
                                <button class="btn btn-action btn-outline-primary" title="Ver Perfil">
                                    <i class="mdi mdi-eye"></i>
                                </button>
                                <button class="btn btn-action btn-outline-warning" title="Editar">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button class="btn btn-action btn-outline-success" title="Adicionar Pontos">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                                <button class="btn btn-action btn-outline-info" title="Histórico">
                                    <i class="mdi mdi-history"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="mdi mdi-account-group text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-2 text-muted">Nenhum cliente encontrado</p>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovoCliente">
                                    <i class="mdi mdi-plus"></i> Cadastrar Primeiro Cliente
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="row">
                <div class="col-md-6">
                    <div class="pagination-info">
                        <small class="text-muted">
                            Mostrando <span id="showing-from">{{ $clientes->firstItem() ?? 0 }}</span> a <span id="showing-to">{{ $clientes->lastItem() ?? 0 }}</span> de <span id="total-records">{{ $clientes->total() ?? 0 }}</span> registros
                        </small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        {{ $clientes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo Cliente -->
<div class="modal fade" id="modalNovoCliente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-account-plus"></i> Novo Cliente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information"></i>
                    Esta é uma página administrativa apenas para visualização. 
                    Para cadastrar novos clientes, acesse o sistema de gestão completo.
                </div>
                <form id="form-novo-cliente">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" class="form-control" name="telefone" placeholder="(11) 99999-9999">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CPF</label>
                            <input type="text" class="form-control" name="cpf" placeholder="000.000.000-00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Programa de Fidelidade *</label>
                            <select class="form-select" name="programa_id" required>
                                <option value="">Selecione...</option>
                                <option value="1">Programa Geral</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nível Inicial</label>
                            <select class="form-select" name="nivel">
                                <option value="bronze">Bronze</option>
                                <option value="prata">Prata</option>
                                <option value="ouro">Ouro</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="salvarCliente()">
                    <i class="mdi mdi-check"></i> Salvar Cliente
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function salvarCliente() {
        showToast('Cliente salvo com sucesso!', 'success');
    }
</script>
@endsection
