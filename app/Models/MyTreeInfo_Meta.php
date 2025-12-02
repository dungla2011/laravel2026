<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class MyTreeInfo_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/my-tree-info';

    protected static $web_url_admin = '/admin/my-tree-info';

    protected static $api_url_member = '/api/member-my-tree-info';

    protected static $web_url_member = '/member/my-tree-info';

    public static $titleMeta = "Thông tin cây";

    public function getNeedIndexFieldDb()
    {
        return ['user_id', 'tree_id', 'deleted_at', 'created_at', 'id__'];
    }

    public function isUseRandId()
    {
        return 0;
    }

    //...
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'image_list') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        return $objMeta;
    }

    public function _image_list($obj, $val, $field)
    {
//        return Helper1::imageShow1($obj, $val, $field);
    }
}
