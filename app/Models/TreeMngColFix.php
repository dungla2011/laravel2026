<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use LadLib\Laravel\Database\TraitModelExtra;

class TreeMngColFix extends ModelGlxBase
{
    use HasFactory, TraitModelExtra;

    protected $guarded = [];
}
