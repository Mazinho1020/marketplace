@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4>Gerenciar Usuários</h4>
                    
                    @permission('usuarios.criar')
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Novo Usuário
                        </a>
                    @endpermission
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Papéis</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario->nome }}</td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>
                                        <span class="badge badge-{{ $usuario->status === 'ativo' ? 'success' : 'danger' }}">
                                            {{ ucfirst($usuario->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @foreach($usuario->getRoles() as $role)
                                            <span class="badge badge-info">{{ $role->nome }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @permission('usuarios.visualizar')
                                            <a href="{{ route('admin.users.show', $usuario) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endpermission
                                        
                                        @permission('usuarios.editar')
                                            <a href="{{ route('admin.users.edit', $usuario) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endpermission
                                        
                                        @permission('usuarios.gerenciar_permissoes')
                                            <a href="{{ route('admin.users.permissions', $usuario) }}" 
                                               class="btn btn-sm btn-secondary">
                                                <i class="fas fa-key"></i>
                                            </a>
                                        @endpermission
                                        
                                        @permission('usuarios.excluir')
                                            @if($usuario->id !== auth()->id())
                                                <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="deleteUser({{ $usuario->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        @endpermission
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $usuarios->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Verificar permissões via API
fetch('/api/admin/my-permissions')
    .then(response => response.json())
    .then(data => {
        const permissions = data.permissions;
        const roles = data.roles;
        
        console.log('Minhas permissões:', permissions);
        console.log('Meus papéis:', roles);
        
        // Mostrar/ocultar elementos baseado em permissões
        if (permissions.includes('configuracoes.seguranca')) {
            document.getElementById('admin-panel')?.style.setProperty('display', 'block');
        }
    });

function deleteUser(userId) {
    if (confirm('Tem certeza que deseja excluir este usuário?')) {
        // Implementar exclusão
        console.log('Excluir usuário:', userId);
    }
}
</script>
@endsection
@endsection
