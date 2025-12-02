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


        .select_month {
            width: 120px;
            display: inline
        }
        @media print{
            @page {size: landscape}
            .main-footer{
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
        .tong_hop{
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
        <section class="content time_sheet" data-code-pos='ppp16950074288821'>
            <div class="container-fluid">
                <?php
                use App\Models\HrCommon;

                //$setTreeId = 11;
                $setUidMng = getCurrentUserId();
                //$setUidMng = 10411;
                if (isAdminACP_())
                    $mUserAdminTree = \App\Models\HrOrgTree_Meta::FGetArrayTreeWithAdminUid($setUidMng);
                else
                    $mUserAdminTree = \App\Models\HrOrgTree_Meta::FGetArrayUserManageTree();

                \App\Models\HrCommon::getListOrg($mUserAdminTree, $setUidMng);

                $mUid = [11332, 10409, 11333, 10451];
                $month = "2023-06";
                if(!$month = request('startMonth'))
                    $month = date("Y-m");

                $mUid = [];
                if(request("uid_list"))
                    $mUid = explode(",",request("uid_list"));

                //Nếu có set orgid, thì user id sẽ trong gid này:
                $catId = $orgId = 0;
                if($catId = $orgId = request("tree_id"))
                {
                    if(!$mUid){
                        $mUid = HrCommon::getUsersIdInOrgId($orgId);
                        //Lấy ra các UID của all timesheet tháng này:
                        $mNhanVienDaInsertVaoTimeSheet = array_column(App\Models\HrSampleTimeEvent::select('user_id')->distinct()
                            ->where('cat1', $orgId)
                            ->where("time_frame", ">=", "$month-01")
                            ->where("time_frame", "<=", "$month-31")
                            ->get()?->toArray(), 'user_id' );
                        $mUid = array_unique(array_merge($mUid, $mNhanVienDaInsertVaoTimeSheet ));
                    }
                }

//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r(clsConfigTimeFrame::$class_data);
//                echo "</pre>";
//                return;
                $metaObj = \App\Models\HrUserExpense::getMetaObj();
                $fieldMetaEx = $metaObj::getMapColFieldEx();

                if($mUid)
                foreach($mUid AS $x)
                {
                    \App\Models\HrCommon::getAllUserInfoInMonth($x, $month, $orgId);
                }

                if(!$mUid || !$month){
                    bl("Bạn chưa chọn thành viên để xuất bảng lương!");
                    echo "<br/>\n";
                }

                $mUid = array_filter($mUid);

                $m2 = \App\Models\HrUserExpense::whereIn("user_id", $mUid)->where("time_frame", $month)->get();
                $metaExpen = \App\Models\HrUserExpense::getMetaObj();
                $mmColExpense = [];
                $mmExpenseUid = [];
                foreach ($m2 AS $expen){
                    $expen = $expen->toArray();
                    foreach ($expen AS $key=>$val){
                        if(substr($key, 0,3) == 'num' && $val){
                            $mmExpenseUid[$expen['user_id']] = $expen;

                            $name = $metaExpen->getDescOfField($key);



                            if(isset($fieldMetaEx)){
                                foreach ($fieldMetaEx AS $fieldEx){
                                    $fieldEx = (object) $fieldEx;
                                    if($fieldEx->field == $key && $fieldEx->org_id == $catId){
                                        $name = $fieldEx->name;
                                        break;
                                    }
                                }
                            }


                            $mmColExpense[$key] = $name;
                        }
                    }
                }


//
//                return;


//                dump(\App\Models\HrSampleTimeEvent::$hrAllUserTimeSheet);
                ?>
                <DIV class="text-center mb-3">
                <H2>
                Tổng hợp lương tháng <?php  echo $month ?>
                </H2>
                    <h4 style="color: green">
                        <?php
                        if($orgId){
                            $linkAll = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('tree_id', null);

                            echo "Mục tiêu: ".\App\Models\HrOrgTree::find($orgId)->name . " ($orgId) ";
                            //echo " | <a href='$linkAll'> Tất cả mục tiêu</a> "

                            echo " <a style='font-size: small' target='_blank' href='/admin/hr-config-session-org-id-salary?seby_s11=$orgId' title='Cài đặt lương ca'> [1] </a>  ";
                            echo " <a style='font-size: small' target='_blank' href='/admin/hr-org-setting?soby_s2=asc' title='Cài đặt nhánh'> [2] </a>  ";
                            echo " <a style='font-size: small' target='_blank' href='/admin/hr-cham-cong2?tree_id=$orgId&startMonth=$month' title='Chấm công tháng'> [3] </a>  ";
                        }
                        else
                            echo "(Tất cả mục tiêu của từng thành viên)"
                        ?>
                    </h4>

                    <select class="form-control form-control-sm mr-3 hide_print select_month " style="" onchange="location = this.value;">
                        <option value=""> - Tháng - </option>
                        <?php
                        for($i = date("m") - 1; $i <= date("m") + 1; $i++){
                            $m = date("Y") .'-'. sprintf("%02d", $i);
                            $link =  \LadLib\Common\UrlHelper1::setUrlParamThisUrl("startMonth", $m);
                            $startTime = $month;
                            $padSl = '';
                            if($startTime && $startTime == $m)
                                $padSl = "selected";

                            echo "\n<option $padSl value='$link'> Tháng $m</option>";
                        }
                        ?>
                    </select>

                </DIV>

                <?php


                $mm = [
                    "STT "=> null,
                    "Thành viên" => 'uinfo',
//                    "Mã số" => null,
//                    "Tháng" => null,
                    "Lương cơ bản" => null,
                    "CÁC KHOẢN CỘNG" => [
                        "Lương ca" => ['Số ca' => null,'Đơn giá' => null, "Thành tiền" => null,],
                        "Tăng ca" => ['Số giờ' => null, 'Đơn giá' => null,"Thành tiền" => null,],
                        "Tăng ca lễ" => ['Số giờ' => null, 'Đơn giá' => null, "Thành tiền" => null,],
                        "Chuyên cần" => null,
                    ],
                    "CÁC KHOẢN TRỪ" => [
                        "Tiền cơm" => ['Số lượng' => null, 'Đơn giá' => null,  "Thành tiền" => null],
                        "Đi muộn" => null,
                    ],
                    "Tổng hợp" => "tong_hop"
                ];


                $mFieldCong = [];
                $mFieldTru = [];
                foreach($mmColExpense AS $k=>$name){
                    if($k[3]!='_'){
                        $mm["CÁC KHOẢN CỘNG"][$name] = null;
                        $mFieldCong[] = $k;
                    }
                    else{
                        $mm["CÁC KHOẢN TRỪ"][$name] = null;
                        $mFieldTru[] = $k;
                    }

                }
                $mm["CÁC KHOẢN CỘNG"]['Tổng cộng'] = null;
                $mm["CÁC KHOẢN TRỪ"]['Tổng Trừ'] = null;


                $luongAll =$totalCol = 0;



                ?>

                <table class='glx00 luong_sum' style="width: 100%">

                <?php
                    echo "\n<tr>";
                    $cc = 0;
                    foreach ($mm AS $name => $row0){

                        $cc++;
                        $span_row = $span_col = 0;
                        $cls0 = '';
                        if(is_array($row0)){
                            foreach ($row0 AS $name1=>$row1){
                                if(is_array($row1)){
                                    foreach ($row1 AS $name2=>$row2){
                                        $span_col++;
                                    }
                                }
                                else
                                    $span_col++;
                            }
                        }else{
                            $span_row = 3;
                            $cls0 = $row0;
                        }

                        if(!$span_row)
                            $span_row = 1;
                        if(!$span_col)
                            $span_col = 1;

                        $cls = '';

                        echo "\n<th data-code-pos='ppp16962231776721' class='$cls $cls0' colspan='$span_col' rowspan='$span_row'> $name  </th> ";

                        $totalCol+= $span_col;

                    }

                    echo "\n</tr>";

                    echo "\n\n<tr>";
                    foreach ($mm AS $name => $row0){
//    echo " $name | ";
                        if(is_array($row0)){

                            foreach ($row0 AS $name1=>$row1){
                                $span_row = $span_col = 0;
                                if(is_array($row1)){
                                    foreach ($row1 AS $name2=>$row2){
                                        $span_col++;
                                    }
                                }
                                else
                                    $span_row = 2;

                                if(!$span_row)
                                    $span_row = 1;
                                if(!$span_col)
                                    $span_col = 1;


                                echo "\n<th data-code-pos='ppp16962231941851' colspan='$span_col' rowspan='$span_row' > $name1   </th> ";
                            }

                        }
                    }
                    echo "\n</tr>";
                    echo "\n\n<tr>";
                    foreach ($mm AS $name => $row0){
//    echo " $name | ";
                        if(is_array($row0)){
                            foreach ($row0 AS $name1=>$row1){
                                if(is_array($row1)){
                                    foreach ($row1 AS $name2=>$row2){

                                        echo "\n<td colspan='1'> $name2  </td> ";
                                    }
                                }
                            }
                        }
                    }
                    echo "\n</tr>";



                    /////////////////////////////////////////////////
                    echo "\n <tr style='background-color: #ddd' data-code-pos='ppp16947804083411'> ";
                    for($i1 = 0; $i1 < $totalCol; $i1++){
                        $tmp = $i1;
                        if($i1 == 0) $tmp = '';

                        echo "<td  style='background-color: #ddd' class='text-center'>$tmp</td>";
                    }

                    echo "\n </tr>";

                    $cc = 0;
                    foreach ($mUid AS $uid1){
                        $cc++;

                        $luongObj = HrCommon::getTinhLuongUserMonthOrgId($uid1, $month , $catId);



                        echo "\n<tr data-code-pos='ppp1691544295323'>\n";

                        echo "\n <td> ";
                        echo $cc;
                        echo "</td>";

                        echo "\n <td class='uinfo uidx_$uid1 '> ";
                        echo HrCommon::userInfoCol($uid1);
                        echo "</td>";

//                        echo "\n <td> .. </td>";
//
//                        echo "\n <td> $month </td>";

                        $x1 = 1;
                        $sal = HrCommon::getSalaryBaseUser($uid1);
                        echo "\n <td data-code-pos='ppp16916456162421'> ";

                        foreach ($luongObj->mmPhanLoaiCa AS $caId=>$numberCa){
                            //if(isset(\App\Models\HrSampleTimeEvent_Meta::_num4_luong_thang($uid1, $month)[$caId]))
                                echo \App\Models\HrSampleTimeEvent_Meta::_num4_luong_thang($uid1, $month, $orgId)[$caId] ?? 0;
//                                echo "\n / $caId / $numberCa  / ";
                            echo "<br/>\n ";
                        }
                        if(count($luongObj->mmPhanLoaiCa) > 1)
                        echo "\n - ";


                        echo " </td>";

                        $x1++;
                        echo "\n <td data-code-pos='ppp1691544304376'>";


                        foreach ($luongObj->mmPhanLoaiCa AS $caId=>$numberCa){
                            //if(isset(HrCommon::getSessionTypeCacheArray()[$caId]))
                            {
                                $nameSS = HrCommon::getSessionTypeCacheArray()[$caId]->name ?? '';
                                echo "\n $numberCa($nameSS) <br/>";
                            }
                        }
                        if(count($luongObj->mmPhanLoaiCa) > 1)
                            echo $nCa = \App\Models\HrCommon::getTotalCaUserMonth($uid1, $month, $catId);

                        echo "\n </td>";


                        $x1++;
                        echo "\n <td data-code-pos='ppp16915507327931'>";
                        foreach ($luongObj->mmPhanLoaiCa AS $caId=>$numberCa){
                            //if(isset(\App\Models\HrSampleTimeEvent_Meta::_num4_luong_ca($uid1, $month, $orgId)[$caId]))
                                echo number_formatvn0(round(\App\Models\HrSampleTimeEvent_Meta::_num4_luong_ca($uid1, $month, $orgId)[$caId]) ?? '0')." <br> ";
                        }
                        if(count($luongObj->mmPhanLoaiCa) > 1)
                        echo "-";
                        echo "\n</td>";

                        $x1++;
                        echo "\n <td data-code-pos='ppp1691544300669'>";
                        $ttLuongCa = 0;
                        foreach ($luongObj->mmPhanLoaiCa AS $caId=>$numberCa){
                            $tmp1 =  $numberCa * (\App\Models\HrSampleTimeEvent_Meta::_num4_luong_ca($uid1, $month, $orgId)[$caId] ?? 0);
                            echo number_formatvn0(round($tmp1)) . "<br/>\n";
                            $ttLuongCa += $tmp1;
                        }
                        if(count($luongObj->mmPhanLoaiCa) > 1)
                            echo number_formatvn0(round($ttLuongCa));

                        echo "\n</td>";



                        $x1++;
                        echo "\n <td data-code-pos='ppp16915541445501'>";

                        echo $luongObj->tongGioCaLamThem;
                        echo "<br/>\n";
                        //if($luongObj->tongGioCaLamThemBuCaThuong)
                            echo $luongObj->tongGioCaLamThemBuCaThuong ."(bù) <br>";

                        //if($luongObj->tongGioCaLamThemTinhLuongLamThem)
                            echo $luongObj->tongGioCaLamThemTinhLuongLamThem;


                        echo "\n</td>";

                        $x1++;
                        echo "\n <td data-code-pos='ppp16915541469491'>";

                        $priceCaDuoi360 = HrCommon::getGiaGioTangCa($orgId,$uid1, $luongObj->tongGioCa, $month);

                        echo "\n- <br>";
                        $priceCaPlus = 0;
                        //if($luongObj->tongGioCaLamThemBuCaThuong)
                        echo round($priceCaDuoi360);
                        //if($luongObj->tongGioCaLamThemTinhLuongLamThem)
                        {
                            echo "<br/>\n";
                            echo $priceCaPlus = HrCommon::getGiaGioTangCa($orgId, $uid1, $luongObj->tongGioCa + $luongObj->tongGioLamThem, $month);
                        }

                        echo "\n</td data-code-pos='ppp16915541489651'>";
                        $x1++;
                        echo "\n <td data-code-pos='ppp16915541508961'>";

                        echo "- <br/>\n";
                        echo "" . number_formatvn0($priceCaDuoi360 * $luongObj->tongGioCaLamThemBuCaThuong);
                        echo "<br/>\n" ;
                        echo  number_formatvn0(round($luongObj->tongGioCaLamThemTinhLuongLamThem * $priceCaPlus));
                        echo "\n</td>";

                        $x1++;
                        echo "\n <td data-code-pos='ppp16915541416791'>";

                        echo $luongObj->tongGioLeLamThem;
                        echo "\n</td>";


                        $x1++;
                        echo "\n <td data-code-pos='ppp16915541551331'>";
                        echo $priceCaPlusLe = HrCommon::getGiaGioTangCaLe($orgId, $uid1, $luongObj->tongGioCa + $luongObj->tongGioLamThem, $month);
                        echo "\n</td>";

                        $x1++;
                        echo "\n <td data-code-pos='ppp16915579011'>";
                        echo  number_formatvn0($priceCaPlusLe * $luongObj->tongGioLeLamThem);
                        echo "\n</td data-code-pos='ppp169110401'>";


                        $x1++;
                        echo "\n <td title='Chuyen Can' data-code-pos='ppp16914455201_chuyen_can'>";
                        //echo  "chuyen can: ";
                        echo $luongObj->thuongChuyenCan;
                        //echo ->num10;
                        echo "\n</td>";

                        $x1++;
                        foreach($mFieldCong AS $fName){
                            echo "\n <td data-code-pos='ppp16915555579011xx'>";
                            if(isset($mmExpenseUid[$uid1]) && $mmExpenseUid[$uid1][$fName]){
                                $formatNum = number_formatvn0($mmExpenseUid[$uid1][$fName]);
                                echo $formatNum;
                            }

                            echo "\n</td>";
                        }


                        $x1++;
                        echo "\n <td data-code-pos='ppp16915541638201'>";
                        echo  number_formatvn0($luongObj->tongCong);
                        echo "\n</td>";


                        $x1++;
                        echo "\n <td data-code-pos='ppp1691544309074'> ";
                        echo $nMeal = HrCommon::getTotalMealUserMonth($uid1, $month);
                        echo "\n</td>";


                        $x1++;
                        echo "\n <td data-code-pos='ppp1691544335511''> ";

                        if($nMeal)
                            echo HrCommon::getPriceMeal($orgId, $uid1);


                        echo "\n</td>";

                        $x1++;
                        echo "\n <td>";
                        echo $luongObj->tongTienAn;
                        echo "\n</td>";


                        $x1++;
                        echo "\n <td data-code-pos='ppp16918335118551'>";


                        if($luongObj->tongTienDiMuonAll)
                        echo number_formatvn0($luongObj->tongTienDiMuon) . "($luongObj->tongGioDiMuon"."h)";
                        echo "<br/>\n";
                        echo number_formatvn0($luongObj->tongTienLeDiMuon) . "($luongObj->tongGioLeDiMuon"."h)";
                        echo "<br/>\n ". number_formatvn0($luongObj->tongTienDiMuonAll);
                        echo "\n</td>";



                        foreach($mFieldTru AS $fName){
                            echo "\n <td data-code-pos='ppp16915541511'>";
                            if(isset($mmExpenseUid[$uid1]) && $mmExpenseUid[$uid1][$fName]){
                                $formatNum = number_formatvn0($mmExpenseUid[$uid1][$fName]);
                                echo $formatNum;
//                                echo  $mmExpenseUid[$uid1][$fName];
                            }
                            echo "\n</td>";
                        }

                        $x1++;
                        echo "\n <td data-code-pos='ppp16915446315601'>";
                        echo number_formatvn0($luongObj->tongTru) ;
                        echo "\n</td>";
                        $x1++;
                        echo "\n <td data-code-pos='ppp16915446311>";
                        echo "";
                        echo "\n</td>";

                        $x1++;


                        $str1 = "";
                        foreach ($luongObj AS $k1=>$v1){
                            if(!is_array($v1))
                                $str1 .= "$k1 = $v1\n";
                        }

                        echo "\n <td data-code-pos='ppp1691544374420' title='$str1' class='luong_tong_hop'>";
                        $luongFinal = round($luongObj->tongLuongFinal);
                        $luongFinal1000 = $luongFinal;
                        echo  "<b>". number_formatvn0($luongFinal1000) . " đ </b> " ;

                        $luongAll += $luongFinal1000;

                        echo "<br/>\n <span style='font-size: x-small'>";


                        //echo \LadLib\Common\cstring2::toTienVietNamString3($luongFinal1000);

                        echo "\n</span>";


                        echo "<div style='' class='info_luong'>";
//                        echo "<pre>";
//                        print_r($luongObj);
//                        echo "</pre>";
                        echo "</div>";

                        //echo "</textarea>";
                        echo "\n</td>";

                        echo "</tr>";

                    }



?>

                    <tr data-code-pos='ppp16947804530501'>
                        <td  style="text-align: center" colspan="<?php echo $totalCol - 1 ?>">
                            <b>
                            Tổng lương
                            </b>
                        </td>
                        <td>
                            <b>
                            <?php
                            echo number_formatvn0($luongAll);
                            ?>
                            </b>
                        </td>
                    </tr>


                </table>

                    <br>


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
