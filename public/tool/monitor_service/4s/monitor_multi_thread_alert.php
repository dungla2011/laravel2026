<?php
//Run on 4S:
//php /var/www/galaxycloud/tool/monitor_service/4s/monitor_multi_thread_alert.php rs $1 $2 $3

$_SERVER['HTTP_HOST'] = '4share.vn';
$phpFile = "glxpp_4share";
$dirName = __DIR__;
require_once "/var/www/galaxycloud/tool/monitor_service/2020/monitor_multi_thread_alert.php";
