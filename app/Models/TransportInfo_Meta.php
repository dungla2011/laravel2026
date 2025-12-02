<?php

namespace App\Models;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class TransportInfo_Meta extends OrderInfo_Meta
{
    protected static $api_url_admin = '/api/transport-info';

    protected static $web_url_admin = '/admin/transport-info';

    protected static $api_url_member = '/api/member-transport-info';

    protected static $web_url_member = '/member/transport-info';

    //    public static $folderParentClass = TransportInfoFolderTbl::class;
    public static $modelClass = TransportInfo::class;
}
