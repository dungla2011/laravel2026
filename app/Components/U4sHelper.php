<?php

namespace App\Components;

use App\Models\FileUpload;
use App\Models\News;
use App\Models\Product;
use App\Models\SiteMng;
use App\Models\TmpDownloadSession;
use App\Models\UploaderInfo;
use App\Models\UserCloud;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;
use App\Models\User;
use App\Models\OrderItem;

!defined('DEF_4S_QUOTA_GB_MEMBER_DEFAULT') || define('DEF_4S_QUOTA_GB_MEMBER_DEFAULT', 50);
!defined('DEF_4S_QUOTA_FILE_MEMBER_DEFAULT') || define('DEF_4S_QUOTA_FILE_MEMBER_DEFAULT', 1000);

define("DEF_GOLDTYPE_POINT", -1000);
define("DEF_GOLDTYPE_ADMIN_INSERT", -1002);
define("DEF_GOLDTYPE_BONUS", -1003);
define("DEF_GOLDTYPE_TRANSFER", -1004); //chuyển gold giữa các tài khoản, cũ? có dùng ko? (3:14 PM 2/3/2012)

define("DEF_GOLDTYPE_SMS_BONUS", -2);
define("DEF_GOLDTYPE_VTCCARD_BONUS", -3);
define("DEF_GOLDTYPE_4SHARE_HOME_BONUS", -4);
define("DEF_GOLDTYPE_MOBILE_CARD_BONUS", -5);
define("DEF_GOLDTYPE_VEGATE_BONUS", -6);
define("DEF_GOLDTYPE_VMSCARD_BONUS", -7);
define("DEF_GOLDTYPE_VDC_CARD_MOBILE_BONUS", -8);
define("DEF_GOLDTYPE_BANK_BONUS", -9);
define("DEF_GOLDTYPE_PPAL_BONUS", -10);
define("DEF_GOLDTYPE_VCOIN_BONUS", -31);
define("DEF_GOLDTYPE_BKIM_BONUS", -32);
class U4sHelper
{

    public static $def_maxNHour = ['maxNHour' , "Số giờ hết hạn"];
    public static $def_maxNDay = ['maxNDay' ,  "Số ngày hết hạn"];
    public static $def_minStartTime = ['minStartTime' , "Giờ bắt đầu dùng Min"];
    public static $def_maxStartTime = ['maxStartTime', "Giờ bắt đầu dùng Max"];
    public static $def_totalAllowSizeGB = ['totalAllowSizeGB', "Tổng số GB cho phép tải"];
    public static $def_totalAllowCount = ['totalAllowCount', "Tổng số Lượt cho phép tải"];
    public static $def_totalAllowHour = ['totalAllowHour' , "Tổng số Giờ cho phép tải"];
    public static $def_totalAllowDay = ['totalAllowDay' ,"Số Ngày cho phép tải"];
    public static $def_totalUsedDownloadByte = ['totalUsedDownloadByte' , "Dung lượng đã tải (GB)"];
    public static $def_totalUsedDownloadGB = ['totalUsedDownloadGB' , "Số GB đã tải (GB)", 'GB'];
    public static $def_totalUsedDownloadCount = ['totalUsedDownloadCount' ,"Số lượt đã tải"];
    public static $def_totalFreeDownloadGB = ['totalFreeDownloadGB', "Dung lượng còn được tải (GB) ", 'GB'];

    public static $def_totalAllowDownloadDailyGB = ['totalAllowDownloadDailyGB', "Dung lượng được tải 1 ngày (GB)", 'GB'];
    public static $def_totalFreeCountDownload = ['totalFreeCountDownload',"Số lượt tải còn được tải"];
    public static $def_expiredDate = ['expiredDate' , "Ngày hết hạn"];
    public static $def_expiredDateByNgold= ['expiredDateByNgold' , "Ngày hết hạn (Mua Gold)"];


    var $user_id;
    var $objUserCms;

    /**
     * @var UserCloud
     */
    var $objUserCloud;
    var $mmUserInfo = null;

    function __construct($uid) {
        if(!$uid)
            return;
        $this->user_id = $uid;
        $this->objUserCms = User::find($uid);
        $this->objUserCloud = UserCloud::where('user_id', $uid)->first();
        if(!$this->objUserCloud)
            UserCloud::getOrCreateNewUserCloud($uid, 50, 1000);
//        if(isDebugIp())
//            loi("Not found cloud info1!");

        if(!$this->objUserCloud){
            loi("Not found cloud info - $uid !");
        }
        if(!$this->objUserCms){
            loi("Not found user info - $uid!");
        }

        $this->mmUserInfo = $this->getQuotaAllOfUser();
//        $this->mmBill = new OrderItem();
    }

    static function getUploaderSizeBig($insertToUploaderTable = 0)
    {

        $ttC = $ttSize = 0;
        $m1 = \Illuminate\Support\Facades\DB::select("
                        SELECT user_id, COUNT(*) AS file_count, SUM(file_size) AS sum_size
                        FROM file_uploads
                        WHERE deleted_at IS NULL
                        GROUP BY user_id
                        HAVING file_count > 500
                        ORDER BY sum_size DESC
                    ");

        $ret = "";

        $ret .= "<table border='1' cellpadding='5' cellspacing='0'>";
        $ret .= "<tr>
                            <th>STT</th>
                            <th>UID</th>
                            <th>Email</th>
                            <th>Ngày tạo</th>
                            <th>Số file</th>
                            <th>Size</th>
                          </tr>";

        $cc =0;
        foreach ($m1 as $one) {
            $cc++;
            $one = (object) $one;
            $user_id = $one->user_id;
            $us = \App\Models\User::find($user_id);
            $file_count = $one->file_count;

            $bsize0 = ByteSize($one->sum_size);

            $bsize = $bsize0;
            $ttC+= $file_count;
            if($one->sum_size > 1000 * _GB){
                $bsize = "<b style='color: green'>$bsize0</b>";
            }
            if($one->sum_size > 5000 * _GB){
                $bsize = "<b style='color: orange'>$bsize0</b>";
            }
            if($one->sum_size > 10000 * _GB){
                $bsize = "<b style='color: red'>$bsize0</b>";
            }

            $ttSize += $one->sum_size;

            if($insertToUploaderTable)
            if(!UploaderInfo::where("user_id", $user_id)->first()){
                $new = new UploaderInfo();
                $new->user_id = $user_id;
                $new->save();
            }

            $ret .= "<tr>";
            $ret .= "<td>$cc</td>";
            $ret .= "<td>$user_id</td>";
            $ret .= "<td>$us->email</td>";
            $ret .= "<td>$us->created_at</td>";
            $ret .= "<td>$file_count</td>";
            $ret .= "<td>$bsize files</td>";
            $ret .= "</tr>";
        }
        $ret .= "<tr>";
        $ret .= "<td></td>";
        $ret .= "<td></td>";
        $ret .= "<td></td>";
        $ret .= "<td></td>";
        $ret .= "<td>$ttC</td>";
        $ret .= "<td>".ByteSize($ttSize)."</td>";
        $ret .= "</tr>";

        $ret .= "</table>";



        return $ret;

    }
    static function getDownloadStats($field = 'user_id')
    {
        $downloadsByDay = \DB::table('tmp_download_sessions')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as downloads, SUM(done_bytes) as total_size');
            if($field)
                $downloadsByDay = $downloadsByDay->where($field, getCurrentUserId());
        $downloadsByDay = $downloadsByDay->where('created_at', '>=', Carbon::now()->subDays(90))
            ->where('done_bytes', '>', 0)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return json_encode($downloadsByDay->toArray());
    }

    static function getUploadStats($field = 'user_id')
    {
        $downloadsByDay = \DB::table('file_uploads')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count_file, SUM(file_size) as total_size');
        if($field)
            $downloadsByDay = $downloadsByDay->where($field, getCurrentUserId());
        $downloadsByDay = $downloadsByDay->where('created_at', '>=', Carbon::now()->subDays(90))
//            ->where('file_size', '>', 0)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return json_encode($downloadsByDay->toArray());
    }

    static function getNewUserStats($field = 'user_id')
    {
        $statByDay = \DB::table('users')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as counts');
        $statByDay = $statByDay->where('created_at', '>=', Carbon::now()->subDays(90))
//            ->where('file_size', '>', 0)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return json_encode($statByDay->toArray());
    }

    static function getWeeklyDownloadStats($field = 'user_id')
    {
        if(!SiteMng::isUseMysqlDb()){
            return "";
        }
        $downloadsByWeek = \DB::table('tmp_download_sessions')
            ->selectRaw('YEARWEEK(created_at) as week, COUNT(*) as downloads, SUM(done_bytes) as total_size');
            if($field)
                $downloadsByWeek = $downloadsByWeek->where($field, getCurrentUserId());
        if(!$downloadsByWeek)
            return;
        $downloadsByWeek = $downloadsByWeek->where('created_at', '>=', Carbon::now()->subWeeks(30))
            ->where('done_bytes', '>', 0)
            ->groupBy('week')
            ->orderBy('week', 'ASC')
            ->get();
        if(!$downloadsByWeek)
            return;

        return json_encode($downloadsByWeek->toArray());
    }

    static
    function getDiskInfoRemote()
    {

        $mSv = \App\Models\CloudServer::getServerDomainAndProxy();
//                            $mSv = ['sv18.4share.vn', 'sv96a.4share.vn'];
        $totalFreeDisk = $totalSizeDisk = 0;
        foreach ($mSv AS $sv0 => $sv) {
            $svName = explode('.', $sv)[0];
            $data = \clsReplicateFile::getRemoteDiskInfo($sv);
            if($data)
            foreach ($data as $disk) {
                $totalFreeDisk += $disk->free;
                $totalSizeDisk += $disk->size;
            }
        }
        $str = '';
        $str .= "\n <span> Free : " . ByteSize($totalFreeDisk) . "/" . ByteSize($totalSizeDisk) . "</span>  <a target='_blank' href='https://sv18.4share.vn/sysinfo_glx.html?disklist_ex2=1&get_temp=1'> GET TEMPERATURE </a>  ";

        $cc = 0;
        foreach ($mSv AS $sv) {
            $svName = explode('.', $sv)[0];
            $data = \clsReplicateFile::getRemoteDiskInfo($sv);

            $str .= "<table border='1'>
                                <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>SV</th>
                                    <th>Disk</th>
                                    <th>Mount</th>
                                    <th>Size (GB)</th>
                                    <th>Used (GB)</th>
                                    <th>Free (GB)</th>
                                    <th>Temperature</th>
                                </tr>
                                </thead>
                                <tbody>";
            if($data)
            foreach ($data as $disk) {
                $cc++;

                $str .= "<tr>
                                    <td>$cc</td>
                                    <td>$svName</td>
                                    <td> " . htmlspecialchars($disk->disk) . "</td>
                                    <td> " . htmlspecialchars($disk->mount) . "</td>
                                    <td> " . number_format($disk->size / (1024 ** 3), 2) . "</td>
                                    <td> " . number_format($disk->used / (1024 ** 3), 2) . "</td>
                                    <td>";

                if ($disk->free > 50 * _GB)
                    $str .= " <b>" . ByteSize($disk->free) . "</b> ";
                else
                    $str .= "<span style = 'color: red' > " . ByteSize($disk->free) . "</span > ";

                $str .= "</td>
                                    <td>" . htmlspecialchars($disk->temperature ?: 'N/A') . "</td>
                                </tr>";
            }
            $str .= "</tbody>
                            </table>";
        }

        return $str;
    }

    static function getMonthDownloadStats($field = 'user_id', $returnArray = 0, $month = 6)
    {
        $downloadsByMonth = \DB::table('tmp_download_sessions')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as downloads, SUM(done_bytes) as total_size');
        if($field)
            $downloadsByMonth = $downloadsByMonth->where($field, getCurrentUserId());
        $downloadsByMonth = $downloadsByMonth->where('created_at', '>=', Carbon::now()->subMonths($month))
            ->where('done_bytes', '>', 0)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

        if($returnArray)
            return $downloadsByMonth;

        return json_encode($downloadsByMonth->toArray());
    }


    static function getBlackWordList0(){
        $arrBlackWord = array("sex", "red sea", 'abominable',
            'crack', "pennis", "pussy", "cunt",
            "xxx", "porn",'pthc',"suck",'pornhub','amateurs','xvideo','cum', "fuck", 'teen', 'cave', 'jav', '9yo','8yo','7yo','5yo','4yo', 'uncensored','dâm', 'thủ dâm', 'hiếp', 'địt', 'đít');
        $arrBlackWord = array_map('strtolower', $arrBlackWord);
        return $arrBlackWord;
    }

    static function getBlackWordList(){
        $arrBlackWord = self::getBlackWordList0();
        return $arrBlackWord;
    }

    function isVip()
    {
        $date = $this->getVipExpiredDate();
        if($date && $date > time())
            return true;
        return false;
    }


    /**
     * @param $fid
     * @param $server
     * @param $location
     * @param $filesize = -1 : not check file size
     * @param $filetime
     * @return int
     * @throws \Exception
     */
    public static function checkDownloadAble_($fid, $server, $location, $filesize = null, $filetime = null){

        if(!$fid)
            loi("Need fid");
        if(!$server)
            loi("Bạn đợi ít phút để tải file này?");
        if(!$location)
            loi("Need location $server/ $location");
        if(!$filesize)
            loi("Need filesize");

        $dirRang = $fid - $fid % 1000;
        $dirRang.="-" . ($dirRang + 1000);
        //$locationFull = "/mnt/$location/$dirRang/$idfileToGetPath";
        $locationFull = "/mnt/$location/$dirRang/$fid";

        $strHex = STH("$locationFull\"$filesize\"$filetime");

        //$urlCheck = "http://$server:" . SERVER_INFO_WEB_PORT . "/checkGlxFile.html?check=$strHex";
//        $urlCheck = "http://$server:" . SERVER_INFO_WEB_PORT . "/tool/sysinfo_glx.php?checkfile=$strHex";
        $urlCheck = "https://$server/tool/sysinfo_glx.php?checkfile=$strHex";
        //Timeout
        $ctx = stream_context_create(array('http'=>
            array(
                'timeout' => 10,  //1200 Seconds is 20 Minutes
            )
        ));

        $check = @file_get_contents($urlCheck, false, $ctx);

        if (!$check)
            loi("Không thể kết nối File Server: $server!");

        if (strstr($check, "OKFILETHIS:$locationFull") === FALSE) {
            echo("Error: File đã bị xoá hoặc không hợp lệ! CHECK: $check");
            return 0;
        }

        return 1;
    }

    static function getFileFullPath($fid = 0, $location = '') {
        $dirRang = $fid - $fid % 1000;
        $dirRang.="-" . ($dirRang + 1000);
        $location = "/mnt/" . $location . "/$dirRang/$fid";
        return $location;
    }

    // Hàm để lấy các biến static của lớp hiện tại mà không lấy của lớp cha
    static function getClassStaticProperties($prefix = 'def') {
        $className = static::class;
        // Tạo một đối tượng ReflectionClass cho lớp mong muốn
        $reflection = new \ReflectionClass(static::class);

        // Lấy tất cả các thuộc tính static của lớp, bao gồm cả của lớp cha
        $staticProperties = $reflection->getProperties(\ReflectionProperty::IS_STATIC);

        // Mảng để lưu trữ các thuộc tính static của lớp hiện tại
        $currentClassStatics = [];

        foreach ($staticProperties as $property) {
            // Kiểm tra xem thuộc tính này được khai báo trong lớp hiện tại hay không
            if ($property->getDeclaringClass()->getName() === $className) {
                // Đảm bảo có thể truy cập giá trị của thuộc tính, ngay cả khi nó là protected hoặc private
                $property->setAccessible(true);
                if($prefix)
                if(!str_starts_with($property->getName(), $prefix))
                    continue;
                // Lấy tên và giá trị của thuộc tính và thêm vào mảng kết quả
                $currentClassStatics[$property->getName()] = $property->getValue();
            }
        }

        return $currentClassStatics;
    }

    function getDownloadAllDay(){

        $userId = $this->user_id; // replace this with the actual user_id
        $totalDownloadByte = TmpDownloadSession::where('user_id', $userId)
            ->sum('done_bytes');

//        echo "Total download byte in the last 24 hours: " . $totalDownloadByte;

        return $totalDownloadByte;
    }

    function getCountDownloadAllDay(){
        $userId = $this->user_id; // replace this with the actual user_id
        $cc = TmpDownloadSession::where('user_id', $userId)->count();
//        echo "Total download byte in the last 24 hours: " . $totalDownloadByte;
        return $cc;
    }
    function getDownloadToday(){

        $userId = $this->user_id; // replace this with the actual user_id
        $fromDate = date('Y-m-d H:i:s', strtotime('-24 hours'));

        $totalDownloadByte = TmpDownloadSession::where('user_id', $userId)
            ->where('created_at', '>=', $fromDate)
            ->sum('done_bytes');

//        echo "Total download byte in the last 24 hours: " . $totalDownloadByte;

        return $totalDownloadByte;
    }

    function getVipExpiredDate($mInfo = null) {

        if(!$mInfo)
            $mInfo = $this->mmUserInfo;

        $expireByGold = $mInfo[self::$def_expiredDateByNgold[0]] ?? 0;
        $expireByOther = $mInfo[self::$def_expiredDate[0]] ?? 0;
        if(!$expireByGold)
            $expireByGold = 0;
        if(!$expireByOther)
            $expireByOther = 0;

        $expireDate = $expireByGold > $expireByOther ? $expireByGold : $expireByOther;
        if($expireDate)
            return $expireDate;
        return null;
    }

    /**
     * Lay thong tin da su dung/ tong so cho phep cua user gom: dung luong, so lan download, thoi gian su dung
     * Cua tat cac cac OrderItem cua user
     * @return array
     */
    function getQuotaAllOfUser()
    {
        $mm = OrderItem::where('user_id', $this->user_id)->orderBy("end_time", 'desc')->get();
        $totalAllowSizeGB = 0;
        $totalAllowCount = 0;
        $totalAllowHour = 0;
        $minStartTime = 0;
        $maxStartTime = 0;
        $maxNHour = 0;

        $mmGold4S = [];

        //Dung de tinh luot tai, luot tai se tu goi này tro di:
        $ngayTaoCuaGoiChuaHetHanXaNhat = "";

        foreach($mm as $item){
            //Xử ly truong hop dac biet 4s, khi co truong tmp_ngold
            if($item->tmp_ngold){
                //Tính ngày hết hạn  nếu chưa có:
                if(!$item->end_time){
                    $nDay = $item->tmp_ngold / 5;
                    if($item->tmp_ngold <=0)
                        $item->end_time = $item->created_at;
                    else
                        $item->end_time = $item->created_at->addDays($nDay);
                    $item->addLog("Add endTime: $item->end_time");
                    $item->save();
                }
                $mmGold4S[] = ['date'=> nowyh(strtotime($item->created_at)), 'nSeconds'=>$item->tmp_ngold / 5 * _NSECOND_DAY];
                continue;
            }

            $product = Product::find($item->product_id);
            if(!$maxStartTime)
                $maxStartTime = ($item->created_at->toDateTimeString());
            if(!$minStartTime)
                $minStartTime = ($item->created_at->toDateTimeString());
            if($item->created_at->toDateTimeString() < $minStartTime)
                $minStartTime = ($item->created_at->toDateTimeString());
            if($item->created_at->toDateTimeString() > $maxStartTime)
                $maxStartTime = ($item->created_at->toDateTimeString());
            if($product){
                if($product instanceof Product);

                if(!$maxNHour)
                    $maxNHour = $product->getQuotaNHour();;
                if($product->getQuotaNHour() > $maxNHour)
                    $maxNHour = $product->getQuotaNHour();

                $nSec = $maxNHour * 3600;
                if(!$item->end_time){
                    $item->end_time = $item->created_at->addSeconds($nSec);
                    $item->addLog("Add endTime: $item->end_time");
                    $item->save();
                }

                //Neu chua het han thi moi cong gio:
                if($item->end_time > nowyh())
                    $totalAllowHour += $product->getQuotaNHour();
            }

            //Nếu gói chưa hết hạn mà ngày tạo nhỏ nhất thì lấy ngày tạo nhỏ nhất
            if($item->end_time > nowyh() && $item->created_at < nowyh() && $item->created_at > $ngayTaoCuaGoiChuaHetHanXaNhat)
                $ngayTaoCuaGoiChuaHetHanXaNhat = $item->created_at;

        }

        $quotaDownloadGBDayOfGold = '';
        //Sau khi đã có $ngayTaoCuaGoiChuaHetHanXaNhat, mới tính dung lượng tải và số lượt tải, của mọi gói chưa hết hạn
        foreach($mm as $item) {
            if($item->end_time < nowyh()){
                if(isDebugIp()){
//                    echo(" <br> Bo qua: $item->created_at < $ngayTaoCuaGoiChuaHetHanXaNhat ");
                }
                continue;
            }
            if(isDebugIp()){
//                echo(" <br> KHONG Bo qua: $item->created_at < $ngayTaoCuaGoiChuaHetHanXaNhat ");
            }

            //Xử ly truong hop dac biet 4s, khi co truong tmp_ngold
            if ($item->tmp_ngold) {
                //Nếu gói gold cũ, thì có dung lượng tải trong ngày
                $quotaDownloadGBDayOfGold = UserCloud::find($this->user_id)?->quota_daily_download ?? DEF_MAX_DOWNLOAD_DAY_GB;

            }
            else{
                $product = Product::find($item->product_id);
                if($product){
                    if($product instanceof Product);
                    $totalAllowSizeGB += $product->getQuotaSizeDownloadAllow();
                    $totalAllowCount += $product->getQuotaCountDownloadAllow();

                    if(isDebugIp()){
//                        echo(" <br> x Coong them: $totalAllowSizeGB G / $totalAllowCount lượt ");
                    }

                }
            }
        }

        if(isDebugIp()){
//            die("NGay tạo Xa nhat : $ngayTaoCuaGoiChuaHetHanXaNhat");
        }

        //Tính luôn số lượt tải, dung lượng đã tải ở đây
        $totalUsedDownloadByte = TmpDownloadSession::where('user_id', $this->user_id)->where('created_at', '>=', $ngayTaoCuaGoiChuaHetHanXaNhat)
            ->sum('done_bytes');
        $totalUsedDownloadCount = TmpDownloadSession::where('user_id', $this->user_id)->where('created_at', '>=', $ngayTaoCuaGoiChuaHetHanXaNhat)->count();
        $totalUsedDownloadGB = round($totalUsedDownloadByte / _GB);

        $totalAllowDay = ceil($maxNHour / 24);
        $expiredDate = nowyh(strtotime($maxStartTime . " + $totalAllowDay days"));
//        $expiredDate = nowyh(strtotime($maxStartTime));
        if(!$totalAllowDay)
            $expiredDate = '';

        //Và dung lượng, số lượt còn lại
        $totalFreeDownloadGB = $totalAllowSizeGB - round($totalUsedDownloadByte / _GB);
        if($totalFreeDownloadGB < 0)
            $totalFreeDownloadGB = '';
        $totalFreeCountDownload = $totalAllowCount - $totalUsedDownloadCount;
        if($totalFreeCountDownload < 0)
            $totalFreeCountDownload = '';


//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($mmGold4S);
//        echo "</pre>";
        $expiredDateGold = null;
        $nDayAllowWithGold = 0;
        if($mmGold4S){
            [$nSecondAllowFromNow, $expiredDateGold] = OrderItem::calculateRemainingSeconds($mmGold4S, nowyh());
            if($nSecondAllowFromNow)
                $nDayAllowWithGold = ceil($nSecondAllowFromNow / _NSECOND_DAY);
        }

        return [
            self::$def_maxNHour[0] => $maxNHour,
            self::$def_maxNDay[0] => $maxNHour / 24,
            self::$def_minStartTime[0] => $minStartTime,
            self::$def_maxStartTime[0] => $maxStartTime,
            self::$def_totalAllowSizeGB[0] => $totalAllowSizeGB,
            self::$def_totalAllowCount[0] => $totalAllowCount,
            self::$def_totalAllowHour[0] => $totalAllowHour,
            self::$def_totalAllowDay[0] => $totalAllowDay,
            self::$def_totalAllowDownloadDailyGB[0] => $quotaDownloadGBDayOfGold,
            self::$def_totalUsedDownloadByte[0] => $totalUsedDownloadByte,
            self::$def_totalUsedDownloadGB[0] => $totalUsedDownloadGB,
            self::$def_totalUsedDownloadCount[0] => $totalUsedDownloadCount,
            self::$def_totalFreeDownloadGB[0] => $totalFreeDownloadGB,
            self::$def_totalFreeCountDownload[0] => $totalFreeCountDownload,
            self::$def_expiredDate[0] => $expiredDate,
            self::$def_expiredDateByNgold[0] => $expiredDateGold,
        ];


    }

}


