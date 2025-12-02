<?php

$params = route()->all();

$callBackFunc = '';

?>

<script src="/public/js/pasteimage.js"></script>
<script>

    function pasteImg2(src) {
        //$(document.body).append("<img style='border: 1px solid red' src='" + src + "'>");
        //insertImageURI(src);
        var data = {};

        //alert('<?php //echo $callBackFunc?>//');
        //return;

        data['function_update_data'] = '<?php if (! $callBackFunc) {
            echo '';
        } else {
            echo eth1b($callBackFunc);
        } ?>';
        var sourceSplit = src.split("base64,");
        var dataImg = sourceSplit[1];

        <?php
            if (ClassSetting::$useMongoDb) {
                echo 'var urlPost = "/a_p_i/member-cloud/upload_v2";';
            } else {
                echo 'var urlPost = "/a_p_i/member-cloud/upload";';
            }
?>

        data['update_id'] = '<?php
    if (isset($params['id'])) {
        echo $params['id'];
    } else {
        if (isset($id)) {
            echo $id;
        }
    }
?>';
        data['sourceString'] = dataImg;
        $.post(urlPost,
            data,
            function(resp, status){
                console.log("DataRet: " , resp);
                if(!ClassApi.checkReturnApi(resp)){
                    return;
                }
                showToastBottom('Upload DONE!')
                uploadDone1(JSON.stringify(resp));
            });
    }

    $(function() {
        $.pasteimage(pasteImg2);
    });


</script>
