
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
 Đặt lại mật khẩu @endsection

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
            <div class="auth-column" class="col-md-6">
                <div class="auth-box" class="col-md-12">
                    <form class="auth-form" class="form" action="{{route("auth.resetPassword")}}" method="post">
                        @csrf
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">

                        <h3 class="text-center ">Đặt lại mật khẩu</h3>
                        <br>
                        <div class="form-group">
                            <label for="email" class="">Email nhận lại mật khẩu:</label><br>
                            <input required type="email"
                                   data-lpignore = 'true'
                                   placeholder="Nhập địa chỉ email " name="email" value="{{ old('email') }}" class="form-control  @error('email') is-invalid @enderror">
                            @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="form-group text-center mt-2">
                            <input type="submit" name="submit" class="btn btn-primary btn-sm" value="Đặt lại mật khẩu">
                        </div>

                        <div style="margin: 30px; text-align: center">
                            <a href="{{route("auth.register")}}" class="">Đăng ký</a>
                            |
                            <a href="{{route("login.login")}}" class="">Đăng nhập</a>
                            |
                            <a href="/" class="">Trang chủ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
