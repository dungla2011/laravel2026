<?php

namespace App\Models;

use App\Components\Helper1;
use App\Http\Middleware\Authenticate;
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
class AffiliateLog_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/affiliate-log";
    protected static $web_url_admin = "/admin/affiliate-log";

    protected static $api_url_member = "/api/member-affiliate-log";
    protected static $web_url_member = "/member/affiliate-log";

    //public static $folderParentClass = AffiliateLogFolderTbl::class;
    public static $modelClass = AffiliateLog::class;

    public static $titleMeta = "Affiliate - Tiếp thị liên kết";

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
            //AffiliateLog edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\AffiliateLogFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\AffiliateLogFolderTbl::joinFuncPathNameFullTree';
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

    //...

    function _visitor_id($obj, $val, $field)
    {
        return User::find($val)?->email;
    }
    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {

        ?>

<div class='row m-2 p-2 px-2' style="background-color: snow">

    <div class="">Mã Affiliate của bạn:

    <?php
    $code = $uid = getCurrentUserId();
    $user = User::find($uid);
    if($user->ide__)
        $code = $user->ide__;

    $domain = UrlHelper1::getDomainHostName();
    echo "<a href='https://$domain/aff-link/$code' target='_blank'> https://$domain/aff-link/$code </a>
<button class='btn btn-sm btn-primary ml-1' onclick='copy_link()' title='copy link affiliate' style=''> COPY </button>
";

    ?>
        <br>
        <textarea readonly class="mt-1" name="" id="" cols="30" rows="4"
                  style="width: 100%; font-size: smaller; padding: 5px; border: 1px solid #ccc; color: green">
Bạn có thể thêm tham số kênh (achannel) để theo dõi nguồn traffic, ví dụ:
https://<?php echo $domain?>/aff-link/<?php echo $code?>?achannel=facebook2025
https://<?php echo $domain?>/aff-link/<?php echo $code?>?achannel=hdvn
Tên kênh chỉ gồm chữ cái, số, dấu gạch dưới, gạch ngang, không chứa khoảng trắng, ký tự đặc biệt
        </textarea>


        <script>
            function copy_link(){
                var copyText = document.createElement("input");
                copyText.value = "https://<?php echo $domain?>/aff-link/<?php echo $code?>";
                document.body.appendChild(copyText);
                copyText.select();
                document.execCommand("copy");
                document.body.removeChild(copyText);
                showToastWarningTop("Đã copy link: " + copyText.value);
            }
        </script>
    </div>

</div>


<?php
    }




}
