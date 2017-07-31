#!/bin/bash
#

sed -i 's/;date.timezone =/date.timezone = Europe\/London/g' /etc/php/5.6/apache2/php.ini
sed -i 's/post_max_size = 8M/post_max_size = ipost_max_size = 50M/g' /etc/php/5.6/apache2/php.ini
sed -i 's/upload_max_filesize = 2M/upload_max_fileisize = 50M/g' /etc/php/5.6/apache2/php.ini
phpenmod intl
a2enmod rewrite
chown -R www-data:www-data /var/www
service apache2 restart

