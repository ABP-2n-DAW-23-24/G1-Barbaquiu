#!/bin/sh
rm -f app.tgz
mkdir -p -m 775 ./storage/app/public ./storage/framework/cache ./storage/framework/sessions ./storage/framework/testing ./storage/framework/views ./storage/logs  
(chgrp -R www-data storage ; true)
(chmod -R g+rwX storage ; true)
sudo mysql -u root -e "DROP DATABASE IF EXISTS barbaq"
sudo mysql -u root -e "CREATE DATABASE IF NOT EXISTS barbaq"
php /var/www/barbaq.es/artisan migrate:refresh
php /var/www/barbaq.es/artisan db:seed
sudo chmod -R 777 /var/www/barbaq.es
npm update
composer update