@extends("layouts.adm")
@section("title")
    REPORT EVENT
@endsection

<?php

$id = request('id');
$ev = \App\Models\EventInfo::find($id);
?>
@section('title_nav_bar')
    {{$ev->name ?? ''}}
@endsection
<?php
//                            echo "<div style='text-align: center'> $ev->name </div>";
// Chỉ đếm những record có user_event_id tồn tại trong event_user_infos (loại bỏ soft delete)
$nUser = \App\Models\EventAndUser::where('event_id', $id)
    ->join('event_user_infos', 'event_and_users.user_event_id', '=', 'event_user_infos.id')
    ->whereNull('event_user_infos.deleted_at')
    ->count();
$nCoMat = \App\Models\EventAndUser::where('event_id', $id)
    ->join('event_user_infos', 'event_and_users.user_event_id', '=', 'event_user_infos.id')
    ->whereNull('event_user_infos.deleted_at')
    ->whereNotNull('event_and_users.attend_at')
    ->count();


?>

@section('js')
    <script>
        setTimeout(function(){
            location.reload();
        }, 3000);

    </script>
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet"
          href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/upload_file.css?v=<?php echo filemtime(public_path().'/admins/upload_file.css'); ?>">

    <style>
        body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper, body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-footer, body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header
        {
            margin-left: 0px !important;
        }
        aside {
            display: none;
        }
        .content-header {
             padding: 0px;
        }
        nav.navbar{
            display: none!important;
        }
        ::-webkit-scrollbar {
            height: 4px;              /* height of horizontal scrollbar ← You're missing this */
            width: 4px;               /* width of vertical scrollbar */
            border: 1px solid gray;
        }
    </style>
@endsection

@section('js')
@endsection

@section("content")
    <div class="content-wrapper">
        <div class="content-header" data-code-pos='ppp17222454759951'>
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12">
                        <h1 class="m-2">

                        </h1>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>

        <!-- Main content -->
        <section class="content" data-code-pos='ppp17222453708171'>
            <div class="container-fluid">
                <h3 style="text-transform: uppercase; color: white;; background-color: royalblue; text-align: center;
                 padding: 18px 0px 15px 10px; border-bottom: 1px solid #ccc; margin-top: 20px; margin-bottom: 20px;
                 border-radius: 5px;
                 ">
                    <a href="/admin/event-info/edit/{{$ev->id}}" style="color: white">
                    <i class="fa fa-users" style="font-size: 90%"></i>
                        {{$ev->name ?? ''}}
                    </a>
                </h3>

                <div class="row">
                    <div class="col-lg-4 col-6">

                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>
                                    {{$nUser}}

                                </h3>
                                <p>Member</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-4 col-6">

                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{$nCoMat}}</h3>
                                <p>Checked-in</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>


                    <div class="col-lg-4 col-6">

                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>  {{$nUser - $nCoMat}}</h3>
                                <p>Haven’t checked-in </p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>


                </div>

                <div class="row" data-code-pos='ppp17222453531761'>
                    <div class="col-sm-12">
                        <table id="example2" class="table table-bordered table-hover dataTable dtr-inline"
                               aria-describedby="example2_info">
                            <thead>
                            <tr>
                                <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending">
                                    Thành viên
                                </th>
                                <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending">
                                    Thông tin
                                </th>
                                <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending">
                                    Thời gian
                                </th>
                                <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending">
                                    Ghi chú
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                \App\Models\EventAndUser::where('event_id', $id)->orderBy('attend_at', 'desc')->get()->map(function ($item) {
                                    $ev = \App\Models\EventInfo::find($item->event_id);
                                    $eu = \App\Models\EventUserInfo::find($item->user_event_id);
                                    if($ev){
                                        echo "<tr data-code-pos='ppp17222457116281' class='one_row'>";
                                        echo "<td>$eu->title $eu->last_name $eu->first_name</td>";
                                        if($item->attend_at)
                                            echo "<td>

                                            Có mặt

                                            </td>";
                                        else
                                            echo "<td></td>";
                                        echo "<td>$item->attend_at</td>";
                                        echo "<td></td>";
                                        echo "</tr>";
                                    }
                                });
                            ?>
                            <tr class="odd">
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
