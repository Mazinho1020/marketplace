# 🔧 Correção de Problemas PSR-4 Autoloading

## 📋 Problemas Identificados

O Composer detectou vários arquivos que não seguem o padrão PSR-4 de autoloading:

### 🗂️ Arquivos com Problemas:

1. **TransacoesController** - Arquivo duplicado/renomeado incorretamente
2. **Arquivos BACKUP** - Não deveriam estar no autoload
3. **Arquivos com sufixos** (_New, _Old, _backup) - Conflitos de naming
4. **Helpers** - Pasta com nomenclatura incorreta
5. **API Controllers** - Namespace incorreto

## 🛠️ Soluções Automatizadas

### Script 1: Mover arquivos BACKUP
```powershell
# Criar pasta de backup fora do autoload
mkdir -p storage/backups/old-files

# Mover arquivos BACKUP
mv "./app/Comerciantes/Controllers/BACKUP" "./storage/backups/old-files/"
mv "./app/Comerciantes/Helpers/BACKUP" "./storage/backups/old-files/"
mv "./app/Comerciantes/Models/BACKUP" "./storage/backups/old-files/"
```

### Script 2: Resolver arquivos duplicados
```powershell
# Remover ou renomear arquivos conflitantes
mv "./app/Http/Controllers/Fidelidade/TransacoesControllerNew.php" "./storage/backups/old-files/"
mv "./app/Core/Cache/CacheServiceNew.php" "./storage/backups/old-files/"
mv "./app/Core/Cache/CacheServiceOld.php" "./storage/backups/old-files/"
```

### Script 3: Corrigir estrutura de pastas
```powershell
# Renomear pasta helpers (minúscula) para Helpers
mv "./app/helpers" "./app/Helpers"
```

## 📂 Estrutura Correta PSR-4

O Laravel espera esta estrutura:
```
app/
├── Http/Controllers/    (não API)
├── Http/Controllers/Api/ (não API)
├── Helpers/            (não helpers)
├── Services/
└── Models/
```

## 🚀 Execução Automática

Vou criar um script para corrigir todos os problemas automaticamente.
