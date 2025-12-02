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


@section("content")

    <style>
        .divTable2Cell span {
            font-size: small;
            padding: 2px 5px;
        }

        .divTable2Cell select.lunch {
            color: grey;
        }


        .divTable2Cell select {
            -webkit-appearance: none;
            -moz-appearance: none;
            text-indent: 1px;
            text-overflow: '';
            text-align: center;
        }
    </style>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <?php

        $today = 12;

        ?>
        <div class="content">
            <div class="container-fluid">


                <br><br>

                <div class="col-md-12">

                    <div class="divTable2 divContainer">
                        <div class="divTable2Body">
                            <div class="divTable2Row divTable2Heading1">
                                <div class="divTable2Cell text-center div_select_all_check">
                                    <input class="select_all_check select_one_check" type="checkbox"
                                           title="Select All">
                                </div>

                                <div class="divTable2Cell cellHeader">
                                    Họ Tên
                                </div>

                                <div class="divTable2Cell cellHeader">
                                    Option
                                </div>

                                <?php
                                for($i = 1; $i <= 31; $i++){
                                ?>
                                <div class="divTable2Cell cellHeader">
                                    <?php
                                    echo $i
                                    ?>
                                </div>
                                <?php
                                }
                                ?>

                            </div>

                            <?php
                            $mName = ["Lê Văn Anh", "Nguyễn Văn Bình", "Trần Lâm",
                                "Lê Nam", "Thái Sơn", "La Minh", "Trần Đức",
                                "Lê Quang", "La Nguyên", "Trần Huệ", "Nguyễn Hoa"];

                            for($x = 0; $x < 10; $x++){
                            ?>
                            <div class="divTable2Row">
                                <div class="divTable2Cell div_select_one_check">
                                    <input type="checkbox" class="select_one_check" data-id="1424">
                                </div>

                                <div class="divTable2Cell text-left" style="min-width: 100px">
                                    <span>
                                        <?php
                                        echo $mName[$x];
                                        ?>
                                    </span>
                                </div>


                                <div class="divTable2Cell divCellDataForTest">
                                    <select data-code-pos="ppp1665411195425433"
                                            class="">
                                        <option value="0" selected=""> ---</option>
                                        <option value="1"> X</option>
                                        <option value="6"> 2X</option>
                                        <option value="7"> 3X</option>
                                        <option value="2"> P</option>
                                        <option value="3"> V</option>
                                        <option value="4"> N</option>
                                        <option value="5"> K</option>
                                    </select>

                                </div>

                                <?php
                                for($i = 1; $i <= 31; $i++){
                                ?>
                                <div class="divTable2Cell divCellDataForTest">

                                    <?php
                                    if($i < $today){
                                    ?>

                                    <select class="work" data-code-pos="ppp1665411dd25433"
                                    >
                                        <option value="0"> -Ca-</option>
                                        <option value="1"> 1x</option>
                                        <option value="2"> 2x</option>
                                        <option value="2"> 3x</option>
                                    </select>
                                    <select class="work" data-code-pos="ppp1665411dd25433"
                                    >
                                        <option value="0"> ---</option>

                                        <option value="-1"> P</option>
                                        <option value="-2"> K</option>
                                        <option value="-3"> O</option>
                                        <option value="-4"> N</option>

                                        <?php
                                        for($i1 = 1; $i1 <= 24; $i1++){

                                        ?>
                                        <option
                                            value="<?php echo $i1 ?>" <?php if ($i1 == 12) echo 'selected' ?> > <?php echo $i1 . "h"; ?> </option>
                                        <?php
                                        }
                                        ?>

                                    </select>

                                    <select data-code-pos="ppp1665411dd25433"
                                            class="lunch">
                                        <option value="0"> -Ăn-</option>
                                        <option value="-1"> A1</option>
                                        <option value="-2"> A2</option>
                                        <option value="-3"> A1+2</option>
                                    </select>


                                    <?php
                                    }
                                    ?>

                                </div>
                                <?php
                                }
                                ?>
                            </div>

                            <?php
                            }
                            ?>


                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>
    <!-- /.content -->
    </div>


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


    <script>

        $(".divTable2Cell select").on("change", function () {
            if ($(this).val() == -1) {
                $(this).css("color", 'red');
                $(this).css("font-weight", 'bolder');
            }

            if ($(this).val() == -2) {
                $(this).css("color", 'blue');
                $(this).css("font-weight", 'bolder');
            }
        })

    </script>

@endsection
