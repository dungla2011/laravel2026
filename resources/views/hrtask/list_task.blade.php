@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('css')
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">
    <style>
        form_data.btn{
            font-size: small;
        }
        #send_img{
            display: none;
        }
    </style>
@endsection

@section('content')

    <main role="main" class="container" style="min-height: 400px">

        <div id="dialog_send_img" data-id="" style="display: none">
            <div class="task_name_of_img" data-id=""></div>
            <form action="" class="form_data" style="position: relative; border: 0px solid red; height: 100%" >
                <div id="img_preview" style="margin: 0px 0px; height: 80%; overflow-y: scroll">
                    {{--                <img id="img_preview" style="display: none; width: 200px" src="#" alt="your image" />--}}
                </div>
                {{--                    <button type="button" class="btn btn-info"--}}
                {{--                            onclick="document.getElementById('img_file_browse').click()">Chụp ảnh--}}
                {{--                    </button>--}}

                <div style="position: absolute; border-top: 1px solid #eee; padding-top: 10px; bottom: 5px; width: 100%; text-align: center">
                    <button type="button" class="btn btn-warning" id="cancel_img" style="display: none"> Hủy</button>
                    <input multiple="multiple" id="img_file_browse" name="file_data[]" value="" type="file"
                           accept="image/*;capture=camera" style="display:none">
                    <button type="submit" class="btn btn-info" id="send_img" style=""> Gửi ảnh</button>
                </div>
            </form>
        </div>

        <div class=""
             style="background-color: #dbe2e9!important; color: #236db6!important">
            <div class="col-md-12 pt-2">

{{--                <textarea name="" id="ret_test" style="font-size: small; width: 100%; height: 100px"></textarea>--}}
                <div style="min-height: 200px">
                    <b>
                    Danh sách Công việc | <?php
                        echo getCurrentUserEmail()
                    ?>
                    </b>

                    <?php
                    $mm1 = \App\Models\HrTask::where("user_id_get", getCurrentUserId())->limit(20)->latest()->get();
//                    dump($mm1);
                    ?>
                    <?php
                    foreach ($mm1 AS $task){
                    ?>
                    <div class="task_one" data-id="<?php echo $task->id ?>">
                        <i class="task_info"> Mã số <b> <?php echo $task->id ?> </b> | tạo <span class="task_created_at"><?php echo $task->created_at ?> </span> ,
                        </i>
                        <b class="task_name">
                            <?php echo $task->name ?>
                        </b>
                        <div style="margin: 5px 0px">
                        Thể loại:

                        <?php
                            echo (new \App\Models\HrTask_Meta())->_type($task, '', '')[$task->type]
                        ?>
                        </div>

                        <button style="display: none" type="button" class="btn btn-info btn_get_img_of_task"
                                onclick="document.getElementById('img_file_browse').click()">Báo cáo
                        </button>

                        <button type="button" data-id="<?php echo $task->id ?>" class="btn btn-warning open_chat_box" style=""> <i class="far fa-comments "></i> Báo cáo </button>



                    </div>
                    <?php
                    }
                    ?>

                    ...
                </div>


            </div>
        </div>


    </main><!-- /.container -->

@endsection


@section('js')
    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>
    <script src="/admins/jctool.js?v=<?php echo filemtime(public_path().'/admins/jctool.js');?>"></script>

    <?php

        \App\Models\HrMessageTask_Meta::loadTaskMessageDialog();
    ?>

    <script>

        var taskDoing = 0;

        $(function (){
            $("#dialog_send_img").dialog({
                autoOpen: false,
                height: 500,
                width: 350,
                modal: true,
            });

            $(".btn_get_img_of_task").on("click", function (){
                let dataId = $(this).parents(".task_one").attr('data-id');
                console.log("DTID = ", dataId);
                taskDoing = dataId
            })
        })



        // Get the selected file from the file input



        document.getElementById('img_file_browse').addEventListener('change', async (e) => {

            $("#dialog_send_img").dialog('open');
            $("#img_preview").html("");
            $("#cancel_img").hide()
            $("#send_img").hide()

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
                $("#img_preview").append("<img src='" + URL.createObjectURL(compressedFile) + "' style='width: 135px; margin-right: 3px'>")
                // $("#img_preview").append("<img src='" + compressedFile + "' style='width: 200px'>")

                // Save back the compressed file instead of the original file
                dataTransfer.items.add(compressedFile);
            }

            // Set value of the file input to our new files list
            e.target.files = dataTransfer.files;
            if (dataTransfer.files.length) {
                $("#cancel_img").show()
                $("#send_img").show()
            }
        });


        $(function () {


            $("#cancel_img").on('click', function () {

                $("#dialog_send_img").dialog('close');
                console.log(" cancel img");
                // let formData = new FormData(document.getElementById('form_data'))
                // formData.delete("files[]");
                $("#img_preview").html("");
                $("#cancel_img").hide()
                $("#send_img").hide()

                $("#img_file_browse").val("");


            })

            let user_token = jctool.getCookie('_tglx863516839');
            // let url = '/api/hr-message-task/getMessageOfTask?task_id=' + taskId



            $("form.form_data").submit(function (e) {
                e.preventDefault();
                var file_data = new FormData(this);
                for (let f1 of file_data) {
                    console.log("formData1 = ", f1);
                }
                showWaittingIcon()
                console.log("formData send = ", file_data);
                $.ajax({
                    url: "/api/hr-message-task/uploadFileForHrTask?task_id=" + taskDoing,
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
            });

        })

    </script>
@endsection
