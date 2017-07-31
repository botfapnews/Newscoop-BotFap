#!/bin/bash
#

cd /var/www/html
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar install  --no-dev
php /var/www/html/scripts/fixer.php
chown -R www-data:www-data /var/www
