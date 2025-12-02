<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

class DemoAndTagTbl_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/demo123';

    protected static $web_url_admin = '/admin/demo-and-tag';

    function _tag_id($obj, $val)
    {
        if($obj = Tag::find($val)){
            return $obj->name;
        }
    }

    function _demo_id($obj, $val)
    {
        if($obj = DemoTbl::find($val)){
            return $obj->name;
        }
    }
}
