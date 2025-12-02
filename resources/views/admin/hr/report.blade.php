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
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="/vendor/lad_tree/clsTreeJs-v1.css?v=<?php echo filemtime(public_path().'/vendor/lad_tree/clsTreeJs-v1.css'); ?>">
    <link rel="stylesheet"
          href="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.css">
    <link rel="stylesheet" href="/admins/img_list.css">
    <link rel="stylesheet" href="/assert/js/date-time-picker/jquery.datetimepicker.css">

    <style>
        @media print{
            @page {size: landscape}
            .main-footer{
                display: none;
            }
        }

        .hide1 {
            display: none;
        }
        .div_user_id_info:hover > * {
            display: block;
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
        <section class="content">
            <div class="container-fluid">


                <DIV class="text-center mb-3">
                <H2>
                TỔNG HỢP LƯƠNG THÁNG <?php
                    if($orgId)
                        echo request("month");
                    ?>
                </H2>
                    <h4 style="color: green">
                        <?php
                        if($orgId){
                            echo "Mục tiêu: ".\App\Models\HrOrgTree::find($orgId)->name . " ($orgId)";
                        }
                        else
                            echo "(Tất cả mục tiêu của từng thành viên)"
                        ?>
                    </h4>
                </DIV>


                <table class='glx00' style="width: 100%">
                <tr>
                    <th rowspan="3" style="text-transform: uppercase;; text-align: center"> <b>STT</b></th>
                    <th rowspan="3" style="text-transform: uppercase;; text-align: center; min-width: 150px"> <b>Thành viên</b></th>
                    <th rowspan="3" style="text-transform: uppercase;; text-align: center"> <b>Mã NS</b></th>
                    <th rowspan="3" style="text-transform: uppercase;; text-align: center"> <b>Tháng</b></th>
                    <th rowspan="3" style="text-transform: uppercase;; text-align: center"> <b>Lương Cơ bản</b></th>
                    <th style="text-transform: uppercase;font-size: large; font-weight: bold; text-align: center"  colspan="10"> Các Khoản Cộng</th>
                    <th style="text-transform: uppercase;font-size: large;font-weight: bold; text-align: center" colspan="9"> Các Khoản Trừ</th>
                    <td rowspan="3" style="background-color: lavender; text-align: center"> <b>Tổng hợp</b></td>
                </tr>
                <tr style="font-weight: bold;text-align: center" >
                <td colspan="3">Lương ca</td> <td colspan="3">Tăng ca</td> <td colspan="3">Ngày lễ</td> <td rowspan="2" style="background-color: lavender">Tổng cộng</td>
                <td colspan="3">Tiền cơm</td>
                    <td rowspan="2">Đồng Phục</td> <td rowspan="2">Tiền nước</td> <td rowspan="2">Tiền điện</td> <td rowspan="2">Tạm ứng</td>  <td rowspan="2">Chứng chỉ</td>
                    <td rowspan="2" style="background-color: lavender">Tổng giảm trừ</td>
                </tr>
                    <tr>
                        <td>Đơn giá/ca</td>
                        <td>Số ca</td>
                        <td>Thành tiền</td>
                        <td>Đơn giá/giờ</td>
                        <td>Số giờ</td>
                        <td>Thành tiền</td>
                        <td>Đơn giá</td>
                        <td>Số giờ</td>
                        <td>Thành tiền</td>
                        <td>Đơn giá</td>
                        <td>Số bữa</td>
                        <td>Thành tiền</td>


                    </tr>

                <?php

                use App\Models\HrCommon;

                $mUid = [11332, 10409, 11333, 10451];
                $month = "2023-06";
                $month = request('month');

                $mUid = explode(",",request("uid_list"));
                $mMonth = explode(",",request("month_list"));


                //Nếu có set orgid:
                $orgId = 0;
                if($orgId = request("org_id")){
                    $mMonth = [];
                    $mUid = HrCommon::getUsersIdInOrgId($orgId);

                    foreach($mUid AS $x){
                        $mMonth[] = request('month');
                    }

                }

                if(!$mUid || !$mMonth){
                    bl("Bạn chưa chọn thành viên để xuất bảng lương!");
                    echo "<br/>\n";
                }

                $priceMeal = 30000;

                $nDayInMn = \LadLib\Common\clsDateTime2::getEndDayOfMonth("$month-01");

                $ttU = count($mUid);
                if($ttU)
                for($i = 0; $i < $ttU ; $i++){
                    $uid = @$mUid[$i];
                    $month = @$mMonth[$i];
                    if(!$uid || !$month)
                        continue;

                    if(!\App\Models\User::find($uid))
                        continue;

                    $nHourPerSession = HrCommon::getNumberHourPerSessionInMonth($uid, $month);

                    $email = \App\Models\User::find($uid)->email;
//                    echo "<hr/>\n $email ";

                    $ttMeal = \App\Models\HrCommon::getTotalMealUserMonth($uid, $month, $orgId);

                    $ttCa = \App\Models\HrCommon::getTotalCaUserMonth($uid, $month, $orgId);
                    $ttHour = \App\Models\HrCommon::getTotalHourSessionUserMonth($uid, $month, $orgId);
                    $ttHourPlus = \App\Models\HrCommon::getTotalHourPlusUserMonth($uid, $month, $orgId);

//                    echo "<br/>\n Số bữa ăn = $ttMeal, Số Ca: $ttCa, Số giờ làm thêm: $ttHourPlus";

                    $salUser = HrCommon::getSalaryBaseUser($uid);


                    $salaOneCa= round($salUser / $nDayInMn);


                    $salaOneHour = round($salUser / $nDayInMn / $nHourPerSession);


                    $tienDongPhuc = $tienChungChi = $tienDien = $tienNuoc = $tienTamUng = 0;
                    if($salMore = \App\Models\HrExtraCostEmployee::where("user_id", $uid)->where("month", $month )->first()){
                        $tienDongPhuc = $salMore->sparam8;
                        $tienNuoc = $salMore->sparam9;
                        $tienDien = $salMore->sparam10;
                        $tienTamUng = $salMore->sparam11;
                        $tienChungChi = $salMore->sparam12;
                    }

                    $objLuong = HrCommon::getTinhLuongUserMonthOrgId($uid, $month, $orgId);

                    ?>

                    <tr>
                        <td><?php echo $i +1 ?></td>
                        <td data-code-pos='ppp16886359473601'> <?php

                            if($salMore){
                                $mt = new \App\Models\HrExtraCostEmployee_Meta();
                                echo $mt->_user_id($salMore, $uid);
                            }
                            else
                                echo $email

                            ?> </td>
                        <td><?php echo $uid ?></td>
                        <td style="white-space: pre-wrap;"> <?php echo explode("-",$month)[1] ."-". explode("-",$month)[0]  . "<br> <i> $nDayInMn ngày </i> "  ?> </td>
                        <td data-code-pos='ppp16887897433251'> <?php echo "$salUser <br>($nHourPerSession giờ/ca )"  ?> </td>
                        <td data-code-pos='ppp16887897459151'><?php echo $salaOneCa ?> </td>
                        <td data-code-pos='ppp16887914780671'><?php echo $ttCa . " <br> $ttHour"."h" ?> </td>
                        <td><?php echo $ttCa * $salaOneCa ?> </td>

                        <td data-code-pos='ppp16887897413441'><?php echo $salaOneHour ?> </td>
                        <td><?php echo $ttHourPlus ?> </td>

                        <td><?php echo $salaOneHour * $ttHourPlus ?> </td>

                        <td data-code-pos='ppp16887897498101'>Le </td>
                        <td>Le </td>
                        <td>Le </td>

                        <td> <b> <?php  echo $tongCong =  $ttCa * $salaOneCa + $salaOneHour * $ttHourPlus  ?></b></td>

                        <td data-code-pos='ppp16887897337771'><?php
                            echo $priceMeal;
                        ?></td>
                        <td data-code-pos='ppp16887897364291'><?php
                            echo $ttMeal;
                        ?></td>
                        <td data-code-pos='ppp16887897388051'><?php
                            echo $priceMeal * $ttMeal;
                        ?></td>

                        <td data-code-pos='ppp16887897211361'> <?php echo $tienDongPhuc ?> </td>
                        <td data-code-pos='ppp16887897256881'> <?php echo $tienNuoc ?> </td>
                        <td> <?php echo $tienDien ?> </td>
                        <td> <?php echo $tienTamUng ?> </td>
                        <td data-code-pos='ppp16887897290801'> <?php echo $tienChungChi ?> </td>
                        <td> <b><?php
                        echo $tongTru = $priceMeal * $ttMeal - $tienDongPhuc-$tienNuoc -$tienDien -$tienTamUng-$tienChungChi ;
                        ?>
                            </b>
                        </td>
                        <td style="font-weight: bold; color: brown"><?php
                            $totalAll = $tongCong - $tongTru;
                            echo $totalAllFm = number_formatvn0($totalAll);
                            echo "<pre>";
                            print_r($objLuong);
                            echo "</pre>";
                            ?>
                        </td>
                    </tr>

                    <?php



                }
                echo "</table>";
                ?>


            </div>
        </section>
        <!-- /.content -->

    </div>


@endsection



@section('js')

    <script src="/admins/table_mng.js?v=<?php echo filemtime(public_path().'/admins/table_mng.js');?>"></script>
    <script src="/vendor/div_table2/div_table2.js?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.js');?>"></script>

    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>

    <script src="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.js"></script>
    <script src="/assert/js/date-time-picker/php-date-formatter.js"></script>
    <script src="/assert/js/date-time-picker/jquery.datetimepicker.js"></script>


@endsection
