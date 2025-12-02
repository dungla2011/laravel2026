<?php

header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Methods: GET, POST, PUSH, DELETE');
header('Access-Control-Allow-Headers: Authorization');

require_once __DIR__.'/../index.php';

$faker = Faker\Factory::create('vi_VN');

for ($i = 0; $i < 3; $i++) {
    echo $faker->name().'<br>';
}
