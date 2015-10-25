#!/bin/sh
sed -i "s|'host' => 'localhost'|'host' => '$POSTGRES_PORT_5432_TCP_ADDR'|" /var/www/html/sites/default/settings.php
sed -i "s|'database' => 'pnud'|'database' => '${POSTGRES_ENV_POSTGRES_USER:-postgres}'|" /var/www/html/sites/default/settings.php
sed -i "s|'username' => 'postgres'|'username' => '${POSTGRES_ENV_POSTGRES_USER:-postgres}'|" /var/www/html/sites/default/settings.php
sed -i "s|'password' => 'postgres'|'password' => '$POSTGRES_ENV_POSTGRES_PASSWORD'|" /var/www/html/sites/default/settings.php
exec apachectl -DFOREGROUND
