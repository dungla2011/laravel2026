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
class CrmMessage_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/crm-message";
    protected static $web_url_admin = "/admin/crm-message";

    protected static $api_url_member = "/api/member-crm-message";
    protected static $web_url_member = "/member/crm-message";

    //public static $folderParentClass = CrmMessageFolderTbl::class;
    public static $modelClass = CrmMessage::class;



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

        if($field == 'status' || $field == 'type' ){
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if($field == 'tag_list_id'){
            $objMeta->join_api_field = 'name';
//          $objMeta->join_func = 'joinTags';
            //CrmMessage edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\CrmMessageFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\CrmMessageFolderTbl::joinFuncPathNameFullTree';
        }
        if($field == 'content'){
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;

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

    function _thread_id($obj, $val, $field){

        $key = $this->getSearchKeyField($field);
        $sname = $this->getSNameFromField($field);
        $l1 = "/admin/crm-message?seoby_$sname=eq&seby_$sname=$val";
//        return $link = "<a class='mx-2' href='$l1'> GET </a>";

        $obj2 = CrmMessage::find($obj->id);

        return $obj2->group_name_zl;
    }

    //...




public function extraJsInclude()
{
    ?>

    <style>
    .input_value_to_post.readonly.content{
        min-width: 301px;
    /*    ...*/
    }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mảng màu sắc để gán
            const colors = [
                '#e74c3c', '#3498db', '#2ecc71', '#f39c12', '#9b59b6',
                '#1abc9c', '#34495e', '#e67e22', '#95a5a6', '#8e44ad'
            ];

            // Tìm tất cả input thread_id
            const threadInputs = document.querySelectorAll('.input_value_to_post.thread_id');

            // Nhóm theo giá trị thread_id
            const threadGroups = {};

            threadInputs.forEach(function(input) {
                const value = input.value.trim();
                if (value) {
                    if (!threadGroups[value]) {
                        threadGroups[value] = [];
                    }
                    threadGroups[value].push(input);
                }
            });

            // Gán màu cho từng nhóm
            let colorIndex = 0;
            Object.keys(threadGroups).forEach(function(threadId) {
                const color = colors[colorIndex % colors.length];

                threadGroups[threadId].forEach(function(input) {
                    input.style.color = color;
                    input.style.fontWeight = 'bold';
                });

                colorIndex++;
            });
        });
    </script>
    <?php
}


}
