<?php
?>

@php
    $template = "layouts.member";
@endphp

@extends($template)

{{--@extends("layouts.member")--}}

@section("title")

    TEST001

@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet"
          href="/admins/table_mng.css?v=<?php echo filemtime(public_path() . "/admins/table_mng.css") ?>">
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">

    <link rel="stylesheet" href="/vendor/lad_tree/clsTreeJs-v1.css?v=<?php echo filemtime(public_path().'/vendor/lad_tree/clsTreeJs-v1.css'); ?>">
    <link rel="stylesheet"
          href="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.css">

    <link rel="stylesheet" href="/admins/img_list.css">
    <link rel="stylesheet" href="/assert/js/date-time-picker/jquery.datetimepicker.css">

    <style>
        .ui-dialog {
            z-index: 10000 !important;
        }
    </style>

@endsection

@section('js')

    <script src="/admins/table_mng.js?v=<?php echo filemtime(base_path() . "/public/admins/table_mng.js") ?>"></script>
    <script
        src="/vendor/div_table2/div_table2.js?v=<?php echo filemtime(base_path() . "/public/vendor/div_table2/div_table2.js") ?>"></script>
    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>
    <script src="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.js"></script>
    <script
        src="/vendor/lad_tree/clsTreeJs-v2.js?v=<?php echo filemtime(base_path() . "/public/vendor/lad_tree/clsTreeJs-v2.js") ?>"></script>
    <script
        src="{{asset("admins/tree_selector.js")}}?v=<?php echo filemtime(base_path() . "/public/admins/tree_selector.js") ?>"></script>

    <script src="/assert/js/date-time-picker/php-date-formatter.js"></script>
    <script src="/assert/js/date-time-picker/jquery.datetimepicker.js"></script>

@endsection

@section("content")

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <div class="content">
            <div class="container-fluid">


                <br><br>

                <div class="col-md-12">
                    <form id="form_data">
                        <div class="divTable2 divContainer">
                            <div class="divTable2Body">
                                <div class="divTable2Row divTable2Heading1">
                                    <div class="divTable2Cell text-center div_select_all_check">
                                        <input class="select_all_check select_one_check" type="checkbox"
                                               title="Select All">
                                    </div>
                                    <div class="divTable2Cell cellHeader"> Action</div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort id">

                                        <a data-code-pos="ppp1666347484461" data-tester="sort_field_id"
                                           href="/admin/demo-api?soby_s2=desc">Id</a></div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort name">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have=""
                                           data-api-if-have="" data-type-field="3" data-field="name"
                                           title="tool for name"></i><a data-code-pos="ppp1666347484461"
                                                                        data-tester="sort_field_name"
                                                                        href="/admin/demo-api?soby_s21=desc">Name</a>
                                    </div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort tag_list_id">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have="name"
                                           data-api-if-have="/api/tags/search" data-type-field="12"
                                           data-field="tag_list_id" title="tool for tag_list_id"></i><a
                                            data-code-pos="ppp1666347484461" data-tester="sort_field_tag_list_id"
                                            href="/admin/demo-api?soby_s12=desc">Tag list</a></div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort status">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have=""
                                           data-api-if-have="" data-type-field="1" data-field="status"
                                           title="tool for status"></i><a data-code-pos="ppp1666347484461"
                                                                          data-tester="sort_field_status"
                                                                          href="/admin/demo-api?soby_s13=desc">Public</a>
                                    </div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort user_id">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have="email"
                                           data-api-if-have="/api/user/search" data-type-field="2" data-field="user_id"
                                           title="tool for user_id"></i><a data-code-pos="ppp1666347484461"
                                                                           data-tester="sort_field_user_id"
                                                                           href="/admin/demo-api?soby_s5=desc">User
                                            id</a></div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort textarea1">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have=""
                                           data-api-if-have="" data-type-field="" data-field="textarea1"
                                           title="tool for textarea1"></i><a data-code-pos="ppp1666347484461"
                                                                             data-tester="sort_field_textarea1"
                                                                             href="/admin/demo-api?soby_s10=desc">Textarea1</a>
                                    </div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort string2">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have=""
                                           data-api-if-have="" data-type-field="23" data-field="string2"
                                           title="tool for string2"></i><a data-code-pos="ppp1666347484461"
                                                                           data-tester="sort_field_string2"
                                                                           href="/admin/demo-api?soby_s9=desc">String2</a>
                                    </div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort string1">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have=""
                                           data-api-if-have="" data-type-field="" data-field="string1"
                                           title="tool for string1"></i><a data-code-pos="ppp1666347484461"
                                                                           data-tester="sort_field_string1"
                                                                           href="/admin/demo-api?soby_s8=desc">String1</a>
                                    </div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort number2">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have=""
                                           data-api-if-have="" data-type-field="" data-field="number2"
                                           title="tool for number2"></i><a data-code-pos="ppp1666347484461"
                                                                           data-tester="sort_field_number2"
                                                                           href="/admin/demo-api?soby_s7=desc">Number2</a>
                                    </div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort parent_id">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have=""
                                           data-api-if-have="/api/demo-folder" data-type-field="25"
                                           data-field="parent_id" title="tool for parent_id"></i>Parent 1
                                    </div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort number1">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have=""
                                           data-api-if-have="" data-type-field="" data-field="number1"
                                           title="tool for number1"></i><a data-code-pos="ppp1666347484461"
                                                                           data-tester="sort_field_number1"
                                                                           href="/admin/demo-api?soby_s6=desc">Number1</a>
                                    </div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort updated_at">

                                        Ngày sửa
                                    </div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort created_at">

                                        <a data-code-pos="ppp1666347484461" data-tester="sort_field_created_at"
                                           href="/admin/demo-api?soby_s3=desc">Ngày tạo</a></div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort textarea2">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have=""
                                           data-api-if-have="" data-type-field="" data-field="textarea2"
                                           title="tool for textarea2"></i>Textarea2
                                    </div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort parent2">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have=""
                                           data-api-if-have="/api/demo-folder" data-type-field="25" data-field="parent2"
                                           title="tool for parent2"></i><a data-code-pos="ppp1666347484461"
                                                                           data-tester="sort_field_parent2"
                                                                           href="/admin/demo-api?soby_s15=desc">Parent2</a>
                                    </div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort parent_multi">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have=""
                                           data-api-if-have="/api/demo-folder" data-type-field="26"
                                           data-field="parent_multi" title="tool for parent_multi"></i><a
                                            data-code-pos="ppp1666347484461" data-tester="sort_field_parent_multi"
                                            href="/admin/demo-api?soby_s16=desc">Parent multi</a></div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort parent_multi2">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have=""
                                           data-api-if-have="/api/demo-folder" data-type-field="26"
                                           data-field="parent_multi2" title="tool for parent_multi2"></i><a
                                            data-code-pos="ppp1666347484461" data-tester="sort_field_parent_multi2"
                                            href="/admin/demo-api?soby_s17=desc">Parent multi2</a></div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort image_list1">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have=""
                                           data-api-if-have="" data-type-field="27" data-field="image_list1"
                                           title="tool for image_list1"></i>Img list1
                                    </div>
                                    <div data-code-pos="ppp1666347493381" class="divTable2Cell cellHeader"
                                         title="Sort image_list2">

                                        <i class="fa fa-cog icon_tool_for_field" data-search-field-if-have=""
                                           data-api-if-have="" data-type-field="27" data-field="image_list2"
                                           title="tool for image_list2"></i>Img list2
                                    </div>
                                </div>
                                <div  class="divTable2Row" data-id="1424">
                                    <div class="divTable2Cell div_select_one_check">
                                        <input type="checkbox" class="select_one_check" data-id="1424">
                                    </div>
                                    <div class="divTable2Cell text-center">
                                        <a href="/admin/demo-api/edit/1424"><i title="Edit" class="fa fa-edit "
                                                                               style="font-size: 20px; margin: 2px;"></i></a>
                                        <i title="Save" style="font-size: 21px; margin: 2px; color: dodgerblue"
                                           class="fa fa-save save_one_item" data-id="1424"></i>
                                    </div>
                                    <div data-joinfunc="0" data-id="1424" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="id"
                                         data-tablerow="0" data-edit-able="0" data-tablecol="0" title="">
                                        <div class="id_data"> 1424</div>
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly=""
                                               class="input_value_to_post readonly  text-center  id " data-field="id"
                                               type="text" data-autocomplete-id="1424-id" value="1424" name="id[]"
                                               title="1424" data-id="1424" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1424" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="name"
                                         data-tablerow="0" data-edit-able="1" data-tablecol="1" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Name"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   name " data-field="name" type="text"
                                               data-autocomplete-id="1424-name" value="" name="name[]" title=""
                                               data-id="1424" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::tag_list_id" data-id="1424"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="1" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="tag_list_id" data-tablerow="0" data-edit-able="1"
                                         data-tablecol="2" title="">
                                        <div data-join-val="1424-tag_list_id" data-code-pos="ppp1665495430328"
                                             class="search-auto-complete-tbl ui-autocomplete-input" style=""
                                             autocomplete="off"></div>
                                        <input data-code-pos="ppp1667865466084" placeholder="Search Tag list" style=""
                                               data-autocomplete-id="1424-tag_list_id"
                                               class="search-auto-complete-tbl ui-autocomplete-input"
                                               data-api-search="/api/tags/search" data-opt-field=""
                                               data-api-search-field="name" type="text" value="" autocomplete="off">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Tag list"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   tag_list_id " data-field="tag_list_id"
                                               type="text" data-autocomplete-id="1424-tag_list_id" value=""
                                               name="tag_list_id[]" title="" data-id="1424" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1424" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="status"
                                         data-tablerow="0" data-edit-able="1" data-tablecol="3" title="">
                                        <div title="status" class="text-center"><i data-code-pos="ppp1681816931570"
                                                                                   data-id="1424" data-field="status"
                                                                                   class="fa fa-toggle-off change_status_item"></i>
                                        </div>
                                        <input data-lpignore="true" autocomplete="off" placeholder="Public"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   status " data-field="status" type="text"
                                               data-autocomplete-id="1424-status" value="0" name="status[]" title="0"
                                               data-id="1424" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::user_id" data-id="1424"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="user_id" data-tablerow="0" data-edit-able="1"
                                         data-tablecol="4" title="">
                                        <div data-join-val="1424-user_id" data-code-pos="ppp1665495430328"
                                             class="search-auto-complete-tbl ui-autocomplete-input" style=""
                                             autocomplete="off"></div>
                                        <input data-code-pos="ppp1667865466084" placeholder="Search User_id" style=""
                                               data-autocomplete-id="1424-user_id"
                                               class="search-auto-complete-tbl ui-autocomplete-input"
                                               data-api-search="/api/user/search" data-opt-field=""
                                               data-api-search-field="email" type="text" value="" autocomplete="off">
                                        <input data-lpignore="true" autocomplete="off" placeholder="User_id"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   user_id " data-field="user_id" type="text"
                                               data-autocomplete-id="1424-user_id" value="" name="user_id[]" title=""
                                               data-id="1424" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1424" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="textarea1"
                                         data-tablerow="0" data-edit-able="1" data-tablecol="5" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Textarea1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   textarea1 " data-field="textarea1"
                                               type="text" data-autocomplete-id="1424-textarea1" value="1685462255"
                                               name="textarea1[]" title="1685462255" data-id="1424" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::string2" data-id="1424"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="string2" data-tablerow="0" data-edit-able="1"
                                         data-tablecol="6" title="">
                                        <select data-code-pos="ppp1665411195425433" data-id="1424" data-joinfunc="1"
                                                class="sl_option " style="" data-field="string2">
                                            <option value="0" selected=""> ---</option>
                                            <option value="1"> Hà Nội</option>
                                            <option value="2"> HCM</option>
                                            <option value="3"> Huế</option>
                                            <option value="4"> Đà nẵng</option>
                                            <option value="5"> Phú Quốc</option>
                                        </select> <input data-lpignore="true" autocomplete="off" placeholder="String2"
                                                         data-edit-able="1" data-code-pos="ppp166549509"
                                                         class="input_value_to_post   string2 " data-field="string2"
                                                         type="text" data-autocomplete-id="1424-string2" value="0"
                                                         name="string2[]" title="0" data-id="1424"
                                                         style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1424" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="string1"
                                         data-tablerow="0" data-edit-able="1" data-tablecol="7" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="String1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   string1 " data-field="string1" type="text"
                                               data-autocomplete-id="1424-string1" value="1685462139" name="string1[]"
                                               title="1685462139" data-id="1424" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1424" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="number2"
                                         data-tablerow="0" data-edit-able="1" data-tablecol="8" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Number2"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   number2 " data-field="number2" type="text"
                                               data-autocomplete-id="1424-number2" value="" name="number2[]" title=""
                                               data-id="1424" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_id" data-id="1424"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_id" data-tablerow="0" data-edit-able="1"
                                         data-tablecol="9" title="">
                                        <span class="tree_select"> </span> <input data-lpignore="true"
                                                                                  autocomplete="off"
                                                                                  placeholder="Parent 1"
                                                                                  data-edit-able="1"
                                                                                  data-code-pos="ppp166549509"
                                                                                  class="input_value_to_post   parent_id "
                                                                                  data-field="parent_id" type="text"
                                                                                  data-autocomplete-id="1424-parent_id"
                                                                                  value="" name="parent_id[]" title=""
                                                                                  data-id="1424"
                                                                                  style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1424" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="number1"
                                         data-tablerow="0" data-edit-able="1" data-tablecol="10" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Number1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   number1 " data-field="number1" type="text"
                                               data-autocomplete-id="1424-number1" value="1685462139" name="number1[]"
                                               title="1685462139" data-id="1424" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1424" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="updated_at" data-tablerow="0" data-edit-able="0"
                                         data-tablecol="11" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  updated_at " data-field="updated_at"
                                               type="text" data-autocomplete-id="1424-updated_at"
                                               value="2023-05-31 12:27:01" name="updated_at[]"
                                               title="2023-05-31 12:27:01" data-id="1424" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1424" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="created_at" data-tablerow="0" data-edit-able="0"
                                         data-tablecol="12" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  created_at " data-field="created_at"
                                               type="text" data-autocomplete-id="1424-created_at"
                                               value="2023-05-30 22:55:40" name="created_at[]"
                                               title="2023-05-30 22:55:40" data-id="1424" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1424" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="textarea2"
                                         data-tablerow="0" data-edit-able="1" data-tablecol="13" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Textarea2"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   textarea2 " data-field="textarea2"
                                               type="text" data-autocomplete-id="1424-textarea2" value=""
                                               name="textarea2[]" title="" data-id="1424" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent2" data-id="1424"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent2" data-tablerow="0" data-edit-able="1"
                                         data-tablecol="14" title="">
                                        <span class="tree_select"> 4</span> <input data-lpignore="true"
                                                                                   autocomplete="off"
                                                                                   placeholder="Parent2"
                                                                                   data-edit-able="1"
                                                                                   data-code-pos="ppp166549509"
                                                                                   class="input_value_to_post   parent2 "
                                                                                   data-field="parent2" type="text"
                                                                                   data-autocomplete-id="1424-parent2"
                                                                                   value="4" name="parent2[]" title="4"
                                                                                   data-id="1424"
                                                                                   style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_multi" data-id="1424"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_multi" data-tablerow="0" data-edit-able="1"
                                         data-tablecol="15" title="">
                                        <span class="tree_select">  </span> <input data-lpignore="true"
                                                                                   autocomplete="off"
                                                                                   placeholder="Parent multi"
                                                                                   data-edit-able="1"
                                                                                   data-code-pos="ppp166549509"
                                                                                   class="input_value_to_post   parent_multi "
                                                                                   data-field="parent_multi" type="text"
                                                                                   data-autocomplete-id="1424-parent_multi"
                                                                                   value="" name="parent_multi[]"
                                                                                   title="" data-id="1424"
                                                                                   style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_multi2" data-id="1424"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_multi2" data-tablerow="0" data-edit-able="1"
                                         data-tablecol="16" title="">
                                        <span class="tree_select">  </span> <input data-lpignore="true"
                                                                                   autocomplete="off"
                                                                                   placeholder="Parent multi2"
                                                                                   data-edit-able="1"
                                                                                   data-code-pos="ppp166549509"
                                                                                   class="input_value_to_post   parent_multi2 "
                                                                                   data-field="parent_multi2"
                                                                                   type="text"
                                                                                   data-autocomplete-id="1424-parent_multi2"
                                                                                   value="" name="parent_multi2[]"
                                                                                   title="" data-id="1424"
                                                                                   style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::image_list1" data-id="1424"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="image_list1" data-tablerow="0" data-edit-able="0"
                                         data-tablecol="17" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  image_list1 "
                                               data-field="image_list1" type="text"
                                               data-autocomplete-id="1424-image_list1" value="" name="image_list1[]"
                                               title="" data-id="1424" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::image_list2" data-id="1424"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="image_list2" data-tablerow="0" data-edit-able="0"
                                         data-tablecol="18" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  image_list2 "
                                               data-field="image_list2" type="text"
                                               data-autocomplete-id="1424-image_list2" value="" name="image_list2[]"
                                               title="" data-id="1424" style="; ">
                                    </div>
                                </div>
                                <div  class="divTable2Row" data-id="1423">
                                    <div class="divTable2Cell div_select_one_check">
                                        <input type="checkbox" class="select_one_check" data-id="1423">
                                    </div>
                                    <div class="divTable2Cell text-center">
                                        <a href="/admin/demo-api/edit/1423"><i title="Edit" class="fa fa-edit "
                                                                               style="font-size: 20px; margin: 2px;"></i></a>
                                        <i title="Save" style="font-size: 21px; margin: 2px; color: dodgerblue"
                                           class="fa fa-save save_one_item" data-id="1423"></i>
                                    </div>
                                    <div data-joinfunc="0" data-id="1423" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="id"
                                         data-tablerow="1" data-edit-able="0" data-tablecol="0" title="">
                                        <div class="id_data"> 1423</div>
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly=""
                                               class="input_value_to_post readonly  text-center  id " data-field="id"
                                               type="text" data-autocomplete-id="1423-id" value="1423" name="id[]"
                                               title="1423" data-id="1423" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1423" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="name"
                                         data-tablerow="1" data-edit-able="1" data-tablecol="1" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Name"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   name " data-field="name" type="text"
                                               data-autocomplete-id="1423-name" value="" name="name[]" title=""
                                               data-id="1423" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::tag_list_id" data-id="1423"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="1" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="tag_list_id" data-tablerow="1" data-edit-able="1"
                                         data-tablecol="2" title="">
                                        <div data-join-val="1423-tag_list_id" data-code-pos="ppp1665495430328"
                                             class="search-auto-complete-tbl ui-autocomplete-input" style=""
                                             autocomplete="off"></div>
                                        <input data-code-pos="ppp1667865466084" placeholder="Search Tag list" style=""
                                               data-autocomplete-id="1423-tag_list_id"
                                               class="search-auto-complete-tbl ui-autocomplete-input"
                                               data-api-search="/api/tags/search" data-opt-field=""
                                               data-api-search-field="name" type="text" value="" autocomplete="off">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Tag list"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   tag_list_id " data-field="tag_list_id"
                                               type="text" data-autocomplete-id="1423-tag_list_id" value=""
                                               name="tag_list_id[]" title="" data-id="1423" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1423" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="status"
                                         data-tablerow="1" data-edit-able="1" data-tablecol="3" title="">
                                        <div title="status" class="text-center"><i data-code-pos="ppp1681816931570"
                                                                                   data-id="1423" data-field="status"
                                                                                   class="fa fa-toggle-off change_status_item"></i>
                                        </div>
                                        <input data-lpignore="true" autocomplete="off" placeholder="Public"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   status " data-field="status" type="text"
                                               data-autocomplete-id="1423-status" value="0" name="status[]" title="0"
                                               data-id="1423" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::user_id" data-id="1423"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="user_id" data-tablerow="1" data-edit-able="1"
                                         data-tablecol="4" title="">
                                        <div data-join-val="1423-user_id" data-code-pos="ppp1665495430328"
                                             class="search-auto-complete-tbl ui-autocomplete-input" style=""
                                             autocomplete="off"></div>
                                        <input data-code-pos="ppp1667865466084" placeholder="Search User_id" style=""
                                               data-autocomplete-id="1423-user_id"
                                               class="search-auto-complete-tbl ui-autocomplete-input"
                                               data-api-search="/api/user/search" data-opt-field=""
                                               data-api-search-field="email" type="text" value="" autocomplete="off">
                                        <input data-lpignore="true" autocomplete="off" placeholder="User_id"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   user_id " data-field="user_id" type="text"
                                               data-autocomplete-id="1423-user_id" value="" name="user_id[]" title=""
                                               data-id="1423" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1423" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="textarea1"
                                         data-tablerow="1" data-edit-able="1" data-tablecol="5" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Textarea1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   textarea1 " data-field="textarea1"
                                               type="text" data-autocomplete-id="1423-textarea1" value="1685462255"
                                               name="textarea1[]" title="1685462255" data-id="1423" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::string2" data-id="1423"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="string2" data-tablerow="1" data-edit-able="1"
                                         data-tablecol="6" title="">
                                        <select data-code-pos="ppp1665411195425433" data-id="1423" data-joinfunc="1"
                                                class="sl_option " style="" data-field="string2">
                                            <option value="0" selected=""> ---</option>
                                            <option value="1"> Hà Nội</option>
                                            <option value="2"> HCM</option>
                                            <option value="3"> Huế</option>
                                            <option value="4"> Đà nẵng</option>
                                            <option value="5"> Phú Quốc</option>
                                        </select> <input data-lpignore="true" autocomplete="off" placeholder="String2"
                                                         data-edit-able="1" data-code-pos="ppp166549509"
                                                         class="input_value_to_post   string2 " data-field="string2"
                                                         type="text" data-autocomplete-id="1423-string2" value="0"
                                                         name="string2[]" title="0" data-id="1423"
                                                         style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1423" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="string1"
                                         data-tablerow="1" data-edit-able="1" data-tablecol="7" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="String1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   string1 " data-field="string1" type="text"
                                               data-autocomplete-id="1423-string1" value="1685287689" name="string1[]"
                                               title="1685287689" data-id="1423" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1423" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="number2"
                                         data-tablerow="1" data-edit-able="1" data-tablecol="8" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Number2"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   number2 " data-field="number2" type="text"
                                               data-autocomplete-id="1423-number2" value="" name="number2[]" title=""
                                               data-id="1423" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_id" data-id="1423"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_id" data-tablerow="1" data-edit-able="1"
                                         data-tablecol="9" title="">
                                        <span class="tree_select"> 1</span> <input data-lpignore="true"
                                                                                   autocomplete="off"
                                                                                   placeholder="Parent 1"
                                                                                   data-edit-able="1"
                                                                                   data-code-pos="ppp166549509"
                                                                                   class="input_value_to_post   parent_id "
                                                                                   data-field="parent_id" type="text"
                                                                                   data-autocomplete-id="1423-parent_id"
                                                                                   value="1" name="parent_id[]"
                                                                                   title="1" data-id="1423"
                                                                                   style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1423" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="number1"
                                         data-tablerow="1" data-edit-able="1" data-tablecol="10" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Number1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   number1 " data-field="number1" type="text"
                                               data-autocomplete-id="1423-number1" value="1" name="number1[]" title="1"
                                               data-id="1423" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1423" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="updated_at" data-tablerow="1" data-edit-able="0"
                                         data-tablecol="11" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  updated_at " data-field="updated_at"
                                               type="text" data-autocomplete-id="1423-updated_at"
                                               value="2023-05-30 22:57:52" name="updated_at[]"
                                               title="2023-05-30 22:57:52" data-id="1423" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1423" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="created_at" data-tablerow="1" data-edit-able="0"
                                         data-tablecol="12" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  created_at " data-field="created_at"
                                               type="text" data-autocomplete-id="1423-created_at"
                                               value="2023-05-28 22:28:09" name="created_at[]"
                                               title="2023-05-28 22:28:09" data-id="1423" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1423" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="textarea2"
                                         data-tablerow="1" data-edit-able="1" data-tablecol="13" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Textarea2"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   textarea2 " data-field="textarea2"
                                               type="text" data-autocomplete-id="1423-textarea2" value="1685461437.88"
                                               name="textarea2[]" title="1685461437.88" data-id="1423" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent2" data-id="1423"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent2" data-tablerow="1" data-edit-able="1"
                                         data-tablecol="14" title="">
                                        <span class="tree_select"> 1</span> <input data-lpignore="true"
                                                                                   autocomplete="off"
                                                                                   placeholder="Parent2"
                                                                                   data-edit-able="1"
                                                                                   data-code-pos="ppp166549509"
                                                                                   class="input_value_to_post   parent2 "
                                                                                   data-field="parent2" type="text"
                                                                                   data-autocomplete-id="1423-parent2"
                                                                                   value="1" name="parent2[]" title="1"
                                                                                   data-id="1423"
                                                                                   style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_multi" data-id="1423"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_multi" data-tablerow="1" data-edit-able="1"
                                         data-tablecol="15" title="">
                                        <span class="tree_select"> 1,2 </span> <input data-lpignore="true"
                                                                                      autocomplete="off"
                                                                                      placeholder="Parent multi"
                                                                                      data-edit-able="1"
                                                                                      data-code-pos="ppp166549509"
                                                                                      class="input_value_to_post   parent_multi "
                                                                                      data-field="parent_multi"
                                                                                      type="text"
                                                                                      data-autocomplete-id="1423-parent_multi"
                                                                                      value="1,2" name="parent_multi[]"
                                                                                      title="1,2" data-id="1423"
                                                                                      style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_multi2" data-id="1423"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_multi2" data-tablerow="1" data-edit-able="1"
                                         data-tablecol="16" title="">
                                        <span class="tree_select"> 1,2 </span> <input data-lpignore="true"
                                                                                      autocomplete="off"
                                                                                      placeholder="Parent multi2"
                                                                                      data-edit-able="1"
                                                                                      data-code-pos="ppp166549509"
                                                                                      class="input_value_to_post   parent_multi2 "
                                                                                      data-field="parent_multi2"
                                                                                      type="text"
                                                                                      data-autocomplete-id="1423-parent_multi2"
                                                                                      value="1,2" name="parent_multi2[]"
                                                                                      title="1,2" data-id="1423"
                                                                                      style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::image_list1" data-id="1423"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="image_list1" data-tablerow="1" data-edit-able="0"
                                         data-tablecol="17" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  image_list1 "
                                               data-field="image_list1" type="text"
                                               data-autocomplete-id="1423-image_list1" value="" name="image_list1[]"
                                               title="" data-id="1423" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::image_list2" data-id="1423"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="image_list2" data-tablerow="1" data-edit-able="0"
                                         data-tablecol="18" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  image_list2 "
                                               data-field="image_list2" type="text"
                                               data-autocomplete-id="1423-image_list2" value="" name="image_list2[]"
                                               title="" data-id="1423" style="; ">
                                    </div>
                                </div>
                                <div  class="divTable2Row" data-id="1422">
                                    <div class="divTable2Cell div_select_one_check">
                                        <input type="checkbox" class="select_one_check" data-id="1422">
                                    </div>
                                    <div class="divTable2Cell text-center">
                                        <a href="/admin/demo-api/edit/1422"><i title="Edit" class="fa fa-edit "
                                                                               style="font-size: 20px; margin: 2px;"></i></a>
                                        <i title="Save" style="font-size: 21px; margin: 2px; color: dodgerblue"
                                           class="fa fa-save save_one_item" data-id="1422"></i>
                                    </div>
                                    <div data-joinfunc="0" data-id="1422" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="id"
                                         data-tablerow="2" data-edit-able="0" data-tablecol="0" title="">
                                        <div class="id_data"> 1422</div>
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly=""
                                               class="input_value_to_post readonly  text-center  id " data-field="id"
                                               type="text" data-autocomplete-id="1422-id" value="1422" name="id[]"
                                               title="1422" data-id="1422" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1422" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="name"
                                         data-tablerow="2" data-edit-able="1" data-tablecol="1" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Name"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   name " data-field="name" type="text"
                                               data-autocomplete-id="1422-name" value="" name="name[]" title=""
                                               data-id="1422" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::tag_list_id" data-id="1422"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="1" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="tag_list_id" data-tablerow="2" data-edit-able="1"
                                         data-tablecol="2" title="">
                                        <div data-join-val="1422-tag_list_id" data-code-pos="ppp1665495430328"
                                             class="search-auto-complete-tbl ui-autocomplete-input" style=""
                                             autocomplete="off"></div>
                                        <input data-code-pos="ppp1667865466084" placeholder="Search Tag list" style=""
                                               data-autocomplete-id="1422-tag_list_id"
                                               class="search-auto-complete-tbl ui-autocomplete-input"
                                               data-api-search="/api/tags/search" data-opt-field=""
                                               data-api-search-field="name" type="text" value="" autocomplete="off">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Tag list"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   tag_list_id " data-field="tag_list_id"
                                               type="text" data-autocomplete-id="1422-tag_list_id" value=""
                                               name="tag_list_id[]" title="" data-id="1422" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1422" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="status"
                                         data-tablerow="2" data-edit-able="1" data-tablecol="3" title="">
                                        <div title="status" class="text-center"><i data-code-pos="ppp1681816931570"
                                                                                   data-id="1422" data-field="status"
                                                                                   class="fa fa-toggle-off change_status_item"></i>
                                        </div>
                                        <input data-lpignore="true" autocomplete="off" placeholder="Public"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   status " data-field="status" type="text"
                                               data-autocomplete-id="1422-status" value="" name="status[]" title=""
                                               data-id="1422" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::user_id" data-id="1422"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="user_id" data-tablerow="2" data-edit-able="1"
                                         data-tablecol="4" title="">
                                        <div data-join-val="1422-user_id" data-code-pos="ppp1665495430328"
                                             class="search-auto-complete-tbl ui-autocomplete-input" style=""
                                             autocomplete="off"><span data-code-pos="ppp1665496102584"
                                                                      data-autocomplete-id="1422-user_id"
                                                                      class="span_auto_complete" data-item-value="1"
                                                                      title="Remove this item">admin@abc.com [x]</span>
                                        </div>
                                        <input data-code-pos="ppp1667865466084" placeholder="Search User_id" style=""
                                               data-autocomplete-id="1422-user_id"
                                               class="search-auto-complete-tbl ui-autocomplete-input"
                                               data-api-search="/api/user/search" data-opt-field=""
                                               data-api-search-field="email" type="text" value="" autocomplete="off">
                                        <input data-lpignore="true" autocomplete="off" placeholder="User_id"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   user_id " data-field="user_id" type="text"
                                               data-autocomplete-id="1422-user_id" value="1" name="user_id[]" title="1"
                                               data-id="1422" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1422" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="textarea1"
                                         data-tablerow="2" data-edit-able="1" data-tablecol="5" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Textarea1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   textarea1 " data-field="textarea1"
                                               type="text" data-autocomplete-id="1422-textarea1" value=""
                                               name="textarea1[]" title="" data-id="1422" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::string2" data-id="1422"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="string2" data-tablerow="2" data-edit-able="1"
                                         data-tablecol="6" title="">
                                        <select data-code-pos="ppp1665411195425433" data-id="1422" data-joinfunc="1"
                                                class="sl_option " style="" data-field="string2">
                                            <option value="0" selected=""> ---</option>
                                            <option value="1"> Hà Nội</option>
                                            <option value="2"> HCM</option>
                                            <option value="3"> Huế</option>
                                            <option value="4"> Đà nẵng</option>
                                            <option value="5"> Phú Quốc</option>
                                        </select> <input data-lpignore="true" autocomplete="off" placeholder="String2"
                                                         data-edit-able="1" data-code-pos="ppp166549509"
                                                         class="input_value_to_post   string2 " data-field="string2"
                                                         type="text" data-autocomplete-id="1422-string2" value="0"
                                                         name="string2[]" title="0" data-id="1422"
                                                         style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1422" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="string1"
                                         data-tablerow="2" data-edit-able="1" data-tablecol="7" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="String1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   string1 " data-field="string1" type="text"
                                               data-autocomplete-id="1422-string1" value="1684916385" name="string1[]"
                                               title="1684916385" data-id="1422" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1422" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="number2"
                                         data-tablerow="2" data-edit-able="1" data-tablecol="8" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Number2"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   number2 " data-field="number2" type="text"
                                               data-autocomplete-id="1422-number2" value="" name="number2[]" title=""
                                               data-id="1422" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_id" data-id="1422"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_id" data-tablerow="2" data-edit-able="1"
                                         data-tablecol="9" title="">
                                        <span class="tree_select"> </span> <input data-lpignore="true"
                                                                                  autocomplete="off"
                                                                                  placeholder="Parent 1"
                                                                                  data-edit-able="1"
                                                                                  data-code-pos="ppp166549509"
                                                                                  class="input_value_to_post   parent_id "
                                                                                  data-field="parent_id" type="text"
                                                                                  data-autocomplete-id="1422-parent_id"
                                                                                  value="" name="parent_id[]" title=""
                                                                                  data-id="1422"
                                                                                  style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1422" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="number1"
                                         data-tablerow="2" data-edit-able="1" data-tablecol="10" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Number1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   number1 " data-field="number1" type="text"
                                               data-autocomplete-id="1422-number1" value="" name="number1[]" title=""
                                               data-id="1422" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1422" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="updated_at" data-tablerow="2" data-edit-able="0"
                                         data-tablecol="11" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  updated_at " data-field="updated_at"
                                               type="text" data-autocomplete-id="1422-updated_at"
                                               value="2023-05-30 22:55:09" name="updated_at[]"
                                               title="2023-05-30 22:55:09" data-id="1422" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1422" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="created_at" data-tablerow="2" data-edit-able="0"
                                         data-tablecol="12" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  created_at " data-field="created_at"
                                               type="text" data-autocomplete-id="1422-created_at"
                                               value="2023-05-24 15:19:46" name="created_at[]"
                                               title="2023-05-24 15:19:46" data-id="1422" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1422" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="textarea2"
                                         data-tablerow="2" data-edit-able="1" data-tablecol="13" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Textarea2"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   textarea2 " data-field="textarea2"
                                               type="text" data-autocomplete-id="1422-textarea2" value="1685287112.5391"
                                               name="textarea2[]" title="1685287112.5391" data-id="1422" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent2" data-id="1422"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent2" data-tablerow="2" data-edit-able="1"
                                         data-tablecol="14" title="">
                                        <span class="tree_select"> </span> <input data-lpignore="true"
                                                                                  autocomplete="off"
                                                                                  placeholder="Parent2"
                                                                                  data-edit-able="1"
                                                                                  data-code-pos="ppp166549509"
                                                                                  class="input_value_to_post   parent2 "
                                                                                  data-field="parent2" type="text"
                                                                                  data-autocomplete-id="1422-parent2"
                                                                                  value="" name="parent2[]" title=""
                                                                                  data-id="1422"
                                                                                  style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_multi" data-id="1422"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_multi" data-tablerow="2" data-edit-able="1"
                                         data-tablecol="15" title="">
                                        <span class="tree_select">  </span> <input data-lpignore="true"
                                                                                   autocomplete="off"
                                                                                   placeholder="Parent multi"
                                                                                   data-edit-able="1"
                                                                                   data-code-pos="ppp166549509"
                                                                                   class="input_value_to_post   parent_multi "
                                                                                   data-field="parent_multi" type="text"
                                                                                   data-autocomplete-id="1422-parent_multi"
                                                                                   value="" name="parent_multi[]"
                                                                                   title="" data-id="1422"
                                                                                   style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_multi2" data-id="1422"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_multi2" data-tablerow="2" data-edit-able="1"
                                         data-tablecol="16" title="">
                                        <span class="tree_select">  </span> <input data-lpignore="true"
                                                                                   autocomplete="off"
                                                                                   placeholder="Parent multi2"
                                                                                   data-edit-able="1"
                                                                                   data-code-pos="ppp166549509"
                                                                                   class="input_value_to_post   parent_multi2 "
                                                                                   data-field="parent_multi2"
                                                                                   type="text"
                                                                                   data-autocomplete-id="1422-parent_multi2"
                                                                                   value="" name="parent_multi2[]"
                                                                                   title="" data-id="1422"
                                                                                   style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::image_list1" data-id="1422"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="image_list1" data-tablerow="2" data-edit-able="0"
                                         data-tablecol="17" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  image_list1 "
                                               data-field="image_list1" type="text"
                                               data-autocomplete-id="1422-image_list1" value="" name="image_list1[]"
                                               title="" data-id="1422" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::image_list2" data-id="1422"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="image_list2" data-tablerow="2" data-edit-able="0"
                                         data-tablecol="18" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  image_list2 "
                                               data-field="image_list2" type="text"
                                               data-autocomplete-id="1422-image_list2" value="" name="image_list2[]"
                                               title="" data-id="1422" style="; ">
                                    </div>
                                </div>
                                <div  class="divTable2Row" data-id="1421">
                                    <div class="divTable2Cell div_select_one_check">
                                        <input type="checkbox" class="select_one_check" data-id="1421">
                                    </div>
                                    <div class="divTable2Cell text-center">
                                        <a href="/admin/demo-api/edit/1421"><i title="Edit" class="fa fa-edit "
                                                                               style="font-size: 20px; margin: 2px;"></i></a>
                                        <i title="Save" style="font-size: 21px; margin: 2px; color: dodgerblue"
                                           class="fa fa-save save_one_item" data-id="1421"></i>
                                    </div>
                                    <div data-joinfunc="0" data-id="1421" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="id"
                                         data-tablerow="3" data-edit-able="0" data-tablecol="0" title="">
                                        <div class="id_data"> 1421</div>
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly=""
                                               class="input_value_to_post readonly  text-center  id " data-field="id"
                                               type="text" data-autocomplete-id="1421-id" value="1421" name="id[]"
                                               title="1421" data-id="1421" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1421" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="name"
                                         data-tablerow="3" data-edit-able="1" data-tablecol="1" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Name"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   name " data-field="name" type="text"
                                               data-autocomplete-id="1421-name" value="" name="name[]" title=""
                                               data-id="1421" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::tag_list_id" data-id="1421"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="1" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="tag_list_id" data-tablerow="3" data-edit-able="1"
                                         data-tablecol="2" title="">
                                        <div data-join-val="1421-tag_list_id" data-code-pos="ppp1665495430328"
                                             class="search-auto-complete-tbl ui-autocomplete-input" style=""
                                             autocomplete="off"></div>
                                        <input data-code-pos="ppp1667865466084" placeholder="Search Tag list" style=""
                                               data-autocomplete-id="1421-tag_list_id"
                                               class="search-auto-complete-tbl ui-autocomplete-input"
                                               data-api-search="/api/tags/search" data-opt-field=""
                                               data-api-search-field="name" type="text" value="" autocomplete="off">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Tag list"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   tag_list_id " data-field="tag_list_id"
                                               type="text" data-autocomplete-id="1421-tag_list_id" value=""
                                               name="tag_list_id[]" title="" data-id="1421" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1421" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="status"
                                         data-tablerow="3" data-edit-able="1" data-tablecol="3" title="">
                                        <div title="status" class="text-center"><i data-code-pos="ppp1681816931570"
                                                                                   data-id="1421" data-field="status"
                                                                                   class="fa fa-toggle-on change_status_item"></i>
                                        </div>
                                        <input data-lpignore="true" autocomplete="off" placeholder="Public"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   status " data-field="status" type="text"
                                               data-autocomplete-id="1421-status" value="1" name="status[]" title="1"
                                               data-id="1421" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::user_id" data-id="1421"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="user_id" data-tablerow="3" data-edit-able="1"
                                         data-tablecol="4" title="">
                                        <div data-join-val="1421-user_id" data-code-pos="ppp1665495430328"
                                             class="search-auto-complete-tbl ui-autocomplete-input" style=""
                                             autocomplete="off"></div>
                                        <input data-code-pos="ppp1667865466084" placeholder="Search User_id" style=""
                                               data-autocomplete-id="1421-user_id"
                                               class="search-auto-complete-tbl ui-autocomplete-input"
                                               data-api-search="/api/user/search" data-opt-field=""
                                               data-api-search-field="email" type="text" value="" autocomplete="off">
                                        <input data-lpignore="true" autocomplete="off" placeholder="User_id"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   user_id " data-field="user_id" type="text"
                                               data-autocomplete-id="1421-user_id" value="" name="user_id[]" title=""
                                               data-id="1421" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1421" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="textarea1"
                                         data-tablerow="3" data-edit-able="1" data-tablecol="5" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Textarea1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   textarea1 " data-field="textarea1"
                                               type="text" data-autocomplete-id="1421-textarea1" value="1"
                                               name="textarea1[]" title="1" data-id="1421" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::string2" data-id="1421"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="string2" data-tablerow="3" data-edit-able="1"
                                         data-tablecol="6" title="">
                                        <select data-code-pos="ppp1665411195425433" data-id="1421" data-joinfunc="1"
                                                class="sl_option " style="" data-field="string2">
                                            <option value="0" selected=""> ---</option>
                                            <option value="1"> Hà Nội</option>
                                            <option value="2"> HCM</option>
                                            <option value="3"> Huế</option>
                                            <option value="4"> Đà nẵng</option>
                                            <option value="5"> Phú Quốc</option>
                                        </select> <input data-lpignore="true" autocomplete="off" placeholder="String2"
                                                         data-edit-able="1" data-code-pos="ppp166549509"
                                                         class="input_value_to_post   string2 " data-field="string2"
                                                         type="text" data-autocomplete-id="1421-string2" value="0"
                                                         name="string2[]" title="0" data-id="1421"
                                                         style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1421" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="string1"
                                         data-tablerow="3" data-edit-able="1" data-tablecol="7" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="String1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   string1 " data-field="string1" type="text"
                                               data-autocomplete-id="1421-string1" value="" name="string1[]" title=""
                                               data-id="1421" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1421" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="number2"
                                         data-tablerow="3" data-edit-able="1" data-tablecol="8" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Number2"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   number2 " data-field="number2" type="text"
                                               data-autocomplete-id="1421-number2" value="" name="number2[]" title=""
                                               data-id="1421" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_id" data-id="1421"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_id" data-tablerow="3" data-edit-able="1"
                                         data-tablecol="9" title="">
                                        <span class="tree_select"> 1</span> <input data-lpignore="true"
                                                                                   autocomplete="off"
                                                                                   placeholder="Parent 1"
                                                                                   data-edit-able="1"
                                                                                   data-code-pos="ppp166549509"
                                                                                   class="input_value_to_post   parent_id "
                                                                                   data-field="parent_id" type="text"
                                                                                   data-autocomplete-id="1421-parent_id"
                                                                                   value="1" name="parent_id[]"
                                                                                   title="1" data-id="1421"
                                                                                   style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1421" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="number1"
                                         data-tablerow="3" data-edit-able="1" data-tablecol="10" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Number1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   number1 " data-field="number1" type="text"
                                               data-autocomplete-id="1421-number1" value="1" name="number1[]" title="1"
                                               data-id="1421" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1421" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="updated_at" data-tablerow="3" data-edit-able="0"
                                         data-tablecol="11" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  updated_at " data-field="updated_at"
                                               type="text" data-autocomplete-id="1421-updated_at"
                                               value="2023-05-30 22:58:03" name="updated_at[]"
                                               title="2023-05-30 22:58:03" data-id="1421" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1421" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="created_at" data-tablerow="3" data-edit-able="0"
                                         data-tablecol="12" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  created_at " data-field="created_at"
                                               type="text" data-autocomplete-id="1421-created_at"
                                               value="2023-05-24 15:19:40" name="created_at[]"
                                               title="2023-05-24 15:19:40" data-id="1421" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1421" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="textarea2"
                                         data-tablerow="3" data-edit-able="1" data-tablecol="13" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Textarea2"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   textarea2 " data-field="textarea2"
                                               type="text" data-autocomplete-id="1421-textarea2" value=""
                                               name="textarea2[]" title="" data-id="1421" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent2" data-id="1421"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent2" data-tablerow="3" data-edit-able="1"
                                         data-tablecol="14" title="">
                                        <span class="tree_select"> 1</span> <input data-lpignore="true"
                                                                                   autocomplete="off"
                                                                                   placeholder="Parent2"
                                                                                   data-edit-able="1"
                                                                                   data-code-pos="ppp166549509"
                                                                                   class="input_value_to_post   parent2 "
                                                                                   data-field="parent2" type="text"
                                                                                   data-autocomplete-id="1421-parent2"
                                                                                   value="1" name="parent2[]" title="1"
                                                                                   data-id="1421"
                                                                                   style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_multi" data-id="1421"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_multi" data-tablerow="3" data-edit-able="1"
                                         data-tablecol="15" title="">
                                        <span class="tree_select"> 1,2 </span> <input data-lpignore="true"
                                                                                      autocomplete="off"
                                                                                      placeholder="Parent multi"
                                                                                      data-edit-able="1"
                                                                                      data-code-pos="ppp166549509"
                                                                                      class="input_value_to_post   parent_multi "
                                                                                      data-field="parent_multi"
                                                                                      type="text"
                                                                                      data-autocomplete-id="1421-parent_multi"
                                                                                      value="1,2" name="parent_multi[]"
                                                                                      title="1,2" data-id="1421"
                                                                                      style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_multi2" data-id="1421"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_multi2" data-tablerow="3" data-edit-able="1"
                                         data-tablecol="16" title="">
                                        <span class="tree_select"> 1,2 </span> <input data-lpignore="true"
                                                                                      autocomplete="off"
                                                                                      placeholder="Parent multi2"
                                                                                      data-edit-able="1"
                                                                                      data-code-pos="ppp166549509"
                                                                                      class="input_value_to_post   parent_multi2 "
                                                                                      data-field="parent_multi2"
                                                                                      type="text"
                                                                                      data-autocomplete-id="1421-parent_multi2"
                                                                                      value="1,2" name="parent_multi2[]"
                                                                                      title="1,2" data-id="1421"
                                                                                      style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::image_list1" data-id="1421"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="image_list1" data-tablerow="3" data-edit-able="0"
                                         data-tablecol="17" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  image_list1 "
                                               data-field="image_list1" type="text"
                                               data-autocomplete-id="1421-image_list1" value="" name="image_list1[]"
                                               title="" data-id="1421" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::image_list2" data-id="1421"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="image_list2" data-tablerow="3" data-edit-able="0"
                                         data-tablecol="18" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  image_list2 "
                                               data-field="image_list2" type="text"
                                               data-autocomplete-id="1421-image_list2" value="" name="image_list2[]"
                                               title="" data-id="1421" style="; ">
                                    </div>
                                </div>
                                <div  class="divTable2Row" data-id="1420">
                                    <div class="divTable2Cell div_select_one_check">
                                        <input type="checkbox" class="select_one_check" data-id="1420">
                                    </div>
                                    <div class="divTable2Cell text-center">
                                        <a href="/admin/demo-api/edit/1420"><i title="Edit" class="fa fa-edit "
                                                                               style="font-size: 20px; margin: 2px;"></i></a>
                                        <i title="Save" style="font-size: 21px; margin: 2px; color: dodgerblue"
                                           class="fa fa-save save_one_item" data-id="1420"></i>
                                    </div>
                                    <div data-joinfunc="0" data-id="1420" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="id"
                                         data-tablerow="4" data-edit-able="0" data-tablecol="0" title="">
                                        <div class="id_data"> 1420</div>
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly=""
                                               class="input_value_to_post readonly  text-center  id " data-field="id"
                                               type="text" data-autocomplete-id="1420-id" value="1420" name="id[]"
                                               title="1420" data-id="1420" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1420" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="name"
                                         data-tablerow="4" data-edit-able="1" data-tablecol="1" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Name"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   name " data-field="name" type="text"
                                               data-autocomplete-id="1420-name" value="" name="name[]" title=""
                                               data-id="1420" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::tag_list_id" data-id="1420"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="1" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="tag_list_id" data-tablerow="4" data-edit-able="1"
                                         data-tablecol="2" title="">
                                        <div data-join-val="1420-tag_list_id" data-code-pos="ppp1665495430328"
                                             class="search-auto-complete-tbl ui-autocomplete-input" style=""
                                             autocomplete="off"></div>
                                        <input data-code-pos="ppp1667865466084" placeholder="Search Tag list" style=""
                                               data-autocomplete-id="1420-tag_list_id"
                                               class="search-auto-complete-tbl ui-autocomplete-input"
                                               data-api-search="/api/tags/search" data-opt-field=""
                                               data-api-search-field="name" type="text" value="" autocomplete="off">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Tag list"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   tag_list_id " data-field="tag_list_id"
                                               type="text" data-autocomplete-id="1420-tag_list_id" value=""
                                               name="tag_list_id[]" title="" data-id="1420" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1420" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="status"
                                         data-tablerow="4" data-edit-able="1" data-tablecol="3" title="">
                                        <div title="status" class="text-center"><i data-code-pos="ppp1681816931570"
                                                                                   data-id="1420" data-field="status"
                                                                                   class="fa fa-toggle-on change_status_item"></i>
                                        </div>
                                        <input data-lpignore="true" autocomplete="off" placeholder="Public"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   status " data-field="status" type="text"
                                               data-autocomplete-id="1420-status" value="1" name="status[]" title="1"
                                               data-id="1420" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::user_id" data-id="1420"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="user_id" data-tablerow="4" data-edit-able="1"
                                         data-tablecol="4" title="">
                                        <div data-join-val="1420-user_id" data-code-pos="ppp1665495430328"
                                             class="search-auto-complete-tbl ui-autocomplete-input" style=""
                                             autocomplete="off"></div>
                                        <input data-code-pos="ppp1667865466084" placeholder="Search User_id" style=""
                                               data-autocomplete-id="1420-user_id"
                                               class="search-auto-complete-tbl ui-autocomplete-input"
                                               data-api-search="/api/user/search" data-opt-field=""
                                               data-api-search-field="email" type="text" value="" autocomplete="off">
                                        <input data-lpignore="true" autocomplete="off" placeholder="User_id"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   user_id " data-field="user_id" type="text"
                                               data-autocomplete-id="1420-user_id" value="" name="user_id[]" title=""
                                               data-id="1420" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1420" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="textarea1"
                                         data-tablerow="4" data-edit-able="1" data-tablecol="5" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Textarea1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   textarea1 " data-field="textarea1"
                                               type="text" data-autocomplete-id="1420-textarea1" value="1"
                                               name="textarea1[]" title="1" data-id="1420" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::string2" data-id="1420"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="string2" data-tablerow="4" data-edit-able="1"
                                         data-tablecol="6" title="">
                                        <select data-code-pos="ppp1665411195425433" data-id="1420" data-joinfunc="1"
                                                class="sl_option " style="" data-field="string2">
                                            <option value="0" selected=""> ---</option>
                                            <option value="1"> Hà Nội</option>
                                            <option value="2"> HCM</option>
                                            <option value="3"> Huế</option>
                                            <option value="4"> Đà nẵng</option>
                                            <option value="5"> Phú Quốc</option>
                                        </select> <input data-lpignore="true" autocomplete="off" placeholder="String2"
                                                         data-edit-able="1" data-code-pos="ppp166549509"
                                                         class="input_value_to_post   string2 " data-field="string2"
                                                         type="text" data-autocomplete-id="1420-string2" value="0"
                                                         name="string2[]" title="0" data-id="1420"
                                                         style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1420" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="string1"
                                         data-tablerow="4" data-edit-able="1" data-tablecol="7" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="String1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   string1 " data-field="string1" type="text"
                                               data-autocomplete-id="1420-string1" value="" name="string1[]" title=""
                                               data-id="1420" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1420" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="number2"
                                         data-tablerow="4" data-edit-able="1" data-tablecol="8" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Number2"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   number2 " data-field="number2" type="text"
                                               data-autocomplete-id="1420-number2" value="" name="number2[]" title=""
                                               data-id="1420" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_id" data-id="1420"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_id" data-tablerow="4" data-edit-able="1"
                                         data-tablecol="9" title="">
                                        <span class="tree_select"> 1</span> <input data-lpignore="true"
                                                                                   autocomplete="off"
                                                                                   placeholder="Parent 1"
                                                                                   data-edit-able="1"
                                                                                   data-code-pos="ppp166549509"
                                                                                   class="input_value_to_post   parent_id "
                                                                                   data-field="parent_id" type="text"
                                                                                   data-autocomplete-id="1420-parent_id"
                                                                                   value="1" name="parent_id[]"
                                                                                   title="1" data-id="1420"
                                                                                   style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1420" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="number1"
                                         data-tablerow="4" data-edit-able="1" data-tablecol="10" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Number1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   number1 " data-field="number1" type="text"
                                               data-autocomplete-id="1420-number1" value="1" name="number1[]" title="1"
                                               data-id="1420" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1420" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="updated_at" data-tablerow="4" data-edit-able="0"
                                         data-tablecol="11" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  updated_at " data-field="updated_at"
                                               type="text" data-autocomplete-id="1420-updated_at"
                                               value="2023-05-30 22:55:16" name="updated_at[]"
                                               title="2023-05-30 22:55:16" data-id="1420" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1420" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="created_at" data-tablerow="4" data-edit-able="0"
                                         data-tablecol="12" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  created_at " data-field="created_at"
                                               type="text" data-autocomplete-id="1420-created_at"
                                               value="2023-05-24 15:19:27" name="created_at[]"
                                               title="2023-05-24 15:19:27" data-id="1420" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1420" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="textarea2"
                                         data-tablerow="4" data-edit-able="1" data-tablecol="13" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Textarea2"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   textarea2 " data-field="textarea2"
                                               type="text" data-autocomplete-id="1420-textarea2" value=""
                                               name="textarea2[]" title="" data-id="1420" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent2" data-id="1420"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent2" data-tablerow="4" data-edit-able="1"
                                         data-tablecol="14" title="">
                                        <span class="tree_select"> 1</span> <input data-lpignore="true"
                                                                                   autocomplete="off"
                                                                                   placeholder="Parent2"
                                                                                   data-edit-able="1"
                                                                                   data-code-pos="ppp166549509"
                                                                                   class="input_value_to_post   parent2 "
                                                                                   data-field="parent2" type="text"
                                                                                   data-autocomplete-id="1420-parent2"
                                                                                   value="1" name="parent2[]" title="1"
                                                                                   data-id="1420"
                                                                                   style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_multi" data-id="1420"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_multi" data-tablerow="4" data-edit-able="1"
                                         data-tablecol="15" title="">
                                        <span class="tree_select"> 1,2 </span> <input data-lpignore="true"
                                                                                      autocomplete="off"
                                                                                      placeholder="Parent multi"
                                                                                      data-edit-able="1"
                                                                                      data-code-pos="ppp166549509"
                                                                                      class="input_value_to_post   parent_multi "
                                                                                      data-field="parent_multi"
                                                                                      type="text"
                                                                                      data-autocomplete-id="1420-parent_multi"
                                                                                      value="1,2" name="parent_multi[]"
                                                                                      title="1,2" data-id="1420"
                                                                                      style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_multi2" data-id="1420"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_multi2" data-tablerow="4" data-edit-able="1"
                                         data-tablecol="16" title="">
                                        <span class="tree_select"> 1,2 </span> <input data-lpignore="true"
                                                                                      autocomplete="off"
                                                                                      placeholder="Parent multi2"
                                                                                      data-edit-able="1"
                                                                                      data-code-pos="ppp166549509"
                                                                                      class="input_value_to_post   parent_multi2 "
                                                                                      data-field="parent_multi2"
                                                                                      type="text"
                                                                                      data-autocomplete-id="1420-parent_multi2"
                                                                                      value="1,2" name="parent_multi2[]"
                                                                                      title="1,2" data-id="1420"
                                                                                      style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::image_list1" data-id="1420"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="image_list1" data-tablerow="4" data-edit-able="0"
                                         data-tablecol="17" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  image_list1 "
                                               data-field="image_list1" type="text"
                                               data-autocomplete-id="1420-image_list1" value="" name="image_list1[]"
                                               title="" data-id="1420" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::image_list2" data-id="1420"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="image_list2" data-tablerow="4" data-edit-able="0"
                                         data-tablecol="18" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  image_list2 "
                                               data-field="image_list2" type="text"
                                               data-autocomplete-id="1420-image_list2" value="" name="image_list2[]"
                                               title="" data-id="1420" style="; ">
                                    </div>
                                </div>
                                <div  class="divTable2Row" data-id="1419">
                                    <div class="divTable2Cell div_select_one_check">
                                        <input type="checkbox" class="select_one_check" data-id="1419">
                                    </div>
                                    <div class="divTable2Cell text-center">
                                        <a href="/admin/demo-api/edit/1419"><i title="Edit" class="fa fa-edit "
                                                                               style="font-size: 20px; margin: 2px;"></i></a>
                                        <i title="Save" style="font-size: 21px; margin: 2px; color: dodgerblue"
                                           class="fa fa-save save_one_item" data-id="1419"></i>
                                    </div>
                                    <div data-joinfunc="0" data-id="1419" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="id"
                                         data-tablerow="5" data-edit-able="0" data-tablecol="0" title="">
                                        <div class="id_data"> 1419</div>
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly=""
                                               class="input_value_to_post readonly  text-center  id " data-field="id"
                                               type="text" data-autocomplete-id="1419-id" value="1419" name="id[]"
                                               title="1419" data-id="1419" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1419" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="name"
                                         data-tablerow="5" data-edit-able="1" data-tablecol="1" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Name"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   name " data-field="name" type="text"
                                               data-autocomplete-id="1419-name" value="" name="name[]" title=""
                                               data-id="1419" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::tag_list_id" data-id="1419"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="1" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="tag_list_id" data-tablerow="5" data-edit-able="1"
                                         data-tablecol="2" title="">
                                        <div data-join-val="1419-tag_list_id" data-code-pos="ppp1665495430328"
                                             class="search-auto-complete-tbl ui-autocomplete-input" style=""
                                             autocomplete="off"></div>
                                        <input data-code-pos="ppp1667865466084" placeholder="Search Tag list" style=""
                                               data-autocomplete-id="1419-tag_list_id"
                                               class="search-auto-complete-tbl ui-autocomplete-input"
                                               data-api-search="/api/tags/search" data-opt-field=""
                                               data-api-search-field="name" type="text" value="" autocomplete="off">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Tag list"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   tag_list_id " data-field="tag_list_id"
                                               type="text" data-autocomplete-id="1419-tag_list_id" value=""
                                               name="tag_list_id[]" title="" data-id="1419" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1419" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="status"
                                         data-tablerow="5" data-edit-able="1" data-tablecol="3" title="">
                                        <div title="status" class="text-center"><i data-code-pos="ppp1681816931570"
                                                                                   data-id="1419" data-field="status"
                                                                                   class="fa fa-toggle-off change_status_item"></i>
                                        </div>
                                        <input data-lpignore="true" autocomplete="off" placeholder="Public"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   status " data-field="status" type="text"
                                               data-autocomplete-id="1419-status" value="" name="status[]" title=""
                                               data-id="1419" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::user_id" data-id="1419"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="user_id" data-tablerow="5" data-edit-able="1"
                                         data-tablecol="4" title="">
                                        <div data-join-val="1419-user_id" data-code-pos="ppp1665495430328"
                                             class="search-auto-complete-tbl ui-autocomplete-input" style=""
                                             autocomplete="off"></div>
                                        <input data-code-pos="ppp1667865466084" placeholder="Search User_id" style=""
                                               data-autocomplete-id="1419-user_id"
                                               class="search-auto-complete-tbl ui-autocomplete-input"
                                               data-api-search="/api/user/search" data-opt-field=""
                                               data-api-search-field="email" type="text" value="" autocomplete="off">
                                        <input data-lpignore="true" autocomplete="off" placeholder="User_id"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   user_id " data-field="user_id" type="text"
                                               data-autocomplete-id="1419-user_id" value="" name="user_id[]" title=""
                                               data-id="1419" style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1419" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="textarea1"
                                         data-tablerow="5" data-edit-able="1" data-tablecol="5" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Textarea1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   textarea1 " data-field="textarea1"
                                               type="text" data-autocomplete-id="1419-textarea1" value=""
                                               name="textarea1[]" title="" data-id="1419" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::string2" data-id="1419"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="string2" data-tablerow="5" data-edit-able="1"
                                         data-tablecol="6" title="">
                                        <select data-code-pos="ppp1665411195425433" data-id="1419" data-joinfunc="1"
                                                class="sl_option " style="" data-field="string2">
                                            <option value="0" selected=""> ---</option>
                                            <option value="1"> Hà Nội</option>
                                            <option value="2"> HCM</option>
                                            <option value="3"> Huế</option>
                                            <option value="4"> Đà nẵng</option>
                                            <option value="5"> Phú Quốc</option>
                                        </select> <input data-lpignore="true" autocomplete="off" placeholder="String2"
                                                         data-edit-able="1" data-code-pos="ppp166549509"
                                                         class="input_value_to_post   string2 " data-field="string2"
                                                         type="text" data-autocomplete-id="1419-string2" value="0"
                                                         name="string2[]" title="0" data-id="1419"
                                                         style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1419" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="string1"
                                         data-tablerow="5" data-edit-able="1" data-tablecol="7" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="String1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   string1 " data-field="string1" type="text"
                                               data-autocomplete-id="1419-string1" value="1684905746" name="string1[]"
                                               title="1684905746" data-id="1419" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1419" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="number2"
                                         data-tablerow="5" data-edit-able="1" data-tablecol="8" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Number2"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   number2 " data-field="number2" type="text"
                                               data-autocomplete-id="1419-number2" value="" name="number2[]" title=""
                                               data-id="1419" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_id" data-id="1419"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_id" data-tablerow="5" data-edit-able="1"
                                         data-tablecol="9" title="">
                                        <span class="tree_select"> </span> <input data-lpignore="true"
                                                                                  autocomplete="off"
                                                                                  placeholder="Parent 1"
                                                                                  data-edit-able="1"
                                                                                  data-code-pos="ppp166549509"
                                                                                  class="input_value_to_post   parent_id "
                                                                                  data-field="parent_id" type="text"
                                                                                  data-autocomplete-id="1419-parent_id"
                                                                                  value="" name="parent_id[]" title=""
                                                                                  data-id="1419"
                                                                                  style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1419" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="number1"
                                         data-tablerow="5" data-edit-able="1" data-tablecol="10" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Number1"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   number1 " data-field="number1" type="text"
                                               data-autocomplete-id="1419-number1" value="" name="number1[]" title=""
                                               data-id="1419" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1419" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="updated_at" data-tablerow="5" data-edit-able="0"
                                         data-tablecol="11" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  updated_at " data-field="updated_at"
                                               type="text" data-autocomplete-id="1419-updated_at"
                                               value="2023-05-30 22:55:09" name="updated_at[]"
                                               title="2023-05-30 22:55:09" data-id="1419" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1419" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="created_at" data-tablerow="5" data-edit-able="0"
                                         data-tablecol="12" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  created_at " data-field="created_at"
                                               type="text" data-autocomplete-id="1419-created_at"
                                               value="2023-05-24 12:22:26" name="created_at[]"
                                               title="2023-05-24 12:22:26" data-id="1419" style="; ">
                                    </div>
                                    <div data-joinfunc="0" data-id="1419" data-selecting-keyboard=""
                                         data-code-pos="ppp1665495460297" data-multi-value="0"
                                         class="divTable2Cell divCellDataForTest  " data-table-field="textarea2"
                                         data-tablerow="5" data-edit-able="1" data-tablecol="13" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="Textarea2"
                                               data-edit-able="1" data-code-pos="ppp166549509"
                                               class="input_value_to_post   textarea2 " data-field="textarea2"
                                               type="text" data-autocomplete-id="1419-textarea2" value="1684915872.4353"
                                               name="textarea2[]" title="1684915872.4353" data-id="1419" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent2" data-id="1419"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent2" data-tablerow="5" data-edit-able="1"
                                         data-tablecol="14" title="">
                                        <span class="tree_select"> </span> <input data-lpignore="true"
                                                                                  autocomplete="off"
                                                                                  placeholder="Parent2"
                                                                                  data-edit-able="1"
                                                                                  data-code-pos="ppp166549509"
                                                                                  class="input_value_to_post   parent2 "
                                                                                  data-field="parent2" type="text"
                                                                                  data-autocomplete-id="1419-parent2"
                                                                                  value="" name="parent2[]" title=""
                                                                                  data-id="1419"
                                                                                  style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_multi" data-id="1419"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_multi" data-tablerow="5" data-edit-able="1"
                                         data-tablecol="15" title="">
                                        <span class="tree_select">  </span> <input data-lpignore="true"
                                                                                   autocomplete="off"
                                                                                   placeholder="Parent multi"
                                                                                   data-edit-able="1"
                                                                                   data-code-pos="ppp166549509"
                                                                                   class="input_value_to_post   parent_multi "
                                                                                   data-field="parent_multi" type="text"
                                                                                   data-autocomplete-id="1419-parent_multi"
                                                                                   value="" name="parent_multi[]"
                                                                                   title="" data-id="1419"
                                                                                   style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::parent_multi2" data-id="1419"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest  "
                                         data-table-field="parent_multi2" data-tablerow="5" data-edit-able="1"
                                         data-tablecol="16" title="">
                                        <span class="tree_select">  </span> <input data-lpignore="true"
                                                                                   autocomplete="off"
                                                                                   placeholder="Parent multi2"
                                                                                   data-edit-able="1"
                                                                                   data-code-pos="ppp166549509"
                                                                                   class="input_value_to_post   parent_multi2 "
                                                                                   data-field="parent_multi2"
                                                                                   type="text"
                                                                                   data-autocomplete-id="1419-parent_multi2"
                                                                                   value="" name="parent_multi2[]"
                                                                                   title="" data-id="1419"
                                                                                   style="; display: none; ; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::image_list1" data-id="1419"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="image_list1" data-tablerow="5" data-edit-able="0"
                                         data-tablecol="17" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  image_list1 "
                                               data-field="image_list1" type="text"
                                               data-autocomplete-id="1419-image_list1" value="" name="image_list1[]"
                                               title="" data-id="1419" style="; ">
                                    </div>
                                    <div data-joinfunc="App\Models\DemoTbl_Meta::image_list2" data-id="1419"
                                         data-selecting-keyboard="" data-code-pos="ppp1665495460297"
                                         data-multi-value="0" class="divTable2Cell divCellDataForTest   bgSnow "
                                         data-table-field="image_list2" data-tablerow="5" data-edit-able="0"
                                         data-tablecol="18" title="">
                                        <input data-lpignore="true" autocomplete="off" placeholder="" data-edit-able="0"
                                               data-code-pos="ppp166549509" readonly="" disabled=""
                                               class="input_value_to_post readonly  image_list2 "
                                               data-field="image_list2" type="text"
                                               data-autocomplete-id="1419-image_list2" value="" name="image_list2[]"
                                               title="" data-id="1419" style="; ">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>


            </div>
        </div>
    </div>
    <!-- /.content -->
    </div>
@endsection
