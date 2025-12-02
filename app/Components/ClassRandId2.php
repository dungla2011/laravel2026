<?php

namespace App\Components;

use App\Models\SiteMng;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class ClassRandId2
{
    public static function getIdFromRand($rand)
    {
        if (is_numeric($rand)) {
            return $rand;
        }

        if(!$rand)
            return $rand;



        if (! preg_match('/^[0-9a-z]+$/i', $rand)) {
            return null;
        }
        if (! $rand) {
            return null;
        }

        if(isDebugIp()){

//            return Redis::get($rand);

        }
        if(SiteMng::isUseOwnMetaTable())
            $ret = DB::table('rand_table')->where('rand', $rand)->first();
        else
            $ret = DB::connection('mysql_for_common')->table('rand_table')->where('rand', $rand)->first();

        if ($ret) {
            return $ret->id;
        }
        loi2(" Not found rand1: $rand");

        return null;
    }

    public static function getRandFromId($id)
    {
        if (! $id || $id < 0) {
            return null;
        }

        //neu dung SnowFlake thi khong can dung rand nua, du co setting
        if(strlen("$id") >=17)
            return $id;

        try {

            if (! is_numeric($id)) {
                if(SiteMng::isUseOwnMetaTable()){
                    if ($ret = DB::table('rand_table')->where('rand', $id)->first()) {
                        return $id;
                    }
                }
                if ($ret = DB::connection('mysql_for_common')->table('rand_table')->where('rand', $id)->first()) {
                    return $id;
                }
            }

            if(isDebugIp()){

    //            return Redis::get($id);

            }
            if(SiteMng::isUseOwnMetaTable()){
                $ret = DB::table('rand_table')->where('id', $id)->first();
            }else
                $ret = DB::connection('mysql_for_common')->table('rand_table')->where('id', $id)->first();
            if ($ret) {
                return $ret->rand;
            }
            loi2(" Not found id (rand): $id");

        }catch (\Exception $e){
            return $id;
        }

        return null;
    }
}
