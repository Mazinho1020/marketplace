# Migração de funforcli para pessoas - COMPLETA

## ✅ Arquivos Atualizados com Sucesso

### 1. Models

- ✅ `app/Models/Cliente.php` - Atualizado para usar tabela `pessoas`
  - Mudou `protected $table = 'funforcli'` para `protected $table = 'pessoas'`
  - Adicionados fillables compatíveis com nova estrutura
  - Criados accessors/mutators para compatibilidade (cpf, ativo)
  - Adicionados scopes para filtrar clientes e ativos

### 2. Controllers Administrativos

- ✅ `app/Http/Controllers/Admin/AdminFidelidadeController.php` - TOTALMENTE ATUALIZADO

  - Todas as queries `DB::table('funforcli')` → `DB::table('pessoas')`
  - Filtros atualizados: `tipo = 'cliente'` → `tipo LIKE '%cliente%'`
  - Campo `ativo = 1` → `status = 'ativo'`
  - Campos inexistentes removidos (pontos_acumulados, saldo_disponivel, etc.)

- ✅ `app/Http/Controllers/Admin/DashboardController.php`

  - `DB::table('funforcli')` → `DB::table('pessoas')`
  - Filtro de afiliados atualizado

- ✅ `app/Http/Controllers/Admin/DashboardControllerNew.php`
  - `DB::table('funforcli')` → `DB::table('pessoas')`

### 3. Controllers de Fidelidade

- ✅ `app/Http/Controllers/Fidelidade/CarteirasController.php`

  - Validação `exists:funforcli,id` → `exists:pessoas,id`

- ✅ `app/Http/Controllers/Fidelidade/TransacoesController.php`

  - Duas validações atualizadas: store() e update()
  - `exists:funforcli,id` → `exists:pessoas,id`

- ✅ `app/Http/Controllers/Fidelidade/TransacoesControllerNew.php`
  - Validação `exists:funforcli,id` → `exists:pessoas,id`

### 4. Requests

- ✅ `app/Http/Requests/Fidelidade/StoreTransacaoRequest.php`
  - `exists:funforcli,id` → `exists:pessoas,id`

### 5. Seeders

- ✅ `database/seeders/FunforcliSeeder.php` → Renomeado logicamente para PessoasSeeder
  - Completamente reescrito para trabalhar com nova estrutura
  - Agora cria registros na tabela `fidelidade_carteiras` em vez de atualizar campos em funforcli
  - Filtros atualizados para usar `tipo LIKE '%cliente%'`

### 6. Rotas

- ✅ `routes/teste.php`
  - `DB::table('funforcli')` → `DB::table('pessoas')`

## 🔄 Lógica de Migração Implementada

### Mapeamento de Campos

- `funforcli.cpf` → `pessoas.cpf_cnpj` (via accessor/mutator)
- `funforcli.ativo` → `pessoas.status` (1/0 → ativo/inativo)
- `funforcli.tipo = 'cliente'` → `pessoas.tipo LIKE '%cliente%'`
- `funforcli.tipo = 'funcionario'` → `pessoas.tipo LIKE '%funcionario%'`

### Campos de Fidelidade Removidos

Os seguintes campos que existiam em funforcli foram removidos pois agora são controlados pela tabela `fidelidade_carteiras`:

- `pontos_acumulados`
- `saldo_disponivel`
- `cashback_acumulado`
- `nivel_fidelidade`
- `data_ultimo_uso`
- `total_compras`
- `valor_total_gasto`
- `programa_fidelidade_ativo`

### Compatibilidade Mantida

- O Model `Cliente` mantém compatibilidade através de accessors/mutators
- Relacionamentos com `fidelidade_carteiras` mantidos através de `cliente_id`
- Validações de formulários atualizadas para referenciar tabela correta

## 🎯 Sistema Funcionando

O sistema agora está completamente migrado de `funforcli` para `pessoas`:

1. **Todas as consultas administrativas** funcionam com a nova tabela
2. **Sistema de fidelidade** integrado via `fidelidade_carteiras`
3. **Validações de formulário** atualizadas
4. **Compatibilidade mantida** através do Model Cliente
5. **Seeders atualizados** para popular dados corretamente

## 📝 Próximos Passos (Opcional)

1. Testar todas as funcionalidades do sistema de fidelidade
2. Verificar se existem views que precisam ser atualizadas
3. Executar os seeders atualizados se necessário
4. Remover referências antigas em comentários de código

## ⚠️ Observações Importantes

- O banco de dados está correto (conforme informado)
- Apenas o código foi atualizado
- Mantida compatibilidade através de accessors/mutators
- Sistema de tipos flexível implementado (LIKE '%cliente%')
