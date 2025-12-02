<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use LadLib\Laravel\Database\TraitModelExtra;

class GiaPhaUser extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    public static function createQuotaUser($uid)
    {
        $gpUser = GiaPhaUser::where('user_id', $uid)->first();
        if (! $gpUser) {
            $mm = ['user_id' => $uid, 'max_quota_node' => DEF_LRV_DEFAULT_QUOTA_NODE_USER_MYTREE];
            GiaPhaUser::create($mm);
        }
        $gpUser = GiaPhaUser::where('user_id', $uid)->first();

        return $gpUser;
    }

    static function getCountBuyedNode($uid)
    {
        $nBuyed = 0;
        if($billAndPro = \App\Models\OrderItem::where('user_id', $uid)->get()){
            foreach ($billAndPro as $item){
                if($item->param1)
                    $nBuyed += $item->param1;
            }
        }
        return $nBuyed;
    }

    public static function getCurrentQuota($uid){
        return self::checkQuota($uid, "", 1);
    }

    public static function checkQuota($uid, $mess, $returnNumberOnly = 0)
    {
        if(!$uid)
            return 0;
        $nBuyed = self::getCountBuyedNode($uid);
        //        $uid = Auth::id();
        //Kiểm tra quota user tại đây:
        $gpUser = GiaPhaUser::createQuotaUser($uid);
        if (! $gpUser) {
            if($returnNumberOnly)
                return 0;
            return rtJsonApiError("Not found quota for user! $uid ", 500);
        }
        //Tính toán quota của user đã dùng
        $countUsing = GiaPha::where('user_id', $uid)->count();
        $nAllow = $nBuyed + $gpUser->max_quota_node;

        if ($returnNumberOnly) {
            return $nAllow;
        }

        if ($nAllow <= $countUsing) {
            return rtJsonApiError("$mess đã sử dụng quá số thành viên cho phép: $countUsing > $nAllow, Bạn có thể Mua thêm số thành viên!");
        }

        return null;
    }
}
