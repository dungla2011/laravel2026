<?php

namespace App\Models;

use App\Services\VpsPricingService;
use LadLib\Common\Database\MetaOfTableInDb;

class VpsPlan_Meta extends MetaOfTableInDb
{
//    protected static $api_url_admin = "/api/task-info";
//    protected static $web_url_admin = "/admin/task-info";
//
//    protected static $api_url_member = "/api/member-task-info";
//    protected static $web_url_member = "/member/task-info";


    public static $folderParentClass = VpsPlan::class;

    public static $modelClass = VpsPlan::class;
//GalaxyCloud - Đối soát thanh toán dịch vụ - 30/04/2026
    public function getHardCodeMetaObj($field) {

        $objMeta = new MetaOfTableInDb();
        if($field == 'log' || $field == 'note' || $field == 'comment' ) {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }
        if ($field == 'image_list')
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;

        if($field == 'content' ) {
            $objMeta->dataType = DEF_DATA_TYPE_RICH_TEXT;
        }
        if($field == 'status' || $field == 'is_active' ){
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if($field == 'type'){
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        if(!$objMeta->dataType)
            return null;
        return $objMeta;
    }

    public function setDefaultValue($field)
    {
        if ($field == 'price_config') {
            return json_encode(VpsPricingService::getPricingConfig());
        }
    }

    function _type($obj, $val, $field)
    {
        return $mm = [
            0=> '---',
            'vps_mmo' => "VPS MMO",
            'vps_standard' => "Standard VPS",
            'vps_cloud' => "Cloud VPS",
        ];

    }

}
