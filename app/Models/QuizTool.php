<?php

namespace App\Models;

use Stichoza\GoogleTranslate\GoogleTranslate;

class QuizTool
{
    /**
     * @param  $objDone  QuizQuestion
     */
    public static function checkQuizTrue($objQuestOrId, $input)
    {

        if (is_numeric($objQuestOrId)) {
            if (! $objQuestOrId = QuizQuestion::find($objQuestOrId)) {
                loi("Not found questID: $objQuestOrId");
            }
        }

        if (is_array($input)) {
            $input = implode("\n", $input);
        }
        $input = trim($input);

        $objQuestOrId->answer = trim($objQuestOrId->answer);

        if (($objQuestOrId->answer) == ($input)) {
            return true;
        }
        //        if($objQuestOrId->id == 768)
        //            echo "<br/>\nABC: ".  json_encode($objQuestOrId->answer) ."!==". json_encode($input);

        return false;
    }

    public static function hetTimeLamBai($objSession)
    {
        if (! $objSession->end_time_do) {
            return false;
        }
        if ($objSession->end_time_do < nowyh()) {
            return true;
        }

        return false;
    }

    public static function hetTimeMoCauTraLoi($objSession)
    {
        if ($objSession->close_answer_time < nowyh()) {
            return true;
        }

        return false;
    }

    public static function translateW3SchoolToVnIgnoreTextInQuote($text)
    {

        //<code class="w3-codespan">Thành phố</code>
        //        if(isSupperAdmin_())

        $text = str_replace('<code class="w3-codespan">', '@@@PLACEHOLDER', $text);
        $text = str_replace('</code>', '@@@', $text);
        //            echo "<br/>\n TXT = $text";

        $tr = new GoogleTranslate('vi'); // Translates into English
        // Phần quan trọng: tìm và lưu trữ các đoạn trong dấu nháy
        preg_match_all('/"[^"]*"/', $text, $matches);

        $placeholders = $matches[0];
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($placeholders);
        //        echo "</pre>";
        // Tạm thời thay thế các đoạn trong dấu nháy bằng placeholders
        foreach ($placeholders as $index => $placeholder) {
            $text = str_replace($placeholder, "@@@PLACEHOLDER{$index}@@@", $text);
        }
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($text);
        //        echo "</pre>";
        $translatedText = $tr->translate($text);
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($translatedText);
        //        echo "</pre>";
        $translatedText = str_replace('@ @', '@@', $translatedText);
        // Phục hồi các đoạn trong dấu nháy
        foreach ($placeholders as $index => $placeholder) {
            $translatedText = str_replace("@@@PLACEHOLDER{$index}@@@", $placeholder, $translatedText);
        }

        //        if(isSupperAdmin_())

        $translatedText = str_replace('@@@PLACEHOLDER', '<code class="w3-codespan">', $translatedText);
        $translatedText = str_replace('@@@', '</code>', $translatedText);

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($translatedText);
        //        echo "</pre>";
        return $translatedText;
    }
}
