#!/bin/sh
set -e

# Port imposé par l'hébergeur (Render, Railway…)
if [ "${PORT:-80}" != "80" ]; then
    sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
    sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf
fi

# En démo, la base est remplie avec des données fictives au premier démarrage
if [ "${DEMO_MODE:-true}" = "true" ]; then
    su -s /bin/sh www-data -c "php /var/www/app/bin/seed.php" || true
fi

exec "$@"
