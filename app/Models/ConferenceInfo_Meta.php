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
class ConferenceInfo_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/conference-info";
    protected static $web_url_admin = "/admin/conference-info";

    protected static $api_url_member = "/api/member-conference-info";
    protected static $web_url_member = "/member/conference-info";

    //public static $folderParentClass = ConferenceInfoFolderTbl::class;
    public static $modelClass = ConferenceInfo::class;

    public static function getIdReadOnlyIfNotSupperAdmin()
    {
        return 1;
    }

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

        if($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }

        if($field == 'cat'){
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        if($field == 'tag_list_id'){
            $objMeta->join_api_field = 'name';
//          $objMeta->join_func = 'joinTags';
            //ConferenceInfo edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\ConferenceInfoFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\ConferenceInfoFolderTbl::joinFuncPathNameFullTree';
        }
        if($field == 'images'){
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        if($field == 'conf1_images' ||$field == 'conf2_images' ||$field == 'conf3_images' || $field == 'supporters'){
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
//            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\ConferenceInfoFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'right_column' || $field == 'key_notes' || $field == 'conf2_keynote' ||$field == 'conf3_keynote' ){
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
//            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\ConferenceInfoFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'video_bottom' || $field == 'conf1_video' ||$field == 'conf2_video' ||$field == 'conf3_video' ){
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
//            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\ConferenceInfoFolderTbl::joinFuncPathNameFullTree';
        }


        if($field == 'conf1_timesheet' ||$field == 'conf2_timesheet' ||$field == 'conf3_timesheet' ){
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
//            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\ConferenceInfoFolderTbl::joinFuncPathNameFullTree';
        }
        if($field == 'summary'){
            $objMeta->dataType = DEF_DATA_TYPE_RICH_TEXT;
        }

        //Nếu không set thì lấy của parent default nếu có
        if(!$objMeta->dataType)
            if($ret = parent::getHardCodeMetaObj($field))
                return $ret;

        return $objMeta;
    }

    function _cat($obj = null, $val = null, $field = null){
        $mm = [
            0=>'----',
//            1=>'HTBD',
//        2=>"OPEN DIALOGUE"
        ];

        $m1 = ConferenceCat::where("status", '>', 0)->get();
//        dump($m1);


        foreach ($m1 AS $cat){
            $mm[$cat->id] = $cat->name;
        }

        return $mm;
    }

    function _image_list1($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }
    function _images($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }

    function _supporters($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }
    //...

    function _conf1_images($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }

    function _conf2_images($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }
    function _conf3_images($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }

    public function extraHtmlIncludeEditButtonZone1($obj = null)
    {

        if(!$obj || !$obj->id)
            return;
        ?>
        <a style="border-color: red" href="https://nghiencuubiendong.vn/events/?id=<?php echo $obj->id ?>"
           target="_blank" title="Xem public" data-code-pos="ppp1628964565" class="btn btn-outline-info  m-2">
            <i class="fa fa-eye" style="color: red"></i>
        </a>
        <?php

    }

    public function extraJsIncludeEdit($objData = null)
    {
        ?>

        <script>

        </script>

        <?php
    }

    public function extraCssIncludeEdit($v1 = null, $v2 = null, $v3 = null)
    {
        ?>

        <style>
            #edit_text_area_conf1_timesheet,#edit_text_area_conf2_timesheet,#edit_text_area_conf3_timesheet, #edit_text_area_right_column {
                height: 400px;
                font-weight: bold;
                color: black
            }
        </style>

        <?php
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        ?>


        <?php

    }

}
