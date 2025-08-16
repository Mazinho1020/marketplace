# Script para corrigir problemas PSR-4 Autoloading
# Executa limpeza automática de arquivos conflitantes

Write-Host "=== Correção PSR-4 Autoloading ===" -ForegroundColor Green
Write-Host ""

# Criar pasta de backup
$backupPath = "storage/backups/old-files"
Write-Host "1. Criando pasta de backup..." -ForegroundColor Cyan
if (!(Test-Path $backupPath)) {
    New-Item -Path $backupPath -ItemType Directory -Force
    Write-Host "✓ Pasta de backup criada: $backupPath" -ForegroundColor Green
} else {
    Write-Host "✓ Pasta de backup já existe" -ForegroundColor Green
}

Write-Host ""
Write-Host "2. Movendo arquivos problemáticos..." -ForegroundColor Cyan

# Lista de arquivos para mover
$problematicFiles = @(
    "./app/Http/Controllers/Fidelidade/TransacoesControllerNew.php",
    "./app/Comerciantes/Controllers/BACKUP",
    "./app/Comerciantes/Helpers/BACKUP", 
    "./app/Comerciantes/Models/BACKUP",
    "./app/Core/Cache/CacheServiceNew.php",
    "./app/Core/Cache/CacheServiceOld.php",
    "./app/DTOs/ContaReceberDTO.php",
    "./app/Http/Controllers/Admin/DashboardControllerNew.php",
    "./app/Http/Controllers/Admin/ReportControllerNew.php",
    "./app/Http/Controllers/API/Financeiro/LancamentoController.php",
    "./app/Http/Controllers/API/Financial/ContasReceberApiController.php",
    "./app/Http/Controllers/Financial/CategoriaContaGerencialController_backup.php",
    "./app/Services/Config/ConfigManager_new.php"
)

foreach ($file in $problematicFiles) {
    if (Test-Path $file) {
        $fileName = Split-Path $file -Leaf
        $destination = Join-Path $backupPath $fileName
        
        try {
            Move-Item $file $destination -Force
            Write-Host "✓ Movido: $fileName" -ForegroundColor Green
        } catch {
            Write-Host "⚠ Erro ao mover $fileName : $($_.Exception.Message)" -ForegroundColor Yellow
        }
    } else {
        Write-Host "- Não encontrado: $file" -ForegroundColor Gray
    }
}

Write-Host ""
Write-Host "3. Corrigindo estrutura de pastas..." -ForegroundColor Cyan

# Corrigir pasta helpers (minúscula para Helpers)
if (Test-Path "./app/helpers") {
    if (Test-Path "./app/Helpers") {
        Write-Host "⚠ Ambas as pastas existem (helpers e Helpers)" -ForegroundColor Yellow
        Write-Host "  Movendo conteúdo de helpers/ para Helpers/" -ForegroundColor Yellow
        
        $helpersFiles = Get-ChildItem "./app/helpers" -File
        foreach ($file in $helpersFiles) {
            $dest = "./app/Helpers/$($file.Name)"
            if (!(Test-Path $dest)) {
                Move-Item $file.FullName $dest
                Write-Host "✓ Movido: $($file.Name)" -ForegroundColor Green
            }
        }
        Remove-Item "./app/helpers" -Recurse -Force
        Write-Host "✓ Pasta helpers/ removida" -ForegroundColor Green
    } else {
        Rename-Item "./app/helpers" "./app/Helpers"
        Write-Host "✓ Renomeado: helpers/ → Helpers/" -ForegroundColor Green
    }
} else {
    Write-Host "- Pasta helpers/ não encontrada" -ForegroundColor Gray
}

Write-Host ""
Write-Host "4. Corrigindo namespaces de API..." -ForegroundColor Cyan

# Verificar se existe pasta API (maiúscula) e Api (minúscula)
if (Test-Path "./app/Http/Controllers/API" -and Test-Path "./app/Http/Controllers/Api") {
    Write-Host "⚠ Ambas as pastas existem (API e Api)" -ForegroundColor Yellow
    Write-Host "  Recomenda-se usar apenas 'Api' (minúscula)" -ForegroundColor Yellow
    
    # Mover arquivos de API para Api se não conflitarem
    $apiFiles = Get-ChildItem "./app/Http/Controllers/API" -Recurse -File
    foreach ($file in $apiFiles) {
        $relativePath = $file.FullName.Replace((Resolve-Path "./app/Http/Controllers/API").Path, "")
        $destPath = "./app/Http/Controllers/Api$relativePath"
        $destDir = Split-Path $destPath -Parent
        
        if (!(Test-Path $destDir)) {
            New-Item -Path $destDir -ItemType Directory -Force
        }
        
        if (!(Test-Path $destPath)) {
            Move-Item $file.FullName $destPath
            Write-Host "✓ Movido para Api/: $relativePath" -ForegroundColor Green
        }
    }
    
    # Remover pasta API vazia
    if ((Get-ChildItem "./app/Http/Controllers/API" -Recurse).Count -eq 0) {
        Remove-Item "./app/Http/Controllers/API" -Recurse -Force
        Write-Host "✓ Pasta API/ removida (vazia)" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "5. Regenerando autoload..." -ForegroundColor Cyan

try {
    & composer dump-autoload -o
    Write-Host "✓ Autoload regenerado com otimização" -ForegroundColor Green
} catch {
    Write-Host "✗ Erro ao regenerar autoload: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "6. Verificando resultado..." -ForegroundColor Cyan

try {
    $result = & composer dump-autoload 2>&1
    if ($result -like "*does not comply with psr-4*") {
        Write-Host "⚠ Ainda existem problemas PSR-4" -ForegroundColor Yellow
        Write-Host "Detalhes:" -ForegroundColor Yellow
        $result | Where-Object { $_ -like "*does not comply with psr-4*" } | ForEach-Object {
            Write-Host "  $_" -ForegroundColor Red
        }
    } else {
        Write-Host "✓ Todos os problemas PSR-4 corrigidos!" -ForegroundColor Green
    }
} catch {
    Write-Host "Erro na verificação final" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=== Correção Concluída ===" -ForegroundColor Green
Write-Host ""
Write-Host "Arquivos movidos para: $backupPath" -ForegroundColor Yellow
Write-Host "Execute 'composer dump-autoload' para verificar se há outros problemas." -ForegroundColor Yellow

Write-Host ""
pause
