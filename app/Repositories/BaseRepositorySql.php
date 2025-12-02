<?php

namespace App\Repositories;

use App\Components\ClassRandId2;
use App\Components\clsParamRequestEx;
use App\Components\Helper1;
use App\Models\BlockUi;
use App\Models\ModelGlxBase;
use App\Models\User;
use App\Support\HTMLPurifierSupport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * Các Repo sẽ có chung các hàm lấy data, chỉ khác nhau ở Model, nên có BaseRepo này
 */
class BaseRepositorySql implements BaseRepositoryInterface
{
    /**
     * @var ModelGlxBase
     *                   Mỗi lớp kế thừa sẽ có kiểu model riêng
     */
    protected $model;

    public function get_list($params, clsParamRequestEx $objParam)
    {

        try {
            DB::enableQueryLog();

            //$dataRet = $this->model->queryDataWithParams($params, 1, @$params['limit'], getGidCurrent_());
            $dataRet = $this->model->queryDataWithParams($params, $objParam);

            //Todo:
            //Bảo mật: nếu ko phải debug thì cần bỏ cái này, đi, ví dụ ifHasAdmin Token
            $qr = DB::getQueryLog();
            $qr = null;

            //Trả lại kiểu laravel, là đối tượng Paginator, được serialize, mục đích để nếu dùng Laravel View có thể lấy đối tượng Pagniator để sử dụng
            if (\request()->get('return_laravel_type') == 1) {
                return rtJsonApiDone(serialize($dataRet), null, 1, $qr);
            }



            //Nếu API thông thường, ko cần return laraveltype, thì ko cần serialize
            return rtJsonApiDone($dataRet, null, 1, $qr);

        } catch (\Throwable $e) {
            $pad = '';
            if(isDebugIp())
                $pad = $e->getTraceAsString();
            return rtJsonApiError("Error21: ".$e->getMessage() . "\n$pad");
        }
    }

    public function get($id, clsParamRequestEx $objParam)
    {
        return $this->model->queryGetOne($id, $objParam);
    }

    public function search($param, clsParamRequestEx $objParam)
    {
        //Todo: kiểm tra nếu thuộc UID, trường hợp nào thì cần UID?
        //Ví dụ với Tag, user cần search all Tag để post bài chứ ko chỉ Tag của riêng user
        //        if (!isset($param['search_str']))
        //        {
        //            //return rtJsonApiError("not valid param search_str1");
        //        } else

        //Tìm cả empty, có limit

        $str = @$param['search_str'];
        $field = @$param['field'];
        if (! $field) {
            //return rtJsonApiError("Not found 'field' in request (get,post)!");
            return null;
        }
        $mret = [];
        //Tìm kiếm

        $cls = $this->model;

        if (! $str) {
            $ret = $cls::limit(50)->get();
        } else {
            if($cls::count() > 10000){
                $ret = $cls::where($field, 'like', "$str%")->limit(50)->get();
            }
            else
                $ret = $cls::where($field, 'like', "%$str%")->limit(50)->get();
        }
        //DB::enableQueryLog();
        if ($ret) { //if ($ret = $this->model->whereFieldLike($field, $str)->get())
            $mm = $ret->toArray();
            foreach ($mm as $obj) {
                $mret[] = ['value' => $obj['id'], 'label' => $obj[$field]];
            }
        }
        //dd(DB::getQueryLog());

        //return response()->json(['errorCode' => 0, 'dataRet' => $mret], 200);
        return rtJsonApiDone($mret , 'Search done');

        //return \response()->json(['errorCode' => 1, 'dataRet' => "Not found input value"], 400);
        return rtJsonApiError('Not found input value');
    }

    public function update($id, $param, clsParamRequestEx $objParam)
    {
        //if(isDebugIp())
        $objMeta = $this->model::getMetaObj();
        $param = array_map(function ($data) {
            return $data ? HTMLPurifierSupport::clean($data) : $data;
        }, $param);

        $lang = null;
        if($userObj = getCurrentUserId(1)){
            $lang = $userObj->language ?? '';
        }

        if ($rl = $this->model->getValidateRuleUpdate($id)) {
            //$request->validate($this->model::::$createRules);
            $validator = \Illuminate\Support\Facades\Validator::make(
                $param,
                $rl,
                Helper1::getValidateStringAlt($lang),
                $objMeta->getMapFieldAndDesc()
            );

            if ($validator->fails()) {
                $mE = $validator->errors()->all();
                loi2(implode("\n- ", $mE));
            }
        }

        //Todo: nếu trường hợp update pid, thì xly kiểu gì
        //Phải kiểm tra PID thuộc user không, loop pid...
        try {

            $objMeta = $this->model::getMetaObj();

            if ($objMeta instanceof MetaOfTableInDb);
            $mMeta = $objMeta->getMetaDataApi();

            if($id == $objMeta::getIdReadOnlyIfNotSupperAdmin()){
                if(!isAdminACP_()){
                    return rtJsonApiError("NOT ALLOW UPDATE DEMO-ID2 ($id) " . get_class($objMeta));
                }
            }




            //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //            print_r($mMeta);
            //            echo "</pre>";
            //            die();

            $mRandField = null;
            if ($objMeta->isUseRandId()) {
                if($this->model->hasField('ide__') && isUUidStr($id)){
                    $id = $this->model->where('ide__', $id)->first()?->id;
                }
                else {
                    $mRandField = $objMeta->getRandIdListField();
                    if (!is_numeric($id)) {
                        $id = ClassRandId2::getIdFromRand($id);
                    }
                }
            }
            if (! is_numeric($id)) {
                loi2("Need input Id number2!  $id / " . $objMeta::class);
            }
            //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //            print_r($mMeta);
            //            echo "</pre>";
            //

            //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //            print_r($objParam);
            //            echo "</pre>";
            //            die();

            if (isset($mMeta['user_id'])) {
                $objParam->setUidIfMust();
            }

            $data = null;
            $objMeta = $this->model::getMetaObj();
            $mMeta = $objMeta->getMetaDataApi();

            if ($id > 0) {
                $data = $this->model->find($id);
                if (! $data) {
                    loi2("Not found data2: $id / ".$this->model::class);
                }
            }



            if ($id > 0 && isset($mMeta['user_id'])) {
                if ($objParam->need_set_uid && $data->user_id != $objParam->need_set_uid) {
                    loi2("Item Not belong your acc? (Dữ liệu không phải của bạn $data->user_id / $objParam->need_set_uid )");
                }
            }

            //Todo ??????: không hiểu sao, để transaction ở đây, thì Test API kq ko cập nhật được textarea2(demo)
            //Dù ở test vẫn báo ok, và trên web vẫn dùng BT
            //testAdminDemoFieldEditable
            //   DB::beginTransaction();

            //
            //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //            print_r($param);
            //            echo "</pre>";
            //            die('xxxx');

            $mBelongUser = null;
            $mBelongUser0 = $objMeta->getAllFieldBelongUserId();
            if ($id > 0) {
                if($mBelongUser0 && is_array($mBelongUser0))
                    $mBelongUser = array_keys($mBelongUser0);
            }

            ////////////////////////////////////////////////////////////
            foreach ($param as $field => $val) {

                //Nếu có randfield:
//                if($mRandRield ?? '')
                if ($mRandField && in_array($field, $mRandField) && $val && ! is_numeric($val)) {
                    $val = ClassRandId2::getIdFromRand($val);
                    $param[$field] = $val;
                }

                //PID phải set = 0 nếu null, nếu ko sẽ ko list được trên tree
                if($field == 'parent_id' ){
                    if(!$val || $val == -1)
                        $param['parent_id'] = 0;
                }

                if(isDebugIp()){
//                    die("VAL = xxx / ");
                }
                if($objParam->need_set_uid)
                if ($mBelongUser && $val) {


                    if (in_array($field, $mBelongUser)) {
                        //kiểm tra id phải thuộc userid này

                        $modelCheck = $this->model;
                        //Nếu được set riêng parent class, thì gán tại đây:
                        if($mBelongUser0 && is_array($mBelongUser0) &&  isset($mBelongUser0[$field]) )
                            $modelCheck = $mBelongUser0[$field];

                        if (! $modelCheck::where(['user_id' => $objParam->need_set_uid, 'id' => $val])->first()) {
                            loi2("Item Not belong account ($field='$val')! (Dữ liệu không phải của bạn (2) ($objParam->need_set_uid))");
                        }
                    }
                }

                if ($field != 'id' && ! $objMeta->isEditableFieldGetOne($field, $objParam->set_gid)) {
                    //bỏ qua các trường ko được edit, ko cần báo lỗi:
                    //                    continue;
                    loi2("Not editable Field:  '$field' / $objParam->set_gid / ".$this->model::class);
                }

                if (isset($mMeta[$field])) {
                    $objMeta = $mMeta[$field];
                    if ($data instanceof ModelGlxBase);


                    $data?->syncDataRelationShip($objMeta, $val, 'update');
                }
            }



            $updateDone = 0;

            if ($id > 0) {


//                if(isSupperAdmin_()){
//
//                    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                    print_r($param);
//                    echo "</pre>";
//
//                    die("ABC = ");
//                }


                if(0)
                foreach ($param AS $k1 => $v1) {
                    //Phải có 1 giá trị gì khác rỗng thì mới update
                    if ($k1 != 'id' && trim($v1) && $v1[0] != '_') {
                        $data->$k1 = $v1;
                    }
                }

//                if($data->save())
//                    $updateDone = 1;

                if(isDebugIp()){
//                    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                    print_r($param);
//                    echo "</pre>";
//                    die("...");
                }

                if ($data->update($param)) {
                    $updateDone = 1;
                }
            }

            $idInsertDone = [];
            if ($id < 0) {
                unset($param['id']);
                foreach ($param as $k1 => $v1) {
                    //Phải có 1 giá trị gì khác rỗng thì mới insert
                    if ($k1 != 'id' && trim($v1)) {
                        $retId = $this->add($param, $objParam, 1);
                        $idInsertDone[$id] = $retId;
                        break;
                    }
                }
            }

            //   DB::commit();
            if (isset($idInsertDone) && $idInsertDone) {
                return rtJsonApiDone(['insert_list' => $idInsertDone], "Update/InsertDone/ID=$id($updateDone)");
            }

            return rtJsonApiDone("Updated done = $updateDone, ID=$id ($updateDone)");

        } catch (\Throwable $e) {
            DB::rollBack();
            loi2("ERROR INDEX: " . $e->getMessage());
        }
    }

    public function add($param, clsParamRequestEx $objParam, $returnId = 0)
    {
        try {


            //Không hieu sao isset ko hoạt dong
            //
            if(array_key_exists('created_at', $param)) {

                if(empty($param['created_at'])) {  // NULL, '', 0, false đều là empty
                    unset($param['created_at']);
                }
            }


            $param0 = unserialize(serialize($param));
            foreach ($param AS $key=>$val){
                //Bo cac truong mo rong
                if(str_starts_with($key, '___')){
                    unset($param[$key]);
                }
            }


            $lang = null;
            if($userObj = getCurrentUserId(1)){
                $lang = $userObj->language ?? '';
            }


            $objMeta = $this->model::getMetaObj();

            $objMeta->beforeInsertDb($param0, $_POST);

            if (isIPDebug()) {
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($param);
//                echo "</pre>";
//                die();
                //               die("xxx = " . $GLOBALS['DEF_DISABLE_HTML_PURIFIER']);
//                $objMeta->afterInsertApi(1,$param);
            }

            if (($GLOBALS['DEF_DISABLE_HTML_PURIFIER'] ?? '') == 1) {
                //                die("123");
            } else {

                //if(isDebugIp())
                $param = array_map(function ($data) {
                    return $data ? HTMLPurifierSupport::clean($data) : $data;
                }, $param);
            }

            if (! $param) {
                return rtJsonApiError('Not valid param?');
            }
//            if(0)
            if ($rl = $this->model->getValidateRuleInsert()) {


                //$request->validate($this->model::::$createRules);
                $validator = \Illuminate\Support\Facades\Validator::make(
                    $param,
                    $rl,
                    Helper1::getValidateStringAlt($lang),
                    $objMeta->getMapFieldAndDesc()
                );
                if ($validator->fails()) {
                    $mE = $validator->errors()->all();

                    return rtJsonApiError(implode("\n", $mE));
                }
            }

            $mMeta = $objMeta->getMetaDataApi();
            DB::beginTransaction();

            if (isset($mMeta['user_id'])) {
                $objParam->setUidIfMust();
                //Mặc định luôn tạo uid, dù admin hay member
                if($param['user_id'] ?? ''){
                    //Nếu có gửi UID, thì ko set, vì nó đã set rồi
                }else
                    $param['user_id'] = $objParam->userIdLogined;
            }

            //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //            print_r($objParam);
            //            echo "</pre>";
            //            die();

            if ($objMeta->isUseRandId()) {
                $mRandField = $objMeta->getRandIdListField();
                if ($mRandField) {
                    foreach ($param as $field => $val) {
                        //Nếu có randfield:
                        if (in_array($field, $mRandField) && $val && ! is_numeric($val)) {
                            $val = ClassRandId2::getIdFromRand($val);
                            $param[$field] = $val;
                        }
                    }
                }
            }


            if ($objParam->need_set_uid) {

                if($param['user_id'] ?? ''){
                    //Nếu có gửi UID, thì ko set, vì nó đã set rồi
                }else
                    //Nếu module là member thì bắt buộc phải có set memberid
                    $param['user_id'] = $objParam->need_set_uid;

                //Kiểm tra các trường bắt buộc phải thuộc userid:

                $mBelongUser0 = $objMeta->getAllFieldBelongUserId();
                $mBelongUser = null;
                if($mBelongUser0 && is_array($mBelongUser0))
                    $mBelongUser = array_keys($mBelongUser0);

                //                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                //                print_r($mBelongUser);
                //                echo "</pre>";
                //
                //                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                //                print_r($param);
                //                echo "</pre>";

                if ($mBelongUser) {
                    foreach ($param as $field => $val) {
                        //Vì là ID, nên sẽ phải > 0
                        if ($val)
                        {
                            if (in_array($field, $mBelongUser)) {
                                //kiểm tra id phải thuộc userid này
                                $modelCheck = $this->model;
                                //Nếu được set riêng parent class, thì gán tại đây:
                                if($mBelongUser0 && is_array($mBelongUser0) &&  isset($mBelongUser0[$field]) )
                                    $modelCheck = $mBelongUser0[$field];

                                if (! $modelCheck::where(['user_id' => $objParam->need_set_uid, 'id' => $val])->first()) {
                                    loi2("Item Not belong account ($field='$val')! (Dữ liệu $val không phải của bạn - 26)");
                                }
                            }
                        }
                    }
                }
            }

            //Trường hợp add cha cho một obj id
            if (isset($_GET['add_parent_to'])) {
                $addPidTo0 = $addPidTo = $_GET['add_parent_to'];
                if ($objMeta->isUseRandId() && $addPidTo && ! is_numeric($addPidTo)) {
                    $addPidTo = ClassRandId2::getIdFromRand($addPidTo);
                }

                unset($param['add_parent_to']);

                //- Kiểm tra objID $addPidTo có tồn tại không,
                //- và obj đó phải có parent_id = 0, vì ko thể add Cha cho obj đã có Cha
                //- Tạo POBJ mới, và đổi obj->parent_id = POBJ.id
                //- và tất cả các anh em của obj phải đổi sang cha mới
                $objChild = $this->model->find($addPidTo);
                if (! $objChild) {
                    return rtJsonApiError("Not found data id OR id not belong to you:  $addPidTo0");
                }
                if ($objChild->parent_id != 0) {
                    return rtJsonApiError("Can not add parent to $addPidTo0, it has other parent:  ".ClassRandId2::getRandFromId($objChild->parent_id));
                }
                if ($objParam->need_set_uid && $objChild->user_id != $objParam->need_set_uid) {
                    return rtJsonApiError("This objId $addPidTo0 not belong to you! (Dữ liệu không phải của bạn)");
                }
                //                return rtJsonApiError("Doing...!");
            }


            if(isDebugIp()){
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($param);
//                echo "</pre>";
//                die();
            }


            $objNew = $this->model->create($param);



            if ($objNew instanceof ModelGlxBase);
            //update luon PR neu co:
            $objNew->updateParentList();

            $data = $objNew;
            ////////////////////////////////////////////////////////////
            //Relate pivot table ở đây, để update pivot table
            foreach ($param as $field => $val) {

                if (isset($mMeta[$field])) {
                    $objMeta = $mMeta[$field];
                    $data->syncDataRelationShip($objMeta, $val, 'add');
                }
            }

            //Sau khi đã thêm $objNew thì sẽ cập nhật lại Child vào $objNew
            //Khi đó pid objChild sẽ không thuộc root0, mà thuộc $objNew mới
            if (isset($_GET['add_parent_to']) && isset($objChild)) {
                $newPid = $objNew->id;
                $objChild->parent_id = $newPid;
                $objChild->update();

                //---Đoạn này chỉ có ở giapha, có thể tách ra
                if (isset($mMeta['married_with'])) {
                    //Tìm tất cả các married_width nếu có
                    $this->model::where('married_with', $objChild->id)
                        ->update(['parent_id' => $newPid]);
                }

                //                return rtJsonApiError("Doing...!");
            }

            $retID = $objNew->id;
            if ($objMeta->isUseRandId()) {
                $retID = ClassRandId2::getRandFromId($objNew->id);
            }

            $objMeta->afterInsertApi($objNew, $param0, $_POST);

            DB::commit();

            if ($returnId) {
                return $retID;
            }

            return rtJsonApiDone($retID, " Add done $retID! ");

        } catch (\Throwable $e) {
            DB::rollBack();
            if ($returnId) {
                loi('Add error1: '.$e->getMessage());
            }

            return rtJsonApiError('Add error2: '.$e->getMessage() . $e->getTraceAsString());
        }
    }

    public function update_multi($param, clsParamRequestEx $objParam)
    {
        try {
            if (isDebugIp()) {

                //                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                //                print_r($param);
                //                echo "</pre>";
                //                die();
            }
            if ($param['___cmd'] ?? '' && $param['___cmd'] == 'update_parent_list') {
                return $this->update_parent_list($param, $objParam);
            }

            if (isset($param['dataPostV2'])) {
                $param['dataPostV2'] = array_map(function ($mdata) {
                    if ($mdata) {
                        $mdata = array_map(function ($data) {
                            return $data ? HTMLPurifierSupport::clean($data) : $data;
                        }, $mdata);
                    }

                    return $mdata;
                }, $param['dataPostV2']);
            } else {
                $param = array_map(function ($mdata) {
                    if (! is_array($mdata)) {
                        return $mdata ? HTMLPurifierSupport::clean($mdata) : $mdata;
                    }
                    if ($mdata) {
                        $mdata = array_map(function ($data) {
                            return $data ? HTMLPurifierSupport::clean($data) : $data;
                        }, $mdata);
                    }

                    return $mdata;
                }, $param);
            }

            $ret = 0;
            if (! $param) {
                return rtJsonApiDone('Not found data to update!');
            }
            $objMeta = $this->model::getMetaObj();
            $mMeta = $objMeta->getMetaDataApi();

            if (isset($mMeta['user_id'])) {
                $objParam->setUidIfMust();
            }

            $lang = null;
            if($userObj = getCurrentUserId(1)){
                $lang = $userObj->language ?? '';
            }

            $gid = $objParam->set_gid;

            //            $m1 = $request->only(['number1','number2','string1','string2']);
            $mPost = $param;

            //Nếu post ID list để update parent
            //Chỉ khi có parent_id, thì mới thực hiện update này
            if (isset($param['id_list'])) {

                //nếu add to multi
                if (isset($param['add_parent_extra'])) {
                    if (! $objMeta->isEditableFieldGetOne('parent_extra', $gid)) {
                        loi2("Not allow edit parent_extra (GID: $gid) / ".get_class($objMeta));
                    }

                    $pListToAddExtra = explode(',', $param['add_parent_extra']);
                    //kiểm tra valid user...:
                    $pListToAddExtra = array_filter($pListToAddExtra);

                    foreach ($pListToAddExtra as $pid) {
                        $pid0 = $pid;
                        if ($objMeta->isUseRandId() && $pid && ! is_numeric($pid)) {
                            $pid = ClassRandId2::getIdFromRand($pid);
                        }
                        if (! is_numeric($pid)) {
                            loi2("Not number pid : $pid");
                        }
                        $parentCls = $objMeta::$folderParentClass;
                        //Không cho phép cha di chuyển vào con
                        if ($pid) {//Chỉ kiểm tra nếu PID > 0
                            if (! $parentCls) {
                                loi2(' MetaClass: Not defined class Folder for '.$objMeta::class.' - '.$this->model::class);
                            }
                            if (! $foldParent = $parentCls::where('id', $pid)->first()) {
                                loi2(" Not found folder ($pid0) ");
                            }
                            if ($objParam->need_set_uid > 0) {
                                //Kiểm tra PID có thuộc user ko:
                                if ($foldParent->user_id != $objParam->need_set_uid) {
                                    loi2(" Folder Not your data?  ($pid0) ");
                                }
                            }
                        }
                    }
                }

                //nếu Chuyển đến cha:
                if (isset($param['move_to_parent_id'])) {
                    $pidMove = $pid0 = $pid = $param['move_to_parent_id'];
                    if ($objMeta->isUseRandId() && $pid && ! is_numeric($pid)) {
                        $pid = ClassRandId2::getIdFromRand($pid);
                    }
                    if (! is_numeric($pid)) {
                        loi2("Not number pid : $pid");
                    }
                    $parentCls = $objMeta::$folderParentClass;
                    //Không cho phép cha di chuyển vào con
                    if ($pid) {//Chỉ kiểm tra nếu PID > 0
                        if (! $parentCls) {
                            loi2(' MetaClass: Not defined class Folder for '.$objMeta::class.' - '.$this->model::class);
                        }
                        if (! $foldParent = $parentCls::where('id', $pid)->first()) {
                            loi2(" Not found folder ($pid0) ");
                        }
                        if ($objParam->need_set_uid > 0) {
                            //Kiểm tra PID có thuộc user ko:
                            if ($foldParent->user_id != $objParam->need_set_uid) {
                                loi2(" Folder Not your data?  ($pid0) ");
                            }
                        }
                        if ($foldParent instanceof ModelGlxBase);
                        $mParentOfParent = $foldParent->getListParentId(1);
                    }
                }

                $id_list0 = $param['id_list'];
                $id_list = explode(',', trim($param['id_list'], ','));
                $idListOK = [];
                if ($id_list) {

                    //Kiểm tra thuoc user ko:
                    foreach ($id_list as $idf) {

                        if(isUUidStr($idf)){
                            $idf = $this->model->where('ide__', $idf)->first()?->id;
                        }
                        else
                        if ($objMeta->isUseRandId() && $idf && ! is_numeric($idf)) {
                            $idf = ClassRandId2::getIdFromRand($idf);
                        }

                        //$obj = $this->model->findOrFail($idf);
                        $obj = $this->model->find($idf);
                        if (! $obj) {
                            loi2("Not found data1 $idf: ".$this->model::class);
                        }

                        //Bo qua neu Readonly
                        if($idf == $objMeta::getIdReadOnlyIfNotSupperAdmin()){
                            if(!isSupperAdminDoing()){
                                continue;
                            }
                        }

                        $idListOK[] = $idf;

                        if (isset($mMeta['user_id'])) {
                            if ($objParam->need_set_uid) {
                                if ($obj->user_id != $objParam->need_set_uid) {
                                    loi2('Not your item to updateMulti? (Dữ liệu không phải của bạn)');
                                }
                            }
                        }

                        //nếu là move parent:
                        //Kiểm tra xem có bị di chuyển vào con không
                        if (isset($parentCls) && $parentCls == $this->model::class) {
                            if (isset($mParentOfParent) && $mParentOfParent && is_array($mParentOfParent)) {
                                //obj di chuyển vào parent, nếu obj có trong danh sách cha của parent thì sẽ báo lỗi
                                if (in_array($obj->id, $mParentOfParent)) {
                                    loi2("Parent Can not move to child or to itself!\n");
                                }
                            }
                        }
                    }

                    //Cho gia pha?
                    if (isset($param['field_val']) && isset($param['field_name_to_change'])) {
                        //Kiểm tra xem field có editable không
                        if (! isset($mMeta[$param['field_name_to_change']])) {
                            loi2('Not valid field name: '.$param['field_name_to_change']);
                        }
                        if ($objMeta->isEditableField($param['field_name_to_change'], $objParam->set_gid)) {
                            if (is_array($param['field_val'])) {
                                //                                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                                //                                print_r($param['field_val']);
                                //                                echo "</pre>";
                                //                                die("xxx");
                                try {
                                    DB::beginTransaction();
                                    $fieldChange = $param['field_name_to_change'];
                                    $cc = 0;
                                    foreach ($idListOK as $idObj) {
                                        $valUpdate = $param['field_val'][$cc];
                                        $cc++;
                                        $objUpdate = new $this->model;

                                        //bỏ qua Id mẫu:
                                        if(!isSupperAdminDoing())
                                        if($idObj == $objMeta::getIdReadOnlyIfNotSupperAdmin()){
                                            continue;
                                        }

                                        if ($objUpdate = $objUpdate->find($idObj)) {

                                            //                                            $objUpdate->$fieldChange = $valUpdate;
                                            $objUpdate->update(['id' => $idObj, $fieldChange => $valUpdate]);
                                        }
                                    }
                                    DB::commit();
                                } catch (\Throwable $e) {
                                    DB::rollBack();
                                    loi2('ERROR UPDATE: '.$e->getMessage().$e->getTraceAsString());
                                }
                            } else {


                                $this->model::whereIn('id', $idListOK)->update([$param['field_name_to_change'] => $param['field_val']]);
                            }
                        } else {
                            loi2('Not editable1: '.$param['field_name_to_change']);
                        }
                    }

                    if(isDebugIp()){
//                        echo "PID = $pid <pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                        print_r($idListOK);
//                        echo "</pre>";
//                        die();
                    }

                    //Nếu move to pr
                    if (isset($pidMove) && $pidMove) {
                        if (isset($parentCls) && isset($mParentOfParent)) {
                            $this->model::whereIn('id', $idListOK)->update(['parent_id' => $pid]);
                            //Cần cập nhật PID ở đây: ???
                            $cls = $this->model::class;
                            $nUpdate = 0;
                            foreach ($idListOK as $idUpdate) {
                                if ($obj = $cls::find($idUpdate)) {
                                    if($obj instanceof ModelGlxBase);
                                    if ($obj->updateParentList()) {
                                        $nUpdate++;
                                    }
                                }
                            }

                            return rtJsonApiDone("Update Move to PID, $nUpdate items", "UI: $objParam->need_set_uid");

                        }
                    }

                    //Cap nhat parent_extra:
                    if (isset($pListToAddExtra)) {
                        $nUpdate = 0;
                        $cls = $this->model::class;
                        foreach ($id_list as $idUpdate) {
                            if ($obj = $cls::find($idUpdate)) {

                                $tmp = $obj->parent_extra.','.implode(',', $pListToAddExtra);
                                //
                                $obj->parent_extra = implode(',', array_unique(array_filter(explode(',', $tmp))));
                                //                                echo "<pre>$obj->parent_extra  /  >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                                //                                print_r($tmp);
                                //                                echo "</pre>";
                                //                                die();

                                if ($obj instanceof ModelGlxBase);
                                if ($obj->updateParentList()) {
                                    $nUpdate++;
                                }
                            }
                        }

                        return rtJsonApiDone("Update PExtra $nUpdate items", "UI: $objParam->need_set_uid");
                    }

                    if (isset($pid) && $pid0 == 0) {
                        $this->model::whereIn('id', $idListOK)->update(['parent_id' => 0]);
                    }

                    return rtJsonApiDone('Update Item Done 2', "UI: $objParam->need_set_uid");
                }

                //
                //                    return rtJsonApiError("Not valid idlist?");
                //                }

                loi2('Not valid cmd idlist?');
            }

            if(isDebugIp()){
                if(!isset($mPost['id']) && $mPost['ide__'] && $this->model->hasField('ide__')){
                    if(count($mPost['ide__'])){
                        $mPost['id'] = [];
                        foreach ($mPost['ide__'] as $ide) {
                            if($foundObj = $this->model->where("ide__", $ide)->first()){
                                $mPost['id'][] = $foundObj->id;
                            }
                        }
                    }
                }
            }

            if(isDebugIp()){
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($mPost);
//                echo "</pre>";
//                die();
            }


            if (! isset($mPost['dataPostV2']) && ! isset($mPost['id'])) {
                loi2('Not valid data, not found id in array!');
            }

            $objMeta = $this->model::getMetaObj();
            //Nếu post dạng này: [id =>[1,2,3,4,5...], field1=>[4,5,7,3,23] , field2=>[4,5,7,3,23] ...]
            //Thì phải tổ chức lại mảng data để save
            $mData = [];
            $mMeta = $objMeta->getMetaDataApi();

            DB::beginTransaction();

            $mRandField = null;
            if ($objMeta->isUseRandId()) {
                $mRandField = $objMeta->getRandIdListField();
            }
            $mBelongUser0 = $objMeta->getAllFieldBelongUserId();
            $mBelongUser = null;
            if($mBelongUser0 && is_array($mBelongUser0))
                $mBelongUser = array_keys($mBelongUser0);

            //Kiểu post v2, mỗi đối tượng riêng biệt
            if (isset($mPost['dataPostV2'])) {
                $mData = $mPost['dataPostV2'];
            } else {


                foreach ($mPost as $field => $mValueOfField) {
                    foreach ($mValueOfField as $key => $val) {
                        if (! isset($mData[$key])) {
                            $mData[$key] = [];
                        }

                        if ($mRandField && in_array($field, $mRandField) && $val && ! is_numeric($val)) {
                            $val = ClassRandId2::getIdFromRand($val);
                        }

                        if ($val) {
                            if ($objParam->need_set_uid && $mBelongUser) {
                                if (in_array($field, $mBelongUser)) {
                                    //kiểm tra id phải thuộc userid này
                                    $modelCheck = $this->model;

                                    //Nếu được set riêng parent class, thì gán tại đây:
                                    if($mBelongUser0 && is_array($mBelongUser0) &&  isset($mBelongUser0[$field]) )
                                        $modelCheck = $mBelongUser0[$field];

                                    if (! $modelCheck::where(['user_id' => $objParam->need_set_uid, 'id' => $val])->first()) {
                                        loi2("Item Not belong account ($field='$val')! (Dữ liệu $val không phải của bạn - 25)");
                                    }
                                }
                            }
                        }

                        $objMeta = $mMeta[$field];

                        //Chỉ đưa các field có quyền edit vào mảng update
                        if ($field == 'id' || $objMeta->isEditableFieldGetOne($field, $gid) || $objMeta->isEditableField($field, $gid)) {
                            $mData[$key][$field] = $val;
                            if (! isset($mPost['id'][$key])) {
                                continue;
                            }

                            //Chắc chắn PID được update là 0 nếu nó null, vì nếu PID null sẽ ko list được trên tree
                            if($field == 'parent_id' && !$val){
                                $mData[$key][$field] = 0;
                            }

                            $idObj = $mPost['id'][$key];

                            if ($mRandField && in_array('id', $mRandField) && $idObj && ! is_numeric($idObj)) {
                                $idObj = ClassRandId2::getIdFromRand($idObj);
                            }

                            if ($idObj < 0) {
                                continue;
                            }

                            //                        $data = $this->model->findOrFail($idObj);
                            $data = $this->model->find($idObj);
                            if (! $data) {
                                loi2("Not found data21 $idObj");
                            }

                            //Sync các giá trị relation nếu có (ở bảng khác)
                            if ($data instanceof ModelGlxBase);
                            $data->syncDataRelationShip($objMeta, $val, 'update');
                        } else {
                            //                        return rtJsonApiError("UpdateMulti - Not editable field '$field'");
                        }
                    }
                }
            }

            if (isDebugIp()) {

            }
            $padIgnoreId = null;
            $idInsertDone = [];
            foreach ($mData as $mObj) {
                if (! isset($mObj['id'])) {
                    continue;
                }

                $id = $mObj['id'];


                //Đã check ở trên, không cần check ở đây nữa:
                if ($id > 0) {
                    $data = $this->model->find($id);

                    //Bo qua neu Readonly
                    if($id == $objMeta::getIdReadOnlyIfNotSupperAdmin()){
                        if(!isSupperAdminDoing()){
                            $padIgnoreId = " | But Ignore Template Id: ". $id;
                            continue;
                        }
                    }

                    if (! $data) {
                        return rtJsonApiError("Not found data3 $id");
                    }
                    if (isset($mMeta['user_id'])) {
                        if ($objParam->need_set_uid) {
                            if ($data->user_id != $objParam->need_set_uid) {
                                $loi = "Not your item to updateMulti? (Dữ liệu không phải của bạn) ";
                                if(isDebugIp())
                                    $loi .= " : UID need_set_uid: $objParam->need_set_uid / UID: $data->id, UID = $data->user_id";
                                loi2($loi);
                            }
                        }
                    }

                    if ($rl = $data->getValidateRuleUpdate($id)) {
                        $validator = \Illuminate\Support\Facades\Validator::make(
                            $mObj,
                            $rl,
                            Helper1::getValidateStringAlt($lang),
                            $objMeta->getMapFieldAndDesc()
                        );
                        if ($validator->fails()) {
                            $mE = $validator->errors()->all();
                            loi2("Error update multi:\n".implode("\n- ", $mE));
                        }
                    }

                    $ret = $data->update($mObj);
                }
                if ($id < 0) {
                    unset($mObj['id']);
                    foreach ($mObj as $k1 => $v1) {
                        //Phải có 1 giá trị gì khác rỗng thì mới insert
                        if ($k1 != 'id' && trim($v1)) {
                            $retId = $this->add($mObj, $objParam, 1);
                            $idInsertDone[$id] = $retId;
                            break;
                        }
                    }
                }
            }

            DB::commit();

            if (! $ret) {
                return rtJsonApiDone('Nothing to update?');
            }

            if (isset($idInsertDone) && $idInsertDone) {
                return rtJsonApiDone(['insert_list' => $idInsertDone], 'Update + Insert multi Done');
            }

            return rtJsonApiDone('Update multi Done ' . ($padIgnoreId ?? ''));

        } catch (\Throwable $e) {
            DB::rollBack();
            loi2('ERROR UPDATE: '.$e->getMessage().$e->getTraceAsString());
        }
        // TODO: Implement update_multi() method.
    }

    public function delete($param, clsParamRequestEx $objParam)
    {
        if (! isset($param['id'])) {
            return rtJsonApiError('Need input valid one id, or valid multi id (seperate by comma)');
        }

        //        if(!is_numeric($param['id']) && strstr($param['id'], ',') === false){
        //            return rtJsonApiError("Need input valid one id, or valid multi id (seperate by comma)");
        //        }
        //
        //        dump($objParam);
        //        die();

        $objMeta = $this->model::getMetaObj();
        $mMeta = $objMeta->getMetaDataApi();

        if (isset($mMeta['user_id'])) {
            $objParam->setUidIfMust();
        }

        $idOrListId0 = $idOrListId = $param['id'] ?? null;
        if (! $idOrListId) {
            return rtJsonApiError('Not found id param!');
        }

        $idOrListId = trim(trim($idOrListId), ',');
        $idOrListId = explode(',', $idOrListId);
        //        $idOrListId = array_filter( $idOrListId,
        //            fn($arrayEntry) => !is_numeric($arrayEntry)
        //        );

        $idOrListIdWithRand = [];

        //Kiểm tra thuoc user ko:

        $tt = 0;
        foreach ($idOrListId as $idf) {
            if ($idf < 0) {
                continue;
            }
            $tt++;

            if ($objMeta->isUseRandId() && $idf && ! is_numeric($idf)) {
                if(isUUidStr($idf) && $this->model->hasField('ide__')){
                    $idf = $this->model::where("ide__", $idf)->first()?->id ?? '';
                }else
                    $idf = ClassRandId2::getIdFromRand($idf);
            }

            //Bo qua neu Readonly
            if($idf == $objMeta::getIdReadOnlyIfNotSupperAdmin()){
                if(!isSupperAdminDoing()){
                    continue;
                }
            }

            if($idf)
                $idOrListIdWithRand[] = $idf;
            //                    echo "<br/>\nxxx";
            $obj = $this->model->find($idf);
            if (! $obj) {
                return rtJsonApiError("Not found item: $idf");
            }

            if (isset($mMeta['user_id']) && $objParam->need_set_uid > 0) {
                if ($obj->user_id != $objParam->need_set_uid) {
                    return rtJsonApiError("Not your item to delete $idf ? (Dữ liệu không phải của bạn)");
                }
            }
        }

        ////
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($idOrListId);
        //        echo "</pre>";
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($idOrListIdWithRand);
        //        echo "</pre>";
        //        die();
        //
        if ($mm = $this->model::whereIn('id', $idOrListIdWithRand)->get()) {
            //Delete từng cái để ghi log
            foreach ($mm as $obj) {
                $obj->delete();
            }

            $idOrListId0 = trim($idOrListId0, ',');
            return rtJsonApiDone($idOrListId0, " DELETE DONE! $tt Item");
        } else {
            return rtJsonApiError(" Can not delete: $tt item ! ");
        }

    }

    /**
     * Update parent
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_parent_list($param, clsParamRequestEx $objParam)
    {
        $idOrListId = $param['id'] ?? '';
        if (! $idOrListId) {
            return rtJsonApiError('Need input valid id');
        }

        $objMeta = $this->model::getMetaObj();
        $mMeta = $objMeta->getMetaDataApi();

        if (! isset($mMeta['parent_id']) || ! isset($mMeta['parent_list'])) {
            return rtJsonApiError('Not parent list?');
        }

        if (isset($mMeta['user_id'])) {
            $objParam->setUidIfMust();
        }

        $idOrListId = trim(trim($idOrListId), ',');
        $idOrListId = explode(',', $idOrListId);

        $done = 0;
        foreach ($idOrListId as $idf) {
            $idf = trim($idf);
            if (! $idf) {
                continue;
            }
            if ($objMeta->isUseRandId() && $idf && ! is_numeric($idf)) {
                $idf = ClassRandId2::getIdFromRand($idf);
            }
            if (! is_numeric($idf)) {
                return rtJsonApiError("Not valid id to update: $idf");
            }
            $obj = $this->model::find($idf);
            if ($obj) {
                if (isset($mMeta['user_id'])) {
                    if ($objParam->need_set_uid) {
                        if ($obj->user_id != $objParam->need_set_uid) {
                            return rtJsonApiError("Not your item to update? $obj->user_id != $objParam->need_set_uid");
                        }
                    }
                }

                //                if(!$this->model->parent_id)
                //                    $this->model->parent_list = '';
                //                else {
                //                    $mPid = $this->model->getListParentId();
                //                    $this->model->parent_list = implode(",", $mPid);
                //                }
                $obj->updateParentList();
                $done++;
                //                $this->model->save();
            }
        }

        return rtJsonApiDone(" UPDATE_PR_DONE: $done item !", " UPDATE_PR_DONE: $done item !");
    }

    /**
     * Xóa một bản ghi
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function un_delete($param, clsParamRequestEx $objParam)
    {

        $idOrListId = $param['id'] ?? '';
        if (! $idOrListId) {
            return rtJsonApiError('Need input valid id');
        }

        $objMeta = $this->model::getMetaObj();
        $mMeta = $objMeta->getMetaDataApi();

        if (isset($mMeta['user_id'])) {
            $objParam->setUidIfMust();
        }

        $idOrListId = trim(trim($idOrListId), ',');
        $idOrListId = explode(',', $idOrListId);

        $idOrListIdWithRand = [];
        //Kiểm tra thuoc user ko:


        foreach ($idOrListId as $idf) {
            $idf = trim($idf);
            if (! $idf) {
                continue;
            }

            if ($objMeta->isUseRandId() && $idf && ! is_numeric($idf)) {
                if(isUUidStr($idf) && $this->model->hasField('ide__')){
                    $idf = $this->model::withTrashed()->where("ide__", $idf)->first()?->id ?? '';
                }else
                    $idf = ClassRandId2::getIdFromRand($idf);
            }


//            if(isDebugIp()){
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($idf);
//                echo "</pre>";
//                die("IDF = ");
//            }

            if($idf)
                $idOrListIdWithRand[] = $idf;

            if (! is_numeric($idf)) {
                return rtJsonApiError("Not valid id to delete: $idf");
            }

            $obj = null;
            if (method_exists($this->model, 'trashed')) {
                $obj = $this->model::onlyTrashed()->find($idf);
            }
            if (! $obj) {
                if ($this->model->find($idf)) {
                    return rtJsonApiDone('Un-delete before!');
                } else {
                    return rtJsonApiError('Not found object, or deleted!');
                }
            }

            if (isset($mMeta['user_id'])) {
                if ($objParam->need_set_uid) {
                    if ($obj->user_id != $objParam->need_set_uid) {
                        return rtJsonApiError("Not your item to un-delete? $obj->user_id != $objParam->need_set_uid");
                    }
                }
            }
        }

        //        $idOrListId = array_filter( $idOrListId,
        //            fn($arrayEntry) => !is_numeric($arrayEntry)
        //        );

        //        Post::onlyTrashed()->where('id', $post_id)->restore();

        if (method_exists($this->model, 'trashed')) {
            $this->model::onlyTrashed()->whereIn('id', $idOrListIdWithRand)->restore();
        }

        return rtJsonApiDone(' UN-DELETE DONE!');
    }

    /**
     * Lấy danh sách trên tree
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree_index($param, clsParamRequestEx $objParam)
    {
        try {

            $objMeta = $this->model::getMetaObj();
            $mMeta = $objMeta->getMetaDataApi();
            if (isset($mMeta['user_id'])) {
                $objParam->setUidIfMust();
            }

            if ($objParam->module == 'member' && $objParam->need_set_uid <= 0) {
//                return rtJsonApiError('Must set userid when access member api');
            }

            $rt = $this->model->queryIndexTree($param, $objParam);

            if (is_array($rt)) {
                return rtJsonApiDone($rt[0], 'DONE Tree2!', 1, $rt[1]);
            }

            //        return response()->json(['payload' => $rt[0], 'payloadEx' => $rt[1], 'debug1' => $objParam->need_set_uid, 'debug' => $qr]);
        } catch (\Throwable $e) { // For PHP 7
            return rtJsonApiError($e->getMessage()."\n\n".$e->getTraceAsString());
        } catch (\Exception $exception) {
            return rtJsonApiError($exception->getMessage()."\n\n".$exception->getTraceAsString());
        }
    }

    /**
     * Tạo node trên tree
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree_create($param, clsParamRequestEx $objParam)
    {
        $objMeta = $this->model::getMetaObj();
        $mMeta = $objMeta->getMetaDataApi();

        if (isset($mMeta['user_id'])) {
            $objParam->setUidIfMust();
        }

        $new_name = $param['new_name'] ?? '';
        if (! $new_name) {
            return rtJsonApiError('Not valid new_name');
        }

        $pid = $param['pid'] ?? 0;
        if ($objMeta->isUseRandId() && $pid && ! is_numeric($pid)) {
            $pid = ClassRandId2::getIdFromRand($pid);
        }

        //Ko cần vì chỉ có PID thôi?
        //        if($objMeta->isUseRandId()){
        //            $mRandField = $objMeta->getRandIdListField();
        //            if($mRandField)
        //            foreach ($param AS $field=>$val){
        //                if(in_array($field, $mRandField) && $val && !is_numeric($val)){
        //                    $param[$field] = ClassRandId2::getIdFromRand($val);
        //                }
        //            }
        //        }
        //
        //        die(" $new_name / $pid");

        //Todo: cần kiểm tra PID có thuộc user hay ko, nếu pid > 0
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($objParam);
        //        echo "</pre>";
        //
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($mMeta);
        //        echo "</pre>";
        //
        //        die();

        if ($objParam && $objParam->need_set_uid && isset($mMeta['user_id'])) {
            $ret = $this->model::create(['name' => $new_name, 'parent_id' => $pid, 'user_id' => $objParam->need_set_uid])->id;
        } else {
            $ret = $this->model::create(['name' => $new_name, 'parent_id' => $pid])->id;
        }

        return rtJsonApiDone($ret, 'Create tree done!', 1, $new_name);
        //        return response()->json(['payload' => $ret]);
    }

    /**
     * Move phần tử trong tree
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree_move($param, clsParamRequestEx $objParam)
    {
        $objMeta = $this->model::getMetaObj();
        $mMeta = $objMeta->getMetaDataApi();

        if (isset($mMeta['user_id'])) {
            $objParam->setUidIfMust();
        }

        //Todo: kiểm tra nếu thuộc UID
        try {
            DB::beginTransaction();
            //api/menu-tree/move?id=4&to_id=0&new_order_node=4,1,3

            $id = $param['id'];
            $to_id0 = $to_id = $param['to_id'];

            if($id == $to_id){
                return rtJsonApiError('Can not move to children (1) ?');
            }

            if ($objMeta->isUseRandId()) {
                if ($id && ! is_numeric($id)) {
                    $id = ClassRandId2::getIdFromRand($id);
                }
                if ($to_id && ! is_numeric($to_id)) {
                    $to_id = ClassRandId2::getIdFromRand($to_id);
                }
            }

            //Todo: cần kiểm tra $to_id có thuộc user hay ko, nếu $to_id > 0

            $obj = $this->model::where('id', $id)->first();

            $changeParent = 1;
            if($obj->parent_id == $to_id){
                $changeParent = 0;
                //Nếu có order thì cần update order, nên bỏ qua dòng này:
//                return rtJsonApiDone(-10, 'Nothing to move!', 1, $id);
            }else
                $obj->parent_id = $to_id;

            if ($objParam->need_set_uid && isset($mMeta['user_id'])) {

                //Chắc chắn pid phải thuộc user:
                if ($to_id && !($found1 = $this->model->find($to_id))) {
                    return rtJsonApiError('Not found item MoveTo?');
                }

                if($found1 ?? '')
                if ($found1->user_id != $objParam->need_set_uid) {
                    return rtJsonApiError("$to_id0 not in this account? (Dữ liệu không phải của bạn)");
                }

                if ($obj->user_id != $objParam->need_set_uid) {
                    return rtJsonApiError('Not your item to move? (Dữ liệu không phải của bạn-2)');
                }
            }

            //Todo: kiểm tra xem có bị loop move không, ví dụ cha move vào con, hoặc move vào chính nó:
            $tmpId = $to_id;
            if($changeParent)
            for($i = 0; $i< 1000; $i++){
                usleep(1);
                $objCheck = $this->model::where('id', $tmpId)->first();
                if(!$objCheck){
                    break;
                }
                //Tìm cha của ToId, nếu có thì là cha move vào con
                if($objCheck->parent_id == $id){
                    return rtJsonApiError('Can not move to children (2) ?');
                }
                $tmpId = $objCheck->parent_id;
            }


            //Todo: kiểm tra parent xem có uid này ko

            //Sắp xếp order nếu có
            //Todo: có thể cần sort theo kiểu giảm dần, ngược lại
            if (isset($obj->orders)) {
                if (isset($param['new_order_node'])) {
                    $new_order_node = $param['new_order_node'];
                    if (strstr($new_order_node, ',')) {
                        $mOrder = explode(',', $new_order_node);
                        $nOrder = 100; //Để start 100 cho dễ thêm Manual vào đầu, cuối
                        foreach ($mOrder as $nodeId) {
                            if (is_numeric($nodeId)) {
                                if ($obj1 = $this->model::where('id', $nodeId)->where('parent_id', $to_id)->first()) {
                                    $nOrder += 10; //Tăng lên 10 cho dễ thêm Manual vào giữa nếu cần
                                    $obj1->orders = $nOrder;
                                    $obj1->update();
                                }
                            }
                        }
                    }
                }
            }

            $obj->update();
            DB::commit();
        } catch (\Throwable $e) { // For PHP 7
            DB::rollBack();

            return rtJsonApiError($e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();

            return rtJsonApiError($e->getMessage());
        }

        return rtJsonApiDone(1, 'Move tree done!', 1, $id);
        //        return response()->json(['payload'=>1]);
    }

    /**
     * Cập nhật node tree: đổi tên ...
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree_save($param, clsParamRequestEx $objParam)
    {

        $objMeta = $this->model::getMetaObj();
        $mMeta = $objMeta->getMetaDataApi();
        if (isset($mMeta['user_id'])) {
            $objParam->setUidIfMust();
        }

        //Todo: kiểm tra nếu thuộc UID
        try {
            //id=2&to_name=11111
            $id = $param['id'] ?? '';

            if(!$id)
                return null;

            if ($objMeta->isUseRandId()) {
                if ($id && ! is_numeric($id)) {
                    $id = ClassRandId2::getIdFromRand($id);
                }
            }

            $obj = $this->model::where('id', $id)->first();

            if ($objParam->need_set_uid && isset($mMeta['user_id'])) {
                if ($obj->user_id != $objParam->need_set_uid) {
                    return rtJsonApiError('Not your item to save? (Dữ liệu không phải của bạn)');
                }
            }

            $haveChange = 0;
            if (isset($param['to_name'])) {
                $to_name = $param['to_name'];
                $obj->name = $to_name;
                $haveChange = 1;
            }

            if (array_key_exists('icon', $param)) {
                $name = $param['icon'];
                $obj->icon = $name;
                $haveChange = 1;
            }

            if (array_key_exists('id_news', $param)) { 
                $tmp = $param['id_news'];
                if ($tmp && ! BlockUi::find($tmp)) {
                    return rtJsonApiError("Not found BlockUi: $tmp");
                }

                $obj->id_news = $tmp;
                $haveChange = 1;
            }

            //Or change link
            if (isset($param['link'])) {
                $link = $param['link'];
                $obj->link = $link;
                $haveChange = 1;
            }

            //open_new_win
            if (isset($param['open_new_window'])) {
                $open_new_window = $param['open_new_window'];
                if ($open_new_window == 'true') {
                    $obj->open_new_window = 1;
                    $haveChange = 1;
                }
                if ($open_new_window == 'false') {
                    $obj->open_new_window = 0;
                    $haveChange = 1;
                }
            }

            //Or change gid, with menu tree:
            if (isset($param['gid']) && isset($param['enable'])) {


                $gid = $param['gid'];
                //                if (!$gid)
                //                    return rtJsonApiError("Not found gid!");

                if (! $obj->gid_allow) {
                    if ($param['enable'] == 'true') {
                        $obj->gid_allow = $gid;
                        $haveChange = 1;
                        $obj->gid_allow = trim($obj->gid_allow, ',');
                    }
                    if ($param['enable'] == 'false') {
                        $obj->gid_allow = '';
                        $haveChange = 1;
                    }
                } else {

                    if(isAdminCookie()){
//                        die("11112 $obj->id, $obj->gid_allow");
                    }

                    $obj->gid_allow = ",$obj->gid_allow,";
                    if ($param['enable'] == 'true') {

                        if (strstr($obj->gid_allow, ",$gid,") === false) {


                            $obj->gid_allow .= $gid;
                            $haveChange = 1;
                            $obj->gid_allow = trim($obj->gid_allow, ',');
                            //  die(" $obj->gid_allow - 1 ");
                        }
                    }
                    if ($param['enable'] == 'false') {

                        if (strstr($obj->gid_allow, ",$gid,") !== false) {
                            $obj->gid_allow = str_replace(",$gid,", ',', $obj->gid_allow);
                            $haveChange = 1;
                            $obj->gid_allow = trim($obj->gid_allow, ',');
                            //  die(" $obj->gid_allow - 2 ");
                        }
                    }
                }
                $obj->gid_allow = trim($obj->gid_allow, ',');
            }
            if ($haveChange) {
                $obj->update();
            }

            return response()->json(['payload' => $obj->gid_allow." Save Tree done, Change = $haveChange "]);

        } catch (\Throwable $e) { // For PHP 7
            //DB::rollBack();
            return rtJsonApiError($e->getMessage());
        } catch (\Exception $e) {
            // DB::rollBack();
            return rtJsonApiError($e->getMessage());
        }

    }

    /**
     * Xóa node tree
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree_delete($param, clsParamRequestEx $objParam)
    {

        return $this->delete($param, $objParam);
        //        $id = $param['id'];
        //        $obj = $this->model::where('id', $id)->first();
        //        $obj->delete();
        //        return response()->json(['payload' => 1]);
    }
}
