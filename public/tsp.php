<?php

$remote_ip = getenv('REMOTE_ADDR');

function RandomHexString1($_len)
{
    $len = $_len-1;
    $base='ABCDEF1234567890';
    $max=strlen($base)-1;
    $activatecode='';
    mt_srand((double)microtime()*1000000);
    while (strlen($activatecode)<$len+1)
        $activatecode.=$base[mt_rand(0,$max)];
    return $activatecode;
}

$str = RandomHexString1(100000);

$bname = basename(__FILE__);

echo "<br /> <strong>TEST Speed by Download 1 link</strong><br /><br />TEST IN DEFAULT 30 s<br /> 
[ SET <strong>t=N</strong> to test in N (s) ] <br /><br /> <a href='/$bname?start=1'> <strong>CLICK TO START TEST DOWNLOAD</strong></a><br /><br /> YOUR IP: $remote_ip";

function AssertValidGetPost1($str)
{
    if(strstr($str,"../")!=FALSE)
        loi1("ERROR INPUT STRING: $str");
}

AssertValidGetPost1($_GET['start']);
AssertValidGetPost1($_GET['t']);

if(isset($_GET['start']))
{
    $time = 30;
    if(isset($_GET['t']))
    {
        $tmp = $_GET['t'];
        if(is_numeric($tmp) && $tmp>10)
            $time = $tmp;
        else
            loi1("ERROR t");
    }

    if($_GET['start'] == 2){
        //Vinh vien:
        $time = 100000000;
        set_time_limit(0);

    }

    $filename = "testspeed.".gethostname();
    $startTime = time();
    ob_clean();
    //Begin writing headers

    //Use the switch-generated Content-Type
    header("Content-Type: application/force-download");
    //Force the download
    $header='Content-Disposition: attachment; filename="'.$filename.'"';
    header($header );
    header("Content-Transfer-Encoding: binary");
    //header("Content-Length: 1000000000");


    while(1)
    {
        if(time()-$startTime>$time)
            return;
        print_r($str);
        flush();
        ob_flush();
        usleep(10);
    }
}

?>