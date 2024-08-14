@extends('layouts.app')
@section('content')
<div class="container-fluid" style="height: 100%">
    <div class="row">

        @if(App\Models\Setting::first())
        <div class="col-md-6 px-0 align-items-center vh-100 d-none d-xl-block d-lg-block d-md-block" style="background:url({{ asset('images/'.App\Models\Setting::first()->login_background) }}) center center/cover no-repeat;">
            <div  id="bg"></div>
        </div>
        @else
        <div class="col-md-6 px-0 align-items-center vh-100 d-none d-xl-block d-lg-block d-md-block" style="background:url({{ asset('images/s1.jpg') }}) center center/cover no-repeat;">
            <div  id="bg"></div>
        </div>
        @endif
        
        <div class="col-md-6  text-center p-0 vh-100">
            <div id="particles"></div>
            <form method="POST" action="{{ route('login') }}" class="mx-auto container" style="margin-top:20vh;width:60%;">
                {{ csrf_field() }}
                <div class="form-group">
                    @if( App\Models\Setting::first())
                        <img src="{{ asset('images/'.  App\Models\Setting::first()->login_logo) }}" width="200" class="mb-2" style="position:relative" alt="Login Logo">
                    @endif
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fa fa-user"></i>
                        </span>
                    </div>
                    <input id="email" name="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" required autocomplete="email" autofocus placeholder="Phone" value="{{ old('email', null) }}">
                    @if($errors->has('email'))
                        <div class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                </div>
    
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                    </div>
    
                    <input id="password" name="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" required placeholder="{{ trans('global.login_password') }}">
    
                    @if($errors->has('password'))
                        <div class="invalid-feedback">
                            {{ $errors->first('password') }}
                        </div>
                    @endif
                </div>
    
                
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="input-group mb-4">
                            <div class="form-check checkbox">
                                <input class="form-check-input" name="remember" type="checkbox" id="remember" style="vertical-align: middle;" />
                                <label class="form-check-label text-white" for="remember" style="vertical-align: middle;">
                                    {{ trans('global.remember_me') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        @if(Route::has('password.request'))
                            <a class="btn btn-link mt-0 pt-0 text-white px-0" href="{{ route('password.request') }}">
                                {{ trans('global.forgot_password') }}
                            </a>
                        @endif
                    </div>
                </div>
    
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit"
                        @if(App\Models\Setting::first() && App\Models\Setting::first()->color != NULL)
                            style="background:{{ App\Models\Setting::first()->color }};border:{{ App\Models\Setting::first()->color }}"
                        @endif
                        class="btn btn-block mt-3 btn-warning text-white px-4">
                            {{ trans('global.login') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        $("form").on("submit", function () {
            $(this).find(":submit").prop("disabled", true);
        });
    </script>
@endsection