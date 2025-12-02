<?php

//30.8.2022 Stop Use Mongo for Meta Table

//use App\Components\modelBaseMongo;
use LadLib\Common\Database\MongoDbConnection;

try {

    defined('DB_HOST') || define('DB_HOST', '127.0.0.1');

    $hostname1 = gethostname();
    //phai include truoc thi moi co class nay, nen 1 so truong hop include sau se ko co:
    //    MongoDbConnection::$conn = new \MongoDB\Client('mongodb://'.DB_HOST.'/');
    //    return;
    \LadLib\Common\Database\MongoDbConnection::connectDb();

    MongoDbConnection::$dbname = 'test_laravel1';

    //    $ret = ClassMongoDbConnection::getConnection()->listDatabaseNames();
    //    dd($ret);

} catch (Exception $e) {
    exit('Error: db2x: '.$e->getMessage());
}
