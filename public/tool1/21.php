<?php
//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Helpers\BkavEHoaDonAPI;

//
require "/var/www/html/vendor/autoload.php";
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


\App\Models\VpsUsage::testCalculateMonthly('2026-03-11 00:00:00', '2027-02-11 00:00:00', 1000000);

//model_meta_infos
//
//use Illuminate\Support\Facades\DB;
//
//// Get all records from model_meta_infos
//$records = DB::table('model_meta_infos')->get();
//
//echo "<pre>";
//echo "=== Searching and updating '1,2' to '1' in model_meta_infos ===\n\n";
//
//$foundCount = 0;
//$updateCount = 0;
//
//foreach ($records as $record) {
//    // Get all properties of the record
//    $recordArray = (array) $record;
//
//    foreach ($recordArray as $fieldName => $fieldValue) {
//        // Check if value equals "1,2"
//        if ($fieldValue === "1,2") {
//            echo "✓ ID: {$record->id} | Field: {$fieldName} | Old Value: {$fieldValue}";
//
//            // Update the field value from "1,2" to "1"
//            DB::table('model_meta_infos')
//                ->where('id', $record->id)
//                ->update([$fieldName => "1"]);
//
//            echo " → New Value: 1 (UPDATED)\n";
//            $foundCount++;
//            $updateCount++;
//        }
//    }
//}
//
//echo "\n=== Total found: {$foundCount} ===\n";
//echo "=== Total updated: {$updateCount} ===\n";
//echo "</pre>";
