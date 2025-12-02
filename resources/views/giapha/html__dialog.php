<?php
if (!isset($treeInfo) || !$treeInfo) {
    return;
}

if ($treeInfo instanceof \App\Models\MyTreeInfo) ;

?>

<style>
    #div_move_item_gp_to_folder {
        padding: 1px 1px 20px 1px;
    }

    .cls_root_tree {
        padding-top: 50px;
    }

    /* width */
    ::-webkit-scrollbar {
        width: 10px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
        background: #eee;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #ddd;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #ccc;
    }

    .cls_root_tree {
        padding: 0px;
    }

    .select_bg_radio {
        background-color: transparent;
        /*margin-left: 40px;*/
    }

    .root_tree_cls_div {
        border: 0px solid #ccc !important;
    }

    .opt_display {
        border-top: 1px solid #ccc;
        padding-top: 10px
    }

    .opt_display div {
        display: inline-block;
        margin-right: 5px;
    }

    .opt_display button {
        /*width: 20px;*/
        border: 1px solid #ccc
    }

    .opt_display input[type=text] {
        width: 40px;
        height: 23px;
    }

    .opt_display .cls1 {
        width: 120px;
        display: inline-block;
    }

</style>
<div id="dialog-show-config" title="Cấu hình Cây: " style="display: none; margin-top: 18px">
    <div style="border: 0px solid #ccc; border-radius: 5px">
        <div style="margin-bottom: 10px">
            <b>
                Chọn hiển thị thành viên:
            </b>
        </div>
        <div style="">
            <input checked name="settypetree" id="allshowtree" type="radio"
                   onclick="clsTreeTopDownCtrl.resetDefault('svg_grid')">
            <label style="" for="allshowtree">
                Hiện thị tất cả
            </label>
        </div>
        <div style="">
            <input name="settypetree" id="showonlyman" type="radio"
                   onclick="setManOnly()">
            <label style="" for="showonlyman">

                Chỉ hiển thị Nam & Đinh</label>
        </div>
        <div>
            <input name="settypetree" id="huyethongonly" type="radio"
                   onclick="setHuyetThongOnly()">
            <label style="" for="huyethongonly">

                Chỉ hiển thị Huyết thống</label>
        </div>
        <?php
        if ($useNewVer) {
            ?>
            <div class="opt_display">
                <div style="margin-bottom: 10px">
                    <b>
                        Thông tin thành viên:

                    </b>
                </div>
                <br>
                <div>
                    <input name="settypetree" id="show_node_image" type="checkbox"
                           onclick="clsTreeTopDownCtrl.show_node_image()"
                        <?php
                        if ($treeInfo->show_node_image)
                            echo "checked";
                        ?>
                    >
                    <label style="" for="show_node_image">Hiển thị Ảnh</label>
                </div>
                <!---->
                <!--                <div>-->
                <!--                    <input name="settypetree" id="show_node_name_one" type="checkbox"-->
                <!--                           onclick="clsTreeTopDownCtrl.show_node_name_one()"-->
                <!--                        --><?php
                //                        if($treeInfo->show_node_name_one)
                //                            echo "checked";
                //                        ?>
                <!--                    >-->
                <!--                    <label style="" for="show_node_name_one">Hiển thị Tên</label>-->
                <!--                </div>-->


                <div>
                    <input name="show_node_title" id="show_node_title" type="checkbox"
                           onclick="clsTreeTopDownCtrl.show_node_title()"
                        <?php
                        if ($treeInfo->show_node_title)
                            echo "checked";
                        ?>
                    >
                    <label style="" for="show_node_title">Danh hiệu</label>
                </div>

                <div>
                    <input name="settypetree" id="show_node_birthday_one" type="checkbox"
                           onclick="clsTreeTopDownCtrl.show_node_birthday_one()"
                        <?php
                        if ($treeInfo->show_node_birthday_one)
                            echo "checked";
                        ?>
                    >
                    <label style="" for="show_node_birthday_one">Ngày sinh</label>
                </div>

                <div>
                    <input name="settypetree" id="show_node_date_of_death" type="checkbox"
                           onclick="clsTreeTopDownCtrl.show_node_date_of_death()"
                        <?php
                        if ($treeInfo->show_node_date_of_death)
                            echo "checked";
                        ?>
                    >
                    <label style="" for="show_node_date_of_death">Ngày mất</label>
                </div>

                <?php

                ?>

                <div class="opt_plus">
                <span class="cls1">
                Chiều cao </span>
                    <button class="minus">-</button>
                    <input id="set_node_height" value="<?php
                    echo $treeInfo->getNodeHeight();
                    ?>" type="text">
                    <button class="plus">+</button>
                </div>
                <div class="opt_plus">
                <span class="cls1">
                Chiều rộng </span>
                    <button class="minus">-</button>
                    <input id="set_node_width" value="<?php

                    echo $treeInfo->getNodeWidth();
                    ?>" type="text">
                    <button class="plus">+</button>
                </div>

                <div class="opt_plus">
                <span class="cls1">
                Cỡ chữ </span>
                    <button class="minus">-</button>
                    <input id="set_font_size_node" type="text" value="<?php
                    echo $treeInfo->getFontSizeNode();
                    ?>">
                    <button class="plus">+</button>
                </div>

                <div class="opt_plus" title="Khoảng cách ngang sẽ bằng 1/3, 1/5, 1/7 hoặc 1/9 chiều rộng của một Ô (thành viên)">
                <span class="cls1">
                Khoảng cách ngang </span>
                    <button class="minus">-</button>
                    1 / <input id="set_space_node_x" type="text" style="width: 22px" value="<?php
                    echo $treeInfo->getSpaceNodeX();
                    ?>">
                    <button class="plus">+</button>
                    <i class="fa fa-question-circle"></i>
                </div>

                <div class="opt_plus">
                <span class="cls1">
                Khoảng cách dọc </span>
                    <button class="minus">-</button>
                    <input id="set_space_node_y" type="text" value="<?php
                    echo $treeInfo->getSpaceNodeY();
                    ?>">
                    <button class="plus">+</button>
                </div>

            </div>
            <?php
        }
        ?>
    </div>


    <div style="padding: 10px 12px; margin-top: 15px; border: 1px solid #ccc; border-radius: 5px">


        <!--            <img style="width: 25px" src="/assert/Ionicons/src/android-arrow-up.svg" alt="">-->
        <!--        <i class="fa fa-hand-o-right"></i> -->
        <input type="checkbox" id="title_before_or_after_name"
            <?php
            if ($treeInfo && $treeInfo->title_before_or_after_name) {
                echo 'checked';
            }
            ?>
        >
        <label title="Có tác dụng trong cây này, các cây khác, cây con không bị ảnh hưởng" style="margin-bottom: 0px"
               for="title_before_or_after_name">

            Danh Hiệu đặt Trên Họ Tên</label>


    </div>

    <div style="padding: 10px 15px; margin-top: 15px; border: 1px solid #ccc; border-radius: 5px">

        <a style="text-decoration: none" href="#" id="clear_col_fix_tree"
           title="Nếu bạn đã di chuyển các thành viên trên cây bằng tay, ở đây sẽ đặt lại vị trí như mặc định ban đầu, nếu cần bạn có thể di chuyển lại các thành viên">
            <!--            <i class="fa fa-undo"></i>-->
            <button style="border: 1px solid gray; border-radius: 5px"> Đặt lại Vị trí mặc định</button> &nbsp; Đặt lại
            các vị trí đã di chuyển bằng tay về mặc định
            <?php
            //
            //            $ttColFix = 0;
            //            if(isset($mret)){
            //
            //                foreach ($mret[0] AS $gp){
            //                    if($gp['col_fix'] && $gp['col_fix'] > 0){
            //                        $ttColFix++;
            //                    }
            //                }
            //            }
            //            if($ttColFix)
            //                echo "<br/>  <i id='title_change_col_fix'> Có $ttColFix vị trí đã thay đổi so với mặc định </i>";
            //            else
            //                echo "<br/>  <i id='title_change_col_fix'> Không có vị trí nào thay đổi so với mặc định </i>";
            ?>
        </a>
        <br/> <i id='title_change_col_fix'> </i>

    </div>

    <div style="padding: 10px 15px; margin-top: 15px; border: 1px solid #ccc; border-radius: 5px">
        <a style="text-decoration: none" href="#" id="clear_cache_tree"
           title="Nếu cây có thay đổi và chưa thấy cập nhật lên giao diện, bạn có thể click vào đây để làm mới cây">
            <i class="fa fa-refresh"></i> Làm mới cây này (Cache)
        </a>
    </div>

    <div style="padding: 10px 10px; margin-top: 15px; border: 1px solid #ccc; border-radius: 5px">
        <a target="_blank" title="Có thể Tìm và phục hồi các thành viên đã xóa" href="/member/tree-mng?in_trash=1">
            <img style="width: 25px" src="/assert/Ionicons/src/ios-trash.svg" alt="">Thùng Rác
        </a>

        <?php
        if (isSupperAdmin_()) {
            //echo "<a href='#' id='test_glx123'> Test </a>";

            ?>

            <script>
                $("#test_glx123").on("click", function () {
                    console.log("Click ...");
                    // I recommend to keep the svg visible as a preview
                    var svg = $('#app_gp > svg').get(0);
// you should set the format dynamically, write [width, height] instead of 'a4'
                    var pdf = new jsPDF('p', 'pt', 'a4');
                    svgElementToPdf(svg, pdf, {
                        scale: 72 / 96, // this is the ratio of px to pt units
                        removeInvalid: true // this removes elements that could not be translated to pdf from the source svg
                    });
                    pdf.output('datauri'); // use output() to get the jsPDF buffer
                })
            </script>
            <?php
        }
        ?>
    </div>


    <?php
    if ($objTree->user_id == auth()->id()) {
        ?>
        <!--    <div style="padding: 10px 8px; margin-top: 15px; border: 1px solid #ccc; border-radius: 5px">-->
        <!--        <form method="post" action="">-->
        <!--            <input type="hidden" name="clear_cache_this_url">-->
        <!--            <button type="submit" alt="">Xóa Cache </button>-->
        <!--        </form>-->
        <!--    </div>-->
        <?php
    }
    ?>

</div>
<div id="dialog-select-background" title="Chọn khung ảnh cho: ">

    <div style="position: relative; background-color: #ccc;">
        <div style="" class="select_bg_radio">

            <div>
                <input type="radio" id="bg_for_man" name="bg_image_for_user" value="1"
                       onclick="clsTreeTopDownCtrl.setBackGroundManOrWoman('svg_grid',1)">
                <label style="" for="bg_for_man">Nam</label>
            </div>
            <div>
                <input type="radio" id="bg_for_woman" name="bg_image_for_user" value="2"
                       onclick="clsTreeTopDownCtrl.setBackGroundManOrWoman('svg_grid',2)">
                <label style="" for="bg_for_woman">Nữ</label>
            </div>
            <div>
                <input checked type="radio" id="bg_for_all" name="bg_image_for_user" value="3"
                       onclick="clsTreeTopDownCtrl.setBackGroundManOrWoman('svg_grid',0)">
                <label style="" for="bg_for_all">Nam và Nữ</label>
            </div>
        </div>

        <div style="position: absolute; top: 40px">
            <?php
            //public\images\border-banner-bg1;
            $folder = '/images/border-frame-img2/';
            $mFile = ListDir(public_path() . "$folder");
            sort($mFile);
            foreach ($mFile as $file) {
                $bname = basename($file);
                if ($bname[0] == '_') {
                    continue;
                }
                $file1 = $folder . basename($file);
                echo "<div class='img_bg_node_svg' > <img class='lazyload' data-src='$file1'><br> Chọn mẫu $bname </div>";
            }
            ?>
        </div>
    </div>
</div>

<div id="dialog-select-banner-background" title="Chọn khung ảnh cho Banner">
    <?php
    //public\images\border-banner-bg1;
    $folder = '/images/border-banner-bg1/';
    $mFile = ListDir(public_path() . "$folder");
    sort($mFile);
    foreach ($mFile as $file) {
        $file1 = $folder . basename($file);
        $bname = basename($file);
        if ($bname[0] == '_') {
            continue;
        }
        echo "<div class='img_bg_banner' > <img class='lazyload' data-src='$file1'><br> Chọn mẫu $bname </div>";
    }
    ?>
</div>


<div id="dialog-edit-banner" title="Thay đổi banner" style="display: none">
    <div style="margin-bottom: 15px"></div>

    <div class=" input_node" style="font-size: smaller">
        <div class="">
            <button onclick="setDefaultBannerInfo()" style="border: 1px solid #ccc; float: right; margin-bottom: 5px">
                Đặt lại mặc định
            </button>
            </button>
            <a href="/member/my-tree-info/edit/<?php echo qqgetRandFromId_($treeInfo->id) ?>" target="_blank">
                <b>
                    Tên cây
                </b>
            </a>

            <input placeholder="Tên Cây - hiển thị dòng đầu" style="width: 100%; " autocomplete="off"
                   class="form-control"
                   type="text" id="banner_name1" value="<?php

            if (isset($treeInfo) && $treeInfo && $treeInfo->name) {
                echo $treeInfo->name;
            } elseif (isset($gp) && isset($gp->name)) {
                echo $gp->name;
            }
            ?>"

            >

            Màu chữ &nbsp;
            <input type="color" class="input_color_banner_tree" id="banner_color_name" style="margin-" value="<?php
            if (isset($treeInfo) && $treeInfo && $treeInfo->color_name) {
                echo "$treeInfo->color_name";
            } else {
                echo '#ff0000';
            }
            ?>">

            Cỡ chữ
            <select id="banner_fontsize_name" class="">
                <?php
                $setSelect = 30;
                for ($i = 8; $i < 80; $i++) {
                    if (isset($treeInfo) && $treeInfo) {
                        if ($treeInfo->fontsize_name == $i) {
                            $setSelect = $i;
                            break;
                        }
                    }
                }
                for ($i = 8; $i < 80; $i++) {
                    $padSelected = null;
                    if ($setSelect == $i) {
                        $padSelected = 'selected';
                    }

                    echo "<option $padSelected value='$i'> $i </option>";
                }
                ?>
            </select>
            <button data-cmd="-" data-min="5" data-max="80" data-id="banner_fontsize_name" class="change_number_btn">
                -
            </button>
            <button data-cmd="+" data-min="5" data-max="80" data-id="banner_fontsize_name" class="change_number_btn">
                +
            </button>

            <p style="margin-top: 10px">
                Cách mép trên
                <input type="text" id="banner_name_margin_top" style="text-align: center;width: 30px" min="0" max="30"
                       value="<?php
                       if (isset($treeInfo) && $treeInfo && $treeInfo->banner_name_margin_top) {
                           echo "$treeInfo->banner_name_margin_top";
                       } else {
                           echo '20';
                       }
                       ?>">
                <button data-cmd="-" data-step="2" data-min="-200" data-max="200" data-id="banner_name_margin_top"
                        class="change_number_btn"> -
                </button>
                <button data-cmd="+" data-step="2" data-min="-200" data-max="200" data-id="banner_name_margin_top"
                        class="change_number_btn"> +
                </button>

                <input style="margin-left: 20px" type="checkbox"
                       id="banner_text_shadow_name" <?php if (isset($treeInfo) && ($treeInfo->banner_text_shadow_name)) {
                    echo 'checked';
                } ?> >
                <label for="banner_text_shadow_name">Viền chữ</label>

                <!--                Cách mép dưới-->
                <!--            <input type="text" id="banner_name_margin_bottom" style="width: 30px" min="0" max="30" value="-->
                <?php
                //            if(isset($treeInfo) && $treeInfo && $treeInfo->banner_name_margin_bottom)
                //                echo "$treeInfo->banner_name_margin_bottom";
                //            else
                //                echo "0";
                //?><!--">-->
            </p>

            <p>
                Độ cong
                <input type="text" id="banner_name_curver" style="text-align: center;width: 40px" min="0" max="30"
                       value="<?php
                       if (isset($treeInfo) && $treeInfo && $treeInfo?->banner_name_curver) {
                           echo "$treeInfo?->banner_name_curver";
                       } else {
                           echo '0';
                       }
                       ?>">
                <button data-cmd="-" data-min="-500" data-max="500" data-step="10" data-id="banner_name_curver"
                        class="change_number_btn"> -
                </button>
                <button data-cmd="+" data-min="-500" data-max="500" data-step="10" data-id="banner_name_curver"
                        class="change_number_btn"> +
                </button>

                <!--                Cách mép dưới-->
                <!--            <input type="text" id="banner_name_margin_bottom" style="width: 30px" min="0" max="30" value="-->
                <?php
                //            if(isset($treeInfo) && $treeInfo && $treeInfo->banner_name_margin_bottom)
                //                echo "$treeInfo->banner_name_margin_bottom";
                //            else
                //                echo "0";
                //?><!--">-->

                <input style="margin-left: 10px" type="checkbox"
                       id="banner_name_bold" <?php if ($treeInfo?->banner_name_bold) {
                    echo 'checked';
                } ?>>
                <label for="banner_name_bold"> Đậm </label>

                <input type="checkbox"
                       id="banner_name_italic" <?php if ($treeInfo?->banner_name_italic) {
                    echo 'checked';
                } ?>>
                <label for="banner_name_italic"> Nghiêng </label>

            </p>

        </div>

        <hr style="border-top: 1px solid gray;">
        <div class="">
            <b>
                Mô tả ngắn
            </b>
            <input placeholder="Mô tả ngắn - hiển thị ở dòng sau" style="width: 100%; " autocomplete="off"
                   class="form-control"
                   type="text" id="banner_title1">

            Màu chữ &nbsp;
            <input type="color" class="input_color_banner_tree" id="banner_color_title" style="" value="<?php
            if (isset($treeInfo) && $treeInfo && $treeInfo->color_title) {
                echo "$treeInfo->color_title";
            } else {
                echo '#ff0000';
            }
            ?>">
            Cỡ chữ
            <select id="banner_fontsize_title">
                <?php
                $setSelect = 20;
                for ($i = 8; $i < 80; $i++) {
                    if (isset($treeInfo) && $treeInfo) {
                        if ($treeInfo->fontsize_title == $i) {
                            $setSelect = $i;
                            break;
                        }
                    }
                }
                for ($i = 8; $i < 80; $i++) {
                    $padSelected = null;
                    if ($setSelect == $i) {
                        $padSelected = 'selected';
                    }
                    echo "<option value='$i' $padSelected> $i </option>";
                }
                ?>
            </select>
            <button data-cmd="-" data-min="5" data-max="80" data-id="banner_fontsize_title" class="change_number_btn">
                -
            </button>
            <button data-cmd="+" data-min="5" data-max="80" data-id="banner_fontsize_title" class="change_number_btn">
                +
            </button>


            <p style="margin-top: 10px">
                Cách mép trên
                <input type="text" id="banner_title_margin_top" style="width: 30px" min="0" max="30" value="<?php
                if (isset($treeInfo) && $treeInfo && $treeInfo->banner_title_margin_top) {
                    echo "$treeInfo->banner_title_margin_top";
                } else {
                    echo '';
                }
                ?>">
                <button data-cmd="-" data-step="2" data-min="-200" data-max="200" data-id="banner_title_margin_top"
                        class="change_number_btn"> -
                </button>
                <button data-cmd="+" data-step="2" data-min="-200" data-max="200" data-id="banner_title_margin_top"
                        class="change_number_btn"> +
                </button>
                <!--                Cách mép dưới-->
                <!--                <input type="text" id="banner_title_margin_bottom" style="text-align: center; width: 30px" min="0" max="30" value="-->
                <?php
                //                if(isset($treeInfo) && $treeInfo && $treeInfo->banner_title_margin_bottom)
                //                    echo "$treeInfo->banner_title_margin_bottom";
                //                else
                //                    echo "0";
                //?><!--">-->
                <input style="margin-left: 20px" type="checkbox"
                       id="banner_text_shadow_title" <?php if ($treeInfo?->banner_text_shadow_title) {
                    echo 'checked';
                } ?> >
                <label for="banner_text_shadow_title">Viền chữ</label>
            </p>

            <p>
                Độ cong
                <input type="text" id="banner_title_curver" style="text-align: center;width: 40px" min="0" max="30"
                       value="<?php
                       if (isset($treeInfo) && $treeInfo && $treeInfo->banner_title_curver) {
                           echo "$treeInfo->banner_title_curver";
                       } else {
                           echo '0';
                       }
                       ?>">
                <button data-cmd="-" data-min="-500" data-max="500" data-step="10" data-id="banner_title_curver"
                        class="change_number_btn"> -
                </button>
                <button data-cmd="+" data-min="-500" data-max="500" data-step="10" data-id="banner_title_curver"
                        class="change_number_btn"> +
                </button>

                <!--                Cách mép dưới-->
                <!--            <input type="text" id="banner_name_margin_bottom" style="width: 30px" min="0" max="30" value="-->
                <?php
                //            if(isset($treeInfo) && $treeInfo && $treeInfo->banner_name_margin_bottom)
                //                echo "$treeInfo->banner_name_margin_bottom";
                //            else
                //                echo "0";
                //?><!--">-->

                <input style="margin-left: 10px" type="checkbox"
                       id="banner_title_bold" <?php if ($treeInfo?->banner_title_bold) {
                    echo 'checked';
                } ?>>
                <label for="banner_title_bold"> Đậm </label>
                <input type="checkbox"
                       id="banner_title_italic" <?php if ($treeInfo?->banner_title_italic) {
                    echo 'checked';
                } ?>>
                <label for="banner_title_italic"> Nghiêng </label>
            </p>

        </div>
    </div>
    <hr style="border-top: 1px solid gray;">
    <div class="" style="font-size: smaller; margin-top: 10px; ">
        <div class="">
            <button class="btn btn-info" id="select_banner_img1"
                    onclick="clsTreeTopDownCtrl.selectBackGroundForBanner()">Chọn Ảnh Banner mẫu
            </button>
            <div style="display: inline-block; float: right">
                <input style="" class="form-control" type="file" accept="image/*" id="file_id_banner" hidden>
                <label class="lb_upload btn btn-info" for="file_id_banner" style="">Tải ảnh lên</label>
            </div>

            <p></p>
            <div style="margin-bottom: 10px">
                Banner Rộng:
                <input type="text" id="banner_width" style="text-align: center; width: 30px" value="<?php
                if (isset($treeInfo) && $treeInfo && $treeInfo->banner_width) {
                    echo "$treeInfo->banner_width";
                } else {
                    echo '790';
                }
                ?>">
                <button data-cmd="-" data-min="300" data-max="1200" data-step="10" data-id="banner_width"
                        class="change_number_btn"> -
                </button>
                <button data-cmd="+" data-min="300" data-max="1200" data-step="10" data-id="banner_width"
                        class="change_number_btn"> +
                </button>
            </div>
            <div style="margin-bottom: 10px">
                Banner Cao &nbsp;&nbsp;
                <input type="text" id="banner_height" style="text-align: center; width: 30px" value="<?php
                if (isset($treeInfo) && $treeInfo && $treeInfo->banner_height) {
                    echo "$treeInfo->banner_height";
                } else {
                    echo '150';
                }
                ?>">
                <button data-cmd="-" data-min="100" data-max="300" data-step="2" data-id="banner_height"
                        class="change_number_btn"> -
                </button>
                <button data-cmd="+" data-min="100" data-max="300" data-step="2" data-id="banner_height"
                        class="change_number_btn"> +
                </button>
            </div>

            <div style="margin-bottom: 0px">
                Khoảng cách Gốc đến banner
                <input type="text" id="banner_margin_top" style="text-align: center; width: 30px" value="<?php
                if (isset($treeInfo) && $treeInfo && $treeInfo->banner_margin_top) {
                    echo "$treeInfo->banner_margin_top";
                } else {
                    echo '0';
                }
                ?>">
                <button data-cmd="-" data-min="-50" data-max="100" data-step="2" data-id="banner_margin_top"
                        class="change_number_btn"> -
                </button>
                <button data-cmd="+" data-min="-50" data-max="100" data-step="2" data-id="banner_margin_top"
                        class="change_number_btn"> +
                </button>
            </div>


        </div>

    </div>

    <hr>
    <div class="row input_node" style="font-size: smaller; margin-bottom: 10px">
        <div class="col-sm-12">
            <button style="float: left; left: 40px" class="btn btn-warning"
                    onclick="closeAddDialog('dialog-edit-banner')">Bỏ qua
            </button>
            <button id="save_banner_info1" style="float: right; right: 40px" class="btn btn-success"
                    onclick="clsTreeTopDownCtrl.saveBannerInfo()">Ghi lại
            </button>
        </div>
    </div>

</div>

<div id="dialog-node-add" title="Cập nhật" style="display: none">
    <div id="title_dialog_node" style="margin-bottom: 15px"></div>

    <div class="row input_node" style="font-size: smaller">
        <div class="col-sm-4 label">Họ Tên</div>
        <div class="col-sm-8">
            <input autocomplete="off" class="form-control" type="text" lang="vi" id="new_name">
        </div>
        <div class="col-sm-4 label" style="margin-top: 15px">Danh hiệu</div>
        <div class="col-sm-8">
            <input lang="vi" autocomplete="off" class="form-control" type="text" id="new_title">
            <!--            <input type="radio" id="pos_title_up" name="pos_title">-->
            <!--            <label for="pos_title_up">Trên tên</label>-->
            <!--            <input type="radio" id="pos_title_down" name="pos_title">-->
            <!--            <label for="pos_title_down">Dưới tên</label>-->
        </div>
    </div>
    <div class="row input_node" style="font-size: smaller">
        <div class="col-sm-4 label">Giới tính</div>
        <div class="col-sm-8 genrer_mytree">
            <input lang="vi" autocomplete="off" type="radio" id="new_gender1" name="new_gender" data-val="1" value="1"
                   checked>
            <label for="new_gender1">Nam</label>
            <input lang="vi" autocomplete="off" type="radio" id="new_gender2" name="new_gender" data-val="2" value="2">
            <label for="new_gender2">Nữ</label>
            &nbsp;
            <input class="set_nu_dinh" title="Nữ có thể là vai trò Đinh trong trường hợp cần thiết" lang="vi"
                   autocomplete="off" type="checkbox"
                   id="set_nu_dinh" name="set_nu_dinh" data-val="" value="">
            <i>
                <label title="Nữ có thể là vai trò Đinh trong trường hợp cần thiết" class="set_nu_dinh"
                       for="set_nu_dinh">Đinh</label>
            </i>

        </div>
    </div>
    <div class="row input_node" style="font-size: smaller">
        <div class="col-sm-4 label" autocomplete="off">Ngày sinh</div>
        <div class="col-sm-8">
            <input lang="vi" autocomplete="off" class="form-control" style="" type="text" id="new_birthday">
        </div>
    </div>
    <div class="row input_node" style="font-size: smaller">
        <div class="col-sm-4 label" title="Trong một hàng, số thứ tự cao sẽ lên đầu tiên">Số thứ tự <span
                style="border: 1px solid gray; ; padding: 1px 2px; border-radius: 10px; font-size: smaller"> ? </span>
        </div>
        <div class="col-sm-8">
            <input autocomplete="off" class="form-control" style="" type="text" id="new_orders">
        </div>
    </div>
    <div class="row c" style="font-size: smaller">
        <div class="col-sm-4 label">
            <b>
                Ảnh
            </b>
        </div>
        <div class="col-sm-8">
            <input class="form-control" type="file" accept="image/*" id="file_id" hidden>
            <label class="lb_upload2" for="file_id">Chọn ảnh</label>

            &nbsp;
            <input type="checkbox" id="remove_img_node">
            <label class="remove_img_node" for="remove_img_node">Bỏ ảnh</label>
        </div>
    </div>

    <button onclick="window.open('/member/tree-mng/edit/' + clsTreeTopDownCtrl.doingNodeObj.id, '_blank').focus();"
            class="form-control" style="color: green; margin: 15px 0px;">
        Soạn Tiểu sử
    </button>

    <div onclick="openMoreAttr()" class="view_more_prop" style=""> + Thuộc tính khác</div>

    <div style="clear: both"></div>

    <div class="" id="open_more" style="display: none; position: relative">
        <div class="row input_node" style="font-size: smaller">
            <div class="col-sm-4 label">Số điện thoại</div>
            <div class="col-sm-8">
                <input autocomplete="off" class="form-control" style="" type="tel" id="phone_number">
            </div>
        </div>
        <div class="row input_node" style="font-size: smaller">
            <div class="col-sm-4 label">Email</div>
            <div class="col-sm-8">
                <input autocomplete="off" class="form-control" style="" type="email" id="email_address">
            </div>
        </div>
        <div class="row input_node" style="font-size: smaller">
            <div class="col-sm-4 label">Nơi ở</div>
            <div class="col-sm-8">
                <input lang="vi" autocomplete="off" class="form-control" style="" type="email" id="home_address">
            </div>
        </div>

        <div class="row input_node cls_child_of_second_sp" style="font-size: smaller; display: none">
            <div class="col-sm-4 label">Con của</div>
            <div class="col-sm-8">
                <select name="" id="child_of_second_sp">
                </select>
                <input style="margin-left: 5px" type="checkbox" id="stepchild_of"

                >
                <label for="stepchild_of">
                    Con riêng
                </label>
            </div>
        </div>
        <div class="row input_node" style="font-size: smaller">
            <div class="col-sm-4 label">Ngày mất</div>
            <div class="col-sm-8">
                <input autocomplete="off" class="form-control" style="" type="email" id="date_of_death">
            </div>
        </div>
        <div class="row input_node" style="font-size: smaller">
            <div class="col-sm-4 label">Nơi an nghỉ</div>
            <div class="col-sm-8">
                <input lang="vi" autocomplete="off" class="form-control" style="" type="email" id="place_heaven">
            </div>
        </div>
        <div class="row input_node" style="font-size: smaller">
            <div class="col-sm-4 label">Liên kết ID</div>
            <div class="col-sm-8">
                <input autocomplete="off" class="form-control" style="" type="email" id="link_remote">
            </div>
        </div>
    </div>

    <div class="row input_node" style="font-size: smaller;">
        <div class="col-sm-12">
            <button style="float: left; left: 40px" class="btn btn-warning" id="close_dlg_node_detail"
                    onclick="closeAddDialog()">Bỏ qua
            </button>
            <button id="save_new_member" style="float: right; right: 40px" class="btn btn-info"
                    onclick="clsTreeTopDownCtrl.saveNewInfoNodeUI()">Ghi lại
            </button>
        </div>
    </div>

</div>

<div id="div_move_item_gp_to_folder" style="display: none" title="Chọn vị trí chuyển đến">
    <div style="position: relative; background-color: #ccc;" class="mb-1">
        <div style="" class="select_bg_radio">
            <button class="" id="btn_move_file"> Chuyển đến
            </button>
            <button class="" id="btn_close_move_tree"> Đóng</button>
        </div>

        <div id="tree_root_move_item" style="position: absolute; top: 40px; width: 100%">
        </div>

    </div>
</div>

<div id="div_dialog_support" style="display: none" title="Hỗ trợ sử dụng">
    <div style="position: relative; text-align: center">

        <div style="text-align: left; margin: 0 auto; max-width: 200px; padding-top: 30px">

            <a style="text-decoration: none; " href="https://zalo.me/g/lwfebw839" target="_blank">
                <img src="/images/icon/icon-zalo.png" style="width: 40px"></img>
                <b>
                    Cộng đồng Zalo
                </b>
            </a>
            <br><br>
            <a style="text-decoration: none; " target="_blank"
               href="https://www.youtube.com/watch?v=JatnYPLi_pU&list=PL2ytCDlW-wDcV2gx1UqabbEFMb19R0viY&index=2">
                <img src="/images/icon/youtube-icon.png" style="width: 40px"></img>
                <b>
                    Kênh YouTube
                </b>
            </a>
            <br><br>
            <a style="text-decoration: none; " target="_blank" href="https://www.facebook.com/mytree.vn">
                <img src="/images/icon/facebook-icon2.jpg" style="width: 40px"></img>
                <b>
                    FaceBook
                </b>
            </a>

        </div>
    </div>
</div>

<script src="/vendor/lad_tree/clsTreeJs-v2.js"></script>

<?php
//Nếu đúng userid mới show tree
if ($objTree->user_id != auth()->id()) {
    return;
}

?>


<script>


    const treeFolder2 = new clsTreeJsV2();
    treeFolder2.bind_selector = "#tree_root_move_item"
    treeFolder2.radio1 = true;
    treeFolder2.api_data = '/api/member-tree-mng';
    treeFolder2.root_id = jctool.getUrlParam('pid').replace("#", '')
    treeFolder2.api_suffix_add = 'create';
    treeFolder2.order_by = "orders"
    treeFolder2.order_type = "desc"
    treeFolder2.api_suffix_index = 'tree';
    treeFolder2.api_suffix_rename = 'rename';
    treeFolder2.api_suffix_delete = 'delete';
    treeFolder2.api_suffix_move = 'move';
    treeFolder2.hide_root_node = 0;
    treeFolder2.disable_drag_drop = 1;
    treeFolder2.disable_menu = 1;

    treeFolder2.showTree();

</script>

<script>

    $('.opt_plus button').on('click', function (){
        let cmd = $(this).attr("class")

        let inp = $(this).parent().find('input');
        let idInp = inp.prop('id');
        console.log("Click ....", cmd, idInp);
        let val
        if(cmd == 'plus')
            val =  parseInt(inp.val()) + 2
        else
            val =  parseInt(inp.val()) - 2
        inp.val(val)

        changeValueNodeOption(idInp, val)
    })

    $('.opt_plus input').on('change', function (){
        let idInp = this.id;
        let val =  parseInt(this.value)
        console.log("change ....", idInp, val);
        changeValueNodeOption(idInp, val)
    })

    function changeValueNodeOption(idx, val){
        let fieldName = idx.replace("set_", '');
        console.log("Change ok: ", idx, val);

       //nếu đổi spacex, thì sẽ tính lại vị trí của các node và update db
        if(idx == 'set_space_node_x'){

            let treIns = clsTreeTopDownCtrl.getInstanceSvgById();

            let oldSpaceXDevide = parseInt(treIns.objBannerTop.spaceXBetweenCellDevidedBy);
            let newSpaceXDevide = parseInt(val);

            if(newSpaceXDevide > 10 || newSpaceXDevide < 1)
            {
                alert("Space không hợp lệ!")
                return;
            }

            if(oldSpaceXDevide != newSpaceXDevide)
                treIns.updateSyncXyNodesToTreeAll(newSpaceXDevide)


            clsTreeTopDownCtrl.setValueOneFieldTreeInfo({ [fieldName]: val})
        }
        else{
            clsTreeTopDownCtrl.setValueOneFieldTreeInfo({ [fieldName]: val})
        }
    }

</script>
