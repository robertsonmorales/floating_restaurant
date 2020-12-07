@extends('layouts.login')

@section('title', 'Forgot Password')

@section('login')
<div class="auth-container">
    <div class="auth-content">
        <div class="row auth-card d-flex justify-content-between">
            <div class="col-md d-flex align-items-center justify-content-center overflow-hidden">
                <img src="{{ asset('images/logo/favicon.png') }}" class="img-fluid" width="300">
            </div>
            <div class="col-md body-card bg-light p-4">
                <h5 class="text-muted">Request Password Reset Link</h5>

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
                            <a href="/login" class="btn-go-back">{{ __('Back to login') }}</a>

                            <button type="submit" class="btn btn-primary btn-auth">Send Password Reset Link</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
