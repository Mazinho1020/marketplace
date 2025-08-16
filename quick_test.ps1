# Teste Rápido: Composer e GitHub funcionando
Write-Host "=== Teste Rápido: Composer + GitHub ===" -ForegroundColor Green
Write-Host ""

# Teste 1: Versões
Write-Host "1. Verificando versões..." -ForegroundColor Cyan
composer --version
php --version | Select-Object -First 1

Write-Host ""

# Teste 2: Conectividade GitHub
Write-Host "2. Testando conectividade GitHub..." -ForegroundColor Cyan
$testResult = Test-NetConnection api.github.com -Port 443 -InformationLevel Quiet
if ($testResult) {
    Write-Host "OK - api.github.com acessível" -ForegroundColor Green
} else {
    Write-Host "ERRO - api.github.com bloqueado" -ForegroundColor Red
}

Write-Host ""

# Teste 3: Composer básico
Write-Host "3. Testando Composer..." -ForegroundColor Cyan
if (Test-Path "composer.json") {
    Write-Host "OK - composer.json encontrado" -ForegroundColor Green
    
    Write-Host "Validando arquivo..." -ForegroundColor Yellow
    $output = & composer validate 2>&1
    Write-Host $output -ForegroundColor White
    
} else {
    Write-Host "AVISO - composer.json não encontrado" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=== Resultado ===" -ForegroundColor Green

if ($testResult -and (Test-Path "composer.json")) {
    Write-Host "SUCESSO! Sistema pronto para 'composer install'" -ForegroundColor Green
    Write-Host ""
    Write-Host "Comandos recomendados:" -ForegroundColor Yellow
    Write-Host "composer install --prefer-dist" -ForegroundColor Cyan
    Write-Host "composer install --no-dev (para produção)" -ForegroundColor Cyan
} else {
    Write-Host "Verifique os problemas acima antes de prosseguir" -ForegroundColor Yellow
}

Write-Host ""
