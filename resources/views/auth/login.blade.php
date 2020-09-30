@extends('layouts.login')

@section('title', 'Login')

@section('login')
<div class="auth-card">
    <div class="logo">
        <img src="{{ asset('images/svg/login.png') }}">
    </div>
    <div class="logo-banner">
        <img src="{{ asset('images/logo/favicon.png') }}">
    </div>
    <div class="body-card">
        <h5>Login your account.</h5>
        <div class="divider"></div>
        
        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf

            <div class="form-group">
                <label>Username</label>
                <div class="input-group-single">
                    <span class="icon">
                        <i data-feather="user"></i>
                    </span>

                    <input id="username" name="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="off">
                </div>
                @error('username')
                <span class="invalid-feedback" role="alert">
                    {{ $message }}
                </span>
                @enderror
                
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-group-single">
                    <span class="icon">
                        <i data-feather="lock"></i>
                    </span>
                    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                </div>
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                        name="remember"
                        id="remember" {{ old('remember') ?
                        'checked' : '' }}>

                    <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>

                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-auth">{{ __('Login') }}</button>
            </div>

            <div class="form-group">
                @if (Route::has('password.request'))
                <div class="forgot">
                    <a href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                </div>
                @endif
            </div>
            
        </form>
    </div>
</div>
@endsection