<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use LadLib\Laravel\Database\TraitModelExtra;

class SkusProductVariantOption extends ModelGlxBase
{
    use HasFactory, TraitModelExtra;

    protected $guarded = [];
}
