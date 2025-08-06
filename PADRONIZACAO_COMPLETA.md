# 📋 **DOCUMENTO DE PADRONIZAÇÃO - MARKETPLACE MAZINHO1020**

## **Guia Completo de Desenvolvimento e Estilo**

---

## 📚 **ÍNDICE**

1. [Estrutura de Pastas](#estrutura-de-pastas)
2. [Padrões de Backend (Laravel)](#padrões-de-backend-laravel)
3. [Padrões de Frontend (Theme Hyper)](#padrões-de-frontend-theme-hyper)
4. [Padrões de Código](#padrões-de-código)
5. [Base de Dados](#base-de-dados) 📖 **Ver**: [`PADRAO_BANCO_DADOS.md`](./PADRAO_BANCO_DADOS.md)
6. [Sistema de Permissões Automáticas](#sistema-de-permissões-automáticas) 🔐 **Ver**: [`SISTEMA_PERMISSOES_AUTOMATICAS.md`](./SISTEMA_PERMISSOES_AUTOMATICAS.md)
7. [Validações e Segurança](#validações-e-segurança)
8. [Configurações e Ambiente](#configurações-e-ambiente)
9. [Sistema de Fidelidade](#sistema-de-fidelidade)

### **📋 Documentos Auxiliares**

- [`PADRAO_BANCO_DADOS.md`](./PADRAO_BANCO_DADOS.md) - Padrão completo para banco de dados
- [`CHECKLIST_BANCO_DADOS.md`](./CHECKLIST_BANCO_DADOS.md) - Checklist rápido para banco de dados
- [`SISTEMA_PERMISSOES_AUTOMATICAS.md`](./SISTEMA_PERMISSOES_AUTOMATICAS.md) - Sistema de permissões automáticas para todo o site
- [`TESTE_SISTEMA_PERMISSOES.md`](./TESTE_SISTEMA_PERMISSOES.md) - Guia de teste do sistema de permissões

---

## 🗂️ **ESTRUTURA DE PASTAS**

### **Estrutura Principal (OBRIGATÓRIA)**

```
/marketplace/
├── app/
│   ├── Models/                    # Modelos organizados por domínio
│   │   ├── Business/             # Modelos relacionados a empresas
│   │   │   └── Business.php      # Substitui Empresa.php
│   │   ├── Finance/              # Modelos relacionados a finanças
│   │   ├── PDV/                  # Modelos relacionados ao PDV
│   │   ├── Cliente/              # Modelos relacionados a clientes
│   │   ├── Delivery/             # Modelos relacionados a entregas
│   │   └── Fidelidade/           # Sistema de fidelidade e cashback
│   │
│   ├── Http/
│   │   ├── Controllers/          # Organizados por tipo de usuário
│   │   │   ├── Admin/           # Painel administrativo
│   │   │   ├── Merchant/        # Painel do lojista
│   │   │   ├── Customer/        # Área do cliente
│   │   │   ├── Delivery/        # App de entregadores
│   │   │   ├── Fidelidade/      # Controllers do sistema de fidelidade
│   │   │   └── API/             # API endpoints
│   │   │
│   │   ├── Requests/            # Form Requests organizados
│   │   │   ├── Admin/
│   │   │   ├── Merchant/
│   │   │   ├── Customer/
│   │   │   └── Fidelidade/
│   │   │
│   │   └── Middleware/          # Middlewares customizados
│   │
│   ├── Services/                # Camada de serviços
│   │   ├── Business/
│   │   ├── Finance/
│   │   ├── Fidelidade/
│   │   └── Payment/
│   │
│   ├── Repositories/            # Padrão Repository
│   ├── Events/                  # Eventos da aplicação
│   ├── Listeners/               # Listeners para eventos
│   └── Providers/               # Service Providers
```

### **Estrutura de Views (Theme Hyper)**

```
/resources/views/
├── layouts/
│   ├── app.blade.php           # Layout base principal
│   ├── admin.blade.php         # Layout para admin
│   ├── merchant.blade.php      # Layout para lojistas
│   ├── customer.blade.php      # Layout para clientes
│   └── partials/               # Componentes reutilizáveis
│       ├── sidebar.blade.php
│       ├── topbar.blade.php
│       ├── footer.blade.php
│       └── breadcrumb.blade.php
│
├── components/                 # Blade Components
│   ├── buttons/
│   ├── forms/
│   ├── cards/
│   ├── tables/
│   └── modals/
│
├── admin/                      # Views do painel admin
├── merchant/                   # Views do painel lojista
├── customer/                   # Views da área do cliente
├── fidelidade/                 # Views do sistema de fidelidade
│   ├── dashboard/
│   ├── transacoes/
│   ├── cupons/
│   └── conquistas/
└── errors/                     # Páginas de erro
```

### **Estrutura de Assets (Baseado no Theme1/Hyper)**

```
/public/assets/
├── css/
│   ├── app.min.css            # CSS principal (do Theme Hyper)
│   ├── icons.min.css          # Ícones (Unicons)
│   ├── custom/                # CSS customizado
│   │   ├── admin.css
│   │   ├── merchant.css
│   │   ├── customer.css
│   │   └── fidelidade.css
│   └── vendor/                # CSS de terceiros
│
├── js/
│   ├── app.min.js             # JS principal (do Theme Hyper)
│   ├── vendor.min.js          # Bibliotecas (do Theme)
│   ├── custom/                # JS customizado
│   │   ├── admin.js
│   │   ├── merchant.js
│   │   ├── customer.js
│   │   └── fidelidade.js
│   └── pages/                 # JS específico por página
│
├── images/
│   ├── logo/
│   ├── avatars/
│   ├── products/
│   └── uploads/
│
└── fonts/                     # Fontes do theme
```

---

## 🎨 **PADRÕES DE FRONTEND (THEME HYPER)**

### **Framework e Bibliotecas Utilizadas**

**CSS Framework:**

- Bootstrap 5.x (base do Theme Hyper)
- Unicons para ícones
- CSS customizado para ajustes específicos

**JavaScript:**

- jQuery 3.x
- Bootstrap 5 JS
- DataTables para tabelas
- ApexCharts para gráficos
- Select2 para selects avançados
- SweetAlert2 para alertas
- Flatpickr para date/time pickers

### **Sistema de Cores (Theme Hyper)**

```css
/* Cores Primárias */
:root {
  --bs-primary: #727cf5; /* Roxo principal */
  --bs-secondary: #6c757d; /* Cinza */
  --bs-success: #0acf97; /* Verde */
  --bs-info: #39afd1; /* Azul claro */
  --bs-warning: #ffbc00; /* Amarelo */
  --bs-danger: #fa5c7c; /* Vermelho */
  --bs-light: #eef2f7; /* Cinza claro */
  --bs-dark: #313a46; /* Azul escuro */
}

/* Cores para Status de Fidelidade */
.text-cashback {
  color: #0acf97;
} /* Verde para cashback */
.text-pontos {
  color: #727cf5;
} /* Roxo para pontos */
.text-conquista {
  color: #ffbc00;
} /* Amarelo para conquistas */
.bg-cashback {
  background-color: #0acf97;
}
.bg-pontos {
  background-color: #727cf5;
}
.bg-conquista {
  background-color: #ffbc00;
}
```

### **Componentes de UI Padronizados**

#### **1. Botões (seguindo Theme Hyper)**

```html
<!-- Botão Primário -->
<button type="button" class="btn btn-primary">
  <i class="uil uil-plus me-1"></i> Adicionar
</button>

<!-- Botão Secundário -->
<button type="button" class="btn btn-secondary">
  <i class="uil uil-eye me-1"></i> Visualizar
</button>

<!-- Botão de Sucesso -->
<button type="button" class="btn btn-success">
  <i class="uil uil-check me-1"></i> Confirmar
</button>

<!-- Botão de Perigo -->
<button
  type="button"
  class="btn btn-danger btn-sm"
  onclick="confirmarExclusao()"
>
  <i class="uil uil-trash-alt me-1"></i> Excluir
</button>

<!-- Botão de Edição -->
<a
  href="{{ route('admin.business.edit', $business) }}"
  class="btn btn-warning btn-sm"
>
  <i class="uil uil-edit me-1"></i> Editar
</a>
```

#### **2. Cards (seguindo padrão Hyper)**

```html
<!-- Card Básico -->
<div class="card">
  <div class="card-header">
    <h4 class="header-title">Título do Card</h4>
    <div class="card-widgets">
      <a href="#" data-bs-toggle="collapse" data-bs-target="#cardContent">
        <i class="mdi mdi-minus"></i>
      </a>
    </div>
  </div>
  <div class="card-body collapse show" id="cardContent">
    <!-- Conteúdo -->
  </div>
</div>

<!-- Card de Estatística -->
<div class="card widget-rounded-circle">
  <div class="card-body">
    <div class="row">
      <div class="col-6">
        <div
          class="avatar-lg rounded-circle bg-soft-primary border-primary border"
        >
          <i
            class="uil uil-money-withdrawal font-22 avatar-title text-primary"
          ></i>
        </div>
      </div>
      <div class="col-6">
        <div class="text-end">
          <h3 class="mt-1">R$ 1.250,00</h3>
          <p class="text-muted mb-1 text-truncate">Cashback Total</p>
        </div>
      </div>
    </div>
  </div>
</div>
```

#### **3. Tabelas (DataTables + Theme Hyper)**

```html
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table
        id="basic-datatable"
        class="table table-striped dt-responsive nowrap w-100"
      >
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <!-- Dados da tabela -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  $(document).ready(function () {
    $("#basic-datatable").DataTable({
      language: {
        url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese-Brasil.json",
      },
      responsive: true,
    });
  });
</script>
```

#### **4. Formulários (seguindo padrão Hyper)**

```html
<form
  action="{{ route('admin.business.store') }}"
  method="POST"
  class="needs-validation"
  novalidate
>
  @csrf

  <div class="row">
    <div class="col-md-6">
      <div class="mb-3">
        <label for="razao_social" class="form-label"
          >Razão Social <span class="text-danger">*</span></label
        >
        <input
          type="text"
          class="form-control @error('razao_social') is-invalid @enderror"
          id="razao_social"
          name="razao_social"
          value="{{ old('razao_social') }}"
          required
        />
        @error('razao_social')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="col-md-6">
      <div class="mb-3">
        <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
        <input
          type="text"
          class="form-control @error('nome_fantasia') is-invalid @enderror"
          id="nome_fantasia"
          name="nome_fantasia"
          value="{{ old('nome_fantasia') }}"
        />
        @error('nome_fantasia')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>
  </div>

  <div class="text-end">
    <button type="submit" class="btn btn-success">
      <i class="uil uil-check me-1"></i> Salvar
    </button>
    <a
      href="{{ route('admin.business.index') }}"
      class="btn btn-secondary ms-1"
    >
      <i class="uil uil-times me-1"></i> Cancelar
    </a>
  </div>
</form>
```

#### **5. Modais (seguindo padrão Hyper)**

```html
<!-- Modal de Confirmação -->
<div
  class="modal fade"
  id="delete-modal"
  tabindex="-1"
  role="dialog"
  aria-hidden="true"
>
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-body p-4">
        <div class="text-center">
          <i class="uil uil-exclamation-triangle h1 text-warning"></i>
          <h4 class="mt-2">Confirmar Exclusão</h4>
          <p class="mt-3">
            Tem certeza que deseja excluir este registro? Esta ação não pode ser
            desfeita.
          </p>
          <button
            type="button"
            class="btn btn-danger my-2"
            onclick="confirmarExclusao()"
          >
            Sim, excluir
          </button>
          <button
            type="button"
            class="btn btn-secondary"
            data-bs-dismiss="modal"
          >
            Cancelar
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
```

### **Alertas e Notificações (SweetAlert2)**

```javascript
// Alerta de Sucesso
function showSuccess(message = "Operação realizada com sucesso!") {
  Swal.fire({
    icon: "success",
    title: "Sucesso!",
    text: message,
    confirmButtonColor: "#0acf97",
  });
}

// Alerta de Erro
function showError(message = "Ocorreu um erro. Tente novamente.") {
  Swal.fire({
    icon: "error",
    title: "Erro!",
    text: message,
    confirmButtonColor: "#fa5c7c",
  });
}

// Confirmação de Exclusão
function confirmarExclusao() {
  Swal.fire({
    title: "Tem certeza?",
    text: "Esta ação não pode ser desfeita!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#fa5c7c",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Sim, excluir!",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // Executar exclusão
      document.getElementById("delete-form").submit();
    }
  });
}
```

### **Sistema de Notificações Flash**

```php
// No Controller
return redirect()->route('admin.business.index')
    ->with('success', 'Empresa criada com sucesso!');

return redirect()->back()
    ->with('error', 'Erro ao processar solicitação.')
    ->withInput();
```

```html
<!-- Na View (layout) -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="uil uil-check-circle me-2"></i>
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif @if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <i class="uil uil-exclamation-triangle me-2"></i>
  {{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
```

---

## �️ **PADRÕES DE ROTAS (COM PERMISSÕES AUTOMÁTICAS)**

### **Estrutura de Rotas Obrigatória**

#### **1. Rotas Protegidas (Comerciantes)**

```php
// routes/comerciantes.php ou routes/web.php
Route::middleware(['comerciantes.protected'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Empresas - Permissões automáticas: empresa.visualizar, empresa.criar, empresa.editar, empresa.excluir
    Route::resource('empresas', EmpresaController::class);

    // Produtos - Permissões automáticas: produto.visualizar, produto.criar, produto.editar, produto.excluir
    Route::resource('produtos', ProdutoController::class);

    // Vendas - Permissões automáticas: venda.visualizar, venda.criar, venda.editar, venda.excluir
    Route::resource('vendas', VendaController::class);

    // Usuários aninhados por empresa
    Route::resource('empresas.usuarios', UsuarioController::class)
        ->except(['index', 'show']); // Permissões: usuario.criar, usuario.editar, usuario.excluir

    // Rotas específicas com permissões customizadas
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::get('/relatorios/vendas', [RelatorioController::class, 'vendas'])->name('relatorios.vendas');
    Route::get('/relatorios/export', [RelatorioController::class, 'export'])->name('relatorios.export');
});
```

#### **2. Rotas Protegidas (Admin)**

```php
// routes/admin.php
Route::prefix('admin')->name('admin.')->middleware(['admin.protected'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Gestão de Empresas
    Route::resource('empresas', EmpresaController::class);

    // Gestão de Usuários do Sistema
    Route::resource('usuarios', UsuarioController::class);

    // Configurações do Sistema
    Route::resource('configuracoes', ConfiguracaoController::class)->only(['index', 'update']);

    // Relatórios Administrativos
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/', [RelatorioController::class, 'index'])->name('index');
        Route::get('/financeiro', [RelatorioController::class, 'financeiro'])->name('financeiro');
        Route::get('/usuarios', [RelatorioController::class, 'usuarios'])->name('usuarios');
    });
});
```

#### **3. Rotas Públicas (Sem Proteção)**

```php
// routes/web.php
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre', [HomeController::class, 'sobre'])->name('sobre');
Route::get('/contato', [ContatoController::class, 'index'])->name('contato');
Route::post('/contato', [ContatoController::class, 'enviar'])->name('contato.enviar');

// Autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
```

### **Nomenclatura de Rotas**

#### **Padrão Obrigatório:**

```php
// ✅ CORRETO - Seguindo convenções
Route::resource('produtos', ProdutoController::class);
// Gera: produtos.index, produtos.create, produtos.store, produtos.show, produtos.edit, produtos.update, produtos.destroy

// Rotas nomeadas específicas
Route::get('/dashboard/estatisticas', [DashboardController::class, 'estatisticas'])->name('dashboard.estatisticas');
Route::post('/produtos/{produto}/duplicar', [ProdutoController::class, 'duplicar'])->name('produtos.duplicar');

// ❌ EVITAR - Nomes inconsistentes
Route::get('/lista-produtos', [ProdutoController::class, 'index'])->name('lista_produtos');
Route::get('/product-list', [ProdutoController::class, 'index'])->name('productList');
```

### **Mapeamento de Permissões por Rota**

| Método    | Rota                  | Ação Controller | Permissão Gerada     |
| --------- | --------------------- | --------------- | -------------------- |
| GET       | `/produtos`           | `index()`       | `produto.visualizar` |
| GET       | `/produtos/create`    | `create()`      | `produto.criar`      |
| POST      | `/produtos`           | `store()`       | `produto.criar`      |
| GET       | `/produtos/{id}`      | `show()`        | `produto.visualizar` |
| GET       | `/produtos/{id}/edit` | `edit()`        | `produto.editar`     |
| PUT/PATCH | `/produtos/{id}`      | `update()`      | `produto.editar`     |
| DELETE    | `/produtos/{id}`      | `destroy()`     | `produto.excluir`    |

#### **Rotas Personalizadas:**

```php
// Para ações customizadas, adicione ao mapeamento do middleware
Route::post('/produtos/{produto}/duplicate', [ProdutoController::class, 'duplicate'])
     ->name('produtos.duplicate'); // Permissão: produto.criar (definido no middleware)

Route::patch('/produtos/{produto}/toggle-status', [ProdutoController::class, 'toggleStatus'])
      ->name('produtos.toggle-status'); // Permissão: produto.editar
```

### **Grupos de Middleware Disponíveis**

```php
// Middleware automático para comerciantes
'comerciantes.protected' => [
    'auth',
    'verified',
    'empresa.access', // Verifica se usuário tem empresa associada
    'auto.permission' // Verifica permissões automaticamente
]

// Middleware automático para admin
'admin.protected' => [
    'auth',
    'verified',
    'role:admin', // Verifica se é administrador
    'auto.permission' // Verifica permissões automaticamente
]
```

### **Como Adicionar Novas Rotas**

#### **1. Para Comerciantes:**

```php
// 1. Adicione no grupo protegido
Route::middleware(['comerciantes.protected'])->group(function () {
    // 2. Use resource para CRUD completo
    Route::resource('clientes', ClienteController::class);

    // 3. Ou rotas específicas
    Route::get('/dashboard/metricas', [DashboardController::class, 'metricas'])
         ->name('dashboard.metricas'); // Permissão: dashboard.visualizar
});

// 4. Pronto! Permissões automáticas:
// - cliente.visualizar (index, show)
// - cliente.criar (create, store)
// - cliente.editar (edit, update)
// - cliente.excluir (destroy)
```

#### **2. Para Admin:**

```php
Route::prefix('admin')->name('admin.')->middleware(['admin.protected'])->group(function () {
    Route::resource('configuracoes', ConfiguracaoController::class);
    // Permissões automáticas: configuracao.visualizar, configuracao.criar, etc.
});
```

---

## �🔧 **PADRÕES DE BACKEND (LARAVEL)**

### **Models (Estrutura Obrigatória)**

```php
<?php

namespace App\Models\{DomainFolder};

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ModelName extends Model
{
    use HasFactory, SoftDeletes;

    // 1. CONFIGURAÇÕES DA TABELA
    protected $table = 'table_name';
    protected $primaryKey = 'id';
    protected $fillable = [
        'field1',
        'field2',
        'empresa_id', // OBRIGATÓRIO para modelos multitenancy
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_active' => 'boolean',
        'data_field' => 'date',
        'decimal_field' => 'decimal:2',
        'sync_data' => 'datetime',
    ];

    // 2. CONSTANTES
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_INATIVO = 'inativo';

    public const STATUS_OPTIONS = [
        self::STATUS_ATIVO => 'Ativo',
        self::STATUS_INATIVO => 'Inativo',
    ];

    // Constantes para sincronização
    public const SYNC_PENDING = 'pending';
    public const SYNC_SYNCED = 'synced';
    public const SYNC_ERROR = 'error';
    public const SYNC_IGNORED = 'ignored';

    public const SYNC_STATUS_OPTIONS = [
        self::SYNC_PENDING => 'Pendente',
        self::SYNC_SYNCED => 'Sincronizado',
        self::SYNC_ERROR => 'Erro',
        self::SYNC_IGNORED => 'Ignorado',
    ];

    // 3. RELACIONAMENTOS
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }

    public function relatedModel(): HasMany
    {
        return $this->hasMany(RelatedModel::class);
    }

    // 4. SCOPES
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePendingSync(Builder $query): Builder
    {
        return $query->where('sync_status', self::SYNC_PENDING);
    }

    public function scopeSynced(Builder $query): Builder
    {
        return $query->where('sync_status', self::SYNC_SYNCED);
    }

    public function scopeSyncError(Builder $query): Builder
    {
        return $query->where('sync_status', self::SYNC_ERROR);
    }

    // 5. ACCESSORS/MUTATORS (Laravel 9+)
    protected function formattedValue(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'R$ ' . number_format($value, 2, ',', '.')
        );
    }

    protected function syncStatusBadge(): Attribute
    {
        return Attribute::make(
            get: fn() => match($this->sync_status) {
                self::SYNC_SYNCED => '<span class="badge bg-success">Sincronizado</span>',
                self::SYNC_PENDING => '<span class="badge bg-warning">Pendente</span>',
                self::SYNC_ERROR => '<span class="badge bg-danger">Erro</span>',
                self::SYNC_IGNORED => '<span class="badge bg-secondary">Ignorado</span>',
                default => '<span class="badge bg-secondary">N/A</span>'
            }
        );
    }

    // 6. MÉTODOS CUSTOMIZADOS
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    public function needsSync(): bool
    {
        return $this->sync_status === self::SYNC_PENDING;
    }

    public function isSynced(): bool
    {
        return $this->sync_status === self::SYNC_SYNCED;
    }

    public function generateSyncHash(): string
    {
        $data = collect($this->getAttributes())
            ->except(['id', 'sync_hash', 'sync_status', 'sync_data', 'created_at', 'updated_at'])
            ->toJson();

        return md5($data);
    }

    public function markForSync(): void
    {
        $this->update([
            'sync_status' => self::SYNC_PENDING,
            'sync_hash' => $this->generateSyncHash()
        ]);
    }

    public function markAsSynced(): void
    {
        $this->update([
            'sync_status' => self::SYNC_SYNCED,
            'sync_data' => now()
        ]);
    }

    public function markSyncError(): void
    {
        $this->update(['sync_status' => self::SYNC_ERROR]);
    }

    // 7. BOOT METHOD
    protected static function booted(): void
    {
        static::creating(function ($model) {
            // Auto-preenchimento de empresa_id se logado
            if (auth()->check() && auth()->user()->empresa_id) {
                $model->empresa_id = auth()->user()->empresa_id;
            }

            // Marcar para sincronização
            $model->sync_status = self::SYNC_PENDING;
            $model->sync_hash = $model->generateSyncHash();
        });

        static::updating(function ($model) {
            // Verificar se houve mudanças que requerem sincronização
            if ($model->isDirty() && !$model->isDirty(['sync_status', 'sync_data', 'sync_hash'])) {
                $model->sync_status = self::SYNC_PENDING;
                $model->sync_hash = $model->generateSyncHash();
            }
        });
    }
}
```

### **Controllers (Estrutura Obrigatória)**

```php
<?php

namespace App\Http\Controllers\{UserType};

use App\Http\Controllers\Controller;
use App\Models\{DomainFolder}\ModelName;
use App\Http\Requests\{UserType}\StoreModelNameRequest;
use App\Http\Requests\{UserType}\UpdateModelNameRequest;
use App\Services\{DomainFolder}\ModelNameService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModelNameController extends Controller
{
    public function __construct(
        protected ModelNameService $modelNameService
    ) {
        // ✅ Middleware de autenticação básico
        $this->middleware('auth');
        $this->middleware('verified');

        // ✅ Permissões são verificadas automaticamente pelo middleware 'auto.permission'
        // ❌ Não precisa mais: $this->middleware('permission:...')->only([...]);
    }

    /**
     * Exibe lista do recurso
     * 🔐 Permissão automática: modelname.visualizar
     */
    public function index(Request $request): View
    {
        $query = ModelName::query();

        // Filtros
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Multitenancy - filtrar por empresa
        if (auth()->user()->empresa_id) {
            $query->forEmpresa(auth()->user()->empresa_id);
        }

        $models = $query->latest()->paginate(15);

        return view('{user_type}.model_name.index', compact('models'));
    }

    /**
     * Mostra formulário de criação
     * 🔐 Permissão automática: modelname.criar
     */
    public function create(): View
    {
        return view('{user_type}.model_name.create');
    }

    /**
     * Armazena novo recurso
     * 🔐 Permissão automática: modelname.criar
     */
    public function store(StoreModelNameRequest $request): RedirectResponse
    {
        try {
            // ✅ Permissão já verificada automaticamente - apenas implemente a lógica
            $this->modelNameService->create($request->validated());

            return redirect()
                ->route('{user_type}.model_names.index')
                ->with('success', 'Registro criado com sucesso!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao criar registro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Exibe recurso específico
     * 🔐 Permissão automática: modelname.visualizar
     */
    public function show(ModelName $modelName): View
    {
        // ❌ Não precisa mais: $this->authorize('view', $modelName);
        // ✅ Permissão já verificada automaticamente

        return view('{user_type}.model_name.show', compact('modelName'));
    }

    /**
     * Mostra formulário de edição
     * 🔐 Permissão automática: modelname.editar
     */
    public function edit(ModelName $modelName): View
    {
        // ❌ Não precisa mais: $this->authorize('update', $modelName);
        // ✅ Permissão já verificada automaticamente

        return view('{user_type}.model_name.edit', compact('modelName'));
    }

    /**
     * Atualiza recurso
     * 🔐 Permissão automática: modelname.editar
     */
    public function update(UpdateModelNameRequest $request, ModelName $modelName): RedirectResponse
    {
        try {
            // ✅ Permissão já verificada automaticamente
            $this->modelNameService->update($modelName, $request->validated());

            return redirect()
                ->route('{user_type}.model_names.index')
                ->with('success', 'Registro atualizado com sucesso!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao atualizar registro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove recurso
     * 🔐 Permissão automática: modelname.excluir
     */
    public function destroy(ModelName $modelName): RedirectResponse
    {
        try {
            // ✅ Permissão já verificada automaticamente
            $this->modelNameService->delete($modelName);

            return redirect()
                ->route('{user_type}.model_names.index')
                ->with('success', 'Registro excluído com sucesso!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao excluir registro: ' . $e->getMessage());
        }
    }

    /**
     * Ação customizada - duplicar
     * 🔐 Permissão automática: modelname.criar (definido no middleware)
     */
    public function duplicate(ModelName $modelName): RedirectResponse
    {
        try {
            $this->modelNameService->duplicate($modelName);

            return redirect()
                ->route('{user_type}.model_names.index')
                ->with('success', 'Registro duplicado com sucesso!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao duplicar registro: ' . $e->getMessage());
        }
    }
}
```

### **Form Requests (Validação)**

```php
<?php

namespace App\Http\Requests\{UserType};

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreModelNameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email:rfc,dns',
                'unique:table_name,email'
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^\(\d{2}\) \d{5}-\d{4}$/'
            ],
            'status' => [
                'required',
                Rule::in(array_keys(ModelName::STATUS_OPTIONS))
            ],
            'business_id' => [
                'required',
                Rule::exists('businesses', 'id')->where('is_active', true)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 100 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ter um formato válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'phone.required' => 'O telefone é obrigatório.',
            'phone.regex' => 'O telefone deve estar no formato (XX) XXXXX-XXXX.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'e-mail',
            'phone' => 'telefone',
        ];
    }
}
```

### **Services (Camada de Negócio)**

```php
<?php

namespace App\Services\{DomainFolder};

use App\Models\{DomainFolder}\ModelName;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ModelNameService
{
    public function create(array $data): ModelName
    {
        return DB::transaction(function () use ($data) {
            $model = ModelName::create($data);

            // Limpar cache relacionado
            Cache::tags(['model_names'])->flush();

            // Disparar eventos se necessário
            event('model.created', $model);

            return $model;
        });
    }

    public function update(ModelName $model, array $data): bool
    {
        return DB::transaction(function () use ($model, $data) {
            $updated = $model->update($data);

            // Limpar cache
            Cache::tags(['model_names'])->flush();

            // Disparar eventos
            event('model.updated', $model);

            return $updated;
        });
    }

    public function delete(ModelName $model): bool
    {
        return DB::transaction(function () use ($model) {
            // Verificar dependências
            if ($model->relatedModels()->exists()) {
                throw new \Exception('Não é possível excluir. Existem registros relacionados.');
            }

            $deleted = $model->delete();

            // Limpar cache
            Cache::tags(['model_names'])->flush();

            return $deleted;
        });
    }

    public function getActive()
    {
        return Cache::tags(['model_names'])->remember('active_models', 3600, function () {
            return ModelName::active()->get();
        });
    }
}
```

---

## 🗄️ **BASE DE DADOS**

> 📖 **DOCUMENTO DEDICADO**: Para o padrão completo de banco de dados, consulte: [`PADRAO_BANCO_DADOS.md`](./PADRAO_BANCO_DADOS.md)

### **Resumo das Convenções**

- **Tabelas**: plural, snake_case (`empresas`, `fidelidade_cashback_transacoes`)
- **Colunas**: snake_case (`razao_social`, `data_criacao`)
- **Chaves primárias**: `id` (auto increment)
- **Chaves estrangeiras**: `{table}_id` (`empresa_id`, `usuario_id`)
- **Timestamps**: `created_at`, `updated_at`, `deleted_at`

### **Campos Obrigatórios Resumidos**

> 📋 **Para detalhes completos**: Consulte [`PADRAO_BANCO_DADOS.md`](./PADRAO_BANCO_DADOS.md)

```sql
-- Estrutura mínima obrigatória para TODAS as tabelas
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
empresa_id INT UNSIGNED NOT NULL,
created_at TIMESTAMP NULL,
updated_at TIMESTAMP NULL,
deleted_at TIMESTAMP NULL,
sync_hash VARCHAR(64) NULL,
sync_status ENUM('pending', 'synced', 'error', 'ignored') DEFAULT 'pending',
sync_data TIMESTAMP NULL
```

### **Migration Template Simplificado**

> 📖 **Template completo**: Ver [`PADRAO_BANCO_DADOS.md`](./PADRAO_BANCO_DADOS.md)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nome_da_tabela', function (Blueprint $table) {
            // 1. CHAVE PRIMÁRIA
            $table->id();

            // 2. MULTITENANCY (OBRIGATÓRIO)
            $table->foreignId('empresa_id')
                  ->constrained('empresas')
                  ->onDelete('cascade');

            // 3. CAMPOS ESPECÍFICOS
            $table->string('nome', 100);
            $table->decimal('valor', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);

            // 4. SINCRONIZAÇÃO (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])->default('pending');
            $table->timestamp('sync_data')->nullable();

            // 5. TIMESTAMPS PADRÃO
            $table->timestamps();
            $table->softDeletes();

            // 6. ÍNDICES OBRIGATÓRIOS
            $table->index(['empresa_id', 'is_active']);
            $table->index('sync_status');
            $table->index(['empresa_id', 'sync_status', 'sync_data'], 'idx_sync_control');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nome_da_tabela');
    }
};
```

### **Atualização de Tabelas Existentes**

> 🔄 **Scripts completos**: Ver seção "Script para Tabelas Existentes" em [`PADRAO_BANCO_DADOS.md`](./PADRAO_BANCO_DADOS.md)

Para aplicar os padrões em tabelas já existentes, use os scripts SQL e migrations disponíveis no documento dedicado.

---

## 🔐 **SISTEMA DE PERMISSÕES AUTOMÁTICAS**

> 🚀 **DOCUMENTO DEDICADO**: Para o guia completo de uso, consulte: [`SISTEMA_PERMISSOES_AUTOMATICAS.md`](./SISTEMA_PERMISSOES_AUTOMATICAS.md)

### **Visão Geral**

O sistema de permissões automáticas foi implementado para **todo o site**, eliminando a necessidade de verificações manuais de permissão em cada controller. O sistema detecta automaticamente as permissões necessárias baseado nas rotas acessadas.

### **Como Funciona**

#### **1. Configuração Automática**

```bash
# Execute uma única vez para configurar todo o sistema
php artisan permissions:setup
```

#### **2. Para Novas Aplicações/Módulos**

**✅ NÃO precisa chamar métodos manualmente!** Apenas use o middleware nos grupos de rotas:

```php
// routes/web.php ou routes/comerciantes.php
Route::middleware(['comerciantes.protected'])->group(function () {
    // TODAS as rotas aqui serão automaticamente protegidas
    Route::resource('produtos', ProdutoController::class);
    Route::resource('vendas', VendaController::class);
    Route::resource('clientes', ClienteController::class);
});
```

#### **3. Mapeamento Automático de Permissões**

| Rota               | Método HTTP | Permissão Gerada     |
| ------------------ | ----------- | -------------------- |
| `produtos.index`   | GET         | `produto.visualizar` |
| `produtos.create`  | GET         | `produto.criar`      |
| `produtos.store`   | POST        | `produto.criar`      |
| `produtos.show`    | GET         | `produto.visualizar` |
| `produtos.edit`    | GET         | `produto.editar`     |
| `produtos.update`  | PUT/PATCH   | `produto.editar`     |
| `produtos.destroy` | DELETE      | `produto.excluir`    |

#### **4. Exemplos Práticos**

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

### **Uso nas Views (Blade)**

#### **Novas Diretivas Blade Disponíveis**

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

### **Benefícios para Desenvolvimento**

#### **✅ Vantagens**

1. **Zero Configuração**: Novas funcionalidades já vêm protegidas
2. **Consistência**: Todas as permissões seguem o mesmo padrão
3. **Manutenibilidade**: Um local central para gerenciar permissões
4. **Escalabilidade**: Funciona automaticamente com qualquer número de recursos
5. **Segurança**: Por padrão, tudo é protegido (fail-safe)

#### **🎯 Para Desenvolvedores**

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

### **Resultado Final**

- ✅ **Todo o site protegido automaticamente**
- ✅ **Novas funcionalidades são seguras por padrão**
- ✅ **Zero código repetitivo de verificação**
- ✅ **Interface se adapta às permissões do usuário**
- ✅ **Fácil manutenção e escalabilidade**

**Resumo**: Após a configuração inicial, você nunca mais precisa se preocupar com permissões manualmente. O sistema cuida de tudo automaticamente! 🎉

---

## 🔐 **VALIDAÇÕES E SEGURANÇA**

### **Validações Customizadas Comuns**

```php
// app/Rules/CnpjValidation.php
<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CnpjValidation implements Rule
{
    public function passes($attribute, $value)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $value);

        if (strlen($cnpj) !== 14) {
            return false;
        }

        // Validação completa do CNPJ
        // ... lógica de validação

        return true;
    }

    public function message()
    {
        return 'O CNPJ informado não é válido.';
    }
}
```

### **Middleware de Segurança**

```php
// app/Http/Middleware/EnsureBusinessAccess.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureBusinessAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Verificar se usuário tem empresa associada
        if (!$user->business_id) {
            return redirect()->route('onboarding.business');
        }

        // Verificar se empresa está ativa
        if (!$user->business->ativo) {
            return redirect()->route('business.suspended');
        }

        return $next($request);
    }
}
```

---

## ⚙️ **CONFIGURAÇÕES ESPECÍFICAS**

### **config/fidelidade.php**

```php
<?php

return [
    'cashback' => [
        'min_percentage' => 0.5,        // 0.5%
        'max_percentage' => 15.0,       // 15%
        'default_percentage' => 2.0,    // 2%
    ],

    'pontos' => [
        'real_para_pontos' => 100,      // R$ 1,00 = 100 pontos
        'pontos_para_real' => 0.01,     // 100 pontos = R$ 1,00
    ],

    'conquistas' => [
        'max_per_business' => 50,
        'tipos_requisito' => [
            'primeira_compra' => 'Primeira Compra',
            'valor_total' => 'Valor Total de Compras',
            'quantidade_compras' => 'Quantidade de Compras',
            'produtos_categoria' => 'Produtos de Categoria',
            'avaliacoes' => 'Avaliações Positivas',
        ],
    ],

    'limites' => [
        'saque_minimo' => 10.00,        // R$ 10,00
        'cashback_maximo_dia' => 100.00, // R$ 100,00 por dia
        'validade_cupom_dias' => 30,    // 30 dias
    ],
];
```

### **config/marketplace.php**

```php
<?php

return [
    'commission' => [
        'default_rate' => 5.0,          // 5%
        'min_rate' => 1.0,              // 1%
        'max_rate' => 20.0,             // 20%
    ],

    'subscription' => [
        'plans' => [
            'basico' => [
                'name' => 'Plano Básico',
                'price' => 99.90,
                'products_limit' => 100,
                'features' => ['Cashback', 'Cupons', 'Relatórios Básicos'],
            ],
            'premium' => [
                'name' => 'Plano Premium',
                'price' => 199.90,
                'products_limit' => 1000,
                'features' => ['Tudo do Básico', 'Conquistas', 'API', 'Relatórios Avançados'],
            ],
            'enterprise' => [
                'name' => 'Plano Enterprise',
                'price' => 399.90,
                'products_limit' => null, // Ilimitado
                'features' => ['Tudo do Premium', 'Suporte Prioritário', 'Customizações'],
            ],
        ],
    ],

    'formats' => [
        'currency' => 'BRL',
        'date' => 'd/m/Y',
        'datetime' => 'd/m/Y H:i',
        'time' => 'H:i',
    ],
];
```

---

## 🎯 **SISTEMA DE FIDELIDADE - PADRÕES ESPECÍFICOS**

### **Estrutura de Controllers de Fidelidade**

```php
// app/Http/Controllers/Fidelidade/TransacoesController.php
<?php

namespace App\Http\Controllers\Fidelidade;

use App\Http\Controllers\Controller;
use App\Models\Fidelidade\FidelidadeCashbackTransacao;
use App\Services\Fidelidade\TransacaoService;
use Illuminate\Http\Request;

class TransacoesController extends Controller
{
    public function __construct(
        protected TransacaoService $transacaoService
    ) {
        $this->middleware(['auth', 'verified', 'business.access']);
    }

    public function dashboard()
    {
        $estatisticas = $this->transacaoService->getEstatisticasDashboard();

        return view('fidelidade.transacoes.dashboard', compact('estatisticas'));
    }

    public function index(Request $request)
    {
        $transacoes = FidelidadeCashbackTransacao::query()
            ->forBusiness(auth()->user()->business_id)
            ->with(['cliente', 'empresa'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('cliente', function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15);

        return view('fidelidade.transacoes.index', compact('transacoes'));
    }
}
```

### **Padrões de Views de Fidelidade**

```html
<!-- resources/views/fidelidade/transacoes/index.blade.php -->
@extends('layouts.merchant')

@section('title', 'Transações de Cashback')

@section('content')
<div class="container-fluid">

    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('merchant.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Fidelidade</a></li>
                        <li class="breadcrumb-item active">Transações</li>
                    </ol>
                </div>
                <h4 class="page-title">Transações de Cashback</h4>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('fidelidade.transacoes.index') }}">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="search"
                                       placeholder="Buscar por cliente..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">Todos os Status</option>
                                    <option value="processada" {{ request('status') == 'processada' ? 'selected' : '' }}>Processada</option>
                                    <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                    <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="uil uil-search me-1"></i> Filtrar
                                </button>
                            </div>
                            <div class="col-md-3 text-end">
                                <a href="{{ route('fidelidade.transacoes.create') }}" class="btn btn-success">
                                    <i class="uil uil-plus me-1"></i> Nova Transação
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Cashback</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transacoes as $transacao)
                                <tr>
                                    <td>
                                        <img src="{{ $transacao->cliente->avatar_url }}" alt="Avatar"
                                             class="rounded-circle me-2" height="32">
                                        {{ $transacao->cliente->nome }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $transacao->tipo == 'credito' ? 'success' : 'warning' }}">
                                            {{ ucfirst($transacao->tipo) }}
                                        </span>
                                    </td>
                                    <td>{{ $transacao->valor_formatado }}</td>
                                    <td class="text-success">{{ $transacao->valor_cashback_formatado }}</td>
                                    <td>{!! $transacao->status_badge !!}</td>
                                    <td>{{ $transacao->data_criacao->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('fidelidade.transacoes.show', $transacao) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="uil uil-eye"></i>
                                        </a>
                                        @if($transacao->status == 'pendente')
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="cancelarTransacao({{ $transacao->id }})">
                                            <i class="uil uil-times"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="uil uil-info-circle h3 text-muted"></i>
                                        <p class="text-muted">Nenhuma transação encontrada</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $transacoes->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function cancelarTransacao(id) {
    Swal.fire({
        title: 'Cancelar Transação?',
        text: "Esta ação não pode ser desfeita!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#fa5c7c',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, cancelar!',
        cancelButtonText: 'Não'
    }).then((result) => {
        if (result.isConfirmed) {
            // Fazer requisição AJAX para cancelar
            fetch(`/fidelidade/transacoes/${id}/cancelar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Cancelada!', data.message, 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Erro!', data.message, 'error');
                }
            });
        }
    });
}
</script>
@endpush
```

---

## 📝 **CHECKLIST DE IMPLEMENTAÇÃO**

### **Para cada nova funcionalidade:**

- [ ] Criar migration seguindo padrões
- [ ] Criar model com todas as seções obrigatórias
- [ ] Criar Form Requests para validação
- [ ] Criar Service para lógica de negócio
- [ ] Criar Controller seguindo estrutura padrão
- [ ] **Adicionar rotas ao grupo protegido** `comerciantes.protected` ou `admin.protected`
- [ ] Criar views usando componentes Theme Hyper
- [ ] **Usar diretivas Blade** `@permission`, `@anypermission`, `@empresaPermission`
- [ ] Implementar testes unitários e feature
- [ ] Adicionar tradução de mensagens
- [ ] ~~Implementar autorização (Policies)~~ ✅ **Automático via middleware**
- [ ] Documentar API endpoints (se aplicável)
- [ ] Testar responsividade
- [ ] Verificar performance (N+1, cache)

### **Para cada Controller:**

- [ ] Usar injeção de dependência para Services
- [ ] Implementar tratamento de exceções adequado
- [ ] **Adicionar rotas ao grupo de middleware protegido**
- [ ] ~~Implementar verificações manuais de permissão~~ ✅ **Automático**
- [ ] Usar Form Requests para validação
- [ ] Retornar respostas consistentes
- [ ] Implementar paginação quando necessário
- [ ] Adicionar logs de auditoria quando aplicável

### **Para cada Model:**

- [ ] Usar SoftDeletes
- [ ] Definir fillable explicitamente
- [ ] Implementar casts apropriados
- [ ] Criar relacionamentos necessários
- [ ] Implementar scopes comuns
- [ ] Adicionar métodos de conveniência
- [ ] Implementar multitenancy (business_id)
- [ ] Criar Factory para testes

### **Para cada View:**

- [ ] Estender layout apropriado
- [ ] Usar componentes Blade reutilizáveis
- [ ] Implementar breadcrumb
- [ ] Adicionar filtros quando necessário
- [ ] **Usar diretivas de permissão** `@permission`, `@anypermission`, `@empresaPermission`
- [ ] Usar classes CSS do Theme Hyper
- [ ] Implementar JavaScript necessário
- [ ] Adicionar loading states
- [ ] Testar em diferentes resoluções

### **Exemplo de View com Permissões:**

```blade
@extends('layouts.merchant')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Lista de Produtos</h4>
        <div class="card-widgets">
            @permission('produto.criar')
                <a href="{{ route('produtos.create') }}" class="btn btn-primary">
                    <i class="uil uil-plus me-1"></i> Novo Produto
                </a>
            @endpermission
        </div>
    </div>
    <div class="card-body">
        <!-- Tabela -->
        <table class="table">
            <tbody>
                @foreach($produtos as $produto)
                <tr>
                    <td>{{ $produto->nome }}</td>
                    <td>
                        @permission('produto.visualizar')
                            <a href="{{ route('produtos.show', $produto) }}" class="btn btn-sm btn-info">
                                <i class="uil uil-eye"></i>
                            </a>
                        @endpermission

                        @permission('produto.editar')
                            <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-sm btn-warning">
                                <i class="uil uil-edit"></i>
                            </a>
                        @endpermission

                        @permission('produto.excluir')
                            <button class="btn btn-sm btn-danger" onclick="excluir({{ $produto->id }})">
                                <i class="uil uil-trash"></i>
                            </button>
                        @endpermission
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
```

---

- [ ] Testar em diferentes resoluções

---

## 🔧 **CONFIGURAÇÃO DO VS CODE**

### **Configuração Recomendada**

Para maximizar a eficiência das extensões e garantir formatação consistente do código, adicione a seguinte configuração ao seu `settings.json` do VS Code:

**Caminho:** `C:\Users\{seu-usuario}\AppData\Roaming\Code\User\settings.json`

```json
{
    // Formatação automática
    "editor.formatOnSave": true,
    "editor.tabSize": 4,
    "editor.insertSpaces": true,
    "files.encoding": "utf8",
    "files.eol": "\n",

    // Configurações PHP
    "php.suggest.basic": false,
    "php.validate.run": "onType",
    "php.validate.executablePath": "C:\xampp\php\php.exe",
    "php.completion.enabled": true,
    "php.completion.classNameCompletion": true,
    "php.completion.variableCompletion": true,

    // Configurações Blade
    "emmet.includeLanguages": {
        "blade": "html"
    },
    "files.associations": {
        "*.blade.php": "blade"
    },

    // Formatadores específicos
    "[php]": {
        "editor.defaultFormatter": "bmewburn.vscode-intelephense-client"
    },
    "[blade]": {
        "editor.defaultFormatter": "onecentlin.laravel-blade"
    },
    "[javascript]": {
        "editor.defaultFormatter": "esbenp.prettier-vscode"
    },
    "[css]": {
        "editor.defaultFormatter": "esbenp.prettier-vscode"
    },
    "[json]": {
        "editor.defaultFormatter": "esbenp.prettier-vscode"
    },

    // Configurações Intelephense
    "intelephense.format.enable": true,
    "intelephense.completion.insertUseDeclaration": true,
    "intelephense.completion.fullyQualifyGlobalConstantsAndFunctions": false,

    // Configurações Blade
    "blade.format.enable": true,
    "blade.format.wrapAttributes": "auto",
    "blade.format.wrapLineLength": 120,

    // Laravel específico
    "laravel-goto-component.roots": [
        "resources/views/components"
    ],
    "laravel.artisan.container": "docker",

    // PHP CS Fixer
    "php-cs-fixer.rules": "@PSR2",
    "php-cs-fixer.executablePath": "${extensionPath}/php-cs-fixer.phar",
    "php-cs-fixer.onsave": true,

    // GitHub Copilot
    "github.copilot.enable": {
        "*": true,
        "yaml": false,
        "plaintext": false,
        "markdown": true,
        "php": true,
        "javascript": true,
        "css": true,
        "html": true,
        "blade": true
    },

    // Configurações do chat
    "chat.experimental.variables": true,
    "chat.experimental.codeGeneration": true,
    "workbench.commandPalette.experimental.askChatLocation": "chatView",

    // Outras configurações úteis
    "explorer.confirmDelete": false,
    "explorer.confirmDragAndDrop": false,
    "workbench.startupEditor": "none",
    "editor.minimap.enabled": false,
    "breadcrumbs.enabled": true,
    "editor.wordWrap": "on",
    "editor.lineNumbers": "on",
    "editor.rulers": [80, 120],

    // Terminal
    "terminal.integrated.defaultProfile.windows": "PowerShell",
    "terminal.integrated.fontSize": 14,

    // Exclusões
    "files.exclude": {
        "**/vendor": true,
        "**/node_modules": true,
        "**/.git": true,
        "**/storage/logs": true,
        "**/storage/framework": true,
        "**/storage/app/public": true
    },

    // Auto save
    "files.autoSave": "afterDelay",
    "files.autoSaveDelay": 1000
}
```

### **Extensões Essenciais**

As seguintes extensões são **obrigatórias** para desenvolvimento no projeto:

#### **PHP & Laravel:**

- `bmewburn.vscode-intelephense-client` - IntelliSense para PHP
- `onecentlin.laravel-blade` - Suporte para Blade templates
- `onecentlin.laravel5-snippets` - Snippets do Laravel
- `ryannaddy.laravel-artisan` - Comandos Artisan integrados
- `codingyu.laravel-goto-view` - Navegação rápida para views

#### **GitHub Copilot:**

- `github.copilot` - Assistente de código IA
- `github.copilot-chat` - Chat integrado
- `github.copilot-labs` - Funcionalidades experimentais

#### **Frontend:**

- `bradlc.vscode-tailwindcss` - IntelliSense para Tailwind (se usado)
- `formulahendry.auto-rename-tag` - Renomear tags HTML automaticamente
- `esbenp.prettier-vscode` - Formatador de código

#### **Banco de Dados:**

- `cweijan.vscode-mysql-client2` - Cliente MySQL integrado

#### **Úteis:**

- `ms-vscode.vscode-json` - Suporte melhorado para JSON
- `streetsidesoftware.code-spell-checker` - Corretor ortográfico
- `gruntfuggly.todo-tree` - Visualizador de TODOs
- `aaron-bond.better-comments` - Comentários coloridos

### **Snippets Customizados**

Crie snippets específicos para o projeto em `Arquivo > Preferências > Configurar Snippets do Usuário > php.json`:

```json
{
  "Laravel Model": {
    "prefix": "model",
    "body": [
      "<?php",
      "",
      "namespace AppModels${1:Domain};",
      "",
      "use IlluminateDatabaseEloquentFactoriesHasFactory;",
      "use IlluminateDatabaseEloquentModel;",
      "use IlluminateDatabaseEloquentSoftDeletes;",
      "",
      "class ${2:ModelName} extends Model",
      "{",
      "    use HasFactory, SoftDeletes;",
      "",
      "    protected $table = '${3:table_name}';",
      "",
      "    protected $fillable = [",
      "        '${4:field_name}',",
      "    ];",
      "",
      "    protected $casts = [",
      "        'created_at' => 'datetime',",
      "        'updated_at' => 'datetime',",
      "        'deleted_at' => 'datetime',",
      "    ];",
      "}"
    ],
    "description": "Criar um Model Laravel seguindo os padrões do projeto"
  },

  "Laravel Controller": {
    "prefix": "controller",
    "body": [
      "<?php",
      "",
      "namespace AppHttpControllers${1:UserType};",
      "",
      "use AppHttpControllersController;",
      "use IlluminateHttpRequest;",
      "",
      "class ${2:ControllerName} extends Controller",
      "{",
      "    public function __construct()",
      "    {",
      "        $this->middleware('auth');",
      "    }",
      "",
      "    public function index()",
      "    {",
      "        return view('${3:view_name}');",
      "    }",
      "}"
    ],
    "description": "Criar um Controller Laravel seguindo os padrões do projeto"
  }
}
```

### **Configuração do Workspace**

Para configurações específicas do projeto, use o arquivo `.vscode/settings.json` no diretório raiz:

```json
{
    "php.validate.executablePath": "C:\xampp\php\php.exe",
    "laravel.artisan.executable": "./artisan",
    "intelephense.environment.includePaths": [
        "./vendor"
    ]
}
```

---

**📅 Documento atualizado em: 06/08/2025 - Sistema de Permissões Automáticas Implementado**

**👨‍💻 Desenvolvedor: Mazinho1020**

**🔐 Recursos Adicionados:**

- ✅ Sistema de Permissões Automáticas para todo o site
- ✅ Middleware `AutoPermissionCheck` com detecção automática de permissões
- ✅ Diretivas Blade: `@permission`, `@anypermission`, `@empresaPermission`, `@role`
- ✅ Comando de configuração: `php artisan permissions:setup`
- ✅ Grupos de middleware protegidos: `comerciantes.protected`, `admin.protected`
- ✅ Documentação completa em [`SISTEMA_PERMISSOES_AUTOMATICAS.md`](./SISTEMA_PERMISSOES_AUTOMATICAS.md)

---

> Este documento deve ser seguido rigorosamente por toda equipe.
> Atualize conforme necessário e sempre mantenha a consistência do código base.
