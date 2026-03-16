#!/bin/bash
chown -R www-data:www-data /var/www/html/writable && chmod -R 775 /var/www/html/writable
exec apache2-foreground
