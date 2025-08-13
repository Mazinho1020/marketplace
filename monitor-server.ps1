# Script para monitorar a saúde do servidor Laravel
param(
    [int]$IntervalSeconds = 30
)

Write-Host "=== MONITOR DE SAÚDE DO SERVIDOR LARAVEL ===" -ForegroundColor Cyan
Write-Host "Verificando a cada $IntervalSeconds segundos..." -ForegroundColor Yellow
Write-Host "Pressione Ctrl+C para parar o monitoramento" -ForegroundColor Yellow
Write-Host "================================================" -ForegroundColor Cyan

while ($true) {
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    
    try {
        # Testar conectividade HTTP
        $response = Invoke-WebRequest -Uri "http://127.0.0.1:8000" -TimeoutSec 5 -UseBasicParsing -ErrorAction Stop
        $statusCode = $response.StatusCode
        
        if ($statusCode -eq 200) {
            Write-Host "[$timestamp] ✅ Servidor OK (HTTP $statusCode)" -ForegroundColor Green
        } else {
            Write-Host "[$timestamp] ⚠️  Servidor respondeu com código $statusCode" -ForegroundColor Yellow
        }
    }
    catch {
        Write-Host "[$timestamp] ❌ Servidor não está respondendo: $($_.Exception.Message)" -ForegroundColor Red
        
        # Verificar se o processo PHP ainda está rodando
        $phpProcesses = Get-Process -Name "php" -ErrorAction SilentlyContinue
        if ($phpProcesses) {
            Write-Host "[$timestamp] ℹ️  Processos PHP ativos: $($phpProcesses.Count)" -ForegroundColor Cyan
        } else {
            Write-Host "[$timestamp] ⚠️  Nenhum processo PHP encontrado!" -ForegroundColor Red
        }
        
        # Verificar se a porta 8000 está em uso
        $portCheck = netstat -ano | findstr ":8000"
        if ($portCheck) {
            Write-Host "[$timestamp] ℹ️  Porta 8000 ainda está em uso" -ForegroundColor Cyan
        } else {
            Write-Host "[$timestamp] ⚠️  Porta 8000 está livre!" -ForegroundColor Red
        }
    }
    
    Start-Sleep -Seconds $IntervalSeconds
}
