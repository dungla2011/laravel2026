<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaxiUser
{
    static function getHeaderClientVersion() {
        $ver = '';
        if (isset($_SERVER['HTTP_X_APP_VERSION']))
            $ver = $_SERVER['HTTP_X_APP_VERSION'];
        return $ver;
    }

    static function getKeywordSearch($uid)
    {
        $mm = MonitorItem::where("user_id", $uid)->where('enable',1)->get();

        $keywords = '';
        $len = 0;
        foreach ($mm AS $obj){
            $obj->name = str_replace("\n", ",", $obj->name);
            $tmp = strip_tags($obj->name).",";
            $len+= strlen($tmp);
            $keywords .= $tmp;
            if($len>1000){
                break;
            }
        }

        $str = str_replace(",,",',',$keywords);
        $str = trim($str,',');
        return $str;
    }

    //mark_search_taxi

    /**
     * Lấy danh sách tin nhắn từ DB cần search
     * @param int $uid User ID
     * @return \Illuminate\Database\Eloquent\Collection Mảng CrmMessage models cần search
     */
    static function getMessageNeedSearch($uid)
    {
        // Tìm trong bảng crm_messages
        // Lấy 100 tin nhắn cuối chưa được đánh dấu search_done
        $messages = CrmMessage::where('user_id', $uid)
            ->where('type', 1) //Nhóm thôi, ko nhận cá nhân
            ->where(function($query) {
                $query->where('mark_search_done', '!=', 1)
                      ->orWhereNull('mark_search_done');
            })
            ->orderBy('id', 'desc')
            ->limit(120)
            ->get();

        // Loại bỏ tin nhắn trùng nội dung, chỉ giữ tin mới nhất (id lớn nhất)
        $uniqueMessages = [];
        $seenContent = [];

        foreach ($messages as $message) {
            $content = trim($message->content ?? '');


            // Bỏ qua tin nhắn rỗng
            if (empty($content)) {
                continue;
            }

            //Tin bắt đầu bằng { sẽ bỏ qua, vì đó là json...
            if (str_starts_with($content, '{')) {
                continue;
            }

            // Normalize Unicode (NFC) để tránh vấn đề combining characters
            // Ví dụ: "đ" có thể lưu dưới 2 dạng:
            // - Precomposed: U+0111 (1 ký tự)
            // - Decomposed: d + U+0309 (2 ký tự)
            $content = \Normalizer::normalize($content, \Normalizer::FORM_C);

            // Chỉ giữ tin đầu tiên gặp (vì đã sort theo id desc, nên là tin mới nhất)
            if (!isset($seenContent[$content])) {
                $seenContent[$content] = true;

                // Update content đã normalize về message
                $message->content = $content;
                $uniqueMessages[] = $message;
            }
        }

//        Log::info("TaxiUser::getMessageNeedSearch", [
//            'user_id' => $uid,
//            'total_messages' => count($messages),
//            'unique_messages' => count($uniqueMessages),
//            'duplicates_removed' => count($messages) - count($uniqueMessages)
//        ]);

        return collect($uniqueMessages);
    }

    /**
     * Tìm kiếm tin nhắn chứa từ khóa (Pure function - dễ test)
     * @param \Illuminate\Database\Eloquent\Collection|array $messages Mảng CrmMessage models hoặc [id => content]
     * @param string $keywordStr Chuỗi keyword cách nhau bằng dấu phẩy
     * @param string $opt Chế độ tìm kiếm: 'search_sub_string' (tìm substring) hoặc 'search_whole_word' (tìm whole word)
     * @return \Illuminate\Database\Eloquent\Collection|array Mảng CrmMessage models match (có thêm property content_highlighted)
     */
    static function searchTaxiKeyword($messages, $keywordStr, $opt = 'search_sub_string')
    {
        if (empty($keywordStr) || empty($messages)) {
            return collect([]);
        }

        // Tách các từ tìm (cách nhau bằng dấu phẩy) ra wordList
        $wordList = array_map('trim', explode(',', $keywordStr));
        $wordList = array_filter($wordList); // Loại bỏ phần tử rỗng

        if (empty($wordList)) {
            return collect([]);
        }

        // Chuyển tất cả keywords sang lowercase MB4 unicode
        $wordListLower = array_map(function($word) {
            // Normalize Unicode trước khi lowercase
            $word = \Normalizer::normalize($word, \Normalizer::FORM_C);
            return mb_strtolower($word, 'UTF-8');
        }, $wordList);

//        Log::info("TaxiUser::searchTaxiKeyword", [
//            'message_count' => count($messages),
//            'keyword_count' => count($wordListLower),
//            'keywords' => implode(', ', array_slice($wordListLower, 0, 10))
//        ]);

        $uid = getCurrentUserId();
        $matchedMessages = [];

        // Duyệt qua từng tin nhắn
        foreach ($messages as $message) {
            // Lấy content từ model hoặc từ array
            $content = is_object($message) ? ($message->content ?? '') : ($message['content'] ?? $message);

            if (empty($content)) {
                continue;
            }

            // Normalize Unicode trước khi xử lý
            $content = \Normalizer::normalize($content, \Normalizer::FORM_C);

            // Chuyển content sang lowercase MB4 unicode
            $contentLower = mb_strtolower($content);

            $matchedKeywords = [];
            $contentHighlighted = $content; // Giữ nguyên case gốc

            // Kiểm tra từng keyword
            foreach ($wordListLower as $index => $keywordLower) {
                if ($opt === 'search_sub_string') {

                    if($uid == 1){
//                        die("ABC123");
                    }

                    // Tìm kiếm substring (simple search)
                    // Chỉ cần keyword xuất hiện bất kỳ đâu trong content
                    if (str_contains($contentLower, $keywordLower)) {
                        // Lưu keyword gốc (giữ nguyên case)
                        $matchedKeywords[] = $wordList[$index];
                    }
                } else {
                    // Tìm khớp nguyên word (whole word matching với Unicode support)
                    // Pattern giải thích:
                    // (?<![...]) : Negative lookbehind - trước keyword không phải là chữ/số/dấu tiếng Việt
                    // (?![...])  : Negative lookahead - sau keyword không phải là chữ/số/dấu tiếng Việt
                    // \p{L} : Unicode letter (bao gồm TẤT CẢ chữ cái có dấu, kể cả đ/Đ)
                    $pattern = '/(?<![a-zA-Z0-9\p{L}])' .
                               preg_quote($keywordLower, '/') .
                               '(?![a-zA-Z0-9\p{L}])/u';

                    if (preg_match($pattern, $contentLower)) {
                        // Lưu keyword gốc (giữ nguyên case)
                        $matchedKeywords[] = $wordList[$index];
                    }
                }
            }

                        // Nếu có keyword match, highlight chúng trong content
            if (!empty($matchedKeywords)) {
                // Loại bỏ duplicate keywords
                $matchedKeywords = array_unique($matchedKeywords);

                // Highlight tất cả matched keywords trong content (case-insensitive)
                foreach ($matchedKeywords as $keyword) {
                    if ($opt === 'search_sub_string') {
                        // Highlight substring (simple replace)
                        // Pattern để tìm keyword với case-insensitive (không cần word boundary)
                        $pattern = '/(' . preg_quote($keyword, '/') . ')/ui';

                        // Replace với <b>keyword</b> (giữ nguyên case của từ trong content)
                        $contentHighlighted = preg_replace($pattern, '<b style="color: red">$1</b>', $contentHighlighted);
                    } else {
                        // Highlight whole word (với word boundary)
                        // Pattern để tìm keyword với case-insensitive
                        // \p{L} : Unicode letter (bao gồm TẤT CẢ chữ cái có dấu)
                        $pattern = '/(?<![a-zA-Z0-9\p{L}])(' .
                                   preg_quote($keyword, '/') .
                                   ')(?![a-zA-Z0-9\p{L}])/ui';

                        // Replace với <b>keyword</b> (giữ nguyên case của từ trong content)
                        $contentHighlighted = preg_replace($pattern, '<b style="color: red">$1</b>', $contentHighlighted);
                    }
                }

                // Thêm property content_highlighted vào model
                if (is_object($message)) {
                    // Clone object để không modify object gốc
                    $messageClone = clone $message;
                    $messageClone->content_highlighted = $contentHighlighted;
                    $matchedMessages[] = $messageClone;
                } else {
                    // Nếu là array, giữ nguyên format cũ
                    $id = $message['id'] ?? array_search($message, $messages);
                    $matchedMessages[$id] = $contentHighlighted;
                }
            }
        }

//        Log::info("TaxiUser::searchTaxiKeyword - Result", [
//            'total_messages_checked' => count($messages),
//            'matched_count' => count($matchedMessages)
//        ]);

        return collect($matchedMessages);
    }

}
