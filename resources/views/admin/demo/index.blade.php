@extends("layouts.adm")

@section("title")
    INDEX Demo
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
                            <li class="breadcrumb-item" data-code-pos='ppp17256350859371'><a href="#">Demo</a></li>
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
                        <a href="{{route('admin.demo.create')}}" class="btn btn-success float-right m-2"> ADD </a>
                    </div>
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col"> id</th>
                                <th > Number1</th>
                                <th > Number2</th>
                                <th > String1</th>
                                <th > String2</th>
                                <th > Action </th>
                            </tr>
                            </thead>
                            <tbody>

                                @foreach($data AS $item)

                                    <tr>
                                        <td> {{$item->id}} </td>
                                        <td> {{$item->number1}} </td>
                                        <td> {{$item->number2}} </td>
                                        <td> {{$item->string1}} </td>
                                        <td> {{$item->string2}} </td>
                                        <td>
                                            <a href="{{route("admin.demo.edit", ['id'=>$item->id])}}" class="btn btn-default"> Edit </a>
                                            <a href="{{route("admin.demo.delete", ['id'=>$item->id])}}" class="btn btn-danger"> Delete </a>
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
