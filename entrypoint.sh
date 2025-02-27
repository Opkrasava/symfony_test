#!/bin/sh
set -e

echo "Запускаем composer install..."
composer install --no-interaction --prefer-dist

echo "Запускаем миграции..."
bin/console doctrine:migrations:migrate --no-interaction

echo "Запускаем Apache..."
exec apache2-foreground
