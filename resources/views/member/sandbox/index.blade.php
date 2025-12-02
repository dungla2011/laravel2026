<?php

use LadLib\Common\Database\MetaClassCommon;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



?>
@extends("layouts.member")

@section("title")
    Member
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet"
          href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet"
          href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <link rel="stylesheet" href="/admins/admin_common.css?v=<?php echo filemtime(public_path().'/admins/admin_common.css'); ?>">


@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>


    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/admins/admin_logs.js"></script>

    <script>
        $("#btn-show-token").on('click', function () {
            $("#user_token").toggle();
        })
    </script>

    <script>


    </script>
@endsection

@section("content")
    <?php

    $user = \Illuminate\Support\Facades\Auth::user();



    ?>

    <div class="content-wrapper ">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content ">
            <div class="container-fluid pt-3">
                <div class="sec1">

                    <div class="row">
                        <div class="col-sm-4">

                            <i class="fa fa-fw fa-user"></i>
                            Mã Tài khoản:


                            <?php
                            $ms = \App\Components\ClassRandId2::getRandFromId(getUserIdCurrent_());
                            echo "<b> $ms </b>";
                            if (\App\Models\User::isSupperAdmin()) {
                                echo " <span style='color: transparent'> [" . getUserIdCurrent_() . '] </span>';
                            }


                            //Các role của user
                            echo "<br> <i class='fa fa-fw fa-check-square'></i> Quyền Tài khoản: <b> " . $user->getRoleNames() . "</b> ";
                            echo "<br> <i class='fa fa-fw fa-clock'></i> Ngày đăng ký: <b> " . $user->created_at . "</b> ";


                            ?>
                        </div>
                        <div class="col-sm-6">
                        <span>
                            <?php
                            echo "  <i class='fa fa-fw fa-inbox'></i> " . $user->email . " , " . $user->username;
                            if (!$user->password)
                                echo "<br/>\n <a href='/reset-password'>
                                <i class='fa fa-fw fa-unlock-alt'></i>
                                 Đặt mật khẩu
                                 </a>";
                            else
                                echo "<br/>\n <i class='fa fa-fw fa-lock'></i> <a href='/member/set-password'> Đặt mật khẩu </a>";
                            ?>
                        </span>
                        </div>
                        <div class="col-sm-2">
                            <div class="float-end">
                        <span id="user_token" style="display: none">
                            <input readonly
                                   style=""
                                   type="text" class="form-control form-control-sm" value="<?php
                            echo Auth()->user()->getJWTUserToken() ;
                            ?>">
                            <?php
                            ?>
                        </span>
                                <button id="btn-show-token" style="display: inline-block" type="button"
                                        class="btn btn-sm btn-default">
                                    <i class="fa fw fa-cog"></i>
                                    Get Api Token
                                </button>

                            </div>
                        </div>
                    </div>


                </div>

                <div class="sec1">
                    <div class="row">
                        <div class="col-sm-12 mb-1" style="font-size: 100%">

                            <?php

                            $uid = getCurrentUserId();

                            $u4s = new \App\Components\U4sHelper($uid);
//                            echo "<i class='fa fa-info-circle'></i>  Ngày hết hạn: ". $u4s->getVipExpiredDate();

//                            //Tìm tổng dung lượng cho phép tải, từ các gói:
//                            $mBillAndPro = \App\Models\OrderItem::where('user_id', $uid)->get();
//                            foreach ($mBillAndPro AS $bAndPro){
//                                $prod = \App\Models\Product::find($bAndPro->product_id);
//                                if($prod){
//                                    if($prod instanceof \App\Models\Product);
//                                    $hanDung = $prod->getQuotaDateText();
//                                    $nHour = $prod->getQuotaNHour();
//                                    $sizeDownload = $prod->getQuotaSizeDownloadAllow();
//                                    $countDownloadAllow = $prod->getQuotaCountDownloadAllow();
////                                    echo "<div> - Gói: $prod->name , $hanDung, $countDownloadAllow lượt / $sizeDownload GB </div>";
//                                }
//                            }
//
//                            $bw =  $u4s->getDownloadToday();
//                            $bwAll = $u4s->getDownloadAllDay();
//                            $countDownloadAll = $u4s->getCountDownloadAllDay();

//                            echo "<br/>\n<i class='fa fa-info-circle'></i> Dung lượng tải 24h qua: ". ByteSize($bw);
//                            echo "<br/>\n<i class='fa fa-info-circle'></i> Dung lượng tải cho phép trong 24h: " . $u4s->objUserCloud->getQuotaDailyDownload() . " GB ";
//                            echo "<br/>\n<i class='fa fa-info-circle'></i> Tổng dung lượng đã tải: ". ByteSize($bwAll);
//                            echo "<br/>\n<i class='fa fa-info-circle'></i> Tổng số lượt  đã tải: ". $countDownloadAll;

                            $mInfo = $u4s->getQuotaAllOfUser();

//                        if(isDebugIp())
                            {

                                //Lấy tất cả biến static của class U4sHelper
                                $static = $u4s::getClassStaticProperties();
//                            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                            print_r($static);
//                            echo "</pre>";

                                function getDesOfField($field, $static)
                                {
                                    foreach ($static AS $k => $v) {
                                        if ($k == "def_" . $field)
                                            return $v[1];
                                    }

                                }

                                function getDes2OfField($field, $static)
                                {
                                    foreach ($static AS $k => $v) {
                                        if ($k == "def_" . $field)
                                            return $v[2] ?? '';
                                    }

                                }
//                            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                            print_r($mInfo);
//                            echo "</pre>";

                                $mmShow = [
                                    \App\Components\U4sHelper::$def_totalUsedDownloadByte[0] => \App\Components\U4sHelper::$def_totalUsedDownloadByte[2] ?? '',
                                    \App\Components\U4sHelper::$def_totalFreeDownloadGB[0] => \App\Components\U4sHelper::$def_totalFreeDownloadGB[2] ?? '',

                                    \App\Components\U4sHelper::$def_totalAllowDownloadDailyGB[0] => \App\Components\U4sHelper::$def_totalAllowDownloadDailyGB[2] ?? '',

                                    \App\Components\U4sHelper::$def_totalUsedDownloadCount[0] => \App\Components\U4sHelper::$def_totalUsedDownloadCount[2] ?? '',
                                    \App\Components\U4sHelper::$def_totalFreeCountDownload[0] => \App\Components\U4sHelper::$def_totalFreeCountDownload[2] ?? '',

//                                \App\Components\U4sHelper::$def_expiredDateByNgold[0],
//                                \App\Components\U4sHelper::$def_expiredDate[0],
                                ];


                                $expire0 = $expire = $u4s->getVipExpiredDate();
                                if ($expire) {
                                    $expire0 = nowyh_vn_date_pre(strtotime($expire));
                                }

                                if ($expire < nowyh()) {
                                    $expire0 .= " <a href='/buy-vip' style='color: red!important; font-weight: bold!important; margin-left: 10px'> <i class='fa fa-hand'></i>
                                Đã hết hạn - Gia hạn VIP </a>";
                                } else {
                                    $nDay = round((strtotime($expire) - time()) / _NSECOND_DAY);
                                    $expire0 .= " <span  style='color: green!important; font-weight: bold!important; margin-left: 10px'> Còn $nDay ngày VIP  </span>";
                                }
                                ?>

                            {{--                            // Dua vao 1 table--}}
                            <b data-code-pos='ppp17385522506161'>Thông tin chi tiết:</b>

                                <?php
                                echo "<table class='mt-3 table table-bordered table-striped dataTable dtr-inline '>";
                                echo("<td style='width: 250px'> Ngày hết hạn VIP </td>");
                                echo("<td class='pr-2'> $expire0 </td>");
                                echo "\n</tr>";
                                foreach ($mmShow AS $k => $desc2) {
                                    $desc = getDesOfField($k, $static);
                                    $v = $mInfo[$k] ?? 0;
                                    if ($k == \App\Components\U4sHelper::$def_totalUsedDownloadByte[0])
                                        $v = ByteSize($v, 2);
                                    echo "\n<tr>";
                                    echo("<td>  $desc </td>");
                                    echo("<td> $v $desc2</td>");
                                    echo "\n</tr>";
                                }

//                            foreach ($mInfo AS $k => $v){
//                                if(!in_array($k, $mmShow))
//                                    continue;
//                                if($k == \App\Components\U4sHelper::$def_totalUsedDownloadByte[0])
//                                    $v = ByteSize($v,4);
////                                if($v)
////                                if($k == \App\Components\U4sHelper::$def_expiredDate[0])
////                                    $v = nowyh(time() + intval($v) * _NSECOND_DAY);
////                                if($v)
////                                    if($k == \App\Components\U4sHelper::$def_expiredDateByNgold[0])
////                                    $v = nowyh(time() + intval($v) * _NSECOND_DAY);
//
//                                    echo "\n<tr>";
//                                $desc  = getDesOfField($k, $static);
//                                $desc2 = getDes2OfField($k, $static);
//
////                                echo "<br/>\n-  $desc: $v";
//                                echo("<td> $desc </td>");
//                                echo("<td> $v $desc2</td>");
//                                echo "\n</tr>";
//                            }


                                echo "</table>";
//
//                            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                            print_r($mInfo);
//                            echo "</pre>";
                            }


//                            echo "<br/>\n- Dung lượng tải còn lại: ". ByteSize($u4s->getDownloadLeft());

                            ?>


                        </div>
                    </div>
                </div>






            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
