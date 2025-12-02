<a href="https://docs.google.com/document/d/1v9ujZDuBGO8zyBCmntB75iKAK51l82t0DfE5PvpAjzE/edit"></a>
<a href="https://docs.google.com/document/d/1VpzWER1NHcbZyFvk3GPUwB7BONvfCk923mJHyK2vMP0/edit"></a>
//....
//Cai them de User:find... hoat dong
composer require --dev barryvdh/laravel-ide-helper

composer dump-autoload

php artisan migrate:generate
php artisan schema:dump

php artisan view:clear
php artisan cache:clear
php artisan make:model ModelMetaInfo -m
php artisan test --filter Api1Test


###cai driver chrome
php artisan dusk:chrome-driver
