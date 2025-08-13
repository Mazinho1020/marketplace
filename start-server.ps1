# Script para inicializar o servidor Laravel de forma robusta
Write-Host "=== INICIANDO SERVIDOR LARAVEL ===" -ForegroundColor Green

# Navegar para o diretório
Set-Location "c:\xampp\htdocs\marketplace"

# Finalizar processos PHP existentes
Write-Host "1. Finalizando processos PHP existentes..." -ForegroundColor Yellow
Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue

# Aguardar um momento
Start-Sleep -Seconds 2

# Verificar se a porta está livre
Write-Host "2. Verificando porta 8000..." -ForegroundColor Yellow
$portCheck = netstat -ano | findstr ":8000"
if ($portCheck) {
    Write-Host "   Porta 8000 em uso. Tentando liberar..." -ForegroundColor Red
    # Encontrar e finalizar processo que usa a porta 8000
    $processes = netstat -ano | findstr ":8000" | ForEach-Object { ($_ -split '\s+')[-1] } | Sort-Object -Unique
    foreach ($processId in $processes) {
        if ($processId -and $processId -ne "0") {
            Stop-Process -Id $processId -Force -ErrorAction SilentlyContinue
        }
    }
    Start-Sleep -Seconds 2
}

# Limpar cache Laravel
Write-Host "3. Limpando cache Laravel..." -ForegroundColor Yellow
php artisan config:clear | Out-Null
php artisan cache:clear | Out-Null
php artisan route:clear | Out-Null

# Verificar se há erros no Laravel
Write-Host "4. Verificando configuração Laravel..." -ForegroundColor Yellow
php artisan --version | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host "   ERRO: Problema na configuração do Laravel!" -ForegroundColor Red
    exit 1
}

# Iniciar servidor com configurações otimizadas
Write-Host "5. Iniciando servidor Laravel..." -ForegroundColor Green
Write-Host "   URL: http://127.0.0.1:8000" -ForegroundColor Cyan
Write-Host "   Para parar: Ctrl+C" -ForegroundColor Cyan
Write-Host "=========================" -ForegroundColor Green

# Iniciar com configurações otimizadas
$env:APP_ENV = "local"
$env:APP_DEBUG = "true"
php -d max_execution_time=300 -d memory_limit=512M artisan serve --host=127.0.0.1 --port=8000
