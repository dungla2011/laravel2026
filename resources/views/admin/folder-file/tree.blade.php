@extends("layouts.adm")

@section("title")
    Menu Tree
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

    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>
    <script src="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.js"></script>

    <script src="/vendor/lad_tree/clsTreeJs-v2.js"></script>



    <script>

        $(function () {
            const treeFolder1 = new clsTreeJsV2();

            treeFolder1.bind_selector = "#tree_container"
            if (jctool.getUrlParam('open_all'))
                treeFolder1.opt_open_all_first = 1;

            // treeFolder1.data = data1
            // treeFolder1.checkbox1 = true;
            // treeFolder1.radio1 = true;
            treeFolder1.api_data = '/api/member-folder-file';
            treeFolder1.api_suffix_index = 'list';
            treeFolder1.api_suffix_add = 'create';
            treeFolder1.api_suffix_rename = 'rename';
            treeFolder1.api_suffix_delete = 'delete';
            treeFolder1.api_suffix_move = 'move';
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
                        <h1 class="m-0">Menu Tree</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item" data-code-pos='ppp17256351733521'><a href="#">Menu Tree</a></li>
                            <li class="breadcrumb-item active"></li>
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
