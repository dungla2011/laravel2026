<?php

namespace App\Models;

use App\Components\clsParamRequestEx;
use App\Components\Helper1;
use Illuminate\Support\Facades\Cache;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class GiaPha_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/tree-mng';

    protected static $web_url_admin = '/admin/tree-mng';

    protected static $api_url_member = '/api/member-tree-mng';

    protected static $web_url_member = '/member/tree-mng';

    public static $titleMeta = "Danh sách Thành viên";

    public static $folderParentClass = GiaPha::class;

    //    public static $useRandxxxId = 1;
    //    //...
    public function isUseRandId()
    {
//        if(SiteMng::getSiteId() == 56)
//            return 0;
//        if(SiteMng::getSiteId() == 55)
//            return 0;
        return 0;
    }

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {

        $objMeta = new MetaOfTableInDb();

        //Riêng Data type của Field, Lấy ra các field datatype mặc định
        //Nếu có thay đổi sẽ SET bên dưới
        $objSetDefault = new MetaOfTableInDb();
        $objSetDefault->setDefaultMetaTypeField($field);
        $objMeta->dataType = $objSetDefault->dataType;

        if ($field == 'image_list') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }
        if ($field == 'set_nu_dinh' || $field == 'child_type') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'content') {
            $objMeta->dataType = DEF_DATA_TYPE_RICH_TEXT;
        }
        if ($field == 'parent_id') {
//                        $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/member-tree-mng';
        }

        return $objMeta;
    }


    public function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
             return  $x->leftJoin('gia_phas AS gp1', 'gp1.id', '=', 'gia_phas.parent_id')
//                 ->leftJoin('gia_phas AS gp0', 'gp0.id','=', 'gia_phas.parent_id')
            ->leftJoin('gia_phas AS gp2', 'gp2.id', '=', 'gia_phas.married_with')
             ->leftJoin('gia_phas AS gp3', 'gp3.id', '=', 'gia_phas.child_of_second_married')
            ->addSelect('gp1.name as _ten_bo_me')
                 ->addSelect('gp3.name as _ten_bo_me2')
            ->addSelect('gp2.name as _ten_vo_chong');
    }

    public function getMapFieldAndClass()
    {
        return [
//            'user_id' => User::class,
            'parent_id' => GiaPha::class,
            'married_with' => GiaPha::class,
            'child_of_second_married' => GiaPha::class,
        ];
    }

    public function isDateType($field)
    {
        //        if(in_array($field, ['birthday', 'date_of_death'])){
        //            return 1;
        //        }
        return 0;
    }

    public function getPublicLink($idOrObj, $opt = null)
    {

        if(is_numeric($idOrObj) && strlen($idOrObj) >=17)
            return '/my-tree?pid='.$idOrObj;
        if(is_object($idOrObj) && strlen($idOrObj->id) >=17)
            return '/my-tree?pid='.$idOrObj->id;

        if (is_object($idOrObj)) {
            if(!$idOrObj->married_with && ($idOrObj->id__ ?? '')){
                return '/my-tree?pid='.$idOrObj->id__;
            }
            $id = $idOrObj->getId();
        }

        $isRand = 0;
        if($this->isUseRandId())
            $isRand = 1;

        $id1 = $idOrObj;

        if (! is_numeric($id1)) {
            $id1 = qqgetIdFromRand_($idOrObj);
        }

        if ($obj = GiaPha::find($id1)) {
            if ($obj->married_with) {
                return '/my-tree?pid='. ($isRand ? qqgetRandFromId_($obj->married_with) : $obj->married_with);
            }
        }

        if($isRand && ($obj->id__ ?? ''))
            return '/my-tree?pid='.$obj->id__;

        return '/my-tree?pid='. ($isRand ? qqgetRandFromId_($id1): $id1);
    }

    public function _user_id($obj, $val)
    {
        $user = User::find($val);
        if ($user) {
            $em = substr($user->email, 0, 5);

            $uid = $user->getId();
            return " <div data-code-pos='ppp16824287521371' style='font-size: small; padding: 3px'> <a target='_blank' title='$user->email' href='/admin/user-api/edit/$uid'>$val. $em</a>  </div> ";
        }
    }

    public function getCacheKeyPublic($id)
    {
        return 'index_public_my_tree.'.$id;
    }

    public function getCacheKeyPublicTimeCreated($id)
    {
        return 'index_public_my_tree_time_created_'.$id;
    }

    /**
     * Lấy tree con của 1 obj trong một cây, có tính level từng phần tử cây con
     * (Có thể Dùng để tính doanh thu kinh doanh đa cấp)
     *
     * @return array
     */
    public static function getTreeDeepBelongObjInATree($obj, $lv, &$mAll)
    {
        $lvx = $lv + 1;
        $ret = [];
        foreach ($mAll as $o1) {
            if ($o1->parent_id == $obj->getId()) {
                $o1->level = $lvx;
                $ret[] = $o1;
                $m1 = self::getTreeDeepBelongObjInATree($o1, $lvx, $mAll);
                if ($m1) {
                    foreach ($m1 as $x1) {
                        $ret[] = $x1;
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * Lấy list node để vẽ tree, với link liên kết ko giới hạn
     *
     * @param  clsParamRequestEx  $objParam
     * @param  int  $first
     *
     * @throws \Exception
     */
    public static function getTreeDeep($pid, $objParam, &$mmAll, $pid0 = 0, $first = 1)
    {

        $pidEnc = $pid;

        if (! is_numeric($pid)) {
            $pid = qqgetIdFromRand_($pid);
        }

        if (! $objParent = GiaPha::find($pid)) {
//            die("getTreeDeep Not found data2: $pid");
            return;
        }

        $gp = new \App\Models\GiaPha();
        $pr = ['pid' => $pidEnc, 'get_tree_all' => 1, 'order_by' => 'orders', 'order_type' => 'DESC'];

        \ladDebug::addTime(__FILE__, __LINE__);


        $mm = $gp->queryIndexTree($pr, $objParam);
        $m0 = &$mm[0];

        \ladDebug::addTime(__FILE__, __LINE__);



        foreach ($m0 as &$obj) {

            if ($obj['image_list']) {
                if ($fileimg = \App\Models\FileUpload::find($obj['image_list'])) {
                    if ($fileimg instanceof \App\Models\FileUpload);
//                    $linkCloud = $fileimg->getCloudLinkImage();
                    $linkCloud = $fileimg->getCloudLinkEnc();

                    $obj['_image_list'] = $linkCloud;
                }
            }
            $trung = 0;
            foreach ($mmAll as &$o1) {
                if ($obj['id'] == $o1['id']) {
                    $trung = 1;
                    break;
                }
            }
            \ladDebug::addTime(__FILE__, __LINE__);


            if ($trung) {
                continue;
            }
            if (! $first) {
                if ($objParent->user_id != $objParam->need_set_uid) {
                    $obj['belong_other'] = 1;
                } else {
                    $obj['belong_link'] = 1;
                }
                //VC của obj phải gán vđ PID0 vì pid0 là có link remote này
                if ($obj['id'] == $pidEnc || $obj['married_with'] == $pidEnc) {
                    $obj['parent_id'] = $pid0;
                }
            }

            $mmAll[] = $obj;

            //Nếu là có link remote:
            if ($obj['link_remote']) {


                $pid0 = $obj['id'];
                if ($obj['married_with']) {
                    $pid0 = $obj['married_with'];
                }

                $mParentOfNode = [];
                //Lấy bố mẹ của $pid0;


                $pid0Decode = qqgetIdFromRand_($pid0);

                if ($objCheck = GiaPha::find($pid0Decode)) {
                    if ($objCheck instanceof GiaPha) {
                        $mParentOfNode = $objCheck->getListParentId();
                    }
                }
                \ladDebug::addTime(__FILE__, __LINE__);

                $obj['link_remote'] = trim($obj['link_remote']);
                $obj['link_remote'] = str_replace(' ', '', $obj['link_remote']);
                $mRemoteId = explode(',', $obj['link_remote']);
                //Kiểm tra link remote có bị đưa bố mej vào không
                foreach ($mRemoteId as $pidRemote) {
                    try {
                        $pid1Decode = qqgetIdFromRand_($pidRemote);
                        //Nếu link remote lại là parent của node thì bỏ qua luôn
                        if (in_array($pid1Decode, $mParentOfNode)) {
                            continue;
                        }
                        static::getTreeDeep($pidRemote, $objParam, $mmAll, $pid0, 0);
                    } catch (\Throwable $e) { // For PHP 7
                        continue;
                    } catch (\Exception $exception) {
                        continue;
                    }
                }
            }
        }

//
//        if(isAdminCookie()){
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($mmAll);
//            echo "</pre>";
//            die();
//
//        }
    }

    public function getCacheKeyCountTree($id)
    {
        return 'index_public_my_tree.count_tree.'.$id;
    }

    public function deleteCachePublic($id)
    {
        $key = $this->getCacheKeyPublic($id);
        $keyTime = $this->getCacheKeyPublicTimeCreated($id);
        if (Cache::has($key)) {
            Cache::forget($key);
        }

        //        if(Cache::has($keyTime))
        //            Cache::forget($keyTime);
        //Đưa time vô để parent có thể reload nếu id này trong link_remote
        Cache::put($keyTime, time());
    }







    public function deleteCachePublicTree($id)
    {

        if (! $id) {
            return;
        }
        if (is_array($id)) {
            $mId = $id;
        } else {
            $mId = explode(',', $id);
        }

        if (is_numeric($id)) {
            $this->deleteCachePublic($id);
            $this->deleteCachePublic(qqgetRandFromId_($id));
        }

        foreach ($mId as $id) {
            if (! $id) {
                continue;
            }

            $obj = new GiaPha();
            if (! is_numeric($id)) {
                $id = qqgetIdFromRand_($id);
            }
            if (! $obj = $obj->find($id)) {
                continue;
            }

            $this->deleteCachePublic($obj->married_with);
            $this->deleteCachePublic($obj->getId());
            $this->deleteCachePublic($obj->parent_id);
            $this->deleteCachePublic(qqgetRandFromId_($obj->getId()));
            $this->deleteCachePublic(qqgetRandFromId_($obj->parent_id));

            if ($obj instanceof ModelGlxBase);

            $mm = $obj->getFullPathParentObj(1);

            //
            //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //            print_r($mm);
            //            echo "</pre>";

            if ($mm) {
                foreach ($mm as $pid) {
                    $this->deleteCachePublic($pid);
                    $this->deleteCachePublic(qqgetRandFromId_($pid));
                }
            }
            $this->deleteCachePublic($id);
            $this->deleteCachePublic(qqgetRandFromId_($id));
        }
        //
        //        die('xxx');
    }

    public function _name($obj, $name, $field)
    {
//        return;
        if ($obj instanceof GiaPha);
        $mp = $obj->getListParentId(0, 1);
        if (count($mp)) {
            $mp0 = end($mp);
            $mt = $mp0::getMetaObj();
            if($mt instanceof GiaPha_Meta);

            $link = $mt->getPublicLink($mp0);

            return "<div data-code-pos='ppp17334787841751' style='margin-left: 5px'> <a style='font-size: x-small; color: gray' href='$link' target='_blank'>Gốc: $mp0->name </a> </div>";
        }
    }

    public function _child_of_second_married($obj, $val, $field)
    {
        if(!$val)
            return;
        $link = "/my-tree?pid=" . qqgetRandFromId_($obj->child_of_second_married);
        $name = $obj->_ten_bo_me2;
        return "<div style='margin-left: 5px; font-size: x-small' title='$val'> <a href='$link' target='_blank'>$name  </a> </div>";
    }


    public function _parent_id($obj, $val, $field)
    {

        if(!$val)
            return;

        $link = "/my-tree?pid=" . qqgetRandFromId_($obj->parent_id);
        $name = $obj->_ten_bo_me;

        return "<div style='margin-left: 5px; font-size: x-small' title='$val'> <a href='$link' target='_blank'>$name  </a> </div>";



        $pr = qqgetIdFromRand_($obj->$field);

        $name = '';
        $link = '#';

        $mt = new GiaPha_Meta();
        if(self::$preDataAfterIndex[GiaPha::class][$pr] ?? false){
            $name = self::$preDataAfterIndex[GiaPha::class][$pr]?->name;

            $link = $mt->getPublicLink(self::$preDataAfterIndex[GiaPha::class][$pr]);
//            echo "<br/>\n Have One";
        }
        else

        if ($obj1 = GiaPha::find($pr)) {
            $name = $obj1->name;
//            $mt = new GiaPha_Meta();
            $link = $mt->getPublicLink($obj1);
        }

        return "<div style='margin-left: 5px; font-size: x-small' title='$val'> <a href='$link' target='_blank'>$name</a> </div>";
    }

    public function _married_with($obj, $val, $field)
    {
        if (!$val)
            return;
        $link = "/my-tree?pid=" . qqgetRandFromId_($obj->married_with);
        $name = $obj->_ten_vo_chong;
        return "<div style='margin-left: 5px; font-size: x-small' title='$val'> <a href='$link' target='_blank'>$name  </a> </div>";
    }

    public function getNeedIndexFieldDb()
    {
        return ['user_id', 'id__', 'parent_id', 'orders', 'created_at', 'deleted_at', 'child_of_second_married', 'married_with', 'tmp_old_id'];
    }

    public function getRandIdListField($field = null)
    {
        return ['id', 'parent_id', 'married_with', 'child_of_second_married', 'stepchild_of'];
    }

    //Các trường sẽ phải cùng thuộc của userid, ví dụ trường hợp obj có user_id = 10, thì parent_id phải là 1 đối tượng tồn tại có user_id cũng = 10
    public function getAllFieldBelongUserId()
    {
        return ['parent_id' => null, 'married_with' => null, 'child_of_second_married' => null, ];
    }

    public function _image_list($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    //    function isStatusField($field)
    //    {
    //        if($field == 'status' || $field == 'child_type')
    //            return 1;
    //        return 0;
    //    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {

        $results = GiaPha::getCountOver();
        $nOver200 = '';
        foreach ($results as $x => $y) {
            $nOver200 .= "$x => <b>$y </b> | ";
        }

        if (Helper1::isAdminModule(request())) {
            $ttToDay = GiaPha::where('created_at', '>', nowy())->count();
//            $ttYesterd = GiaPha::where('created_at', '>', nowy(time() - 24 * 3600))->where('created_at', '<', nowy())->count();
//            $ttYesterd1 = GiaPha::where('created_at', '<', nowy(time() - 24 * 3600 * 1))->where('created_at', '>', nowy(time() - 24 * 3600 * 2))->count();

            $strDay = '';
            $counts = [];
            for ($i = 0; $i < 20; $i++) {
                $startOfDay = nowy(time() - 24 * 3600 * $i);
                $endOfDay = nowy(time() - 24 * 3600 * ($i - 1));
                $count = GiaPha::where('created_at', '>', $startOfDay)
                    ->where('created_at', '<', $endOfDay)
                    ->count();
                $counts[] = $count;
                $strDay .= "$count, ";
            }
//            print_r($counts);

            echo "<div style='margin: 0px; padding: 5px'>
<div style='border: 1px dashed #ccc; padding: 5px 10px; background-color: white; margin: 5px 3px; font-size: small'>
N Node daily: <b>$strDay </b> ; | <b> Over node: </b>:   $nOver200
</div>
</div>";
        }
    }

    public function get__col_fix($node_id, $param = [])
    {
        if (! request('pid')) {
            return;
        }
        $pid = qqgetIdFromRand_(request('pid'));
        if ($cf = \App\Models\TreeMngColFix::where('node_id', $node_id)->where('pid', $pid)->first()) {
            return $cf->col_fix;
        }

        return null;
    }

    public static function updateColFix($node_id, $with_pid, $col_fix)
    {

        //$with_pid = qqgetIdFromRand_($request->with_pid);

        if ($col = TreeMngColFix::where(['pid' => $with_pid, 'node_id' => $node_id])->first()) {
            $uid = getCurrentUserId();
            //PID phải là của user mới có thể chỉnh cột
            //Không cần id là của user, vì id đó có thể gắn từ node của user khác
            $gp = GiaPha::find($with_pid);
            if (! $gp || $gp->user_id != $uid) {
                return rtJsonApiError("updateColFix: Not found data or PID not belong your account? $uid");
            }
            $col->pid = $with_pid;
            $col->col_fix = $col_fix;
            $col->update();
        } else {
            $col = new TreeMngColFix();
            $col->pid = $with_pid;
            $col->node_id = $node_id;
            $col->col_fix = $col_fix;
            $col->save();
        }

    }
}
