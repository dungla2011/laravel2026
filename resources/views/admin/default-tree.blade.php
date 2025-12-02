{{--@if(\App\Components\Helper1::isMemberModule())--}}
@php
    $template = \App\Components\Helper1::isAdminModule(request()) ? "layouts.adm" : "layouts.member";
@endphp
@extends($template)


@section("title")
    Tree - Cây Danh mục
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <link rel="stylesheet" href="/admins/menu_tree.css?v=<?php echo filemtime(public_path().'/admins/menu_tree.css'); ?>">
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="/vendor/lad_tree/clsTreeJs-v1.css?v=<?php echo filemtime(public_path().'/vendor/lad_tree/clsTreeJs-v1.css'); ?>">
    <link rel="stylesheet"
          href="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.css">



@endsection

@section('js')

    <script src="/admins/table_mng.js"></script>

    <script src="/vendor/div_table2/div_table2.js?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.js');?>"></script>
    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>
    <script src="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.js"></script>

    <script src="/vendor/lad_tree/clsTreeJs-v2.js"></script>




    <script>


        <?php
            if($objMeta ?? '')
            if($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb);
        ?>


        $(function () {
            const treeFolder1 = new clsTreeJsV2();

            treeFolder1.bind_selector = "#tree_container"
            if (jctool.getUrlParam('open_all'))
                treeFolder1.opt_open_all_first = 1;

            // treeFolder1.data = data1
            // treeFolder1.checkbox1 = true;
            // treeFolder1.radio1 = true;

            treeFolder1.api_data = '/api/test123';
            treeFolder1.api_data = '<?php echo $objMeta->getApiUrl(\App\Components\Helper1::getModuleCurrentName(request())) ?>';

            treeFolder1.api_suffix_index = 'tree';
            treeFolder1.api_suffix_add = 'tree-create';
            treeFolder1.api_suffix_rename = 'tree-rename';
            treeFolder1.api_suffix_delete = 'tree-delete';
            treeFolder1.api_suffix_move = 'tree-move';
            treeFolder1.hide_root_node = 1;
            treeFolder1.order_by = 'orders';
            treeFolder1.root_id = jctool.getUrlParam('pid')
            if (!treeFolder1.root_id)
                treeFolder1.root_id = 0;

            treeFolder1.disable_toggle = 0;


            //if (jctool.getUrlParam('pid') && jctool.getUrlParam('gid'))

            treeFolder1.showTree();

            console.log(" DataGet01 = ", treeFolder1.data);
        })

    </script>


@endsection

@section("content")

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0" data-code-pos="qqq1709000521848">
                            <?php
                            $m1 = explode("\\", $objMeta::$modelClass);
                            echo end($m1) . ' Tree '
                            ?>

                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">
                                <a style="font-size: small; border: 1px solid #ccc; padding: 3px 5px; border-radius: 2px" data-code-pos="ppp16734351390967" href="<?php
                                echo dirname(\LadLib\Common\UrlHelper1::getUriWithoutParam());
                                ?>">
                                    <i class="fa fa-bars mx-1"></i>
                                    List
                                </a>

                            </li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">

                <div id="tree_container"></div>
            {{--                <br>--}}
            {{--                --}}
            {{--                <div id="tree_container2"></div>--}}
            <!-- Modal content -->
                <div class="modal_dialog_edit_node" style="text-align: center; display: none">
                    <span style="display: block" class="close_btn">&times;</span>
                    <span class="name_desc">Enter new name</span>
                    <br>
                    <br>
                    <input class="new_name" type="text" style="" value="">
                    <br>
                    <input type="submit" value="Enter" class="btn_create" style="">
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
    </div>
@endsection
