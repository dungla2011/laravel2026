<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load BaoKim Payment Helper
use App\Helpers\BaoKimPaymentHelper;


require __DIR__.'/../../../vendor/autoload.php';
$app = require_once __DIR__.'/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);



// Lấy tất cả parameters từ webhook callback
$params = $request->all();

BaoKimPaymentHelper::log("=== WEBHOOK RECEIVED ===");
BaoKimPaymentHelper::log("Request method: " . $_SERVER['REQUEST_METHOD']);
BaoKimPaymentHelper::log("Params: " . json_encode($params));

try {
    // Xử lý webhook callback từ BaoKim
    $result = BaoKimPaymentHelper::processWebhookCallback($params);

    if ($result['success']) {
        // Trả về response cho BaoKim
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'code' => 0,
            'message' => 'OK',
            'data' => $result['data'] ?? []
        ]);
    } else {
        // Có lỗi xảy ra
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'code' => 1,
            'message' => $result['message'],
            'data' => []
        ]);
    }

} catch (Throwable $e) {
    BaoKimPaymentHelper::log("❌ Webhook exception: " . $e->getMessage());
    BaoKimPaymentHelper::log("Stack trace: " . $e->getTraceAsString());

    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'code' => 2,
        'message' => 'Internal server error',
        'data' => []
    ]);
}


