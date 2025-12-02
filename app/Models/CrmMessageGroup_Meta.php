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
class CrmMessageGroup_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/crm-message-group";
    protected static $web_url_admin = "/admin/crm-message-group";

    protected static $api_url_member = "/api/member-crm-message-group";
    protected static $web_url_member = "/member/crm-message-group";

    //public static $folderParentClass = CrmMessageGroupFolderTbl::class;
    public static $modelClass = CrmMessageGroup::class;

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
            //CrmMessageGroup edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\CrmMessageGroupFolderTbl::joinFuncPathNameFullTree';
        }


        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\CrmMessageGroupFolderTbl::joinFuncPathNameFullTree';
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

    public function _gid($obj, $val, $field)
    {
        return "<button type='button' class='m-2 get_group_info mt-2 btn btn-sm btn-info' data-value='$val'> Lấy Thông tin Nhóm</button> ";
    }

    //...
    public function extraJsInclude()
    {
        ?>
        <script>

            $('.get_group_info').on('click', function() {
                console.log("Button clicked!");
                // Gọi API để lấy thông tin nhóm
                let gid = this.getAttribute('data-value');
                let link = '/train/zalo/test-zalo-n8n/get-group-info.php?gid=' + gid;
                fetch(link)
                    .then(response => response.text())
                    .then(data => {
                        // Xử lý dữ liệu trả về từ API

                        console.log("link:", link);
                        console.log(" Dữ liệu trả về từ API:", data);

                        showToastInfoTop('Thông tin nhóm đã được lấy thành công! Xem console để biết chi tiết.' + data);
                    })
                    .catch(error => {
                        console.error('Lỗi khi lấy thông tin nhóm:', error);
                        alert('Đã xảy ra lỗi khi lấy thông tin nhóm.');
                    });
            });

        </script>

<?php
    }

    public function executeBeforeIndex($param = null)
    {
        //CrmMessage Lấy ra 100 hàng cuối cùng có type = 1, distinc thread_id

        // Lấy ra các thread_id duy nhất từ 100 row cuối cùng có type = 1
        $distinctThreadIds = CrmMessage::where('type', 1)
//            ->where("channel_name" , 'anh_taxi')
            ->orderBy('id', 'desc')
            ->limit(2000)
            ->pluck('thread_id')
            ->unique()
            ->values()
            ->toArray();
//        dump($distinctThreadIds);

        // Insert tất cả thread_id vào CrmMessageGroup với gid = thread_id
        foreach ($distinctThreadIds as $threadId) {
            CrmMessageGroup::updateOrCreate(
                ['gid' => $threadId], // Điều kiện tìm kiếm
                [
                    'gid' => $threadId,
                    // 'name' => 'Thread ' . $threadId, // Có thể tùy chỉnh tên
                    // 'created_at' => now(),
                    // 'updated_at' => now()
                ]
            );
        }

//        dump($distinctThreadIds);

        // Trả về mảng thread_id
        return $distinctThreadIds;
    }



}
