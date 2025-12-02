<?php

/**
 * Lấy nội dung câu hỏi trên W3School, tách ra các nội dung, show lên web
 * Require: simple_html_dom.php
 */
class qA_W3School
{
    public $questionText;

    public $questionCont;

    public $answerCont;

    public $answerArray;

    public $section;

    public function __construct($html = null)
    {
        if (! $html) {
            return;
        }
        $x = str_get_html($html, $lowercase = true,
            $forceTagsClosed = true,
            $target_charset = DEFAULT_TARGET_CHARSET,
            $stripRN = false);

        //        $ct = $x->find("#assignmenttext", 0)->firstChild()->innertext;

        $ct = '';
        foreach ($x->find('#assignmenttext', 0)->childNodes() as $ch) {
            if ($ch->id == 'assignmentcode') {
                break;
            }
            //            $ct .= $ch->innertext;
            $ct .= $ch->outertext;
        }

        $this->questionText = $ct;

        $ct = $x->find('#assignmentcode', 0)->innertext;

        //Giảm bớt xuống dòng:
        //        $ct = str_replace("<br>", "", $ct);
        //        $ct = str_replace("<br/>", "", $ct);
        //        $ct = str_replace("\n\n", "\n", $ct);
        //
        $this->questionCont = $ct;

        $ct = $x->find('#correctcode', 0)->innertext;
        $this->answerCont = $ct;

        $section = '';
        $lines = explode("\n", $html);
        foreach ($lines as $l1) {
            $l1 = trim($l1);
            if (str_starts_with($l1, 'var exsection')) {
                $section = trim(str_replace(';', '', explode('=', $l1)[1]));
            }
        }

        $section = str_replace('"', '', $section);
        $this->section = trim($section);
        $this->answerArray = $this->getMangCauTraLoi($this->questionCont, $this->answerCont, $section);

    }

    public static function showIframe($objQuestionInDbQuiz_OR_id)
    {

        //        $objQuestionInDbQuiz_OR_id = '';
        if (is_numeric($objQuestionInDbQuiz_OR_id)) {
            $idf = $objQuestionInDbQuiz_OR_id;
            $objQuestionInDbQuiz_OR_id = \App\Models\QuizQuestion::find($objQuestionInDbQuiz_OR_id);
            if (! $objQuestionInDbQuiz_OR_id) {
                loi("Not found id: '$idf'");
            }
        }

        $objx = $objQuestionInDbQuiz_OR_id;

        if (! $objx->content_vi) {
            $txt = \App\Models\QuizTool::translateW3SchoolToVnIgnoreTextInQuote($objx->content);
            if ($txt) {
                $objx->content_vi = $txt;
                $objx->addLog('translate in showIframe');
                $objx->save();
            }
        }

        $link = $objQuestionInDbQuiz_OR_id->refer;
        $questionText = $objQuestionInDbQuiz_OR_id->content;
        $content_textarea = $objQuestionInDbQuiz_OR_id->content_textarea;

        echo " Link question: <a href='$link' target='_blank'>$link</a> ";

        //        $ct = file_get_content_cache($link);
        //
        $obj = new qA_W3School();
        $obj->codeCss();

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($obj);
        //        echo "</pre>";

        echo "<br/>\n";
        echo "<b>- Question: $questionText </b>";
        echo "<br/>\n";
        echo "<b>- Question (VN): $objQuestionInDbQuiz_OR_id->content_vi  </b>";
        ?>

        <div id="assignmenttext" style=''></div>
        <pre id="assignmentcontainer" style="overflow:auto"></pre>
        <div id="assignmentcode" style="display: none"><?php echo $content_textarea ?></div>

        <?php

        echo "<div style='background-color: lavender; padding: 10px; border: 1px  solid #ccc'><br>";

        $cc = 0;
        $mA = explode("\n", $objQuestionInDbQuiz_OR_id->answer);
        foreach ($mA as $one) {
            $cc++;
            echo "Input $cc: <span style='color: red'> ".htmlentities($one).'</span>';
            echo "<br/>\n";
        }
        echo '</div>';
        $obj->codeJs();
    }

    public static function sampleCode()
    {
        $link = 'https://www.w3schools.com/html/exercise.asp?filename=exercise_html_styles1';
        $link = 'https://www.w3schools.com/css/exercise.asp?filename=exercise_css3_animations6';
        //        $link = 'https://www.w3schools.com/html/exercise.asp?filename=exercise_html_styles6';
        //        $link = 'https://www.w3schools.com/html/exercise.asp?filename=exercise_html_formatting3';
        echo " Link question: <a href='$link' target='_blank'>$link</a> ";
        echo ' <hr> ';

        $ct = file_get_content_cache($link);

        $obj = new qA_W3School($ct);

        $obj->codeCss();

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($obj);
        //        echo "</pre>";

        echo "<h3> Question: $obj->questionText </h3>";
        ?>

        <div id="assignmenttext" style=''></div>
        <pre id="assignmentcontainer" style="overflow:auto"></pre>
        <div id="assignmentcode" style="display: none"><?php echo $obj->questionCont ?></div>
        <button id="confirm_result"> <b> Confirm </b> </button>
        <br><br>
        <?php

        echo "<div style='background-color: lavender; padding: 10px; border: 1px  solid #ccc'>
Answers Input (admin zone): <br>";

        $cc = 0;
        foreach ($obj->answerArray as $one) {
            $cc++;
            echo "<br/>\n Input $cc: <span style='color: red'> ".htmlentities($one).'</span>';
        }
        echo '</div>';
        $obj->codeJs();
    }

    public function codeJs()
    {
        ?>
        <script>
            window.document.onload = function (){
            }
            if(document.getElementById('confirm_result'))
            document.getElementById('confirm_result').onclick = function (){
                let kq = JSON.parse('<?php echo json_encode($this->answerArray) ?>');
                let count = 0;
                let strKq = "";
                document.querySelectorAll('.editablesection').forEach(function (elm){
                    let valInput = kq[count].trim()
                    if(valInput == elm.value) {
                        strKq += " - Input " + (count + 1) + ": True \n";
                        console.log(" Đúng ");
                    }
                    else {
                        strKq += " - Input " + (count + 1) + ": False \n";
                        console.log(" Sai ");
                    }
                    console.log(" ELM val = ", elm.value);
                    count++;
                })

                alert("Your result: \n" + strKq)

                console.log(" KQ " , kq);
            }

            var formanswers = [];
            var editable = false;
            var trimcheck = true;
            var invalue = false;
            formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');
            var originalassignmentcode;

            function initAssignment() {
                var x, y, txt, i, newtxt, c, cc, n, numberofchar, j, inputs, templates, l, inputcount = -1, endpos, endpos2, inputvalue;
                document.getElementById("assignmenttext").style.display = "block";
                x = document.getElementById("assignmentcode");
                x.style.display = "none";
                txt = x.innerHTML;
                originalassignmentcode = txt;
                if (x.getAttribute("trimcheck") == "false") {trimcheck = false;}
                if (x.getAttribute("contenteditable") == "true") {
                    editable = true;
                    document.getElementById("assignmentcontainer").innerHTML = txt;
                    document.getElementById("assignmentcontainer").setAttribute("contenteditable", "true");
                    document.getElementById("assignmentcontainer").addEventListener("keydown", function(e) {
                        if(e.keyCode == 9){
                            e.preventDefault();
                            var doc = document.getElementById("assignmentcontainer").ownerDocument.defaultView;
                            var sel = doc.getSelection();
                            var range = sel.getRangeAt(0);
                            var blankspaces = document.createTextNode("    ");
                            range.insertNode(blankspaces);
                            range.setStartAfter(blankspaces);
                            range.setEndAfter(blankspaces);
                            sel.removeAllRanges();
                            sel.addRange(range);
                        }
                    });
                } else {
                    newtxt = "";
                    for (i = 0; i < txt.length; i++) {
                        c = txt[i]
                        numberofchar = 0;
                        if (c == "@") {
                            inputcount++
                            inputvalue = "";
                            if (txt[i + 1] == "(") {
                                startpos = i + 2;
                                endpos = txt.indexOf(")", startpos);
                                //if (txt.indexOf("(", startpos) > -1) {
                                //  if (txt.indexOf("(", startpos) < endpos) {
                                //    endpos = txt.indexOf(")", endpos + 1);
                                //  }
                                //}
                                //endpos2 = txt.indexOf(",", startpos);
                                //if (endpos2 > -1) {
                                //  invalue = true;
                                //  inputvalue = txt.substring(endpos2 + 1, endpos);
                                //  n = txt.substring(startpos, endpos2)
                                //  if (!isNaN(n)) {numberofchar = Number(n) + inputvalue.length;}
                                //} else {
                                n = txt.substring(startpos, endpos)
                                if (!isNaN(n)) {numberofchar = n;}
                                //}
                            }
                            if (numberofchar > 0) {
                                i = endpos;
                                c = "<pre class='meassureInputWidth'>"
                                for (j = 0; j < numberofchar; j++) {
                                    c += " ";
                                }
                                c += "</pre>"
                                c += "<input spellcheck='false' class='editablesection' maxlength='" + numberofchar + "'>"
                                //c += "<input value='" + inputvalue + "' spellcheck='false' class='editablesection' onkeypress='checkKey(event)' oninput='writinginput(this, " + inputcount + ")' maxlength='" + numberofchar + "'>"
                            }
                        }
                        newtxt += c;
                    }
                    document.getElementById("assignmentcontainer").innerHTML = newtxt;
                    inputs = document.getElementsByClassName("editablesection");
                    templates = document.getElementsByClassName("meassureInputWidth");
                    for (i = 0; i < inputs.length; i++) {
                        inputs[i].style.width = ((templates[i].offsetWidth) + 4) + "px";
                        templates[i].style.display = 'none';
                        templates[i].innerHTML = "w3exercise_input_no_" + i;
                        //if (inputs[i].value == "") {
                        cc = formanswers[i];
                        cc = cc.replace(/&apos;/g, "'");
                        cc = cc.replace(/&quot;/g, '"');
                        inputs[i].value = cc;
                        //}
                    }
                }
                //window.setTimeout(function () {inputs[0].focus()}, 800);
            }

            function checkKey(event) {
                if (event.keyCode == 13) {
                    checkassignmentcode();
                    uic_r_p();
                }
            }

            initAssignment();

        </script>
        <?php
    }

    public function codeCss()
    {
        ?>
        <style>
            #assignmentNotCorrect, #assignmentCorrect {
                display:none;
                width:100%;
                height:100%;
                position:absolute;
                background-color:rgba(242, 222, 222, 0.99);
                background-color:#FFC0C7;
                padding:50px;
                z-index:1;
                color: #b94a48;
                cursor:pointer;
                border-radius:5px;
            }

            #assignmentCorrect {
                color: #04AA6D;
                background-color: #D9EEE1;
            }

            #assignmentcontainer, #showcorrectanswercontainer {
                font-size: 120%;
                background-color:#ddd;
                padding:30px;
                padding-top:0;
                /*padding-bottom:90px;*/
                /*min-height:250px;*/
                font-family:Consolas,Menlo,"Courier New", Courier, monospace;
                /*font-size:120%;*/
                line-height:1.5em;
                border-radius:5px;
            }
            #assignmentcontainer[contenteditable], #assignmentcontainer[contenteditable]~#showcorrectanswercontainer {
                background-color:#fff;
                outline:10px solid #f1f1f1;
            }

            body.darkpagetheme #assignmentcontainer[contenteditable],body.darkpagetheme  #assignmentcontainer[contenteditable]~#showcorrectanswercontainer {
                background-color:#38444d;
            }

            body.darkpagetheme #assignmentcontainer,body.darkpagetheme  #showcorrectanswercontainer {
                background-color:#38444d;
            }


            #showcorrectanswercontainer {
                display:none;
            }
            .editablesection {
                background-color:#ffffff;
                display:inline-block;
                border:none;
                border-top: 1px dashed #ccc;
                border-bottom: 1px dashed #ccc;
                height:1.8em;
                padding:1px 2px;
                font-size: 100%;
                color: brown;

                /*font-family:'Source Code Pro',Menlo,Consolas,"Courier New", Courier, monospace;*/
                /*
                  height:1.2em;
                  padding:0;
                  outline-offset:0;*/
            }
            /*
            .editablesection:focus {
              outline:2px solid #ffffff;
            }
            */
            .meassureInputWidth {
                display:inline-block;
                position:absolute;
            }

            .w3-codespan {
                color:#000000;
            }

            [id^="correctcode"] {
                display:none;
            }

            #showcorrectanswercontainer input {
                color:mediumblue;
            }

            .phonebr {
                display:none;
            }

            .showanswerbutton, .hideanswerbutton {
                background-color:#282A35;
                position:absolute;
                right:20px;
                bottom:20px;
            }
            .showanswerbutton:hover,.showanswerbutton:active, .hideanswerbutton:hover,.hideanswerbutton:active {
                background-color:#000!important;
            }
            @media screen and (max-width: 899px) {
                #answerbuttoncontainer {
                    position:fixed;
                    bottom:0;
                    background-color:rgba(85,85,85,0.9);
                    background-color:#D9EEE1;
                    padding:20px;
                    width:100%;
                    left:0;
                }
                #assignmentcontainer, #showcorrectanswercontainer {
                    min-height:150px;
                    padding-bottom:60px;
                    margin-left:-20px;
                    margin-right:-20px;
                    border-radius:0;

                }
                #assignmentNotCorrect, #assignmentCorrect {
                    padding:20px;
                }
            }
        </style>

        <?php
    }

    /**
     * Lấy hàm showanswer() trong view-source:https://www.w3schools.com/html/exercise.asp?filename=exercise_html_form_elements1
     * Chuyển sang php:
     *
     * @param  $exSection  : lấy 'var exsection' trong source trên
     */
    public function getMangCauTraLoi($codeQuestion, $correctTxt, $exSection)
    {
        $txt = $codeQuestion;

        $cc = $from = $to = $endPos = $numberofchar = 0;
        $correctPositions = $correctAnswers = [];
        $x = $y = $z = $inputs = $inputValue = null;

        //        echo "<br/>\nxxx1 '$exSection'";

        if ($exSection == 'HTML_2' || $exSection == 'R' || $exSection == 'REACT' || $exSection == 'CSS_2' || $exSection == 'CSS') {

            //            die("xxx $exSection");

            $correctTxt = str_replace('&lt;', '<', $correctTxt);
            $correctTxt = str_replace('&gt;', '>', $correctTxt);
            $correctTxt = str_replace('&amp;', '&', $correctTxt);

            $txt = str_replace('&lt;', '<', $txt);
            $txt = str_replace('&gt;', '>', $txt);
            $txt = str_replace('&amp;', '&', $txt);
        }

        for ($i = 0; $i < strlen($txt); $i++) {
            $c = $txt[$i];
            $numberofchar = 0;

            if ($c == '@') {
                //                echo "<br/>\n x1 / $i";
                if ($txt[$i + 1] == '(') {
                    $startPos = $i + 2;
                    $endPos = strpos($txt, ')', $startPos);
                    $n = substr($txt, $startPos, $endPos - $startPos);
                    if (is_numeric($n)) {
                        $numberofchar = $n;
                    }
                    if ($numberofchar > 0) {
                        $from = $i + $cc;
                        $to = (int) $numberofchar;
                        //                        $cc += (int)$numberofchar - 3 - strlen((string)$numberofchar);
                        $cc += (int) $numberofchar - 3 - strlen("$numberofchar");
                        $correctPositions[] = [$from, $to];
                    }
                }
            }
        }

        $cc = 0;
        foreach ($correctPositions as $position) {
            [$x, $y] = $position;
            $z = substr($correctTxt, $x + $cc, $y);

            if ($z == '&') {
                if (substr($correctTxt, $x + $cc, 4) == '&lt;') {
                    $z = '<';
                    $cc = $cc + 3;
                }
                if (substr($correctTxt, $x + $cc, 4) == '&gt;') {
                    $z = '>';
                    $cc = $cc + 3;
                }
            }

            if ($z == '&l') {
                if (substr($correctTxt, $x + $cc, 8) == '&lt;&gt;') {
                    $z = '<>';
                    $cc = $cc + 6;
                }
            }

            if ($z == '&lt;?') {
                if (substr($correctTxt, $x + $cc, 8) == '&lt;?php') {
                    $z = '<?php';
                    $cc = $cc + 3;
                }
            }

            if ($z == '?&') {
                if (substr($correctTxt, $x + $cc, 5) == '?&gt;') {
                    $z = '?>';
                    $cc = $cc + 3;
                }
            }

            if ($z == '=&') {
                if (substr($correctTxt, $x + $cc, 5) == '=&gt;') {
                    $z = '=>';
                    $cc = $cc + 3;
                }
            }

            $correctAnswers[] = $z;
        }

        return $correctAnswers;
    }
}


?>
