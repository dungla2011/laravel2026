
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
 {{ __('auth.reset_password') }} @endsection

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
                    <form class="auth-form" class="form" action="{{route("auth.resetPassword")}}" method="post" data-recaptcha="true" data-recaptcha-action="reset_password">
                        @csrf
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">

                        <h3 class="text-center ">{{ __('auth.reset_password') }}</h3>
                        <br>
                        <div class="form-group">
                            <label for="email" class="">{{ __('auth.email_reset_password') }}:</label><br>
                            <input required type="email"
                                   data-lpignore = 'true'
                                   placeholder="{{ __('auth.enter_email') }}" name="email" value="{{ old('email') }}" class="form-control  @error('email') is-invalid @enderror">
                            @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        @error('recaptcha')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <div class="form-group text-center mt-2">
                            <input type="submit" class="btn btn-primary btn-sm" value="{{ __('auth.reset_password') }}">
                        </div>

                        <div style="margin: 30px; text-align: center">
                            <a href="{{route("auth.register")}}" class="">{{ __('auth.register') }}</a>
                            |
                            <a href="{{route("login.login")}}" class="">{{ __('auth.login') }}</a>
                            |
                            <a href="/" class="">{{ __('auth.home') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@include('partials.recaptcha_v3', ['action' => 'reset_password'])

@endsection
