<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class MoneyAndTag_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/money-and-tag';

    protected static $web_url_admin = '/admin/money-and-tag';

    protected static $api_url_member = '/api/member-money-and-tag';

    protected static $web_url_member = '/member/money-and-tag';

    //...
}
