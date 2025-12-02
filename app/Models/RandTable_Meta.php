<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

class RandTable_Meta extends MetaOfTableInDb
{
    public function getNeedIndexFieldDb()
    {
        return ['rand'];
    }
}
