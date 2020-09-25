@extends('layouts.login')
@section('title', 'Password Confirm')
@section('login')
<div class="auth-card" id="admin-login">
    <div class="logo">
        <img src="{{ asset('images/svg/login.png') }}">
    </div>
    <div class="body-card">
        <h5>{{ __('Please confirm your password before continuing.') }}</h5>
        <div class="divider"></div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="form-group">
                <label>{{ __('Password')}}</label>

                <div class="input-group-single">
                    <span class="icon">
                        <i data-feather="lock"></i>
                    </span>
                    <input id="password" type="password"
                        class="form-control @error('password') is-invalid @enderror" name="password"
                        required autocomplete="current-password">
                </div>
                @error('password')
                <span class="invalid-feedback" role="alert">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <div class="form-group row">
                <button type="submit" class="btn btn-primary btn-auth">
                    {{ __('Confirm Password') }}
                </button>
            </div>

            <div class="form-group">                
                <div class="forgot">
                    @if (Route::has('password.request'))
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection