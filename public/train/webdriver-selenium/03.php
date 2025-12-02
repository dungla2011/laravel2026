<?php

use Dompdf\Exception;
use Facebook\WebDriver\Exception\ElementNotInteractableException;
use Facebook\WebDriver\Exception\InvalidSessionIdException;
use Facebook\WebDriver\Exception\WebDriverCurlException;
use Facebook\WebDriver\WebDriverBy;

require_once "E:/Projects/laravel2022-01/laravel01/public/index.php";

echo "\n Start: 123";

// Chạy driver trước khi chạy script này:
//chromedriver --port=4444

// This is where Selenium server 2/3 listens by default. For Selenium 4, Chromedriver or Geckodriver, use http://localhost:4444/
$serverUrl = 'http://localhost:9515';

$options = new \Facebook\WebDriver\Chrome\ChromeOptions();

//        $userAgent = "lad_tester_2020";

//Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36
$options->addArguments(array(
//            '--user-agent=' . $userAgent,
//            "--disable-web-security"
));

if(1){
    $options->addArguments(['--headless']); // Thêm cờ để chạy headless
    $options->addArguments(['--disable-gpu']); // Thêm cờ để tránh lỗi không mong muốn
}

//        _chrome.AddUserProfilePreference("credentials_enable_service", false);


$options->setExperimentalOption('excludeSwitches' , ["enable-automation"]);

///////////////////
//Bỏ show save pw:
//https://stackoverflow.com/questions/43223857/save-password-for-this-website-dialog-with-chromedriver-despite-numerous-comm
$prefs = array("credentials_enable_service" => false,
    "profile.password_manager_enabled"=>false);
$options->setExperimentalOption('prefs', $prefs);
$caps = \Facebook\WebDriver\Remote\DesiredCapabilities::chrome();
$caps->setCapability(\Facebook\WebDriver\Chrome\ChromeOptions::CAPABILITY, $options);
$caps->setCapability( 'loggingPrefs', ['browser' => 'ALL'] );

$nSlow = $cc = 0;

while(1){
    $cc++;

    //$driver = clsTester::$driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::chrome());
    $driver = \Facebook\WebDriver\Remote\RemoteWebDriver::create($serverUrl, $caps);

    // Mở URL của trang web cần kiểm tra
//    $driver->get('http://test1.pmvn123.site/');
//    $driver->get('http://test2.pmvn123.site/');

    $url = 'https://suadieuhoabacninh.vn/';
    echo "\n URL = $url";
    $driver->get($url);


    $elm = $driver->findElement(WebDriverBy::cssSelector('.phpdebugbar-fa.phpdebugbar-fa-clock-o'));
    $siblingElm = $elm->findElement(WebDriverBy::xpath('following-sibling::*[contains(@class, "phpdebugbar-text")]'));

    $text = $siblingElm->getText();
    $ts = nowyh();
    echo "<br>\n $cc. $ts | nSlow = $nSlow , TimeThis Request  = $text";

    if(!strstr($text, 'ms')){
        $nSlow++;
        echo "<br/>\n Slow ... $text";
    }

    // Đóng trình duyệt
    $driver->quit();

    sleep(1);
}
