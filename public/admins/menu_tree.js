/**
 *
 */
function addExtraInfoToNode(){
//Đưa link vào từng node menu
    $(".node_extra_info_after_name").each(function () {
        let nodeId = $(this).parents(".real_node_item").attr('data-tree-node-id');
        let linkNode = '';
        let iconNode = ''
        let idNews = ''
        let checkedOpenNewWin = '';
        let pad1 = '';
        for (let nodeData of treeFolder1.data) {
            if (nodeData.id == nodeId) {
                linkNode = nodeData.link;
                if (!linkNode)
                    linkNode = '';
                if (nodeData.open_new_window == 1)
                    checkedOpenNewWin = 'checked';
                iconNode = nodeData.icon;
                if (!iconNode)
                    iconNode = '';

                idNews = nodeData.id_news;
                if (!idNews)
                    idNews = '';
                iconNode = jctool.toHtmlEntities(iconNode)
                linkNode = jctool.toHtmlEntities(linkNode)
                // idNews = jctool.toHtmlEntities(idNews)


                if(idNews)
                    pad1 = ';border: 2px solid red;';

            }
        }

        $(this).html(
            "<input type='text' class='input_link_menu' title='Link menu: " + linkNode + "' placeholder='Enter link of menu' style=''  value='" + linkNode + "'> " +
            "<input type='text' class='input_icon_menu' title='Icon menu: " + iconNode + "' placeholder='Enter icon of menu' style=''  value='" + iconNode + "'> " +
            "<input type='text' class='input_id_news' title='Đưa ID BlockUI vào nếu Link này chứa nội dung của BlockUI' placeholder='ID BlockUI - nếu route là nội dung tin' style='width: 100px; color: red; font-weight: bold; "+ pad1 + "'  value='" + idNews + "'> " +
            "<i class='fa fa-link btn_click_open_link' style='' ></i>" +
            "<input type='checkbox' title='open in new window' style=''  " + checkedOpenNewWin + " class='check_open_new_win_menu'>" +
            "<i title='save link of this menu' class='fa fa-save btn_click_save_link' style=''></i>"
        )
    })


//Đưa link vào từng node menu
    $(".node_extra_info_before_name").each(function () {
        let nodeId = $(this).parents(".real_node_item").attr('data-tree-node-id');

        let gidUrl = jctool.getUrlParam('gid');

        let checkedGid = '';
        console.log(" gidUrl = " + gidUrl);
        for (let nodeData of treeFolder1.data) {
            if(!nodeData.gid_allow)
                continue;
            // console.log(" nodeData.id = " + nodeData.id + " nodeData.gid_allow = " + nodeData.gid_allow);

            if (nodeData.id == nodeId) {
                nodeData.gid_allow = "," + nodeData.gid_allow + ',';
                if (nodeData.gid_allow.includes(',' + gidUrl + ','))
                    checkedGid = 'checked';
            }
        }
        let strExtra = "<input type='checkbox' title='enable this group on this menu' " + checkedGid + " class='check_gid_allow_menu'>"
        $(this).html(strExtra)
    })
}


$(function (){



    addExtraInfoToNode()

    $(document).on('keypress', '.node_extra_info_after_name > input.input_link_menu, .node_extra_info_after_name > .input_id_news, .node_extra_info_after_name > input.input_icon_menu', function(event) {
        if (event.keyCode == 13) {
            console.log(" Enter save ...");
            $(this).siblings('.btn_click_save_link').click();
        }
    })

    $(document).on('click', '.real_node_item .toggle_node', function () {
        console.log(" .real_node_item .toggle_node ...");
        setTimeout(function (){
            addExtraInfoToNode()
        }, 1)
    })

    $('.node_extra_info_after_name > input.input_link_menu').keypress(function (event) {

    });

    $('.node_extra_info_after_name > input.input_icon_menu').keypress(function (event) {
        // if (event.keyCode == 13) {
        //     console.log(" Enter save ...");
        //     $(this).siblings('.btn_click_save_link').click();
        // }
    });

})

//Khi click sẽ tạo node
$(document).on('click', '.btn_click_save_link', function () {


    let nodeId = $(this).parents(".real_node_item").attr('data-tree-node-id');
    let newLink = $(this).siblings('input.input_link_menu').prop("value")
    let newIcon = $(this).siblings('input.input_icon_menu').prop("value")
    let idNews = $(this).siblings('input.input_id_news').prop("value")


    console.log("Save ..." + nodeId);
    console.log("newLink ..." + newLink);

    // alert("save link doing ...");
    let param = "id=" + nodeId;
    var url = treeFolder1.api_data + '/' + treeFolder1.api_suffix_rename + "?" + param

    console.log("URL newLink: " + url);
    let user_token = jctool.getCookie('_tglx863516839');

    if (treeFolder1.api_data) {
        var jqXHR = $.ajax({
            //url: this.api_data + "?cmd=rename&id=" + nodeId + '&to_name='+ nodeName,
            url: url,
            type: 'POST',
            async: false,
            data: {'link': newLink, 'icon': newIcon, 'id_news': idNews },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                // xhr.setRequestHeader("Cookie", "currentUser=TK_380e420b4b4149560c4256160a0a");
            },
            success: function (result) {
                console.log(" RET1 = ", result);
                // return mRet  = result
                showToastInfoTop("DONE change link!")
            },
            error: function (result) {

                console.log(" RET2 = ", result);
                if(result && result.responseJSON && result.responseJSON.message){
                    alert("Error: " + result.responseJSON.message);
                    return;
                }

                alert("Can not rename4!")
            },
        });
        try {
            JSON.parse(jqXHR.responseText);
        } catch (e) {
            console.log("Loi,", e);
            alert("Can not add link!")
            return false;
        }
    }
})

$(document).on('click', '.btn_click_open_link', function () {
    let val = $(this).siblings('input').prop("value")
    val = val.trim();
    if(!val)
    {
        alert("Chưa gắn link cho menu?")
        return;
    }
    window.open(val);
})

$(document).on('click', '.check_open_new_win_menu', function () {

    let nodeId = $(this).parents(".real_node_item").attr('data-tree-node-id');
    let checked = $(this).prop('checked')

    console.log(" nodeId Menu = " + nodeId);
    console.log(" checked = " + checked);
    let gidNew = jctool.getUrlParam('gid');
    console.log(" Set for GID: " + gidNew);

    let param = "id=" + nodeId + '&open_new_window=' + checked;
    var url = treeFolder1.api_data + "?" + param
    if (treeFolder1.api_suffix_rename)
        url = treeFolder1.api_data + '/' + treeFolder1.api_suffix_rename + "?" + param
    let user_token = jctool.getCookie('_tglx863516839');

    console.log("URL newLink: " + url);
    if (treeFolder1.api_data) {
        var jqXHR = $.ajax({
            //url: this.api_data + "?cmd=rename&id=" + nodeId + '&to_name='+ nodeName,
            url: url,
            type: 'GET',
            async: false,
            // data: dataPost,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                // xhr.setRequestHeader("Cookie", "currentUser=TK_380e420b4b4149560c4256160a0a");
            },
            success: function (result) {
                console.log(" RET1 = ", result);
                // return mRet  = result
                showToastInfoTop("DONE change link!")
            },
            error: function (result) {
                alert("Can not rename3!")
                console.log(" RET2 = ", result);
            },
        });
        try {
            JSON.parse(jqXHR.responseText);
        } catch (e) {
            console.log("Loi,", e);
            alert("Can not add link!")
            return false;
        }
    }
});

$(document).on('click', '.check_gid_allow_menu', function () {

    let nodeId = $(this).parents(".real_node_item").attr('data-tree-node-id');

    let checked = $(this).prop('checked')

    console.log(" nodeId Menu = " + nodeId);
    console.log(" checked = " + checked);
    let gidNew = jctool.getUrlParam('gid');
    console.log(" Set for GID: " + gidNew);

    let param = "id=" + nodeId + '&gid=' + gidNew + '&enable=' + checked;
    var url = treeFolder1.api_data + "?" + param
    if (treeFolder1.api_suffix_rename)
        url = treeFolder1.api_data + '/' + treeFolder1.api_suffix_rename + "?" + param
    let user_token = jctool.getCookie('_tglx863516839');

    console.log("URL newLink: " + url);
    if (treeFolder1.api_data) {
        var jqXHR = $.ajax({
            //url: this.api_data + "?cmd=rename&id=" + nodeId + '&to_name='+ nodeName,
            url: url,
            type: 'GET',
            async: false,
            // data: dataPost,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                // xhr.setRequestHeader("Cookie", "currentUser=TK_380e420b4b4149560c4256160a0a");
            },
            success: function (result) {
                console.log(" RET1 = ", result);
                // return mRet  = result
                showToastInfoTop("DONE change link!")
            },
            error: function (result) {
                alert("Can not rename2!")
                console.log(" RET2 = ", result);
            },
        });
        try {
            JSON.parse(jqXHR.responseText);
        } catch (e) {
            console.log("Loi,", e);
            alert("Can not add link!")
            return false;
        }
    }

})
