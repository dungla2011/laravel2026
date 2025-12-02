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
$link = 'https://www.w3schools.com/html/exercise.asp?filename=exercise_html_styles1';

//saveLinkToImg($link, "fq1.png", 'fa1.png');

function saveLinkToImg($link, $questionFile, $answerFile)
{
    $serverUrl = 'http://localhost:9515';
    $driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::chrome());
    $driver->get($link);
    $driver->executeScript("document.getElementById('exercisemenu').remove();");
    $driver->executeScript("document.getElementById('topTD').style.paddingLeft=0;");
    $driver->executeScript("document.getElementById('menubtn').remove()");
    $driver->executeScript("document.getElementById('tryitLeaderboard').parentElement.parentElement.remove()");
    $driver->executeScript('document.getElementsByTagName("h1")[0].remove()');
    $driver->executeScript("document.getElementById('answerbutton').remove()");
    $driver->executeScript("document.getElementById('assignmentcontainer').style.minHeight='0px'");
    $driver->executeScript("document.getElementById('assignmentcontainer').style.paddingTop='5px'");
    $driver->executeScript("document.getElementById('assignmentcontainer').style.backgroundColor='white'");
    //    sleep(3);
    $driver->executeScript('document.getElementsByClassName("showanswerbutton")[0].style.display="none"');
    $driver->executeScript('let boxes = document.querySelectorAll("input"); boxes.forEach(box => {box.blur();  box.style.border = "1px dotted #bbb"; });');
    usleep(1000);
    $driver->takeScreenshot($questionFile);
    $driver->executeScript('document.getElementsByClassName("showanswerbutton")[0].style.display="block"');
    $driver->executeScript('document.getElementsByClassName("showanswerbutton")[0].click()');
    $driver->executeScript('document.getElementsByClassName("hideanswerbutton")[0].style.display="none"');

    $driver->executeScript("document.getElementById('showcorrectanswercontainer').style.minHeight='0px'");
    $driver->executeScript("document.getElementById('showcorrectanswercontainer').style.paddingTop='5px'");
    $driver->executeScript("document.getElementById('showcorrectanswercontainer').style.backgroundColor='white'");
    //    $driver->executeScript("document.getElementById('showcorrectanswercontainer').style.borderLeft='1px dashed #aaa'");
    usleep(1000);
    $driver->takeScreenshot($answerFile);
    $driver->executeScript('document.getElementsByClassName("showanswerbutton")[0].remove()');
    $driver->quit();
}
