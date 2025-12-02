
{{--@extends("layouts_multi.gp2023")--}}
@extends(getLayoutNameMultiReturnDefaultIfNull())
@section("title")
 Đăng ký tài khoản @endsection

@section('header')
@endsection

@section('css')
    @include("login.css")
@endsection


@section('js')
@endsection

@section("content")

<div class="auth_cont">
    @if($errors->any())
{{--        <div class="jumbotron p-3 mt-5 mb-5" style="max-width: 600px; margin: 0 auto">--}}
{{--            @foreach ($errors->all() as $error)--}}
{{--                <li class="text-danger">{{ $error }}</li>--}}
{{--            @endforeach--}}
{{--        </div>--}}
    @endif

    <div class="auth_zone">

        <div class="" class="justify-content-center align-items-center">
            <div class="auth-column" class="col-md-6">
                <div class="auth-box" class="col-md-12">
                    <form class="auth-form" class="form" action="{{route("auth.register")}}" method="post">
                        @csrf
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">

                        <div class="form-group text-center py-3">
                        <a href="{{ url('auth/google') }}" style=""
                           class="btn btn-warning">
                            <img style="width: 25px" src="/assert/Ionicons/src/social-googleplus.svg" alt="">
                            Đăng ký nhanh qua Gmail
                        </a>
                        </div>

                        <hr>
                        <h3 class="text-center ">Đăng ký</h3>
                        <br>
                        <div class="form-group">
                            <label for="email" class="">Email:</label><br>
                            <input data-lpignore = 'true' required type="text" placeholder="Nhập địa chỉ email" name="email" value="{{ old('email') }}" class="form-control  @error('email') is-invalid @enderror">
                            @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="username" class="">Tên tài khoản (viết liền gồm chữ và số, dấu gạch dưới)</label><br>
                            <input data-lpignore = 'true' required type="text" placeholder="Nhập tên tài khoản"  name="username" value="{{ old('username') }}" class="form-control  @error('username') is-invalid @enderror">
                            @error('username')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password" class="">Mật khẩu:</label><br>
                            <input data-lpignore = 'true' required id="password"  placeholder="Nhập mật khẩu"  type="password" name="password" value="{{ old('password') }}" class="form-control  @error('password') is-invalid @enderror">
                            @error('password')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="">Nhập lại Mật khẩu:</label><br>
{{--                            <input type="password" name="password2" value="{{ old('password2') }}" class="form-control">--}}
                            <input data-lpignore = 'true' required id="password2" placeholder="Nhập lại mật khẩu"  type="password" value="{{ old('password2') }}" class="form-control @error('password2') is-invalid @enderror"
                                   name="password2">

                            @error('password2')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group text-center mt-2 " data-code-pos='ppp17370018434361'>

                            <input type="submit" name="submit" class="btn btn-primary btn-sm" value="Đăng ký">


                            <div class="py-3">
                                <a href="{{route("login.login")}}" class="">Đăng nhập</a>
                                |
                                <a href="{{route("auth.resetPassword")}}" class="">Quên mật khẩu</a>
                                |
                                <a href="{{route("auth.activeAccount")}}" class="">Kích hoạt tài khoản</a>
                                |
                                <a href="/" class="">Trang chủ</a>

                            </div>



                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
