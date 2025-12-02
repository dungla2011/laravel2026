<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

class demoMg_Meta extends MetaOfTableInDb
{
    public $table_name_model = '_demo_mg';

    protected static $api_url_admin = '/api/demo-mg';

    protected static $web_url_admin = '/admin/demo-mg';

    protected static $api_url_member = '/api/member-demo-mg';

    protected static $web_url_member = '/member/demo-mg';

    //    public static $folderParentClass = demoMg::class;

    public static $modelClass = demoMg::class;
}
