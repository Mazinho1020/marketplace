@echo off
echo 🚀 Iniciando Marketplace...
echo.

echo 📦 Verificando MySQL (Docker)...
docker start marketplace-mysql
echo ✅ MySQL iniciado!

echo 🌐 Iniciando Laravel Server...
php artisan serve
