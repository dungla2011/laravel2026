<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'mytree.vn';
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "/var/www/html/public/index.php";

?>
<!DOCTYPE HTML>
<html>
<head>

</head>

<style>
    div {
        padding: 2px;
        display: block;
        max-width: 500px;
    }

    div.real_node_item {
        color: red;
        /*font-size: larger;*/
        border: 1px solid blue;
    }

    div.empty_node_pad {
        display: block;
        /*background-color: gray;*/
        height: 8px;
        /*border: 1px solid green;*/
        margin: 2px 0px;
    }
    #top_parent {
        border: 1px solid blue
    }
    .drop_done{
        border: 1px solid green!important;
    }
    .drop_done_bg{
        background-color: green;!important;
    }
</style>
<body>

<h2>Kéo thả các Div </h2>

<div data-id="top_parent" ondrop="clsDragDropSortTree.drop_event(event)" ondragleave="clsDragDropSortTree.dragLeave(event)"
     ondragover="clsDragDropSortTree.allowDrop(event)" > DIV0

    <div class="empty_node_pad" ondrop="clsDragDropSortTree.drop_event(event)" ondragleave="clsDragDropSortTree.dragLeave(event)" ondragover="clsDragDropSortTree.allowDrop(event)"> </div>

    <div class="real_node_item" data-id="div1" draggable="true" ondragstart="clsDragDropSortTree.drag_event(event)" ondrop="clsDragDropSortTree.drop_event(event)"
         ondragleave="clsDragDropSortTree.dragLeave(event)" ondragover="clsDragDropSortTree.allowDrop(event)" >
        DIV 1
    </div>

    <div class="empty_node_pad" ondrop="clsDragDropSortTree.drop_event(event)" ondragleave="clsDragDropSortTree.dragLeave(event)" ondragover="clsDragDropSortTree.allowDrop(event)"> </div>

    <div class="real_node_item" data-id="div2" draggable="true" ondragstart="clsDragDropSortTree.drag_event(event)" ondrop="clsDragDropSortTree.drop_event(event)"
         ondragleave="clsDragDropSortTree.dragLeave(event)" ondragover="clsDragDropSortTree.allowDrop(event)" >
        DIV 2
    </div>

    <div class="empty_node_pad" ondrop="clsDragDropSortTree.drop_event(event)" ondragleave="clsDragDropSortTree.dragLeave(event)" ondragover="clsDragDropSortTree.allowDrop(event)"> </div>

    <div class="real_node_item" data-id="div3" draggable="true" ondragstart="clsDragDropSortTree.drag_event(event)" ondrop="clsDragDropSortTree.drop_event(event)"
         ondragleave="clsDragDropSortTree.dragLeave(event)" ondragover="clsDragDropSortTree.allowDrop(event)" >
        DIV 3
    </div>

    <div class="empty_node_pad"
         ondrop="clsDragDropSortTree.drop_event(event)"
         ondragleave="clsDragDropSortTree.dragLeave(event)"
         ondragover="clsDragDropSortTree.allowDrop(event)"> </div>

    <div class="real_node_item" data-id="div4" draggable="true" ondragstart="clsDragDropSortTree.drag_event(event)" ondrop="clsDragDropSortTree.drop_event(event)"
         ondragleave="clsDragDropSortTree.dragLeave(event)" ondragover="clsDragDropSortTree.allowDrop(event)" >
        DIV 4 <input type="checkbox">
    </div>

    <div class="empty_node_pad" ondrop="clsDragDropSortTree.drop_event(event)"
         ondragleave="clsDragDropSortTree.dragLeave(event)" ondragover="clsDragDropSortTree.allowDrop(event)"> </div>

</div>


</body>

<script src="/vendor/jquery/jquery-3.6.0.js"></script>
<script src="/vendor/lad_tree/clsDragDropSortTree.js"></script>

<script>
    clsDragDropSortTree.callBeforeDrop = function() {
        let url = "https://mytree.vn/vendor/lad_tree/clsDragDropSortTree-demo_data_get.php";
        let ret = 0
        $.ajax({
            url: url,
            async: false,
            type: 'GET',
            beforeSend: function (xhr) {
                // xhr.setRequestHeader('Authorization', 'Bearer 123456');
            },
            data: {},
            success: function (data, status) {
                console.log("Data: ", data, " \nStatus: ", status);
                if(data == "abc123456"){
                    ret = 1
                }
                else{
                    alert("Không sort, vì ajax trả về không valid")
                }
            },
            error: function () {
                console.log(" Eror....");
            },
        });
        return ret;
    }
</script>

</html>
