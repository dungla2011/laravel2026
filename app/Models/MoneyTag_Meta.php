<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class MoneyTag_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/money-tag';

    protected static $web_url_admin = '/admin/money-tag';

    protected static $api_url_member = '/api/member-money-tag';

    protected static $web_url_member = '/member/money-tag';

    //...
}
