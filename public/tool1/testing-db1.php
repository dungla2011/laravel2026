<?php

require_once __DIR__.'/../index.php';

echo 'ABC';

$obj = new \LadLib\Laravel\Test1\test2();

$product = new \App\Models\ProductBak();
$product->find(1);
//
$columns = $product->getTableColumnAndDataType();
dump($columns);

dd(request()->url());
//dd(request()->route());
