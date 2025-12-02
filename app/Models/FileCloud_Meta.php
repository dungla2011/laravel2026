<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

class FileCloud_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/admin-file-cloud';

    protected static $web_url_admin = '/admin/file-cloud';
    //protected static $index_view_member = "member.file.index";
    //protected static $index_view_admin = "admin.file.index";

    public static $modelClass = FileCloud::class;

    public static $disableAddItem = 1;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {

    }

    //Hình như chưa dùng hàm này, mà chỉ dựa vào /member , api/member để xly quyền UID
    public function setBelongUserId()
    {
        return 1;
    }
}
