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
class DepartmentEvent_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/department-event";
    protected static $web_url_admin = "/admin/department-event";

    protected static $api_url_member = "/api/member-department-event";
    protected static $web_url_member = "/member/department-event";

    //public static $folderParentClass = DepartmentEventFolderTbl::class;
    public static $modelClass = DepartmentEvent::class;

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
            //DepartmentEvent edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\DepartmentEventFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\DepartmentEventFolderTbl::joinFuncPathNameFullTree';
        }
        if($field == 'department_id'){
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

    function _department_id($obj, $val)
    {

        //Lấy ra all department, và đưa ra  mảng tất cả id=>name
        $departments = Department::all();
        $departmentArr = $departments->pluck('name', 'id')->toArray();
        //Đưa  0 -> chọn vào đầu mảng
        $departmentArr = [0 => '-- Chọn Đơn vị -- '] + $departmentArr;
        return $departmentArr;
    }

    public function getFullSearchJoinField()
    {
        return [
            'event_infos.name'  => "like",
        ];
    }
    //...
    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        return $x->leftJoin('event_infos', 'event_id', '=', 'event_infos.id')
            ->addSelect([
                'event_infos.name AS _name',
            ]);
    }
    function _name($obj, $val)
    {
        return " <a href='/admin/event-info/edit/$obj->event_id'> $val </a> ";


    }
    public function executeBeforeIndex($param = null)
    {
        //Tìm tất cả các event trong EventInfo, chưa có trong bảng DepartmentEvent và insert các event_id vào bảng này
        $eventInfo = EventInfo::all();
        foreach ($eventInfo as $event){
            $departmentEvent = DepartmentEvent::where('event_id', $event->id)->first();
            if(!$departmentEvent){
                $departmentEvent = new DepartmentEvent();
                $departmentEvent->event_id = $event->id;
                $departmentEvent->save();
            }
        }


    }



}
