<?php
//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);


//
require "/var/www/html/vendor/autoload.php";
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

if(!$objU = getCurrentUserId(1)){

    header('Content-Type: application/json');
    http_response_code(401);   // ← bắt buộc, bất kỳ 4xx/5xx đều được
    $ret = json_encode([
        'success' => false,
        'error'   => "Vui lòng <a href='/login'> ĐĂNG NHẬP</a> ",
    ]);
    die($ret);
}

//$ret = json_encode([
//    'success' => true,
//    'data'    => [
//        'reply'           => "UID = $objU->id",
//        'conversation_id' => '123',
//    ],
//]);
//die($ret);

//die("xxx = " . $objU->getId());

$apiBase    = 'https://ai20.glx.com.vn';
$apiHeaders = [
    'X-Service-Key: iqO2s1-53ru2mzxZjywC60kBiOODfKNJanX4ZG5SopI',
    'X-User-ID: '   . $objU->getId(),
    'X-User-Name: ' . $objU->email,
    'Content-Type: application/json',
];

// ── Nếu có ?get_conversation=<id> → trả về lịch sử hội thoại ────────────────
$getConvId = isset($_GET['get_conversation']) ? trim($_GET['get_conversation']) : null;
if ($getConvId !== null) {

    // Thử lấy đúng conversation đó
    $chMsg = curl_init($apiBase . '/api/messages?conversation_id=' . urlencode($getConvId));
    curl_setopt($chMsg, CURLOPT_HTTPHEADER, $apiHeaders);
    curl_setopt($chMsg, CURLOPT_RETURNTRANSFER, true);
    $msgsResp = curl_exec($chMsg);
    $msgsCode = curl_getinfo($chMsg, CURLINFO_HTTP_CODE);
    curl_close($chMsg);
    $msgsData = json_decode($msgsResp, true);

    // Không tìm thấy conversation của user này → fallback conversation cuối cùng
    if ($msgsCode === 404 || empty($msgsData['success'])) {
        $chMsg = curl_init($apiBase . '/api/messages');
        curl_setopt($chMsg, CURLOPT_HTTPHEADER, $apiHeaders);
        curl_setopt($chMsg, CURLOPT_RETURNTRANSFER, true);
        $msgsResp = curl_exec($chMsg);
        $msgsCode = curl_getinfo($chMsg, CURLINFO_HTTP_CODE);
        curl_close($chMsg);
        $msgsData = json_decode($msgsResp, true);
    }

//    echo "<pre> $msgsCode >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($msgsData);
//    echo "</pre>";
//    die();

    header('Content-Type: application/json');
    if ($msgsCode >= 400 || empty($msgsData['success'])) {
        http_response_code($msgsCode ?: 500);
        $errDetail = $msgsData['error'] ?? $msgsData['detail'] ?? $msgsData['message'] ?? 'Không thể tải lịch sử hội thoại';
        echo json_encode(['success' => false, 'error' => $errDetail]);
    } else {
        echo json_encode($msgsData); // forward nguyên {success:true, data:{messages, conversation_id}}
    }
    die();
}
// ─────────────────────────────────────────────────────────────────────────────

// Đọc raw JSON body
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

$message        = $data['message']         ?? '';
$conversationId = $data['conversation_id'] ?? null;

$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

if (!$message) {
    header('Content-Type: application/json');
    http_response_code(400);
    $ret = json_encode([
        'success' => false,
        'error'   => 'Hãy nhập vào nội dung',
    ]);
    die($ret);
}

$body = json_encode([
    'message' => $message,
    'channel' => 'documents',
    'conversation_id' => $conversationId,
]);

$ch = curl_init($apiBase . '/api/chat');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $apiHeaders);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$apiData = json_decode($response, true);

// API trả về {success: true, data: {reply, conversation_id}} hoặc {success: false, error}
if ($httpCode >= 400 || empty($apiData['success'])) {
    header('Content-Type: application/json');
    http_response_code($httpCode ?: 500);

    $errDetail = $apiData['error'] ?? $apiData['detail'] ?? $apiData['message'] ?? null;
    if (!$errDetail) {
        if ($httpCode === 401)      $errDetail = 'API key không hợp lệ hoặc chưa xác thực';
        elseif ($httpCode === 403)  $errDetail = 'Không có quyền truy cập AI (kiểm tra X-Service-Key)';
        elseif ($httpCode === 429)  $errDetail = 'Quá nhiều yêu cầu, vui lòng thử lại sau';
        elseif ($httpCode === 503)  $errDetail = 'Dịch vụ AI tạm thời không khả dụng';
        elseif ($httpCode >= 500)   $errDetail = 'Máy chủ AI gặp lỗi nội bộ (HTTP ' . $httpCode . ')';
        elseif ($httpCode >= 400)   $errDetail = 'Yêu cầu không hợp lệ (HTTP ' . $httpCode . ')';
        else                        $errDetail = 'Không nhận được phản hồi từ máy chủ AI';
    }

    $ret = json_encode([
        'success' => false,
        'error'   => $errDetail,
    ]);
    echo $ret;
    die();
}

$inner          = $apiData['data'] ?? $apiData;
$reply          = $inner['reply']           ?? '';
$conversationId = $inner['conversation_id'] ?? '';

header('Content-Type: application/json');

$ret = json_encode([
    'success' => true,
    'data'    => [
        'reply'           => $reply,
        'conversation_id' => $conversationId,
    ],
]);

echo $ret;

//die($reply);
