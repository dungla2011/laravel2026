sudo apt update
sudo apt install docker.io docker-compose -y

docker-compose build
docker-compose up -d

git clone https://gitlab.com/dungla2011/laravel_2022_lad.git web_code/

docker exec -it my_web php /var/www/html/artisan cache:clear

copy .env sang

import db
http://10.0.0.18:8081/
docker exec -i my_db mysql -u root -pQaz@12abc_000 glx_test1 < ./db1.sql

chown 33:33 web_code/storage -R
chown 33:33 var_glx -R
