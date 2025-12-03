<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use LadLib\Common\UrlHelper1;
use LadLib\Laravel\Database\TraitModelExtra;

class SiteMng extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    /**
     * @var SiteMng
     */
    public static $one;

    public static $tmp_encode_id_cloud;

    static function getAuthType()
    {
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        if(isset($GLOBALS['mMapDomainDb'][$domainName] ))
            if(isset($GLOBALS['mMapDomainDb'][$domainName]['password_type']))
                return 2;
        return 1;
    }

    static function getAuthTypeSha1()
    {
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        if(isset($GLOBALS['mMapDomainDb'][$domainName] ))
            if(isset($GLOBALS['mMapDomainDb'][$domainName]['password_type']) && $GLOBALS['mMapDomainDb'][$domainName]['password_type'] == 2)
                return 1;
        return 0;
    }

    static function getForceLanguage()
    {
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        $ret = $GLOBALS['mMapDomainDb'][$domainName]['force_fallback_language'] ?? 'vi';
        return $ret;
    }

    static function isEnableMultiLanguage()
    {
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        return @$GLOBALS['mMapDomainDb'][$domainName]['enable_multi_language'] ?? null;
    }

    static function getDbName(){
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        return @$GLOBALS['mMapDomainDb'][$domainName]['db_name'];
    }

    static function isAdminSidebarCollapse()
    {
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        return @$GLOBALS['mMapDomainDb'][$domainName]['sidebar-collapse'] ?? null;
    }


    static function getUploadTmpFolderGlx()
    {
        //upload_tmp_folder_glx
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        $folder = @$GLOBALS['mMapDomainDb'][$domainName]['upload_tmp_folder_glx'] ?? env('DEF_BASE_FILE_UPLOAD_FOLDER');
        return $folder;
//        if(!file_exists($folder)){
//            mkdir($folder, 0755, true);
//        }
//        if(!file_exists($folder)){
//            $folder = strip_tags($folder);
//            loi("Can not create folder: $folder");
//        }
    }

    static function rand_in_redis()
    {
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        return @$GLOBALS['mMapDomainDb'][$domainName]['rand_in_redis'] ?? null;
    }

    static function getDbAdminUrl()
    {
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        return @$GLOBALS['mMapDomainDb'][$domainName]['dbadmin'] ?? null;
    }

    static function getUploadDomainUrl()
    {
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        return @$GLOBALS['mMapDomainDb'][$domainName]['upload_domain'] ?? null;
    }

    static function use_snowflake_models($modelName)
    {
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        $models0 = $GLOBALS['mMapDomainDb'][$domainName]['use_snowflake_models'] ?? '';
        if ($models0) {
            $models = explode(',', $models0);
            if (in_array($modelName, $models)) {
                return true;
            }
        }
        return false;
    }

    static function getDbConnectionDriverName()
    {
        //$connectionName = config('database.default'); // 'mysql'
        $driver = DB::connection()->getDriverName(); // 'mysql'
        return $driver; // Driver: mysql
    }

    static function isUsePosgresDb()
    {
        $driver = DB::connection()->getDriverName(); // 'mysql'
        if ($driver == 'pgsql') {
            return true;
        }
        return false;
    }

    static function isUseMysqlDb()
    {
        $driver = DB::connection()->getDriverName(); // 'mysql'
        if ($driver == 'mysql') {
            return true;
        }
        return false;
    }
    static function isUseSqllite()
    {
        $driver = DB::connection()->getDriverName(); // 'mysql'
        if ($driver == 'sqlite') {
            return true;
        }
        return false;
    }

//use_own_meta_table

    static function isUseOwnMetaTable()
    {
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        $sid = $GLOBALS['mMapDomainDb'][$domainName]['siteid'] ?? '';
        if(!$sid)
            $sid = "_not_sid_";

        $useOwn = @$GLOBALS['mMapDomainDb'][$domainName]['use_own_meta_table'] ?? null;

        if($useOwn)
            return $sid;

        return null;

    }

    static function enableDocumentEditor()
    {
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        return @$GLOBALS['mMapDomainDb'][$domainName]['enable_doc_edit'] ?? null;
    }

    static function isIndexElasticFile()
    {
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        return @$GLOBALS['mMapDomainDb'][$domainName]['is_index_elastic_file'] ?? null;
    }

    static function enable4sLink()
    {
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        return @$GLOBALS['mMapDomainDb'][$domainName]['4s_link'] ?? null;
    }

    public function getValidateRuleInsert()
    {

        //        if(!isIPDebug())
        //            return;
        //OK: '/^([^`\$<>]+)$/u'; //Chuỗi bất kỳ không chứa `$<>
        $sreg = '/^([^`\$<>()#@\!]+)$/u';

        return [
            'domain' => 'nullable|regex:'.$sreg.'|max:64',
            'domain1' => 'nullable|regex:'.$sreg.'|max:64',
            'domain2' => 'nullable|regex:'.$sreg.'|max:64',
            'domain3' => 'nullable|regex:'.$sreg.'|max:64',
            'domain4' => 'nullable|regex:'.$sreg.'|max:64',
            'domain5' => 'nullable|regex:'.$sreg.'|max:64',
            'MEMBER_APP_NAME' => 'nullable|regex:'.$sreg.'|max:64',
            'metaTitle' => 'nullable|regex:'.$sreg.'|max:256',
            'metaDescription' => 'nullable|regex:'.$sreg.'|max:256',
            'metaTitle_en' => 'nullable|regex:'.$sreg.'|max:256',
            'metaDescription_en' => 'nullable|regex:'.$sreg.'|max:256',
            'admin_email' => 'nullable|email|max:64',
            'admin_phone_support' => 'nullable|regex:'.$sreg.'|max:15',
        ];
    }

    public function getValidateRuleUpdate($id = null)
    {
        return $this->getValidateRuleInsert();
    }

    public static function getTitle()
    {
        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one) {
            return null;
        }

        return static::$one->metaTitle;
    }

    public static function getTitleEn()
    {
        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one) {
            return null;
        }


        $lang = app()->getLocale();
        if ($lang == 'en' && static::$one->metaTitle_en) {
            return static::$one->metaTitle_en;
        }

        return static::$one->metaTitle_en;
    }

    public static function getDesc()
    {
        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one) {
            return null;
        }

        $lang = app()->getLocale();
        if ($lang == 'en' && static::$one->metaDescription_en) {
            return static::$one->metaDescription_en;
        }

        return static::$one->metaDescription;
    }

    public static function getLogoIcon($id = '')
    {
        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one) {
            return null;
        }
        $fieldId = "logo_image$id";
        $fid = static::$one->$fieldId;
        if ($obj = FileUpload::find($fid)) {
            return $obj->getCloudLinkImage();
        }
        return '';
    }

    static function isEncodeIdModel($model)
    {

    }

    static function isEncodeIdCloud()
    {
        if(isset(self::$tmp_encode_id_cloud))
            return self::$tmp_encode_id_cloud;

        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        if(isset($GLOBALS['mMapDomainDb'][$domainName] ))
            if(isset($GLOBALS['mMapDomainDb'][$domainName]['encode_id_rand_models'])) {
                $str = $GLOBALS['mMapDomainDb'][$domainName]['encode_id_rand_models'];
                if (str_contains($str, 'FileUpload')){
                    self::$tmp_encode_id_cloud = 1;
                    return 1;
                }
            }
        self::$tmp_encode_id_cloud = 0;
        return 0;
    }

    public static function getKeyword()
    {
        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one) {
            return null;
        }

        return static::$one->metaKeyword;
    }

    public static function getDomain1()
    {
        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one) {
            return null;
        }

        return static::$one->domain1;
    }

    public static function getPhoneAdmin()
    {
        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one) {
            return null;
        }

        return static::$one->admin_phone_support;
    }

    public static function getAddress1()
    {
        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one) {
            return null;
        }

        return static::$one->address1;
    }

    public static function getAddress2()
    {
        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one) {
            return null;
        }

        return static::$one->address2;
    }

    public static function getMaxSizeUpload()
    {
        $max = self::getAnyFieldValue('maxSizeUploadWebMB');
        //Mặc định chỉ cho 10MB
        if (! $max) {
            return 10 * _MB;
        }

        if ($max && is_numeric($max)) {
            return min($max * _MB, get_file_upload_max_size());
        }

        return get_file_upload_max_size();
    }

    public static function getAnyFieldValue($field)
    {
        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one) {
            return null;
        }

        return static::$one->$field;
    }

    public static function getEmailAdmin($notCache = false)
    {
        if($notCache){
            return SiteMng::first()?->admin_email;
        }

        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one) {
            return null;
        }

        return static::$one->admin_email;
    }

    //    static function getMetaTitle(){
    //        if(!static::$one)
    //            static::$one = SiteMng::first();
    //        if(!static::$one)
    //            return null;
    //        return static::$one->metaTitle;
    //    }
    public static function getAppName()
    {
        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one) {
            return 'ADMIN';
        }
        if (! static::$one->MEMBER_APP_NAME) {
            return 'ADMIN';
        }

        return static::$one->MEMBER_APP_NAME;
    }

    public static function getImageOg()
    {
        self::getLogo();
    }

    /**
     * @return SiteMng
     */
    public static function getInstance()
    {
        if (! static::$one) {
            static::$one = SiteMng::first();
        }

        return static::$one;
    }

    public static function getLogo()
    {
        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one) {
            return '/template/gp1/images/logo_xoay.png';
        }
        $logo = static::$one->getThumbInImageList('logo_image');
        if (! $logo) {
            return '/template/gp1/images/logo_xoay.png';
        }

        return $logo;
    }

    public static function getGOOGLE_CLIENT_ID($dm = null)
    {
        $domainName = \LadLib\Common\UrlHelper1::getDomainHostName();
        return @$GLOBALS['mMapDomainDb'][$domainName]['GOOGLE_CLIENT_ID'] ?? null;
    }

    public static function getSiteId($dm = null)
    {
        return getSiteIDByDomain();
        //        if(!$dm)
        //            $dm = UrlHelper1::getDomainHostName();
        //        $sid = $GLOBALS['mMapDomainDb'][$dm]['siteid'];
        //        return $sid;
    }

    public static function getSiteCode($notUseCache = false)
    {
        if($notUseCache){
           return SiteMng::first()?->site_code;
        }

        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one || ! static::$one->site_code) {
            return 'MãCty';
        }

        return static::$one->site_code;
    }

    public static function getGoogleAnalyticCode()
    {
        if (! static::$one) {
            static::$one = SiteMng::first();
        }
        if (! static::$one) {
            return null;
        }

        return static::$one->google_analytics_code;
    }
}
