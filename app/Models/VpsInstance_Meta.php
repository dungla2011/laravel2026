<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

class VpsInstance_Meta extends MetaOfTableInDb
{
    public static $folderParentClass = VpsInstance::class;

    public static $modelClass = VpsInstance::class;
}
