<style>
    .search-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #f1f1f1;
        padding: 1px;
        border-radius: 5px;
    }

    #search_user_name{
        color: red;
    }

    .search-container input {
        flex-grow: 1;
        border: none;
        padding: 5px;
        outline: none;
        /*background-color: transparent;*/
    }

    .search-container .search-button {
        margin-left: 2px;
        padding: 5px 5px;
        border: none;
        background-color: #bbb;
        color: white;
        cursor: pointer;
    }

    .search-container .search-button:hover {
        background-color: #45a049;
    }
</style>


<?php

if (! isset($treeInfo)) {
    $treeInfo = new \App\Models\MyTreeInfo();
}

if($treeInfo instanceof \App\Models\MyTreeInfo);


$giaphaUser = \App\Models\GiaPhaUser::where("user_id", getCurrentUserId())->first();
$version_using = $giaphaUser->version_using ?? 1;


?>


<!--<div class="btn_ctrl_svg1" style="right: 20px; ">-->
<!--    <img style="" src="/assert/Ionicons/src/share-alt1.svg" alt="">-->
<!--</div>-->

<div onclick="showHelpDgl()" class="btn_ctrl_svg1" style="right: 5px; border: 0px; width: 120px; font-weight: bold; font-size: 15px ">
<!--    <img style="width: 18px; margin-right: 2px" src="/assert/Ionicons/src/help.svg" alt="">-->
    <i class="fa fa-users"></i>
    &nbsp;
    Cộng đồng
</div>

<a href="/member">
    <div title="Thành viên" class="btn_ctrl_svg1" style=" top: 50px; left: 15px;; " onclick="">
        <img style="" src="/assert/Ionicons/src/person.svg" alt="">
    </div>
</a>





<div title="Chọn một trong hai:  Phiên bản 1 máy tự động sắp xếp cho Bạn. Phiên bản 2 bạn có thể sắp xếp bằng tay, khó hơn nhưng chủ động vị trí hơn!" class="" style=" top: 55px; left: 60px;; position: absolute" onclick="">
    <?php
if($giaphaUser->user_id ?? '')
if(($objTree->user_id ?? '') == $giaphaUser->user_id)
{
?>
    <select name="" id="change_version_my_tree" style="font-size: 80%; padding: 3px; color: red; border-radius: 3px; border: 2px solid red">
        <option <?php if($version_using == 1) echo 'selected' ?> value="1">Phiên bản 1</option>
        <option <?php if($version_using == 2) echo 'selected' ?> value="2">Phiên bản 2</option>
    </select>
    <?php
}
    ?>

    <a target="_blank" style="text-decoration: none; font-size: 80%; border: 1px solid #ccc; padding: 5px 8px; border-radius: 5px" href="/tool/site/mytree/export_svg.html?pid=<?php echo $pid ?>">Xuất PDF, SVG</a>

</div>


<div title="Chia sẻ phả đồ này, người dùng khác chỉ có thể xem không thể sửa!" class="btn_ctrl_svg1" id="share_btn"
     style="border: 0px;  bottom: 110px; left: 15px; font-size: 130%; background-color: transparent; color: ; ">

    <i class="fa fa-share-alt " style="border: 1px solid gray; border-radius: 50%; padding: 9px 10px; background-color: white"></i>

</div>
<div title="Giới hạn cấp xem" class="btn_ctrl_svg1"
     style="border: 0px;  bottom: 60px; left: 15px; background-color: transparent ">
    <select name="" id="set_limit_tree_level"
            style=" background-color: white; border-color: #ddd ; color: grey; <?php if (request('level')) {
                echo 'color : red;  border: 2px solid red; background-color: #ccc; ';
            } ?> ; border-radius: 5px; padding: 4px 3px; max-width: 40px; font-size: small">
        <option value="">-</option>
        <option value="0">0 - Không giới hạn cấp xem</option>

        <?php
        $levelC = request('level');
for ($i = 1; $i < 10; $i++) {
    $padSl = '';
    if ($levelC && $levelC == $i) {
        $padSl = 'selected';
    }
    echo "<option $padSl value='$i'>$i - Giới hạn xem đến cấp $i</option>";
}
?>


    </select>
</div>

<div title="Thông tin" class="btn_ctrl_svg1" style=" top: 50px; right: 15px;; ">
    <img onclick="clsTreeTopDownCtrl.showInformation()" style="" src="/assert/Ionicons/src/information.svg" alt="">
</div>

<div title="Tìm kiếm thành viên,  nhập chuỗi cần tìm, bấm Enter để chuyến đến các thành viên!" class="search-container"
     style=" top: 50px; right: 60px; position: absolute; font-size: 90%;">
    <input id="search_user_name" type="text" class="" placeholder="Tìm thành viên"
           style="width: 120px;  padding-left: 5px ; border: 0px solid #ccc">
    <button id="prev_button_search" class="search-button"> < </button>
    <button id="next_button_search" class="search-button"> > </button>
</div>
<div id="search_info" style="position: absolute; top: 85px; right: 200px; background-color: white"></div>

<div class="btn_ctrl_svg2" style=" top: 50px; right: 270px;; ">
    <div id="selecting_nodes" onclick="clsTreeTopDownCtrl.unSelectNodes()" title="Chọn/Bỏ chọn" class="btn_ctrl_svg2" style=" ">

    </div>
</div>

<div class="" id="tree_info_" style="display: none; position: fixed; top: 100px; right: 15px;;
 max-width: 300px; border: 1px solid #ccc; background-color: snow; border-radius: 5px; padding: 20px ">
    <div title="Thông tin" id="showInformation_close"
         style="position: fixed; top: 105px; right: 25px;z-index: 1000;">
        &#x2716;
    </div>
    <div id="tree_info_1">
    </div>
</div>

<?php
$left = 15
?>
<div class="btn_ctrl_svg1" style="  left: <?php echo $left ?>px; "
     onclick="window.location.reload();">
    <img title="Làm mới" style="" src="/assert/Ionicons/src/refresh.svg" alt="">
</div>
<div class="btn_ctrl_svg1" style="  left: <?php echo $left += 45 ?>px; "
     onClick="clsTreeTopDownCtrl.center_fit('svg_grid')">
    <img title="Thu gọn" style="" src="/assert/Ionicons/src/arrow-shrink.svg" alt="">
</div>

<!--
<div class="btn_ctrl_svg1" style=" left: <?php //echo $left += 45?>px; ">
    <img title="Danh sách cây" style="" src="/assert/Ionicons/src/list-ul1.svg" alt="">
</div>

<div class="btn_ctrl_svg1" style=" left: <?php //echo $left+=45?>px; ">
<img title="Tạo cây mới" style="" src="/assert/Ionicons/src/plus.svg" alt="">
</div>


-->
<div class="btn_ctrl_svg1" style=" left: <?php echo $left += 45 ?>px; "
     onclick="clsTreeTopDownCtrl.showConfigTree('svg_grid')">
    <img title="Cấu hình" style="" src="/assert/Ionicons/src/ios-gear.svg" alt="">
</div>
<div class="btn_ctrl_svg1" style=" left: <?php echo $left += 45 ?>px; "
     onclick="clsTreeTopDownCtrl.selectBackGround('svg_grid',0)">
    <img title="Chọn khung ảnh thành viên" style="" src="/assert/Ionicons/src/tablet-portrait-outline.svg" alt="">
</div>


<div onclick="clsTreeTopDownCtrl.downloadImagePng('svg_grid', '')" class="btn_ctrl_svg1"
     style=" left: <?php echo $left += 45 ?>px; ">
    <img title="Tải xuống dạng ảnh" style="" src="/assert/Ionicons/src/android-download.svg" alt="">
</div>

<?php
//if (isSupperAdmin__())
{

    ?>
    <div onclick="testAdm()" class="btn_ctrl_svg1"
         style=" border-color: #eee; width: 20px; height: 20px; border-radius: 0px ; left: <?php echo $left += 45 ?>px; ">
        <span>  </span>
    </div>

    <style>
        .root_svg .svg-pan-zoom_viewport{
            border: 1px solid gray
        }
    </style>
    <script>

        //Khi bam vao id = share_btn, thi copy link vao Clibboard
        $(function () {
            $("#share_btn").on("click", function () {
                let url = window.location.href;
                let text = "Cây gia phả gia đình: " + url;

                if (navigator.share) {
                    // Nếu thiết bị hỗ trợ Web Share API (thường là mobile)
                    navigator.share({
                        title: "Chia sẻ cây gia phả",
                        text: text,
                        url: url
                    }).then(() => {
                        showToastInfoTop("Đã chia sẻ thành công!");
                    }).catch((error) => {
                        // showToastInfoTop("Lỗi khi chia sẻ: " + error);
                    });
                } else {
                    // Nếu không hỗ trợ Web Share API, fallback về copy clipboard
                    navigator.clipboard.writeText(text).then(() => {
                        showToastInfoTop("Đã copy link vào Clipboard: " + text);
                    }).catch((err) => {
                        showToastInfoTop("Error copy link: " + err);
                    });
                }
            });


        });

        //Vẽ thử Viền và ảnh nền
        $(function (){

            setTimeout(function (){


            }, 1000);

            function getNodeById1(nodeId, tree) {
                // Giả sử dataPart là một mảng chứa tất cả các nút trong cây
                for (let node of tree.dataPart) {
                    if (node.id == nodeId) {
                        return node;
                    }
                }
                return null; // Trả về null nếu không tìm thấy nút với id đã cho
            }

            function blinkNodeCenter(nodeId){
                console.log("Blink node...", nodeId);
                $("[id^='blink_new_node_']").css('display', 'none');

                let tree = clsTreeTopDownCtrl.getInstanceSvgById();
                let node = getNodeById1(nodeId, tree);

                console.log(" NODE = ", node, tree);

                if(node){
                    tree.moveObjToCenterOfViewPort(node);
                    let id = 'blink_new_node_' + nodeId;
                    //Đổi màu chữ span có id trên
                    let span = document.getElementById(id);
                    if(span){
                        span.style.display = 'block';
                    }
                }
            }

            let currentIndex = 0;
            let totalFound = 0;
            let searchResults = [];

            function updateSearchInfo() {
                let info = totalFound > 0 ? (currentIndex + 1) + " / " + totalFound : "";
                document.getElementById("search_info").innerText = info;
            }

            $("#search_user_name").on("keyup", function (){
                if(event.keyCode == 13) { // 13 là mã của phím Enter
                    // Gọi hàm tương tự như khi nhấn nút "Next"
                    if(currentIndex < totalFound - 1){
                        currentIndex++;
                        let idNode = document.getElementsByClassName("node_name_one")[searchResults[currentIndex]].id.replace('id_node_name_', '');
                        blinkNodeCenter(idNode);
                    }
                    else {
                        currentIndex = 0;
                        let idNode = document.getElementsByClassName("node_name_one")[searchResults[currentIndex]].id.replace('id_node_name_', '');
                        blinkNodeCenter(idNode);
                    }
                    updateSearchInfo();
                    return;
                }
                if(event.keyCode == 27) { // 27 là mã của phím Esc
                    // Xóa tất cả các kết quả tìm kiếm
                    currentIndex = 0;
                    totalFound = 0;
                    searchResults = [];
                    document.getElementById("search_user_name").value = '';
                    document.getElementById("search_info").innerText = '';
                    $("[id^='blink_new_node_']").css('display', 'none');
                    return;
                }

                let val = $(this).val().toLowerCase();

                let mm = document.getElementsByClassName("node_name_one");
                totalFound = 0;
                searchResults = [];

                if(val.length > 1)
                for(let i = 0; i < mm.length; i++){
                    let item = mm[i];
                    let text = item.innerText.toLowerCase();
                    if(text.includes(val)){
                        searchResults.push(i);
                        totalFound++;
                        if(totalFound == 1) {
                            currentIndex = 0;
                            let idNode = item.id.replace('id_node_name_', '');
                            blinkNodeCenter(idNode);
                        }
                    }
                }
                updateSearchInfo();
            });

            $("#prev_button_search").on("click", function (){
                if(currentIndex > 0){
                    currentIndex--;
                    let idNode = document.getElementsByClassName("node_name_one")[searchResults[currentIndex]].id.replace('id_node_name_', '');
                    blinkNodeCenter(idNode);
                }
                updateSearchInfo();
            });

            $("#next_button_search").on("click", function (){
                console.log(" Search member...");
                if(currentIndex < totalFound - 1){
                    currentIndex++;
                    let idNode = document.getElementsByClassName("node_name_one")[searchResults[currentIndex]].id.replace('id_node_name_', '');
                    blinkNodeCenter(idNode);
                }
                else {
                    currentIndex = 0;
                    let idNode = document.getElementsByClassName("node_name_one")[searchResults[currentIndex]].id.replace('id_node_name_', '');
                    blinkNodeCenter(idNode);
                }

                updateSearchInfo();
            });

        })

        function testAdm(){
// Lấy đối tượng SVG bằng cách sử dụng id của nó
            var svgObject = document.getElementById("svg_grid");

            svgObject = document.getElementsByClassName("svg-pan-zoom_viewport")[0];

// Tạo một chuỗi mới chứa mã XML của đối tượng SVG
            var svgData = new XMLSerializer().serializeToString(svgObject);

// Tạo một Blob từ chuỗi SVG
            var svgBlob = new Blob([svgData], {type: "image/svg+xml;charset=utf-8"});

// Tạo một URL cho Blob
            var svgUrl = URL.createObjectURL(svgBlob);

// Tạo một liên kết tải xuống và gắn vào trang
            var downloadLink = document.createElement("a");
            downloadLink.href = svgUrl;
            downloadLink.download = "image.svg";
            document.body.appendChild(downloadLink);

// Kích hoạt liên kết tải xuống
            downloadLink.click();

// Xóa liên kết tải xuống khỏi trang sau khi tải xuống
            document.body.removeChild(downloadLink);

        }

        function testAdm1(){

            let svg = clsTreeTopDownCtrl.allInstance[0]
            console.log("SVG = ", svg);

            let sizeXySvg = svg._panZoomTiger.getSizes()
            console.log(" sizeXySvg = " , sizeXySvg);
            let grid = svg.getRootSvgIfHavePanZoom()

            let objContSvg
            let newDiv1


            console.log(" GRID = ", grid);
            objContSvg = document.createElementNS('http://www.w3.org/2000/svg', "foreignObject");
            //nếu ko tạo mới thì chỉ set lại thuộc tính:
            objContSvg.setAttribute('id', "abc123");
            // objContSvg.setAttribute('class', "svg_cont_node_cls");
            objContSvg.setAttribute('x', 100);
            objContSvg.setAttribute('y', 100);
            objContSvg.setAttribute('width', 300);
            objContSvg.setAttribute('height', 300);
            newDiv1 = document.createElement('div');
            newDiv1.innerHTML = "<div style='border: 1px solid red; width: 200px; height: 200px'></div>"
            objContSvg.appendChild(newDiv1);
            grid.appendChild(objContSvg);



            objContSvg = document.createElementNS('http://www.w3.org/2000/svg', "foreignObject");
            //nếu ko tạo mới thì chỉ set lại thuộc tính:
            objContSvg.setAttribute('id', "abc1231");
            // objContSvg.setAttribute('class', "svg_cont_node_cls");
            objContSvg.setAttribute('x', sizeXySvg.width - 500);
            objContSvg.setAttribute('y', 100);
            objContSvg.setAttribute('width', 300);
            objContSvg.setAttribute('height', 300);
            newDiv1 = document.createElement('div');
            newDiv1.innerHTML = "<div style='border: 1px solid red; width: 200px; height: 200px'></div>"
            objContSvg.appendChild(newDiv1);
            grid.appendChild(objContSvg);
        }


        $(function (){

            $("#change_version_my_tree").on("change", function (){
                let val = $(this).val();
                console.log(" VAL = ", val);
                let user_token = jctool.getCookie('_tglx863516839');
                let url = "/api/member-my-tree-info/changeVersion?version_using=" + val;
                showWaittingIcon();
                $.ajax({
                    url: url,
                    type: 'GET',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                    },
                    success: function (data, status) {
                        hideWaittingIcon();
                        console.log("Data ret2: ", data, " \nStatus: ", status);
                        if (data.code) {
                            alert(data.payload)
                            window.location.reload()
                        } else {
                            alert("Có lỗi: " + JSON.stringify(data))
                        }
                        console.log("Data: ", data, " \nStatus: ", status);
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

        })

    </script>
    <?php

}
?>

<script>


</script>
