
{{--@extends("layouts_multi.gp2023")--}}
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
 Kích hoạt tài khoản @endsection

@section('header')
@endsection


@section('css')
    @include("login.css")
@endsection


@section('js')

@endsection

@section("content")

<div class="auth_cont">
    <div class="auth_zone">
        <div class="" class="justify-content-center align-items-center">

            @if($errors->any())
                <div class="jumbotron p-2 align-middle text-center mt-2 mb-2">
                    @foreach ($errors->all() as $error)
                        <li class="text-danger">{{ $error }}</li>
                    @endforeach
                </div>
            @endif

            <div class="auth-column" class="col-md-6">
                <div class="auth-box" class="col-md-12">


                    <form class="auth-form" class="form" action="{{route("auth.activeAccount")}}" method="post">
                        @csrf
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">

                        <h3 class="text-center ">Kích hoạt tài khoản</h3>
                        <br>
                        <div class="form-group">
                            <label for="email" class="">Nhập email đã đăng ký để kích hoạt tài khoản (nếu tài khoản đã đăng ký mà chưa nhận được mail kích hoạt):</label><br>
                            <input required type="email" placeholder="Nhập địa chỉ email" name="email" value="{{ old('email') }}" class="form-control  @error('email') is-invalid @enderror">
                            @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group text-center mt-3">
                            <input type="submit" name="submit" class="btn btn-primary btn-sm" value="Kích hoạt lại tài khoản">
                        </div>

                        <div style="margin: 30px; text-align: center">
                            <a href="{{route("auth.register")}}" class="">Đăng ký</a>
                            |
                            <a href="{{route("login.login")}}" class="">Đăng nhập</a>
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

