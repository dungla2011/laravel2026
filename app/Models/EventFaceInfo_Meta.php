<?php

namespace App\Models;

use App\Components\Helper1;
use App\Http\ControllerApi\EventInfoControllerApi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * ABC123
 * @param null $objData
 */
class EventFaceInfo_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/event-face-info";
    protected static $web_url_admin = "/admin/event-face-info";

    protected static $api_url_member = "/api/member-event-face-info";
    protected static $web_url_member = "/member/event-face-info";

    //public static $folderParentClass = EventFaceInfoFolderTbl::class;
    public static $modelClass = EventFaceInfo::class;

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
            //EventFaceInfo edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\EventFaceInfoFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\EventFaceInfoFolderTbl::joinFuncPathNameFullTree';
        }

        if ($field == 'image_list') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        if ($field == 'face_vector') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        //Nếu không set thì lấy của parent default nếu có
        if(!$objMeta->dataType)
            if($ret = parent::getHardCodeMetaObj($field))
                return $ret;

        return $objMeta;
    }
    public function _image_list($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }


    //...
    public function getExtraDataEditFieldNameX1($field)
    {
//        $html = '';
//        if ($field == 'face_vector') {
//            $html = "<button type='button' id='re_learn' class='btn btn-sm btn-info' title='Nhận dạng ảnh mới up vào ImageList, xong cần ấn nút Save - Ghi lại'> Nhận dạng lại </button>";
//        }
//
//        return $html;
    }

    public function executeBeforeIndex($param = null) {

        return;


        //get all id array only of EventUserInfo
        $arrEventFaceInfo = EventUserInfo::select('id')->get()->pluck('id')->toArray();
//        dump($arrEventFaceInfo);
        foreach ($arrEventFaceInfo as $id) {
            $obj = EventFaceInfo::find($id);
            if (!$obj) {
                $obj = new EventFaceInfo();
                $obj->id = $id;
//                $obj->user_event_id = $id;
                $obj->save();
            }
        }

    }

    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        return $x->leftJoin('event_user_infos', 'user_event_id', '=', 'event_user_infos.id')
            ->addSelect([
                'event_user_infos.email AS _email',
                'event_user_infos.first_name as _first_name',
                'event_user_infos.last_name as _last_name',
            ]);
    }

    function getMapJoinFieldAlias()
    {
        return [
            '_email'=>'event_user_infos.email',
            '_first_name'=>'event_user_infos.first_name',
            '_last_name'=>'event_user_infos.last_name',
        ];
    }

    public function getFullSearchJoinField()
    {
        return [
            'event_user_infos.first_name'  => "like",
            'event_user_infos.last_name'  => "like",
            'event_user_infos.email'   => "like",
            'event_user_infos.phone'   => "like",
        ];
    }

    public function _file_cloud_id($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);

    }
    public function _id111($obj, $valIntOrStringInt, $field)
    {
        $objU = EventUserInfo::find($valIntOrStringInt);
        if(!$objU)
            return "Not found user : $valIntOrStringInt";
        $name = $objU->getFullname();
        $email = $objU->email;

        $_group_name = $objU->_group_name;
        $org = $objU->organization ? "<br>  $objU->organization" : '';
        $designation = $objU->designation ? " <br>  $objU->designation" : '';
        $_group_name = $_group_name ? "<br> Nhóm: $_group_name" : '';
        $uid1 = $objU->id;
        $module = Helper1::getModuleCurrentName();
        $ret = "<div data-code-pos='ppp17121128641' style='font-size: small; padding: 5px; color: royalblue; position: relative'>";
        $ret .= "$name
        <br>
        $objU->phone
        <br>
        $email
 $designation
 $org
 $_group_name
";
        $ret .= '';
        $ret .= '</div>';

        return $ret;
    }

    public function extraJsIncludeEdit($objData = null)
    {

        require_once "/var/www/html/public/tool1/_site/event_mng/js_event.php";
        ?>

        <script>




        </script>

<?php


    }


}
