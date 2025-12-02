<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use LadLib\Laravel\Database\TraitModelExtra;

class News extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    public function getValidateRuleInsert()
    {

        //        if(!isIPDebug())
        //            return;
        //OK: '/^([^`\$<>]+)$/u'; //Chuỗi bất kỳ không chứa `$<>
        $sreg = '/^([^`\$<>]+)$/u';

        return [
            'name' => 'required|regex:'.$sreg.'|max:256',
            'summary' => 'nullable|regex:'.$sreg.'|max:2000',
        ];
    }

    public function getValidateRuleUpdate($id = null)
    {
        return $this->getValidateRuleInsert();
    }

    public function getLinkPublic()
    {
        return '/tin-tuc/'.Str::slug($this->name).".$this->id.html";
    }

    public function getMetaDesc()
    {
        return strip_tags($this->meta_desc ?? $this->name);
    }
}
