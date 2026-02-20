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

// Test từ 14/01/2026 đến 13/02/2026
$from_date = '2026-01-14';
$to_date = '2026-02-13';

echo "======================================\n";
echo "TEST: Tìm hoá đơn chưa ký từ $from_date đến $to_date\n";
echo "======================================\n\n";

try {
    // Lấy page 1
    $result = $api->getInvoicesByDate($from_date, $to_date, 1);
    
    if ($result['isOk']) {
        $invoices = $result['Object'] ?? [];
        $page_count = $result['PageCount'] ?? 0;
        
        echo "Tổng page: $page_count\n";
        echo "Invoices trong page 1: " . count($invoices) . "\n\n";
        
        $unsigned_count = 0;
        $signed_count = 0;
        $invoice_list = [];
        
        foreach ($invoices as $item) {
            $inv = $item['Invoice'] ?? $item;
            $mtc = $inv['InvoiceCode'] ?? '';  // MTC là InvoiceCode!
            $status = $inv['InvoiceStatusID'] ?? '';
            $serial = $inv['InvoiceSerial'] ?? '';
            $no = $inv['InvoiceNo'] ?? '';
            $buyer = $inv['BuyerUnitName'] ?? '';
            $amount = $inv['SumPaymentAmount'] ?? '';
            
            // Check xem đã ký hay chưa theo InvoiceStatusID
            // Đã ký: 2, 6, 8, 15
            // Chưa ký: 1, 5, 7, 11, 14
            $signed_status = [2, 6, 8, 15];  // Đã phát hành, Thay thế, Điều chỉnh, Điều chỉnh chiết khấu
            $unsigned_status = [1, 5, 7, 11, 14];  // Mới tạo, Chờ thay thế, Chờ điều chỉnh, Chờ ký, Chờ điều chỉnh chiết khấu
            
            if (in_array($status, $signed_status)) {
                $signed_count++;
                $is_signed = "✅ ĐÃ KÝ";
            } elseif (in_array($status, $unsigned_status) || !$mtc) {
                $unsigned_count++;
                $is_signed = "❌ CHƯA KÝ";
            } else {
                $unsigned_count++;
                $is_signed = "❓ KHÔNG XÁC ĐỊNH";
            }
            
            $invoice_list[] = [
                'mtc' => $mtc,
                'status' => $status,
                'serial' => $serial,
                'no' => $no,
                'buyer' => $buyer,
                'amount' => $amount,
                'is_signed' => $is_signed
            ];
        }
        
        echo "Tổng hoá đơn: " . count($invoice_list) . "\n";
        echo "- Đã ký: $signed_count\n";
        echo "- Chưa ký: $unsigned_count\n\n";
        
        echo "DANH SÁCH TẤT CẢ HOÁĐƠN:\n";
        echo str_repeat("-", 140) . "\n";
        echo sprintf("%-20s | %-8s | %-6s | %-6s | %-40s | %-12s | %s\n", "MTC", "Status", "Serial", "No", "Buyer", "Amount", "Status");
        echo str_repeat("-", 140) . "\n";
        foreach ($invoice_list as $inv) {
            $amount_val = floatval($inv['amount']);
            echo sprintf(
                "%-20s | %-8s | %-6s | %-6s | %-40s | %-12s | %s\n",
                $inv['mtc'] ?: '[NO MTC]',
                $inv['status'] ?: '[NONE]',
                $inv['serial'],
                $inv['no'],
                substr($inv['buyer'], 0, 40),
                number_format($amount_val, 0),
                $inv['is_signed']
            );
        }
        
        if ($unsigned_count > 0) {
            echo "\n\nDANH SÁCH CHƯA KÝ:\n";
            echo str_repeat("-", 140) . "\n";
            foreach ($invoice_list as $inv) {
                if (strpos($inv['is_signed'], 'CHƯA') !== false || strpos($inv['is_signed'], 'KHÔNG') !== false) {
                    $amount_val = floatval($inv['amount']);
                    echo sprintf(
                        "%-20s | %-8s | %-6s | %-6s | %-40s | %-12s | %s\n",
                        $inv['mtc'] ?: '[NO MTC]',
                        $inv['status'] ?: '[NONE]',
                        $inv['serial'],
                        $inv['no'],
                        substr($inv['buyer'], 0, 40),
                        number_format($amount_val, 0),
                        $inv['is_signed']
                    );
                }
            }
        }
        
    } else {
        echo "✗ Error: " . ($result['Message'] ?? 'Unknown error') . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}
?>
