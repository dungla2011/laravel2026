<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends ModelGlxBase
{
    use HasFactory;
    use SoftDeletes;

    //    private $name;
    //    private $parent_id;
    protected $fillable = ['name', 'parent_id'];
}
