<?php

namespace App\Models;

use LadLib\Common\Database\mongoDb;

class demoMg extends mongoDb
{
    public static $_tableName = '_demo_mg';

    public $_id;

    public $name;

    public $title;

    public $f1;

    public $f2;

    public $f3;

    public $f4;

    public $f5;

    public $user_id;

    public $created_at;

    public $deleted_at;
}
