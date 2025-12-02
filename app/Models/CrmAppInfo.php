<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class CrmAppInfo extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;
    protected $guarded = [];

    static function insertOrUpdateFBTokenAndReadyStatus($firebase_token, $ready)
    {
        if(!$firebase_token)
            return;
        $obj = self::where('firebase_token', $firebase_token)->first();
        if (!$obj) {
            $obj = new self();
            $obj->firebase_token = $firebase_token;
        }
        if($ready != -1)
            $obj->ready = $ready;

        $obj->save();
        return $obj;

    }

}
