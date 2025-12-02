<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use LadLib\Laravel\Database\TraitModelExtra;

class MediaActor extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;
    protected $guarded = [];

    function getLink1()
    {

        return "/movie/actor/".Str::slug($this->name).".".$this->id;
    }
}
