<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\Data;
use App\Models\GiaPha;
use App\Models\GiaPha_Meta;
use App\Models\GiaPhaUser;
use App\Models\MyTreeInfo;
use App\Models\TreeMngColFix;
use App\Repositories\TreeMngRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use LadLib\Common\UrlHelper1;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;

class TreeMngControllerApi extends BaseApiController
{
    public function __construct(TreeMngRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function get_list_node_full_info(Request $request)
    {
        $uid = getCurrentUserId();

        $isVip = 0;
        if($uid){
            //Xem user đã mua VIP chưa
            if($bill = \App\Models\OrderItem::where(['user_id' => $uid])->first()){
                $isVip = 1;
            }
        }
//
//        if(!$isVip)
//            die("Tính năng này chỉ dành cho thành viên VIP - Đã nạp phí. Nếu bạn chưa nạp phí, hãy nạp phí để sử dụng tính năng này.");

        $idList = $request->id_list;
        $mId = [];
        if($idList)
        foreach ($idList AS $one){
            $mId[] = qqgetIdFromRand_($one);
        }
//        $mm = GiaPha::whereIn('id', $mId)->get()->toArray();
        $ids = implode(',', $mId);
//        $mm = GiaPha::whereIn('id', $mId)->where('user_id', $uid)->orderByRaw("FIELD(id, $ids)")->get()->toArray();


        if($idList && !$ids)
            die("Not valid ids");

        if($ide = $request->isWord) {

            $idf = qqgetIdFromRand_($ide);
            //Kiêm tra quyền tải xuong:
            if(!$obj = GiaPha::find($idf)){
                die("Không tìm thấy ID: $ide");
            }

            if(!isSupperAdmin_())
            if($obj->user_id != $uid){
                die("Không phải cây của bạn: $ide");
            }

            $obj = new GiaPha();

            $mm = $obj->getAllTreeDeep($request->isWord);

            $mm2 = [];
            if($mm)
            foreach ($mm AS $m1){
                $o2 = GiaPha::find($m1['id']);
                $mm2[] = $o2;
            }

            $mm = $mm2;
//
//            dump($mm);
//            return;
        }
        else
            $mm = GiaPha::whereIn('id', $mId)->where('user_id', $uid)->orderByRaw("FIELD(id, $ids)")->get();

        if(!$mm || count($mm) == 0){
//
//            print_r($mId);
            die("Danh sách rỗng? $uid / $ids");
        }

        $firstMember = $mm[0] ?? '';

        if(!$firstMember) {
            die("Không tìm thấy thành viên đầu tiên?");
        }

        $nameFirst = $firstMember->name;

        $mFieldAllow = [
            'name',
            'title',
            'image_info',
            'id',
            'parent_id',
            'child_of_second_married',
            'married_with',
            'cac_con',
            'dau_re',
            'home_address',
            'orders',
            'child_type',
            'gender',
            'birthday',
            'date_of_death',
            'place_birthday',
            'place_heaven',

//            'last_name',
//            'sur_name',
            'phone_number',
            'email_address',
            'created_at',
            'updated_at',
            'summary',
            'content',

        ];

        $meta = GiaPha::getMetaObj();
        $mRet = [];
        $mFieldDes = [];
        $mFieldDesAndKey = [];
        foreach ($mFieldAllow AS $fieldOK){
            $mFieldDes[] = $meta->getDescOfField($fieldOK);
            $mFieldDesAndKey[$fieldOK] = $meta->getDescOfField($fieldOK);
        }

        $cc = 0;
        foreach ($mm AS $obj) {
            if($obj instanceof GiaPha);
            if($obj->user_id != $uid)
                continue;

//            $obj = (object)$m1;
            $objRet = new \stdClass();
            foreach ($mFieldAllow as $fieldOK) {
                $tmp = $obj->$fieldOK;
                $tmp = str_replace(",", '_', $tmp);
                $tmp = str_replace("\n", '__', $tmp);
                $tmp = str_replace("\r", '__', $tmp);
                $tmp = str_replace("\t", '___', $tmp);
//                if($obj->$fieldOK && in_array($fieldOK, ['name', 'title', 'home_address','summary',
//                        'content', 'place_birthday', 'place_heaven', 'last_name', 'sur_name', 'phone_number', 'email_address'])){
//                    $objRet->$fieldOK = json_encode($obj->$fieldOK);
//                }
                $objRet->$fieldOK = $tmp;
                if ($obj->$fieldOK && in_array($fieldOK, ['married_with', 'child_of_second_married', 'parent_id', 'id'])) {
                    $objRet->$fieldOK = qqgetRandFromId_($obj->$fieldOK);
                }
            }
            $mRet[] = $objRet;
        }
        $domain = UrlHelper1::getDomainHostName();

//        if(0)
            if($request->isWord){
                // Initialize PhpWord object
                $phpWord = new PhpWord();
// Set default font name and size
                $phpWord->setDefaultFontName('Arial');
                $phpWord->setDefaultFontSize(12);
                $section = $phpWord->addSection();

                $section->addImage(
                    "/var/www/html/public/images/border-banner-bg1/banner151.png",
                    [
                        'width' => '',
                        'height' => 150 // Nếu muốn tự động tính toán chiều cao, có thể để null.
                    ]
                );
//
//                // Tạo section
//                $section = $phpWord->addSection();

// Thêm tiêu đề "Cây Gia phả"
                $section->addText(
                    "Cây Gia phả",
                    ['size' => 20],
                    ['alignment' => Jc::CENTER]
                );

// Thêm liên kết URL
                $section->addLink(
                    "https://$domain",
                    "https://$domain",
                    ['size' => 18],
                    ['alignment' => Jc::CENTER]
                );

// Thêm khoảng cách (3 dòng)
                $section->addTextBreak(3);
//
//                $section = $phpWord->addSection();
//                $html = '<h1>The Description</h1>';
//                \PhpOffice\PhpWord\Shared\Html::addHtml($section, $html);

//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($mRet);
//                echo "</pre>";

                $cc = 0;
//                if(0)
                // Assuming $mRet is the array of objects and $mFieldDes is the array of field descriptions
                foreach ($mm AS $obj) {
                    $cc++;
                    if($obj instanceof GiaPha);

                    if(!$isVip && $cc > 10){
                        $section->addTextBreak(3); // Add a line break between objects
                        $section->addText("Tài khoản chưa tài khoản nạp phí, nên chỉ tải được 10 người đầu tiên", ['bold' => true]);
                        break;
                    }

//                    $obj = (object)$m1;
                    foreach ($mFieldDesAndKey as $key => $fieldName) {
                        $fieldValue = isset($obj->$key) ? $obj->$key : '';
//                        if ($fieldValue)
                        {


                            $orgVal = $fieldValue;
                            $orgVal = strip_tags($orgVal);
                            $orgVal = trim($orgVal);
                            if($key == 'id'){
                                $orgVal = qqgetRandFromId_($orgVal);
                            }

                            if($key == 'image_info'){
                                if($filePathImg = $obj->getFirstImageFilePath()) {
                                    $section->addTextBreak(1); // Add a line break between objects
                                    // Add an image to the section
                                    $section->addImage($filePathImg, [
                                        'width' => '',
                                        'height' => 300,
                                    ]);
                                    $section->addTextBreak(1); // Add a line break between objects
                                }
                            }

                            if($key == 'created_at' || $key == 'updated_at'){
                                $orgVal = date('d/m/Y', strtotime($fieldValue));
                            }

                            if($key == 'dau_re') {
                                $orgVal = '';
                                if($mCon = $obj->getCacCon()){
                                    foreach ($mCon AS $con) {
                                        if($con->married_with)
                                            $orgVal .= $con->name. "; ";
                                    }
                                }
                                $fieldName = "Dâu/Rể";

                            }

                            if($key == 'cac_con') {
                                $orgVal = '';
                                if($mCon = $obj->getCacCon()){

                                    foreach ($mCon AS $con) {
                                        if($con->married_with)
                                            continue;

                                        $orgVal .= $con->name. "; ";
                                    }
                                }
                                $fieldName = "Các con";

                            }

//                            if(0)
                            if($key == 'child_of_second_married'){

                                //Neu khong khai bao Con cua ai, thì tìm Vo chong cua Bo
                                if(!$obj->child_of_second_married){
                                    //Tim boMe cua Obj
                                    if($boMe = $obj->getBoMe() ?? ''){
                                        //TTìm vơ chong dau tien cua bo me
                                        if($vcs = $boMe->getCacVoChong()??'') {
                                            //Tim gioi tinh cua obj2
                                            if($orgVal = ($vcs[0]?->name ?? ''))
                                            if ($vcs[0]->gender == 1) {
                                                $fieldName = "Cha";
                                            } else
                                                $fieldName = "Mẹ";
                                        }
                                    }
                                }
                                else{
                                    //Nêu có khai báo con của ai, thì tìm người đó, và xem có đúng cưới ko:
                                    if($boMe = GiaPha::find($obj->child_of_second_married)){
                                        if($boMe->married_with == $obj->parent_id){
                                            $orgVal = $boMe->name;
                                            //Tim gioi tinh cua obj2
                                            if($boMe->gender == 1)
                                                $fieldName = "Cha";
                                            else
                                                $fieldName = "Mẹ";
                                        }
                                    }

                                }
                            }


                            if($key == 'married_with') {
                                if($obj->gender == 2)
                                    $fieldName = "Chồng";
                                else
                                    $fieldName = "Vợ";
                                $orgVal = "";
                                //Tim cac vo chong
                                if($mVC = $obj->getCacVoChong()){
                                    foreach ($mVC AS $vc) {
                                        $orgVal .= $vc->name. "; ";
                                    }
                                }
                            }

                            if($key == 'parent_id') {
                                if($bm = $obj->getBoMe()){
                                    $orgVal = $bm->name;
                                    if($bm->gender == 1)
                                        $fieldName = "Cha";
                                    if($bm->gender == 2)
                                        $fieldName = "Mẹ";
                                }
                            }

                            if($key == 'gender'){
                                if($fieldValue == 2){
                                    $orgVal = 'Nữ';
                                }
                                else
                                    $orgVal= 'Nam';
                            }
                            if($key == 'child_type'){
                                if(!$obj->child_type)
                                    continue;
                                if($obj->gender == 2){
                                    $orgVal = 'Con Dâu';
                                }
                                else
                                    $orgVal= 'Con Rể';
                            }

                            if(!$fieldName || !$orgVal)
                                continue;


                            $textRun = $section->addTextRun();
                            if($key == 'name'){
                                $section->addTitle("Thành viên: $orgVal", 1);
                            }
                            else
                            {
                                $textRun->addText("$fieldName: ");
                                $textRun->addText("$orgVal");
                            }
//                            echo "<br/>\n $fieldName: $orgVal";
                        }
                    }

//                    $textRun = $section->addTextRun();

//                    echo "<br/>\n $fieldName: $orgVal <br>";

                    $section->addTextBreak(1); // Add a line break between objects

                }

//                die();
                // Save the document
                ob_clean();
                $writer = IOFactory::createWriter($phpWord, 'Word2007');
//                $writer->save('/share/output1.docx');
                //xuâất ra cho user tải :
                header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
//                header('Content-Disposition: attachment;filename="Thành viên cây - '.$nameFirst.'.docx"');
                header("Content-Disposition: attachment;filename=\"Thành viên cây - $nameFirst - $cc Người.docx\"");
                header('Cache-Control: max-age=0');
                $writer->save('php://output');

//                $writer = IOFactory::createWriter($phpWord, 'HTML');
//                $writer->save('/share/output1.html');

                die();
            }

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($mRet);
//        echo "</pre>";

        ob_clean();
        if(!$mRet)
            return rtJsonApiDone("Không có dữ liệu của bạn?", '', -2);
        return rtJsonApiDone($mRet, "DONE get all member full info", 1, $mFieldDes);
    }


    public function tree_index(Request $request)
    {
        //        $this->objParamEx->module = 'member';
        //        $this->objParamEx->need_set_uid = getUserIdCurrentInCookie();

        if ((new GiaPha_Meta())->isUseRandId()) {
            if (isset($request->pid)) {
                if (! $request->pid) {
                    //return rtJsonApiError("Not valid PID ($request->pid)");
                }
                if ($request->pid) {
                    if (is_numeric($request->pid)) {
                        return rtJsonApiError("Not valid PID Number ($request->pid)");
                    }

                    $pid = qqgetIdFromRand_($request->pid);
                    if (! is_numeric($pid)) {
                        return rtJsonApiError("Not valid PID ($request->pid)");
                    }
                }
            }
        }

        return parent::tree_index($request); // TODO: Change the autogenerated stub
    }

    public function add(Request $request)
    {
        if ($ret = GiaPhaUser::checkQuota(Auth::id(), 'Không thể thêm vì ')) {
            return $ret;
        }

        if ($request->married_with) {
            GiaPha::getMetaObj()->deleteCachePublicTree($request->married_with);
        }
        if ($request->parent_id) {
            GiaPha::getMetaObj()->deleteCachePublicTree($request->parent_id);
        }

        return parent::add($request);
    }

    public function update($id, Request $request)
    {

        if (isset($request->col_fix) && $request->with_pid) {
            //            if(!isSupperAdmin_()){
            //                return rtJsonApiError("Hệ thống đang chỉnh sửa phần chỉnh cột, mời bạn chỉnh lại sau 11h ngày hôm nay 15.4.2023");
            //            }
            $pidx = qqgetIdFromRand_($request->with_pid);
            //            if($col = TreeMngColFix::where(['pid'=>qqgetIdFromRand_($request->with_pid), 'node_id' => qqgetIdFromRand_($id)])->first()){
            //                $uid = getCurrentUserId();
            //                //PID phải là của user mới có thể chỉnh cột
            //                //Không cần id là của user, vì id đó có thể gắn từ node của user khác
            //                $gp = GiaPha::find(qqgetIdFromRand_($request->with_pid));
            //                if(!$gp || $gp->user_id!=$uid)
            //                    return rtJsonApiError("Not found data or PID not belong your account? $uid");
            //                $col->pid = qqgetIdFromRand_($request->with_pid);
            //                $col->col_fix = qqgetIdFromRand_($request->col_fix);
            //                $col->update();
            //            }
            //            else{
            //                $col = new TreeMngColFix();
            //                $col->pid = qqgetIdFromRand_($request->with_pid);
            //                $col->node_id = qqgetIdFromRand_($id);
            //                $col->col_fix = qqgetIdFromRand_($request->col_fix);
            //                $col->save();
            //            }

            GiaPha_Meta::updateColFix(qqgetIdFromRand_($id), qqgetIdFromRand_($request->with_pid), $request->col_fix);
            GiaPha::getMetaObj()->deleteCachePublicTree($pidx);

            return rtJsonApiDone(' done fixcol!');
        }

        if ($request->clear_cache) {
            //Xóa cache
            GiaPha::getMetaObj()->deleteCachePublicTree($request->id);

            return rtJsonApiDone(' clear_cache done!');
        }

        if ($request->clear_col_fix_all) {



            GiaPha::getMetaObj()->deleteCachePublicTree($request->id);

            if($mti = MyTreeInfo::where('tree_id', qqgetIdFromRand_($request->id))->first()){
                $mti->tree_nodes_xy = '';
                $mti->minX = 0;
                $mti->minY = 0;
                $mti->save();
//                die("xxxxx2 $request->id");
            }


            //Chỉ cần xóa của PID là xong
            TreeMngColFix::where('pid', qqgetIdFromRand_($request->id))->delete();

            //            $meta = GiaPha::getMetaObj();
            //            $obj = new GiaPha();
            //            $allMem = $obj->getAllTreeDeep($request->id);
            //            if($allMem){
            //                foreach ($allMem AS $obj){
            //                    $mmId[] = $obj['id'];
            //                    $meta->deleteCachePublic($obj['id']);
            //                    $meta->deleteCachePublic(qqgetRandFromId_($obj['id']));
            //                }
            //                GiaPha::whereIn('id', $mmId)->update(['col_fix'=>null]);
            //            }

            return rtJsonApiDone(' clear_col_fix_all done!');
        }

        if ($request->id) {

            //Kiểm tra thay đổi

            $fid = $request->id;
            if (! is_numeric($fid)) {
                $fid = qqgetIdFromRand_($fid);
            }
            if (is_numeric($fid)) {
                if ($objFound = GiaPha::find($fid)) {
                    if ($objFound->married_with) {
                        GiaPha::getMetaObj()->deleteCachePublic(qqgetRandFromId_($objFound->married_with));
                        GiaPha::getMetaObj()->deleteCachePublic(($objFound->married_with));
                    }

                    if ($request->stepchild_of) {
                        $request['stepchild_of'] = qqgetIdFromRand_($request->stepchild_of);
                    }

                    if(isDebugIp()){
//                        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                        print_r($request->all());
//                        echo "</pre>";
//                        die();
                    }

                    //Kiểm tra mẹ phải là vợ của bố:
                    if ($request->child_of_second_married) {

                        //Nếu là chính bố, thì gán là Null, chỉ có tác dụng khi là con riêng
                        if(qqgetIdFromRand_($request->child_of_second_married) == $objFound->parent_id){
                            $request['child_of_second_married'] = null;
                        }
                        else{

                            if (! $objSP = GiaPha::find(qqgetIdFromRand_($request->child_of_second_married))) {
                                return rtJsonApiError("Not found spouse: $request->child_of_second_married");
                            }


                            if ($objSP->married_with != $objFound->parent_id) {
                                return rtJsonApiError("Bố/mẹ không hợp lệ: $objSP->name ($request->child_of_second_married)");
                            }
                        }
                    }

                }
            }
        }

        if ($request->link_remote) {
            try {
                //kiểm tra validate
                $mid = explode(',', $request->link_remote);
                foreach ($mid as $fid0) {
                    $fid = trim($fid0);
                    if (! $fid) {
                        continue;
                    }
                    $fid = qqgetIdFromRand_($fid0);
                    if (! $objLink = GiaPha::find($fid)) {
                        return rtJsonApiError("Mã Liên kết ID không đúng, không có id này: $fid0");
                    }
                }
            } catch (\Throwable $e) { // For PHP 7
                return rtJsonApiError('Có lỗi Liên kết ID: '.$e->getMessage());
            } catch (\Exception $e) {
                return rtJsonApiError('Có lỗi Liên kết ID: '.$e->getMessage());
            }
        }


        //        die("xxx:" . $id);

        //Kiểm tra, nếu có thay đổi mới xóa cache:
        if (isset($objFound)) {
            $mObjDb = $objFound->toArray();
            $haveChange = 0;
            $pr0 = $request->toArray();
            if ($pr0) {
                foreach ($mObjDb as $key => $val) {
                    if (isset($pr0[$key]) && $pr0[$key] != $val) {
                        $haveChange = 1;
                        break;
                    }
                }


                if ($haveChange) {
                    //Có thay đổi nên change cache!
                    GiaPha::getMetaObj()->deleteCachePublicTree($request->id);
                }
            }
        }

        return parent::update($id, $request); // TODO: Change the autogenerated stub
    }

    public function update_multi(Request $request)
    {

        if ($m1 = $request->field_name_to_change2) {
            $pid = qqgetIdFromRand_($request->pid_root);
            foreach ($m1 as $fid => $newCol) {
                GiaPha_Meta::updateColFix(qqgetIdFromRand_($fid), $pid, $newCol);
            }
            GiaPha::getMetaObj()->deleteCachePublicTree($pid);

            return rtJsonApiDone('update_multi colfix done!');
        }

        GiaPha::getMetaObj()->deleteCachePublicTree($request->get('id_list'));
        GiaPha::getMetaObj()->deleteCachePublicTree($request->get('move_to_parent_id'));
        GiaPha::getMetaObj()->deleteCachePublicTree($request->get('id'));

        return parent::update_multi($request); // TODO: Change the autogenerated stub
    }

    public function delete(Request $request)
    {

        GiaPha::getMetaObj()->deleteCachePublicTree($request->id);

        return parent::delete($request);
    }

    public function un_delete(Request $request)
    {
        if ($ret = GiaPhaUser::checkQuota(Auth::id(), 'Không thể khôi phục vì ')) {
            return $ret;
        }

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($request->all());
        //        echo "</pre>";
        //        die();

        $ret = parent::un_delete($request);

        //Phải để sau api, nếu ko thì ko thấy id để xly:
        GiaPha::getMetaObj()->deleteCachePublicTree($request->id);

        return $ret;
    }

    public function tree_create(Request $request)
    {
        GiaPha::getMetaObj()->deleteCachePublicTree($request->id);

        return parent::tree_create($request);
    }

    public function tree_move(Request $request)
    {
        GiaPha::getMetaObj()->deleteCachePublicTree($request->id);
        GiaPha::getMetaObj()->deleteCachePublicTree($request->to_id);

        return parent::tree_move($request);
    }

    public function tree_save(Request $request)
    {
        GiaPha::getMetaObj()->deleteCachePublicTree($request->id);

        return parent::tree_save($request);
    }

    public function tree_delete(Request $request)
    {
        (new GiaPha_Meta())->deleteCachePublicTree($request->id);

        return parent::tree_delete($request);
    }
}
