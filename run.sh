#!/bin/bash

echo "Executando script"

sudo /etc/init.d/apache2 stop
sudo systemctl stop mysql
docker stop redis

./vendor/bin/sail up -d

cd ../book-app-front/
nvm use 20
ng serve

echo "Terminou script"
