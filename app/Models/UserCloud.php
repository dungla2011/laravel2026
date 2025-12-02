<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Laravel\Database\TraitModelExtra;

defined("DEF_MAX_DOWNLOAD_DAY_GB") || define("DEF_MAX_DOWNLOAD_DAY_GB", 120);


class UserCloud extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    /**
     * @param  $field
     * @return MetaOfTableInDb
     */

    /**
     * Tạo user cloud mặc định nếu chưa có
     *
     * @return UserCloud
     */
    public static function getOrCreateNewUserCloud($user_id, $gb = 1, $nfile = 1000)
    {
        if ($ret = UserCloud::where('user_id', $user_id)->first()) {
            return $ret;
        }
        $pr = ['user_id' => $user_id];
        //100MB
        $pr['quota_size'] = _GB * $gb;
        $pr['quota_file'] = $nfile;

        return UserCloud::create($pr);
    }

    function getQuotaDailyDownload()
    {
        if(!$this->quota_daily_download)
            return DEF_MAX_DOWNLOAD_DAY_GB;
        return $this->quota_daily_download;
    }


    /**
     * Lấy Đường dẫn lưu trữ file upload lên, của user
     *
     * @return mixed|string
     */
    public function getLocationFile()
    {

        $sid = getSiteIDByDomain();

        if ($this->location_store_file) {
            $locationStore = $this->location_store_file;
        } else { //$locationStore = DEF_BASE_FILE_UPLOAD_FOLDER."/". env('DB_DATABASE') . "/user_files_" . $this->user_id; ;
            //            $locationStore = DEF_BASE_FILE_UPLOAD_FOLDER."/". env('DB_DATABASE') . "/user_files";

            if($tmpFolder = SiteMng::getUploadTmpFolderGlx())
                $locationStore = $tmpFolder."/user_files/siteid_$sid";
            else
                $locationStore = DEF_BASE_FILE_UPLOAD_FOLDER."/user_files/siteid_$sid";

        }

        return $locationStore;
    }
}
