/**
 * DataTable : get name, and value to post to API, to change one CELL value
 * @param nameInput
 * @param valInput
 * @returns {{}}
 */
function getDataPostToChangeOneValueInDataTable(nameInput, valInput){
    let field = nameInput.split("[")[0];
    let fieldMeta = nameInput.split("[")[1].replace("]", '');
    let nameId = field + "[_id]";

    console.log(" Field = " + field + ' fieldMeta = ' + fieldMeta);

    let valId = $('input[data-meta-field=id][data-field='+ field + ']').val();

    console.log("nameId = " + nameId);
    console.log("valId = " + valId);
    console.log("  Enter $(this).value = " + valInput);

    let dataPost = {};
    dataPost['id'] = valId;
    dataPost['fieldData'] = field;
    dataPost['fieldMeta'] = fieldMeta;
    dataPost['value'] = valInput;
    dataPost['one-item'] = 1;

    return dataPost;
}

$(function () {
    let user_token = jctool.getCookie('_tglx863516839');

    $(".change_status_item").on('click', function (){
        let InputName = $(this).data('name');
        console.log(" Set input value: ... " + InputName);
        let oldVal = $("input[name='"+  InputName +"']").val();
        let newVal = 1;
        if(oldVal != 0)
            newVal = 0;
        let that = this;
        console.log("Oldval = " + oldVal + ' / newVal = ' + newVal);

        let dataPost = getDataPostToChangeOneValueInDataTable(InputName,  newVal);
        console.log("PostData1 = ", dataPost);

        //get post api:

        //let urlPost = 'http://127.0.0.1:8001/api/common/save-meta-data?table_name=<?php echo $tableNameMetaInfo ?>';
        let urlPost = $("#div_container_meta").data("api-url");
        if(!urlPost){
            alert("Not valid url post!");
            return;
        }

        console.log(" DataUrl = " + urlPost);
        $.ajax({
            url: urlPost,
            type: 'POST',
            data: dataPost,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
            },
            success: function (result) {
                $("input[name='"+  InputName +"']").attr('value', newVal);
                $(that).toggleClass("fa-toggle-on fa-toggle-off");
                console.log(" RET1 = ", result);
                showToastInfoTop(" Done ?");
            },
            error: function (result) {
                showToastWarningTop("Error: " + result.message)
                console.log(" RET2 = ", result);
            },
        });
    })

    $("select[id^=to_update_]").on("change", function (){
        // alert("xxx");
        $(this).next('input').attr('value',this.value);
        console.log(" Set input value: " + this.value);

        let InputName = this.name;
        let newVal = this.value;
        let dataPost = getDataPostToChangeOneValueInDataTable(this.name,  this.value);
        console.log("PostData2 = ", dataPost);
        // let urlPost = 'http://127.0.0.1:8001/api/common/save-meta-data?table_name=<?php echo $tableNameMetaInfo ?>';

        let urlPost = $("#div_container_meta").data("api-url");
        if(!urlPost){
            alert("Not valid url post!");
            return;
        }

        $.ajax({
            url: urlPost,
            type: 'POST',
            data: dataPost,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
            },
            success: function (result) {
                $("input[name='"+  InputName +"']").attr('value', newVal);
                // $(that).toggleClass("fa-toggle-on fa-toggle-off");
                console.log(" RET3 = ", result);
                showToastInfoTop(" Done ?");
            },
            error: function (result) {
                showToastWarningTop("Error: " + result.message)
                console.log(" RET4 = ", result);
            },
        });
    });

    //Enter input, call api save cell
    $("div[data-tablerow] > input").on('keyup', function (e) {
        var code = e.keyCode || e.which;
        //Enter save item
        if (code == 13) {

            console.log(" EnterGet = " + $(this).prop('name'));
            let name = $(this).prop('name');
            let dataPost = getDataPostToChangeOneValueInDataTable(name,  $(this).val());
            console.log("PostData3 = ", dataPost);

            // let urlPost = 'http://127.0.0.1:8001/api/common/save-meta-data?table_name=<?php echo $tableNameMetaInfo ?>';
            let urlPost = $("#div_container_meta").data("api-url");
            if(!urlPost){
                alert("Not valid url post!");
                return;
            }
            console.log("urlPost3 = " + urlPost);
            $.ajax({
                url: urlPost,
                type: 'POST',
                data: dataPost,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                },
                success: function (result) {
                    console.log(" RET5 = ", result);
                    showToastInfoTop(" Done ?");
                },
                error: function (result) {
                    console.log(" RET8 = ", result);
                    if(result.responseJSON && result.responseJSON.message)
                        alert("Error: " + result.responseJSON.message);
                    else
                        showToastWarningTop("Error: " + result.message)
                },
            });
            //user_id[show_get_one]
        }
    });

    $("#save_all_form_button").on("click", function () {
        // alert("Save all");
        console.log("Save all glx");

        $(".divTable2Cell > input").each(function () {
            let dataField = $(this).data('field');
            // console.log("---");
            // console.log(" dataField = " + dataField);
            let dataMeta = $(this).data('meta-field');
            // console.log(" dataMeta = " + dataMeta);
            let val = $(this).val();
            // console.log("- VAL = " + val);
        });

        let input_serialized = $("#form_post_data").serializeArray();

        console.log(" input_serialized ", input_serialized);

        // console.log(" input_serialized ", input_serialized[1]);
        // $("#form_post_data").submit();
        // let urlPost = 'http://galaxycloud.vn/0test/test2.php';
        // let urlPost = 'http://127.0.0.1:8001/api/common/save-meta-data?table_name=<?php echo $tableNameMetaInfo ?>';
        let urlPost = $("#div_container_meta").data("api-url");
        if(!urlPost){
            alert("Not valid url post!");
            return;
        }
        $.ajax({
            url: urlPost,
            type: 'POST',
            data: input_serialized,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
            },
            success: function (result) {
                console.log(" RET7 = ", result);
                showToastInfoTop(" Done ?");
            },
            error: function (result) {
                console.log(" RET8 = ", result);
                if(result.responseJSON && result.responseJSON.message)
                    alert("Error: " + result.responseJSON.message);
                else
                    showToastWarningTop("Error: " + result.message)

            },
        });

        $("#form_post_data").submit(function (e) {
            // e.preventDefault();
            // $(this).submit();
            // return true; //Tried with an without
        });
    });
});
