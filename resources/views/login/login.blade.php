@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
 {{ __('auth.login') }} @endsection

@section('header')
@endsection

@section('css')
    @include("login.css")
@endsection

@section('js')
<script>
// Capture URL parameters and populate hidden fields
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Get action parameter from URL
    const action = urlParams.get('action');
    if (action) {
        document.getElementById('url_action').value = action;
        document.getElementById('redirect_action').value = action;
        console.log('Captured action parameter:', action);
    }
    
    // Also capture email if present in URL (but don't override the input field unless it's empty)
    const email = urlParams.get('email');
    if (email) {
        const emailInput = document.querySelector('input[name="email"]');
        if (emailInput && !emailInput.value) {
            emailInput.value = email;
        }
    }
    
    // Log all URL parameters for debugging
    console.log('URL Parameters:', Object.fromEntries(urlParams));
});

function showPassword() {
    const input = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-password');
    
    input.type = 'text';
    // Change to eye-slash icon
    eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
}

function hidePassword() {
    const input = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-password');
    
    input.type = 'password';
    // Change back to normal eye icon
    eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
}
</script>
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

                        <!-- Hidden inputs to capture URL parameters -->
                        <input type="hidden" name="action" id="url_action" value="">
                        <input type="hidden" name="redirect_action" id="redirect_action" value="">

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
                            <input data-lpignore='true' required type="text" name="email" value="{{ request('email') ? request('email') : old('email') }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password" class="">{{ __('auth.password_field') }}:</label><br>
                            <div style="position: relative;">
                                <input data-lpignore = 'true' required id="password" type="password" name="password" value="{{old('password')}}" class="form-control" style="padding-right: 40px;">
                                <span class="toggle-password" onmousedown="showPassword()" onmouseup="hidePassword()" onmouseleave="hidePassword()" ontouchstart="showPassword()" ontouchend="hidePassword()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; user-select: none;">
                                    <svg id="eye-password" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </span>
                            </div>
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
