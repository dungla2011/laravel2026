<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class MyDocumentCat_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/my-document-cat';

    protected static $web_url_admin = '/admin/my-document-cat';

    protected static $api_url_member = '/api/member-my-document-cat';

    protected static $web_url_member = '/member/my-document-cat';

    public static $folderParentClass = MyDocumentCat::class;

    public static $modelClass = MyDocumentCat::class;

    public static $allowAdminShowTree = 1;

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
            //MyDocumentCat edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    //...
}
