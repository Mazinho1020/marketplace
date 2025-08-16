# Script para configurar acesso ao GitHub via Firewall do Windows
# Execute este script como Administrador

Write-Host "=== Configuração de Firewall para GitHub API ===" -ForegroundColor Green
Write-Host ""

# Verificar se está executando como administrador
$currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
$principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
$isAdmin = $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "ERRO: Este script precisa ser executado como Administrador!" -ForegroundColor Red
    Write-Host "Clique com o botão direito no PowerShell e selecione 'Executar como administrador'" -ForegroundColor Yellow
    Write-Host ""
    Read-Host "Pressione Enter para sair"
    exit 1
}

Write-Host "✓ Executando com privilégios administrativos" -ForegroundColor Green

# URLs e domínios do GitHub que precisam de acesso
$githubDomains = @(
    "api.github.com",
    "github.com",
    "raw.githubusercontent.com",
    "codeload.github.com",
    "avatars.githubusercontent.com"
)

Write-Host ""
Write-Host "=== 1. Testando conectividade atual ===" -ForegroundColor Cyan

foreach ($domain in $githubDomains) {
    Write-Host "Testando $domain..." -NoNewline
    try {
        $result = Test-NetConnection -ComputerName $domain -Port 443 -InformationLevel Quiet -WarningAction SilentlyContinue
        if ($result) {
            Write-Host " ✓ OK" -ForegroundColor Green
        }
        else {
            Write-Host " ✗ FALHOU" -ForegroundColor Red
        }
    }
    catch {
        Write-Host " ✗ ERRO" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "=== 2. Configurando regras de firewall ===" -ForegroundColor Cyan

# Criar regras de saída para permitir acesso ao GitHub
$ruleName = "GitHub API Access"

# Verificar se a regra já existe
$existingRule = Get-NetFirewallRule -DisplayName $ruleName -ErrorAction SilentlyContinue

if ($existingRule) {
    Write-Host "Removendo regra existente..." -ForegroundColor Yellow
    Remove-NetFirewallRule -DisplayName $ruleName
}

Write-Host "Criando nova regra de firewall para GitHub..." -ForegroundColor Yellow

try {
    # Criar regra para permitir conexões HTTPS de saída para GitHub
    New-NetFirewallRule -DisplayName $ruleName `
        -Direction Outbound `
        -Protocol TCP `
        -RemotePort 443 `
        -Action Allow `
        -Program "System" `
        -Description "Permite acesso à API do GitHub e serviços relacionados"
    
    Write-Host "✓ Regra de firewall criada com sucesso!" -ForegroundColor Green
}
catch {
    Write-Host "✗ Erro ao criar regra de firewall: $($_.Exception.Message)" -ForegroundColor Red
}

# Configurar regras específicas para aplicações comuns
$commonApps = @(
    "C:\Program Files\Git\cmd\git.exe",
    "C:\Program Files (x86)\Git\cmd\git.exe",
    "C:\Windows\System32\curl.exe",
    "C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe"
)

Write-Host ""
Write-Host "=== 3. Configurando regras para aplicações específicas ===" -ForegroundColor Cyan

foreach ($app in $commonApps) {
    if (Test-Path $app) {
        $appName = Split-Path $app -Leaf
        $ruleName = "GitHub Access - $appName"
        
        # Verificar se a regra já existe
        $existingRule = Get-NetFirewallRule -DisplayName $ruleName -ErrorAction SilentlyContinue
        
        if ($existingRule) {
            Remove-NetFirewallRule -DisplayName $ruleName
        }
        
        try {
            New-NetFirewallRule -DisplayName $ruleName `
                -Direction Outbound `
                -Protocol TCP `
                -RemotePort 443 `
                -Action Allow `
                -Program $app `
                -Description "Permite acesso ao GitHub via $appName"
            
            Write-Host "✓ Regra criada para $appName" -ForegroundColor Green
        }
        catch {
            Write-Host "✗ Erro ao criar regra para $appName" -ForegroundColor Red
        }
    }
}

Write-Host ""
Write-Host "=== 4. Testando conectividade após configuração ===" -ForegroundColor Cyan

Start-Sleep -Seconds 2

foreach ($domain in $githubDomains) {
    Write-Host "Testando $domain..." -NoNewline
    try {
        $result = Test-NetConnection -ComputerName $domain -Port 443 -InformationLevel Quiet -WarningAction SilentlyContinue
        if ($result) {
            Write-Host " ✓ OK" -ForegroundColor Green
        }
        else {
            Write-Host " ✗ AINDA BLOQUEADO" -ForegroundColor Red
        }
    }
    catch {
        Write-Host " ✗ ERRO" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "=== 5. Verificando configuração de proxy ===" -ForegroundColor Cyan

# Verificar configurações de proxy
$proxySettings = Get-ItemProperty -Path "HKCU:\Software\Microsoft\Windows\CurrentVersion\Internet Settings" -ErrorAction SilentlyContinue

if ($proxySettings.ProxyEnable -eq 1) {
    Write-Host "⚠ Proxy detectado: $($proxySettings.ProxyServer)" -ForegroundColor Yellow
    Write-Host "Se você estiver em uma rede corporativa, pode ser necessário:" -ForegroundColor Yellow
    Write-Host "1. Configurar o proxy no Git: git config --global http.proxy http://proxy:porta" -ForegroundColor Yellow
    Write-Host "2. Ou solicitar ao administrador de rede para liberar o acesso" -ForegroundColor Yellow
}
else {
    Write-Host "✓ Nenhum proxy detectado" -ForegroundColor Green
}

Write-Host ""
Write-Host "=== 6. Configurações adicionais recomendadas ===" -ForegroundColor Cyan

# Sugestões de configuração do Git
Write-Host "Para o Git, você pode configurar:" -ForegroundColor Yellow
Write-Host "git config --global http.sslverify false  # (apenas se necessário em redes corporativas)" -ForegroundColor Gray
Write-Host "git config --global http.postBuffer 524288000  # (para repositórios grandes)" -ForegroundColor Gray

Write-Host ""
Write-Host "=== Configuração concluída ===" -ForegroundColor Green
Write-Host ""
Write-Host "Se os problemas persistirem, pode ser necessário:" -ForegroundColor Yellow
Write-Host "1. Contatar o administrador de rede da sua empresa/organização" -ForegroundColor Yellow
Write-Host "2. Configurar um proxy corporativo se aplicável" -ForegroundColor Yellow
Write-Host "3. Usar uma VPN se permitido pela política da empresa" -ForegroundColor Yellow

Write-Host ""
Read-Host "Pressione Enter para finalizar"
