
@extends("layouts.adm")

@section("title")
 INDEX @endsection

@section("css")
    <link rel="stylesheet" href="{{asset("admins/product/index/list.css")}}">
@endsection
@section("js")
    <script src="{{asset("vendor/sweetAlert2/sweetalert2@11.js")}}"></script>
{{--    <script src="{{asset("admins/product/index/list.js")}}"></script>--}}
    <script src="{{asset("admins/main.js")}}"></script>
@endsection

@section('header')
    @include('parts.header-product')
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
                            <li class="breadcrumb-item" data-code-pos='ppp17256733521'><a href="#">INDEX</a></li>
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
                        <a href="{{route('admin.product.create')}}" class="btn btn-success float-right m-2"> ADD </a>
                    </div>
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col"> # </th>
                                <th scope="col"> Tên sp  </th>
                                <th scope="col"> Gia  </th>
                                <th scope="col"> Hinh anh  </th>
                                <th scope="col"> Danh muc  </th>
                                <th scope="col"> Action </th>
                            </tr>
                            </thead>
                            <tbody>
{{--                            <tr>--}}
{{--                                <th scope="row"> 1 </th>--}}
{{--                                <td> Iphone 4</td>--}}
{{--                                <td> Iphone 4</td>--}}
{{--                                <td> Iphone 4</td>--}}
{{--                                <td> Iphone 4</td>--}}
{{--                                <td>--}}
{{--                                    <a href="{{route("admin.product.edit", ['id'=>111])}}" class="btn btn-default"> Edit </a>--}}
{{--                                    <a href="{{route("admin.product.delete", ['id'=>111])}}" class="btn btn-danger"> Delete </a>--}}
{{--                                </td>--}}
{{--                            </tr>--}}

                                @foreach($data AS $item)

                                    <tr>
                                        <th scope="row"> {{$item->id}} </th>
                                        <td> {{$item->name}} </td>
                                        <td> {{($item->price)}} </td>
                                        <td>
                                            <img class="product_image_100_100" src="{{$item->feature_image_path}}" alt="">
                                        </td>
                                        <td> {{ $item->category->name ?? ''  }} </td>

                                        <td>
                                            <a href="{{route("admin.product.edit", ['id'=>$item->id])}}" class="btn btn-default"> Edit </a>
                                            <a data-url="{{route("admin.product.delete", ['id'=>$item->id])}}"
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
