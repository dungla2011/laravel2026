
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

function showPassword(inputId) {
    // Show both password fields
    document.getElementById('password').type = 'text';
    document.getElementById('password2').type = 'text';
    
    // Change both icons to eye-slash
    const slashIcon = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    document.getElementById('eye-password').innerHTML = slashIcon;
    document.getElementById('eye-password2').innerHTML = slashIcon;
}

function hidePassword(inputId) {
    // Hide both password fields
    document.getElementById('password').type = 'password';
    document.getElementById('password2').type = 'password';
    
    // Change both icons back to normal eye
    const eyeIcon = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    document.getElementById('eye-password').innerHTML = eyeIcon;
    document.getElementById('eye-password2').innerHTML = eyeIcon;
}
</script>
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

        <div class="" class="justify-content-center align-items-center" data-code-pos='ppp17696883413701'>
            <div class="auth-column" class="col-md-6">
                <div class="auth-box" class="col-md-12">
                    <form class="auth-form" class="form" action="{{route("auth.register")}}" method="post" data-recaptcha="true" data-recaptcha-action="register" id="registerForm">
                        @csrf

                        <!-- Hidden inputs to capture URL parameters -->
                        <input type="hidden" name="action" id="url_action" value="">
                        <input type="hidden" name="redirect_action" id="redirect_action" value="">

                        <div class="form-group text-center py-3" data-code-pos='ppp17696883330501'>
                        <a href="{{ url('auth/google') }}" style=""
                           class="btn btn-warning">
                            <img style="width: 25px" src="/assert/Ionicons/src/social-googleplus.svg" alt="">
                            {{ __('auth.register_with') }} Gmail
                        </a>
                        </div>

                        <hr>
                        <h3 class="text-center ">{{ __('auth.register') }}</h3>
                        <br>
                        <div class="form-group" data-code-pos='ppp17696883384901'>
                            <label for="email" class="">{{ __('auth.email') }}:</label><br>
                            <input data-lpignore = 'true' required type="text" placeholder="{{ __('auth.enter_email') }}" name="email" value="{{ request('email') ? request('email')  :  old('email') }}" class="form-control  @error('email') is-invalid @enderror">
                            @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

{{--                        <div class="form-group">--}}
{{--                            <label for="username" class="">{{ __('auth.username') }}</label><br>--}}
{{--                            <input data-lpignore = 'true' required type="text" placeholder="{{ __('auth.account') }}"  name="username" value="{{ old('username') }}" class="form-control  @error('username') is-invalid @enderror">--}}
{{--                            @error('username')--}}
{{--                            <div class="alert alert-danger">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}

                        <div class="form-group">
                            <label for="password" class="">{{ __('auth.password_field') }}:</label><br>
                            <div style="position: relative;">
                                <input data-lpignore = 'true' required id="password"  placeholder="{{ __('auth.password_field') }}"  type="password" name="password" value="{{ old('password') }}" class="form-control  @error('password') is-invalid @enderror" style="padding-right: 40px;">
                                <span class="toggle-password" onmousedown="showPassword('password')" onmouseup="hidePassword('password')" onmouseleave="hidePassword('password')" ontouchstart="showPassword('password')" ontouchend="hidePassword('password')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; user-select: none;">
                                    <svg id="eye-password" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </span>
                            </div>
                            @error('password')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="">{{ __('auth.password_confirmation') }}:</label><br>
                            <div style="position: relative;">
                                <input data-lpignore = 'true' required id="password2" placeholder="{{ __('auth.password_confirmation') }}"  type="password" value="{{ old('password2') }}" class="form-control @error('password2') is-invalid @enderror" name="password2" style="padding-right: 40px;">
                                <span class="toggle-password" onmousedown="showPassword('password2')" onmouseup="hidePassword('password2')" onmouseleave="hidePassword('password2')" ontouchstart="showPassword('password2')" ontouchend="hidePassword('password2')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; user-select: none;">
                                    <svg id="eye-password2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </span>
                            </div>

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
