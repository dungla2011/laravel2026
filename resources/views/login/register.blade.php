
{{--@extends("layouts_multi.gp2023")--}}
@extends(getLayoutNameMultiReturnDefaultIfNull())
@section("title")
 {{ __('auth.register') }} @endsection

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
                    <form class="auth-form" class="form" action="{{route("auth.register")}}" method="post" data-recaptcha="true" data-recaptcha-action="register">
                        @csrf

                        <div class="form-group text-center py-3">
                        <a href="{{ url('auth/google') }}" style=""
                           class="btn btn-warning">
                            <img style="width: 25px" src="/assert/Ionicons/src/social-googleplus.svg" alt="">
                            {{ __('auth.register_with') }} Gmail
                        </a>
                        </div>

                        <hr>
                        <h3 class="text-center ">{{ __('auth.register') }}</h3>
                        <br>
                        <div class="form-group">
                            <label for="email" class="">{{ __('auth.email') }}:</label><br>
                            <input data-lpignore = 'true' required type="text" placeholder="{{ __('auth.enter_email') }}" name="email" value="{{ old('email') }}" class="form-control  @error('email') is-invalid @enderror">
                            @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="username" class="">{{ __('auth.username') }}</label><br>
                            <input data-lpignore = 'true' required type="text" placeholder="{{ __('auth.account') }}"  name="username" value="{{ old('username') }}" class="form-control  @error('username') is-invalid @enderror">
                            @error('username')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password" class="">{{ __('auth.password_field') }}:</label><br>
                            <input data-lpignore = 'true' required id="password"  placeholder="{{ __('auth.password_field') }}"  type="password" name="password" value="{{ old('password') }}" class="form-control  @error('password') is-invalid @enderror">
                            @error('password')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="">{{ __('auth.password_confirmation') }}:</label><br>
{{--                            <input type="password" name="password2" value="{{ old('password2') }}" class="form-control">--}}
                            <input data-lpignore = 'true' required id="password2" placeholder="{{ __('auth.password_confirmation') }}"  type="password" value="{{ old('password2') }}" class="form-control @error('password2') is-invalid @enderror"
                                   name="password2">

                            @error('password2')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- reCAPTCHA hidden input -->
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
                        @error('recaptcha')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        
                        <div class="form-group text-center mt-2 " data-code-pos='ppp17370018434361'>

                            <input type="submit" class="btn btn-primary btn-sm" value="{{ __('auth.register') }}">


                            <div class="py-3">
                                <a href="{{route("login.login")}}" class="">{{ __('auth.login') }}</a>
                                |
                                <a href="{{route("auth.resetPassword")}}" class="">{{ __('auth.forgot_password') }}</a>
                                |
                                <a href="{{route("auth.activeAccount")}}" class="">{{ __('auth.activate_account') }}</a>
                                |
                                <a href="/" class="">{{ __('auth.home') }}</a>

                            </div>



                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@include('partials.recaptcha_v3', ['action' => 'register'])

@endsection
