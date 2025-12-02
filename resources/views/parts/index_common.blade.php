
<section class="index_block_common">

<?php
if(!$objMeta::isDisableMenuBarIndex()){
?>
<section id="show_action_multi_item" style="">
    <div class="sub_multi" style="">
        <?php
        if(request('in_trash')){
        ?>
        <button ppp49df332875 style="background-color: orange" id="un_delete_item_multi">
            {{ __('index-data.restore') }}
        </button>
        <?php
        }
        else{
        ?>

        <?php
        if(isDebugIp()){
        ?>
        <button title="{{ __('index-data.share_files') }}" data-code-pos="qqq170669975" style="background-color: dodgerblue" id="share_files">
            {{ __('index-data.share') }}
        </button>
        <?php
        }
        ?>

            <?php
        if($mMetaAll ?? '')
        if(($mMetaAll['parent_id'] ?? '')) {
            ?>
        <button title="{{ __('index-data.move_to_folder') }}" data-code-pos="qqq1706687999975" style="background-color: dodgerblue" id="move_item_multi">
            {{ __('index-data.move_item_multi') }}
        </button>
            <?php
        }
            ?>

        <?php
        if($mMetaAll ?? '')
        if(($mMetaAll['parent_extra'] ?? '')) {
        ?>
        <button data-code-pos="qqq1706687999975" style="background-color: dodgerblue" id="add_extra_parent">
           {{ __('index-data.add_to_another_group') }}
        </button>
        <?php
        }
        ?>

        <button data-code-pos="qqq1706688004238" style="background-color: orangered" id="delete_item_multi">{{ __('index-data.delete') }}
        </button>


        <button title="{{ __('index-data.get_all_id_list_selected') }}" data-code-pos="qqq17066879111" style="background-color: #ccc" id="get_id_list">
            {{ __('index-data.id_list') }}
        </button>


        <button data-code-pos="qqq1706688002245" style="background-color: #ccc" id="update_parent_list">{{ __('index-data.update_parent') }}
        </button>
        <?php
        }
        ?>
        <div class="status_delete" style="display: inline"></div>
        <div id="close_multi_action" title="close modal" style="" class="fa fa-times"></div>

    </div>
</section>

<?php
}
?>


    <?php
    $objPr = new \App\Components\clsParamRequestEx();
    $objPr->setParamsEx(\request());

    if(!$mMetaAll){
        echo "<br/>\n Not found MetaAll2";
        return;
    }
    //Kiêểm tra có thẻ add/save item ko
    if($mMetaAll ?? ''){

        $objMeta = end($mMetaAll);
        if($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb);
        $module = 1;
        if(\App\Components\Helper1::isMemberModule()){
            $module = 3;
        }
        $mEditAbleInGetOne = $objMeta->getShowEditAllowFieldList($module);
        $mEditAbleIndex = $objMeta->getEditAllowInIndexFieldList($module);
//        $mEditAbleInGetOne = [1];
//        $mEditAbleIndex = [1];

    }
    ?>

    <style>
        input[type="search"]::-webkit-search-cancel-button {
            -webkit-appearance: none;
            appearance: none;
        }

        .table_grid_index nav{
            display: inline-block;
            /*margin-top: 10px;*/
        }
        .content-header {
            padding-bottom: 5px;
        }

        /*.pagination_div span{*/
        /*    font-size: small;*/
        /*    font-style: italic;*/
        /*}*/

        .pagination_div nav{
            display: inline-block;
        }
        .page-link {
            padding: 3px 8px;
        }
        <?php
        if(request('in_trash')){
        ?>
        .divTable2Cell input {
            text-decoration: line-through;
        }
        <?php
        }
        ?>
    </style>
<div class="row">
    <div class="col-md-10 mb-0" data-code-pos='ppp17319896730641'>

        <div style="padding-left: 0px">
            <?php

            $limit = request('limit');
            if(!$limit)
                $limit = $objMeta::$limitRecord ?: 20;
            $params['limit'] = $limit;

            $total = 0;
            if($dataView)
                $total = $dataView->total() ?? 0;
            $objPr->total_item = $total;
            \LadLib\Common\Database\MetaOfTableInDb::showFormFilterDataGrid($mMetaAll, $params, $objPr);
            ?>

        </div>


    </div>
    <div class="col-md-2 mb-0" data-code-pos='ppp17319896730641'>
        <?php
//        if(isIPDebug())

        $mFieldSearchNotDot = [];
        if($mFieldFS = $objMeta->getFullSearchJoinField())
        {

            $bold = '';
            $findS = request('full_search_join');
            if($findS){
                $bold = 'text-bold';
            }


            $strDescFieldSearch = "";
            foreach ($mFieldFS AS $fields => $typeJoin){
                if(strstr($fields, "."))
                    $fields = explode(".", $fields)[1];
                $mFieldSearchNotDot[] = $fields;
                $strDescFieldSearch .= $objMeta->getDescOfField($fields). ", ";
            }
            $strDescFieldSearch = trim($strDescFieldSearch);
            $strDescFieldSearch = trim($strDescFieldSearch, ",");


        ?>

        <div class="input-group" style="margin-top: 4px;">
            <input id="id_full_search_join" value="{{$findS}}" type="search"
                   class="form-control form-control-sm text-danger  {{$bold}}"
                   placeholder="Tìm nhanh: {{$strDescFieldSearch}}"
                title="Tìm nhanh theo các trường: {{$strDescFieldSearch}}"
            >
            <div class="input-group-append">
                <?php
                if($findS){
                ?>
                <button type="button" class="btn btn-sm btn-default text-danger" id="clear-search">
                    <i class="fa fa-times"></i>
                </button>
                <?php
                }
                ?>
                <button type="submit" id="search_button_glx" class="btn btn-sm btn-default">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>


        <?php



        }

        ?>

    </div>


    <div class="container-fluid table_grid_index mb-2 px-3">

        <div class="row">
            <div class="pagination_div col-sm-6 filter_9" data-code-pos="ppp1676113887607" style="padding-left: 0px">

                <div style="display: flex; align-items: center; margin-top: 8px">
                <?php

                echo '<select class="select_glx abc123"
                onchange="if (this.value) window.location.href=this.value" style="display: inline"> ';
                $urlLimit0 = \LadLib\Common\UrlHelper1::setUrlParam(null, "page", 1);

                $arr = $objMeta->getArraySelectNumber();

                foreach ($arr as $num) {
                    $padSelect = "";
                    if ($limit == $num)
                        $padSelect = " selected id='get_limit_number' data-limit='$num' ";
                    $urlLimit = \LadLib\Common\UrlHelper1::setUrlParam($urlLimit0, "limit", $num);
                    ?>
                <option <?php echo $padSelect ?> value="<?php echo $urlLimit ?>"> Show <?php echo $num; ?>
                </option>
                    <?php
                }
                echo "</select>";

                if(!$dataView)
                    $dataView = [];

                ?>

                {{ ($dataView instanceof \Illuminate\Pagination\LengthAwarePaginator ) ? $dataView?->links() : '' }}
                <span data-code-pos='ppp17041956176401' style="font-size: smaller; margin-left: 5px; color: gray"> {{ ($dataView && $dataView instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $dataView->total() :  count($dataView) }}
                    </span>
                </div>

            </div>

            <div class="col-sm-6  filter_3 mr-0 pr-0" style="" data-code-pos='ppp17323266639701'>
                <?php
                if(!request('in_trash')){
                    ?>
                <div style="z-index: 100000">


                        <?php
                        if($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb);

                        $objMeta->extraContentIndexButton2();

                    if($objMeta::enableAddMultiItem()){
                        ?>
                    <a title="{{ __('index-data.create_multiple_rows') }}" data-code-pos="ppp1665645645363340" href="<?php
                            if(!request('last_order'))
                                echo \LadLib\Common\UrlHelper1::getUriWithoutParam()."?last_order=1";
                            else
                                echo \LadLib\Common\UrlHelper1::getUriWithoutParam();
                        ?>"
                       style="<?php if(request('last_order')) echo ';color: white; background-color: red'; else echo ';color: red;' ?>"
                       class="btn btn-default btn-sm float-right mt-2 ml-3">
                        <i  class="fa fa-plus"></i> ...

                    </a>

                        <?php
                    }
                    if(!$objMeta::$disableAddItem && $mEditAbleInGetOne && !$objMeta::isDisableAddItemIndex()){
                        ?>

                    <a title="{{ __('index-data.add_new_item') }}" data-code-pos="ppp1667661363340" id="add-new-item" href="<?php echo $objMeta->getAdminUrlWeb($objPr->module) . "/create" ?>"
                       class="btn btn-primary btn-sm float-right mt-2 ml-3">
                        <i  class="fa fa-plus"></i>
                        {{ __('index-data.create') }} </a>
                        <?php
                    }

                    if(!$objMeta::$disableSaveAllButton  && $mEditAbleIndex && !$objMeta::isDisableSaveAllItemIndex()){
                        ?>

                    <a title="{{ __('index-data.save_all_title') }}" id="save-all-data"
                       class="btn btn-primary btn-sm float-right mt-2 ml-3">
                        <i  class="fa fa-save"></i>
                        {{ __('index-data.save_all') }} </a>

                        <?php
                    }
                        ?>

                    <i title="Export to Excel" onclick='$("#export_to_ecxel").click()'
                       style="float: right; color: gray; margin-top: 14px; font-size: 1.4em" class="fa fa-file-excel ml-3"></i>

                    <?php


                    if(!$objMeta::isDisableTrashIndex())
                    {

                    ?>

                    <a title="Go to Trash" style="" href="<?php echo $objMeta->getAdminUrlWeb($objPr->module) . "?in_trash=1" ?>"
                       class="float-right">
                        <button class="btn_trash btn btn-default  btn-sm ml-3 mt-2">
                            <i class="fa fa-trash "></i>
                        </button>

                    </a>

                    <?php
                    }
                    ?>

                        <?php

                        $objMeta->extraContentIndexButton1();
                        ?>

                </div>
                    <?php
                }
                else{
                    ?>
                <a href="<?php echo $objMeta->getAdminUrlWeb($objPr->module) ?>"
                   class="btn_trash btn btn-sm btn-primary float-right m-2">
                    <i title="return index" class="fa fa-arrow-left "></i></a>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-12 mt-2">
        <?php

        if(\App\Components\Helper1::isAdminModule(request()))
            $gid = 1;
        else
            $gid = 3;

        \LadLib\Common\Database\MetaOfTableInDb::showDataTableDataGrid($dataView, $mMetaAll, $dataApiUrl, $params, $objParamEx);
        ?>

        <div data-code-pos="ppp1681607924563" class="pagination_div mt-3">
            <?php
            ?>
            {{ ($dataView instanceof \Illuminate\Pagination\LengthAwarePaginator ) ? $dataView?->links() : '' }}
            <span style="font-size: smaller; margin-left: 5px; color: gray" > Tổng số: {{ ($dataView instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $dataView->total() : count($dataView) }}
            </span>
        </div>
    </div>
</div>

<div id="common_dialog" style="display: none" title="Select item">
    <div id="tree_root_to_do" style="">
    </div>

    <div class="div_common_sub" style="text-align: center; margin: 15px;">
        <input placeholder="Search value to set" type="text"
               id="search_autocomplete_this_value_to_all_item_field" style="display: none">

        <div title="clear all" id="found_search_autocomplete_this_value_to_all_item_field"
             style="display: none">
            <span style="float: right">x</span>
        </div>

        <input placeholder="Set value (string/number)" type="text"
               id="input_set_this_value_to_all_item_field" style="display: none">

        <div id="number_of_item_selected" style="">
        </div>

        <div id="html_zone_api">

        </div>

        <div>
            <button class="btn btn-primary btn-sm" id="btn_set_value_all_item_selecting"> Set
                Value All
            </button>
            <button class="btn btn-default btn-sm" id="btn_close_select_tree"> Đóng </button>
        </div>
    </div>
</div>

</section>


<script>

    let mFieldFullSearch = <?php echo json_encode($mFieldSearchNotDot) ?>;

    function removeVietnameseDiacritics(str) {
        const map = {
            'à': 'a', 'á': 'a', 'ạ': 'a', 'ả': 'a', 'ã': 'a', 'â': 'a', 'ầ': 'a', 'ấ': 'a', 'ậ': 'a', 'ẩ': 'a', 'ẫ': 'a', 'ă': 'a', 'ằ': 'a', 'ắ': 'a', 'ặ': 'a', 'ẳ': 'a', 'ẵ': 'a',
            'è': 'e', 'é': 'e', 'ẹ': 'e', 'ẻ': 'e', 'ẽ': 'e', 'ê': 'e', 'ề': 'e', 'ế': 'e', 'ệ': 'e', 'ể': 'e', 'ễ': 'e',
            'ì': 'i', 'í': 'i', 'ị': 'i', 'ỉ': 'i', 'ĩ': 'i',
            'ò': 'o', 'ó': 'o', 'ọ': 'o', 'ỏ': 'o', 'õ': 'o', 'ô': 'o', 'ồ': 'o', 'ố': 'o', 'ộ': 'o', 'ổ': 'o', 'ỗ': 'o', 'ơ': 'o', 'ờ': 'o', 'ớ': 'o', 'ợ': 'o', 'ở': 'o', 'ỡ': 'o',
            'ù': 'u', 'ú': 'u', 'ụ': 'u', 'ủ': 'u', 'ũ': 'u', 'ư': 'u', 'ừ': 'u', 'ứ': 'u', 'ự': 'u', 'ử': 'u', 'ữ': 'u',
            'ỳ': 'y', 'ý': 'y', 'ỵ': 'y', 'ỷ': 'y', 'ỹ': 'y',
            'đ': 'd',
            'À': 'A', 'Á': 'A', 'Ạ': 'A', 'Ả': 'A', 'Ã': 'A', 'Â': 'A', 'Ầ': 'A', 'Ấ': 'A', 'Ậ': 'A', 'Ẩ': 'A', 'Ẫ': 'A', 'Ă': 'A', 'Ằ': 'A', 'Ắ': 'A', 'Ặ': 'A', 'Ẳ': 'A', 'Ẵ': 'A',
            'È': 'E', 'É': 'E', 'Ẹ': 'E', 'Ẻ': 'E', 'Ẽ': 'E', 'Ê': 'E', 'Ề': 'E', 'Ế': 'E', 'Ệ': 'E', 'Ể': 'E', 'Ễ': 'E',
            'Ì': 'I', 'Í': 'I', 'Ị': 'I', 'Ỉ': 'I', 'Ĩ': 'I',
            'Ò': 'O', 'Ó': 'O', 'Ọ': 'O', 'Ỏ': 'O', 'Õ': 'O', 'Ô': 'O', 'Ồ': 'O', 'Ố': 'O', 'Ộ': 'O', 'Ổ': 'O', 'Ỗ': 'O', 'Ơ': 'O', 'Ờ': 'O', 'Ớ': 'O', 'Ợ': 'O', 'Ở': 'O', 'Ỡ': 'O',
            'Ù': 'U', 'Ú': 'U', 'Ụ': 'U', 'Ủ': 'U', 'Ũ': 'U', 'Ư': 'U', 'Ừ': 'U', 'Ứ': 'U', 'Ự': 'U', 'Ử': 'U', 'Ữ': 'U',
            'Ỳ': 'Y', 'Ý': 'Y', 'Ỵ': 'Y', 'Ỷ': 'Y', 'Ỹ': 'Y',
            'Đ': 'D'
        };
        return str.split('').map(char => map[char] || char).join('');
    }

    // // Example usage
    // let vietnameseText = "Tiếng Việt có dấu";
    // let textWithoutDiacritics = removeVietnameseDiacritics(vietnameseText);


    function performSearch(key) {
        let searchValue = document.getElementById('id_full_search_join').value;
        let currentUrl = new URL(window.location.href);

        if (searchValue.length === 0 && key === 'Enter') {
            currentUrl.searchParams.delete('full_search_join');
            window.location.href = currentUrl.toString();
            return;
        }

        //Toi thieu 2 ky tu
        if (searchValue.length < 2 && key === 'Enter') {
            showToastWarningTop("Nhập tối thiểu 2 ký tự để tìm kiếm")
            return;
        }

        let urlParams = new URLSearchParams(window.location.search);
        if (!urlParams.has('full_search_join') && key === 'Escape') {
            return;
        }


        currentUrl.searchParams.delete('page'); // Remove the 'page' parameter if it exists

        //Neu khong co searchValue thi xoa param full_search_join
        if (searchValue)
            currentUrl.searchParams.set('full_search_join', searchValue);
        else
            currentUrl.searchParams.delete('full_search_join');

        window.location.href = currentUrl.toString();


    }
    document.getElementById('id_full_search_join')?.addEventListener('keyup', function(event) {
        if (event.key === 'Enter' || event.key === 'Escape') {
            event.preventDefault();
            performSearch(event.key);
        }
    });

    document.getElementById('search_button_glx')?.addEventListener('click', function() {
        performSearch();
    });

    document.addEventListener('DOMContentLoaded', function() {
        let searchInput = document.getElementById('id_full_search_join');
        if(!searchInput)
            return;

        let urlParams = new URLSearchParams(window.location.search);
        // if (urlParams.has('full_search_join'))
        {
            searchInput.focus();
            searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
        }

        let searchInputLower = searchInput.value.toLowerCase();
        if(!searchInputLower)
            return;
        searchInputLower = removeVietnameseDiacritics(searchInputLower);
        //Tất cả các input_value_to_post   , nếu có chứa searchInput thì text chuyển màu đỏ
        let input_value_to_post = document.querySelectorAll('.input_value_to_post');
        input_value_to_post.forEach(function(item) {
            let val = item.value.toLowerCase();
            //Chuyển từ có dấu sang không dấu
            val = removeVietnameseDiacritics(val);

            if (val.includes(searchInputLower)) {
                item.style.color = 'red';
            }
        });

        //Tìm tất cả các div.divTable2Cell có chứa searchInput thì text chuyển màu đỏ mọi phần tuwr con cháu
// Select all elements with the class 'divTable2Cell'
        let divTable2Cell = document.querySelectorAll('.divTable2Cell');

        // divTable2Cell data-table-field

// Iterate over each element
        divTable2Cell.forEach(function(item) {

            let dataTableField = item.getAttribute('data-table-field');

            //Không return , vì có thể có trường không có trong mFieldFullSearch, vì JoinField
            if (!mFieldFullSearch.includes(dataTableField)) {
                // return;
            }

            // Get all child elements of the current 'divTable2Cell' element
            let children = item.querySelectorAll('*');
            // Iterate over each child element
            children.forEach(function(child) {
                // Replace the child element with its text content
                // child.replaceWith(document.createTextNode(child.textContent));
                console.log(" child.textContent " , child.textContent);
                val = removeVietnameseDiacritics(child.textContent);
                val = val.toLowerCase();

                if (val.includes(searchInputLower)) {

                    console.log(" FOUND *** ", val);

                    child.style.color = 'red';
                }
            });
        });



    });



    document.getElementById('clear-search')?.addEventListener('click', function() {
        let currentUrl = new URL(window.location.href);
        currentUrl.searchParams.delete('full_search_join');
        window.location.href = currentUrl.toString();
    });
</script>
