<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\QuizChoice;
use App\Models\QuizClass;
use App\Models\QuizQuestion;
use App\Models\QuizSessionInfoTest;
use App\Models\QuizTest;
use App\Models\QuizTestQuestion;
use App\Models\QuizUserAndTest;
use App\Models\QuizUserClass;
use App\Models\User;
use App\Repositories\QuizTestRepositoryInterface;
use Illuminate\Http\Request;

class QuizTestControllerApi extends BaseApiController
{
    public function __construct(QuizTestRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function doTestPostResult(Request $request)
    {

        if (! $testIdOfUser = $request->testIdOfUser) {
            return rtJsonApiError('Not testIdOfUser?');
        }
        if (! $qid = $request->qid) {
            return rtJsonApiError('Not testIdOfUser?');
        }
        if (! $ans = $request->ans) {
            return rtJsonApiError('Not answer?');
        }

        if (strlen(serialize($ans)) > 300) {
            return rtJsonApiError('Câu trả lời quá dài, lớn hơn 300 ký tự');
        }

        $ans = implode("\n", $ans);

        $uid = getUserIdCurrentInCookie();

        $testObjOfUser = QuizUserAndTest::find($testIdOfUser);

        //Kiểm tra testIdOfUser có thuộc userid không
        if ($testObjOfUser->user_id != $uid) {
            return rtJsonApiError('Not your sessionTest id?');
        }

        //Kiểm tra xem bài test này đã hết hạn chưa:

        if (! $testObjOfUser->session_id) {
            return rtJsonApiError('have not session id for this test of user?');
        }

        $oSession = QuizSessionInfoTest::find($testObjOfUser->session_id);
        if ($oSession->end_time_do) {
            if ($oSession->end_time_do < nowyh()) {
                return rtJsonApiError("Đã hết giờ làm: $oSession->end_time_do");
            }
        }

        //Kiểm tra qid có thuộc testIdOfUser không:
        //Lấy qtest ra, rồi lấy các qid của của qtest
        if (! $qObj = QuizTest::find($testObjOfUser->test_id)) {
            return rtJsonApiError('Not found test id?');
        }

        $mQTest = QuizTestQuestion::where('test_id', $testObjOfUser->test_id)->get();
        $mQId = [];
        $totalQ = 0;
        $totalDone = 0;
        foreach ($mQTest as $obj) {
            $mQId[$obj->question_id] = $obj->question_id;
            $totalQ++;
        }

        if (! in_array($qid, $mQId)) {
            return rtJsonApiError('Not valid QID, QID not in session test ?');
        }
        if (! $testObjOfUser->obj_result) {
            $testObjOfUser->obj_result = json_encode([$qid => $ans]);
        }
        $mAns = (array) json_decode($testObjOfUser->obj_result);
        $mAns[$qid] = $ans;

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        var_dump($mAns);
        //        echo "</pre>";
        //        die($qid . " " . $ans);

        $testObjOfUser->obj_result = json_encode($mAns);
        if ($testObjOfUser instanceof QuizUserAndTest);

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($mQ->toArray());
        //        echo "</pre>";
        //
        //        die();

        //Kiểm tra lại tất cả câu trả lời, để tính điểm
        $nTrue = 0;
        foreach ($mQId as $qid) {
            if ($mAns[$qid] ?? '') {

                if ($qReal = QuizQuestion::find($qid)) {
                    $qReal->answer = trim($qReal->answer);
                    $mAns[$qid] = trim($mAns[$qid]);

                    if ($mAns[$qid]) {
                        $totalDone++;
                    }

                    if ($qReal->answer == $mAns[$qid]) {
                        $nTrue++;
                    }
                }
            }
        }
        $testObjOfUser->addLog("point = $nTrue / $totalQ, Set QID answer $qid: $ans");

        if (! $totalQ) {
            $totalQ = 1;
        }
        $testObjOfUser->percent_do = round(100 * $totalDone / $totalQ);
        $testObjOfUser->count_post++;
        $testObjOfUser->point = number_format((float) (100 * $nTrue / $totalQ), 1, '.', '');
        $testObjOfUser->save();

        return rtJsonApiDone('DONE?');

    }

    public function postChoiceOfQues(Request $request)
    {
        //
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($request->all());
        //        echo "</pre>";

        $mIdRet = [];

        if ($dataPost = $request->dataPost) {

            $mCheck = [];
            foreach ($dataPost as $one) {
                $choice_input_value = $one['choice_input_value'];
                if (in_array($choice_input_value, $mCheck)) {
                    return rtJsonApiError("Có lỗi trùng đáp án: '$choice_input_value' \nĐáp án không được giống nhau!");
                }
                $mCheck[] = $choice_input_value;
            }

            foreach ($dataPost as $one) {
                $choiceId = $one['choiceId'];
                $enable = $one['enable'];
                if ($enable == 'false') {
                    $enable = 0;
                }
                if ($enable == 'true') {
                    $enable = 1;
                }

                $right_choice = $one['right_choice'];
                if ($right_choice == 'false') {
                    $right_choice = 0;
                }
                if ($right_choice == 'true') {
                    $right_choice = 1;
                }

                $choice_input_value = $one['choice_input_value'];
                $choice_input_text = $one['choice_input_text'];

                if (isset($mIdRet[$choice_input_value])) {
                    return rtJsonApiError("Trùng giá trị đáp án: $choice_input_value");
                }

                if ($choiceId) {
                    //$mIdRet[$choice_input_value] = intval($choiceId);
                    if ($qz = QuizChoice::find($choiceId)) {
                        $qz->value = $choice_input_value;
                        $qz->value_richtext = $choice_input_text;
                        $qz->enable = $enable;
                        $qz->is_right_choice = $right_choice;
                        $qz->update();
                    }
                } else {
                    $qz = new QuizChoice();
                    $qz->value = $choice_input_value;
                    $qz->value_richtext = $choice_input_text;
                    $qz->enable = $enable;
                    $qz->is_right_choice = $right_choice;
                    $qz->question_id = $request->qid;
                    $qz->save();

                    //  echo "\n ID save = $qz->id ";
                    $mIdRet[$choice_input_value] = intval($qz->id);
                }
                //echo "\n  $choiceId / $enable / $right_choice / $choice_input_value / $choice_input_text";
            }

            return rtJsonApiDone(['array_id' => $mIdRet]);
        }
    }

    public function addUserToClass(Request $request)
    {
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($request->all());
        //        echo "</pre>";
        if ($classId = $request->select_class) {
            if (! $objClass = QuizClass::find($classId)) {
                return rtJsonApiError("Not found tid : $classId?");
            }

            $listUid = $request->listUserId;
            $listUid = trim(trim($listUid, ','));
            $mUidEmail = explode(',', $listUid);
            $doneBefore = $cc = 0;

            if ($mUidEmail) {
                foreach ($mUidEmail as $email) {
                    $email = trim($email);
                    if (! $email) {
                        continue;
                    }
                    if ($user = User::where(['email' => $email])->first()) {
                        if ($lastQ = QuizUserClass::where(['parent_id' => $classId, 'user_id' => $user->id])->orderBy('id', 'DESC')->first()) {
                            $doneBefore++;
                            //                              return rtJsonApiError("Bài test mới được thêm cho user này vào lúc: $lastQ->created_at");
                            //                            if($lastQ->created_at > nowyh(time() - 600)){
                            //                                return rtJsonApiError("Bài test mới được thêm cho user này vào lúc: $lastQ->created_at");
                            //                            }
                        } else {
                            $cc++;
                            $q = new QuizUserClass();
                            $q->parent_id = $classId;
                            $q->user_id = $user->id;
                            $q->save();
                        }
                    }
                }
            }

            if (! $cc) {
                if ($doneBefore) {
                    return rtJsonApiError("0 User thêm thành công, $doneBefore user đã được thêm vào bài test này từ trước!");
                }

                return rtJsonApiError('Không có user nào được thêm!');
            } else {
                return rtJsonApiDone("Có $cc user được thêm vào lớp $objClass->name ($classId)!");
            }
        }
        else
            loi("Bạn chưa chọn lớp?");

        return rtJsonApiError('Not valid param request?');
    }

    public function resetBaiKiemTra(Request $request)
    {
        $uid = getUserIdCurrentInCookie();
        if (! $uid) {
            return rtJsonApiError('Need login?');
        }

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($request->all());
        //        echo "</pre>";
        if ($tid = $request->testId) {
            $uid = getUserIdCurrentInCookie();
            if (! $obj = QuizUserAndTest::find($tid)) {
                return rtJsonApiError("Not found tid : $tid?");
            }
            if ($obj->user_id != $uid) {
                return rtJsonApiError('Not valid user?');
            }

            $obj->obj_result = '';
            $obj->percent_do = 0;
            $obj->point = 0;
            $obj->count_post = 0;

            $obj->addLog('reset ket qua');
            $obj->save();

            return rtJsonApiDone('DONE');

        }

    }

    public function addUserToTest(Request $request)
    {
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($request->all());
        //        echo "</pre>";
        if ($tid = $request->testId) {
            if (! QuizTest::find($tid)) {
                return rtJsonApiError("Not found tid : $tid?");
            }

            $listUid = $request->listUserId;
            $listUid = trim(trim($listUid, ','));
            $select_session_quiz = trim($request->select_session_quiz);
            $select_class_quiz = trim($request->select_class_quiz);
            $mUidEmail = explode(',', $listUid);
            $doneBefore = $cc = 0;

            //Tìm all user trong Class
            $mUClass = QuizUserClass::where('parent_id', $select_class_quiz)->get();
            $mUidInClass = [];

            foreach ($mUClass as $cls) {
                $mUidInClass[] = $cls->user_id;
            }
            //Mảng useremail nếu có nhập:
            foreach ($mUidEmail as $email) {
                $email = trim($email);
                if (! $email) {
                    continue;
                }
                if ($user = User::where(['email' => $email])->first()) {
                    $mUidInClass[] = $user->id;
                }
            }

            //            if($mUidEmail)
            //                foreach ($mUidEmail AS $email)

            //All user cần thêm vào:
            if ($mUidInClass) {
                foreach ($mUidInClass as $uid) {
                    //                    $email = trim($email);
                    //                    if(!$email)
                    //                        continue;
                    //                    if($user = User::where(['email'=>$email])->first())

                    if ($lastQ = QuizUserAndTest::where(['session_id' => $select_session_quiz, 'test_id' => $tid, 'user_id' => $uid])->orderBy('id', 'DESC')->first()) {
                        $doneBefore++;
                        //                              return rtJsonApiError("Bài test mới được thêm cho user này vào lúc: $lastQ->created_at");
                        //                            if($lastQ->created_at > nowyh(time() - 600)){
                        //                                return rtJsonApiError("Bài test mới được thêm cho user này vào lúc: $lastQ->created_at");
                        //                            }
                    } else {
                        $cc++;
                        $q = new QuizUserAndTest();
                        $q->test_id = $tid;
                        $q->user_id = $uid;
                        $q->session_id = $select_session_quiz;
                        $q->save();
                    }

                }
            }

            if (! $cc) {
                if ($doneBefore) {
                    return rtJsonApiError("0 Usẻ thêm thành công, $doneBefore user đã được thêm vào bài test này từ trước!");
                }

                return rtJsonApiError('Không có user nào được thêm!');
            } else {
                return rtJsonApiDone("Có $cc user được thêm vào bài test $tid!");
            }
        }

        return rtJsonApiError('Not valid param request?');
    }

    public function postQuestToTest(Request $request)
    {

        if ($request->clear_orders && $request->test_id) {
            $m1 = QuizTestQuestion::where(['test_id' => $request->test_id])->get();
            foreach ($m1 as $obj) {
                $obj->orders = null;
                $obj->update();
            }

            return rtJsonApiDone('Sort done!');
        }

        //Sort
        if ($request->idOrder && $request->test_id) {

            $m1 = QuizTestQuestion::where(['test_id' => $request->test_id])->get();
            foreach ($request->idOrder as $key => $qid) {
                //                echo "<br/>\n $key->$qid";
                foreach ($m1 as $obj) {
                    if ($obj->question_id == $qid) {
                        $obj->orders = $key + 1;
                        $obj->update();
                    }
                }
            }

            return rtJsonApiDone('Sort done!');
        }

        if ($request->list_quest && ($request->list_test || $request->new_test_name)) {
            $list_quest = $request->list_quest;
            $list_test = $request->list_test;

            //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //            print_r($list_quest);
            //            echo "</pre>";
            //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //            print_r($list_test);
            //            echo "</pre>";
            //            die('...');

            $request->new_test_name = trim($request->new_test_name);
            if ($request->new_test_name) {
                //Tạo bài test mới, kiểm tra trùng tên:
                if (QuizTest::where('name', $request->new_test_name)->first()) {
                    return rtJsonApiError("Đã có bài test trùng tên: $request->new_test_name");
                }
                $newTest = new QuizTest();
                $newTest->name = $request->new_test_name;
                $newTest->enable = 1;
                $newTest->save();
                $list_test[] = $newTest->id;
            }

            //            print_r($list_quest);
            //            print_r($list_test);
            //            die();

            $nAdd = 0;
            foreach ($list_quest as $qid) {
                if (! $qid) {
                    continue;
                }

                //Xem Quest đó có active ko:
                $ques = QuizQuestion::find($qid);
                //                if(!$ques->is_active)
                //                    continue;

                foreach ($list_test as $tid) {
                    if (! $tid) {
                        continue;
                    }
                    if (! QuizTestQuestion::where(['question_id' => $qid, 'test_id' => $tid])->first()) {
                        QuizTestQuestion::insert(['question_id' => $qid, 'test_id' => $tid]);
                        $nAdd++;
                    }
                }
            }
            if (! $nAdd) {
                return rtJsonApiError('Không có câu hỏi được thêm!');
            }

            return rtJsonApiDone("Thêm thành công $nAdd câu hỏi!");
        }

        return rtJsonApiError('Có lỗi param API?');
    }

    public function list(): \Illuminate\Http\JsonResponse
    {
        return parent::list(); // TODO: Change the autogenerated stub
    }
}
