<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class GiaPhaUser_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/tree-mng-user';

    protected static $web_url_admin = '/admin/tree-mng-user';

    protected static $api_url_member = '/api/member-tree-mng-user';

    protected static $web_url_member = '/member/tree-mng-user';

    public static $titleMeta = "Danh sách thành viên cây";

    //...
    public function getNeedIndexFieldDb()
    {
        return ['user_id'];
    }

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {

        $objMeta = new MetaOfTableInDb();

        if ($field == 'user_id') {
            $objMeta->join_api_field = 'email';
            //            $objMeta->join_func = 'joinUserEmailUserId';
            $objMeta->join_api = '/api/user/search';
        }

        return $objMeta;
    }

    /**
     * @return array
     */
    public function getMapFieldAndClass(): array
    {
        return [
            'user_id' => User::class,
        ];
    }

    public function _user_id($obj, $val)
    {
        if ($obj1 = User::find($val)) {
            return [$val => $obj1->email];
        }

        return null;
    }
}
