<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;

class VpsOsVersion_Meta extends MetaOfTableInDb
{
    public static $folderParentClass = VpsOsVersion::class;

    public static $modelClass = VpsOsVersion::class;
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


}
