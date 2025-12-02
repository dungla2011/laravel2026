<?php


// ========== CONFIGURATION ==========
$PROJECT_ID = "taxi2025-6932c";  // Project ID từ Firebase
$FCM_TOKEN = "d8YTOl6MRY2okhavek48hP:APA91bHxjKo5y9TJYxMDDzQD8DDTei6UmbPIX3FjOUZbBfieawBjfzpRQMayh7eHtbvPUUuRT_WmDa6Hk5ymMxjri27-srKWiOWskTVL2wXmP72aaq5ISto";  // FCM Token từ Flutter app
$FCM_TOKEN = "cXbQE3IwS1GpJr-OorT2RI:APA91bGgsmX-gKqylh7G1G5wHXqFx4QSIdu-xvzQtikVkSb2qmnZ687BcsQzhYIOqRoSfLgkfncwNGHrH6YImi4XnPp6a-V6MaFiChJTHsYYHo6eCdBlh0M";
$SERVICE_ACCOUNT_FILE = "/var/www/html/config/service-account-key-firebase-taxi.json";  // Path to service account file

// ========== FIREBASE V1 API FUNCTIONS ==========
$ignoreArray = ['vj'];
$ignoreArray = [];


function ol5($str) {
    global $flog;
    if(!$flog) {
        $flog = "/var/glx/weblog/taxi_2025.log"; // Default log file
    }
    file_put_contents($flog, date("Y-m-d H:i:s") . " # " . $str . "\n", FILE_APPEND);
}


/**
 * Generate OAuth 2.0 Access Token từ Service Account
 */
function getAccessToken($serviceAccountFile) {
    try {
        echo "🔧 DEBUG: Starting getAccessToken...\n";

        if (!file_exists($serviceAccountFile)) {
            throw new Exception("Service account file not found: $serviceAccountFile");
        }

        $content = file_get_contents($serviceAccountFile);
        $serviceAccount = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON: " . json_last_error_msg());
        }

        // ⭐ DEBUG private key chi tiết hơn
        $privateKey = $serviceAccount['private_key'];

        echo "🔧 Original private_key length: " . strlen($privateKey) . "\n";
        echo "🔧 Contains \\n: " . (strpos($privateKey, '\\n') !== false ? 'YES' : 'NO') . "\n";

        // ⭐ Debug từng dòng của private key
        $lines = explode('\\n', $privateKey);
        echo "🔧 Private key has " . count($lines) . " lines\n";
        echo "🔧 First line: " . ($lines[0] ?? 'EMPTY') . "\n";
        echo "🔧 Last line: " . ($lines[count($lines)-1] ?? 'EMPTY') . "\n";

        // Convert \n to actual newlines
        $privateKey = str_replace('\\n', "\n", $privateKey);

        // ⭐ Validate private key format
        if (!str_contains($privateKey, '-----BEGIN PRIVATE KEY-----') ||
            !str_contains($privateKey, '-----END PRIVATE KEY-----')) {
            throw new Exception("Invalid private key format - missing BEGIN/END markers");
        }

        // ⭐ Debug processed key lines
        $processedLines = explode("\n", $privateKey);
        echo "🔧 Processed key has " . count($processedLines) . " lines\n";
        echo "🔧 Processed first line: " . ($processedLines[0] ?? 'EMPTY') . "\n";
        echo "🔧 Processed last line: " . trim($processedLines[count($processedLines)-1] ?? 'EMPTY') . "\n";

        // ⭐ Try to create and verify a test signature first
        echo "🧪 Testing private key with dummy data...\n";
        $testData = "test_data_" . time();
        $privateKeyResource = openssl_pkey_get_private($privateKey);

        if (!$privateKeyResource) {
            $errors = [];
            while ($error = openssl_error_string()) {
                $errors[] = $error;
            }
            throw new Exception("Failed to load private key. OpenSSL errors: " . implode('; ', $errors));
        }

        $testSignature = '';
        if (!openssl_sign($testData, $testSignature, $privateKeyResource, OPENSSL_ALGO_SHA256)) {
            openssl_pkey_free($privateKeyResource);
            throw new Exception("Failed to create test signature");
        }

        // ⭐ Verify test signature
        $publicKey = openssl_pkey_get_details($privateKeyResource)['key'];
        $verifyResult = openssl_verify($testData, $testSignature, $publicKey, OPENSSL_ALGO_SHA256);
        openssl_pkey_free($privateKeyResource);

        if ($verifyResult !== 1) {
            throw new Exception("Test signature verification failed");
        }

        echo "✅ Private key test passed\n";

        // Use current timestamp
        $now = time();
        echo "🕐 Using timestamp: $now (" . date('Y-m-d H:i:s', $now) . ")\n";

        $header = ['alg' => 'RS256', 'typ' => 'JWT'];

        // ⭐ Try with different scopes and shorter expiration
        $scopes = [
            'https://www.googleapis.com/auth/firebase.messaging',
            'https://www.googleapis.com/auth/cloud-platform'
        ];

        foreach ($scopes as $scope) {
            echo "\n🧪 Testing with scope: $scope\n";

            $payload = [
                'iss' => $serviceAccount['client_email'],
                'scope' => $scope,
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 1800 // ⭐ Giảm xuống 30 phút thay vì 1 tiếng
            ];

            echo "🔧 iat: " . $payload['iat'] . " (" . date('Y-m-d H:i:s', $payload['iat']) . ")\n";
            echo "🔧 exp: " . $payload['exp'] . " (" . date('Y-m-d H:i:s', $payload['exp']) . ")\n";

            // ⭐ Use JSON_UNESCAPED_SLASHES to match Google's expected format
            $headerJson = json_encode($header, JSON_UNESCAPED_SLASHES);
            $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);

            echo "🔧 Header JSON: $headerJson\n";
            echo "🔧 Payload JSON: $payloadJson\n";

            $headerEncoded = base64UrlEncode($headerJson);
            $payloadEncoded = base64UrlEncode($payloadJson);
            $signData = $headerEncoded . '.' . $payloadEncoded;

            // Sign with fresh key resource
            $privateKeyResource = openssl_pkey_get_private($privateKey);
            if (!$privateKeyResource) {
                echo "❌ Failed to reload private key\n";
                continue;
            }

            $signature = '';
            if (!openssl_sign($signData, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256)) {
                echo "❌ Failed to sign JWT\n";
                openssl_pkey_free($privateKeyResource);
                continue;
            }

            openssl_pkey_free($privateKeyResource);

            $jwt = $signData . '.' . base64UrlEncode($signature);
            echo "✅ JWT created, length: " . strlen($jwt) . "\n";

            // Test with Google OAuth
            $tokenUrl = 'https://oauth2.googleapis.com/token';
            $postData = [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ];

            echo "🌐 Testing JWT with Google OAuth...\n";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $tokenUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            echo "📡 HTTP Code: $httpCode\n";
            echo "📡 Response: $response\n";

            $responseData = json_decode($response, true);

            if ($httpCode === 200 && isset($responseData['access_token'])) {
                echo "🎉 SUCCESS! Access token obtained with scope: $scope\n";
                return $responseData['access_token'];
            } else {
                echo "❌ Failed with scope: $scope\n";
                if (isset($responseData['error'])) {
                    echo "Error: " . $responseData['error'] . "\n";
                    echo "Description: " . ($responseData['error_description'] ?? 'N/A') . "\n";
                }
            }
        }

        throw new Exception("All scope attempts failed");

    } catch (Exception $e) {
        echo "❌ Error in getAccessToken: " . $e->getMessage() . "\n";
        throw $e;
    }
}

function base64UrlEncode($data) {
    $encoded = base64_encode($data);
    $encoded = str_replace(['+', '/', '='], ['-', '_', ''], $encoded);
    return $encoded;
}

/**
 * Send notification using FCM V1 API
 */
function sendNotificationV1($projectId, $accessToken, $fcmToken, $title, $body, $data = []) {
    $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

    $message = [
        'message' => [
            'token' => $fcmToken,
            'notification' => [
                'title' => $title,
                'body' => $body
            ],
            'data' => $data,
            'android' => [
                'priority' => 'high',
                'notification' => [
                    'sound' => 'default',
                    'channel_id' => 'booking_channel'
                ]
            ]
        ]
    ];

    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    return [
        'success' => $httpCode === 200,
        'result' => $result,
        'http_code' => $httpCode,
        'error' => $error
    ];
}

/**
 * Kiểm tra xem keyword có tồn tại như một từ độc lập trong content hay không
 * Ví dụ: "hạ" sẽ match với "hạ", " hạ ", "hạ," nhưng không match với "phạm", "hạm"
 * ✅ Hỗ trợ Unicode đầy đủ cho tiếng Việt
 */
function isWordMatch($content, $keyword , $ignoreArray = []) {
    if($ignoreArray && in_array($keyword, $ignoreArray)) {
        return false; // Bỏ qua nếu từ khóa nằm trong mảng ignore
    }

    // Character class đầy đủ hơn cho tiếng Việt + các ký tự Latin
    // Bao gồm: a-z, A-Z, các ký tự có dấu tiếng Việt, số, và một số ký tự đặc biệt
    $wordChars = 'a-zA-Z0-9À-ỹĂăÂâÊêÔôƠơƯưĐđ';

    // Sử dụng regex với word boundary tự định nghĩa cho tiếng Việt
    $pattern = '/(?<![' . $wordChars . '])' . preg_quote($keyword, '/') . '(?![' . $wordChars . '])/u';

    return preg_match($pattern, $content) > 0;
}

/**
 * Kiểm tra nhiều keywords có match như từ độc lập hay không
 */
function hasWordMatch($content, $keywords, $ingoreArray = []) {
    if(empty($keywords)) return false;

    foreach($keywords as $keyword) {
        if(isWordMatch($content, $keyword, $ingoreArray)) {
            return true;
        }
    }
    return false;
}

/**
 * @param $message
 * @return void
 * Mỗi khi có tin đến, thì tìm các user đang online, và gửi thông báo đến họ nếu text họ match
 */


function sendAlertForUser($msId) {


    global $SERVICE_ACCOUNT_FILE, $PROJECT_ID, $FCM_TOKEN, $flog;
    //Tìm các user ready = 1
    ol5("# ---- sendAlertForUser , MID = $msId---- \n\n");

    $mm = \App\Models\CrmAppInfo::where("ready", 1)->get();

    foreach ($mm as $item) {

        file_put_contents($flog,  "# ---- sendAlertForUser UID = $item->id ,  $item->last_request ---- \n\n", FILE_APPEND);
        usleep(1000);


        $firebaseToken = $item->firebase_token;
        $request = json_decode($item->last_request, true);

        if (!$firebaseToken) {
            ol5("❌ No Firebase token for user: $item->id");
            continue; // Skip if no token or request data
        }
        if (!$request) {
            ol5("❌ No last request data for user: $item->id");
            continue; // Skip if no last request data
        }

        if(!str_contains($item->last_request, "{")) {
            ol5("❌ Invalid last request format for user: $item->id. Expected JSON format.");
            echo "❌ No last request data for user: $item->id\n";
            continue; // Skip if no last request data
        }

        $js_last_request = json_decode($item->last_request);
        if(!$js_last_request){
            ol5("❌ Failed to decode last request JSON for user: $item->id");
            echo "❌ Failed to decode last request JSON for user: $item->id\n";
            continue; // Skip if JSON decoding fails
        }

        $viTri1 = $js_last_request->vi_tri1;
        $viTri2 = $js_last_request->vi_tri2;
        $phut = $js_last_request->phut;

        $allTin = searchTaxiMessages(
            $viTri1,
            $viTri2,
            $phut, $msId);

        if (!$allTin) {
            ol5("❌ No messages found for: $viTri1 to $viTri2 in last $phut minutes");
            echo "❌ No messages found for: $viTri1 to $viTri2 in last $phut minutes\n";
            continue; // Skip if no messages found
        }

        $message_ids_string = $allTin['message_ids_string'] ?? '';
        if($message_ids_string)
            $message_ids_string = "FBID=$item->id.$message_ids_string";

        ol5("\n message_ids_string = $message_ids_string");
        ol5("✅ Found ok " . count($allTin['messages']) . " messages for: $viTri1 to $viTri2 in last $phut minutes\n");
//        if(!isset($mmMarkStopSendMessage[$message_ids_string])){
//            //Tìm mọi phần tử trong  mảng $mmMarkStopSendMessage mà có key bắt đầu là $item->id, unset key này
//            foreach ($mmMarkStopSendMessage as $key => $value) {
//                if (strpos($key, "FBID=$item->id.") === 0) {
//                    unset($mmMarkStopSendMessage[$key]);
//                }
//            }
//            $mmMarkStopSendMessage[$message_ids_string] = 1;
//
//        }
//        else{
//            $mmMarkStopSendMessage[$message_ids_string]++;
//        }

//        if($mmMarkStopSendMessage[$message_ids_string] > 1){
//            echo "\n❌ Stop send message for: " . $mmMarkStopSendMessage[$message_ids_string] . " times\n";
//            continue;
//        }



        $accessToken = getAccessToken($SERVICE_ACCOUNT_FILE);

        ol5("✅ Access token received\n");

        $urgentData = [
            "booking_id" => "IDF_" . time(),
            "pickup_location" => "Có Khách gọi chuyến của bạn",
            "destination" => "Test Destination",
            "priority" => "urgent"
        ];

        $response = sendNotificationV1(
            $PROJECT_ID,
            $accessToken,
            $firebaseToken,
            "🚨 ($item->id) Taxi Có chuyến mới",
            "Hãy vào App kiểm tra - " . date('H:i:s'),
            $urgentData
        );

        ol5($response['success'] ? "✅ Notification Sent!" : "❌ Failed");
//        echo "\n\n Sleep 15s";

    }

}

//Nếu có msid là chỉ 1 tin
function searchTaxiMessages($diemDi, $diemDen, $nPhut, $msId = 0) {
    global $ignoreArray;

    $diemDi = mb_strtolower($diemDi);
    $diemDen = mb_strtolower($diemDen);
    // Xử lý $diemDi để tách các từ khóa
    $diemDiKeywords = [];
    if($diemDi) {
        $diemDiKeywords = array_map('trim', explode(',', $diemDi));
        $diemDiKeywords = array_filter($diemDiKeywords);
    }


    $query = \App\Models\CrmMessage::select('crm_messages.*', 'crm_message_groups.name AS gname','crm_message_groups.link_group AS link_group' )
        ->leftJoin('crm_message_groups', 'crm_messages.thread_id', '=', 'crm_message_groups.gid');
    if($msId)
        $query->where('crm_messages.id', $msId); // Nếu có msId thì chỉ tìm tin đó
    else
        $query->where('crm_messages.created_at', '>=', now()->subMinute($nPhut))
//            ->where('crm_messages.channel_name', 'anh_taxi')
            ->limit(30);

    // Thêm điều kiện OR cho các từ khóa từ $diemDi
    if(!empty($diemDiKeywords)) {
        $query->where(function($q) use ($diemDiKeywords) {
            foreach($diemDiKeywords as $keyword) {
                $q->orWhere('crm_messages.content', 'LIKE', "%$keyword%");
            }
        });
    }

    $query->orderBy('crm_messages.created_at', 'desc');
    $messages = $query->get();

    //Nếu tin có content giống nhau, chỉ giữ lại 1 cái (lọc trùng)
    $messages = $messages->unique('content');

    // Xử lý $diemDen để tách các từ khóa
    $diemDenKeywords = [];
    if($diemDen) {
        $diemDenKeywords = array_map('trim', explode(',', $diemDen));
        $diemDenKeywords = array_filter($diemDenKeywords);
    }

    //Neu co $diemDen , thi tim tiep
    if(!empty($diemDenKeywords)){
        $messages = $messages->filter(function ($message) use ($diemDenKeywords) {
            foreach($diemDenKeywords as $keyword) {
                if(stripos($message->content, $keyword) !== false) {
                    return true;
                }
            }
            return false;
        });
    }

    //Bỏ qua các message  có đồng thời các ký tự: {, ", :
    $messages = $messages->filter(function ($message) {
        return !str_contains($message->content, '{') &&
               !str_contains($message->content, '"') &&
                !str_contains($message->content, '}') &&
               !str_contains($message->content, ':');
    });

    //Tìm lại điểm đi và điểm đến trong nội dung tin nhắn (sử dụng word boundary)
//    if(0)
    {
    if($diemDiKeywords) {
        $messages = $messages->filter(function ($message) use ($diemDiKeywords, $ignoreArray) {
            return hasWordMatch($message->content, $diemDiKeywords, $ignoreArray);
        });
    }

    if($diemDenKeywords) {
        $messages = $messages->filter(function ($message) use ($diemDenKeywords, $ignoreArray) {
            return hasWordMatch($message->content, $diemDenKeywords, $ignoreArray);
        });
    }
    }

    if($messages->count() == 0) {
        return null; // Không có tin nào phù hợp
    }

    //pluck lấy ra mảng id của all $messages
    $messageIds = $messages->pluck('id')->toArray();

    return [
        'messages' => $messages,
        'message_ids_string' => implode(', ', $messageIds),
        'diemDiKeywords' => $diemDiKeywords,
        'diemDenKeywords' => $diemDenKeywords
    ];
}

/**
 * Test function để kiểm tra word boundary matching
 */
function testWordMatching() {
    echo "<div class='alert alert-info'>";
    echo "<h5>🧪 Test Word Boundary Matching:</h5>";

    // Test case đặc biệt cho "vn" trong "vn385"
    echo "<div style='background: #f0f8ff; padding: 10px; margin: 10px 0; border-left: 4px solid #007bff;'>";
    echo "<strong>🔍 Test case đặc biệt:</strong><br>";
    $testContent = "vn385 hạ rồi - hàng muối ck 300k sedan ki10 kvf5";
    $testKeyword = "vn";
    $match = isWordMatch($testContent, $testKeyword);
    $status = $match ? '✅ MATCH' : '❌ NO MATCH';
    echo "Nội dung: '$testContent'<br>";
    echo "Tìm từ: '$testKeyword' → $status<br>";
    echo "<em>Giải thích: 'vn' không đứng độc lập vì nó là một phần của 'vn385', nên không match</em><br>";
    echo "</div>";

        $testCases = [
        // Test cơ bản
        'phạm' => ['hạ', 'phạ', 'hạm'],
        'hạm' => ['hạ', 'phạ', 'hạm'],
        'phạ' => ['hạ', 'phạ', 'hạm'],
        'hạ' => ['hạ', 'phạ', 'hạm'],
        ' hạ ' => ['hạ', 'phạ', 'hạm'],
        'hạ,' => ['hạ', 'phạ', 'hạm'],
        'đi hạ nội' => ['hạ', 'phạ', 'hạm'],
        'hạ long' => ['hạ', 'phạ', 'hạm'],

        // Test Unicode tiếng Việt
        'đường' => ['đường', 'ường', 'đư'],
        'nghệ an' => ['nghệ', 'hệ', 'an'],
        'đà nẵng' => ['đà', 'nẵng', 'à'],
        'hồ chí minh' => ['hồ', 'chí', 'minh'],
        'thành phố' => ['thành', 'phố', 'ành'],

        // Test với số
        'đường 3/2' => ['đường', '3', '2'],
        'quận 1' => ['quận', '1', 'ận'],

        // Test ký tự đặc biệt
        'bến-xe' => ['bến', 'xe', 'ến'],
        'nhà ga' => ['nhà', 'ga', 'à'],

        // Test thêm các case tương tự "vn385"
        'vn385' => ['vn', '385', 'v'],
        'abc123' => ['abc', '123', 'ab'],
        'đi vn rồi' => ['vn', 'đi', 'rồi'],
        'vn-airlines' => ['vn', 'airlines', 'vn-airlines']
    ];

    foreach($testCases as $content => $keywords) {
        echo "<strong>Nội dung:</strong> '$content'<br>";
        foreach($keywords as $keyword) {
            $match = isWordMatch($content, $keyword);
            $status = $match ? '✅' : '❌';
            echo "&nbsp;&nbsp;- Tìm '$keyword': $status " . ($match ? 'MATCH' : 'NO MATCH') . "<br>";
        }
        echo "<br>";
    }

    echo "</div>";
}

// Thêm vào đầu lib_taxi.php để debug
function debugServiceAccount($serviceAccountFile) {
    echo "🔍 Debugging Service Account File:\n";
    echo "File path: $serviceAccountFile\n";
    echo "File exists: " . (file_exists($serviceAccountFile) ? "✅ YES" : "❌ NO") . "\n";

    if (file_exists($serviceAccountFile)) {
        $content = file_get_contents($serviceAccountFile);
        $json = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "❌ JSON Error: " . json_last_error_msg() . "\n";
            return false;
        }

        $requiredFields = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email'];
        foreach ($requiredFields as $field) {
            echo "  $field: " . (isset($json[$field]) ? "✅" : "❌") . "\n";
        }

        // Kiểm tra private_key format
        if (isset($json['private_key'])) {
            $privateKey = $json['private_key'];
            echo "  private_key starts with: " . substr($privateKey, 0, 27) . "...\n";
            echo "  private_key ends with: ..." . substr($privateKey, -25) . "\n";
        }

        return $json;
    }
    return false;
}
