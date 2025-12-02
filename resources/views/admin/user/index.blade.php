
@extends("layouts.adm")

@section("title")
 User INDEX @endsection

@section("css")
    <link rel="stylesheet" href="{{asset("admins/product/index/list.css")}}">
@endsection
@section("js")
    <script src="{{asset("vendor/sweetAlert2/sweetalert2@11.js")}}"></script>
{{--    <script src="{{asset("admins/product/index/list.js")}}"></script>--}}
    <script src="{{asset("admins/main.js")}}"></script>
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section("content")
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item" ppp0854590374958><a href="#"></a></li>
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

                <div class="row">

                    <div class="col-md-12">
                        <a href="{{route('admin.user.create')}}" class="btn btn-success float-right m-2"> ADD </a>
                    </div>
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col"> # </th>
                                <th scope="col"> Username   </th>
                                <th scope="col"> Email  </th>

                                <th scope="col"> Action </th>
                            </tr>
                            </thead>
                            <tbody>

                                @foreach($data AS $item)

                                    <tr>
                                        <th scope="row"> {{$item->id}} </th>
                                        <td> {{$item->username}} </td>
                                        <td> {{($item->email)}} </td>
                                        <td>
                                            <a href="{{route("admin.user.edit", ['id'=>$item->id])}}" class="btn btn-default"> Edit </a>
                                            <a data-url="{{route("admin.user.delete", ['id'=>$item->id])}}"
                                               class="btn btn-danger action_delete"> Delete </a>
                                        </td>

                                    </tr>

                                @endforeach
                            </tbody>

                        </table>



                    </div>

                    <div class="col-md-12">
                        {{ $data->links() }}
                    </div>
                </div>


            </div>
        </div>
        <!-- /.content -->
    </div>
@endsection
