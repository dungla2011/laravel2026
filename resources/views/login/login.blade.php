@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
 {{ __('auth.login') }} @endsection

@section('header')
@endsection

@section('css')
    @include("login.css")
@endsection

@section('js')
@endsection

@section("content")


    <style>

        a {
            text-decoration: none!important;
        }

        .auth_cont {
            text-align: center;
        }

        .auth_zone {
            text-align: left;
            display: inline-block;
            margin: 30px 10px;
            max-width: 600px;
            /*height: 320px;*/
            border: 1px solid #ccc;
            background-color: snow;
            border-radius: 5px;

            padding: 20px 30px;
        }
        .alert-danger {
            color: #721c24;
            background-color: transparent!important;
            border-color: transparent!important;
            color: red;
            font-style: italic;
            /* font-size: small; */
        }

        .alert {
            position: relative;
            padding: 3px 3px!important;;
            margin-bottom: 1rem!important;;
            margin-top: 1px!important;
            /* border-radius: 0.25rem; */
        }
        .content-wrapper {
            min-height: 600px;
        }

        .navbar-dark .navbar-nav .nav-link {
            color: white!important;
        }
        .auth_cont input {
             -webkit-appearance: auto!important;
        }
    </style>

<div class="auth_cont" data-code-pos="ppp1682131894047">
    <div class="auth_zone">
        @if($errors->any())
            <div class="jumbotron p-2 align-middle text-center mt-2 mb-2">
                @foreach ($errors->all() as $error)
                    <li class="text-danger">{{ $error }}</li>
                @endforeach
            </div>
        @endif

        <div class="" class="justify-content-center align-items-center">
            <div class="auth-column" class="col-md-6">
                <div class="auth-box" class="col-md-12">
                    <form id="formGlx" class="auth-form" class="form" action="{{route("post.login")}}" method="post" data-recaptcha="true" data-recaptcha-action="login">
                        @csrf

                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
                        <div class="form-group text-center py-3">
                        <a href="{{ url('auth/google') }}" style=""
                           class="btn btn-warning">
                            <img style="width: 25px" src="/assert/Ionicons/src/social-googleplus.svg" alt="">
                            {{ __('auth.login_with') }} Gmail
                        </a>
                        </div>

                        <hr>

                        <h3 class="text-center ">{{ __('auth.login') }}</h3>
                        <br>
                        <div class="form-group">
                            <label for="email" class="">{{ __('auth.email_or_account') }}:</label><br>
                            <input data-lpignore='true' required type="text" name="email" value="{{old('email')}}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password" class="">{{ __('auth.password_field') }}:</label><br>
                            <input data-lpignore = 'true' required type="password" name="password" value="{{old('password')}}" class="form-control">
                        </div>
                        
                        @error('recaptcha')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        
                        <div class="form-group text-center">

                            <div class="my-2">
                                <input name="remember_me" id="remember-me" type="checkbox">
                                <label for="remember-me" class=""><span>{{ __('auth.remember_me') }}</span></label>
                            </div>

                            <input id="submit_form" type="submit" class="btn btn-primary btn-sm" value="{{ __('auth.login') }}">


                            <div class="py-3">
                                <a href="{{route("auth.register")}}" class="">{{ __('auth.register') }}</a>
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

@include('partials.recaptcha_v3', ['action' => 'login'])

@endsection
