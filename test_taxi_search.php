<?php
/**
 * Test script cho TaxiUser::searchTaxiKeyword()
 * 
 * Usage: php test_taxi_search.php
 */

use App\Models\Product;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());




$mm = Product::all()->toArray();

print_r($mm);