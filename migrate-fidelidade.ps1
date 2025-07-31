# Script PowerShell para migrar módulo Fidelidade
Write-Host "=== Migrando Módulo Fidelidade ===" -ForegroundColor Green

# Copiar migrations para o diretório principal
Write-Host "Copiando migrations..." -ForegroundColor Yellow
Copy-Item "database\migrations\fidelidade\*.php" "database\migrations\"

# Executar composer dump-autoload
Write-Host "Atualizando autoload..." -ForegroundColor Yellow
composer dump-autoload

# Executar migrations
Write-Host "Executando migrations..." -ForegroundColor Yellow
php artisan migrate

# Limpar cache
Write-Host "Limpando cache..." -ForegroundColor Yellow
php artisan config:clear
php artisan route:clear
php artisan view:clear

Write-Host "=== Migração do módulo Fidelidade concluída! ===" -ForegroundColor Green
