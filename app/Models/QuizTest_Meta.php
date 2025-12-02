<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class QuizTest_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/quiz-test';

    protected static $web_url_admin = '/admin/quiz-test';

    protected static $api_url_member = '/api/member-quiz-test';

    protected static $web_url_member = '/member/quiz-test';

    //public static $folderParentClass = QuizTestFolderTbl::class;
    public static $modelClass = QuizTest::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status' || $field == 'enable') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //QuizTest edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function extraJsIncludeEdit($objData = null)
    {

        $arg = func_get_args();
        if (isset($arg[0])) {
            $objData = $arg[0];
        }
        if (isset($arg[1])) {
            $metaArr = $arg[1];
        }

        if (! isset($objData)) {
            return;
        }

        ?>
        <script src="/vendor/lad_tree/clsDragDropSortTree.js"></script>
        <script>
            let user_token = jctool.getCookie('_tglx863516839');

            clsDragDropSortTree.class_name_pad_zone = "before-quiz";
            clsDragDropSortTree.class_name_item = "one-quiz";
            clsDragDropSortTree.root_id = "quiz_zone";
            clsDragDropSortTree.optDisableMoveInsideOther = 1;


            function array_move(arr0, old_index, new_index) {
                let arr = JSON.parse(JSON.stringify(arr0))
                if (new_index >= arr.length) {
                    var k = new_index - arr.length + 1;
                    while (k--) {
                        arr.push(undefined);
                    }
                }
                arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
                return arr; // for testing
            };

            clsDragDropSortTree.callBeforeDrop = function(cmd, id1, id2 ) {

                let ret = 0;

                if(!id2)
                    id2 = -1;

                id1 = parseInt(id1)
                id2 = parseInt(id2)

                console.log(" CMD, id1, id2 = " , cmd, id1, id2);
                let idOrder = [];
                $(".one-quiz").each(function (){
                    // console.log(" IDx = ", this.getAttribute("data-id"));
                    idOrder.push(parseInt(this.getAttribute("data-id")));
                })

                // console.log("xxxxx----");
                let index1 = idOrder.indexOf(id1)
                let index2 = idOrder.indexOf(id2)

                console.log(" index1 , index2" , index1 , index2, idOrder);

                // return 0;


                let idOrder2 = array_move(idOrder, index1, index2 + 1);
                // let idOrder2 = idOrder.move1(index1, index2 + 1);

                if(JSON.stringify(idOrder2) === JSON.stringify(idOrder)){
                    console.log(" Khong thay doi!");
                    return 0;
                }

                showWaittingIcon();

                let url = "/api/quiz-test/postQuestToTest?test_id=<?php echo $objData->id?>";
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {idOrder: idOrder2},
                    async: false,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                    },
                    success: function (data, status) {
                        hideWaittingIcon();
                        console.log("Data ret: ", data, " \nStatus: ", status);

                        if (data.payload){
                            if (data.payload == 'Sort done!') {
                                showToastInfoTop(data.payload);
                                ret = 1
                            } else {
                                alert("Có lỗi sort: " + data.payload)
                            }
                        }
                        else{
                            alert("Có lỗi sort: " + JSON.stringify(data))
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

                return ret;

            }
        </script>

        <script>
            $(function (){
                $("#clear_orders").on('click', function (){
                    console.log("Click....");

                    let text;
                    if (confirm("Các thứ tự câu hỏi nếu có sắp xếp sẽ đặt trở lại trạng thái ban đầu!\nBạn chắc chắn thực hiện?\n\n(Trạng thái ban đầu là Câu nào nhập trước sẽ hiển thị trước trong bài thi)") == true) {
                    } else {
                        return
                    }

                    let url = "/api/quiz-test/postQuestToTest?test_id=<?php echo $objData->id?>&clear_orders=1";
                    $.ajax({
                        url: url,
                        type: 'POST',
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
                                alert("Có lỗi sort: " + JSON.stringify(data))
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

    public function extraHtmlIncludeEdit1()
    {

        $arg = func_get_args();
        if (isset($arg[0])) {
            $objData = $arg[0];
        }
        if (isset($arg[1])) {
            $metaArr = $arg[1];
        }

        if (! isset($objData)) {
            return;
        }

        ?>

        <style>
            .one-quiz {
                padding: 10px;
                background-color: white;
                border: 1px solid #ccc;
                margin: 0px 0px;
            }
            .before-quiz {
                height: 15px;
            }

            .drop_done{
                border: 1px solid green!important;
            }
            .drop_done_bg{
                background-color: green;!important;
            }
        </style>
<?php

        echo "<br/>\n";
        echo "<div style='float: right; display: inline-block; padding-right: 10px'>";
        echo "<button id='clear_orders' class='btn btn-primary btn-sm'>  Xóa các thứ tự câu hỏi </button> &nbsp;";
        echo "<a href='/admin/quiz-test-question?seby_s2=3' target='_blank'><button class='btn btn-default btn-sm'> Xem dạng bảng </button></a> ";
        echo '</div>';
        echo "<div> <i> - Danh sách câu hỏi của bài test:</i> <b> $objData->name </b></div>";
        echo "<div ondrop='clsDragDropSortTree.drop_event(event)' ondragleave='clsDragDropSortTree.dragLeave(event)'
     ondragover='clsDragDropSortTree.allowDrop(event)' data-code-pos='ppp16839617494961' data-id='quiz_zone' class='quiz_zone' style='padding: 5px 8px; font-size: small'>";
        if (isset($objData) && isset($metaArr)) {

            $m1 = QuizTestQuestion::where(['test_id' => $objData->id])->orderBy('orders', 'ASC')->get();

            echo 'Có '.count($m1).' Câu hỏi';

            $cc = 0;
            //            $tt = count($m1);
            foreach ($m1 as $ques) {
                if ($q = QuizQuestion::find($ques->question_id)) {
                    //                if($q->is_active)
                    $cc++;
                    $txt = mb_substr($q->content, 0, 200).'...';
                    $txt = $q->content;
                    echo "\n\n<div class='before-quiz' idxx='quiz-before-id-$q->id'
ondragleave='clsDragDropSortTree.dragLeave(event)'
ondrop='clsDragDropSortTree.drop_event(event)'
ondragover='clsDragDropSortTree.allowDrop(event)'  data-idxxx='$q->id' style=''></div>";

                    echo "\n\n<div class='one-quiz'  data-id='$q->id' draggable='true'
ondragstart='clsDragDropSortTree.drag_event(event)' style=''> <a href='/admin/quiz-question/edit/$q->id' target='_blank'>Câu $cc. $q->name </a>
<br> <span style='font-size: x-small'>(Mã câu hỏi: $q->id) </span>
 <br> $txt </div> ";
                }
            }
            //echo "<div class='after-quiz' id='end-quiz' ondrop='drop1(event)' ondragover='allowDrop1(event)' style='height: 10px; background-color: lavender'></div>";
        }

        echo '</div>';
    }
    //...
}
