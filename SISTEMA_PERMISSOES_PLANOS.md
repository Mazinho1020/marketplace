# 🔐 Sistema de Permissões por Plano - Guia Completo

## 📋 Visão Geral

O sistema de permissões por plano permite controlar o acesso às funcionalidades do sistema baseado no plano de assinatura do usuário.

## ✅ Implementação Completa

### 1. **Middleware Criado**

- ✅ `CheckPlanPermission` registrado como `plan`
- ✅ Verifica autenticação do usuário
- ✅ Busca assinatura ativa da empresa
- ✅ Valida se o plano tem a funcionalidade solicitada
- ✅ Retorna mensagem de erro em português

### 2. **Planos Configurados**

- ✅ **Plano Básico** (R$ 29,90/mês)
  - Relatórios básicos
  - Gestão de usuários básica
  - Gestão de marcas limitada
- ✅ **Plano Profissional** (R$ 79,90/mês)
  - Todas as funcionalidades básicas
  - Relatórios avançados
  - Acesso à API
  - Operações em lote
  - Gestão de empresas
- ✅ **Plano Enterprise** (R$ 199,90/mês)
  - Todas as funcionalidades
  - Auditoria e logs
  - Campos personalizados
  - Permissões avançadas

### 3. **Rotas Protegidas**

- ✅ Relatórios avançados: `middleware('plan:advanced_reports')`
- ✅ Acesso à API: `middleware('plan:api_access')`
- ✅ Operações em lote: `middleware('plan:bulk_operations')`
- ✅ Gestão de empresas: `middleware('plan:company_management')`
- ✅ Auditoria: `middleware('plan:audit_logs')`
- ✅ Campos personalizados: `middleware('plan:custom_fields')`
- ✅ Permissões avançadas: `middleware('plan:advanced_permissions')`

## 🚀 Como Usar

### 1. **Proteger uma Rota Individual**

```php
Route::get('/relatorios/avancados', [RelatorioController::class, 'avancados'])
    ->middleware('plan:advanced_reports');
```

### 2. **Proteger um Grupo de Rotas**

```php
Route::middleware('plan:api_access')->group(function () {
    Route::get('/api/dados', [ApiController::class, 'dados']);
    Route::post('/api/criar', [ApiController::class, 'criar']);
    Route::put('/api/atualizar/{id}', [ApiController::class, 'atualizar']);
});
```

### 3. **Proteger Resource Controller**

```php
// Proteger todas as ações
Route::middleware('plan:company_management')
    ->resource('empresas', EmpresaController::class);

// Proteger apenas algumas ações
Route::resource('marcas', MarcaController::class)->except(['edit', 'update']);
Route::middleware('plan:brand_management')->group(function () {
    Route::get('marcas/{marca}/edit', [MarcaController::class, 'edit']);
    Route::put('marcas/{marca}', [MarcaController::class, 'update']);
});
```

### 4. **Verificar Permissão no Controller**

```php
public function funcaoAvancada(Request $request)
{
    $user = Auth::guard('comerciante')->user();
    $assinatura = AfiPlanAssinaturas::where('empresa_id', $user->empresa_id)
        ->whereIn('status', ['trial', 'ativo'])
        ->with('plano')
        ->first();

    if (!$assinatura || !$assinatura->plano->hasFeature('advanced_reports')) {
        return response()->json([
            'error' => 'Esta funcionalidade requer o Plano Profissional ou superior.'
        ], 403);
    }

    // Lógica da função...
}
```

### 5. **Verificar Permissão na View**

```blade
@php
    $user = Auth::guard('comerciante')->user();
    $assinatura = App\Models\AfiPlanAssinaturas::where('empresa_id', $user->empresa_id ?? 1)
        ->whereIn('status', ['trial', 'ativo'])
        ->with('plano')
        ->first();
    $hasAdvancedReports = $assinatura && $assinatura->plano->hasFeature('advanced_reports');
@endphp

@if($hasAdvancedReports)
    <a href="{{ route('comerciantes.relatorios.analytics') }}" class="btn btn-primary">
        Relatórios Avançados
    </a>
@else
    <button class="btn btn-secondary" disabled title="Requer Plano Profissional">
        Relatórios Avançados 🔒
    </button>
@endif
```

## 🔧 Funcionalidades Disponíveis

### Básicas (Todos os planos)

- `basic_reports` - Relatórios básicos
- `user_management` - Gestão de usuários
- `company_management` - Gestão básica de empresa
- `brand_management` - Gestão básica de marcas

### Profissionais (Profissional + Enterprise)

- `advanced_reports` - Relatórios avançados
- `api_access` - Acesso à API
- `custom_branding` - Personalização visual
- `priority_support` - Suporte prioritário
- `advanced_integrations` - Integrações avançadas
- `bulk_operations` - Operações em lote

### Enterprise (Apenas Enterprise)

- `unlimited_users` - Usuários ilimitados
- `custom_fields` - Campos personalizados
- `audit_logs` - Auditoria e logs
- `white_label` - White label
- `multi_company` - Multi-empresas
- `advanced_permissions` - Permissões avançadas

## 📊 Limites por Plano

### Plano Básico

- `max_users`: 3
- `max_companies`: 1
- `max_brands`: 2
- `storage_gb`: 1
- `api_calls_month`: 0

### Plano Profissional

- `max_users`: 10
- `max_companies`: 5
- `max_brands`: 10
- `storage_gb`: 10
- `api_calls_month`: 10.000

### Plano Enterprise

- `max_users`: -1 (ilimitado)
- `max_companies`: -1 (ilimitado)
- `max_brands`: -1 (ilimitado)
- `storage_gb`: 100
- `api_calls_month`: 100.000

## 🧪 Testar o Sistema

### 1. **Acessar a Demonstração**

```
http://localhost:8000/comerciantes/demo-permissoes
```

### 2. **Criar Assinatura de Teste**

```php
// Via Tinker
use App\Models\AfiPlanAssinaturas;
AfiPlanAssinaturas::create([
    'empresa_id' => 1,
    'plano_id' => 2, // Profissional
    'status' => 'ativo',
    'inicia_em' => now(),
    'expira_em' => now()->addMonths(1),
    'valor_pago' => 79.90
]);
```

### 3. **Testar Diferentes Cenários**

- ✅ Usuario sem assinatura → Bloqueio
- ✅ Usuario com plano básico → Acesso limitado
- ✅ Usuario com plano profissional → Acesso avançado
- ✅ Usuario com plano enterprise → Acesso total

## 🔍 Monitoramento

### 1. **Logs de Acesso Negado**

O middleware registra tentativas de acesso negado:

```
[2024-01-01 10:30:00] Acesso negado: Usuário ID 123 tentou acessar 'advanced_reports' com plano 'basico'
```

### 2. **Verificar Status da Assinatura**

```php
// Controller ou Command
$assinaturas = AfiPlanAssinaturas::with('plano', 'empresa')
    ->where('status', 'ativo')
    ->where('expira_em', '<', now()->addDays(7))
    ->get();
```

## 🚨 Tratamento de Erros

### 1. **Usuário sem Assinatura**

```json
{
  "error": "Nenhum plano ativo encontrado. Assine um plano para acessar esta funcionalidade.",
  "action": "subscribe",
  "redirect": "/comerciantes/planos"
}
```

### 2. **Plano Insuficiente**

```json
{
  "error": "Esta funcionalidade requer o Plano Profissional ou superior. Seu plano atual: Básico",
  "action": "upgrade",
  "current_plan": "Plano Básico",
  "required_features": ["advanced_reports"]
}
```

### 3. **Assinatura Expirada**

```json
{
  "error": "Sua assinatura expirou em 01/01/2024. Renove para continuar usando esta funcionalidade.",
  "action": "renew",
  "expired_date": "2024-01-01"
}
```

## 📈 Próximos Passos

1. **Implementar notificações de upgrade**
2. **Criar dashboard de uso por funcionalidade**
3. **Adicionar analytics de conversão**
4. **Implementar trial automático**
5. **Criar sistema de cotas**

---

**🎉 Sistema implementado e funcionando!**

Acesse: `/comerciantes/demo-permissoes` para ver a demonstração completa.
