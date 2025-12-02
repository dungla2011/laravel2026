class clsDragDropSortTree {

    static root_id = 'top_parent'
    static class_name_item = 'real_node_item'
    static class_name_pad_zone = 'empty_node_pad'

    static dragLeave(ev) {
        ev.target.classList.remove('drop_done_bg')
        // ev.target.style.backgroundColor = 'transparent';
        ev.preventDefault();
        return false;
    }

    static allowDrop(ev) {
        ev.target.classList.add("drop_done_bg");
        // ev.target.style.backgroundColor = 'green';
        ev.preventDefault();
        return false;
    }

    static drag_event(ev) {
        ev.dataTransfer.setData("textId", ev.target.id);
        return false;
    }

    static htmlToElement(html) {
        var template = document.createElement('template');
        html = html.trim(); // Never return a text node of whitespace as the result
        template.innerHTML = html;
        return template.content.firstChild;
    }

    static addPadZoneTo(node1, cond) {
        console.log(" COND : " + cond);
        node1 = document.getElementById(node1);
        var newDiv = clsDragDropSortTree.htmlToElement('<div class="'+ clsDragDropSortTree.class_name_pad_zone+'" ' +
            'ondrop="clsDragDropSortTree.drop_event(event)" ' +
            'ondragleave="clsDragDropSortTree.dragLeave(event)" ' +
            'ondragover="clsDragDropSortTree.allowDrop(event)" >  </div>');
        if (cond === 'before') {
            console.log("Add before");
            node1.before(newDiv)
        } else {
            console.log("Add after");
            node1.after(newDiv)
        }
    }

    //Sắp xếp lại các div đệm, duyệt từ trên xuống, 2 cái nào gần nhau ko có đệm, thì cần đưa đệm vào
    //Lấy 2 đệm gần nhau để đưa vào đó
    //Vùng nào ko còn Node, thì bỏ hết đệm
    static reArrangePadZone(parentIdNode) {
        if(!parentIdNode)
            return;
        var allChild = parentIdNode.children;
        // console.log(" AllChild10 ", allChild);
        //Duyệt tất cả node, trước và sau node chỉ có 1 và chỉ 1 đệm
        //Thừa thì xóa, thiếu thì thêm

        //console.log(" allChild.length " , allChild.length);
        let len = allChild.length;
        for (var i = 0; i < allChild.length; i++) {
            // console.log(" -- check node " + i);
            if (allChild[i].matches('.' + clsDragDropSortTree.class_name_item)) {
                // console.log(" fondid : " + i);
                //Xem trước nó có đệm chưa:
                if (i == 0 || i > 0 && !allChild[i - 1].matches("." + clsDragDropSortTree.class_name_pad_zone)) {
                    // console.log("Add one ...");
                    clsDragDropSortTree.addPadZoneTo(allChild[i].id, 'before')
                }
                if (i < len - 1)
                    if (!allChild[i + 1].matches("." + clsDragDropSortTree.class_name_pad_zone)) {
                        clsDragDropSortTree.addPadZoneTo(allChild[i].id, 'after')
                    }
            }
        }
        if (allChild[allChild.length - 1].matches('.' + clsDragDropSortTree.class_name_item)) {
            clsDragDropSortTree.addPadZoneTo(allChild[allChild.length - 1].id, 'after')
        }

        //Chỗ nào có 2 đệm thì bỏ đi 1:
        for(let x = 0; x < 3; x++) // lặp 3 lần
            for (let child of allChild) {
                // console.log(" -- check node ", child);
                if (child.matches("." + clsDragDropSortTree.class_name_pad_zone)) {
                    // console.log("Pad zone ...");
                    if (child.nextElementSibling && child.nextElementSibling.matches("." + clsDragDropSortTree.class_name_pad_zone)) {
                        child.parentNode.removeChild(child);
                        // console.log("REMOVE OK next 1");
                    }
                    if (child.previousElementSibling && child.previousElementSibling.matches("." + clsDragDropSortTree.class_name_pad_zone)) {
                        child.parentNode.removeChild(child);
                        // console.log("REMOVE OK prev 1");
                    }
                }
            }

        //Nếu ko còn node thì bỏ hết pad
        var foundNode = 0;
        for (let child of allChild) {
            if (child.matches('.' + clsDragDropSortTree.class_name_item)) {
                foundNode = 1;
                break;
            }
        }
        if (!foundNode) {
            for (let child of allChild) {
                if (child.matches("." + clsDragDropSortTree.class_name_pad_zone))
                    child.parentNode.removeChild(child);
            }
        }
    }



    static drop_event(ev) {

        ev.preventDefault();
        ev.stopPropagation();
        var textId = ev.dataTransfer.getData("textId");
        var parentOfDrag = document.getElementById(textId).parentElement;
        // console.log(" textId = " + textId);
        // console.log(" toID Elament = " + ev.target.id);
        // document.getElementById(textId).style.backgroundColor = "#ccc";

        Array.from(document.querySelectorAll('.drop_done_bg')).forEach(
            (el) => el.classList.remove('drop_done_bg')
        );

        Array.from(document.querySelectorAll('.drop_done')).forEach(
            (el) => el.classList.remove('drop_done')
        );

        document.getElementById(textId).classList.add("drop_done");

        // ev.target.style.backgroundColor = 'white';

        if (!textId) {
            console.log("Not id to move node?");
            return;
        }
        if (textId == ev.target.id) {
            console.log("Can Not to move to it self?");
            return;
        }

        //Nếu di chuyển đến parent của nó thì bỏ qua
        if (document.getElementById(textId).parentElement.id == ev.target.id) {
            console.log("Ignore because move to parent");
            return;
        }




        //Nếu drop vào 1 cái empty_node_pad, thì tìm tiếp theo của empty_node_pad để đưa vào trước, hoặc cái trước padzone và đưa vào sau
        if (ev.target.matches("." + clsDragDropSortTree.class_name_pad_zone)) {
            if (ev.target.previousElementSibling){
                console.log(textId + "--- Chuyển đến sau ID1 : " + ev.target.previousElementSibling.id);
                if(!clsDragDropSortTree.callBeforeDrop('after', textId, ev.target.previousElementSibling.id)){
                    return
                }

                //Đưa node vào sau cái trước padzone
                ev.target.previousSibling.after(document.getElementById(textId));
            }
            else
            if (ev.target.nextElementSibling){
                //Đưa node vào trước cái sau padzone
                ev.target.nextSibling.before(document.getElementById(textId));
                console.log(textId + "--- Chuyển đến trước ID : ", ev.target.id);
                if(!clsDragDropSortTree.callBeforeDrop('before', textId, ev.target.id)){
                    return
                }
            }

            //Sắp xếp lại vùng cũ vừa rời đi:
            console.log(" Sắp xếp lại vùng cũ vừa rời đi: " , parentOfDrag);
            clsDragDropSortTree.reArrangePadZone(parentOfDrag);
            //Sắp xếp lại parrent của padzone
            console.log("===  Sắp xếp lại parent của padzone");
            clsDragDropSortTree.reArrangePadZone(ev.target.parentElement);
            console.log("===  Kết thúc Sắp xếp lại parent của padzone");

        } else {
            //Nếu Drop vào 1 node, thì gắn luôn vào làm con của node đó

            console.log(textId + " --- Chuyển vào trong ID: " + ev.target.id);
            ev.target.appendChild(document.getElementById(textId));
            //Xu ly đệm của đích:
            clsDragDropSortTree.reArrangePadZone(ev.target)
            //và của nguồn
            clsDragDropSortTree.reArrangePadZone(ev.target.parentElement);
            console.log(" Sắp xếp lại vùng cũ vừa rời đi: " , parentOfDrag);
            clsDragDropSortTree.reArrangePadZone(parentOfDrag);
        }
    }

    static callBeforeDrop = function() {
        return 1;
    }
}

// clsDragDropSortTree.callBeforeDrop = function() {
//     let url = "https://galaxycloud.vn/train/tree-view/lad-tree2022/01.php";
//     let ret = 0
//     $.ajax({
//         url: url,
//         async: false,
//         type: 'GET',
//         beforeSend: function (xhr) {
//             // xhr.setRequestHeader('Authorization', 'Bearer 123456');
//         },
//         data: {},
//         success: function (data, status) {
//             console.log("Data: ", data, " \nStatus: ", status);
//             if(data == "abc123456"){
//                 ret = 1
//             }
//         },
//         error: function () {
//             console.log(" Eror....");
//         },
//     });
//     return ret;
// }
