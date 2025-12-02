<?php

namespace App\Models;

use App\Components\Helper1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\Database\MetaOfTableInDb;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * ABC123
 * @param null $objData
 */
class PlanDefineValue_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/plan-define-value";
    protected static $web_url_admin = "/admin/plan-define-value";

    protected static $api_url_member = "/api/member-plan-define-value";
    protected static $web_url_member = "/member/plan-define-value";

    //public static $folderParentClass = PlanDefineValueFolderTbl::class;
    public static $modelClass = PlanDefineValue::class;

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
            //PlanDefineValue edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\PlanDefineValueFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\PlanDefineValueFolderTbl::joinFuncPathNameFullTree';
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

    function _plan_id($obj, $val, $field)
    {
        //Lấy tên của PlanName
        $planName = PlanName::query()
            ->where('id', $val)
            ->value('name');
        return $planName ? $planName : '';
    }

    function _plan_define_id($obj, $val, $field)
    {
        //Lấy tên của PlanDefine
        $planDefineName = PlanDefine::query()
            ->where('id', $val)
            ->value('name');
        return $planDefineName ? $planDefineName : '';
    }

    static function excuteInsertPlanDefineValue($planId = null)
    {
        $obj = new PlanDefineValue_Meta();
        $obj->executeBeforeIndex($planId);
    }

    //...

    public function executeBeforeIndex($param = null) {
        //Lấy tất cả các PlanId

        if(is_numeric($param))
            $planIds = [$param];
        else
            $planIds = PlanName::query()
                ->select('id')
                ->distinct()
                ->pluck('id')
                ->toArray();
        //Lấy tất cả PlanDefine Id
//        $planDefineIds = PlanDefine::query()
//            ->select('id')
//            ->distinct()
//            ->pluck('id')
//            ->toArray();
        $plan_field_names = ['input_gia_ban_du_kien', 'input_luong_ban_du_kien_thang'];

//        dump("PlanDefineValue_Meta executeBeforeIndex: planIds = " . implode(',', $planIds) .
//            ", planDefineIds = " . implode(',', $planDefineIds));
        //Insert bào bảng PlanDefineValue nếu chưa có cặp plan_id , plan_define_id trong bảng
        foreach ($planIds as $planId) {
            foreach ($plan_field_names as $fname) {
                $exists = PlanDefineValue::query()
                    ->where('plan_id', $planId)
                    ->where('plan_field_name', $fname)
                    ->exists();
                if (!$exists) {
                    PlanDefineValue::create([
                        'plan_id' => $planId,
                        'plan_field_name' => $fname,
                    ]);
                }
            }
        }

    }


}
