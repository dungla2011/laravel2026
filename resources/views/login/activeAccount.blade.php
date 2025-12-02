
{{--@extends("layouts_multi.gp2023")--}}
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
 {{ __('auth.activate_account') }} @endsection

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


                    <form class="auth-form" class="form" action="{{route("auth.activeAccount")}}" method="post" data-recaptcha="true" data-recaptcha-action="active_account">
                        @csrf
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">

                        <h3 class="text-center ">{{ __('auth.activate_account') }}</h3>
                        <br>
                        <div class="form-group">
                            <label for="email" class="">{{ __('auth.activation_message') }}:</label><br>
                            <input required type="email" placeholder="{{ __('auth.enter_email') }}" name="email" value="{{ old('email') }}" class="form-control  @error('email') is-invalid @enderror">
                            @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        @error('recaptcha')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        
                        <div class="form-group text-center mt-3">
                            <input type="submit" class="btn btn-primary btn-sm" value="{{ __('auth.activate_account') }}">
                        </div>

                        <div style="margin: 30px; text-align: center">
                            <a href="{{route("auth.register")}}" class="">{{ __('auth.register') }}</a>
                            |
                            <a href="{{route("login.login")}}" class="">{{ __('auth.login') }}</a>
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

@include('partials.recaptcha_v3', ['action' => 'active_account'])

@endsection

