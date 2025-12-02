<?php

require "/var/www/html/public/index.php";

$ct = request('content');
if($ct)
    echo removeCommentsWithDOM($ct);
die();
function removeCommentsWithDOM21($html) {
    // Nếu nội dung trống, trả về luôn
    if (empty($html)) {
        return $html;
    }

    // Tạo một đối tượng DOMDocument
    $dom = new DOMDocument('1.0', 'UTF-8');

    // Tắt báo lỗi HTML không chuẩn
    libxml_use_internal_errors(true);

    // Thêm header charset UTF-8 và wrapper để đảm bảo Unicode được xử lý đúng
    $html = '<?xml encoding="UTF-8">' .
        '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>' .
        '<body><div id="wrapper">' . $html . '</div></body></html>';

    // Load HTML với các tùy chọn
    $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    // Lấy các lỗi và xóa chúng
    libxml_clear_errors();

    // Danh sách các phần tử cần xóa
    $nodesToRemove = [];

    // Tìm tất cả các thẻ p
    $paragraphs = $dom->getElementsByTagName('p');

    // Xử lý DOM theo cách bảo tồn các nút
    $paragraphsToRemove = [];
    foreach ($paragraphs as $p) {
        $paragraphsToRemove[] = $p;
    }

    // Xử lý từng paragraph
    foreach ($paragraphsToRemove as $p) {
        $hasComment = false;

        // Kiểm tra text content trực tiếp của p
        $content = trim($p->textContent);
        if (strpos($content, '###') === 0) {
            $hasComment = true;
        } else {
            // Nếu không có ### ở đầu text content, kiểm tra các span bên trong
            $spans = $p->getElementsByTagName('span');
            $spansArray = [];
            foreach ($spans as $span) {
                $spansArray[] = $span;
            }

            // Kiểm tra từng span trong p
            foreach ($spansArray as $span) {
                $spanContent = trim($span->textContent);
                if (strpos($spanContent, '###') === 0) {
                    $hasComment = true;
                    break;
                }
            }
        }

        // Nếu có comment ###, đánh dấu để xóa p
        if ($hasComment && $p->parentNode) {
            $nodesToRemove[] = $p;
        }
    }

    // Xóa các node đã đánh dấu
    foreach ($nodesToRemove as $node) {
        if ($node->parentNode) {
            $node->parentNode->removeChild($node);
        }
    }

    // Lấy nội dung HTML đã được xử lý
    $wrapper = $dom->getElementById('wrapper');
    $processedHtml = '';

    if ($wrapper) {
        // Lưu nội dung HTML của các nút con
        foreach ($wrapper->childNodes as $child) {
            $processedHtml .= $dom->saveHTML($child);
        }
    }

    return $processedHtml;
}
//
//if(1){
//    $str = '
//<p>Dear <span class="s5">[TENKHACH]</span>,</p>
//<p>The Diplomatic Academy of Vietnam cordially invites you to attend [EVENT_NAME].</p>
//<p>Event details:</p>
//<div class="s3"><span class="s11">• </span>Time:<span class="s5"><em><strong>[START_TIME]</strong></em> to <strong>[END_TIME]</strong></span></div>
//<div class="s3"><span class="s11">• </span>Venue: <span class="s5"><strong>[LOCATION]</strong></span></div>
//<div class="s3"><span class="s11">###• </span>Topic:</div>
//<div class="s3"><span class="s11">###• </span>Language:</div>
//<p>To confirm your participation, please register via the following link: <span class="s5">[LINKTHAMDU]</span>.</p>
//<p>###For further information, please contact [Name], at [Phone Number], or via email: [Email].</p>
//<p>The Diplomatic Academy of Vietnam sincerely appreciates your interest and looks forward to welcoming you at [EVENT_NAME].</p>
//';
//
//    echo removeCommentsWithDOM21($str);
//
//}
