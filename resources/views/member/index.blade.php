<?php
use LadLib\Common\Database\MetaClassCommon;
?>
@extends("layouts.member")

@section("title")
    Member
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>
    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>

    <script>
        $("#btn-show-token").on('click', function (){
            $("#user_token").toggle();
        })
    </script>
@endsection

@section("content")
    <?php

        $user = \Illuminate\Support\Facades\Auth::user();

    ?>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid pt-3">
                <div class="sec1"
                    style="">
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
                    <?php

                        if(0){
                    //Lấy thông tin Deparement name của user
                    $depName = \App\Models\EventInfo::getDepartmentIdOfUser($user->id, 1)?->name ?? " <b> Chưa xác định </b> -
                     Bạn cần Liên hệ Admin để gán tài khoản vào một Phòng ban,
                     và có thể thao tác nội dung các Sự kiện của phòng ban.";
                    $depId = \App\Models\EventInfo::getDepartmentIdOfUser($user->id, 1)?->id ?? -10000;

//                echo "\n<b> <i class='fa fa-fw fa-check'></i> Bạn thuộc đơn vị: $depName </b>";

                    //Các thành viên có quyền quản trị, là các user trong bảng Department_User
                    $adminUsers = \App\Models\DepartmentUser::where('department_id', $depId)->get();
                    $adminUserIds = $adminUsers->pluck('user_id')->toArray();
                    //Lấy ra userObj  tư mảng naày
                    $adminUserObjs = \App\Models\User::whereIn('id', $adminUserIds)->get();
//                echo "<br/>\n <br/><i class='fa fa-fw fa-check'></i> Danh sách các thành viên có quyền Quản trị Sự kiện: ";
                    $cc = 0;

                    $userList = "<table class='table table-bordered mx-2 mt-2'>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>";

                    $cc = 0;
                    foreach ($adminUserObjs as $adminUserObj) {
                        $cc++;
                        $name = $adminUserObj->name ?? '';
                        $userList .= "<tr>
                    <td>$cc</td>
                    <td>$name</td>
                    <td>$adminUserObj->email</td>
                  </tr>";
                    }

                    if ($cc == 0) {
                        $userList .= "<tr><td colspan='3'>Không có</td></tr>";
                    }

                    $userList .= "</tbody></table>";
                        }
                    ?>

                </div>


            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
