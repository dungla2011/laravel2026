<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use LadLib\Laravel\Database\TraitModelExtra;

class NewsFolder extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    public function getLinkPublic()
    {
        return '/tin-tuc/s/'.Str::slug($this->name).".$this->id.html";
    }

    public function getMetaDesc()
    {
        return strip_tags($this->meta_desc ?? $this->name);
    }
}
