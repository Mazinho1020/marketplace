#!/bin/bash

# Script para migrar módulo Fidelidade
echo "=== Migrando Módulo Fidelidade ==="

# Copiar migrations para o diretório principal
echo "Copiando migrations..."
cp database/migrations/fidelidade/*.php database/migrations/

# Executar composer dump-autoload
echo "Atualizando autoload..."
composer dump-autoload

# Executar migrations
echo "Executando migrations..."
php artisan migrate

# Limpar cache
echo "Limpando cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "=== Migração do módulo Fidelidade concluída! ==="
