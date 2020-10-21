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
            <label for="">New Password</label>
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

        <div class="input-group">
            <h6>Password Requirements:</h6>

            <ul class="list-group ml-4" style="font-size: .9em; color: #909090;">
              <li>Minimumm 8 characters long, the more the better</li>
              <li>Contain at-least 1 uppercase character</li>
              <li>Contain at-least 1 lowercase character</li>
              <li>And 1 numeric and 1 special character</li>
            </ul>
        </div>

        @method('PUT')
        <input type="hidden" name="id" value="{{ Auth::user()->id }}">

        <div class="actions">                        
            <button type="submit" class="btn btn-primary btn-save" id="btn-save">Save Changes</button>
        </div>
    </form>
</div>