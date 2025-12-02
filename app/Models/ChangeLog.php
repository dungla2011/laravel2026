<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class ChangeLog extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    public static function addLog1($userid, $text, $ip = null)
    {
        if (! $ip) {
            $ip = @$_SERVER['REMOTE_ADDR'];
        }
        $obj = new ChangeLog();
        $obj->change_log = $text;
        $obj->ip_address = $ip;
        $obj->user_id = $userid;
        $obj->save();
    }
}
