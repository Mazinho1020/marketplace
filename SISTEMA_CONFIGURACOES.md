# 🛠️ **SISTEMA DE CONFIGURAÇÕES - MARKETPLACE**

## **Documentação Completa do Sistema de Configurações Multi-Site**

---

## 📋 **VISÃO GERAL**

O sistema de configurações foi desenvolvido seguindo os padrões estabelecidos na **PADRONIZACAO_COMPLETA.md**, implementando:

-   ✅ **Multitenancy** com `empresa_id`
-   ✅ **Sincronização** com `sync_hash`, `sync_status`, `sync_data`
-   ✅ **SoftDeletes** em todas as tabelas
-   ✅ **Timestamps** padrão Laravel (`created_at`, `updated_at`)
-   ✅ **Nomenclatura snake_case** consistente
-   ✅ **Relacionamentos** bem definidos
-   ✅ **Scopes** para filtros por empresa

---

## 🗄️ **ESTRUTURA DO BANCO DE DADOS**

### **Tabelas Criadas:**

1. **`config_environments`** - Ambientes de execução (online/offline)
2. **`config_sites`** - Sites do marketplace (sistema, pdv, fidelidade, delivery)
3. **`config_groups`** - Grupos organizacionais de configurações
4. **`config_definitions`** - Definições das configurações disponíveis
5. **`config_values`** - Valores das configurações por contexto
6. **`config_history`** - Histórico de alterações
7. **`config_roles`** - Papéis para controle de acesso
8. **`config_permissions`** - Permissões de acesso às configurações
9. **`config_url_mappings`** - Mapeamento de URLs por site/ambiente
10. **`config_db_connections`** - Conexões de banco por ambiente

### **Campos Obrigatórios (Padronização):**

```sql
-- Em TODAS as tabelas:
empresa_id          FOREIGN KEY to businesses
sync_hash           VARCHAR(64) NULLABLE
sync_status         ENUM('pending', 'synced', 'error') DEFAULT 'pending'
sync_data           TIMESTAMP NULLABLE
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULLABLE (SoftDeletes)

-- Índices obrigatórios:
INDEX(empresa_id, sync_status)
```

---

## 🏗️ **ARQUITETURA E FUNCIONAMENTO**

### **Hierarquia de Configurações:**

O sistema implementa uma hierarquia de prioridades para resolver configurações:

1. **Específico** - Site específico + Ambiente específico
2. **Site Global** - Site específico + Todos os ambientes
3. **Ambiente Global** - Todos os sites + Ambiente específico
4. **Global** - Todos os sites + Todos os ambientes

### **Exemplo Prático:**

```php
// 1. Mais específico (maior prioridade)
app_name para site='pdv' + environment='online' = "PDV Produção"

// 2. Site específico
app_name para site='pdv' + environment=NULL = "PDV Sistema"

// 3. Ambiente específico
app_name para site=NULL + environment='online' = "Marketplace Produção"

// 4. Global (menor prioridade)
app_name para site=NULL + environment=NULL = "Marketplace"
```

---

## 💻 **COMO USAR O SISTEMA**

### **1. Via Helper Function (Recomendado)**

```php
// Obter configuração
$appName = config_marketplace('app_name');
$companyName = config_marketplace('empresa_nome', 'Empresa Padrão');

// Obter grupo de configurações
$telegramConfigs = config_marketplace()->getGroup('telegram');

// Definir configuração
config_marketplace()->set('app_name', 'Novo Nome', $siteId, $environmentId);
```

### **2. Via Injeção de Dependência**

```php
use App\Services\Config\ConfigManager;

class ExemploController extends Controller
{
    public function __construct(private ConfigManager $configManager)
    {
    }

    public function exemplo()
    {
        $valor = $this->configManager->get('chave_config');
        $this->configManager->set('nova_chave', 'valor', $siteId, $environmentId);
    }
}
```

### **3. Via Facade (Se configurado)**

```php
use App\Services\Config\ConfigManager;

$manager = app(ConfigManager::class);
$value = $manager->get('app_name');
```

---

## 🖥️ **INTERFACE ADMINISTRATIVA**

### **Rotas Disponíveis:**

```php
// Listar todas as configurações
GET /admin/config

// Criar nova configuração
GET /admin/config/create
POST /admin/config

// Editar configuração
GET /admin/config/{id}/edit
PUT /admin/config/{id}

// Remover configuração
DELETE /admin/config/{id}

// Visualizar por grupo
GET /admin/config/group/{groupCode}

// Definir valor via AJAX
POST /admin/config/set-value

// Limpar cache
POST /admin/config/clear-cache

// Exportar configurações
GET /admin/config/export

// Histórico de alterações
GET /admin/config/{id}/history
```

### **Filtros Disponíveis:**

-   Por grupo de configuração
-   Por site específico
-   Por ambiente específico
-   Busca por chave ou descrição

---

## ⌨️ **LINHA DE COMANDO (CLI)**

### **Comando Artisan:**

```bash
# Obter configuração
php artisan config:manage get app_name

# Definir configuração global
php artisan config:manage set empresa_nome "Minha Empresa"

# Definir configuração específica para site
php artisan config:manage set app_name "PDV Nome" --site=pdv

# Definir configuração específica para ambiente
php artisan config:manage set debug true --environment=offline

# Listar todas as configurações
php artisan config:manage list

# Listar configurações de um grupo
php artisan config:manage list --group=telegram

# Limpar cache
php artisan config:manage clear-cache

# Usar empresa específica
php artisan config:manage get app_name --empresa=2
```

---

## 🎯 **GRUPOS DE CONFIGURAÇÃO PADRÃO**

### **1. Grupo 'geral'**

-   `app_name` - Nome da aplicação
-   `app_author` - Autor da aplicação
-   `app_email` - Email de contato
-   `app_version` - Versão do sistema
-   `debug` - Modo de depuração

### **2. Grupo 'empresa'**

-   `empresa_nome` - Nome da empresa
-   `empresa_cnpj` - CNPJ da empresa
-   `empresa_telefone` - Telefone da empresa
-   `empresa_endereco` - Endereço da empresa

### **3. Grupo 'telegram'**

-   `telegram_token` - Token do bot
-   `telegram_chat_id` - ID do chat
-   `telegram_api_url` - URL da API
-   `telegram_message` - Mensagem padrão

### **4. Grupo 'sync'**

-   `sync_interval_minutes` - Intervalo de sincronização
-   `sync_auto_on_startup` - Sincronizar ao iniciar
-   `sync_backup_local` - Backup local antes de sincronizar
-   `export_dir` - Diretório de exportação
-   `import_dir` - Diretório de importação
-   `backup_dir` - Diretório de backup
-   `log_dir` - Diretório de logs

### **5. Grupo 'fidelidade'**

-   `fidelidade_ativo` - Sistema de fidelidade ativo
-   `fidelidade_percentual_cashback` - Percentual de cashback padrão
-   `fidelidade_min_compra_cashback` - Valor mínimo para ganhar cashback
-   `fidelidade_max_cashback_dia` - Valor máximo de cashback por dia

### **6. Grupo 'pdv'**

-   `pdv_ativo` - PDV ativo
-   `pdv_impressora_automatica` - Impressão automática de cupons
-   `pdv_desconto_maximo` - Desconto máximo permitido (%)

---

## 🚀 **INSTALAÇÃO E CONFIGURAÇÃO**

### **1. Executar Migration:**

```bash
php artisan migrate
```

### **2. Executar Seeder:**

```bash
php artisan db:seed --class=ConfigSeeder
```

### **3. Registrar Service Provider (se necessário):**

```php
// config/app.php
'providers' => [
    // ...
    App\Providers\ConfigServiceProvider::class,
];
```

### **4. Incluir Rotas:**

```php
// routes/web.php
require __DIR__.'/admin_config.php';
```

---

## 🔒 **SEGURANÇA E PERMISSÕES**

### **Sistema de Roles:**

-   `admin` - Acesso total às configurações
-   `gerente` - Acesso restrito às configurações
-   `operador` - Apenas leitura

### **Validações:**

-   Chaves seguem padrão `snake_case`
-   Valores são validados conforme tipo definido
-   Apenas usuários autorizados podem editar
-   Histórico completo de alterações

### **Multitenancy:**

-   Todas as operações são filtradas por `empresa_id`
-   Usuários só veem configurações de sua empresa
-   Isolamento completo entre empresas

---

## 📈 **RECURSOS AVANÇADOS**

### **Cache Inteligente:**

-   Cache automático por contexto (empresa + site + ambiente)
-   Invalidação automática ao alterar valores
-   Controle manual de cache via interface e CLI

### **Sincronização:**

-   Todos os registros possuem campos de sincronização
-   Status automático de 'pending' ao alterar
-   Métodos para marcar como sincronizado/erro

### **Auditoria:**

-   Histórico completo de alterações
-   Registro de usuário, IP e user-agent
-   Rastreamento de valores anteriores e novos

### **Import/Export:**

-   Exportação de configurações em JSON
-   Backup automático antes de alterações
-   Restore de configurações por timestamp

---

## 🔧 **EXEMPLOS DE USO PRÁTICO**

### **Configuração Específica por Site:**

```php
// Definir nome diferente para cada site
config_marketplace()->set('app_name', 'Sistema Administrativo', $sitesSistema->id);
config_marketplace()->set('app_name', 'PDV Loja', $sitesPdv->id);
config_marketplace()->set('app_name', 'Fidelidade', $sitesFidelidade->id);

// Obter nome baseado no site atual (detectado automaticamente)
$appName = config_marketplace('app_name'); // Retorna o nome específico do site atual
```

### **Configuração por Ambiente:**

```php
// Debug apenas em desenvolvimento
config_marketplace()->set('debug', true, null, $environmentOffline->id);
config_marketplace()->set('debug', false, null, $environmentOnline->id);
```

### **Configuração de Fidelidade por Site:**

```php
// Diferentes percentuais de cashback por loja
config_marketplace()->set('fidelidade_percentual_cashback', '2.5', $loja1->id);
config_marketplace()->set('fidelidade_percentual_cashback', '3.0', $loja2->id);
config_marketplace()->set('fidelidade_percentual_cashback', '1.5', $loja3->id);
```

---

## 🎨 **INTEGRAÇÃO COM FRONTEND (Theme Hyper)**

### **Blade Templates:**

```blade
{{-- Obter configurações no Blade --}}
<h1>{{ config_marketplace('app_name') }}</h1>
<p>Empresa: {{ config_marketplace('empresa_nome') }}</p>

{{-- Verificar se recurso está ativo --}}
@if(config_marketplace('fidelidade_ativo'))
    <div class="alert alert-success">
        <i class="uil uil-star me-1"></i>
        Sistema de Fidelidade Ativo
    </div>
@endif
```

### **JavaScript/AJAX:**

```javascript
// Alterar configuração via AJAX
function updateConfig(configId, valor, siteId = null, ambienteId = null) {
    fetch("/admin/config/set-value", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify({
            config_id: configId,
            valor: valor,
            site_id: siteId,
            ambiente_id: ambienteId,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showSuccess(data.message);
            } else {
                showError(data.message);
            }
        });
}
```

---

## 📚 **REFERÊNCIAS E PADRÕES**

-   **Documento Base:** `PADRONIZACAO_COMPLETA.md`
-   **Nomenclatura:** snake_case para campos do banco
-   **Framework:** Laravel 9+ com Eloquent ORM
-   **Frontend:** Theme Hyper (Bootstrap 5)
-   **Multitenancy:** Campo `empresa_id` obrigatório
-   **Sincronização:** Campos `sync_hash`, `sync_status`, `sync_data`
-   **Soft Deletes:** Campo `deleted_at` em todas as tabelas

---

Este sistema de configurações está completamente alinhado com os padrões estabelecidos e pronto para uso em produção! 🚀
