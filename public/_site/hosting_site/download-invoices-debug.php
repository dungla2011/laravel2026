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

echo str_repeat("=", 80) . "\n";
echo "DEBUG: LẤY DANH SÁCH HOÁ ĐƠN VÀ PHÂN TÍCH CẤU TRÚC\n";
echo str_repeat("=", 80) . "\n";

try {
    $api = new BkavEHoaDonAPI();
    
    $to_date = date('Y-m-d');
    $from_date = date('Y-m-d', strtotime('-1 years'));
    
    echo "\n→ Lấy hoá đơn từ $from_date đến $to_date (Trang 1)...\n";
    
    $result = $api->getInvoicesByDate($from_date, $to_date, 1);
    
    if (!$result['isOk'] || $result['Status'] != 0) {
        echo "✗ Lỗi khi lấy danh sách: " . ($result['Object'] ?? 'Unknown error') . "\n";
        exit;
    }
    
    $invoices = $result['Object'];
    if (is_string($invoices)) {
        $invoices = json_decode($invoices, true);
    }
    
    echo "✓ Lấy được " . count($invoices) . " hoá đơn\n\n";
    
    // Phân tích 5 hoá đơn đầu tiên
    $count = min(5, count($invoices));
    echo "\n→ PHÂN TÍCH $count HOÁS ĐẦU TIÊN:\n";
    echo str_repeat("-", 80) . "\n";
    
    for ($i = 0; $i < $count; $i++) {
        $inv_data = $invoices[$i];
        $invoice = $inv_data['Invoice'] ?? [];
        
        echo "\n[" . ($i + 1) . "]\n";
        echo "  Serial: " . ($invoice['InvoiceSerial'] ?? 'N/A') . "\n";
        echo "  No: " . ($invoice['InvoiceNo'] ?? 'N/A') . "\n";
        echo "  Status: " . ($invoice['InvoiceStatusID'] ?? 'N/A') . " (1=Mới tạo, 11=Trống-Chờ ký, 2=Đã phát hành)\n";
        echo "  InvoiceCode (MTC): " . ($invoice['InvoiceCode'] ?? 'KHÔNG CÓ') . "\n";
        echo "  InvoiceGUID: " . ($invoice['InvoiceGUID'] ?? 'KHÔNG CÓ') . "\n";
        echo "  PartnerInvoiceID: " . ($invoice['PartnerInvoiceID'] ?? 'KHÔNG CÓ') . "\n";
        echo "  PartnerInvoiceStringID: " . ($invoice['PartnerInvoiceStringID'] ?? 'KHÔNG CÓ') . "\n";
        echo "  InvoiceDate: " . substr($invoice['InvoiceDate'] ?? '', 0, 10) . "\n";
        echo "  BuyerUnitName: " . substr($invoice['BuyerUnitName'] ?? '', 0, 40) . "\n";
        
        // In toàn bộ keys của Invoice object
        echo "  All fields in Invoice: ";
        $keys = array_keys($invoice);
        echo implode(", ", $keys) . "\n";
    }
    
    // Thống kê theo status
    echo "\n" . str_repeat("-", 80) . "\n";
    echo "THỐNG KÊ THEO TRẠNG THÁI:\n";
    
    $status_count = [];
    foreach ($invoices as $inv_data) {
        $invoice = $inv_data['Invoice'] ?? [];
        $status = $invoice['InvoiceStatusID'] ?? 0;
        $status_count[$status] = ($status_count[$status] ?? 0) + 1;
    }
    
    $status_names = [
        1 => "Mới tạo (Không có số)",
        2 => "Đã phát hành",
        3 => "Đã hủy",
        11 => "Trống - Đã cấp số, chờ ký"
    ];
    
    foreach ($status_count as $status => $count) {
        $name = $status_names[$status] ?? "Status $status";
        echo "  Status $status ($name): $count hoá đơn\n";
    }
    
    // Kiểm tra hoá đơn chưa ký
    echo "\n" . str_repeat("-", 80) . "\n";
    echo "HOÁS CHƯA KÝ (Status 1 hoặc 11) - Có MTC hay không:\n";
    
    $unsigned_with_mtc = 0;
    $unsigned_without_mtc = 0;
    $unsigned_with_guid = 0;
    
    foreach ($invoices as $inv_data) {
        $invoice = $inv_data['Invoice'] ?? [];
        $status = $invoice['InvoiceStatusID'] ?? 0;
        
        if ($status == 1 || $status == 11) {
            $has_mtc = !empty($invoice['InvoiceCode']);
            $has_guid = !empty($invoice['InvoiceGUID']);
            
            if ($has_mtc) $unsigned_with_mtc++;
            if (!$has_mtc) $unsigned_without_mtc++;
            if ($has_guid) $unsigned_with_guid++;
        }
    }
    
    echo "  Hoá đơn chưa ký có MTC: $unsigned_with_mtc\n";
    echo "  Hoá đơn chưa ký không có MTC: $unsigned_without_mtc\n";
    echo "  Hoá đơn chưa ký có GUID: $unsigned_with_guid\n";
    
    if ($unsigned_without_mtc > 0) {
        echo "\n  ⚠ Có $unsigned_without_mtc hoá đơn chưa ký không có MTC!\n";
        echo "  → Cần sử dụng CmdType 808/809 (với PartnerInvoiceID/PartnerInvoiceStringID)\n";
        echo "  → HOẶC CmdType 800 (với InvoiceGUID)\n";
    }
    
} catch (Exception $e) {
    echo "\n✗ Lỗi: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
