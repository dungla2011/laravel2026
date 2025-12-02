<?php

namespace App\Models;

use App\Components\Helper1;
use App\Support\HTMLPurifierSupport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\cstring2;
use LadLib\Common\Database\MetaOfTableInDb;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * ABC123
 * @param null $objData
 */
class TmpDownloadSession_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/download-session";
    protected static $web_url_admin = "/admin/download-session";

    protected static $api_url_member = "/api/member-download-session";
    protected static $web_url_member = "/member/download-session";

    public static $titleMeta = "Lịch sử tải file";

    //public static $folderParentClass = TmpDownloadSessionFolderTbl::class;
    public static $modelClass = TmpDownloadSession::class;

    public function isUseRandId()
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

        if($field == 'status'){
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if($field == 'tag_list_id'){
            $objMeta->join_api_field = 'name';
//          $objMeta->join_func = 'joinTags';
            //TmpDownloadSession edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\TmpDownloadSessionFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\TmpDownloadSessionFolderTbl::joinFuncPathNameFullTree';
        }


        return $objMeta;
    }
    function _image_list1($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }

    function _token($obj, $val, $field){
        $val = HTS($val);
        return "<div class='ml-2' style='font-size: 80%'>  $val </div>";
    }


    function _done_bytes($obj, $val)
    {
        $bs = \ByteSize($val);
        return "<div class='ml-2' style='font-size: 80%'>  $bs </div>";
    }

    public function extraCssIncludeEdit()
    {

        $this->extraCssInclude();

    }

    public function extraCssInclude()
    {
    ?>

        <style>
            .input_value_to_post.file_size, .input_value_to_post.done_bytes {
                display: none;
            }
        </style>
    <?php

     }

     function _user_id($obj, $val, $field)
     {
         $em = '';
         if($user = User::find($val)){
             $em = $user->email;
         }
         return "<div class='ml-2' style='font-size: 80%'>  $em</div>";
     }

    function _user_id_file($obj, $val, $field)
    {
        return $this->_user_id($obj, $val, $field);
     }


    function _file_size($obj, $val, $field){
        $bs = \ByteSize($val);
        return "<div class='ml-2' style='font-size: 80%'>  $bs </div>";
    }

    function _ide__($obj)
    {
        if(!$obj || !($obj->fid ?? '')){
            return " NOT FOUND OBJ";
        }

        if($obj1 = FileUpload::find($obj->fid)){
            $obj1->name = HTMLPurifierSupport::clean($obj1->name);
            $name = cstring2::substr_fit_char_unicode($obj1->name, 0,50,1);
            return "<div class='ml-2' style='font-size: 80%'> <a href='/f/$obj1->link1' title='$obj1->name' target='_blank'> $name </a>  </div>";
        }
        if($obj1 = FileUpload::withTrashed()->find($obj->fid)){
            $obj1->name = HTMLPurifierSupport::clean($obj1->name);
            $name = cstring2::substr_fit_char_unicode($obj1->name, 0,50,1);
            return "<div class='ml-2' style='font-size: 80%'> <a href='/f/$obj1->link1' title='$obj1->name' target='_blank'> $name </a>  </div>";
        }


    }

    //...

    public function executeBeforeIndex($param = null)
    {
        //Set all ide__ if null
        $mm = self::$modelClass::whereNull('ide__')
        ->get();
        foreach ($mm AS $obj){
            if(!$obj->ide__){
                $obj->ide__ = getUUidGlx();
                $obj->addLog("Set ide__");
                $obj->save();
            }
        }
    }


}
