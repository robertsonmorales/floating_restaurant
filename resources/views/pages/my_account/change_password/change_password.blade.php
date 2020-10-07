<div class="user-content">
    <h5>Change Password</h5>
    
    <form action="{{ route('my_account.password_update') }}" method="POST" id="change-password-form">
        @csrf
        <div class="input-group">
            <label for="">Current Password</label>
            <input type="password" name="old_password" class="form-control @error('old_password') is-invalid @enderror" id="current_password" required autofocus>

            @error('old_password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror

            @if(session()->get('incorrect'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ session()->get('incorrect') }}</strong>
            </span>
            @endif

        </div>

        <div class="input-group">
            <label for="">New Password</label>
            <input type="password" name="password" id="password" required autocomplete="off"
                class="form-control @error('password') is-invalid @enderror" autofocus>
            <!-- <label for="" style="margin-top: 5px; color: #ff9800;">Your password must be more than 8 characters long, should contain at-least <b>one uppercase</b>, <b>one lowercase</b>, <b>one numeric</b> and <b>one special character</b>.</label> -->

            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror

            @if(session()->get('match'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ session()->get('match') }}</strong>
            </span>
            @endif
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
            <button type="submit" class="btn btn-primary btn-save">Save Password</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
    </form>
</div>