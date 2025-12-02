<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class EventUserGroup_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/event-user-group';

    protected static $web_url_admin = '/admin/event-user-group';

    protected static $api_url_member = '/api/member-event-user-group';

    protected static $web_url_member = '/member/event-user-group';

    public static $titleMeta = 'Quản lý Nhóm thành viên';

    public static $folderParentClass = EventUserGroup::class;

    public static $modelClass = EventUserGroup::class;

    public static $allowAdminShowTree = 1;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();

        //Riêng Data type của Field, Lấy ra các field datatype mặc định
        //Nếu có thay đổi sẽ SET bên dưới
        $objSetDefault = new MetaOfTableInDb();
        $objSetDefault->setDefaultMetaTypeField($field);

        $objMeta->dataType = $objSetDefault->dataType;
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //EventUserGroup edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if ($field == 'parent_id') {
            $objMeta->join_api = '/api/event-user-group';
        }


        return $objMeta;
    }

    //...
}
