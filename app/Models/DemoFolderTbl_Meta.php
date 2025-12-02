<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

class DemoFolderTbl_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/demo-folder';

    protected static $web_url_admin = '/admin/demo-folder';

    public static $folderParentClass = DemoFolderTbl::class;

    public function isStatusField($field)
    {
        $mm = ['status'];
        if (in_array($field, $mm)) {
            return 0;
        }

        return 0;
    }
}
