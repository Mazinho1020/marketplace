# 🔐 Sistema de Permissões Automáticas - Guia Completo

## 📋 Visão Geral

O sistema de permissões automáticas foi projetado para funcionar em **todo o seu site** sem necessidade de configuração manual em cada função. Aqui está como funciona:

## 🚀 Como Ativar para Todo o Site

### 1. Execute o Comando de Configuração

```bash
php artisan permissions:setup
```

Este comando configurará automaticamente:

- ✅ Middleware automático nas rotas
- ✅ Registrará o sistema no Laravel
- ✅ Configurará grupos de rotas protegidas

### 2. Para Novas Aplicações/Módulos

**NÃO precisa chamar métodos manualmente!** Apenas use o middleware nos grupos de rotas:

```php
// routes/web.php ou routes/comerciantes.php
Route::middleware(['comerciantes.protected'])->group(function () {
    // TODAS as rotas aqui serão automaticamente protegidas
    Route::resource('produtos', ProdutoController::class);
    Route::resource('vendas', VendaController::class);
    Route::resource('clientes', ClienteController::class);
});
```

## 🎯 Como o Sistema Funciona Automaticamente

### Mapeamento Automático de Permissões

O sistema mapeia automaticamente:

| Rota               | Método HTTP | Permissão Gerada     |
| ------------------ | ----------- | -------------------- |
| `produtos.index`   | GET         | `produto.visualizar` |
| `produtos.create`  | GET         | `produto.criar`      |
| `produtos.store`   | POST        | `produto.criar`      |
| `produtos.show`    | GET         | `produto.visualizar` |
| `produtos.edit`    | GET         | `produto.editar`     |
| `produtos.update`  | PUT/PATCH   | `produto.editar`     |
| `produtos.destroy` | DELETE      | `produto.excluir`    |

### Exemplos Práticos

```php
// ❌ ANTES: Você tinha que fazer isso em cada método
public function index()
{
    if (!auth()->user()->hasPermission('produto.visualizar')) {
        abort(403);
    }
    // ... resto do código
}

// ✅ AGORA: Automático! Apenas escreva sua lógica
public function index()
{
    // Permissão já verificada automaticamente!
    return view('produtos.index', compact('produtos'));
}
```

## 🛠️ Uso nas Views (Blade)

### Novas Diretivas Blade Disponíveis

```blade
{{-- Verificar permissão simples --}}
@permission('produto.criar')
    <a href="{{ route('produtos.create') }}" class="btn btn-primary">
        Novo Produto
    </a>
@endpermission

{{-- Verificar múltiplas permissões (qualquer uma) --}}
@anypermission('produto.editar', 'produto.excluir')
    <div class="btn-group">
        <!-- Botões de ação -->
    </div>
@endanypermission

{{-- Verificar permissão específica de empresa --}}
@empresaPermission('usuario.gerenciar', $empresa->id)
    <button class="btn btn-success">
        Gerenciar Usuários
    </button>
@endempresaPermission

{{-- Verificar role/função --}}
@role('administrador')
    <div class="admin-panel">
        <!-- Painel administrativo -->
    </div>
@endrole
```

## 🎮 Configurações Avançadas

### 1. Personalizar Mapeamento de Recursos

```php
// app/Http/Middleware/AutoPermissionCheck.php
protected function extractResourceFromRoute(string $routeName): string
{
    $resourceMap = [
        'empresas' => 'empresa',
        'usuarios' => 'usuario',
        'produtos' => 'produto',        // ← Adicione novos recursos aqui
        'vendas' => 'venda',
        'clientes' => 'cliente',
        'relatorios' => 'relatorio',
    ];
    // ...
}
```

### 2. Rotas Públicas (Sem Verificação)

```php
protected function isPublicRoute(string $routeName): bool
{
    $publicRoutes = [
        'comerciantes.dashboard',
        'comerciantes.profile',
        'api.public.status',            // ← Adicione rotas públicas aqui
        'produtos.catalog',             // ← Catálogo público
    ];

    return in_array($routeName, $publicRoutes);
}
```

### 3. Ações Personalizadas

```php
protected function determineUserPermission(string $action, string $httpMethod): string
{
    $customActions = [
        'index' => 'visualizar',
        'show' => 'visualizar',
        'create' => 'criar',
        'store' => 'criar',
        'edit' => 'editar',
        'update' => 'editar',
        'destroy' => 'excluir',

        // ← Adicione ações personalizadas
        'duplicate' => 'criar',
        'archive' => 'editar',
        'export' => 'visualizar',
        'import' => 'criar',
    ];

    return $customActions[$action] ?? 'visualizar';
}
```

## 📊 Benefícios para Novas Aplicações

### ✅ Vantagens

1. **Zero Configuração**: Novas funcionalidades já vêm protegidas
2. **Consistência**: Todas as permissões seguem o mesmo padrão
3. **Manutenibilidade**: Um local central para gerenciar permissões
4. **Escalabilidade**: Funciona automaticamente com qualquer número de recursos
5. **Segurança**: Por padrão, tudo é protegido (fail-safe)

### 🎯 Para Desenvolvedores

```php
// Criando um novo módulo? É só isso:

// 1. Criar o controller
class RelatorioController extends Controller
{
    public function index() { /* sua lógica */ }
    public function create() { /* sua lógica */ }
    public function store() { /* sua lógica */ }
    // ... outros métodos
}

// 2. Adicionar as rotas no grupo protegido
Route::middleware(['comerciantes.protected'])->group(function () {
    Route::resource('relatorios', RelatorioController::class);
});

// 3. Pronto! Permissões automáticas:
// - relatorio.visualizar
// - relatorio.criar
// - relatorio.editar
// - relatorio.excluir
```

## 🔄 Migração de Código Existente

### Removendo Verificações Manuais

```php
// ❌ Código antigo (pode remover)
public function destroy($id)
{
    if (!auth()->user()->hasPermission('produto.excluir')) {
        return redirect()->back()->with('error', 'Sem permissão');
    }

    // lógica de exclusão
}

// ✅ Código novo (limpo)
public function destroy($id)
{
    // lógica de exclusão (permissão já verificada)
}
```

## 🎨 Interface de Usuário Inteligente

As views se adaptam automaticamente às permissões:

```blade
{{-- Botões aparecem apenas se o usuário tem permissão --}}
<div class="card-header">
    <h5>Produtos</h5>
    <div class="btn-group">
        @permission('produto.criar')
            <a href="{{ route('produtos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo
            </a>
        @endpermission

        @permission('produto.exportar')
            <a href="{{ route('produtos.export') }}" class="btn btn-success">
                <i class="fas fa-download"></i> Exportar
            </a>
        @endpermission
    </div>
</div>
```

## 🚀 Resultado Final

- ✅ **Todo o site protegido automaticamente**
- ✅ **Novas funcionalidades são seguras por padrão**
- ✅ **Zero código repetitivo de verificação**
- ✅ **Interface se adapta às permissões do usuário**
- ✅ **Fácil manutenção e escalabilidade**

**Resumo**: Após a configuração inicial, você nunca mais precisa se preocupar com permissões manualmente. O sistema cuida de tudo automaticamente! 🎉
