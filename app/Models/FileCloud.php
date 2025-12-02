<?php

namespace App\Models;

use App\Components\TreeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;


//function call_back_fn_download_glx($ch, $chunk) {
//    echo $chunk;
//    return strlen($chunk);
//};

/**
 * FileCloud này để lưu trữ 1 file duy nhất (MD5 duy nhất) để tránh lãng phí lưu file giống nhau , khi user upload file lên
 * Bảng FileUpload lưu thông tin file được user upload
 * Nếu File upload lên có thể trùng nội dung với 1 file upload trước đó (check trùng MD5) vẫn đang tồn tại trên DB và file vật lý
 * thì bảng FileUpload khi đó lưu thông tin file mới trỏ đến FileCloud đã có (với trường cloud_id), và xóa file mới up lên
 */
class FileCloud extends ModelGlxBase implements TreeInterface
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    public function getParentClass(){
        return FolderFile::class;
    }
    //Hãy implment tất cả các hàm này nếu muốn sử dụng TreeInterface
    public function getChildClass(){
        return null;
    }
    //Hãy implment tất cả các hàm này nếu muốn sử dụng TreeInterface
    public function getParent($uid){

    }
    //Hãy implment tất cả các hàm này nếu muốn sử dụng TreeInterface
    public function getChildren($uid){

    }
    //Hãy implment tất cả các hàm này nếu muốn sử dụng TreeInterface
    public function isLeaf(){

    }

    static function genPathFileCloud($uid, $fid)
    {
        $userCloud = UserCloud::getOrCreateNewUserCloud($uid);
        $locationStore = $userCloud->getLocationFile();
        return "$locationStore/".gen_path_from_number($fid).'/'.$fid;
    }

    function setMd5($log = 0)
    {
        if(!file_exists($this->file_path)){
            if($log && function_exists('ol1'))
                ol1("setMd5 File not found: '$this->file_path'");
            return null;
        }

        //Neu co roi bo qua
        if(FileCloud::find($this->id)?->md5){
            return null;
        }

        if (!$this->md5) {
            if($log && function_exists('ol1'))
                ol1("get md5 - " . nowyh());
            if ($md5 = md5_file($this->file_path)) {
                $md5 = trim($md5);
                $this->md5 = $md5;
                $this->addLog("Add md5 OK: '$md5'", 1);
            } else {
                $this->addLog("Error: can not get md5?", 1);
            }
            if($log && function_exists('ol1'))
                ol1("done get md5 - " . nowyh());
            $this->save();
        }

        return $this->md5;

    }
    function setCRC32($log = 0)
    {
        if(!file_exists($this->file_path)){
            if($log && function_exists('ol1'))
                ol1("setCRC32 File not found: '$this->file_path'");
            return null;

        }
        //Neu co roi bo qua
        if(FileCloud::find($this->id)?->crc32){
            return null;
        }

        if (!$this->crc32) {
            if($log && function_exists('ol1'))
                ol1("get crc - " . nowyh());
            if ($crc32 = \App\Models\FileCloud::getCrc32b($this->file_path)) {
                $this->crc32 = $crc32;
                $this->addLog("Add CRC32 OK: '$crc32'");
                $this->save();
            } else {
                $this->addLog("Error: can not get CRC32?");
                $this->save();
            }

            if($log && function_exists('ol1'))
                ol1("done get crc - " . nowyh());
        }
        return $this->crc32;
    }

    function checkRemoteFileExist()
    {
        $server = $this->server1;
        $location = $this->location1;
        $fid = $this->getId();
        $filesize = $this->size;
        $filetime = '';

        if(!$fid)
            return 0;
        if(!$server)
            return 0;
        if(!$location)
            return 0;
        if(!$filesize)
            return 0;

        $dirRang = $fid - $fid % 1000;
        $dirRang.="-" . ($dirRang + 1000);
        //$locationFull = "/mnt/$location/$dirRang/$idfileToGetPath";
        $locationFull = "/mnt/$location/$dirRang/$fid";

        $strHex = STH("$locationFull\"$filesize\"$filetime");

        //$urlCheck = "http://$server:" . SERVER_INFO_WEB_PORT . "/checkGlxFile.html?check=$strHex";
//        $urlCheck = "http://$server:" . SERVER_INFO_WEB_PORT . "/tool/sysinfo_glx.php?checkfile=$strHex";
        $urlCheck = "https://$server/tool/sysinfo_glx.php?checkfile=$strHex";
        $check = @file_get_contents($urlCheck);
        if (!$check)
            loi("Can not call remote $server!");

        if (str_contains($check, "OKFILETHIS:$locationFull") == FALSE) {
            echo("Error " . __FUNCTION__ . ": File not good in DLSV or Deleted?");
            return 0;
        }

        return 1;
    }

    static function getCrc32b($file){
        if(!file_exists($file))
            loi("File not found: '$file'");
        return hexdec(hash_file('crc32b', $file));
    }

    /**
     * Đưa một direct link vào, sau đó re-stream nó ra cho client, cho phép resume download
     * @param $dlink
     * @param $filesize
     * @param $fname
     * @return bool|string
     */
    static function outputWebResumeProxyDownload($dlink, $filesize = 0, $fname){

        $writefn11 = function ($ch, $chunk) {
            echo $chunk;
            return strlen($chunk);
        };

        $size = $filesize;
        $seek_start = 0;
        $seek_end = $size - 1;
        //check if http_range is sent by browser (or download manager)
        if (isset($_SERVER['HTTP_RANGE'])) {
            list($size_unit, $range_orig) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if ($size_unit == 'bytes') {
                @list($range, $extra_ranges) = @explode(',', $range_orig, 2);
                @list($seek_start, $seek_end) = @explode('-', $range, 2);
                $seek_end = (empty($seek_end)) ? ($size - 1) : min(abs(intval($seek_end)), ($size - 1));
                $seek_start = (empty($seek_start) || $seek_end < abs(intval($seek_start))) ? 0 : max(abs(intval($seek_start)), 0);
                if ($seek_start > 0 || $seek_end < ($size - 1)) {
                    header('HTTP/1.1 206 Partial Content');
                    header('Content-Range: bytes ' . $seek_start . '-' . $seek_end . '/' . $filesize);
                    header('Content-Length: ' . ($seek_end - $seek_start + 1));
                    header('Accept-Ranges: bytes');
                }
            } else {
                $range = '';
            }
        } else {
            $range = '';
        }

        if (!$range)
            header('Content-Length: ' . $filesize);

    //    header("Content-Type: " . CFile::getMimeRemote($dlink));
        header('Content-Disposition: attachment; filename="' . $fname . '"');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $dlink);
        curl_setopt($ch, CURLOPT_RANGE, "$seek_start-$seek_end");
//        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $writefn11);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
