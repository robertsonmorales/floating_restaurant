@extends('layouts.app')
@section('title', $title)

@section('content')
<center>
<div class="content" style="width: 45%;">
    <form action="{{ ($mode == 'update') ? 
        route('user_accounts.update', $user->id) : 
        route('user_accounts.store') }}"
        method="POST" class="card-form" id="card-form">
        @csrf

        <h5>{{ ucfirst($mode).' '.\Str::Singular($header) }}</h5>
        
        <div class="input-group">
            <label for="">First Name</label>
            <input type="text" name="first_name" id="first_name" autocomplete="off"
                class="form-control @error('first_name') is-invalid @enderror"
                value="{{($mode == 'update') ? Crypt::decryptString($user->first_name) : old('first_name')}}">

            <span class="messages">
                <strong id="error-firstname"></strong>
            </span>

            @error('first_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Last Name</label>
            <input type="text" name="last_name" id="last_name" autocomplete="off"
                class="form-control @error('last_name') is-invalid @enderror"
                value="{{($mode == 'update') ? Crypt::decryptString($user->last_name) : old('last_name')}}">

            <span class="messages">
                <strong id="error-lastname"></strong>
            </span>

            @error('last_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        @if($mode == 'create')
        <div class="input-group">
            <label for="">Email</label>
            <input type="email" name="email" id="email" autocomplete="off"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}">

            <span class="messages">
                <strong id="error-email"></strong>
            </span>

            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Username</label>
            <input type="text" name="username" id="username" autocomplete="off"
                class="form-control @error('username') is-invalid @enderror"
                value="{{ old('username') }}">

            <span class="messages">
                <strong id="error-username"></strong>
            </span>

            @error('username')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Password 
                <a tabindex="0" id="password-popover" role="button" data-toggle="popover" data-placement="top" data-trigger="focus" title="More Information" data-content="Your password must be more than 8 characters long, should contain at-least one Uppercase, one Lowercase, one Numeric and one special character." style="outline: none;">
                    <i data-feather="help-circle"></i>
                </a>
            </label>
            <input type="password" name="password" id="password" autocomplete="off"
                class="form-control @error('password') is-invalid @enderror"
                value="{{ old('password') }}">

            <span class="messages">
                <strong id="error-password"></strong>
            </span>

            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Contact Number</label>
            <input type="text" name="contact_number" id="contact_number" autocomplete="off"
                class="form-control @error('contact_number') is-invalid @enderror"
                value="{{ old('contact_number') }}">

            <span class="messages">
                <strong id="error-contact-number"></strong>
            </span>

            @error('contact_number')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        @endif

        <div class="input-group">
            <label for="">Address</label>
            <input type="text" name="address" id="address" autocomplete="off"
                class="form-control @error('address') is-invalid @enderror"
                value="{{($mode == 'update') ? $user->address : old('address')}}">

            <span class="messages">
                <strong id="error-address"></strong>
            </span>

            @error('address')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        
        <div class="input-group">
            <label for="">Status</label>
            <select name="status" id="status" class="custom-select form-control @error('status') is-invalid @enderror">
                <option value="1" {{ ($mode == 'update' && $user->status == 1) ? 'selected' : '' }}>Active</option>
                <option value="0" {{ ($mode == 'update' &&  $user->status == 0) ? 'selected' : '' }}>In-active</option>
            </select>

            <span class="messages">
                <strong id="error-status"></strong>
            </span>

            @error('status')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">User Role</label>
            <select name="user_role" id="user_role" class="custom-select form-control @error('user_role') is-invalid @enderror">
                <option value="4" {{ ($mode == 'update' && $user->user_role == 4) ? 'selected' : '' }}>Cook</option>
                <option value="3" {{ ($mode == 'update' && $user->user_role == 3) ? 'selected' : '' }}>Manager</option>
                <option value="2" {{ ($mode == 'update' && $user->user_role == 2) ? 'selected' : '' }}>Cashier</option>
                <option value="1" {{ ($mode == 'update' && $user->user_role == 1) ? 'selected' : '' }}>Admin</option>
            </select>

            <span class="messages">
                <strong id="error-user-type"></strong>
            </span>

            @error('user_role')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        @if ($mode == 'update')
        @method('PUT')
        <input type="hidden" name="id" value="{{ ($mode == 'update') ? $user->id : ''}}">
        @endif

        <div class="actions">           
            <button type="submit" class="btn btn-primary btn-submit" id="btn-submit">{{ ($mode == 'update') ? 'Submit Changes' : 'Submit' }}</button>
            <button type="reset" class="btn btn-secondary" id="btn-reset">Reset</button>
            <button type="button" onclick="window.location.href='{{route('user_accounts.index') }}'" class="btn btn-secondary" id="btn-back">Back</button>
        </div>

    </form>
</div>
</center>
<br>
@endsection
@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
    $('#card-form').on('submit', function(){
        var mode = "{{ $mode }}";
        $('#btn-submit').prop('disabled', true);
        $('#btn-reset').prop('disabled', true);
        $('#btn-back').prop('disabled', true);

        $('#btn-submit').html((mode == "update") ? "Submitting Changes.." : "Submitting..");
        $(this).submit();
    });

    $('#password-popover').popover({
        container: 'body'
    });
});
</script>
@endsection