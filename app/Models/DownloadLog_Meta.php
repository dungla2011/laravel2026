<?php

namespace App\Models;

use App\Components\Helper1;
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
class DownloadLog_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/download-log";
    protected static $web_url_admin = "/admin/download-log";

    protected static $api_url_member = "/api/member-download-log";
    protected static $web_url_member = "/member/download-log";

    //public static $folderParentClass = DownloadLogFolderTbl::class;
    public static $modelClass = DownloadLog::class;

    public static $titleMeta = "Lịch sử tải file";

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
        if($field == 'status'){
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if($field == 'tag_list_id'){
            $objMeta->join_api_field = 'name';
//          $objMeta->join_func = 'joinTags';
            //DownloadLog edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\DownloadLogFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\DownloadLogFolderTbl::joinFuncPathNameFullTree';
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

    function _filename($obj, $val) {

        if(!$obj->user_id_file){
            if($fileR = FileRefer::find($obj->file_refer_id)){
                $obj->user_id_file = $fileR->user_id;
                $obj->save();
            }
        }
//        $obj->refresh();
        $dm = UrlHelper1::getDomainHostName();
        $link = "https://$dm/dl-file/$obj->file_id_enc";
        return "<div style='margin-left: 5px; color: green'> <a target='_blank' href='$link'> $link </a>  </div> ";
    }
    //...

    function _size($obj,  $val)
    {
        return " <div style='margin-left: 5px; color: green'> " . ByteSize($val) . " </div> ";
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        $uid = getCurrentUserId();
        $mm = \App\Models\DownloadLog::query()
            ->select(["user_id_file", "count_dl", 'user_id', 'price_k', 'size'])
            ->where("user_id_file", $uid)
            ->where('count_dl', '>', 0)->get();
        $cc = 0;
        $tt = count($mm);
        $money = 0;
        $mmPr = [];
        $share = 0;
        $ttSize = 0;
        $tuDownload = 0;
        foreach ($mm AS $obj){

            if(!$obj->price_k){
                $obj->price_k = 1;
            }

            if($obj->user_id == $uid || $obj->user_id == 1){
                $tuDownload++;
                continue;
            }
            $cc++;
            $ttSize += $obj->size;
//            $priceGB = ceil($obj->size / (10 * _GB)) * 0.4;
            if($obj->price_k == 1){
//                $share += 1 * 0.4;
            }

            if(!isset($mmPr[$obj->price_k])){
                $mmPr[$obj->price_k] = 0;
            }
            $mmPr[$obj->price_k]++;
            $money+= $obj->price_k;
//            echo "<br> $cc . ($obj->price_k K) $obj->filename ";
        }

        $back = round( $ttSize / (30* _GB) );

        $str = "";
        $str .= " SizeTT = " . ByteSize($ttSize);
        $str .= " / $back  ";
        $str .= " <br> ";
        $str .= "\n Total:  <b> $cc ($tt) </b>  lượt : <b> $money K </b> = ";
        foreach ($mmPr AS $pr=>$c1){
            $str .=" $pr * $c1 +";
        }

        $str = trim($str, '+');
        $str .= " , <span title='Trừ Lượt tự tải, hoặc admin tải test'> Đã trừ : $tuDownload <i class='fa fa-question-circle'></i> </span>  ";
        echo "<div style='padding: 5px 10px; font-size: smaller; background-color: white; margin-bottom: 5px ;  border: 1px solid #ccc'> $str </div>" ;
    }


}
