@echo off
echo ========================================
echo LIMPEZA DE CACHE E REINICIO DO LARAVEL
echo ========================================

echo.
echo 1. Encerrando processos PHP...
taskkill /F /IM php.exe >nul 2>&1
timeout /t 2 >nul

echo 2. Limpando cache de configuracao...
if exist "bootstrap\cache\config.php" del "bootstrap\cache\config.php"
if exist "bootstrap\cache\packages.php" del "bootstrap\cache\packages.php"
if exist "bootstrap\cache\services.php" del "bootstrap\cache\services.php"

echo 3. Limpando cache de aplicacao Laravel...
php artisan config:clear 2>nul
php artisan cache:clear 2>nul
php artisan route:clear 2>nul
php artisan view:clear 2>nul

echo 4. Regenerando cache otimizado...
php artisan config:cache 2>nul

echo 5. Testando configuracao de banco...
echo Verificando APP_ENV e configuracao de banco...
php test_final_config.php

echo.
echo 6. Iniciando servidor Laravel...
echo Pressione Ctrl+C para parar o servidor
php artisan serve

echo.
echo ========================================
echo PROCESSO CONCLUIDO
echo ========================================
pause
