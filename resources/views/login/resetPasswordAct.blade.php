
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
    @if($errors->any())
        <div class="jumbotron p-3 mt-5 mb-5" style="max-width: 600px; margin: 0 auto">
            @foreach ($errors->all() as $error)
                <li class="text-danger">{{ $error }}</li>
            @endforeach
        </div>
    @endif

    <div class="auth_zone">

        <div class="" class="justify-content-center align-items-center">
            <div class="auth-column" class="col-md-6">
                <div class="auth-box" class="col-md-12">
                    <form class="auth-form" class="form" action="" method="post">
                        @csrf
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">

                        <h3 class="text-center ">Đặt lại mật khẩu</h3>
                        <br>

                        <div class="form-group">
                            <label for="password1" class="">Mật khẩu:</label><br>
                            <input required placeholder="Nhập mật khẩu"  type="password" name="password1" value=""
                                   class="form-control  @error('password1') is-invalid @enderror">
                            @error('password1')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="">Nhập lại Mật khẩu:</label><br>
                            <input required placeholder="Nhập lại mật khẩu"  type="password"
                                   class="form-control @error('password2') is-invalid @enderror"
                                   name="password2" required >
                            @error('password2')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group text-center mt-2">
                            <input type="submit" name="submit" class="btn btn-primary btn-sm" value="Đặt mật khẩu">
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
