<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Helpers\BkavEHoaDonAPI;

require "../../../vendor/autoload.php";
$app = require_once '../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

if(!isCli()){
    die("Not cli!");
}

$api = new BkavEHoaDonAPI();

$from_date = '2026-01-10';
$to_date = '2026-02-13';

try {
    // Lấy page 1
    $result = $api->getInvoicesByDate($from_date, $to_date, 1);
    
    if ($result['isOk']) {
        $invoices = $result['Object'] ?? [];
        
        echo "JSON Structure của 1 hoá đơn (First Invoice):\n";
        echo str_repeat("=", 120) . "\n";
        
        if (count($invoices) > 0) {
            $first = $invoices[0];
            echo json_encode($first, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        }
        
    } else {
        echo "Error: " . ($result['Message'] ?? 'Unknown error') . "\n";
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
?>
