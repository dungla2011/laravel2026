<?php
$orgId = 0;
$orgId = request("org_id");
?>
@php
    $template = \App\Components\Helper1::isAdminModule(request()) ? "layouts.adm" : "layouts.member";
@endphp

@extends($template)

@section("title")

    HR - Bảng lương tổng hợp - Report <?php
    ?>

@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet"
          href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path() . '/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet"
          href="/admins/table_mng.css?v=<?php echo filemtime(public_path() . '/admins/table_mng.css'); ?>">
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">
    <link rel="stylesheet"
          href="/vendor/lad_tree/clsTreeJs-v1.css?v=<?php echo filemtime(public_path() . '/vendor/lad_tree/clsTreeJs-v1.css'); ?>">
    <link rel="stylesheet"
          href="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.css">
    <link rel="stylesheet" href="/admins/img_list.css">
    <link rel="stylesheet" href="/assert/js/date-time-picker/jquery.datetimepicker.css">

    <style>


        .select_month {
            width: 120px;
            display: inline
        }

        @media print {
            @page {
                size: landscape
            }

            .main-footer {
                display: none;
            }

            .hide_print {
                display: none;
            }
        }

        .hide1 {
            display: none;
        }

        .div_user_id_info:hover > * {
            display: block;
        }

        .info_luong {
            display: none;

        }

        .tong_hop {
            min-width: 100px;
        }

        .luong_tong_hop:hover .info_luong {
            display: block;
        }

        .luong_sum tr:hover td {
            background-color: lavender;
        }


    </style>

@endsection

@section("content")
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">

            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content time_sheet" data-code-pos='ppp1695288821'>
            <div class="container-fluid">
                <?php
                use App\Models\HrCommon;

                if (!$month = request('startMonth'))
                    $month = date("Y-m");

                ?>
                <DIV class="text-center mb-3" data-code-pos='ppp16950107700341'>
                    <H2>
                        Tổng hợp lương tháng <?php  echo $month ?>
                    </H2>
                    <h4 style="color: green">
                        Tất cả mục tiêu
                    </h4>

                    <select class="form-control form-control-sm mr-3 hide_print select_month " style=""
                            onchange="location = this.value;">
                        <option value=""> - Tháng -</option>
                        <?php
                        for ($i = date("m") - 1; $i <= date("m") + 1; $i++) {
                            $m = date("Y") . '-' . sprintf("%02d", $i);
                            $link = \LadLib\Common\UrlHelper1::setUrlParamThisUrl("startMonth", $m);
                            $startTime = $month;
                            $padSl = '';
                            if ($startTime && $startTime == $m)
                                $padSl = "selected";

                            echo "\n<option $padSl value='$link'> Tháng $m</option>";
                        }
                        ?>
                    </select>

                </DIV>


                <table class="glx00 luong_sum" style="width: 100%" data-code-pos='ppp16950107666581'>
                    <?php
                    $allOrgId = \App\Models\HrCommon::getAllOrgIdHasEmployee();

                    $totalSalAllTree = 0;

                    foreach ($allOrgId AS $orgId => $org) {

                        echo "<tr data-code-pos='ppp16950107829551'> <td colspan='3' style='text-align: center; font-weight: bold; background-color: lavender' data-code-pos='ppp16950107634861'> Bộ phận: $org->name ($orgId) </td> </tr>";

//    echo "\n <hr> ORGID = $orgId";
                        $mUid = \App\Models\HrCommon::getUsersIdInOrgId($orgId);
                        //Lấy ra các UID của all timesheet tháng này:
                        $mNhanVienDaInsertVaoTimeSheet = array_column(App\Models\HrSampleTimeEvent::select('user_id')->distinct()
                            ->where('cat1', $orgId)
                            ->where("time_frame", ">=", "$month-01")
                            ->where("time_frame", "<=", "$month-31")
                            ->get()?->toArray(), 'user_id');
                        $mUid = array_unique(array_merge($mUid, $mNhanVienDaInsertVaoTimeSheet));

                        $totalSalTree = $cc = 0;
                        foreach ($mUid AS $uid) {
                            if(!$uid)
                                continue;
                            $cc++;
                            echo "<tr> ";
                            echo "\n <td style='text-align: center'> $cc </td>";
                            echo "\n <td>";
                            echo \App\Models\HrCommon::userInfoCol($uid);
                            echo "\n </td>";
                            $luong = \App\Models\HrCommon::getTinhLuongUserMonthOrgId($uid, $month, $orgId);
                            if ($luong->tongLuongFinal) {
                                //echo "<br/>\n ($orgId) $uid .  $luong->tongLuongFinal ";
                                $totalSalTree+=$luong->tongLuongFinal;
                            }
                            $tongLuongFinal = number_formatvn0($luong->tongLuongFinal);
                            echo "\n <td> $tongLuongFinal (VND) </td>";
                            echo "</tr>";

                        }

                        $totalSalTreeStr = number_formatvn0($totalSalTree);
                        $totalSalAllTree+=$totalSalTree;

                        echo "<tr data-code-pos='ppp16950107769841'> <td colspan='2' > <span style='float: right'> Tổng lương: </td> <td colspan='2'> <b> $totalSalTreeStr (VND)</b> </b> </td> </tr>";
                    }
                    ?>
                <?php
                    $totalSalAllTreeStr = number_formatvn0($totalSalAllTree);
//                    echo "\n  $totalSalAllTreeStr (VND) ";

                    echo "\n <tr data-code-pos='ppp16950107787981' style='background-color: lavender; font-size: larger'>
<td colspan='2'> <b style='float:right'> TỔNG LƯƠNG TẤT CẢ CÁC NHÁNH: </b> </td> ";
                    echo "\n <td colspan='1'> <b> $totalSalAllTreeStr (VND) </b></td>";
                    echo "\n </tr>  ";
                ?>
                </table>
            </div>
            <br>
        </section>
        <!-- /.content -->

    </div>


@endsection



@section('js')

    <script src="/admins/table_mng.js?v=<?php echo filemtime(public_path() . '/admins/table_mng.js');?>"></script>
    <script
        src="/vendor/div_table2/div_table2.js?v=<?php echo filemtime(public_path() . '/vendor/div_table2/div_table2.js');?>"></script>

    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>

    <script src="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.js"></script>
    <script src="/assert/js/date-time-picker/php-date-formatter.js"></script>
    <script src="/assert/js/date-time-picker/jquery.datetimepicker.js"></script>


@endsection
