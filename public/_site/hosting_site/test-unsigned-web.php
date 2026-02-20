<?php
use App\Helpers\BkavEHoaDonAPI;

require "../../../vendor/autoload.php";
$app = require_once '../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

header('Content-Type: application/json; charset=utf-8');

$api = new BkavEHoaDonAPI();

$from_date = '2026-01-10';
$to_date = '2026-02-13';

try {
    $result = $api->getInvoicesByDate($from_date, $to_date, 1);
    
    if ($result['isOk']) {
        $invoices = $result['Object'] ?? [];
        
        $unsigned = [];
        foreach ($invoices as $item) {
            $inv = $item['Invoice'] ?? $item;
            $mtc = $inv['MTC'] ?? '';
            $status = $inv['Status'] ?? '';
            $serial = $inv['InvoiceSerial'] ?? '';
            $no = $inv['InvoiceNo'] ?? '';
            
            if (!$mtc || $status == 1 || $status == 11) {
                $unsigned[] = [
                    'serial' => $serial,
                    'no' => $no,
                    'mtc' => $mtc ?: null,
                    'status' => $status
                ];
            }
        }
        
        echo json_encode([
            'success' => true,
            'total' => count($invoices),
            'unsigned_count' => count($unsigned),
            'unsigned_invoices' => $unsigned
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'error' => $result['Message'] ?? 'Unknown error'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
