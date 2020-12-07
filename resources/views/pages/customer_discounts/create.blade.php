@extends('layouts.app')
@section('title', $title)

@section('content')
<div class="d-flex flex-column align-items-center">
    <form action="{{ ($mode == 'update') ? 
        route('customer_discounts.update', $data->id) : 
        route('customer_discounts.store') }}"
        method="POST" class="col-5 mb-4 card-form" id="card-form">
        @csrf

        <h5>{{ ucfirst($mode).' '.\Str::Singular($header) }}</h5>
        
        <div class="input-group">
            <label for="">Name</label>
            <input type="text" name="name" id="name" required autocomplete="off"
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
            <label for="">Percentage
                <a tabindex="0" id="percentage-popover" role="button" data-toggle="popover" data-placement="top" data-trigger="focus" title="More Information" data-content="Please input the percentage of the discount, e.g(10, 20, 30)." style="outline: none;">
                    <i data-feather="help-circle"></i>
                </a>
            </label>

            <input type="number" name="percentage" id="percentage" required autocomplete="off"
                class="form-control @error('percentage') is-invalid @enderror" autofocus
                value="{{($mode == 'update') ? $data->percentage : old('percentage')}}" max="100" min="1">

            <span class="messages">
                <strong id="error-percentage"></strong>
            </span>

            @error('percentage')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Customer Verification
                <a tabindex="0" id="verification-popover" role="button" data-toggle="popover" data-placement="top" data-trigger="focus" title="More Information" data-content="This is to validate the customer using validation cards, e.g(Senior Citizen ID, PWD ID)." style="outline: none;">
                    <i data-feather="help-circle"></i>
                </a>
            </label>
            <select name="verification" id="verification" class="custom-select form-control @error('verification') is-invalid @enderror" autofocus>
                <option value="1" {{ ($mode == 'update' && $data->verification == 1) ? 'selected' : '' }}>Required</option>
                <option value="0" {{ ($mode == 'update' && $data->verification == 0) ? 'selected' : '' }}>Not Required</option>
            </select>

            <span class="messages">
                <strong id="error-verification"></strong>
            </span>

            @error('verification')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        
        <div class="input-group">
            <label for="">Status</label>
            <select name="status" id="status" class="custom-select form-control @error('status') is-invalid @enderror" autofocus>
                <option value="1" {{ ($mode == 'update' && $data->status == 1) ? 'selected' : '' }}>Active</option>
                <option value="0" {{ ($mode == 'update' && $data->status == 0) ? 'selected' : '' }}>In-active</option>
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

        @if ($mode == 'update')
        @method('PUT')
        <input type="hidden" name="id" value="{{ ($mode == 'update') ? $data->id: ''}}">
        @endif

        <div class="actions">           
            <button type="submit" class="btn btn-primary btn-submit" id="btn-submit">{{ ($mode == 'update') ? 'Submit Changes' : 'Submit' }}</button>
            <button type="reset" class="btn btn-secondary" id="btn-reset">Reset</button>
            <button type="button" onclick="window.location.href='{{route('customer_discounts.index') }}'" class="btn btn-secondary" id="btn-back">Back</button>
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

    const Toast = Swal.mixin({
        toast: true,
        position: 'bottom-right',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: false,
    });

    $("#percentage").on('keyup', function(){
        if (isNaN($("#percentage").val())) {
            Toast.fire({
                icon: 'warning',
                title: 'Please input a number'
            });
        }
    });

    $('#percentage-popover').popover({
        container: 'body'
    });

    $('#verification-popover').popover({
        container: 'body'
    });
});
</script>
@endsection