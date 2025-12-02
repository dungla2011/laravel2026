<?php

namespace App\Models;

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

}
