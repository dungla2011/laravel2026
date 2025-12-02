<?php

namespace Tests\Feature;

require_once 'E:\\Projects\\laravel2022-01\\laravel01\\public\\index.php';

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

?>
<script>
//
//         document.getElementsByClassName()
</script>
<?php
$link = 'https://www.w3schools.com/';

//saveLinkToImg($link, "fq1.png", 'fa1.png');

    $serverUrl = 'http://localhost:9515';
    $driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::chrome());
    $driver->get($link);


    // specify the desired capabilities
    $capabilities = DesiredCapabilities::chrome();


    $capabilities = DesiredCapabilities::chrome();
    $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
    $capabilities->setCapability('proxy', $proxy);

    // Extract text content using JavaScript
    $textContent = $driver->executeScript('return document.body.innerText;');

    // Print or save the extracted text
//    echo substr($textContent, 0, 200);



    $driver->quit();

