$(function () {
    $("#dialog_tree").dialog({
        width: 500,
        position: {my: "center top+50", at: "center top+50", of: window},
        autoOpen: false,
        modal: true,
        open: function (event, ui) {

            console.log("Open dialog tree");

            $('.ui-widget-overlay').bind('click', function () {
                console.log("Click out side...");
                $("#dialog_tree").dialog('close');
                //Xóa hết đi, kẻo chứa rác cũ
                clsTreeJsV2.clearAllTreeInstance();
            });
        }
    });

    $(".clear_select_tree_item").on("click", function () {

        let dtField = $(this).attr('data-field')
        $("input[data-field='" + dtField + "']").attr('value', '');
        $("input[data-field='" + dtField + "']").prop('value', '');
        $("span.full_node_path_name[data-field='" + dtField + "']").html('');

    });

    $(".btn_open_dialog_tree").on("click", function () {
        $("#dialog_tree").dialog("open");

        clsTreeJsV2.clearAllTreeInstance();

        var treeFolder1 = new clsTreeJsV2();
        treeFolder1.bind_selector = "#dialog_tree1"
        treeFolder1.opt_open_all_first = 0;
        // treeFolder1.data = data1

        if ($(this).attr('data-multi-select') == 1) {
            treeFolder1.checkbox1 = true;
            console.log("+++ tree type Check box...");
        } else {
            console.log("+++ tree type Radio box...");
            treeFolder1.radio1 = true;
        }

        // const queryString = window.location.search;


        treeFolder1.api_data = $(this).attr('data-api');

        //Nếu có param bổ xung pid, thì mở theo PID đó:
        const urlParams = new URLSearchParams(treeFolder1.api_data.split('|')[1] ?? '');
        if(urlParams){
            const pid = urlParams.get('pid')
            if(pid)
                treeFolder1.root_id = pid;
            console.log(" PID = ", pid, treeFolder1.api_data, urlParams);
        }





        // treeFolder1.api_suffix_add = '/create';
        // treeFolder1.api_suffix_rename = '/rename';
        // treeFolder1.api_suffix_delete = '/delete';
        // treeFolder1.api_suffix_move = '/move';

        treeFolder1.hide_root_node = 0; //6.9.24: tai sao hide?

        treeFolder1.disable_drag_drop = 1;
        treeFolder1.disable_menu = 1;
        treeFolder1.showTree();
        $(treeFolder1.bind_selector).attr('data-field', $(this).attr('data-field'));
    });


    // $(document).on('click', '.real_node_item', function (ev){
    //     ev.stopPropagation()
    //     ev.preventDefault();
    //     console.log('clicked...:', this.getAttribute('data-tree-node-id'));
    //     var isMulti = 0
    //     if(clsTreeJsV2.getInstanceTreeOfAnyObject(this).checkbox1) {
    //         isMulti = 1
    //         $(this).find(' > input[type=checkbox]').not(':checked').prop("checked", true);
    //     }
    //     else
    //         $(this).find(' > input[type=radio]').not(':checked').prop("checked", true);
    //
    //     let dtField = $(this).parents('.cls_root_tree').attr('data-field')
    //     console.log("DT Field: ", dtField);
    //
    //
    //     //$("input[data-field='"+ dtField +"']").attr('value', this.getAttribute('data-tree-node-id'));
    //     //console.log(" NAME = " + $(this).parent('.real_node_item').find('.node_name').text());
    //     let val = this.getAttribute('data-tree-node-id');
    //     setDataFieldInput(this,dtField, val, isMulti)
    // })

    function getFullTreePathName(elm) {
        let fullTreePathName = elm.find(" > .node_name").text();
        // $(this).parentsUntil("div.cls_root_tree").css({"color": "red", "border": "2px solid red"});
        elm.parentsUntil("div.cls_root_tree").map(function () {


            if ($(this).attr('data-tree-node-id') > 0)
                // console.log(" Node name:  " + $(this).attr('data-tree-node-id'));
                // console.log(" -- " + $(this).find(" > .node_name").text());
                fullTreePathName = $(this).find(" > .node_name").text() + ' / ' + fullTreePathName;
            console.log(" getFullTreePathName ..." + fullTreePathName);
        })
        return fullTreePathName;
    }

    $(document).on('click', '.real_node_item .node_name, .real_node_item .check_box_node1, .real_node_item input[type=radio]', function (ev) {

        ev.stopPropagation()
        // ev.preventDefault();
        console.log('clicked...:', this.parentElement.getAttribute('data-tree-node-id'));

        //$(this.parentElement).find(' > input[type=radio]').not(':checked').prop("checked", true);
        let dtField = $(this).parents('.cls_root_tree').attr('data-field')
        let valIdTree = this.parentElement.getAttribute('data-tree-node-id');

        let treeInstance = clsTreeJsV2.getInstanceTreeOfAnyObject(this)

        console.log(" treeInstance found = " , treeInstance);

        if (treeInstance.checkbox1) {

            let tmp = $(this.parentElement).find(' > input[type=checkbox]');

            let nameNode = $(this.parentElement).find(' > span[class*="node_name"]').html();

            let fullPathName = getFullTreePathName($(this.parentElement))
            console.log("+++Checkbox Check: ");

            if (!$(this).hasClass('check_box_node1')) //Nếu là check box thì ko cần làm động tác này, vì nó tự xly
                tmp.prop("checked", !tmp.prop("checked"));

            if (!tmp.prop("checked")) {
                console.log("Un check...");
                addOrRemoveMultiValueFromItem(valIdTree, dtField, nameNode, fullPathName, 2)
            } else {
                addOrRemoveMultiValueFromItem(valIdTree, dtField, nameNode, fullPathName, 1)
            }

        } else {
            console.log("+++ Radio Check ...");
            $(this.parentElement).find(' > input[type=radio]').not(':checked').prop("checked", true);

            console.log(" Set Val for input if have input ");

            $("input[data-field='" + dtField + "']").attr('value', valIdTree);
            $("input[data-field='" + dtField + "']").prop('value', valIdTree);

            // let fullTreePathName = $(this).find(" > .node_name").text();
            // // $(this).parentsUntil("div.cls_root_tree").css({"color": "red", "border": "2px solid red"});
            // $(this).parentsUntil("div.cls_root_tree").map(function () {
            //     if ($(this).attr('data-tree-node-id') > 0)
            //         // console.log(" Node name:  " + $(this).attr('data-tree-node-id'));
            //         // console.log(" -- " + $(this).find(" > .node_name").text());
            //         fullTreePathName = $(this).find(" > .node_name").text() + ' / ' + fullTreePathName;
            // })

            let fullTreePathName = getFullTreePathName($(this));

            let objNew = "<span title='remove this: " + valIdTree +" ' class='one_node_name' data-id='" + valIdTree + "' data-field='" + dtField + "'> [x] " + fullTreePathName + " </span> ";

//            $("span.full_node_path_name[data-field='" + dtField + "']").html(" [x] " + fullTreePathName);
            $("span.all_node_name[data-field='" + dtField + "']").html(objNew);

        }

        console.log("DT Field1: ", dtField);

        //setDataFieldInput(this, dtField, valIdTree, isMulti)
    })

    function addOrRemoveMultiValueFromItem(valueNodeTreeId, dataField, nameNode, fullPathName, add1Remove2 = 1) {

        console.log(" addOrRemoveMultiValueFromItem ... nameNode = " , nameNode);
        let oldVal = $("input[data-field='" + dataField + "']").attr('value')
        if (add1Remove2 == 2) {
            console.log(" + add1Remove2 " + add1Remove2);
            $("input[data-field='" + dataField + "']").prop('value', jctool.removeNumberInStringComma(oldVal, valueNodeTreeId));
            $("input[data-field='" + dataField + "']").attr('value', jctool.removeNumberInStringComma(oldVal, valueNodeTreeId));
            $("span.one_node_name[data-field='" + dataField + "'][data-id='" + valueNodeTreeId + "']").remove();
        } else {
            if (!jctool.checkIdInStringComma(oldVal, valueNodeTreeId)) {
                console.log(" - add1Remove2x = " + add1Remove2);
                $("input[data-field='" + dataField + "']").prop('value', jctool.addNumberInStringComma(oldVal, valueNodeTreeId));
                $("input[data-field='" + dataField + "']").attr('value', jctool.addNumberInStringComma(oldVal, valueNodeTreeId));
                let objNew = "<span title='remove this: " + fullPathName + ' (' + valueNodeTreeId + ")' class='one_node_name' data-id='" + valueNodeTreeId + "' data-field='" + dataField + "'> [x] " + nameNode + " </span> ";
                $("span.all_node_name[data-field='" + dataField + "']").append(objNew);
            }
        }
    }

    // function setDataFieldInput(elm, dtField, valIdTree, isMulti){
    //
    //     if(isMulti) {
    //         let tree1 = clsTreeJsV2.getInstanceTreeOfAnyObject(elm)
    //         var mIdSelect = ''
    //         var mName = '';
    //         let oldStr = $("input[data-field='" + dtField + "']").prop('value');
    //         //Duyệt tất cả các node
    //         $(tree1.bind_selector + " .real_node_item").each(function (){
    //
    //             //Tìm node có con đang là check
    //             if($(this).find("> .check_box_node1").is(":checked")){
    //
    //                 let thisDataIdTree = this.getAttribute('data-tree-node-id')
    //                 //   console.log("- thisDataIdTree1 = " + thisDataIdTree);
    //                 // console.log("Old str = " + oldStr);
    //                 oldStr = ',' + oldStr + ','
    //                 if(oldStr.includes(',' + thisDataIdTree + ',' )){
    //                     console.log(" Have id: " , thisDataIdTree);
    //                 }
    //                 else{
    //                     console.log(" Not Have id: " , thisDataIdTree);
    //                     mName += "<span title='remove this '" + thisDataIdTree + " class='one_node_name' data-id='" + thisDataIdTree + "' data-field='"+ dtField +"'> [x] " + $(this).find("> .node_name").html() + " </span> ";
    //                     console.log("IDx = " , thisDataIdTree);
    //                     //mIdSelect += this.getAttribute('data-tree-node-id') + ','
    //                     oldStr = jctool.addNumberInStringComma(oldStr, thisDataIdTree)
    //                     console.log(" oldStr after add val: " , oldStr);
    //                 }
    //
    //                 oldStr = jctool.trimLeftRightAndRemoveDoubleComma(oldStr);
    //             }
    //         })
    //
    //         $("input[data-field='" + dtField + "']").attr('value', oldStr);
    //         $("span.all_node_name[data-field='" + dtField + "']").append(mName);
    //     }
    //     else{
    //
    //         $("input[data-field='" + dtField + "']").attr('value', valIdTree);
    //         var fullTreePathName = $(elm).find(" > .node_name").text();
    //         // $(this).parentsUntil("div.cls_root_tree").css({"color": "red", "border": "2px solid red"});
    //         $(elm).parentsUntil("div.cls_root_tree").map(function () {
    //             if ($(this).attr('data-tree-node-id') > 0)
    //                 // console.log(" Node name:  " + $(this).attr('data-tree-node-id'));
    //                 // console.log(" -- " + $(this).find(" > .node_name").text());
    //                 fullTreePathName = $(this).find(" > .node_name").text() + ' / ' + fullTreePathName;
    //         })
    //         $("span.full_node_path_name[data-field='" + dtField + "']").html(fullTreePathName);
    //     }
    // }

    $("#close_select_tree").on('click', function () {
        $("#dialog_tree").dialog('close');
        //Xóa hết đi, kẻo chứa rác cũ
        clsTreeJsV2.clearAllTreeInstance()
    })

    $(document).on('click', '.all_node_name .one_node_name, .full_node_path_name', function (ev) {
        ev.stopPropagation()
        let dtId = $(this).attr('data-id')
        let dtField = $(this).attr('data-field')
        let oldVal = $("input[data-field='" + dtField + "']").prop('value');
        let newVal = jctool.removeNumberInStringComma(oldVal, dtId);

        let dataIdObj = $(this).closest('.divTable2Cell').attr('data-id');





        console.log(" dataIdObj1 ", dataIdObj);
        if(dataIdObj){
            let isEditable = $(this).closest('.divTable2Cell').attr('data-edit-able');
            console.log(" Editable: ", isEditable);
            if(isEditable == 0)
                return;
            $("input[data-field='" + dtField + "'][data-id='" + dataIdObj + "']").attr('value', newVal);
            $("input[data-field='" + dtField + "'][data-id='" + dataIdObj + "']").prop('value', newVal);
        }
        else{
            $("input[data-field='" + dtField + "']").attr('value', newVal);
            $("input[data-field='" + dtField + "']").prop('value', newVal);
        }

        console.log(" Click to: " + dtId + " / " + dtField , dataIdObj);
        console.log(" old/new: " + oldVal + " / "  , dataIdObj, newVal);

        $(this).closest('.img_zone').remove();

        //10.4.23: mở ra để remove đi? parent của demo, news cần phải remove khi click vào
        if ($(this).hasClass('full_node_path_name')) {
            $(this).html('');
        } else
            $(this).remove();

    });


});
