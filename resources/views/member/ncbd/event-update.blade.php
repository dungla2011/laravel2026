<?php

use LadLib\Common\Database\MetaClassCommon;
use App\Models\EventUserInfo;

//$eventUserInfo = EventUserInfo::where('email', getCurrentUserEmail())->first();

?>
{{--@extends("layouts.member")--}}
@extends("layouts_multi.ncbd")
@section("title")
    Cập nhật thông tin
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet"
          href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet"
          href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/choices.js/10.2.0/choices.min.css" />

    <?php

?>
@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>
    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/choices.js/10.2.0/choices.min.js"></script>

    <script>
        $("#btn-show-token").on('click', function () {
            $("#user_token").toggle();
        })

    </script>
@endsection

@section("content")

    <style>
        .member_zone i {
            color: #555555;
        }
        .img-responsive-glx {max-width: 1200px; height: auto; }
    </style>
    <?php

    $user = \Illuminate\Support\Facades\Auth::user();

    ?>

    <div class="content-wrapper member_zone">
         <!-- Main content -->
        <section class="content" >
            <div class="container pt-3" >

                <?php
                    if(!$eventUserInfo){
                        $email =  getCurrentUserEmail();
                                // Get EventUserInfo for current user
                        $eventUserInfo = \App\Models\EventUserInfo::where('email', $email)->first();
                    }
                    else{
                        $email = $eventUserInfo->email;
                    }
                ?>

                @include("member.ncbd.form-update-event-user-info")


            </div>
        </section>
        <!-- /.content -->


        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Modal title</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

    </div>



@endsection
