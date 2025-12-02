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
                            <li class="breadcrumb-item" data-code-pos='ppp172563510148551'><a href="#">INDEX</a></li>
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
                        <a href="{{route('admin.categories.create')}}" class="btn btn-success float-right m-2"> ADD </a>
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

                                @foreach($categories AS $cat)

                                    <tr>
                                        <th scope="row"> {{$cat->id}} </th>
                                        <td> {{$cat->name}} </td>
                                        <td>
                                            <a href="{{route("admin.categories.edit", ['id'=>$cat->id])}}" class="btn btn-default"> Edit </a>
                                            <a href="{{route("admin.categories.delete", ['id'=>$cat->id])}}" class="btn btn-danger"> Delete </a>
                                        </td>

                                    </tr>

                                @endforeach
                            </tbody>

                        </table>



                    </div>

                    <div class="col-md-12">
                        {{ $categories->links() }}
                    </div>
                </div>


            </div>
        </div>
        <!-- /.content -->
    </div>
@endsection
