<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class MoneyAndTag extends ModelGlxBase
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

    //    /**
    //     * @return \Illuminate\Database\Eloquent\Relations\HasMany
    //     */
    //    public function sub1(){
    //        return $this->hasMany(MoneyAndTagSub1::class , 'money-and-tag_id');
    //    }
    //

    //    /**
    //     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    //     */
    //    public function joinTags(){
    //        return $this->belongsToMany(TagDemo::class , 'demo_and_tag_tbls', 'demo_id', 'tag_id')->withTimestamps();
    //    }
}
