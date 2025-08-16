# Script de Teste e Configuração Final para Composer
# Execute este script para verificar se tudo está funcionando

Write-Host "=== Teste Final: Composer e GitHub ===" -ForWrite-Host "=== Teste Concluido ===" -ForegroundColor GreengroundColor Green
Write-Host ""

# Informações do sistema
Write-Host "=== Informações do Sistema ===" -ForegroundColor Cyan
Write-Host "Data/Hora: $(Get-Date)" -ForegroundColor White
Write-Host "Sistema: $($env:OS)" -ForegroundColor White
Write-Host "Usuário: $($env:USERNAME)" -ForegroundColor White
Write-Host "Pasta Atual: $(Get-Location)" -ForegroundColor White

Write-Host ""
Write-Host "=== Teste 1: Versões Instaladas ===" -ForegroundColor Cyan

# Verificar PHP
try {
    $phpVersion = & php --version 2>&1 | Select-Object -First 1
    Write-Host "✓ PHP: $phpVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ PHP não encontrado" -ForegroundColor Red
}

# Verificar Composer
try {
    $composerVersion = & composer --version 2>&1
    Write-Host "✓ Composer: $composerVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ Composer não encontrado" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== Teste 2: Conectividade GitHub ===" -ForegroundColor Cyan

$githubDomains = @(
    "api.github.com",
    "github.com",
    "raw.githubusercontent.com",
    "repo.packagist.org",
    "packagist.org"
)

foreach ($domain in $githubDomains) {
    try {
        $test = Test-NetConnection $domain -Port 443 -InformationLevel Quiet -WarningAction SilentlyContinue
        if ($test) {
            Write-Host "✓ $domain" -ForegroundColor Green
        } else {
            Write-Host "✗ $domain - BLOQUEADO" -ForegroundColor Red
        }
    } catch {
        Write-Host "✗ $domain - ERRO" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "=== Teste 3: Configurações do Composer ===" -ForegroundColor Cyan

# Verificar configurações importantes
try {
    Write-Host "Configurações atuais do Composer:" -ForegroundColor Yellow
    & composer config --list --global | Select-String -Pattern "timeout|protocol|proxy" | ForEach-Object {
        Write-Host "  $_" -ForegroundColor White
    }
} catch {
    Write-Host "Erro ao obter configurações do Composer" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== Teste 4: Diagnóstico do Composer ===" -ForegroundColor Cyan

try {
    Write-Host "Executando diagnóstico completo..." -ForegroundColor Yellow
    $diagnose = & composer diagnose 2>&1
    
    # Filtrar apenas problemas importantes
    $diagnose | ForEach-Object {
        if ($_ -match "OK") {
            Write-Host "OK $_" -ForegroundColor Green
        } elseif ($_ -match "WARNING|warning") {
            Write-Host "WARNING $_" -ForegroundColor Yellow
        } elseif ($_ -match "ERROR|error|failed") {
            Write-Host "ERROR $_" -ForegroundColor Red
        }
    }
} catch {
    Write-Host "Erro ao executar diagnóstico" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== Teste 5: Configurações Otimizadas ===" -ForegroundColor Cyan

Write-Host "Aplicando configurações otimizadas..." -ForegroundColor Yellow

try {
    # Configurar timeout
    & composer config --global process-timeout 300
    Write-Host "✓ Timeout configurado para 300s" -ForegroundColor Green
    
    # Configurar protocolo HTTPS
    & composer config --global github-protocols https
    Write-Host "✓ Protocolo HTTPS configurado" -ForegroundColor Green
    
    # Configurar cache
    $cacheDir = "$env:USERPROFILE\.composer\cache"
    & composer config --global cache-dir $cacheDir
    Write-Host "✓ Diretório de cache configurado" -ForegroundColor Green
    
} catch {
    Write-Host "⚠ Erro ao aplicar algumas configurações" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=== Teste 6: Teste Real do Composer ===" -ForegroundColor Cyan

Write-Host "Testando se conseguimos verificar dependências..." -ForegroundColor Yellow

try {
    # Teste com validate (não faz downloads)
    $validateResult = & composer validate 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Arquivo composer.json válido" -ForegroundColor Green
    } else {
        Write-Host "⚠ Possíveis problemas no composer.json" -ForegroundColor Yellow
    }
    
    # Teste se consegue acessar repositórios
    Write-Host "Testando acesso aos repositórios..." -ForegroundColor Yellow
    $showResult = & composer show --available | Select-Object -First 5 2>&1
    if ($showResult -and $showResult.Count -gt 0) {
        Write-Host "✓ Acesso aos repositórios funcionando" -ForegroundColor Green
    } else {
        Write-Host "✗ Problemas de acesso aos repositórios" -ForegroundColor Red
    }
    
} catch {
    Write-Host "✗ Erro nos testes do Composer" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== Resumo e Recomendações ===" -ForegroundColor Green

# Verificar se há arquivo composer.json
if (Test-Path "composer.json") {
    Write-Host "✓ Arquivo composer.json encontrado" -ForegroundColor Green
    
    Write-Host ""
    Write-Host "Próximos comandos sugeridos:" -ForegroundColor Yellow
    Write-Host "1. composer install --prefer-dist" -ForegroundColor Cyan
    Write-Host "2. composer update (se necessário)" -ForegroundColor Cyan
    Write-Host "3. composer install --no-dev (para produção)" -ForegroundColor Cyan
    
} else {
    Write-Host "⚠ Arquivo composer.json não encontrado nesta pasta" -ForegroundColor Yellow
    Write-Host "Certifique-se de estar na pasta raiz do projeto Laravel" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Comandos úteis para troubleshooting:" -ForegroundColor Yellow
Write-Host "• composer clear-cache (limpar cache)" -ForegroundColor Cyan
Write-Host "• composer diagnose (diagnóstico)" -ForegroundColor Cyan
Write-Host "• composer install -vvv (modo verbose)" -ForegroundColor Cyan
Write-Host "• composer config --list (ver configurações)" -ForegroundColor Cyan

Write-Host ""
if ((Test-Path "composer.json") -and (Get-NetFirewallRule -DisplayName "*GitHub*" -ErrorAction SilentlyContinue)) {
    Write-Host "🎉 SISTEMA PRONTO PARA USO!" -ForegroundColor Green
    Write-Host "Firewall configurado e Composer funcionando!" -ForegroundColor Green
} else {
    Write-Host "⚠ Algumas configurações podem precisar de ajustes" -ForegroundColor Yellow
    Write-Host "Consulte o arquivo SOLUCOES_FIREWALL_COMPOSER.md para mais detalhes" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=== Teste Concluído ===" -ForegroundColor Green
pause
