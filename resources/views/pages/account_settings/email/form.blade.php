<div class="col-6 user-content">
    <h5>Email</h5>

	<form action="{{ route('account_settings.email_update', Auth::user()->id) }}" method="post" id="settings-form">
	    @csrf

	    <div class="input-group">
	        <label for="">Your current email address is <strong class="text-info">{{ Crypt::decryptString($users->email) }}</strong></label>
	    </div>
	    <div class="input-group">
	        <label for="">New Email Address</label>
	        <input type="email" name="email" id="email" class="form-control" value=""
	        class="form-control @error('email') is-invalid @enderror" autocomplete="off" autofocus>
	        @error('email')
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