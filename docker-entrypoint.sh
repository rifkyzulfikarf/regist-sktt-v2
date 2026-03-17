#!/bin/bash
set -e

mkdir -p /var/www/html/writable/cache \
         /var/www/html/writable/logs \
         /var/www/html/writable/session \
         /var/www/html/writable/uploads \
         /var/www/html/writable/debugbar

chown -R www-data:www-data /var/www/html/writable
chmod -R 775 /var/www/html/writable

exec apache2-foreground
