$(function () {
    $(".tags_select_choose").select2({
        tags: true,
        tokenSeparators: [',', ' ']
    });

    $(document).on('click', '.all_node_name .one_node_name', function (ev) {
        //Remove img in parent
        $(this).parent(".img_zone").remove();
    })

    $("#post_data_form").submit(function (e) {

        console.log(" post_data_form submit");

        e.preventDefault();
        input_serialized =  $(this).serializeArray();
        console.log(input_serialized);
        let urlPost = $(this).data('url');
        $.ajax({
            url: urlPost,
            type: 'POST',
            data: input_serialized,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
            },
            success: function (result) {

                console.log(" RET = ", result);
                // alert("DONE!");
                toastr.info('Api Done?')
                console.log(" window.location.href.substring(0,6) = " + window.location.href.substring(window.location.href.length - 6));
                if(window.location.href.substring(window.location.href.length - 6) == 'create'){
                    //Return Index:
                    window.location.href = window.location.href.substring(0, window.location.href.length - 6);
                }
            },
            error: function (result) {
                console.log(" RET = ", result);
                toastr.error('Api error?')
                alert("Error!");
            },
        });
    });

    $( ".sort_able_imgs" ).sortable({
        stop: function (event, ui) {
            console.log("sortable Stop...");
            let dataField = $(this).parents('.divTable2Cell').attr('data-field-div')
            let parentCell = $(this).parents('.divTable2Cell')
            let listImgId = '';
            parentCell.find( ".sort_able_imgs .img_zone" ).each(function (){
                console.log("... att " + $(this).attr('data-img-id'));
                listImgId+= $(this).attr('data-img-id') + ',';
            });
            listImgId = jctool.trimLeftRightAndRemoveDoubleComma(listImgId);
            console.log(" listImgId = " + listImgId);

            //let dataId = $(this).parents('.divTable2Cell').attr('data-id')
            //console.log(" dataField = ", dataField , dataId);
            $("input.input_value_to_post[data-field='"+ dataField +"']").val(listImgId);
        }
    })
});

//Browse file:
$(function () {
    $(".browse-img-btn").on('click', function (){
        let dataField = $(this).attr('data-field')
        console.log(" Need set DtField = " + dataField);

        $("#id-iframe-browser-file").attr('data-cmd', 'browse_img_for_field')
        $("#id-iframe-browser-file").prop('data-cmd', 'browse_img_for_field')
        let filebr = $("#id-iframe-browser-file");
        filebr.prop('data-field', dataField);
        filebr.attr('data-field', dataField);
        if(filebr.prop('src') !== filebr.attr('data-src')){
            console.log(" Reload iframe ...");
            filebr.prop('src', filebr.attr('data-src'));
            filebr.attr('src', filebr.attr('data-src'));
        }

        $("#id-browse-file-dlg").dialog('open');


    })
    $("#close_browse_file").on('click', function (){
        $('#id-browse-file-dlg').dialog('close');
    })
    $("#id-browse-file-dlg").dialog({
        width: 860,
        height: 600,
        position: {my: "center top+50", at: "center top+50", of: window},
        autoOpen: false,
        modal: true,
        open: function (event, ui) {
            console.log("Open dialog browse file");
            $('.ui-widget-overlay').bind('click', function () {
                console.log("Click out side dialog upload...");
                $("#id-browse-file-dlg").dialog('close');
                //Xóa hết upload instance đi, kẻo chứa rác cũ
            });
        }
    });


})

function uploadDone1(ret, objUpload){

    console.log(" upload_done_call_function - RET from server: " , ret);

    let retObj;
    if(typeof ret == 'object')
        retObj = ret;
    else
        retObj = JSON.parse(ret);

    let dataField = $("#" + objUpload.bind_selector_upload).parents(".divTable2Cell").attr('data-field-div');
    console.log(" dataFieldx = ", objUpload.bind_selector_upload,  dataField);

    console.log("Add more one item to img list...");

    let parentThisImg = $("#" + objUpload.bind_selector_upload).parents(".divTable2Cell");

    if(!dataField || !parentThisImg.length){
        console.log(" *** Not found img zone ", parentThisImg );
        return;
    }

    let fileId = retObj.payload.id;
    let fileName = retObj.payload.name;
    let fileLink = retObj.payload.link;


    /////
    let oneImg = `<span class='img_zone' data-img-id='${fileId}' ui-state-default'> ` +
        `<a data-code-pos='ppp16832490958071 target="_blank" href='${fileLink}'>` +
        `<img style="min-width: 60px; min-height: 40px; border: 1px dashed #ccc" src='${fileLink}' alt='' title='${fileName}'> ` +
        `</a>` +
        `<span class='one_node_name fa fa-times' title='remove this: ${fileId}' data-id='${fileId}' data-field='${dataField}'>  ` +
        `</span> </span>`

    parentThisImg.find('.all_node_name[data-field-img="'+ dataField +'"]').append(oneImg)

    //Thêm newVal vào input
    let newIdList = jctool.addNumberInStringComma($("input[data-field='" + dataField +"']").val(), fileId)

    $("input[data-field='" + dataField +"']").val(newIdList);
    $("#" + objUpload.bind_selector_upload).find('.upload_result_all').hide()
}


