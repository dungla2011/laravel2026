<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class QuizUserAndTest_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/quiz-user-and-test';

    protected static $web_url_admin = '/admin/quiz-user-and-test';

    protected static $api_url_member = '/api/member-quiz-user-and-test';

    protected static $web_url_member = '/member/quiz-user-and-test';

    //public static $folderParentClass = QuizUserAndTestFolderTbl::class;
    public static $modelClass = QuizUserAndTest::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //QuizUserAndTest edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function isDateTimeType($field)
    {
        if (in_array($field, ['open_answer_time', 'close_answer_time'])) {
            return 1;
        }

        return 0;
    }

    public function _session_id($obj, $val, $field)
    {

        if ($qs = QuizSessionInfoTest::find($val)) {

            $strDo = '';
            if (Helper1::isMemberModule()) {
                $str = 'Làm bài Kiểm tra';
                if ($qs->end_time_do > nowyh()) {

                } else {
                    $str .= ' - Đã hết giờ làm';
                }
                $strDo = "<a href='/member/quiz-user-and-test/doTest?testIdOfUser=$obj->id'> <button type='button' class='btn btn-primary btn-sm' style=''> $str </button> </a> <br>";

                $strDo = "<a href='/member/quiz-user-and-test/doTest?sid=$obj->session_id'> <button type='button' class='btn btn-primary btn-sm' style=''> $str </button> </a> <br>";
            }

            $padEndTime = '; color: blue ;';
            $padHetGio = '';
            if ($qs->end_time_do < nowyh()) {
                $padEndTime = '; color: gray ;';
                //                $padHetGio = "(Hết giờ làm)";
            }

            $info = " <span style='font-size: small $padEndTime'> $padHetGio Bắt đầu: $qs->start_time_do | Kết thúc: $qs->end_time_do <br> Mở đáp án: $qs->open_answer_time -> $qs->close_answer_time </span> ";

            if (Helper1::isAdminModule()) {
                return "$strDo <a target='_blank' href='/admin/quiz-session-info-test/edit/$qs->id'> <b> $qs->name </b></a> <br> $info";
            }

            $qTest = QuizTest::find($obj->test_id)->name ?? ' Not found test name?';

            return "$strDo  <b>Phiên: $qs->name </b> <br> $qTest <br> $info";
        }

    }

    public function extraHtmlIncludeEdit1()
    {

        echo "<br/>\n";
        echo "<button class='btn btn-info' id='reset_bai_lam'> RESET </button>";

        ?>


        <?php

    }

    public function _point($obj)
    {
        return "<div style='text-align: center; padding: 5px; ' class='btn-sm btn-info'> $obj->point / 100 </div>";
    }

    public function _name($obj)
    {
        if (Helper1::isMemberModule()) {
            if (! $obj->end_time) {
                //                return "<a href='/member/quiz-user-and-test/doTest?testIdOfUser=$obj->id'> <button type='button' class='btn btn-primary btn-sm' style='margin-left: 10px'> Làm bài test </button> </a> ";
            }
        }
    }

    public function _percent_do($obj)
    {

        return " <div class='btn btn-sm btn-success w-100'> $obj->percent_do % </div> ";

    }

    public function _user_id($obj)
    {

        if ($obj->user_id) {
            if ($u = User::find($obj->user_id)) {
                return "<div style='text-align: left; font-size: small; padding: 5px; ' class=''><b> $u->name </b> <br/> $u->email </div>";
            }
        }

    }

    public function _test_id($obj)
    {
        //        if(Helper1::isMemberModule())

        $testId = $obj->test_id;
        if ($tobj = QuizTest::where(['id' => $testId])->first()) {
            if ($mm = QuizTestQuestion::where(['test_id' => $testId])->get()) {
                $mm = $mm->toArray();

                return " <div style='font-size: small; padding: 5px; margin: 5px'> <a target='_blank' href='/admin/quiz-test/edit/$tobj->id'> <b> $tobj->name </b> <br> Số câu hỏi: ".count($mm).' </a> </div>';
            }
        }

    }

    public function extraJsIncludeEdit($objData = null)
    {

        ?>
        <script>
            $(function (){
                $("#reset_bai_lam").on("click", function (){

                    console.log(" REsset bai tap");

                    let text = "Bạn có chắc chắn Xóa hết kết qua bài làm?";
                    if (confirm(text) == true) {
                    } else {
                        return;
                    }

                    let testId = '<?php echo request('id') ?>';
                    let user_token = jctool.getCookie('_tglx863516839');

                    let url = "/api/quiz-test/addUserToTest";
                    url = "/api/quiz-test/resetBaiKiemTra";
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {testId: testId},
                        async: false,
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                        },
                        success: function (data, status) {
                            hideWaittingIcon();
                            console.log("Data ret: ", data, " \nStatus: ", status);

                            if (data.payload){
                                showToastInfoTop(data.payload);

                            }
                            else{
                                alert("Có lỗi: " + JSON.stringify(data))
                            }
                        },
                        error: function (data) {
                            hideWaittingIcon();
                            console.log(" DATAx " , data);
                            if(data.responseJSON && data.responseJSON.message)
                                alert('Error call api: ' + "\n" + data.responseJSON.message)
                            else
                                alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0,1000));
                        }
                    });
                })

            })

        </script>
        <?php
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {

        if (Helper1::isMemberModule()) {
            return;
        }

        ?>

        <div style="border: 1px dashed #ccc; padding: 10px; margin-bottom: 10px">

            <button class="btn btn-primary" id="add_test"> ADD User/Test</button>
        </div>

        <div id="dialog-select-test" title="Thêm Học viên vào Phiên kiểm tra" style="display: none; padding: 10px 20px">

            <p>Tên Phiên kiểm tra:
                <!--                <input class="form-control" id="new_test_name" type="text">-->

                <select name="" id="select_session_quiz" class="form-control">
                    <option value=""> -- Select --</option>
                    <?php

                    $mm = QuizSessionInfoTest::all()->toArray();
        $mm = array_reverse($mm);
        foreach ($mm as $obj) {
            $obj = (object) $obj;
            echo "<option value='$obj->id'> $obj->name </option>";
        }

        ?>

                </select>
            </p>


            <p>Tên Bài kiểm tra:
<!--                <input class="form-control" id="new_test_name" type="text">-->

                <select name="" id="select_quiz" class="form-control">
                    <option value=""> -- Select --</option>
                    <?php

        $mm = QuizTest::all()->toArray();
        $mm = array_reverse($mm);
        foreach ($mm as $obj) {
            $obj = (object) $obj;
            echo "<option value='$obj->id'> $obj->name </option>";
        }

        ?>

                </select>
            </p>


            <p>Thành viên thuộc Lớp học:
                <!--                <input class="form-control" id="new_test_name" type="text">-->

                <select name="" id="select_class_quiz" class="form-control">
                    <option value=""> -- Select --</option>
                    <?php

        $mm = QuizClass::all()->toArray();
        $mm = array_reverse($mm);
        foreach ($mm as $obj) {
            $obj = (object) $obj;
            $n = QuizUserClass::where('parent_id', $obj->id)->count();
            echo "<option value='$obj->id'> $obj->name ($n thành viên) </option>";

        }

        ?>

                </select>
            </p>


            <p></p>
            Hoặc Danh sách User email (cách nhau bằng dấu phẩy):
            <textarea class="form-control" style="width: 100%; min-height: 100px" id="user_list"></textarea>
            <p></p>
            <div style="float: right">
                <button class="btn btn-primary" id="add_to_test"> Thêm</button>
                <button class="btn btn-default" id="close_add"> Bỏ qua</button>
            </div>
        </div>




    <?php

    }

    public function extraCssInclude()
    {
        ?>
        <style>
            [data-table-field='session_id'] {
                padding-left: 10px;
            }
        </style>

        <?php
    }

    public function extraJsInclude()
    {
        ?>
        <script>

            $(function (){
                $("#add_to_test").on('click', function (){

                    let testId = $("#select_quiz").val();
                    let listUser = $("#user_list").val();
                    let select_session_quiz = $("#select_session_quiz").val();
                    let select_class_quiz = $("#select_class_quiz").val();



                    console.log(".....", testId, listUser);
                    let user_token = jctool.getCookie('_tglx863516839');

                    let url = "/api/quiz-test/addUserToTest";
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {testId: testId, select_class_quiz: select_class_quiz, listUserId: listUser, select_session_quiz: select_session_quiz},
                        async: false,
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                        },
                        success: function (data, status) {
                            hideWaittingIcon();
                            console.log("Data ret: ", data, " \nStatus: ", status);

                            if (data.payload){
                                showToastInfoTop(data.payload);

                            }
                            else{
                                alert("Có lỗi: " + JSON.stringify(data))
                            }
                        },
                        error: function (data) {
                            hideWaittingIcon();
                            console.log(" DATAx " , data);
                            if(data.responseJSON && data.responseJSON.message)
                                alert('Error call api: ' + "\n" + data.responseJSON.message)
                            else
                                alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0,1000));
                        }
                    });
                    // $("#dialog-select-test").dialog("close");
                })

                $("#close_add").on('click', function (){
                    console.log(".....");
                    $("#dialog-select-test").dialog("close");
                })

                $("#add_test").on('click', function (){
                    console.log(".....");
                    $("#dialog-select-test").dialog("open");
                })

                $("#dialog-select-test").dialog({
                    resizable: true,
                    height: "auto",
                    autoOpen: false,
                    width: 600,
                    modal: true,
                    open: function (event, ui) {
                        console.log('opened ...')
                    },
                });

            })
        </script>
<?php
    }

    //...
}
