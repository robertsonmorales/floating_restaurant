@extends('layouts.app')
@section('title', $title)

@section('content')
<div class="d-flex flex-column align-items-center">
    <form action="{{ ($mode == 'update') ? 
        route('deliveries.update', $data->id) : 
        route('deliveries.store') }}"
        method="POST" class="col-5 mb-4 card-form" id="card-form">
        @csrf        

        <h5>{{ ucfirst($mode).' '.\Str::Singular($header) }}</h5>
        
        <div class="input-group">
            <label for="">Delivery Name</label>
            <input type="text" name="delivery_name" id="delivery_name" class="form-control @error('delivery_name') is-invalid @enderror" value="{{ ($mode == 'update') ? $data->delivery_name : old('delivery_name') }}">

            <span class="messages">
                <strong id="error-delivery-code"></strong>
            </span>

            @error('delivery_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-info btn-sm d-flex align-items-center" id="btn-plus">
                        <span><i data-feather="plus-circle"></i></span>
                        <span class="btn-text ml-2">Add Product</span>                        
                    </button>
                </div>
            </div>
        </div>

        <div class="input-group" id="product-list">
            @if($mode == 'create')
            <div class="row align-items-center">
                    <div class="col-7">
                        <select name="product[]" id="product" class="custom-select form-control" autofocus required title="product name">
                            @foreach($products as $prod)
                            <option value="{{ $prod->id.'|'.$prod->name }}">{{ $prod->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-3">
                        <input type="number" min="1" name="qty[]" id="qty" required autocomplete="off"
                            class="form-control @error('qty') is-invalid @enderror" autofocus
                            value="1" title="product quantity">
                    </div>
                    <div class="col-2"></div>
                </div>
            @endif

            @if($mode == 'update')
                @foreach($delivered_products as $delivered)
                <div class="row align-items-center mt-2">
                    <div class="col-7">
                        <select name="product[]" id="product" class="custom-select form-control" autofocus required title="product name">
                            <option value="{{ $delivered->product_id.'|'.$delivered->product_name }}">{{ $delivered->product_name }}</option>
                            @foreach($products as $prod)
                            @if($prod->id != $delivered->product_id)
                            <option value="{{ $prod->id.'|'.$prod->name }}">{{ $prod->name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div> 
                    <div class="col-3">
                        <input type="text" min="1" name="qty[]" id="qty" required autocomplete="off"
                            class="form-control @error('qty') is-invalid @enderror" autofocus
                            value="{{ $delivered->qty }}" title="product quantity" readonly>
                    </div>
                    <div class="col-2">
                        <!-- <button type="button" class="btn btn-sm text-danger btn-minus" title="remove">
                            <i data-feather="x"></i>
                        </button> -->
                    </div>
                </div>
                @endforeach
            @endif

        </div>

        {{--<div class="input-group">
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
        </div> --}}

        {{--<div class="input-group">
            <label for="">Quantity</label>
            <input type="text" name="quantity" id="quantity" autocomplete="off"
                class="form-control @error('quantity') is-invalid @enderror" autofocus
                value="{{($mode == 'update') ? $data->qty : old('quantity')}}">

            <span class="messages">
                <strong id="error-qty"></strong>
            </span>

            @error('quantity')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>--}}

        <div class="input-group">
            <label for="">Description</label>
            <textarea cols="3" name="description" id="description" class="form-control @error('description') is-invalid @enderror" autofocus>{{ ($mode == 'update') ? $data->description : old('description') }}</textarea>

            <span class="messages">
                <strong id="error-description"></strong>
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
                <strong id="error-approved-by"></strong>
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
            <button type="button" onclick="window.location.href='{{route('deliveries.index') }}'" class="btn btn-secondary" id="btn-back">Back</button>
        </div>
    </form>

    <!-- The Modal -->
    <div class="modal">
        <div class="modal-content">
            <div class="modal-header">      
                <div class="modal-icon">
                    <i data-feather="alert-triangle"></i>
                </div>

                <div class="modal-body">
                    <h5></h5>
                    <p></p>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn" id="btn-okie"></button>
            </div>
        </div>
    </form>
    <!-- Ends here -->
</div>
<br>
@endsection
@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
    var products = <?= $products ?>;
    
    $('#card-form').on('submit', function(){
        var mode = "{{ $mode }}";
        $('#btn-submit').prop('disabled', true);
        $('#btn-reset').prop('disabled', true);
        $('#btn-back').prop('disabled', true);

        $('#btn-submit').html((mode == "update") ? "Submitting Changes.." : "Submitting..");
        $(this).submit();
    });

    $('#btn-plus').on('click', function(){
        var length = document.getElementsByClassName('btn-minus').length;
        var cols_length = $('#product-list').children().length;

        var x = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';

        var productContent = '\
            <div class="row align-items-center mt-2">\
                <div class="col-7">\
                    <select name="product[]" id="product" class="custom-select form-control" autofocus required title="product name">\
                        <option value="" style="display: none;">Select product...</option>\
                        @foreach($products as $pro)\
                        <option value="{{ $pro->id."|".$pro->name }}">{{ $pro->name }}</option>\
                        @endforeach\
                    </select>\
                </div>\
                <div class="col-3">\
                    <input type="number" min="1" name="qty[]" id="qty" required autocomplete="off"\
                        class="form-control" autofocus\
                        value="1" title="product quantity">\
                </div>\
                <div class="col-2">\
                    <button type="button" class="btn text-danger btn-sm btn-minus btn-minus'+length+'" title="remove" onclick="removeProduct('+length+')">'+ x +'\
                    </button>\
                </div>\
            </div>';

        if (cols_length < products.length) {
            $('#product-list').append(productContent);
        }else{
            $(this).prop('disabled', true);
            $('.modal').attr('style', 'display: flex;');
            $('.modal .modal-icon').addClass('modal-icon-info');
            $('.modal .modal-body h5').html('Information');
            $('.modal .modal-body p').html('You have reached the maximum adding of products.');
            $('#btn-okie').addClass('btn-secondary');
            $('#btn-okie').html('Close');
        }
    });

    $('#btn-okie').on('click', function(){
        $('.modal').hide();
    });

    $('.btn-minus').on('click', function(){
        $(this).parent().parent().remove();
        $('#btn-plus').prop('disabled', false);
    });    
});

function removeProduct(data){
    var remove = $('.btn-minus'+data).parent().parent().remove();
    $('#btn-plus').prop('disabled', false);
}
</script>
@endsection