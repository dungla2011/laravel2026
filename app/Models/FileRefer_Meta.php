<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class FileRefer_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/file-refer';

    protected static $web_url_admin = '/admin/file-refer';

    protected static $api_url_member = '/api/member-file-refer';

    protected static $web_url_member = '/member/file-refer';

    //public static $folderParentClass = FileReferFolderTbl::class;
    public static $modelClass = FileRefer::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //FileRefer edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if ($field == 'parent_extra' || $field == 'parent_all') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
            //            $objMeta->join_func = 'App\Models\FileReferFolderTbl::joinFuncPathNameFullTree';
        }

        if ($field == 'parent_id') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
            //            $objMeta->join_func = 'App\Models\FileReferFolderTbl::joinFuncPathNameFullTree';
        }

        if ($field == 'make_torrent') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
            //            $objMeta->join_func = 'App\Models\FileReferFolderTbl::joinFuncPathNameFullTree';
        }

        //Nếu không set thì lấy của parent default nếu có
        if (! $objMeta->dataType) {
            if ($ret = parent::getHardCodeMetaObj($field)) {
                return $ret;
            }
        }

        return $objMeta;
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        $uid = getCurrentUserId();

        if($uid == 3 || isAdminMngGroup()){

            echo "<button type='button' id='logTor' class='btn btn-sm btn-info mb-2'>Torrent Log</button>";

            $ct = @file_get_contents_timeout('http://4share.vn:36869/test01/test_torrent_running.php?status_tor_log=1',3);

            echo "<pre id='logTo1' style='font-size: 60%; display: none'>";
            print_r($ct);
            echo "</pre>";
            ?>
            <?php
        }
    }

    public function _make_torrent($obj, $field)
    {
        return "ABC123";
    }

    public function extraCssInclude()
    {
        ?>

        <style>
            div[data-table-field=count_dl] input.count_dl {
                display: none!important;
            }
            div[data-table-field=count_dl] .join_val, div[data-table-field=count_dl] input.count_dl {
                display: inline-block;
            }
        </style>
<?php
    }

    function _count_dl($obj, $val)
    {
        return " <a style='font-size: smaller; display: block; margin-left: 5px' target='_blank' href='/member/download-log/your-file?seby_s11=$obj->remote_id&seoby_s12=C'>
 ($obj->count_dl)
 Xem chi tiết các lượt </a>";
    }


    function _name($obj, $val)
    {
        $obj->refresh();
        $obj4S = json_decode($obj->refer_obj);
        if($obj4S && isset($obj4S->link1)){

            $idf = $obj4S->id;
            if($obj4S->idlink){
                $idf = $obj4S->idlink;
            }

            $linkDownTor = "";
            //Kieemr tra idf nay co trong file_upload refer chua
            if($fileUp = FileUpload::where("refer", "torent_remote_id.$idf")->first()){
                $linkDownTor = "<a href='/test_cloud_file?fid=$fileUp->id__' target='_blank' style='background-color: green; color: white; display: inline-block; font-size: 80%' class=''>
 <span class='' style='display: inline-block; border: 1px solid #ccc; padding : 1px 3px'>Torrent</span>
  </a> ";
            }

            $dm = UrlHelper1::getDomainHostName();
            $link = "https://$dm/dl-file/$obj4S->link1";
            return "<div style='margin-left: 5px; color: green'> $linkDownTor <a target='_blank' href='$link'> $link </a>   </div> ";
        }

        //Tim torent file neu co:

        return "<div style='margin-left: 5px; color: red; font-size: 70%'>  Check Link Error? </div> ";
    }

    function _filesize($obj,  $val)
    {
        return " <div style='margin-left: 5px; color: green'> " . ByteSize($val) . " </div> ";
    }

    public function _image_list1($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    public function extraJsInclude()
    {
        ?>
        <script>
            $(document).ready(function () {
                $("#logTor").click(function () {
                    $("#logTo1").toggle();
                });
            });
        </script>
        <?php
    }
    //...

}
