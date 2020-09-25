@extends('layouts.login')
@section('title', 'Password Reset')
@section('login')
<div class="auth-card" id="admin-login">
    <div class="logo">
        <img src="{{ asset('images/svg/undraw_nature_m5ll.svg') }}">
    </div>
    <div class="body-card">
        <h5>{{ __('Reset Password') }}</h5>

        <div class="divider"></div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group row">
                <label for="email">{{ __('E-Mail Address') }}</label>

                <div class="input-group-single">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="password">{{ __('Password') }}</label>

                <div class="input-group-single">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label for="password-confirm">{{ __('Confirm Password') }}</label>

                <div class="input-group-single">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <div class="form-group row mb-0">
                <button type="submit" class="btn btn-primary btn-auth">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
