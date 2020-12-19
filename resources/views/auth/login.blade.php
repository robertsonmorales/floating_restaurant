@extends('layouts.login')

@section('title', 'Login')

@section('login')
<div class="auth-container">
    <div class="auth-content">
        <div class="row auth-card mx-3">
            <div class="col-md d-none d-md-flex align-items-center justify-content-center overflow-hidden">
                <div class="p-4">
                    <img src="{{ asset('images/logo/favicon.png') }}" class="img-fluid" width="300">
                </div>
            </div>
            <div class="col-md body-card bg-light p-4">
                <h5 class="text-muted">Login your account.</h5>
                
                <form method="POST" action="{{ route('login') }}" id="login-form">
                    @csrf

                    <div class="form-group">
                        <label class="text-muted">Username</label>
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
                        <label class="text-muted">Password</label>
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

                    <div class="form-group form-columns">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                name="remember"
                                id="remember" {{ old('remember') ?
                                'checked' : '' }}>

                            <label class="text-muted" for="remember">{{ __('Remember Me') }}</label>

                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-auth" id="btn-auth">{{ __('Login') }}</button>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="forgot text-center">
                            <a href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        </div>
                    @endif
                    
                </form>
            </div>
        </div>      
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
    $('#login-form').on('submit', function(){
        $('#btn-auth').prop('disabled', true);

        $('#btn-auth').html('Logging in..');
        $(this).submit();
    });
});
</script>
@endsection