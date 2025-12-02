<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

class MenuTree_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/menu-tree';

    protected static $web_url_admin = '/admin/menu-tree';

    protected static $api_url_member = '/api/member-menu-tree';

    protected static $web_url_member = '/member/menu-tree';



    public static $folderParentClass = MenuTree::class;

    public static $modelClass = MenuTree::class;
}
