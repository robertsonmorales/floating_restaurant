@extends('layouts.login')

@section('title', 'Forgot Password')

@section('login')
<div class="auth-card">
    <div class="logo">
        <img src="{{ asset('images/svg/forgot.png') }}">
    </div>
    <div class="body-card">
        <h5>Reset Password</h5>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label>Email Address</label>

                <div class="input-group-single">
                    <span class="icon">
                        <i data-feather="mail"></i>
                    </span>

                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                </div>

                @error('email')
                <span class="invalid-feedback" role="alert">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="two-cols">
                    <a href="/login" class="btn-go-back">{{ __('Go Back') }}</a>

                    <button type="submit" class="btn btn-primary btn-auth">
                        {{ __('Send Password Reset Link') }}
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
