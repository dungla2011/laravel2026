<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\UrlHelper1;
use LadLib\Laravel\Database\TraitModelExtra;

class LogUser extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    public static function FInsertLog($action = null, $url = '', $comment = '', $uid = null, $time = null, $ip = null)
    {
        if (! $url) {
            $url = UrlHelper1::getUrlRequestUri();
        }
        $obj = new LogUser();
        $obj->url = $url;
        if (! $ip) {
            $obj->ip = @$_SERVER['REMOTE_ADDR'];
        } else {
            $obj->ip = $ip;
        }

        $obj->user_id = getCurrentUserId();
        $obj->comment = $comment;

        if ($uidAdm = isSupperAdminDevCookie()) {
            $obj->admin_uid = $uidAdm;
        }

        $obj->save();
    }
}
