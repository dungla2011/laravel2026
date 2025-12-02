<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class MoneyTag extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    // Các hàm Set, Get riêng của Model laravel
    //    public function setNumber1Attribute($number1){
    //        $this->attributes['number1'] = $number1 * 2;
    //    }
    //    public function getNumber1Attribute(){
    //        return 111;
    //    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sub1()
    {
        return $this->hasMany(MoneyTagSub1::class, 'money-tag_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function joinTags()
    {
        return $this->belongsToMany(TagMoneyTag::class, 'money-tag_and_tag_tbls', 'money-tag_id', 'tag_id')->withTimestamps();
    }
}
