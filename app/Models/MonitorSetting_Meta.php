<?php

namespace App\Models;

use App\Components\Helper1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\Database\MetaOfTableInDb;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;
use Illuminate\Support\Facades\Log;

/**
 * ABC123
 * @param null $objData
 */
class MonitorSetting_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/monitor-setting";
    protected static $web_url_admin = "/admin/monitor-setting";

    protected static $api_url_member = "/api/member-monitor-setting";
    protected static $web_url_member = "/member/monitor-setting";


    public static $disableAddItem = true;

    //public static $folderParentClass = MonitorSettingFolderTbl::class;
    public static $modelClass = MonitorSetting::class;

    /**
     * @param $field
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field){

        $objMeta = new MetaOfTableInDb();

        //Riêng Data type của Field, Lấy ra các field datatype mặc định
        //Nếu có thay đổi sẽ SET bên dưới
        $objSetDefault = new MetaOfTableInDb();
        $objSetDefault->setDefaultMetaTypeField($field);

        $objMeta->dataType = $objSetDefault->dataType;

        if($field == 'status'){
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if($field == 'tag_list_id'){
            $objMeta->join_api_field = 'name';
//          $objMeta->join_func = 'joinTags';
            //MonitorSetting edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\MonitorSettingFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\MonitorSettingFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'timezone'){
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        //Nếu không set thì lấy của parent default nếu có
        if(!$objMeta->dataType)
            if($ret = parent::getHardCodeMetaObj($field))
                return $ret;

        return $objMeta;
    }
    function _image_list1($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }

    public function setDefaultValue($field)
    {
        if($field == 'timezone')
            return '7';
    }

    function _user_id($obj, $val, $field)
    {
        $user = User::find($val);
        if($user)
            return "<div class='mx-2 small'> <a target='_blank' href='/admin/user/edit/$val'> $user->email </a> </div>";
    }

    function _timezone($obj,$val, $field)
    {
        $main_timezones = [
            0=> '-- Your Time Zone --',
            -11=> 'GMT-11: Pacific/Midway',
            -10=> 'GMT-10: Pacific/Honolulu',
            -9=> 'GMT-9: America/Anchorage',
            -8=> 'GMT-8: America/Los_Angeles',
            -7=> 'GMT-7: America/Denver',
            -6=> 'GMT-6: America/Chicago',
            -5=> 'GMT-5: America/New_York',
            -4=> 'GMT-4: America/Caracas',
            -3=> 'GMT-3: America/Sao_Paulo',
            -2=> 'GMT-2: Atlantic/South_Georgia',
            -1=> 'GMT-1: Atlantic/Azores',
            0=> 'GMT+0: UTC',
            1=> 'GMT+1: Europe/Paris',
            2=> 'GMT+2: Europe/Athens',
            3=> 'GMT+3: Europe/Moscow',
            4=> 'GMT+4: Asia/Dubai',
            5=> 'GMT+5: Asia/Karachi',
            6=> 'GMT+6: Asia/Dhaka',
            7=> 'GMT+7: Asia/Ho_Chi_Minh,Bangkok',
            8=> 'GMT+8: Asia/Shanghai',
            9=> 'GMT+9: Asia/Tokyo',
            10 => 'GMT+10: Australia/Sydney',
            11 => 'GMT+11: Pacific/Noumea',
            12 => 'GMT+12: Pacific/Auckland',
        ];
        return $main_timezones;

    }

    //...

    public function executeBeforeIndex($params = null)
    {
        try {
            // Tìm tất cả user ID và tạo MonitorSetting cho những user chưa có
            $existingUserIds = MonitorSetting::pluck('user_id')->toArray();

            User::whereNotIn('id', $existingUserIds)
                ->chunk(100, function ($users) {
                    $settingsToInsert = [];
                    foreach ($users as $user) {
                        $settingsToInsert[] = [
                            'user_id' => $user->id,
                        ];
                    }

                    if (!empty($settingsToInsert)) {
                        MonitorSetting::insert($settingsToInsert);
                    }
                });

            nowyh();


        } catch (\Exception $e) {
            loi2('Error in executeBeforeIndex: ' . $e->getMessage());
        }
    }


}
