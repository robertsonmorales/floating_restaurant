<div class="col-6 user-content">
    <h5>Password</h5>
    
    <form action="{{ route('account_settings.password_update') }}" method="POST" id="settings-form">
        @csrf
        <div class="input-group">
            <label for="">Current Password</label>
            <input type="password" name="old_password" class="form-control @error('old_password') is-invalid @enderror" id="current_password" required autofocus>

            @error('old_password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror

        </div>

        <div class="input-group">
            <label for="">New Password
                <a id="password-popover" role="button" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="More Information" data-content="Your new password must be more than 8 characters long, should contain at-least one Uppercase, one Lowercase, one Numeric and one special character." style="outline: none;">
                    <i data-feather="help-circle"></i>
                </a>
            </label>
            <input type="password" name="password" id="password" required autocomplete="off"
                class="form-control @error('password') is-invalid @enderror" autofocus>

            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="off"
                class="form-control @error('password_confirmation') is-invalid @enderror" autofocus>

            @error('password_confirmation')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror

        </div>

        @method('PUT')
        <input type="hidden" name="id" value="{{ Auth::user()->id }}">

        <div class="actions">                        
            <button type="submit" class="btn btn-primary btn-save" id="btn-save">Save Changes</button>
        </div>
    </form>
</div>