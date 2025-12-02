@extends("layouts.adm")

@section("title")
INDEX
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
                            <li class="breadcrumb-item" ppp0857974958><a href="#">INDEX</a></li>
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
                        <a href="{{route('admin.menu.create')}}" class="btn btn-success float-right m-2"> ADD </a>
                    </div>
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col"> # </th>
                                <th scope="col"> Tên danh mục </th>
                                <th scope="col"> Action </th>
                            </tr>
                            </thead>
                            <tbody>

                                @foreach($menu AS $cat)

                                    <tr>
                                        <th scope="row"> {{$cat->id}} </th>
                                        <td> {{$cat->name}} </td>
                                        <td>
                                            <a href="{{route("admin.menu.edit", ['id'=>$cat->id])}}" class="btn btn-default"> Edit </a>
                                            <a href="{{route("admin.menu.delete", ['id'=>$cat->id])}}" class="btn btn-danger"> Delete </a>
                                        </td>

                                    </tr>

                                @endforeach
                            </tbody>

                        </table>



                    </div>

                    <div class="col-md-12">
                        {{ $menu->links() }}
                    </div>
                </div>


            </div>
        </div>
        <!-- /.content -->
    </div>
@endsection
