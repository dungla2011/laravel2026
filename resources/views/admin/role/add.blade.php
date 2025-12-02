<style>
    .table td, .table th {
        padding: 5px 0px 0px 0px !important;
    }
</style>
@extends("layouts.adm")

@section("title")
 ADD Role @endsection

@section("css")
    <link href="{{asset("vendor/select2/select2.min.css")}}" rel="stylesheet"/>
    <link href="{{asset("admins/product/add.css")}}" rel="stylesheet"/>
@endsection

@section("js")
    <script src="{{asset("vendor/select2/select2.min.js")}}"></script>
    <script src="{{asset("vendor/tinymce/tinymce48.min.js")}}"></script>
    <script src="{{asset("admins/role/add.js")}}"></script>
@endsection
@section('header')
    @include('parts.header-all')
@endsection

@section("content")
    <div class="content-wrapper  p-3">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Add</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item" ppp0857937459958><a href="#">ADD</a></li>
                            <li class="breadcrumb-item active"></li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>


        <!-- Main content -->
        <form action="{{ route("admin.role.add") }}"
              method="post" enctype="multipart/form-data">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">

                            @csrf
                            <div class="form-group">
                                <label>Tên </label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       name="name"
                                       placeholder="Nhập tên"
                                       value="{{ old('name') }}"
                                >
                                @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Display name </label>
                                <input type="text"
                                       class="form-control @error('display_name') is-invalid @enderror"
                                       name="display_name"
                                       placeholder="Nhập display_name"
                                       value="{{ old('display_name') }}"
                                >
                                @error('display_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12 parent_all_check_per">


                            <label>

                                <input type="checkbox" class="checkall">
                                <b>Check All Role</b>
                            </label>


                            @foreach($permissionsParent AS $permission)
                                <div class="inside_a_parent">
                                    <b>

                                        <label

                                            title="{{$permission->route_name_code}}">
                                            <input
                                                name="route_name_code[]"
                                                value="{{$permission->route_name_code}}"
                                                title="{{$permission->route_name_code}}" type="checkbox"
                                                class="checkbox_wrapper">
                                            <b>
                                                Module {{  $permission->display_name  }}
                                            </b>
                                        </label>
                                    </b>
                                    <br>
                                    @foreach($permission->permissionChilds AS $child)
                                        <label
                                            title="{{$child->route_name_code}} / {{$child->route_name_code}}">
                                            <input

                                                class="checkbox_child_route" name="route_name_code[]"
                                                value="{{$child->route_name_code}}"
                                                title="{{$child->route_name_code}} / {{$child->route_name_code}}"
                                                type="checkbox">

                                            {{   $child->display_name ." . "  }}
                                        </label>
                                    @endforeach
                                </div>
                                <br>
                            @endforeach
                        </div>


                        <div class="col-md-12">
                            <br>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <br><br>
                        </div>

                    </div>
                </div>
            </section>
        </form>
        <!-- /.content -->
    </div>
@endsection


