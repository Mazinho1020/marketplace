# Script de restart rápido do servidor Laravel
Write-Host "=== RESTART RÁPIDO DO SERVIDOR ===" -ForegroundColor Yellow

# Finalizar todos os processos PHP
Write-Host "1. Finalizando processos PHP..." -ForegroundColor Yellow
Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue

# Aguardar um momento
Start-Sleep -Seconds 3

# Liberar porta 8000 se estiver em uso
Write-Host "2. Liberando porta 8000..." -ForegroundColor Yellow
$portCheck = netstat -ano | findstr ":8000"
if ($portCheck) {
    $processes = netstat -ano | findstr ":8000" | ForEach-Object { ($_ -split '\s+')[-1] } | Sort-Object -Unique
    foreach ($processId in $processes) {
        if ($processId -and $processId -ne "0") {
            Stop-Process -Id $processId -Force -ErrorAction SilentlyContinue
        }
    }
    Start-Sleep -Seconds 2
}

# Navegar para o diretório
Set-Location "c:\xampp\htdocs\marketplace"

# Iniciar servidor novamente
Write-Host "3. Iniciando servidor..." -ForegroundColor Green
Write-Host "   URL: http://127.0.0.1:8000" -ForegroundColor Cyan
$env:APP_ENV = "local"
$env:APP_DEBUG = "true"
php artisan serve --host=127.0.0.1 --port=8000
