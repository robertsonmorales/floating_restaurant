@extends('layouts.app')
@section('title', $title)

@section('content')
<div class="d-flex flex-column align-items-center">
    <form action="{{ ($mode == 'update') ? 
        route('employees.update', $data->id) : 
        route('employees.store') }}"
        method="POST" class="col-5 mb-4 card-form" id="card-form">
        @csrf

        <h5>{{ ucfirst($mode).' '.\Str::Singular($header) }}</h5>

        <div class="input-group">
            <label for="">First Name</label>
            <input type="text" name="first_name" id="first_name" required autocomplete="off"
                class="form-control @error('first_name') is-invalid @enderror" autofocus
                value="{{($mode == 'update') ? Crypt::decryptString($data->first_name) : old('first_name')}}">
            @error('first_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Middle Name</label>
            <input type="text" name="middle_name" id="middle_name" required autocomplete="off"
                class="form-control @error('middle_name') is-invalid @enderror" autofocus
                value="{{($mode == 'update') ? Crypt::decryptString($data->middle_name) : old('middle_name')}}">
            @error('middle_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Last Name</label>
            <input type="text" name="last_name" id="last_name" required autocomplete="off"
                class="form-control @error('last_name') is-invalid @enderror" autofocus
                value="{{($mode == 'update') ? Crypt::decryptString($data->last_name) : old('last_name')}}">
            @error('last_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Birthdate</label>
            <input type="date" name="birthdate" id="birthdate" required autocomplete="off"
                class="form-control @error('birthdate') is-invalid @enderror" autofocus
                value="{{($mode == 'update') ? Crypt::decryptString($data->birthdate) : old('birthdate')}}">
            @error('birthdate')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Gender</label>
            <select name="gender" id="gender" class="custom-select form-control @error('gender') is-invalid @enderror" autofocus required>
                <option value="1" {{ ($mode == 'update' && $data->gender == 1) ? 'selected' : '' }}>Male</option>
                <option value="0" {{ ($mode == 'update' && $data->gender == 0) ? 'selected' : '' }}>Female</option>
            </select>
            @error('gender')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Contact Number</label>
            <input type="text" name="contact_number" minlength="11" maxlength="11" id="contact_number" required autocomplete="off"
                class="form-control @error('contact_number') is-invalid @enderror" autofocus
                value="{{($mode == 'update') ? Crypt::decryptString($data->contact_number) : old('contact_number')}}">
            @error('contact_number')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Address</label>
            <input type="text" name="address" id="address" required autocomplete="off"
                class="form-control @error('address') is-invalid @enderror" autofocus
                value="{{($mode == 'update') ? Crypt::decryptString($data->address) : old('address')}}">
            @error('address')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Job Position</label>
            <select name="position" id="position" class="custom-select form-control @error('position') is-invalid @enderror" autofocus required>
                @if($mode == 'create')
                @foreach($employee_positions as $position)
                <option value="{{ $position->id }}">
                    {{ $position->name }}
                </option>
                @endforeach
                @endif

                @if($mode == 'update')
                <option value="{{ $selected_position->id }}" selected>{{ $selected_position->name }}</option>
                @foreach($employee_positions as $position)
                @if($position->id != $selected_position->id)
                <option value="{{ $position->id }}">{{ $position->name }}</option>
                @endif
                @endforeach
                @endif
            </select>
            @error('position')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        
        <div class="input-group">
            <label for="">Status</label>
            <select name="status" id="status" class="custom-select form-control @error('status') is-invalid @enderror" autofocus required>
                <option value="1" {{ ($mode == 'update' && $data->status == 1) ? 'selected' : '' }}>Active</option>
                <option value="0" {{ ($mode == 'update' && $data->status == 0) ? 'selected' : '' }}>In-active</option>
            </select>
            @error('status')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        @if ($mode == 'update')
        @method('PUT')
        <input type="hidden" name="id" value="{{ ($mode == 'update') ? $data->id: ''}}">
        @endif

        <div class="actions">           
            <button type="submit" class="btn btn-primary btn-submit" id="btn-submit">{{ ($mode == 'update') ? 'Submit Changes' : 'Submit' }}</button>
            <button type="reset" class="btn btn-secondary" id="btn-reset">Reset</button>
            <button type="button" onclick="window.location.href='{{route('employees.index') }}'" class="btn btn-secondary" id="btn-back">Back</button>
        </div>
    </form>
</div>
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

});
</script>
@endsection
