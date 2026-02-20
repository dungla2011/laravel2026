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

echo "======================================\n";
echo "TEST: Tải XML để check MTC và signature\n";
echo "======================================\n\n";

try {
    // Lấy 5 hoá đơn gần nhất
    $result = $api->getInvoicesByDate($from_date, $to_date, 1);
    
    if ($result['isOk']) {
        $invoices = $result['Object'] ?? [];
        
        echo "Tổng hoá đơn lấy được: " . count($invoices) . "\n\n";
        
        // Tải XML cho các hoá đơn
        $temp_dir = '/tmp/test_invoices_xml';
        if (!is_dir($temp_dir)) mkdir($temp_dir, 0777, true);
        
        echo "Đang tải XML files...\n";
        $api->downloadInvoiceXmls(5, $temp_dir, null, false);
        
        // Kiểm tra XML files
        $xml_files = glob("$temp_dir/*.xml");
        echo "\nXML files đã tải: " . count($xml_files) . "\n";
        
        foreach ($xml_files as $xml_file) {
            echo "\n" . str_repeat("-", 80) . "\n";
            echo "File: " . basename($xml_file) . "\n";
            echo str_repeat("-", 80) . "\n";
            
            // Parse XML
            $parsed = $api->parseInvoiceXml($xml_file);
            
            if ($parsed) {
                $mtc = $parsed['MTC'] ?? '';
                $serial = $parsed['Serial'] ?? '';
                $number = $parsed['Number'] ?? '';
                $buyer = $parsed['Buyer'] ?? '';
                $amount = $parsed['Amount'] ?? '';
                $signed = $parsed['IsSigned'] ?? false;
                $sign_status = $parsed['SignatureStatus'] ?? '';
                
                echo "MTC: $mtc\n";
                echo "Serial: $serial\n";
                echo "Number: $number\n";
                echo "Buyer: " . substr($buyer, 0, 50) . "\n";
                echo "Amount: " . number_format($amount, 0) . "\n";
                echo "Signed: " . ($signed ? "✅ CÓ" : "❌ KHÔNG") . "\n";
                echo "Signature Status: $sign_status\n";
            } else {
                echo "Failed to parse\n";
            }
        }
        
    } else {
        echo "Error: " . ($result['Message'] ?? 'Unknown error') . "\n";
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
?>
