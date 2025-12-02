<!--<meta http-equiv="refresh" content="0.1">-->

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($disableForm))
    $disableForm = false; // Mặc định không tắt form tìm kiếm

global $disableForm;

define("DEF_TOOL_CMS", 1);
//$_SERVER['SERVER_NAME'] = '';
require "/var/www/html/public/index.php";
require_once "lib_taxi.php";

$time  = date("H:i:s d/m/Y");
echo "<div style='text-align: right; font-size: small; padding-right: 10px '> $time </div>";

function ol1($str)
{
    file_put_contents("/var/glx/weblog/taxi_2025.log", date("Y-m-d H:i:s") . "#" . $str . "\n", FILE_APPEND);
}


/**
 * Highlight text với các từ khóa
 */
function highlightKeywords($content, $keywords) {
    $highlightedContent = $content;
    if(!empty($keywords)) {
        foreach($keywords as $keyword) {
            $highlightedContent = preg_replace(
                '/('.preg_quote($keyword, '/').')/iu',
                '<span class="highlight-text">$1</span>',
                $highlightedContent
            );
        }
    }
    return $highlightedContent;
}

/**
 * Hiển thị kết quả tìm kiếm
 */
function displaySearchResults($searchResult) {

    if(!$searchResult){
        echo '<div class="alert alert-danger">Không có tin nào phù hợp?</div>';
        return;
    }

    $messages = $searchResult['messages'];
    $diemDiKeywords = $searchResult['diemDiKeywords'];
    $diemDenKeywords = $searchResult['diemDenKeywords'];

    // Hiển thị số lượng kết quả
    echo '<div class="d-flex justify-content-between align-items-center mb-1">

            <h4 onclick="window.location.href = window.location.href" class="mb-0"><i class="fas fa-comments me-2 text-primary"></i>Kết quả </h4>

            <span class="badge stats-badge px-2 py-1 fs-6">
                <i class="fas fa-envelope me-1"></i>' . $messages->count() . ' tin nhắn
            </span>
          </div>';

    if($messages->count() > 0) {
        echo '<div class="row">';
        $cc = 0;
        foreach ($messages as $message) {
            $cc++;

            // Xử lý highlight content
            $highlightedContent = $message->content;
            $highlightedContent = highlightKeywords($highlightedContent, $diemDiKeywords);
            $highlightedContent = highlightKeywords($highlightedContent, $diemDenKeywords);

            // Format thời gian
            $timeAgo = \Carbon\Carbon::parse($message->created_at)->diffForHumans();
            $timeDetail = \Carbon\Carbon::parse($message->created_at)->format('H:i d/m');

            echo '<div class="col-12 mb-2">
                    <div class="card message-card">
                        <div class="card-body p-1 compact-content content123">
                            <div class="d-flex1 justify-content-between1 align-items-start1">
                                <div class="flex-grow-1 me-2">
                                    <div class="d-flex1 align-items-center1 mb-1">
                                        <span class="badge bg-info compact-badge me-0">' . $cc .'.'. $message->id. '</span>
                                        <small class="text-muted me-1">' . $timeAgo . ' (' . $timeDetail . ')</small>
                                        <div></div>
                                        ';

            if($message->link_group) {
                echo '
                <a style="text-decoration: none " href="' . $message->link_group . '" target="_blank">
                 <div class="mt-1">
                 <span class="badge bg-primary compact-badge text-decoration-none py-1 my-1 span_group_ok" style="">
                                        <i class="fas fa-external-link-alt me-1"></i>' . ($message->gname ?? 'Nhóm') . '
                                    </span>
                                    <span> '.$highlightedContent.' </span>
                                    </div>
                </a>
                                    ';
            } else {
                echo '            <div>  <span class="badge bg-warning compact-badge me-1 my-1 span_group_ok">' . ($message->gname ?? 'N/A') . '</span> <span> '.$highlightedContent.' </span>  </div>';
            }

            echo '</div>
                                    <div class="text-break" style="font-size: 0.9rem; line-height: 1.3; max-height: 2.6rem; overflow: hidden;">
                                        ' . '' . '
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>';
        }
        echo '</div>';
    } else {
        echo '<div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-search fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted">Không tìm thấy tin nhắn nào</h4>
                <p class="text-muted">Hãy thử điều chỉnh điều kiện tìm kiếm của bạn</p>
              </div>';
    }
}

/**
 * Hiển thị điều kiện tìm kiếm
 */
function displaySearchConditions($diemDiKeywords, $diemDenKeywords, $nPhut) {
    if(!$diemDenKeywords && !$diemDiKeywords ) return;

    $time =  nowyh();
    return;
    if(!empty($diemDiKeywords) || !empty($diemDenKeywords) || $nPhut) {
        echo '<div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                    <strong>Điều kiện tìm kiếm:</strong> ';

        if(!empty($diemDiKeywords)) {
            echo "Điểm đi: ";
            foreach($diemDiKeywords as $index => $keyword) {
                if($index > 0) echo " HOẶC ";
                echo "<strong>$keyword</strong>";
            }
            echo " ";
        }

        if(!empty($diemDenKeywords)) {
            echo "và điểm đến: ";
            foreach($diemDenKeywords as $index => $keyword) {
                if($index > 0) echo " HOẶC ";
                echo "<strong>$keyword</strong>";
            }
            echo " ";
        }

        if($nPhut) echo "trong <strong>$nPhut</strong> phút gần đây";
        echo '</div></div>';
    }
}

/**
 * Test function để kiểm tra collation và tìm kiếm
 */
function testSearchCollation($keyword1, $keyword2) {
    echo "<div class='alert alert-warning'>";
    echo "<h5>🧪 Test Collation:</h5>";

    // Test với LIKE thường
    $result1 = \App\Models\CrmMessage::where('content', 'LIKE', "%$keyword1%")->count();
    $result2 = \App\Models\CrmMessage::where('content', 'LIKE', "%$keyword2%")->count();

    echo "LIKE thường:<br>";
    echo "- '$keyword1': $result1 kết quả<br>";
    echo "- '$keyword2': $result2 kết quả<br>";

    // Test với BINARY
    $result3 = \App\Models\CrmMessage::whereRaw('BINARY content LIKE ?', ["%$keyword1%"])->count();
    $result4 = \App\Models\CrmMessage::whereRaw('BINARY content LIKE ?', ["%$keyword2%"])->count();

    echo "<br>BINARY LIKE:<br>";
    echo "- '$keyword1': $result3 kết quả<br>";
    echo "- '$keyword2': $result4 kết quả<br>";

    // Kiểm tra collation
    $collation = \DB::select("SHOW FULL COLUMNS FROM crm_messages WHERE Field = 'content'")[0] ?? null;
    if($collation) {
        echo "<br>Collation hiện tại: <strong>" . $collation->Collation . "</strong><br>";
    }

    echo "</div>";
}

$fullUrl = \LadLib\Common\UrlHelper1::getFullUrl();
ol1("\n\n URLx = $fullUrl");
//ol1("+++ Server2 = ". serialize($_SERVER));

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'test2023.mytree.vn';;
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';


$nPhut =  request('phut') ?? '';;

if($nPhut > 300)
    $nPhut = 300; // Giới hạn tối đa là 6 giờ
if(!$nPhut)
    $nPhut = 5;

//$nHour = floor($nPhut / 60); // Chuyển đổi phút sang giờ

$diemDi = request('vi_tri1') ?? '';
$diemDen = request('vi_tri2') ?? '';


$alert_from1 = $_GET['alert_from1'] ?? '';
$alert_to1 = $_GET['alert_to1'] ?? '';
$alert_from2 = $_GET['alert_from2'] ?? '';
$alert_to2 = $_GET['alert_to2'] ?? '';


//Nếu là app thì mới cần xử lý user đưa vào db
if(str_contains($userAgent, 'taxi_driver_2025') || $disableForm){
    $HTTP_X_FCM_TOKEN = $_SERVER['HTTP_X_FCM_TOKEN'] ?? '';
    if (!$HTTP_X_FCM_TOKEN)
        $HTTP_X_FCM_TOKEN = $_REQUEST['HTTP_X_FCM_TOKEN'] ?? '';
    if ($HTTP_X_FCM_TOKEN) {
        $obj = \App\Models\CrmAppInfo::insertOrUpdateFBTokenAndReadyStatus(
            $HTTP_X_FCM_TOKEN, -1);

        if($obj && ($alert_from1 || $alert_from2)){
                $json = json_encode([
                    'from1' => $alert_from1,
                    'to1' => $alert_to1,
                    'from2' => $alert_from2,
                    'to2' => $alert_to2
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                if($obj->alert_from != $json) {
                    $obj->alert_time = $json;
                    $obj->addLog("Change alert_from: " . $obj->alert_time);
                    $obj->update();
                }
        }

        if($obj && $diemDi && $nPhut){
            $mm = [
                'vi_tri1'=>$diemDi,
                'vi_tri2'=>$diemDen,
                'phut'=>$nPhut,
            ];
            $lastR = json_encode($mm, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            if($obj->last_request != $lastR){
                $obj->last_request = $lastR;
//                $obj->addLog("Change last_request: " . $obj->last_request);
                $obj->update();
            }
        }
    }
}

__UI:

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm Kiếm Tin Nhắn Taxi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .search-form {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .span_group_ok{
            float: right; font-size: 80%; position: absolute; top: 2px; right: 5px;
        }
        .message-card {
            transition: transform 0.2s ease-in-out;
            border-left: 4px solid #007bff;
            /*max-height: 80px;*/
            overflow: hidden;
        }
        .message-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .compact-content {
            font-size: 0.85rem;
            line-height: 1.2;
        }
        .compact-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
        .highlight-text {
            /*border: 2px solid #dc3545;*/
            /*background-color: #fff3cd;*/
            /*padding: 2px 4px;*/
            /*border-radius: 3px;*/
            font-weight: bold;
            color: red;
        }
        .stats-badge {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border-radius: 50px;
        }
        .header-title {
            background: linear-gradient(45deg, #007bff, #6610f2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-2 pb-3">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">

                <?php
                if( isset($disableForm) && !$disableForm){
                ?>
                <!-- Search Form -->
                <div class="card search-form mb-4">
                    <div class="card-body px-2" style="padding: 5px">
                        <form action="" method="get" class="row g-3">
                            <div class="col-md-3 col-xs-6">
                                <label for="vi_tri1" class="form-label text-white fw-semibold">
                                    <i class="fas fa-map-marker-alt me-1"></i>Điểm đi
                                </label>
                                <input type="text" class="form-control"
                                       id="vi_tri1" name="vi_tri1"
                                       placeholder="Nhập điểm đi..."
                                       value="<?php echo htmlspecialchars($diemDi) ?>">
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <label for="vi_tri2" class="form-label text-white fw-semibold">
                                    <i class="fas fa-map-marker-alt me-1"></i>Điểm đến
                                </label>
                                <input type="text" class="form-control"
                                       id="vi_tri2" name="vi_tri2"
                                       placeholder="Nhập điểm đến..."
                                       value="<?php echo htmlspecialchars($diemDen) ?>">
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <label for="phut" class="form-label text-white fw-semibold">
                                    <i class="fas fa-clock me-1"></i>Mấy phút gần đây
                                </label>
                                <input type="number" class="form-control "
                                       id="phut" name="phut"
                                       placeholder="Nhập số phút..."
                                       value="<?php echo $nPhut ?>"
                                       max="300" min="1">
                            </div>
                            <div class="col-md-3  col-xs-6 text-center">
                                <button type="submit" class="btn btn-sm btn-light px-5 py-2 mt-0 fw-semibold">
                                    <i class="fas fa-search "></i>TÌM
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
                }
                ?>
<?php
// Test collation nếu có tham số test
if(request('test_collation')) {
    testSearchCollation('hạ', 'hà');
}

// Test word boundary nếu có tham số test
if(request('test_word')) {
    testWordMatching();
}

// Chỉ thực hiện tìm kiếm khi có điều kiện
if($diemDi || $diemDen || $nPhut) {
    // Tìm kiếm tin nhắn
    $searchResult = searchTaxiMessages($diemDi, $diemDen, $nPhut);

    // Hiển thị điều kiện tìm kiếm
    displaySearchConditions($searchResult['diemDiKeywords'] ?? null, $searchResult['diemDenKeywords']?? null, $nPhut);

    // Hiển thị kết quả
    displaySearchResults($searchResult);

    // Lưu $messages để sử dụng cho JavaScript
    $messages = $searchResult['messages'];
}
?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        // Auto-focus vào input đầu tiên
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.getElementById('vi_tri1');
            if (firstInput && !firstInput.value) {
                firstInput.focus();
            }
        });

        // Thêm hiệu ứng loading khi submit form
        document.querySelector('form').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tìm kiếm...';
            submitBtn.disabled = true;
        });

        // Smooth scroll khi có kết quả
        <?php if(isset($messages) && $messages->count() > 0): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const resultsSection = document.querySelector('.d-flex.justify-content-between.align-items-center');
            if (resultsSection) {
                resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
