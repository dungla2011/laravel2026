<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<style>
    * {font-family: Courier;
        font-size: small;}
    span{color: red}
    div{
        max-width: 200px;
    }
</style>

<a target="_blank" href="https://cheatography.com/davechild/cheat-sheets/regular-expressions/"> https://cheatography.com/davechild/cheat-sheets/regular-expressions/ </a>

<br>
<?php

$s = '/^.+$/';

$mm = [
    '/[A-z]/' => 'Tất cả các ký tự từ A-z',
    '/[A-z ]/' => 'Tất cả các ký tự từ A-z, cả dấu space',
    '/[A-z]+/' => 'Các chuỗi dạng A-z lắp lại từ 1 ký tự trở lên',
    '/[A-z 0-9]/' => 'Tất cả các ký tự từ A-z, cả dấu space, và số',
    '/^[A-z]+/' => 'Bắt đầu bằng ký tự, lặp lại từ 1 lần trở lên',
    '/^[A-z]*/' => 'Bắt đầu bằng ký tự, lặp lại từ 0 lần trở lên',
    '/^[A-z]+$/' => 'Chuỗi Bắt đầu và kết thúc, chứa ký A-z tự lặp lại từ 1 lần trở lên',

    '/[A-z][A-z]+/' => 'Các chuỗi ít nhất 2 ký tự Az',
    '/[A-z][A-z]*/' => 'Các chuỗi 1 ký tự Az, từ ký tự thứ 2 là rỗng hoặc A-z hoặc (OR)',

    '/[A-z][^x]/' => 'Các chuỗi 2 ký tự không chứa x ở vị trí thứ 2',
    '/^.+$/' => 'Mọi ký tự',
    '/^.*$/' => 'Mọi ký tự',
    '/^[^x]$/' => 'Không chứa x',
    '/^[^x]+$/' => 'Không chứa x',
    '/^[^y]+$/' => 'Bắt đầu và kết thúc bất kỳ không chứa y',
    '/^[^yzw]+$/' => 'Bắt đầu và kết thúc bất kỳ không chứa yzw',

];

//$str = "x";

function showReg2($reg, $str, $note)
{
    echo "<div style='float: left; border: 1px solid gray; display: inline-block; padding: 10px; margin: 5px'>";

    echo " <b style='color: blue'>$note</b> ";
    echo "<br> Txt: '<span style='background-color: yellow; color: black'>$str</span>'";
    echo "<br/>\n";
    echo "REG: <span style='background-color: darkgreen; color: white'> $reg </span>";

    echo '</div>';
}

function showReg($reg, $str, $note)
{
    echo "<div style='float: left; border: 1px solid gray; display: inline-block; padding: 10px; margin: 5px'>";
    $ret = preg_match_all($reg, $str, $match);

    echo " <b style='color: blue'>$note</b> ";
    $str = str_replace("\n", '\n', $str);
    echo "<br> Txt: '<span style='background-color: yellow; color: black'>$str</span>'";
    echo "<br/>\n";
    echo "REG: <span style='background-color: darkgreen; color: white'> $reg </span>";

    echo "<br/>\n";
    echo "\n + Number match:$ret";

    if ($ret) {
        echo "<br/>\n -- <b style='background-color: lavender'> Match: </b> ";
    } else {
        echo "<br/>\n -- <u style='background-color: yellow'>No Match</u>  ";
    }
    if ($ret) {
        foreach ($match as $m1) {
            $cc = 0;
            foreach ($m1 as $m2) {
                $cc++;
                echo "<br/>\n $cc. $m2";
            }
        }
    }
    echo '</div>';
}
$str = 'Text hello x 123';
foreach ($mm as $reg => $note) {
    showReg($reg, $str, $note);
}

$str = "Cộng hòa \n xã hội";
$note = 'Từng ký tự';
$reg = '/./';
showReg($reg, $str, $note);

$str = "Cộng hòa \n xã hội";
$note = 'Từng ký tự Unicode';
$reg = '/./u';
showReg($reg, $str, $note);

$str = "Cộng hòa \n xã hội";
$note = 'Chỉ ký tự unicode + Latin';
$reg = "/\p{L}/u";
showReg($reg, $str, $note);

$str = "Cộng hòa \n xã hội";
$note = 'Chỉ ký tự Latin';
$reg = "/\p{L}/";
showReg($reg, $str, $note);

$note = 'Cả chuỗi chỉ chứa ký tự unicode, \n là ký tự không phải unicode';
$reg = "/^\p{L}$/u";
showReg($reg, $str, $note);

$str = 'Cộng hòa xã hội, abc.';
$note = 'Cả chuỗi chỉ chứa ký tự unicode, space, và dấu , và chấm .';
$reg = "/^[\p{L} ,.]+$/u";
showReg($reg, $str, $note);

$str = 'Cộng hòa, xã hội';
$note = 'Cả chuỗi chỉ chứa ký tự unicode, dấu phẩy không phải unicode';
$reg = "/^\p{L}$/u";
showReg($reg, $str, $note);

$str = 'Cộng hòa, xã hội';
$note = 'Cả chuỗi chỉ chứa ký tự unicode, dấu phẩy không phải unicode';
$reg = "/^\p{L},$/u";
showReg($reg, $str, $note);

$str = 'Cộng hòa, xã hội';
$note = 'chuỗi';
$reg = '/^.+$/u';
showReg($reg, $str, $note);

$str = 'Cong hoa 12x3 abc';
$note = 'Hoặc a-z hoặc (OR) 0-9';
$reg = '/[A-z]+|[0-9]+/';
showReg($reg, $str, $note);

$str = 'Cong hoa 123 abc';
$note = 'Hoặc a-z hoặc (OR) 0-9';
$reg = '/[A-z]+|[0-9]+/';
showReg($reg, $str, $note);

$str = 'Cong hoa 123 abc';
$note = 'Hoặc a-z hoặc (OR) 0-9';
$reg = '/[A-z]+[0-9]+/';
showReg($reg, $str, $note);

$str = 'Cong hoa abc 123 ';
$note = 'Hoặc a-z hoặc(OR) 0-9';
$reg = '/[A-z]+[0-9]+/';
showReg($reg, $str, $note);

$str = 'Cong hoa abc 123 ';
$note = ' ???';
$reg = '/[A-z ][0-9 ]+/';
showReg($reg, $str, $note);

$str = 'Cong hoa abc 123 ';
$note = ' ???';
$reg = '/[A-z ][0-9 ]/';
showReg($reg, $str, $note);

$str = 'Cong hoa 123 abc';
$note = 'a-z, 0-9 và space';
$reg = '/[A-z0-9 ]+/';
showReg($reg, $str, $note);

$str = 'Cong hoa 123 abc';
$note = 'a-z, 0-9 và space';
$reg = '/^[A-z0-9 ]+$/';
showReg($reg, $str, $note);

$str = 'Cong hoa 12x3 abc';
$note = 'Hoặc a-z hoặc(OR) 0-9 ???';
$reg = '/^[A-z]+|[0-9]+$/';
showReg($reg, $str, $note);

$str = '123 Cong hoa';
$note = 'Số đứng đầu Và (AND) chuỗi đứng sau';
$reg = '/^[0-9 ]+[A-z ]+$/';
showReg($reg, $str, $note);

$str = 'Cong hoa 123';
$note = 'Số đứng đầu Và (AND) chuỗi đứng sau';
$reg = '/^[0-9 ]+[A-z ]+$/';
showReg($reg, $str, $note);

$str = '123 Cong hoa';
$note = 'Số đứng đầu không có số 3 Và (AND) chuỗi đứng sau';
$reg = '/^[0-9 ][^3]+[A-z ]+$/';
showReg($reg, $str, $note);

$str = '123 Cong hoa';
$note = 'Số đứng đầu không có số 4 Và (AND) chuỗi đứng sau';
$reg = '/^[0-9 ][^4]+[A-z ]+$/';
showReg($reg, $str, $note);

$str = '123 Cong hoa';
$note = 'Số đứng đầu Và (AND) chuỗi đứng sau không có chữ a';
$reg = '/^[0-9 ]+[A-z ][^a]+$/';
showReg($reg, $str, $note);

$str = '123 Cong hoa';
$note = 'Số đứng đầu Và (AND) chuỗi đứng sau không có chữ xy';
$reg = '/^[0-9 ]+[A-z ][^xy]+$/';
showReg($reg, $str, $note);

$str = '123 Cong hoa';
$note = 'Số đứng đầu tối đa 2 ký tự Và (AND) chuỗi đứng sau';
$reg = '/^[0-9 ]{0,2}+[A-z ]+$/';
showReg($reg, $str, $note);

$str = '123 Cong hoa';
$note = 'Số đứng đầu tối đa 4 ký tự Và (AND) chuỗi đứng sau';
$reg = '/^[0-9 ]{0,4}+[A-z ]+$/';
showReg($reg, $str, $note);

$str = '123';
$note = 'Số đứng đầu, không bắt buộc chuỗi đứng sau';
$reg = '/^[0-9 ]+[A-z ]*$/';
showReg($reg, $str, $note);

$str = '123';
$note = 'Số đứng đầu, bắt buộc chuỗi đứng sau';
$reg = '/^[0-9 ]+[A-z ]+$/';
showReg($reg, $str, $note);

echo "<div style='float: left; border: 1px solid gray; display: inline-block; padding: 10px; margin: 5px'>";
$str = '-12-3-';
$reg = '/-$/';
$ret = preg_replace($reg, '', $str);
echo "Cắt ký tự - ở cuối chuỗi: '$str'";
echo "\n preg_replace('$reg', '', '$str');";
echo "<br/>\nREG: $reg <br> Kết quả: $ret ";
$reg = '/^-/';
$ret = preg_replace($reg, '', $str);
echo "<hr/>\n Cắt ký tự - ở đầu chuỗi: '$str'";
echo "\n preg_replace('$reg', '', '$str');";
echo "<br/>\nREG: $reg <br> Kết quả: $ret ";
echo "\n</div>";
