<?php

namespace Tests\Feature;

require_once 'E:\\Projects\\laravel2022-01\\laravel01\\public\\index.php';


require_once('vendor/autoload.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

// Thiết lập WebDriver
$host = 'http://localhost:4444/wd/hub'; // Selenium Server
$capabilities = DesiredCapabilities::chrome();
$driver = RemoteWebDriver::create($host, $capabilities);

// Mở URL của trang web cần kiểm tra
$driver->get('https://example.com');

// Tìm phần tử input theo ID
$inputField = $driver->findElement(WebDriverBy::id('input_id'));

// Nhập dữ liệu vào trường input
$inputField->sendKeys('Hello, World!');

// Tìm phần tử button theo XPath
$submitButton = $driver->findElement(WebDriverBy::xpath("//button[@class='submit-button']"));

// Click vào nút Submit
$submitButton->click();

// Đợi 5 giây để trang web xử lý
sleep(5);

// Lấy tiêu đề của trang web sau khi thực hiện hành động
echo "Page title is: " . $driver->getTitle() . "\n";

// Đóng trình duyệt
$driver->quit();
