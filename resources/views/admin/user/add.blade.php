@extends("layouts.adm")

@section("title")
ADD
@endsection

@section("css")
    <link href="{{asset("vendor/select2/select2.min.css")}}" rel="stylesheet"/>
    <link href="{{asset("admins/product/add.css")}}" rel="stylesheet"/>
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
                        <h1 class="m-0">Add</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item" ppp08579374374958><a href="#">ADD</a></li>
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
        <form action="{{ route("admin.user.add") }}"
              method="post" enctype="multipart/form-data">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">

                            @csrf
                            <div class="form-group">
                                <label>Tên </label>
                                <input type="text"
                                       data-lpignore = 'true'
                                       class="form-control @error('name') is-invalid @enderror"
                                       name="username"
                                       placeholder="Nhập tên"
                                       value="{{ old('username') }}"
                                >
                                @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Email </label>
                                <input type="text"
                                       data-lpignore = 'true'
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email"
                                       placeholder="Nhập tên"
                                       value="{{ old('email') }}"
                                >
                                @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Password </label>
                                <input type="password"
                                       data-lpignore = 'true'
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password"
                                       placeholder="Nhập mật khẩu"
                                       value="{{ old('password') }}"
                                >
                                @error('password')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Token API </label>
                                <input type="text"
                                       class="form-control @error('token_user') is-invalid @enderror"
                                       name="token_user"
                                       placeholder="Nhập Token"
                                       value="{{ old('token_user') }}"
                                >
                                @error('token_user')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Vai trò </label>

                                <select  class="form-control select2_init" name="role_id[]" id="" multiple>
                                    <option value=""></option>
                                    @foreach($roles AS $role)
                                        <option value="{{$role->id}}"> {{$role->name}}</option>
                                    @endforeach
                                </select>

                                @error('role')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </form>
        <!-- /.content -->
    </div>
@endsection


@section("js")
    <script src="{{asset("vendor/select2/select2.min.js")}}"></script>
    <script src="{{asset("vendor/tinymce/tinymce48.min.js")}}"></script>

    <script>
        $(function (){
            $('.select2_init').select2({
                'placeholder' : 'Chọn vai trò'
            })
        })
    </script>

@endsection
