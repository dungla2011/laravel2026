<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use LadLib\Common\UrlHelper1;
use LadLib\Laravel\Database\TraitModelExtra;

class FileUpload extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra, SnowflakeId;
    protected $guarded = [];

    function isImageFileName(){
        $ext = strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
        $arrExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
        if (in_array($ext, $arrExt)) {
            return true;
        }
        return false;
    }

    function afterInsertModel()
    {
        if(SiteMng::isIndexElasticFile())
        {

            try {

//                dumpdebug("After insert file upload: ". $this->id);




                $svEl = 'elasticSv';
                $client = \Elastic\Elasticsearch\ClientBuilder::create()
                    ->setBasicAuthentication('elastic',env('ELASTIC_PASSWORD'))
                    ->setHosts(["http://$svEl:9200"])
                    ->setSSLVerification(false)->build();


                $dbName = $this->getElasticDbName();
                $obj = $this;
                $name = strip_tags($obj->name);
                $name = fixFileNameToIndexElastic($name);

                $body = [
                    'id' => $obj->id,
                    'name' => $name,
                    'size' => $obj->file_size,
                    'user_id'=> $obj->user_id,
                    'md5' => ($obj->md5),
                    'ext' => strtolower(pathinfo($obj->name, PATHINFO_EXTENSION)),
                    'count_download' => $obj->count_download,
                    'crc32b' => ($obj->crc32),
                    'created_at' => strtotime($obj->created_at),
                ];

                $prs = [
                    'index' => $dbName,
                    'id' => $obj->id,
                    'type' => 'article_type',
                    //'timestamp' => time(),
                    'body' => $body
                ];
    //                    $response = $client->update($prs);
                $response = $client->index($prs);
                $this->addLog("Add to elastic: done?");
//                dumpdebug("After insert file upload2: ". $this->id);
            }
            catch (\Elasticsearch\Common\Exceptions\BadRequest400Exception $e) { // For PHP 7
                $this->addLog("Add to elastic: Error1?");
                dumperrorglobal("Error sync elastic after upload1: ". $e->getMessage());
            }
            catch (\Exception $e){
                $this->addLog("Add to elastic: Error2?");
                dumperrorglobal("Error sync elastic after upload2: ". $e->getMessage());
            }
        }
    }

    public function getValidateRuleInsert()
    {

        //        if(!isIPDebug())
        //            return;
        //OK: '/^([^`\$<>]+)$/u'; //Chuỗi bất kỳ không chứa `$<>
        $sreg = '/^([^`\$<>#\!]+)$/u';

        return [
            //            'name'=>'required|regex:/^([^<>]+)$/u|max:200',
            'name' => 'required|regex:'.$sreg.'|max:255',
            'comment' => 'max:50',
        ];
    }

    static function getCloudObj($fid)
    {
        if(!is_numeric($fid))
            $fid = qqgetIdFromRand_($fid);
        if(!is_numeric($fid))
            return null;
        if($obj = FileUpload::find($fid))
            return FileCloud::find($obj->cloud_id);
        return null;
    }

    public function getLink1($set = null)
    {
        if($set)
        if($this->getId() && !$this->link1){
            $this->link1 = "ms".eth1b($this->getId());
            $this->update();
        }
        if(!str_starts_with($this->link1, 'ms'))
            return "ms".($this->link1);
        return $this->link1;
    }

    function getElasticDbName()
    {
        return 'file_upload_4s2';
//        return "sid_0_ms_cloud_file_4s";
    }

    public function getValidateRuleUpdate($id = null)
    {
        //OK: '/^([^`\$<>]+)$/u'; //Chuỗi bất kỳ không chứa `$<>
        $sreg = '/^([^`\$<>#\!]+)$/u';

        return [
            //            'name'=>'required|regex:/^([^<>]+)$/u|max:200',
            'name' => 'regex:'.$sreg.'|max:255',
            'comment' => 'max:50',
        ];
    }

    public function getFilePathCloud()
    {
        if ($fc = FileCloud::find($this->cloud_id)) {
            return $fc->file_path;
        }

        return null;
    }

    /**
        Apache:
     *  Alias /slink /var/glx/upload_file_glx/user_files/slink
     *
     * @return string
     */
    public function getCloudLinkImgThumb($w = 300, $domain = null)
    {
        if ($domain) {
            $urlBase = $domain;
        } else {
            $urlBase = UrlHelper1::getUrlWithDomainOnly();
        }

        $idx= 'id';
        if($this->ide__ ?? '')
            $idx = 'ide__';
        if ($w){
            return $urlBase.'/test_cloud_file?fid='.$this->$idx."&type=thumb&size=$w";
        }
        return $urlBase.'/test_cloud_file?fid='.$this->$idx;
    }

    public function getSlink()
    {
        $urlBase = UrlHelper1::getUrlWithDomainOnly();

        $dm = UrlHelper1::getDomainHostName();
        if (substr_count($dm, '.') > 1) {
            $dm0 = substr($dm, strpos($dm, '.') + 1);
            $urlBase = str_replace($dm, $dm0, $urlBase);
            //die("xxx $dm0 $urlBase");
        }
        if ($dm != 'localhost' && $dm != '127.0.0.1' && substr_count($urlBase, '.') <= 1) {
            $urlBase = str_replace('://', '://cdn-img-'.rand(1, 9).'.', $urlBase);
        }

        $siteId = getSiteIDByDomain();
        if (! $siteId) {
            $siteId = '0';
        }

        $path = $siteId.'/'.gen_path_from_number_not_file($this->id);

        //Todo xxx : cần setting theo siteid ở đây
        // $file = "/var/glx/upload_file_glx/user_files/siteid_$siteId/$path";

        //        die($file);

        //Todo: chưa alias được
        //        if(file_exists($file))
        //            return $urlBase."/image_static2/siteid_$siteId/".$path;

        //        $dbName =
        //Kiem tra file co tren static ko, 07.2023:

        $obj2 = $this::find($this->id);
        if(!$obj2->link1){
            //Save ở đây sẽ sinh lỗi api index, vì tự nhiên link1 và updateAt được đưa vào index field
            $obj2->link1 = eth1b($obj2->getId(), null, $obj2->getId());
            $obj2->save();
        }

        $fileRealPath = $this->getFilePathCloud();
        $uploadFolder = env("UPLOAD_FOLDER");
        $spath = "$uploadFolder/user_files/slink/$path";
        $spathFile = "$uploadFolder/user_files/slink/$path/".$this->link1;
        $pathFileId = $path.'/'.$this->link1;
        //if(isDebugIp())

        if (file_exists($spathFile)) {
            return '/slink/'.$pathFileId;
        }
        if (file_exists($fileRealPath)) {
            //                echo "<br/>\n sp = $spath";
            if (! file_exists(($spath))) {
                mkdir(($spath), 0755, 1);
            }

            try {
                if (symlink($fileRealPath, $spathFile)) {
                    return '/slink/'.$pathFileId;
                }
            } catch (\Throwable $e) { // For PHP 7
                return "error_symlink: $spath / $fileRealPath / ".$e->getMessage();
            }
        }

        $uploadFolder = env('UPLOAD_FOLDER');

        //Mỗi mytree theo kiểu cũ glx2022db
        if ($siteId == 1) {
            $file = "$uploadFolder/glx2022db/user_files/$path";
            if (file_exists($file)) {
                //            if(is_link($file . '.png'))
                //                return $urlBase."/image_static/".$path.'.png';
                //            else
                //            if(symlink(  $file, $file . '.png'))
                //                return $urlBase."/image_static/".$path.'.png';
                return $urlBase.'/image_static/'.$path;
            }
        }

        $urlBase = '';

        return $urlBase.'/test_cloud_file?fid='.$this->getId();
    }

    public function getCloudLinkEnc($returnCloudLink = 1){

        if(!SiteMng::isEncodeIdCloud())
            return '/test_cloud_file?fid='.$this->id;

        if($this->ide__){
            return '/test_cloud_file?fid='.$this->ide__;
        }

        //Save ở đây sẽ sinh lỗi api index, vì tự nhiên link1 và updateAt được đưa vào index field
        if(!$this->id__){
//            $this->link1 = eth1b($this->id, null, $this->id);
            $this->save();
        }
        return '/test_cloud_file?fid='.$this->id__;
    }

    public function getCloudLink($returnCloudLink = 1){

        //Save ở đây sẽ sinh lỗi api index, vì tự nhiên link1 và updateAt được đưa vào index field
        if(!$this->link1){
//            $this->link1 = eth1b($this->id, null, $this->id);
//            $this->save();
        }
        if($this->ide__ ?? '')
            return '/test_cloud_file?fid='.$this->ide__;
        return '/test_cloud_file?fid='.$this->id;
    }

    public function getCloudLinkImage()
    {
        return $this->getCloudLink();

        return $this->getCloudLink().'&type=thumb';
    }

    public static function createQuotaUser($uid)
    {
        $gpUser = UserCloud::where('user_id', $uid)->first();
        if (! $gpUser) {
            $mm = ['user_id' => $uid, 'quota_file' => DEF_QUOTA_USER_CLOUD_INIT_FILE, 'quota_size' => DEF_QUOTA_USER_CLOUD_INIT_SIZE_GB * 1024 * 1024 * 1024];
            UserCloud::create($mm);
        }
        $gpUser = UserCloud::where('user_id', $uid)->first();

        return $gpUser;
    }

    /** Làm giảm tải khi cần, ví dụ 5 phút cache
     *  Đề phòng user fresh liên tục nặng db
     * */
    static function getUserQuotaUsingCache($user_id){
        $key = "user_using_cloud_$user_id";
        if($value = Cache::get($key)){
            $ret =  json_decode($value);
            $ret->is_cache = nowyh();
            return (array) $ret;
        }
        $countUsing = FileUpload::where('user_id', $user_id)->count();
        $sumByteUsing = FileUpload::where('user_id', '=', $user_id)->sum('file_size');
        $ret = ['count_file'=>$countUsing, 'size_byte'=>$sumByteUsing , 'is_cache'=>0];
        Cache::put($key, json_encode($ret), 60); // 600 giây (10 phút)
        return $ret;
    }

    public static function checkQuota($uid, $mess)
    {

        //Todo: cần check cả số folder cho phép, và file size
        //Hoặc folder thì sẽ có quota riêng ở bên folder, mặc định 1000 chẳng hạn

        //        $uid = Auth::id();
        //Kiểm tra quota user tại đây:
        //$gpUser = UserCloud::where('user_id', $uid)->first();
        $gpUser = FileUpload::createQuotaUser($uid);
        if (! $gpUser) {
            return rtJsonApiError("Not found quota for user! $uid", 500);
        }

        //Tính toán quota của user đã dùng
        $countUsing = FileUpload::where('user_id', $uid)->count();
        $sumByteUsing = FileUpload::where('user_id', '=', $uid)->sum('file_size');

        if ($gpUser->quota_size <= $sumByteUsing) {
            return rtJsonApiError("$mess đã sử dụng quá dung lượng cho phép: ".ByteSize($sumByteUsing).' > '.ByteSize($gpUser->quota_size));
        }

        if ($gpUser->quota_file <= $countUsing) {
            return rtJsonApiError("$mess đã sử dụng quá số file cho phép: $countUsing > $gpUser->quota_file");
        }

        return null;
    }

    public static function uploadFileContentByApi0($url, $tokenApi, $filePath, $pid = 0)
    {
        $mime = mime_content_type($filePath);
        $bname = basename($filePath);
        echo "\n Bname = $bname";

        return self::uploadFileContentByApi($url, $tokenApi, file_get_contents($filePath), basename($filePath), $mime, $pid);
    }

    //https://stackoverflow.com/questions/3085990/post-a-file-string-using-curl-in-php
    //https://php.watch/versions/8.1/CURLStringFile
    /*
     * $txt = 'test content';
    $txt_curlfile = new \CURLStringFile($txt, 'text/plain', 'test.txt');
    $ch = curl_init('http://example.com/upload.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => $txt_curlfile]);
    curl_exec($ch);
     */
    /**
     * @param  int  $uid
     * @param  int  $pid
     * @return int
     *             /api/member-file/upload
     */
    public static function uploadFileContentByApi($url, $tokenApi, $fileCont, $fileName, $mime = '', $pid = 0)
    {

        $cFile = new \CURLStringFile($fileCont, $fileName, $mime);
        //        $cFile = new \CURLFile($fileName);
        $post = ['set_parent_id' => $pid, 'file_data' => $cFile];
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            //'Content-Type: application/json',
            'Authorization: Bearer '.$tokenApi,
            'Content-type: multipart/form-data',
        ]);
        $result = curl_exec($ch);
        if (! $result) {
            return null;
        }
        //
        echo "\n $mime / $fileName / Upload RET ---";
        print_r($result);

        $error_msg = curl_error($ch);
        if ($error_msg) {
        }
        curl_close($ch);

        if ($obj = json_decode($result)) {
            $id = $obj->payload->id;
            //            $name = $obj->payload->name;
            //            echo "<br/>\n ID File = $id ";
            if (is_numeric($id)) {

//                ob_clean();
                return $id;

                //                if($ofile = FileUpload::find($id))
                //                {
                //                    return $ofile;
                //                }
            }
        }

        return null;
    }

    /**
     * Upload trực tiếp file
     * @param $filePath
     * @param $basename
     * @param $uid
     * @param $pid
     * @param $returnType
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public static function uploadFileLocal($filePath, $basename = '', $uid = 1, $pid = 0, $returnType = 1, $refer = '')
    {
        if(!file_exists($filePath))
            loi("File not found: $filePath");

        if(!$basename)
            $basename = basename($filePath);
        $std = new \stdClass();
        $std->file_name = $basename;
        $std->file_size = filesize($filePath);
        $std->file_path_local_upload_ = $filePath;
        $std->file_path_local_is_copy_ = 0;
        $std->folder_id = $pid;
        $std->user_id = $uid;
        $std->refer = $refer;
        $ret = \App\Http\ControllerApi\FileUploadControllerApi::uploadStatic($std,$returnType);
        return $ret;
    }

    public static function uploadFileLocalCopy($filePath, $basename = '', $uid = 1, $pid = 0, $returnType = 2)
    {
        if(!$basename)
            $basename = basename($filePath);
        $std = new \stdClass();
        $std->file_name = $basename;
        $std->file_size = filesize($filePath);
        $std->file_path_local_upload_ = $filePath;
        $std->file_path_local_is_copy_ = 1;
        $std->folder_id = $pid;
        $std->user_id = $uid;
        $ret = \App\Http\ControllerApi\FileUploadControllerApi::uploadStatic($std,$returnType);
        return $ret;
    }

    ///api/member-file/upload
    public static function uploadFileApiV2($url, $tk, $fileCont, $fileName, $mime, $param = null)
    {
        //        $tk = User::find($uid)->getUserToken();

        //        $url = "/api/member-file/upload";

        $cFile = new \CURLStringFile($fileCont, $fileName, $mime);

        //        $cFile = curl_file_create($filePath);

        //$res  = $this->post($url, ['file'=>$cFile, 'set_parent_id'=>0]);
        //$res  = $this->post($url, ['set_parent_id'=>0]);

        //        $cFile = new \CURLFile($filePath);
        //        $cFile->name = $fileName;

        //        $post = [$param ...,  ;//['set_parent_id' => $pid, 'file_data' => $cFile];
        if($param)
            $post = array_merge($param, ['file_data' => $cFile]);
        else
            $post = ['file_data' => $cFile];
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($post);
        //        echo "</pre>";

        //        die();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            //'Content-Type: application/json',
            'Authorization: Bearer '.$tk,
        ]);

        return $result = curl_exec($ch);
    }
}
