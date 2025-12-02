<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class QuizQuestion_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/quiz-question';

    protected static $web_url_admin = '/admin/quiz-question';

    protected static $api_url_member = '/api/member-quiz-question';

    protected static $web_url_member = '/member/quiz-question';

    public static $folderParentClass = QuizFolder::class;

    public static $modelClass = QuizQuestion::class;

    public static $allowAdminShowTree = 1;

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

        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }

        if ($field == 'is_active' || $field == 'is_english') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }

        if ($field == 'cat1' || $field == 'cat2') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //QuizQuestion edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        if ($field == 'image_list') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        if ($field == 'type') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        if ($field == 'content' || $field == 'explains' || $field == 'content_vi') {
            $objMeta->dataType = DEF_DATA_TYPE_RICH_TEXT;
        }

        if ($field == 'summary' || $field == 'draft' || $field == 'answer' || $field == 'content_textarea') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        if ($field == 'hard_level' || $field == 'class') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        if ($field == 'parent_id') {
            $objMeta->join_api = '/api/quiz-folder';
        }


        return $objMeta;
    }

    function _cat1($obj)
    {
        return [
            0=> '--Phân loại---',
            1=> 'Đã duyệt ok',
            2=> 'Cần xem lại',
        ];
    }
    function _cat2($obj)
    {
        return [
            0=> '--Phân loại---',
            1=> 'Bỏ qua',
            2=> 'Bỏ qua - Lý thuyết',
            3=> 'Cần xem lại',
        ];
    }


    public function extraHtmlIncludeEditButtonZone1($obj = null)
    {

        //        clsTableMngJs.saveOneDataTable()
        echo "<i class='btn btn-sm btn-info mb-1' id='saveAndChange' type='button'> Next </i>";

        ?>

        <style>
            .readonly_imgs img{
                max-height: 850px!important;
                max-width: 800px!important;
                position: fixed;
                right: 80px;
                top: 40px;
                z-index: 100000;
            }
            .divTable2CellEdit .mce-tinymce {
                width: 800px!important;
            }
        </style>

        <script>

            window.addEventListener('load', function () {
                console.log("Loaded ...");
                //                $("#saveAndChange").click();

                $("#saveAndChange").on('click', function (){
                    console.log("Trigger saveChoice click");

                    let ret = clsTableMngJs.saveOneDataTable(0)

                    console.log("RETx = ", ret);

                    if(ret.code != 1){
                        alert("Error save?")
                    }
                    else{

                        window.location.href = '/admin/quiz-question/edit/<?php

                        $idx =  request('id');

                        if($idx)
                            echo QuizQuestion::where("id",">", $idx)?->first()?->id;

                        ?>'
                    }



                })

            })


        </script>

        <?php

    }

    public function getNeedIndexFieldDb()
    {
        return ['parent_id', 'parent_list', 'user_id', 'refer', 'deleted_at'];
    }

    public function _hard_level($obj)
    {
        $mm = [
            0 => '- Độ khó -',
            1 => 'Dễ',
            2 => 'Trung bình',
            3 => 'Khó',
        ];

        return $mm;
    }

    public function _class($obj)
    {
        $mm = [
            0 => '- Lớp -',
        ];
        for ($i = 1; $i < 13; $i++) {
            $mm[$i] = $i;
        }

        return $mm;
    }

    public function _name($obj, $val, $field)
    {
        if (strstr($obj->refer, 'w3schools.com/')) {
            $link = '/tool1/training/selenium%20webdriver%20train/w3school-sample/4-w3school-iframe-QA-using.php?qid='.$obj->id;

            return "<span> <a style='font-size: small; margin-left: 10px; color: royalblue; cursor: pointer' onclick=\"window.open('$link','targetWindow',
                `ttilebar=no,location=no,status=no,menubar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1000,height=600`)\"> Preview </a>";
        }
    }

    public function _type($objData = null, $value = null, $field = null)
    {
        $mm = [
            0 => '- Kiểu câu hỏi -',
            1 => 'Lựa chọn Multi/Single-Check',
            //            2 => 'Lựa chọn Multi-Check',
            3 => 'Giá trị',
            4 => 'Tự luận',
            5 => 'Sắp xếp',
        ];

        //Nếu có obj có thì mới trả lại Key=>id
        //Nếu ko, nghĩa là trường hợp Get all để chọn
        if ($objData) {
            if (isset($mm[$value]) && $value) {
                return [$value => $mm[$value]];
            } else {
                return null;
            }
        }

        return $mm;
    }

    public function _image_list($obj, $val, $field)
    {
        $meta = new DemoTbl_Meta();

        return $meta->_image_list2($obj, $val, $field);
    }

    public static function getOneDivChoiceHtml($choice = null)
    {
        $right = $id = $val = $val_full = null;
        $enable = 'checked';

        if ($choice instanceof QuizChoice) {
            $val = $choice->value;
            $val_full = $choice->value_richtext;
            $id = $choice->id;
            if ($choice->is_right_choice) {
                $right = 'checked';
            }
            if ($choice->enable) {
                $enable = 'checked';
            } else {
                $enable = '';
            }
        }

        return "<tr class='tr_choice_input_value' data-id='$id' data-code-pos='ppp1689926091'>".
            "<td style='width: 40px; text-align: center'> <i  class='delete_choice fa fa-times' title='Delete Choice'></i> </td> ".
            "<td style='width: 70px; text-align: center'> <input $enable type='checkbox' title='Right choice' class='enable_choice'> </td> ".
            "<td style='width: 70px; text-align: center'> <input $right type='checkbox' title='Right choice' class='right_choice'> </td> ".
            "<td style='width: 70px; text-align: center'><input class='choice_input_value' style='width: 60px;border-color: #ccc' value='$val'></td>".
            "<td> <textarea class='choice_input_text' style='width: 100%; height: 60px; border-color: #ccc'>$val_full</textarea></td>".
            '</tr>';

        //        return "<div data-code-pos='ppp16840733454451' style='margin-bottom: 10px; border: 1px dashed #ccc; padding: 10px'>" .
        //            " <i class='fa fa-times' title='Delete Choice'></i> &nbsp;  ".
        //            " <input type='checkbox' title='Right choice' class='right_choice'></input> &nbsp;  ".
        //            "<input class='choice_input_value' data-id='$id' style='width: 60px' value='$name'> <br>$val".
        //            "</div>";
    }

    public function _choice($obj, $val, $field)
    {
        $mm = QuizChoice::where('question_id', $obj->id)->get();
        $str = "<div class=''> <a href='/admin/quiz-choice?seby_s5=$obj->id' target='_blank'>Xem dạng bảng</a> </div>";
        $str = "<div class=''>";
        $str .= "<table class='glx03 choice_zone' data-id='$obj->id' style='width: 98%'>";
        $str .= "<tr data-code-pos='ppp1686091'> <th> Xóa </th> <th>Mở/Đóng</th> <th>Câu đúng</th> <th>Đáp án</th> <th>Mô tả</th>  </tr>";

        foreach ($mm as $ch) {
            $str .= QuizQuestion_Meta::getOneDivChoiceHtml($ch);
        }
        $str .= '</table>';
        $str .= '</div>';
        $str .= "<div style='margin-top: 10px'> <button id='addOneChoice' type='button'> + Thêm lựa chọn </button>
<button style='display: none' type='button' id='saveChoice'>Ghi lại</button> </div>";

        return $str;
    }

    public function extraContentEdit1($v1 = null, $v2 = null, $v3 = null)
    {
        ?>


        <?php
        $this->extraContentIndex1();
    }

    public function extraCssInclude()
    {

    }


    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {

        ?>

        <div style="margin: 5px 0px 10px 0px" data-code-pos='ppp16837283271871'>
            <button class="btn btn-primary btn-sm" id="addMoreTest"> Thêm vào Bài test</button>
        </div>

        <div id="dialog-select-question" title="Thêm vào Bài test" style="display: none; padding: 10px 20px">
            <p >
                <span id="thong_tin_chon_cau_hoi" style="font-weight: bold"> </span>
                <span style="float: right">
                 Thêm vào bài test mới:
                <input id="new_test_name" placeholder="Nhập tên mới" type="text">
                </span>
            </p>


            <p>Hoặc chọn danh sách bài test đã tạo: <a target="_blank" style="float: right; font-size: small" href="/admin/quiz-test">Xem Danh sách bài test</a> </p>
            <div id="div_list_bai_test_id" style="height: 400px; padding: 10px; border-radius: 5px; background-color: #eee; overflow-y: scroll">
            </div>

            <div style="float: right; margin: 20px 0px 10px 0px">
                <button class="btn btn-primary" id="add_to_test"> Thêm</button>
                <button class="btn btn-default" id="close_add"> Bỏ qua</button>
            </div>
        </div>

        <div id="dialog-preview-question" title="Xem trước câu hỏi" style="display: none; padding: 10px 20px">

        </div>


        <?php

    }

    public function extraJsInclude()
    {
        ?>
        <style>
            .data_test_add
            {
                /*padding-left: 20px;*/
                padding-bottom: 10px;
            }
        </style>

        <script>


            let user_token = jctool.getCookie('_tglx863516839');

            $(function () {


                $("#close_add").on('click', function () {
                    $("#dialog-select-question").dialog('close');
                })

                $("#addMoreTest").on('click', function () {
                    $("#show_action_multi_item").hide();
                    let mIdSelected = clsTableMngJs.getSelectingCheckBox();

                    <?php
                    //Nếu là edit, không phải index thì:
                    if (request('id')) {
                        ?>
                        mIdSelected = [<?php echo request('id') ?>];
                        <?php
                    }
        ?>

                    console.log("mIdSelected=", mIdSelected);

                    if (mIdSelected.length <= 0) {
                        alert("Hãy chọn câu hỏi để thêm!")
                        return;
                    }

                    $("#dialog-select-question").dialog("open");
                    $("#thong_tin_chon_cau_hoi").html("Đã chọn : " + mIdSelected.length + "  Câu ")

                })
            })

            $(function () {

                $("#add_to_test").on("click", function () {

                    let new_test_name = $("#new_test_name").val().trim();
                    if(new_test_name && new_test_name.length < 3 ){
                        alert("Tên bài test cần ít nhất 3 ký tự: " + new_test_name)
                        return;
                    }

                    let mIdCheck = clsTableMngJs.getSelectingCheckBox();

                    <?php
        //Nếu là edit, không phải index thì:
        if (request('id')) {
            ?>
                    mIdCheck = [<?php echo request('id') ?>];
                    <?php
        }
        ?>

                    console.log("mIdCheck =  ", mIdCheck);

                    //input_extra_select
                    let mIdExtraAdd = []
                    $(".input_extra_select:checked").each(function () {
                        // console.log(" Check id ", this.getAttribute('data-id'));
                        mIdExtraAdd.push(this.getAttribute('data-id'))
                    })

                    console.log("mIdCheck / mIdExtraAdd =  ", mIdCheck, mIdExtraAdd);

                    let datax = {
                        list_quest: mIdCheck,
                        list_test: mIdExtraAdd,
                        new_test_name: new_test_name
                    }

                    showWaittingIcon();

                    let url = "/api/quiz-test/postQuestToTest";
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: datax,
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                        },
                        success: function (data, status) {
                            hideWaittingIcon();
                            console.log("Data ret: ", data, " \nStatus: ", status);
                            if (data.payload) {
                                showToastInfoTop(data.payload);
                            }
                            $("#dialog-select-question").dialog('close');
                        },
                        error: function (data) {
                            hideWaittingIcon();
                            console.log(" DATAx ", data);
                            if (data.responseJSON && data.responseJSON.message)
                                alert('Error call api: ' + "\n" + data.responseJSON.message)
                            else
                                alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                        }
                    });
                })


                $("#dialog-select-question").dialog({
                    resizable: true,
                    height: "auto",
                    autoOpen: false,
                    width: 600,
                    modal: true,
                    open: function (event, ui) {
                        console.log('opened ...')
                        showWaittingIcon();
                        let url = "/api/quiz-test/list";
                        $.ajax({
                            url: url,
                            type: 'GET',
                            beforeSend: function (xhr) {
                                xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                            },
                            success: function (data, status) {
                                hideWaittingIcon();
                                console.log("Data ret: ", data, " \nStatus: ", status);
                                if (data.payload) {
                                    console.log(" get all Data ", data.payload.data);

                                    for (let dt of data.payload.data) {
                                        console.log(" dtx ", dt);
                                        let listIdAdded = [];
                                        $(".data_test_add").each(function () {
                                            listIdAdded.push(this.getAttribute('data-id'));
                                        })
                                        console.log(" listIdAdded = ", listIdAdded, "" + dt.id);
                                        if (listIdAdded.indexOf("" + dt.id) == -1)
                                            $("#div_list_bai_test_id").append("<div class='data_test_add' data-id=" + dt.id + "> " +
                                                "<input style='transform: scale(1.5); margin: 0px 5px 0px 5px ' class='input_extra_select' data-id=" + dt.id + " type='checkbox'> " +
                                                " <a style='color: dodgerblue' target='_blank' href='/admin/quiz-test/edit/" + dt.id + "'> <i class=''></i>" + dt.name + "  </a> " +
                                                "<div style='font-size: small; padding-left: 10px' > <b>Mã đề: " + dt.id + " </b> , Ngày tạo: " + dt.created_at.substr(0, 19) + "</div>" +
                                                "</div>")
                                    }

                                    // $("#div_list_bai_test_id").html(JSON.stringify(data.payload.data));
                                }
                            },
                            error: function (data) {
                                hideWaittingIcon();
                                console.log(" DATAx ", data);
                                if (data.responseJSON && data.responseJSON.message)
                                    alert('Error call api: ' + "\n" + data.responseJSON.message)
                                else
                                    alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                            }
                        });


                    },
                });
            });

        </script>
        <?php
    }

    public function extraHtmlIncludeEdit1()
    {
        ?>

        <style>
            #edit_text_area_answer {

            }
        </style>

        <div class="cls_extra_question">
        </div>
        <?php
    }

    public function extraJsIncludeEdit($objData = null)
    {

        $this->extraJsInclude();

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
            div.divTable2Row[data-field=_choice] {
                display: none;
            }
            div.divTable2Row[data-field=answer] {
                display: none;
            }

        </style>


        <script>

            <?php
            if ($objData && $objData->refer && strstr($objData->refer, 'w3schools.com/')) {
                ?>
                $( "#edit_text_area_answer" ).prop( "disabled", true ); //Disable
            <?php
            }
        ?>

            let templateOneChoice = "<?php echo QuizQuestion_Meta::getOneDivChoiceHtml() ?>";
            if(user_token === undefined || !user_token)
                user_token = jctool.getCookie('_tglx863516839');

            $(function() {
                let qType = $("select.sl_option[data-field=type]").val()
                if (qType == 1)
                    $('div.divTable2Row[data-field=_choice]').css('display', 'table-row');

                if (qType == 3)
                    $('div.divTable2Row[data-field=answer]').css('display', 'table-row');
            })

            $("#save-one-data").on('click', function (){
                console.log("Trigger saveChoice click");
                $("#saveChoice").click();
            })

            $(document).on("click",".delete_choice", function (){

                let dataId = $(this).closest('.tr_choice_input_value').attr("data-id")
                console.log("DTID = ", dataId);
                that = this
                if(!dataId){
                    $(that).closest('.tr_choice_input_value').remove();
                    return;
                }

                if(confirm("Chắc chắn Bạn muốn xóa đáp án này?\n\nCó thể phục hồi lại trong Thùng rác!") == true){
                }else
                    return;

                let url = "/api/quiz-choice/delete?id=" + dataId;
                $.ajax({
                    url: url,
                    type: 'GET',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                    },
                    success: function (data, status) {
                        console.log("Data: ", data, " \nStatus: ", status);
                        showToastInfoTop("Xóa thành công!");
                        $(that).closest('.tr_choice_input_value').remove();
                    },
                    error: function (ret) {
                        console.log(" Error....", ret);
                        if(ret.responseJSON && ret.responseJSON.payload)
                            alert("ERROR: \n" + ret.responseJSON.payload)
                        else
                            alert("ERROR: \n" + JSON.stringify(ret))
                    },
                });


            })

            $('#saveChoice').on('click', function (){
                console.log("save Choice ...");

                let dataId = $(this).closest('.divTable2Row').attr('data-id');


                let dataPost = []
                $(".choice_zone").find(".tr_choice_input_value").each(function (){

                    let enable = $(this).find('.enable_choice').is(':checked');
                    let right_choice = $(this).find('.right_choice').is(':checked');
                    let choice_input_value = $(this).find('.choice_input_value').val().trim();
                    let choice_input_text = $(this).find('.choice_input_text').val().trim();
                    let choiceId = this.getAttribute('data-id');

                    console.log("--- choiceId = ", choiceId);
                    console.log(" enable = ", enable);
                    console.log(" right_choice = ", right_choice);
                    console.log(" choice_input_value = ", choice_input_value, choice_input_value.trim());
                    console.log(" choice_input_text = ", choice_input_text);

                    if(choice_input_value == '')
                        return;

                    dataPost.push({
                        choiceId : choiceId,
                        enable: enable,
                        right_choice: right_choice ,
                        choice_input_value: choice_input_value,
                        choice_input_text: choice_input_text
                    })
                })

                console.log(" dataPost = ", dataPost);

                let url = "/api/quiz-test/postChoiceOfQues?qid=" + dataId;
                $.ajax({
                    url: url,
                    type: 'POST',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                    },
                    data: {dataPost},
                    success: function (data, status) {
                        console.log("Data: ", data, " \nStatus: ", status);
                        console.log(" data.payload.array_id = " , data.payload.array_id);
                        for(let val in data.payload.array_id){
                            let choiceId = data.payload.array_id[val];
                            console.log(" val = ",val, choiceId);
                            // $("input.choice_input_value[value='" + val +"']").parent().parent().prop('data-id', choiceId)
                            // $("input.choice_input_value[value='" + val +"']").parent().parent().attr('data-id', choiceId)

                            //Chỉ lấy 1 cái chưa có điền id vô
                            let new1 = $(".choice_zone").find(".tr_choice_input_value[data-id='']").first();
                            new1.attr('data-id', choiceId)
                            new1.prop('data-id', choiceId)
                        }
                        showToastInfoTop("Ghi Đáp án thành công!");
                    },
                    error: function (ret) {

                        console.log(" Error....", ret);
                        if(ret.responseJSON && ret.responseJSON.payload)
                            alert("ERROR: \n" + ret.responseJSON.payload)
                        else
                            alert("ERROR: \n" + JSON.stringify(ret))
                    },
                });



            })


            $('#addOneChoice').on('click', function (){
                console.log("Click add one ...");
                $(".choice_zone").append(templateOneChoice);
            })

            $(".right_choice").on('change', function (){
                let typeQuest = $("select.sl_option[data-field=type]").val()
                console.log(" TypeQ = ", typeQuest);
                if($(this).prop('checked')){
                    if(typeQuest == 1){
                        $(".right_choice").prop('checked', false);
                        $(this).prop('checked', true);
                    }
                }
            })

            $("select.sl_option[data-field=type]").on("change", function () {

                console.log("Change ... to ", $(this).val());

                let changeTo = $(this).val();

                if(changeTo == 1){
                    $('div.divTable2Row[data-field=_choice]').css('display', 'table-row');
                    $('div.divTable2Row[data-field=answer]').css('display', 'none');
                }
                else{
                    $('div.divTable2Row[data-field=_choice]').css('display', 'none');
                    $('div.divTable2Row[data-field=answer]').css('display', 'table-row');
                }

                // let user_token = jctool.getCookie('_tglx863516839');
                // let changeTo = $(this).val();
                // if (changeTo == 1) {
                //     let url = '/api/quiz-choice/list'
                //     $.ajax({
                //         url: url,
                //         type: 'GET',
                //         beforeSend: function (xhr) {
                //             xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                //         },
                //         data: {},
                //         success: function (data, status) {
                //             console.log("Data: ", data.payload.data );
                //         },
                //         error: function () {
                //             console.log(" Eror....");
                //         },
                //     });
                //
                // }

            })
        </script>

        <?php
    }

    public function getHeightTinyMce($field)
    {
        //        if($field == 'content')

        return 300;

        return null;
    }

    //...
}
