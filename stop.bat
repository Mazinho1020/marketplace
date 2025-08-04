@echo off
echo 🛑 Parando Marketplace...
echo.

echo 📦 Parando MySQL (Docker)...
docker stop marketplace-mysql
echo ✅ MySQL parado!

echo 🌐 Laravel Server parado automaticamente
echo.
echo ✅ Tudo finalizado!
pause
