<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemoFolderTbl extends ModelGlxBase
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
}
