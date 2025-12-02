<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class Todo2_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/todo2';

    protected static $web_url_admin = '/admin/todo2';

    protected static $api_url_member = '/api/member-todo2';

    protected static $web_url_member = '/member/todo2';

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        //        $objMeta = new MetaOfTableInDb();
        //        return $objMeta;
    }
}
