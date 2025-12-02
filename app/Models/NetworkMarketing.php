<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\UrlHelper1;
use LadLib\Laravel\Database\TraitModelExtra;

class NetworkMarketing extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    public static function getLinkMarketingNetwork($id, $pad = 'shb')
    {
        if (is_numeric($id)) {
            $id = qqgetRandFromId_($id);
        }

        return 'https://'.UrlHelper1::getDomainHostName()."/network-marketing/$pad/".$id;
    }

    //Tìm xem uidparent có chưa
    public static function getMyParent($uid)
    {
        $uid = qqgetIdFromRand_($uid);
        //Tìm xem có uid này đký chưa
        if ($obj = NetworkMarketing::where('user_id', $uid)->first()) {
            if ($obj->parent_id) {
                if ($pr = NetworkMarketing::where('user_id', $obj->parent_id)->first()) {
                    return $pr;
                }
            }
        }

        return null;
    }

    /**
     * @return NetworkMarketing
     */
    public static function checkGetValidPid($uid)
    {
        $uid = qqgetIdFromRand_($uid);
        //Tìm xem có uid này đký chưa
        if ($obj = NetworkMarketing::where('user_id', $uid)->first()) {
            return $obj;
        }

        return null;
    }

    /**
     * @return NetworkMarketing
     */
    public function isBelongUser($uid)
    {
        $uid = qqgetIdFromRand_($uid);
        //Tìm xem có uid này đký chưa
        if ($obj = NetworkMarketing::where('user_id', $uid)->first()) {
            //nếu có thì xem nó có phải cha của obj này ko
            if ($obj->id == $this->parent_id) {
                return $obj;
            }
        }

        return null;
    }

    /**
     * @return NetworkMarketing
     */
    public static function insertOrGetNetworkObj($uid, $projectId = 0)
    {
        if ($obj = NetworkMarketing::where('user_id', $uid)->first()) {
            return $obj;
        }
        $obj = new NetworkMarketing();
        $obj->user_id = $uid;
        $obj->project_id = $projectId;
        $obj->save();
        $obj = NetworkMarketing::where('user_id', $uid)->first();

        return $obj;
    }

    public function isMyParent($uid)
    {
        return $this->isBelongUser($uid);
    }

    public function setMyParent($uid)
    {
        return $this->setBelongUser($uid);
    }

    public function setBelongUser($uid)
    {
        $uid = qqgetIdFromRand_($uid);

        $uidCheck = $uid;
        for ($i = 0; $i < 1000; $i++) {
            if ($objCheck = NetworkMarketing::where('user_id', $uidCheck)->first()) {
                $uidCheck = $objCheck->parent_id;
                if ($uidCheck == $this->id) {
                    loi('Có lỗi ra nhập mạng lưới, không thể ra nhập mạng con!');
                }
            }
        }

        if ($obj = NetworkMarketing::where('user_id', $uid)->first()) {
            //nếu có thì xem nó có phải cha của obj này ko
            if ($obj->id == $this->parent_id) {
                return 1;
            }
            $this->parent_id = $obj->id;
            $this->update();
        }
    }

    /**
     * @return NetworkMarketing
     */
    public function getObjParent()
    {
        if ($obj = NetworkMarketing::find($this->parent_id)) {
            return $obj;
        }

        return null;
    }

    /**
     * @return int
     */
    public function getUserIdParent()
    {
        if ($obj = NetworkMarketing::find($this->parent_id)) {
            return $obj->user_id;
        }

        return null;
    }

    public static function insertUpdateUserLinkMarketing($cuid, $puid = null)
    {

        $cuid = qqgetIdFromRand_($cuid);
        $puid = qqgetIdFromRand_($puid);

        $pNetObj = NetworkMarketing::where('user_id', $puid)->first();
        $cNetObj = NetworkMarketing::where('user_id', $cuid)->first();
        //Nếu chưa có CUID thì insert:
        if ($cuid && ! $cNetObj) {
            echo "<br/>\n save1 $cuid, $puid";
            //Nếu chưa có CUID, thì insert, và nếu có PUID thì update vô
            $cNetObj = new NetworkMarketing();
            $cNetObj->user_id = $cuid;
            if ($pNetObj) {
                if ($cNetObj->parent_id != $pNetObj->id) {
                    $cNetObj->parent_id = $pNetObj->id;
                }
            }
            //            echo "<br/>\n save2";
            //            die('xxx');
            $cNetObj->save();
        }

        if ($puid == $cuid) {
            return;
        }

        if ($puid && ! $pNetObj) {
            loi('Bạn không thể liên kết vì User này chưa tham gia vào mạng lưới: '.qqgetRandFromId_($puid));
        }

        //        if($cuid == $puid){
        //            return;
        //        }

        //Tìm PUID nếu có

        //        dump($pNetObj);
        //        echo "<br/>\n save0 $cuid, $puid";
        //Tìm cuid

        //Nếu có $cObj rồi, chưa có pid, thì cập nhật parent
        if ($cNetObj && ! $cNetObj->parent_id && $pNetObj) {
            //Nếu có cUID, chưa có parent, thì update chính PUID nếu có
            //            echo "<br/>\n check update pr1 /$cNetObj->parent_id/";
            if (! $cNetObj->parent_id && $pNetObj && $pNetObj->id != $cNetObj->id) {
                //                echo "<br/>\n update pr now ";

                //Kiểm tra nếu cha link đến con thì bỏ qua vì loop
                if ($pNetObj->parent_id == $cNetObj->id) {

                } else {
                    $cNetObj->parent_id = $pNetObj->id;
                    $cNetObj->update();
                }
            }
        }
    }
}
