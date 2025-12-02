<?php

namespace App\Components;

class CDisk
{
    public $mount_point;

    public $device;

    public $disk_total_space;

    public $disk_free_space;

    public $disk_used_space;

    public $util;  //xx %

    public $wait;  //xx milisec

    public $checktime = 0;

    public static $mountUtilWaitArray;

    /* Local get
     * $getUtil = 0: get util, wait or not (getUtil=1 will take about +3 second than not getUtil = 0)
     * $getInCache 0/1 : get direct or in cache file
     * $timeCacheRange: if cache file older more 20s than current, so get direct and rewite cache
     */

    public function getDiskInfoArray($getUtil = 0, $getInCache = 1, $timeCacheRange = 20)
    {

        $cret = new CReturnError();

        $file = '/mnt/glx/weblog/cache/server/localDiskInfo';
        if ($getInCache) {

            if (file_exists($file) && filesize($file) > 0) {

                $filetime = filemtime($file);
                if ($filetime > time() - $timeCacheRange) {
                    CDisk::$mountUtilWaitArray = $arrCDiskObj = unserialize(file_get_contents($file));
                    if (is_array($arrCDiskObj)) {
                        return $arrCDiskObj;
                    }
                }
            }
        }

        $ret = shell_exec('df -Hl'); //$ret = shell_exec(HTS("6466202d486c"));
        $arr = explode("\n", $ret);
        $arrayDiskAndMount = [];

        $count = 0;
        //Get all mount point and dev
        foreach ($arr as $key => $str) {
            $count++;

            //B�? dòng đầu tiên
            if ($count == 1) {
                continue;
            }

            $str1 = trim($str);
            //if(substr($str1,0,5)=="/dev/")
            //$str1 = substr($str1,5);
            $arrTMP = explode(' ', $str1);
            $disk = trim($arrTMP[0]);
            $mount = trim($arrTMP[count($arrTMP) - 1]);

            if (empty($disk) || empty($mount)) {
                continue;
            }

            $cdisk = new CDisk();
            $cdisk->device = $disk;
            $cdisk->mount_point = $mount;
            $cdisk->disk_free_space = disk_free_space($mount);
            $cdisk->disk_total_space = disk_total_space($mount);
            $cdisk->disk_used_space = $cdisk->disk_total_space - $cdisk->disk_free_space;
            $cdisk->checktime = time();
            //  if(strstr($mount,"/media"))
            //    continue;
            //        $arrayDisk[] = $str1;
            $arrayDiskAndMount[] = $cdisk;
        }

        if ($getUtil) {
            $ret = shell_exec('iostat -x 1 3');

            $arr = explode("\n", $ret);

            $diskSummary = [];
            $countDisk = [];
            $diskWait = [];
            $count = 0;

            /*
              ....
              [6] => sda               0.14   333.49   42.96    5.53  5460.36  1356.07   281.17     0.30    6.22    0.19   53.07   5.15  24.99
              [7] => sdb               0.95   264.05  134.65    3.60 16927.63  1070.62   260.38     0.00    2.49    2.17   14.21   0.46   6.40
              [8] => sdc               1.44   253.62  147.03    3.36 18575.80  1027.92   260.70     0.18    3.47    1.70   80.79   1.01  15.25
              [9] => sdd               1.85   239.42  137.93    3.22 17505.03   970.55   261.79     0.31    2.18    1.65   24.73   0.82  11.56
              [10] => sde               0.76   249.77  123.30    3.54 15472.77  1013.25   259.94     0.14    3.76    1.74   74.06   0.41   5.24
              [11] => sdf               0.99   217.71  129.66    3.17 16316.71   883.53   258.99     0.24    1.80    1.60    9.94   0.43   5.65
              [12] => sdg               0.03    69.36    9.90   21.92   423.88   365.21    49.59     0.12    3.93    0.64    5.41   0.17   0.55
              [13] => sdh               0.38  3299.06   93.72   33.81 11440.87 13331.50   388.49     0.15    3.84    2.01    8.90   0.77   9.88
              ...
              [19] => sda               0.00     0.00    0.00    0.00     0.00     0.00     0.00     0.00    0.00    0.00    0.00   0.00   0.00
              [20] => sdb               0.00     0.00  182.00    0.00 22780.00     0.00   250.33     4.68   25.64   25.64    0.00   5.45  99.20
              [21] => sdc               0.00     0.00  141.00    0.00 18048.00     0.00   256.00     2.78   19.62   19.62    0.00   6.24  88.00
              [22] => sdd               0.00     0.00  206.00    0.00 24856.00     0.00   241.32    20.64   92.19   92.19    0.00   4.85 100.00
              [23] => sde               0.00     0.00   76.00    0.00  9728.00     0.00   256.00     1.74   22.20   22.20    0.00  11.45  87.00
              ...
             */

            //�?ếm N lần rồi lấy trung bình
            for ($i = 0; $i < count($arr); $i++) {
                $line = trim($arr[$i]);
                $line = preg_replace("'\s+'", ' ', $line);
                if (substr($line, 0, 2) != 'sd' && substr($line, 0, 2) != 'hd') {
                    continue;
                }

                $arr1 = explode(' ', $line);

                $disk = $arr1[0];
                $util = trim($arr1[count($arr1) - 1]);
                $wait = trim($arr1[count($arr1) - 4]);

                if (! isset($diskSummary[$disk])) {
                    $diskSummary[$disk] = 1;    //new thì đặt = 1 lần sau isset OK
                    $countDisk[$disk] = 0;      //B�? qua first found, vì số liệu ko đúng
                    $diskWait[$disk] = 0;       //B�? qua first found, vì số liệu ko đúng
                } else {
                    $diskSummary[$disk] += $util;
                    $countDisk[$disk]++;
                    $diskWait[$disk] += $wait;
                }
            }

            //Lấy trung bình:
            foreach ($diskSummary as $disk => $util) {
                //Vì ổ (dev) có thể được phân nhi�?u vùng, nên chỗ này là để tính performance chung :
                $percent = number_format($diskSummary[$disk] / $countDisk[$disk], 0);
                $wait = number_format($diskWait[$disk] / $countDisk[$disk], 0);
                $wait = str_replace(',', '', $wait);

                //echo "<br/> Disk -> util =  $disk => $util";

                foreach ($arrayDiskAndMount as $cDisk) {
                    if ($cDisk->device == '/dev/'.$disk) {
                        $cDisk->util = $percent;
                        $cDisk->wait = $wait;
                        //break;
                    }
                }
            }
        }

        //Write to cache:
        CDisk::$mountUtilWaitArray = $arrayDiskAndMount;

        $dir = dirname($file);
        if (! file_exists(($dir))) {
            @mkdir($dir, 0777, 1);
        }

        if (! file_exists($dir)) {
            return $cret = CReturnError::returnErrorStatic($cret, 'Error '.__FUNCTION__.": not found cache dir? $dir ", '###');
        }

        $serial = serialize($arrayDiskAndMount);
        outputW($file, $serial);
        $checkSerial = file_get_contents($file);
        if ($checkSerial != $serial) {
            return $cret = CReturnError::returnErrorStatic($cret, 'Error '.__FUNCTION__.': can not write serial cache?', '###');
        }

        return $arrayDiskAndMount;
    }

    /*
     * $getUtil = 0: get util, wait or not (getUtil=1 will take about +3 second than not getUtil = 0)
     * $getInCache 0/1 : get direct or in cache file
     * $timeCacheRange: if cache file older more 20s than current, so get direct and rewite cache
     */

    public function getDiskInfoArrayRemote($server = null, $getUtil = 0, $getInCache = 1, $timeCacheRange = 20)
    {

        $baseUrlRemoteServer = '';
        //if(defined('BASE_URL'))
        //  $baseUrl = "/".BASE_URL;

        if (! isset($server)) {
            loi('Error '.__FUNCTION__.': empty server?', '###');
        }

        $link = "http://$server:".SERVER_INFO_WEB_PORT."$baseUrlRemoteServer/tool/sysinfo.php?getDiskArrInfo=1&getUtil=$getUtil&getInCache=$getInCache&dTimeCache=$timeCacheRange";

        $content = @file_get_contents($link);

        if (isset($content) && ! empty($content)) {

            $contentOK = trim($content);
            //            echo "<br/>\n $contentOK ";
            $arrayDiskAndMount = unserialize(base64_decode($contentOK));

            //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //            print_r($arrayDiskAndMount);
            //            echo "</pre>";
            //            echo "<br> $contentOK";
            //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //            print_r($arrayDiskAndMount);
            //            echo "</pre>";

            if (! is_array($arrayDiskAndMount)) {

                loi('Error '.__FUNCTION__.": not valid remote array disk info ($contentOK)?");
            }

            return $arrayDiskAndMount;
        } else {
            loi('Error '.__FUNCTION__.': can not call remote html1 ?');
        }
    }

    public function getDiskInfoFromArray($arrayDiskObj, $mount)
    {
        if (! is_array($arrayDiskObj)) {
            loi('Error '.__FUNCTION__.': not array disk obj?');
        }
        $diskObj = new CDisk();
        foreach ($arrayDiskObj as $diskObj) {

            if ($diskObj->mount_point == $mount) {
                return $diskObj;
            }
        }

        return null;
    }

    public function getDiskObjArray($getUtil = 0, $getInCache = 1, $timeCacheRange = 20)
    {

        $file = '/mnt/glx/weblog/cache/server/localDiskInfo';
        if ($getInCache) {

            if (file_exists($file) && filesize($file) > 0) {

                $filetime = filectime($file);
                if ($filetime > time() - $timeCacheRange) {
                    CDisk::$mountUtilWaitArray = $arrCDiskObj = unserialize(file_get_contents($file));
                    if (is_array($arrCDiskObj)) {
                        return $arrCDiskObj;
                    }
                }
            }
        }

        $ret = shell_exec('df -Hl'); //$ret = shell_exec('df -Hl'); //$ret = shell_exec(HTS("6466202d486c"));

        $arr = explode("\n", $ret);
        $arrayDiskAndMount = [];

        $count = 0;
        //Get all mount point and dev
        foreach ($arr as $key => $str) {
            $count++;

            //B�? dòng đầu tiên
            if ($count == 1) {
                continue;
            }

            $str1 = trim($str);
            //if(substr($str1,0,5)=="/dev/")
            //$str1 = substr($str1,5);
            $arrTMP = explode(' ', $str1);
            $disk = trim($arrTMP[0]);
            $mount = trim($arrTMP[count($arrTMP) - 1]);

            if (empty($disk) || empty($mount)) {
                continue;
            }

            $cdisk = new CDisk();
            $cdisk->device = $disk;
            $cdisk->mount_point = $mount;
            $cdisk->disk_free_space = disk_free_space($mount);
            $cdisk->disk_total_space = disk_total_space($mount);
            $cdisk->disk_used_space = $cdisk->disk_total_space - $cdisk->disk_free_space;
            $cdisk->checktime = time();
            //  if(strstr($mount,"/media"))
            //    continue;
            //        $arrayDisk[] = $str1;
            $arrayDiskAndMount[] = $cdisk;
        }

        if ($getUtil) {

            //$ret = shell_exec("iostat -x 1 3");
            $ret = shell_exec('iostat -x 1 3');

            $arr = explode("\n", $ret);

            $diskSummary = [];
            $countDisk = [];
            $diskWait = [];
            $count = 0;

            /*
              ....
              [6] => sda               0.14   333.49   42.96    5.53  5460.36  1356.07   281.17     0.30    6.22    0.19   53.07   5.15  24.99
              [7] => sdb               0.95   264.05  134.65    3.60 16927.63  1070.62   260.38     0.00    2.49    2.17   14.21   0.46   6.40
              [8] => sdc               1.44   253.62  147.03    3.36 18575.80  1027.92   260.70     0.18    3.47    1.70   80.79   1.01  15.25
              [9] => sdd               1.85   239.42  137.93    3.22 17505.03   970.55   261.79     0.31    2.18    1.65   24.73   0.82  11.56
              [10] => sde               0.76   249.77  123.30    3.54 15472.77  1013.25   259.94     0.14    3.76    1.74   74.06   0.41   5.24
              [11] => sdf               0.99   217.71  129.66    3.17 16316.71   883.53   258.99     0.24    1.80    1.60    9.94   0.43   5.65
              [12] => sdg               0.03    69.36    9.90   21.92   423.88   365.21    49.59     0.12    3.93    0.64    5.41   0.17   0.55
              [13] => sdh               0.38  3299.06   93.72   33.81 11440.87 13331.50   388.49     0.15    3.84    2.01    8.90   0.77   9.88
              ...
              [19] => sda               0.00     0.00    0.00    0.00     0.00     0.00     0.00     0.00    0.00    0.00    0.00   0.00   0.00
              [20] => sdb               0.00     0.00  182.00    0.00 22780.00     0.00   250.33     4.68   25.64   25.64    0.00   5.45  99.20
              [21] => sdc               0.00     0.00  141.00    0.00 18048.00     0.00   256.00     2.78   19.62   19.62    0.00   6.24  88.00
              [22] => sdd               0.00     0.00  206.00    0.00 24856.00     0.00   241.32    20.64   92.19   92.19    0.00   4.85 100.00
              [23] => sde               0.00     0.00   76.00    0.00  9728.00     0.00   256.00     1.74   22.20   22.20    0.00  11.45  87.00
              ...
             */

            //�?ếm N lần rồi lấy trung bình
            for ($i = 0; $i < count($arr); $i++) {
                $line = trim($arr[$i]);
                $line = preg_replace("'\s+'", ' ', $line);
                if (substr($line, 0, 2) != 'sd' && substr($line, 0, 2) != 'hd') {
                    continue;
                }

                $arr1 = explode(' ', $line);

                $disk = $arr1[0];
                $util = trim($arr1[count($arr1) - 1]);
                $wait = trim($arr1[count($arr1) - 4]);

                if (! isset($diskSummary[$disk])) {
                    $diskSummary[$disk] = 1;    //new thì đặt = 1 lần sau isset OK
                    $countDisk[$disk] = 0;      //B�? qua first found, vì số liệu ko đúng
                    $diskWait[$disk] = 0;       //B�? qua first found, vì số liệu ko đúng
                } else {
                    @$diskSummary[$disk] += $util;
                    $countDisk[$disk]++;
                    @$diskWait[$disk] += $wait;
                }
            }

            //Lấy trung bình:
            foreach ($diskSummary as $disk => $util) {
                //Vì ổ (dev) có thể được phân nhi�?u vùng, nên chỗ này là để tính performance chung :
                $percent = number_format($diskSummary[$disk] / $countDisk[$disk], 0);
                $wait = number_format($diskWait[$disk] / $countDisk[$disk], 0);
                $wait = str_replace(',', '', $wait);

                //echo "<br/> Disk -> util =  $disk => $util";

                foreach ($arrayDiskAndMount as $cDisk) {
                    if ($cDisk->device == '/dev/'.$disk) {
                        $cDisk->util = $percent;
                        $cDisk->wait = $wait;
                        //break;
                    }
                }
            }
        }

        //Write to cache:
        CDisk::$mountUtilWaitArray = $arrayDiskAndMount;

        $dir = dirname($file);
        if (! file_exists(($dir))) {
            @mkdir($dir, 0777, 1);
        }

        if (! file_exists($dir)) {
            echo 'Error '.__FUNCTION__.": not found cache dir? $dir", '###';

            return null;
        }

        if ($getInCache) {
            $serial = serialize($arrayDiskAndMount);
            outputW($file, $serial);
            $checkSerial = file_get_contents($file);
            if ($checkSerial != $serial) {
                loi('Error '.__FUNCTION__.": may be can not write serial cache? ($file)", '###');

                return null;
            }
        }

        return $arrayDiskAndMount;
    }

    public function getDiskObjArrayRemote($server = null, $getUtil = 0, $getInCache = 1, $timeCacheRange = 20)
    {

        $baseUrl = '';
        //if(defined('BASE_URL'))
        //  $baseUrl = "/".BASE_URL;

        $cret = new CReturnError();
        if (! isset($server)) {
            return $cret = CReturnError::returnErrorStatic($cret, 'Error '.__FUNCTION__.': empty server?', '###');
        }

        $link = "http://$server:".SERVER_INFO_WEB_PORT."$baseUrl/tool/sysinfo.php?getDiskArrInfo=1&getInCache=$getInCache&dTimeCache=$timeCacheRange";

        //echo "<br/> link = $link";

        $content = @file_get_contents($link);
        if (isset($content) && ! empty($content)) {

            $contentOK = ($content);
            $arrayDiskAndMount = @unserialize($contentOK);

            if (! is_array($arrayDiskAndMount)) {
                return $cret = CReturnError::returnErrorStatic($cret, 'Error '.__FUNCTION__.': not valid remote array disk info?', __FILE__.'('.__LINE__.')  ');
            }

            return $arrayDiskAndMount;
        } else {
            return $cret = CReturnError::returnErrorStatic($cret, 'Error '.__FUNCTION__.': can not call remote html ?', __FILE__.'('.__LINE__.") $link");
        }
    }

    public function getDiskObjFromArray($arrayDiskObj, $mount)
    {

        $cret = new CReturnError();

        if (! is_array($arrayDiskObj)) {
            return $cret = CReturnError::returnErrorStatic($cret, 'Error '.__FUNCTION__.': not array disk obj?', '###');
        }

        $diskObj = new CDisk();
        foreach ($arrayDiskObj as $diskObj) {

            if ($diskObj->mount_point == $mount) {
                return $diskObj;
            }
        }

        return null;
    }

    public static function CheckMountValid($folder)
    {

        $arrMountDisk = GetMountPoints();

        $found = 0;
        for ($i = 0; $i < count($arrMountDisk); $i++) {
            //echo "<br />  $dev => $mount";
            if ($arrMountDisk[$i]['mount'] == "$folder") {
                return 1;
                //echo "<br /> DEV = ".$arrMountDisk[$i]['disk'];
            }
        }

        return 0;
    }

    //Lấy free disk chứa file:
    public static function getTotalDiskSizeInFilePath($filePath)
    {

        $oDisk = new CDisk('/');
        $mm = $oDisk->getDiskObjArray(1, 1);
        $fileP1 = $filePath;
        $maxLenMount = 0;
        $lastSizeOK = 0;

        //Tìm moutpoint có path dài nhất nằm trong filepath:
        //FilePath max = 100 /
        if ($mm) {
            for ($i = 0; $i < 100; $i++) {
                foreach ($mm as $oDisk) {
                    if ($oDisk->mount_point == substr($fileP1, 0, strlen($oDisk->mount_point))) {
                        //                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                        //                print_r($oDisk);
                        //                echo "</pre>";
                        if (strlen($oDisk->mount_point) > $maxLenMount) {
                            $maxLenMount = strlen($oDisk->mount_point);
                            $lastSizeOK = $oDisk->disk_total_space;
                        }
                    }
                }
                $fileP1 = dirname($filePath);
                if ($fileP1 == '/' || ! $fileP1) {
                    break;
                }
            }
        }

        return $lastSizeOK;
    }

    /*
     * Window: getDiskVolumeName("d");
     */
    public static function getDiskVolumeName($disk)
    {
        // Try to grab the volume name
        if (preg_match('#Volume in drive [a-zA-Z]* is (.*)\n#i', shell_exec('dir '.$disk.':'), $m)) {
            $volname = ' ('.$m[1].')';
        } else {
            $volname = '';
        }

        return $volname;
    }

    public static function getFreeDiskInFilePathV2($filePath)
    {
        return disk_free_space($filePath);
    }

    //Lấy free disk chứa file:
    public static function getFreeDiskInFilePath($filePath, $getUtil = 0, $inCache = 0)
    {

        $oDisk = new CDisk('/');
        $mm = $oDisk->getDiskObjArray($getUtil, $inCache);
        $fileP1 = $filePath;
        $maxLenMount = 0;
        $lastSizeOK = 0;

        //Tìm moutpoint có path dài nhất nằm trong filepath:
        //FilePath max = 100 /
        if ($mm) {
            for ($i = 0; $i < 100; $i++) {
                foreach ($mm as $oDisk) {
                    if ($oDisk->mount_point == substr($fileP1, 0, strlen($oDisk->mount_point))) {
                        //                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                        //                print_r($oDisk);
                        //                echo "</pre>";
                        if (strlen($oDisk->mount_point) > $maxLenMount) {
                            $maxLenMount = strlen($oDisk->mount_point);
                            $lastSizeOK = $oDisk->disk_free_space;
                        }
                    }
                }
                $fileP1 = dirname($filePath);
                if ($fileP1 == '/' || ! $fileP1) {
                    break;
                }
            }
        }

        return $lastSizeOK;
    }

    public function _ALAST()
    {

    }
}
