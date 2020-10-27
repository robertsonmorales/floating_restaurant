@extends('layouts.app')
@section('title', $title)

@section('content')
<div class="d-flex flex-column align-items-center">
    <form action="{{ ($mode == 'update') ? 
        route('products.update', $data->id) : 
        route('products.store') }}"
        method="POST" class="col-5 mb-4 card-form" id="card-form">
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
            <label for="">Product Unit</label>
            <select name="unit" id="unit" class="custom-select form-control @error('unit') is-invalid @enderror" autofocus>
                @if($mode == 'create')
                    @foreach($product_units as $units)
                    <option value="{{ $units->id.'|'.$units->name }}">{{ $units->name }}</option>
                    @endforeach
                @endif

                @if($mode == 'update')
                    <option value="{{ $select_product_units->id.'|'.$select_product_units->name }}">{{ $select_product_units->name }}</option>
                    @foreach($product_units as $units)                        
                        @if($select_product_units->id != $units->id)
                        <option value="{{ $units->id.'|'.$units->name }}">{{ $units->name }}</option>
                        @endif
                    @endforeach
                @endif
            </select>

            <span class="messages">
                <strong id="error-unit"></strong>
            </span>

            @error('unit')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Product Category</label>
            <select name="product_categories" id="product_category" class="custom-select form-control @error('product_categories') is-invalid @enderror" autofocus>
                @if($mode == 'create')
                    @foreach($product_categories as $types)
                    <option value="{{ $types->id.'|'.$types->name }}">{{ $types->name }}</option>
                    @endforeach
                @endif

                @if($mode == 'update')
                    <option value="{{ $select_product_categories->id.'|'.$select_product_categories->name }}">{{ $select_product_categories->name }}</option>
                    @foreach($product_categories as $types)                        
                        @if($select_product_categories->id != $types->id)
                        <option value="{{ $types->id.'|'.$types->name }}">{{ $types->name }}</option>
                        @endif
                    @endforeach
                @endif
            </select>

            <span class="messages">
                <strong id="error-product-categories"></strong>
            </span>

            @error('product_categories')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        
        <div class="input-group">
            <label for="">Inventoriable
                <a tabindex="0" id="inventoriable-popover" role="button" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="More Information" data-content="If the product is inventoriable, select 'Yes' and it will be monitored in stock management." style="outline: none;">
                    <i data-feather="help-circle"></i>
                </a>
            </label>
            <select name="inventoriable" id="inventoriable" class="custom-select form-control @error('inventoriable') is-invalid @enderror" autofocus>
                <option value="1" {{ ($mode == 'update' && $data->inventoriable == 1) ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ ($mode == 'update' && $data->inventoriable == 0) ? 'selected' : '' }}>No</option>
            </select>

            <span class="messages">
                <strong id="error-inventoriable"></strong>
            </span>

            @error('inventoriable')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Minimum Stocks
                <a tabindex="0" id="stocks-popover" role="button" data-toggle="popover" data-placement="bottom" data-trigger="focus" title="More Information" data-content="When the stocks is lower than the minimum stocks this will give a warning to the user that the product needs to be supplied" style="outline: none;">
                    <i data-feather="help-circle"></i>
                </a>
            </label>
            <input type="text" name="minimum_stocks" id="minimum_stocks" autocomplete="off"
                class="form-control @error('minimum_stocks') is-invalid @enderror" autofocus
                value="{{($mode == 'update') ? $data->minimum_stocks : old('minimum_stocks') }}">

            <span class="messages">
                <strong id="error-minimum_stocks"></strong>
            </span>

            @error('minimum_stocks')
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
            <button type="button" onclick="window.location.href='{{route('products.index') }}'" class="btn btn-secondary" id="btn-back">Back</button>
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

    $('#stocks-popover').popover({
        container: 'body'
    });

    $('#inventoriable-popover').popover({
        container: 'body'
    });
});
</script>
@endsection