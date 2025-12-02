<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;
use Pusher\Pusher;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrMessageTask_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-message-task';

    protected static $web_url_admin = '/admin/hr-message-task';

    protected static $api_url_member = '/api/member-hr-message-task';

    protected static $web_url_member = '/member/hr-message-task';

    //public static $folderParentClass = HrMessageTaskFolderTbl::class;
    public static $modelClass = HrMessageTask::class;

    public static $pusher = null;

    public function __construct()
    {

    }

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'message') {
            $objMeta->dataType = DEF_DATA_TYPE_RICH_TEXT;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //HrMessageTask edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public static function pushMessageOfTaskHr($taskId, $uidSend, $objHrTaskInfo)
    {
        if (! self::$pusher) {
            $options = [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ];

            self::$pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                $options
            );
        }

        $task = HrTask::find($taskId);
        if (! $task) {
            loi("Not found task: $taskId");
        }

        $mUid = explode(',', $task->uid_list_chat);
        if (! in_array($task->user_id_get, $mUid)) {
            $mUid[] = $task->user_id_get;
        }
        if (! in_array($task->user_id, $mUid)) {
            $mUid[] = $task->user_id;
        }

        $mUid = array_unique($mUid);

        foreach ($mUid as $uidGet) {
            if (! $uidGet) {
                continue;
            }
            HrMessageTask_Meta::pushOneMessageChatHr($taskId, $uidSend, $uidGet, $objHrTaskInfo);
        }
    }

    public function _image_list($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    public static function pushOneMessageChatHr($taskId, $uidSend, $uidGet, $objHrTask)
    {

        if (! self::$pusher) {
            $options = [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ];

            self::$pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                $options
            );
        }

        if ($uidGet == $uidSend) {
            $data['message_new'] = HrMessageTask_Meta::getOneTaskMessageFormat('task_message_r', $objHrTask);
        } else {
            $data['message_new'] = HrMessageTask_Meta::getOneTaskMessageFormat('task_message_l', $objHrTask);
        }

        self::$pusher->trigger('glx-message-task', 'glx-message-task-id-'.$taskId.'-'.$uidGet, $data);

    }

    //$clsMs:
    // task_message_r (User hiện tại)
    // task_message_l (User khác gửi)
    /**
     * @param  $obj  HrMessageTask
     * @return string
     */
    public static function getOneTaskMessageFormat($clsMs, $obj)
    {

        $mess = $obj->message;
        if ($obj->image_list) {
            $mess .= '<br>'.$obj->getHtmlShowFileOfMessageInChatBox();
        }

        return "<div data-code-pos='ppp16854967597831' class='$clsMs'>".
            "<div class='task_message'>$mess".
            "<i> $obj->created_at</i>".
            '</div>'.
            '</div>';
    }

    //...

    public static function loadTaskMessageDialog()
    {

        $uid = getUserIdCurrentInCookie();

        ?>


        <style>
            .task_one i.task_info {
                font-size: smaller;
                display: block;
            }

            .task_one {
                font-size: small;
                /*font-weight: bold;*/
                padding: 10px;
                background-color: white;
                border: 1px dashed #ccc;
                margin: 5px 1px;
            }

            .task_message_r {
                text-align: right;
                margin: 0px 5px;
            }

            .task_message_l {
                margin: 0px 5px;
                text-align: left;
            }


            .task_message i {
                font-size: xx-small;
                display: block;
            }
            .task_message{
                font-size: small;
                /*font-weight: bold;*/
                padding: 2px 5px;
                /*border: 1px dashed #ccc;*/
                margin: 5px 1px;

            }

            .task_message_r .task_message{
                border-radius: 5px;display: inline-block; max-width: 80%; text-align: right; background-color: snow
            }

            .task_message_l .task_message{
                border-radius: 5px;display: inline-block; max-width: 80%; text-align: left; background-color: lavender;
            }

            .ui-widget-header{
                border: 0px;
            }
            .ui-corner-all, .ui-corner-top, .ui-corner-left, .ui-corner-tl{
                border-radius: 0px;
            }

            #send_message, #close_dialog_tasks{
                background-color: #007bff!important;
                color: white;
            }

            .ui-dialog-titlebar{
                background-color: #007bff!important;
            }
            .ui-icon-closethick{
                color: #007bff!important;
            }
            .ui-dialog  {
                border: 1px solid #007bff!important;
                padding: 0px!important;
                border-radius: 5px!important;
            }
            #dialog_tasks .task_one {
                margin: 5px 8px;
            }

        </style>


        <form id="form_upload_file_chat_box" action="">
        <input multiple="multiple" id="img_file_browse_chat_box" name="file_data[]" value="" type="file"
               accept="image/*;capture=camera" style="display:none">
        </form>

        <div id="dialog_tasks" data-id="" style="display: none; padding: 5px 1px">
            <div id="task_chat" data-id=""  style="font-size: small; padding: 5px; border-bottom: 1px solid #ccc">
                Nhiệm vụ <b id="task_id_chat"> </b> : <b id="task_name_chat"> </b>
                <br>
                Ngày:  <i style="font-size: x-small" id="task_name_chat_created_at">........</i>
            </div>
            <div style=" height: 72%; overflow-y: scroll;" id="chat_log_task">
            </div>
            <div style="clear: both"></div>
            <div style="position: absolute; bottom: 1px;  width: 97% ; padding: 10px ">
                <div style=" margin-bottom: 10px">
                    <i id="browse_file_upload_chat_box" style="font-size: 24px; color: #007bff!important; margin-top: 5px" class="fa fa-camera" aria-hidden="true"></i>



                    <input class="form-control " placeholder="Nhập thông tin..." type="text"
                           style="display: inline-block; width: 70%;font-size: small; margin-top: 10px" id="message_text">
                    <button style="width: 18%;" class="btn inline-block" type="button" id="send_message">Gửi</button>
                </div>
                <button id="close_dialog_tasks" class="btn inline-block" style="font-size: small;"> Đóng lại</button>
                <span style="float: right">
                <input id="check_completed" type="checkbox" <?php
                if (! Helper1::isAdminModule()) {
                    echo 'disabled';
                }

        ?>> <label style="font-size: small" for="check_completed">Đánh dấu hoàn thành</label>
                </span>
            </div>
        </div>

        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

        <script>


            let user_token = jctool.getCookie('_tglx863516839');

            $(function (){

                // Pusher.logToConsole = true;
                let scroll_to_bottom = document.getElementById('chat_log_task');
                var pusher = new Pusher('e2d3c27e21727e9f9804', {
                    cluster: 'ap1'
                });

                let channel = pusher.subscribe('glx-message-task');

                $("#check_completed").on("click", function () {

                    let taskId = $(this).closest('#dialog_tasks').prop("data-id");

                    console.log(" Checked" , taskId , this.checked);

                    let checked1 = this.checked
                    let that1 = this;

                    function checkDone(done){

                        console.log("Donex = ", done);

                        //Nếu ko xong thì đảo ngược lại
                        if(!done){
                            console.log(" Not done!");
                            let old = $(that1).prop("checked")
                            $(that1).attr("checked", !old);
                            $(that1).prop("checked", !old);

                            if(checked1){
                                $("input.input_value_to_post[data-field='done'][data-id="+ taskId+"]").prop('value',0)
                                $("input.input_value_to_post[data-field='done'][data-id="+ taskId+"]").attr('value',0)
                                $("i.change_status_item[data-field='done'][data-id="+ taskId+"]").removeClass('fa-toggle-on')
                                $("i.change_status_item[data-field='done'][data-id="+ taskId+"]").addClass('fa-toggle-off')
                            }
                            else{
                                $("input.input_value_to_post[data-field='done'][data-id="+ taskId+"]").attr('value',1)
                                $("input.input_value_to_post[data-field='done'][data-id="+ taskId+"]").prop('value',1)
                                $("i.change_status_item[data-field='done'][data-id="+ taskId+"]").removeClass('fa-toggle-off')
                                $("i.change_status_item[data-field='done'][data-id="+ taskId+"]").addClass('fa-toggle-on')
                            }
                        }
                    }

                    if(taskId){
                        if(this.checked){
                            $("input.input_value_to_post[data-field='done'][data-id="+ taskId+"]").attr('value',1)
                            $("input.input_value_to_post[data-field='done'][data-id="+ taskId+"]").prop('value',1)
                            $("i.change_status_item[data-field='done'][data-id="+ taskId+"]").removeClass('fa-toggle-off')
                            $("i.change_status_item[data-field='done'][data-id="+ taskId+"]").addClass('fa-toggle-on')
                        }
                        else{
                            $("input.input_value_to_post[data-field='done'][data-id="+ taskId+"]").prop('value',0)
                            $("input.input_value_to_post[data-field='done'][data-id="+ taskId+"]").attr('value',0)
                            $("i.change_status_item[data-field='done'][data-id="+ taskId+"]").removeClass('fa-toggle-on')
                            $("i.change_status_item[data-field='done'][data-id="+ taskId+"]").addClass('fa-toggle-off')
                        }

                        clsTableMngJs.saveOneIdTable(taskId, checkDone)


                        // $("input.input_value_to_post[data-field='done'][data-id="+ taskId+"]")
                    }


                })

                $("#browse_file_upload_chat_box").on("click", function (){

                    console.log(" browse_file_upload_chat_box ...");

                    $("#img_file_browse_chat_box").val('');
                    $("#img_file_browse_chat_box").click();
                })

                $("#message_text").keydown(function(e){
                    if(e.which == 13) {
                        $("#send_message").click()
                    }
                });

                $("#send_message").on("click", function () {

                    let taskId = $(this).closest('#dialog_tasks').prop("data-id");
                    let mess = $("#message_text").val()
                    let user_token = jctool.getCookie('_tglx863516839');
                    let url = '/api/hr-message-task/postTaskMessage?task_id=' + taskId
                    console.log("Url Task : ", url);
                    showWaittingIcon()
                    $.ajax({
                        url: url,
                        type: 'POST',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                        },
                        data: {info: mess},
                        success: function (data, status) {
                            hideWaittingIcon()
                            console.log("Data ret: ", data, " \nStatus: ", status);
                            if(data.payload){
                                // $("#chat_log_task").append(data.payload);
                                // let scroll_to_bottom = document.getElementById('chat_log_task');
                                // scroll_to_bottom.scrollTop = scroll_to_bottom.scrollHeight;
                            }
                            $("#message_text").val("")
                            $("#message_text").focus()
                        },
                        error: function (data) {
                            hideWaittingIcon()
                            console.log(" DATAx " , data);
                            if(data.responseJSON && data.responseJSON.message)
                                alert('Error call api: ' + "\n" + data.responseJSON.message)
                            else
                                alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0,1000));
                        }
                    });
                })

                $(".open_chat_box").on("click", function () {

                    $("#dialog_tasks").dialog('open')
                    $("#chat_log_task").html('');
                    let taskId = $(this).attr("data-id");

                    channel.bind('glx-message-task-id-' + taskId + '-<?php echo $uid ?>', function(data) {
                        console.log("DataPuhser = ", data);
                        $("#chat_log_task").append(data['message_new']);
                        scroll_to_bottom.scrollTop = scroll_to_bottom.scrollHeight;
                    });

                    console.log("TaskID = ", taskId);

                    $("#dialog_tasks").prop("data-id", taskId);
                    $("#dialog_tasks").attr("data-id", taskId);

                    // let name = $(this).find(".task_name").text().trim()
                    // let task_created_at = $(this).find(".task_created_at").text().trim()
                    // console.log("Name = ", name);




                    //Load text task from server:
                    let user_token = jctool.getCookie('_tglx863516839');
                    let url = '/api/hr-message-task/getMessageOfTask?task_id=' + taskId
                    showWaittingIcon()
                    $.ajax({
                        url: url,
                        type: 'GET',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                        },
                        success: function (data, status) {
                            hideWaittingIcon()
                            console.log("Data ret: ", data, " \nStatus: ", status);

                            if(data.payload && data.payload.message_list){

                                $("#chat_log_task").html(data.payload.message_list);
                                $("#task_name_chat").html(data.payload.name);
                                $("#task_name_chat_created_at").html(data.payload.created_at);

                                $("#task_id_chat").html(data.payload.id);

                                if(data.payload.done) {
                                    $("#check_completed").prop("checked", true);
                                    $("#check_completed").attr("checked", true);
                                }
                                else{
                                    $("#check_completed").prop("checked", false);
                                    $("#check_completed").attr("checked", false);
                                }

                                scroll_to_bottom.scrollTop = scroll_to_bottom.scrollHeight;

                            }
                            else{
                                alert("Can not load message?")
                            }
                        },
                        error: function (data) {
                            hideWaittingIcon()
                            console.log(" DATAx " , data);
                            if(data.responseJSON && data.responseJSON.message)
                                alert('Error call api: ' + "\n" + data.responseJSON.message)
                            else
                                alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0,1000));
                        }
                    });
                })

                $("#close_dialog_tasks").on("click", function () {
                    $("#dialog_tasks").dialog('close')
                })

                $("#dialog_tasks").dialog({
                    width: 350,
                    height: 600,
                    autoOpen: false,
                    // modal: true,
                    position: {
                        my: 'right bottom',
                        at: 'right bottom',
                        // of: $('#dialog_tasks')
                    },
                    open: function (event, ui) {
                        let scroll_to_bottom = document.getElementById('chat_log_task');
                        scroll_to_bottom.scrollTop = scroll_to_bottom.scrollHeight;
                    }
                });

            })



            document.getElementById('img_file_browse_chat_box').addEventListener('change', async (e) => {


                // Get the files
                const {files} = e.target;

                // No files selected
                if (!files.length) return;

                // We'll store the files in this data transfer object
                const dataTransfer = new DataTransfer();

                // For every file in the files list
                for (const file of files) {

                    // We don't have to compress files that aren't images
                    if (!file.type.startsWith('image')) {
                        // Ignore this file, but do add it to our result
                        dataTransfer.items.add(file);
                        continue;
                    }

                    // We compress the file by 50%
                    const compressedFile = await jctool.compressImage(file, {
                        maxSize: 1000,
                        quality: 0.95,
                        type: 'image/jpeg',
                    });
                    console.log(" compressedFile = ", compressedFile);

                    // $("#img_preview").append("<img src='" + compressedFile + "' style='width: 200px'>")

                    // Save back the compressed file instead of the original file
                    dataTransfer.items.add(compressedFile);
                }

                // Set value of the file input to our new files list
                e.target.files = dataTransfer.files;
                if (dataTransfer.files.length) {

                    let taskDoing = $("#dialog_tasks").prop("data-id");

                    var file_data = new FormData(document.getElementById('form_upload_file_chat_box'));
                    for (let f1 of file_data) {
                        console.log("formData1 = ", f1);
                    }
                    let url = "/api/hr-message-task/uploadFileForHrTask?send_file_on_chat_box=1&task_id=" + taskDoing

                    showWaittingIcon()
                    console.log("formData send = ", file_data);
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: file_data,
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                        },
                        success: function (data) {
                            hideWaittingIcon()
                            console.log("DataRET = ", data)
                            $("#ret_test").val((data))
                            $("#dialog_send_img").dialog('close');
                        },
                        cache: false,
                        contentType: false,
                        processData: false,
                        error: function (data) {
                            hideWaittingIcon()
                            console.log(" DATAx " , data);
                            if(data.responseJSON && data.responseJSON.message)
                                alert('Error call api: ' + "\n" + data.responseJSON.message)
                            else
                                alert('Error call api: ' + "\n" + JSON.stringify(data).substr(0,1000));
                        }
                    });

                }


            });

        </script>
<?php
    }
}
