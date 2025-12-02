<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class AffiliateLog extends ModelGlxBase
{
    use HasFactory, TraitModelExtra, SnowflakeId;
    protected $guarded = [];


    static function checkAffCode($cuid, $affCode = null){

        $affCodeCC = '';
        if(!$affCode)
            //Lay tu cookie
            $affCodeCC = $affCode = $_COOKIE['aff_code'] ?? null;
        if(!$affCode)
            return;

        try{
            $saler = null;
            if(isUUidStr($affCode))
                $saler = User::where("ide__", $affCode)->first();
            elseif(is_numeric($affCode))
                $saler = User::find($affCode);
            if($saler){

                //Nếu ko có UID, thì affcode vào cookie, de sau khi login, sẽ lưu vào db
                if(!$cuid)
                    setcookie("aff_code", $affCode, time() + 3600*24*360, "/");
                //Neu user da login
                if($cuid && $cuid != $saler->id){
                    $idCu = User::find($cuid)?->ide__ ?? '';
                    if(!$idCu)
                        $idCu = $cuid;
                    $newAf = 1;
                    if($affLog = AffiliateLog::where("visitor_id", $idCu)->latest()->first()){
                        //nếu trong vòng 1 ngay thi ko lam gi
                        if($affLog->created_at->diffInDays() <= 1){
                            $newAf = 0;
                        }
                    }
                    //neu chua co hoac qua 1 ngay, thi insert
                    if($newAf){
                        $affLog = new AffiliateLog();
                        $affLog->visitor_id = $idCu;
                        $affLog->user_id = $saler->id;
                        $affLog->save();
                    }
                    //Delete cookie
                    setcookie("aff_code", "", time() - 3600, "/");
                }
            }

        }
        catch (\Exception $e){
            $siteId = SiteMng::getSiteId();
//            echo $e->getMessage();
            dumperrorglobal("SID = $siteId, ERROR checkAffCode ". $e->getMessage());
            return;
        }
    }

}
