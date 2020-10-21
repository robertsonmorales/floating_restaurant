<!-- <div class="user-content" style="padding: 0 !important;">    
    <form action="{{ route('account_settings.update', Auth::user()->id) }}" method="post" id="settings-form" style="background: #36D1DC;  /* fallback for old browsers */
    background: -webkit-linear-gradient(to right, #5B86E5, #36D1DC);  /* Chrome 10-25, Safari 5.1-6 */
    background: linear-gradient(to right, #5B86E5, #36D1DC); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */">
        @csrf        
        <div class="py-4">
            <h3>hl</h3>
        </div> 
        @method('PUT')
        <input type="hidden" name="id" value="{{ Auth::user()->id }}">
    </form>
</div> -->

<div class="user-content">
    <h5>Basic Information</h5>

    <form action="{{ route('account_settings.update', Auth::user()->id) }}" method="post" id="settings-form">
        @csrf
        <div class="input-group">
            <label for="">First Name</label>
            <input type="text" name="first_name" id="first_name" class="form-control" required
            class="form-control @error('first_name') is-invalid @enderror"
            value="{{ Crypt::decryptString($users->first_name) }}" autocomplete="off" autofocus>

            @error('first_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="form-control" required value="{{ Crypt::decryptString($users->last_name) }}"
            class="form-control @error('last_name') is-invalid @enderror" autocomplete="off" autofocus>

            @error('last_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Username</label>
            <input type="text" name="username" id="username" required autocomplete="off"
                class="form-control @error('username') is-invalid @enderror" autofocus
                value="{{ $users->username }}" autocomplete="off">
                
            @error('username')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        
        <div class="input-group">
            <label for="">Contact Number</label>
            <input type="text" name="contact_number" id="contact_number" class="form-control" required value="{{ Crypt::decryptString($users->contact_number) }}"
            class="form-control @error('contact_number') is-invalid @enderror" autocomplete="off" autofocus>
            @error('contact_number')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        
        <div class="input-group">
            <label for="">Address</label>
            <input type="text" name="address" id="address" class="form-control" required value="{{ $users->address }}"
            class="form-control @error('address') is-invalid @enderror" autocomplete="off" autofocus>
            @error('address')
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