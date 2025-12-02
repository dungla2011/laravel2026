<?php
use LadLib\Common\Database\MetaClassCommon;

$code = $uid = getCurrentUserId();
$user = \App\Models\User::find($uid);
if($user->ide__)
    $code = $user->ide__;

$domain = \LadLib\Common\UrlHelper1::getDomainHostName();


?>
@extends("layouts.member")

@section("title")
 Member @endsection

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
        function copy_link(){
            var copyText = document.createElement("input");
            copyText.value = "https://<?php echo $domain?>/aff-link/<?php echo $code?>";
            document.body.appendChild(copyText);
            copyText.select();
            document.execCommand("copy");
            document.body.removeChild(copyText);
            showToastWarningTop("Đã copy link: " + copyText.value);
        }
    </script>
@endsection

@section("content")
    <?php

        $user = \Illuminate\Support\Facades\Auth::user();

    ?>

    <div class="content-wrapper">

        <!-- Main content -->
        <section class="content">
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

                <div class="row">
                    <div class="col-sm-12" style="">
                        <div class="sec1" style="text-align: center">
                        <a href="/my-tree">
                        <button style="" type="button" class="btn btn-primary btn-sm mx-3">Tạo & Xem Cây Phả Hệ</button>
                        </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12" style="">
                        <div class="sec1" style="text-align: center">
                            Mã Affiliate của bạn:
                            <br>
                <?php

                echo "<a href='https://$domain/aff-link/$code' target='_blank'> https://$domain/aff-link/$code </a>
                <br>
<button class='btn btn-sm btn-primary ml-1 mt-2' onclick='copy_link()' title='copy link affiliate' style=''> COPY LINK </button>
";

                ?>
                        </div>
                    </div>
                </div>

            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
