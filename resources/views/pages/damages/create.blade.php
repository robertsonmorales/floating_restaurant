@extends('layouts.app')
@section('title', $title)

@section('content')
<center>
<div class="content">
    <form action="{{ ($mode == 'update') ? 
        route('damages.update', $data->id) : 
        route('damages.store') }}"
        method="POST" class="mb-4 card-form" id="card-form" style="width: 45%;">
        @csrf        

        <h5>{{ ucfirst($mode).' '.\Str::Singular($header) }}</h5>
        
        <div class="input-group">
            <label for="">Product</label>
            <select name="product" id="product" class="custom-select form-control @error('product') is-invalid @enderror" autofocus>
                @if($mode == 'create')
                    @foreach($products as $prod)
                    <option value="{{ $prod->id.'|'.$prod->name }}">{{ $prod->name }}</option>
                    @endforeach
                @endif

                @if($mode == 'update')
                    <option value="{{ $select_product->id.'|'.$select_product->name }}">{{ $select_product->name }}</option>
                    @foreach($products as $prod)                        
                        @if($select_product->id != $prod->id)
                        <option value="{{ $prod->id.'|'.$prod->name }}">{{ $prod->name }}</option>
                        @endif
                    @endforeach
                @endif
            </select>

            <span class="messages">
                <strong id="error-unit"></strong>
            </span>

            @error('product')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Quantity</label>
            <input type="text" name="quantity" id="quantity" autocomplete="off"
                class="form-control @error('quantity') is-invalid @enderror" autofocus
                value="{{($mode == 'update') ? $data->qty : old('quantity')}}">

            <span class="messages">
                <strong id="error-name"></strong>
            </span>

            @if(session()->get('error'))
            <span class="messages" role="alert">
                <strong>{{ session()->get('error') }}</strong>
            </span>
            @endif

            @error('quantity')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Description</label>
            <textarea cols="3" name="description" id="description" class="form-control @error('description') is-invalid @enderror" autofocus>{{ ($mode == 'update') ? $data->description : old('description') }}</textarea>

            <span class="messages">
                <strong id="error-name"></strong>
            </span>

            @error('description')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Approved By</label>
            <input type="text" name="approved_by" id="approved_by" autocomplete="off"
                class="form-control @error('approved_by') is-invalid @enderror" autofocus
                value="{{($mode == 'update') ? $data->approved_by : old('approved_by')}}">

            <span class="messages">
                <strong id="error-name"></strong>
            </span>

            @error('approved_by')
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
            <button type="button" onclick="window.location.href='{{route('damages.index') }}'" class="btn btn-secondary" id="btn-back">Back</button>
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