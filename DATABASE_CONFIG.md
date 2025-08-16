# Configurações do Database Client para evitar pedidos de permissão

## Configurações importantes que foram adicionadas ao settings.json:

1. **savePassword: true** - Salva a senha da conexão
2. **autoConnect: true** - Conecta automaticamente ao banco
3. **confirmBeforeDelete: false** - Não pede confirmação para deletar
4. **confirmBeforeExecute: false** - Não pede confirmação para executar queries
5. **enableMultiQuery: true** - Permite executar múltiplas queries
6. **enableSqlHistory: true** - Mantém histórico de queries

## Como usar:

1. Abra a aba "Database" no VS Code (ícone de banco de dados na barra lateral)
2. Sua conexão "Marketplace Database" deve aparecer automaticamente
3. Clique para expandir e explorar as tabelas
4. Execute queries diretamente sem confirmações

## Comandos úteis:

- `Ctrl+Shift+P` -> "Database: New Query" - Nova query
- `Ctrl+Shift+P` -> "Database: Run Query" - Executar query
- `F5` - Executar query selecionada

## Configurações adicionais que você pode fazer:

No arquivo settings.json do usuário (Ctrl+Shift+P -> "Preferences: Open User Settings (JSON)"):

```json
{
  "database-client.defaultDatabase": "meufinanceiro",
  "database-client.autoOpenConsole": true,
  "database-client.showRunningStatus": false
}
```
