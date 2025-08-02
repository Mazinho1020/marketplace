@extends('admin.layouts.app')

@section('title', 'Merchants')
@section('page-title', 'Gerenciamento de Merchants')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Merchants</h4>
            <p class="text-muted mb-0">Gerencie todos os merchants da plataforma</p>
        </div>
        <div>
            <button class="btn btn-outline-primary me-2" onclick="exportMerchants()">
                <i class="fas fa-download me-1"></i>
                Exportar
            </button>
            <a href="{{ route('admin.merchants.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Novo Merchant
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="text-center">
                <div class="stats-icon mx-auto mb-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-store"></i>
                </div>
                <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                <p class="text-muted mb-0">Total Merchants</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="text-center">
                <div class="stats-icon mx-auto mb-2" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="mb-0">{{ number_format($stats['active']) }}</h3>
                <p class="text-muted mb-0">Ativos</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="text-center">
                <div class="stats-icon mx-auto mb-2" style="background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="mb-0">{{ number_format($stats['pending']) }}</h3>
                <p class="text-muted mb-0">Pendentes</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="text-center">
                <div class="stats-icon mx-auto mb-2" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                    <i class="fas fa-ban"></i>
                </div>
                <h3 class="mb-0">{{ number_format($stats['inactive']) }}</h3>
                <p class="text-muted mb-0">Inativos</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="table-container mb-4">
    <form method="GET" action="{{ route('admin.merchants.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Buscar</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Nome, email, empresa...">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspenso</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Plano</label>
                <select class="form-select" name="plan">
                    <option value="">Todos</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->code }}" {{ request('plan') === $plan->code ? 'selected' : '' }}>
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Data Cadastro</label>
                <input type="date" class="form-control" name="created_from" value="{{ request('created_from') }}">
            </div>
            
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <input type="date" class="form-control" name="created_to" value="{{ request('created_to') }}">
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i>
                    Filtrar
                </button>
                <a href="{{ route('admin.merchants.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Merchants Table -->
<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>
                        <a href="{{ route('admin.merchants.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" 
                           class="text-decoration-none text-dark">
                            Merchant
                            @if(request('sort') === 'name')
                                <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>Contato</th>
                    <th>Plano</th>
                    <th>Status</th>
                    <th>
                        <a href="{{ route('admin.merchants.index', array_merge(request()->all(), ['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" 
                           class="text-decoration-none text-dark">
                            Cadastro
                            @if(request('sort') === 'created_at')
                                <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </a>
                    </th>
                    <th>Receita (30d)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($merchants as $merchant)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                <span class="text-white fw-bold">{{ substr($merchant->business_name ?: $merchant->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $merchant->business_name ?: $merchant->name }}</div>
                                <small class="text-muted">ID: {{ $merchant->id }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <div>{{ $merchant->email }}</div>
                            @if($merchant->phone)
                                <small class="text-muted">{{ $merchant->phone }}</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($merchant->subscription)
                            <span class="badge bg-light text-dark">{{ $merchant->subscription->plan_name }}</span>
                            <br>
                            <small class="text-muted">R$ {{ number_format($merchant->subscription->amount, 2, ',', '.') }}/mês</small>
                        @else
                            <span class="badge bg-secondary">Sem plano</span>
                        @endif
                    </td>
                    <td>
                        @switch($merchant->status)
                            @case('active')
                                <span class="badge bg-success">Ativo</span>
                                @break
                            @case('pending')
                                <span class="badge bg-warning">Pendente</span>
                                @break
                            @case('inactive')
                                <span class="badge bg-secondary">Inativo</span>
                                @break
                            @case('suspended')
                                <span class="badge bg-danger">Suspenso</span>
                                @break
                        @endswitch
                    </td>
                    <td>
                        <div>{{ $merchant->created_at->format('d/m/Y') }}</div>
                        <small class="text-muted">{{ $merchant->created_at->diffForHumans() }}</small>
                    </td>
                    <td>
                        <div class="fw-bold text-success">R$ {{ number_format($merchant->monthly_revenue ?: 0, 2, ',', '.') }}</div>
                        <small class="text-muted">{{ number_format($merchant->monthly_transactions ?: 0) }} transações</small>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.merchants.show', $merchant) }}">
                                        <i class="fas fa-eye me-2"></i>
                                        Visualizar
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.merchants.edit', $merchant) }}">
                                        <i class="fas fa-edit me-2"></i>
                                        Editar
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.merchants.usage', $merchant) }}">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        Uso da API
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @if($merchant->status === 'active')
                                    <li>
                                        <button class="dropdown-item text-warning" onclick="suspendMerchant({{ $merchant->id }})">
                                            <i class="fas fa-pause me-2"></i>
                                            Suspender
                                        </button>
                                    </li>
                                @elseif($merchant->status === 'suspended')
                                    <li>
                                        <button class="dropdown-item text-success" onclick="activateMerchant({{ $merchant->id }})">
                                            <i class="fas fa-play me-2"></i>
                                            Reativar
                                        </button>
                                    </li>
                                @endif
                                <li>
                                    <button class="dropdown-item text-danger" onclick="deleteMerchant({{ $merchant->id }})">
                                        <i class="fas fa-trash me-2"></i>
                                        Excluir
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Nenhum merchant encontrado</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($merchants->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Mostrando {{ $merchants->firstItem() }} a {{ $merchants->lastItem() }} de {{ $merchants->total() }} resultados
            </div>
            <div>
                {{ $merchants->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function exportMerchants() {
    const params = new URLSearchParams(window.location.search);
    params.append('export', '1');
    window.location.href = '{{ route("admin.merchants.index") }}?' + params.toString();
}

function suspendMerchant(id) {
    if (confirm('Tem certeza que deseja suspender este merchant?')) {
        axios.patch(`/admin/merchants/${id}/suspend`)
            .then(response => {
                if (response.data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                alert('Erro ao suspender merchant');
            });
    }
}

function activateMerchant(id) {
    if (confirm('Tem certeza que deseja reativar este merchant?')) {
        axios.patch(`/admin/merchants/${id}/activate`)
            .then(response => {
                if (response.data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                alert('Erro ao reativar merchant');
            });
    }
}

function deleteMerchant(id) {
    if (confirm('Tem certeza que deseja excluir este merchant? Esta ação não pode ser desfeita.')) {
        axios.delete(`/admin/merchants/${id}`)
            .then(response => {
                if (response.data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                alert('Erro ao excluir merchant');
            });
    }
}
</script>
@endpush
