@extends('layouts.app')
@section('title', $title)

@section('content')
<center>
<div class="content" style="width: 45%;">
    <form action="{{ ($mode == 'update') ? 
        route('cancellation_reasons.update', $data->id) : 
        route('cancellation_reasons.store') }}"
        method="POST" class="card-form" id="card-form">
        @csrf

        <h5>{{ ucfirst($mode).' '.\Str::Singular($header) }}</h5>
        
        <div class="input-group">
            <label for="">Name</label>
            <input type="text" name="name" id="name" autocomplete="off"
                class="form-control @error('name') is-invalid @enderror" autofocus
                value="{{($mode == 'update') ? $data->name : old('name')}}">

            <span class="messages">
                <strong id="error-name"></strong>
            </span>

            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Description</label>
            <textarea cols="5" name="description" id="description" class="form-control @error('description') is-invalid @enderror" autofocus>{{ ($mode == 'update') ? $data->description : old('description') }}</textarea>

            <span class="messages">
                <strong id="error-description"></strong>
            </span>

            @error('description')
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
            <button type="button" onclick="window.location.href='{{route('cancellation_reasons.index') }}'" class="btn btn-secondary" id="btn-back">Back</button>
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
});
</script>
@endsection