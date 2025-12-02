<?php
// Autoload các thư viện đã cài đặt từ Composer
require_once('vendor/autoload.php');


use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;

$proxyList = explode("\n", "103.74.121.39
103.74.121.155
103.74.121.162
103.74.121.163
103.74.121.164
103.74.121.165
103.74.121.174
103.74.121.180
103.74.121.196");


// print_r($proxyList);
// return;
$serverUrl = 'http://localhost:9515';
//$link = 'https://mytree.vn/tool1/21.php';
$link = 'https://www.w3schools.com';

$folder = "c:/save_file_html";

if(!file_exists($folder))
    mkdir($folder);

while(1){
    sleep(1);

//    $link = 'https://glx.com.vn';


    // $driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::chrome());


    // Set Chrome options to run in headless mode
    $options = new ChromeOptions();
    $options->addArguments(['--headless', '--disable-gpu', '--window-size=1920,1080']);

    //Get random proxy IP from $proxyList:


    $randomProxy = trim($proxyList[array_rand($proxyList)]);

    echo "\n\n Proxy = $randomProxy ";
    $options->addArguments(['--proxy-server=http://'.$randomProxy.':38888']);

    // Block loading of images and JavaScript
    $preferences = [
        'profile.managed_default_content_settings.images' => 2,
        'profile.managed_default_content_settings.javascript' => 2
    ];

    $options->setExperimentalOption('prefs', $preferences);

    // specify the desired capabilities
    $capabilities = DesiredCapabilities::chrome();


    $capabilities = DesiredCapabilities::chrome();
    $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
    // $capabilities->setCapability('proxy', $proxy);



    $driver = RemoteWebDriver::create($serverUrl, $capabilities);

    $driver->manage()->timeouts()->pageLoadTimeout(60); // Timeout in seconds

    $driver->get($link);
    // Extract text content using JavaScript
    $textContent = $driver->executeScript('return document.body.innerText;');

    echo "\n TXT cont: " . substr($textContent, 0, 50) . '...';

    // Extract all links using JavaScript
    $links = $driver->findElements(WebDriverBy::tagName('a'));
    $linkUrls = [];
    foreach ($links as $linkElement) {
        $href = $linkElement->getAttribute('href');
        if ($href) {
            $linkUrls[] = $href;
        }
    }
//    // List all extracted links
//    foreach ($linkUrls as $url) {
//        echo $url . "\n";
//    }
    // Select a random link from the list
    if (!empty($linkUrls)) {
        $randomLink = $linkUrls[array_rand($linkUrls)];

        if(str_starts_with($randomLink, '#')){
            continue;
        }

        if(!str_starts_with($randomLink, 'https')){
            $randomLink = 'https://www.w3schools.com' . $randomLink;
        }

        echo "\n--- Navigating to: " . $randomLink . "\n";

        $driver->manage()->timeouts()->pageLoadTimeout(60); // Timeout in seconds

        $driver->get($randomLink);

        $textContent =  $driver->getPageSource(); ;//$driver->executeScript('return document.body.innerText;');

//        $textContent = file_get_contents($randomLink);

        echo "\n TXT cont: " . substr($textContent, 0, 200) . '...';

        $saveTo = "$folder/file_".str_replace(["/", '?', '$', '#', ':', '\\', '\/', '&'],
                '_', $randomLink).microtime(1).'.html';

        // file_put_contents($saveTo, $randomLink. '\n' . $textContent);

    }


    sleep(5);;
    $driver->quit();
}
