<?php

use Facebook\WebDriver\WebDriverBy;
use LadLib\Common\Tester\clsTestBase2;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../index.php';

\LadLib\Common\Tester\clsTestBase2::initDriver();

$driver = clsTestBase2::$driver;

//$driver->get('https://www.facebook.com/groups/vultr');
//$driver->get('https://www.facebook.com/groups/1385208401533990/');

//Xay bay gia re
$driver->get('https://www.facebook.com/groups/466260233546713');

//$driver->get('https://mytree.vn/tool1/testing/test1.php');
//$x = clsTestBase2::findOneByXPath("//div[@class='x1']");
//
//$m1 = $x->findElements(WebDriverBy::xpath(".//a[@class='x3']"));
//
////Lệnh không có dấu . này sẽ tìm tất cả, không chỉ ở trong x này
////$m1 = $x->findElements(WebDriverBy::xpath("//a[@class='x3']"));
//
//foreach ($m1 AS $a){
//    echo "<br/>\n Atext = " . $a->getText();
//    echo "<br/>\n AHtml = " . $a->getDomProperty("innerHTML");
//}

//$x->findElements( ".//a[@class=x3]");

//sleep(2);
//Đăng nhập
//clsTestBase2::findOneByXPath("//input[@name='email']")->sendKeys("...@gmail.com");
//clsTestBase2::findOneByXPath("//input[@name='pass']")->sendKeys("Cloud111!@)(("."1212")->sendKeys(\Facebook\WebDriver\WebDriverKeys::ENTER);

//sleep(2);
//clsTestBase2::findOneByXPath('/html/body')->sendKeys([\Facebook\WebDriver\WebDriverKeys::CONTROL , \Facebook\WebDriver\WebDriverKeys::END]);
//sleep(2);
//clsTestBase2::findOneByXPath('/html/body')->sendKeys([\Facebook\WebDriver\WebDriverKeys::CONTROL , \Facebook\WebDriver\WebDriverKeys::END]);
//sleep(2);
//
//$feed = clsTestBase2::findOneByXPath("//div[@role='feed']");
//
//if($feed){
//    echo "<br/>\n Found feed";
//    $html = $feed->getAttribute("innerHTML");
//
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($html);
//    echo "</pre>";
//
//}
//else
//    echo "<br/>\n *** NOT Found feed";
//
//$mm = clsTestBase2::findAllByXPath("//div[@role='feed']//img");
//
//$m2 = clsTestBase2::findAllByXPath("//div[@role='feed']")[0];
//$html = $m2->getAttribute("innerHTML");
//
//echo "<br/>\n $html";
//
//
//return;

sleep(2);
clsTestBase2::findOneByXPath('/html/body')->sendKeys([\Facebook\WebDriver\WebDriverKeys::CONTROL, \Facebook\WebDriver\WebDriverKeys::END]);
sleep(2);
//clsTestBase2::findOneByXPath('/html/body')->sendKeys([\Facebook\WebDriver\WebDriverKeys::CONTROL , \Facebook\WebDriver\WebDriverKeys::END]);
//sleep(2);

//getch("...");

//$ma = clsTestBase2::findAllByXPath("//a[@role='link']");
//if($ma){
//    echo "<br/>\nxxx2";
//    foreach ($ma AS $a){
////        $a = $ma[0];
//        echo "<br/>\n LINK x = " . $a->getDomProperty("href");
//    }
//}
//return;
//
//getch("...");
//

//$m2 = clsTestBase2::findAllByXPath("//div[@role='feed']/div");
$m2 = clsTestBase2::findAllByXPath("//div[@role='article']");
$cc = 0;
foreach ($m2 as $div) {

    $cc++;
    echo "<br/>\n $cc. ===================================================";
    // /descendant::*[contains(@class, 'example') and self::a]
    //    $a = $div->findElement(\Facebook\WebDriver\WebDriverBy::xpath(".//a[@role='link']"));
    $ma = $div->findElements(\Facebook\WebDriver\WebDriverBy::xpath(".//a[@role='link']"));
    if (! $ma) {
        continue;
    }
    //
    $a = $ma[0];

    echo "<br/>\n LINK FB = ".$a->getDomProperty('href');

    //
    $txt = $div->getText();
    $htmlDiv = $div->getDomProperty('innerHTML');
    $htmlDiv = preg_replace('/class=".*?"/', '', $htmlDiv);
    $htmlDiv = preg_replace('/style=".*?"/', '', $htmlDiv);
    $htmlDiv = strip_tags($htmlDiv, '<p><a><div><span><i>');
    //    echo "<br/>\n $htmlDiv";

    echo "<br/>\n----\n\n $txt ";

    continue;
    //    echo "<br/>\n $txt";
    $ahtml = $a->getDomProperty('innerHTML');
    echo "<br/>\n------------------------------";
    echo "<br/>\n Atext = ".$a->getText();
    echo "<br/>\n A HTML0 = ".strip_tags($ahtml);
    echo "<br/>\n A HTML1 = ".$ahtml;
    echo "<br/>\n------------------------------";

}

return;

if ($mm) {
    echo "<br/>\n Found MM IMG";
} else {
    echo "<br/>\n Not Found IMG";
}

$cc = 10;
foreach ($mm as $img) {
    $anh = $img->getAttribute('src');
    if (str_starts_with($anh, 'http')) {

        $cc++;
        $src = $img->getAttribute('src');
        echo " \n $cc. IMG:  ".$src;

        $cont = file_get_contents($src);
        outputW("c:/1/$cc.png", $cont);

    }
}
