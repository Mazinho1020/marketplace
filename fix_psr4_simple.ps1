# Script simples para corrigir problemas PSR-4
Write-Host "=== Correção PSR-4 Autoloading ===" -ForegroundColor Green

# Criar pasta de backup
Write-Host "Criando pasta de backup..." -ForegroundColor Yellow
New-Item -Path "storage/backups/old-files" -ItemType Directory -Force -ErrorAction SilentlyContinue

# Mover arquivos problemáticos
Write-Host "Movendo arquivos problemáticos..." -ForegroundColor Yellow

$files = @(
    "app/Http/Controllers/Fidelidade/TransacoesControllerNew.php",
    "app/Core/Cache/CacheServiceNew.php", 
    "app/Core/Cache/CacheServiceOld.php",
    "app/Http/Controllers/Admin/DashboardControllerNew.php",
    "app/Http/Controllers/Admin/ReportControllerNew.php",
    "app/Http/Controllers/Financial/CategoriaContaGerencialController_backup.php",
    "app/Services/Config/ConfigManager_new.php"
)

foreach ($file in $files) {
    if (Test-Path $file) {
        $name = Split-Path $file -Leaf
        Move-Item $file "storage/backups/old-files/$name" -Force
        Write-Host "Movido: $name" -ForegroundColor Green
    }
}

# Mover pastas BACKUP
if (Test-Path "app/Comerciantes/Controllers/BACKUP") {
    Move-Item "app/Comerciantes/Controllers/BACKUP" "storage/backups/old-files/Controllers-BACKUP" -Force
    Write-Host "Movido: Controllers BACKUP" -ForegroundColor Green
}

if (Test-Path "app/Comerciantes/Helpers/BACKUP") {
    Move-Item "app/Comerciantes/Helpers/BACKUP" "storage/backups/old-files/Helpers-BACKUP" -Force  
    Write-Host "Movido: Helpers BACKUP" -ForegroundColor Green
}

if (Test-Path "app/Comerciantes/Models/BACKUP") {
    Move-Item "app/Comerciantes/Models/BACKUP" "storage/backups/old-files/Models-BACKUP" -Force
    Write-Host "Movido: Models BACKUP" -ForegroundColor Green
}

# Corrigir helpers minúsculo
if (Test-Path "app/helpers") {
    if (Test-Path "app/Helpers") {
        Write-Host "Mesclando app/helpers com app/Helpers..." -ForegroundColor Yellow
        Get-ChildItem "app/helpers" | Move-Item -Destination "app/Helpers" -Force
        Remove-Item "app/helpers" -Recurse -Force
    } else {
        Rename-Item "app/helpers" "app/Helpers"
        Write-Host "Renomeado: helpers -> Helpers" -ForegroundColor Green
    }
}

# Mover arquivos de API maiúsculo para Api
if (Test-Path "app/Http/Controllers/API") {
    Write-Host "Movendo arquivos de API para backup..." -ForegroundColor Yellow
    Move-Item "app/Http/Controllers/API" "storage/backups/old-files/API-Controllers" -Force
}

# Mover DTOs mal posicionados
if (Test-Path "app/DTOs/ContaReceberDTO.php") {
    Move-Item "app/DTOs/ContaReceberDTO.php" "storage/backups/old-files/ContaReceberDTO.php" -Force
    Write-Host "Movido: ContaReceberDTO.php" -ForegroundColor Green
}

Write-Host ""
Write-Host "Regenerando autoload..." -ForegroundColor Yellow
composer dump-autoload -o

Write-Host ""
Write-Host "=== Correção concluída! ===" -ForegroundColor Green
Write-Host "Arquivos movidos para storage/backups/old-files/" -ForegroundColor Cyan
