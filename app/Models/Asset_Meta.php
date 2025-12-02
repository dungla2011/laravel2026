<?php

namespace App\Models;

use App\Components\Helper1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\Database\MetaOfTableInDb;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * ABC123
 * @param null $objData
 */
class Asset_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/assets";
    protected static $web_url_admin = "/admin/assets";

    protected static $api_url_member = "/api/member-assets";
    protected static $web_url_member = "/member/assets";

    public static $folderParentClass = AssetCategory::class;
    public static $modelClass = Asset::class;

    public static $allowAdminShowTree = true;


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
            //Assets edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\AssetsFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'description'){
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        if($field == 'image_list'){
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = AssetCategory_Meta::$api_url_admin;
//            $objMeta->join_func = 'App\Models\AssetsFolderTbl::joinFuncPathNameFullTree';
        }

        //Nếu không set thì lấy của parent default nếu có
        if(!$objMeta->dataType)
            if($ret = parent::getHardCodeMetaObj($field))
                return $ret;

        return $objMeta;
    }
    function _image_list($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }

    function _barcode($obj, $val)
    {
        $code = "glx-$obj->id";
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_93);
        $barcodeBase64 = base64_encode($barcode);

        $dataQrImg = base64_encode(QrCode::size(80)->margin(1)->format('png')->encoding('UTF-8')->generate($code));

        return '<img src="data:image/png;base64,' . $dataQrImg . '" />';

    }

    public function extraCssInclude()
    {
    ?>

        <style>
            input.barcode {
                display: none;
            }

        </style>

<?php

    }


    //...


}
