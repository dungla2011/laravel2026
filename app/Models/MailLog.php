<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class MailLog extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;
    protected $guarded = [];

    static function addMail($toEmail, $title, $content, $param)
    {
        //Neu co roi khong insert
        if(MailLog::where('to_email', $toEmail)->where('param', $param)->first()){
            return 0;
        }

        $obj = new MailLog();
        $obj->to_email = $toEmail;
        $obj->mail_title = $title;
        $obj->mail_content = $content;
        $obj->param  = $param ;
        $obj->log  = $param ;
        $obj->save();
        return 1;

    }

}
