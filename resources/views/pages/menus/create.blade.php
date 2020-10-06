@extends('layouts.app')
@section('title', $title)

@section('content')
<center>
<div class="content" style="width: 45%;">
    <form action="{{ ($mode == 'update') ? 
        route('menus.update', $data->id) : 
        route('menus.store') }}"
        method="POST" class="card-form" id="card-form">
        @csrf

        <h5>{{ ucfirst($mode).' '.\Str::Singular($header) }}</h5>
        <div class="divider"></div>
        
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
            <label for="">Menu Category</label>
            <select name="menu_category" id="menu_category" class="form-control @error('menu_category') is-invalid @enderror" autofocus required>
                @if($mode == 'create')
                    @foreach($menu_category as $types)
                    <option value="{{ $types->id }}">{{ $types->name }}</option>
                    @endforeach
                @endif

                @if($mode == 'update')
                    <option value="{{ $select_menu_category->id }}">{{ $select_menu_category->name }}</option>
                    @foreach($menu_category as $types)                        
                        @if($select_menu_category->id != $types->id)
                        <option value="{{ $types->id }}">{{ $types->name }}</option>
                        @endif
                    @endforeach
                @endif
            </select>

            <span class="messages">
                <strong id="error-menu-category"></strong>
            </span>

            @error('menu_category')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Price</label>
            <input type="text" name="price" id="price" required autocomplete="off"
                class="form-control @error('price') is-invalid @enderror" autofocus
                value="{{($mode == 'update') ? $data->price : old('price')}}">

            <span class="messages">
                <strong id="error-price"></strong>
            </span>

            @error('price')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group" id="recipe-list">
            <div class="columns" style="margin-bottom: 5px; display: flex;">
                <label for="">Add Recipes</label>
                <button type="button" class="btn btn-primary btn-plus" id="btn-plus">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            @if($mode == 'update')
            @foreach($recipes as $recipe)
            <div class="columns">
                <select name="recipe[]" id="recipe" class="form-control @error('recipe') is-invalid @enderror" autofocus required title="product name">
                    @foreach($products as $pro)
                    <option value="{{ ($pro->id == $recipe['product'][0]) ? $recipe['product'][0].'|'.$recipe['product'][1] : $pro->id.'|'.$pro->name }}" {{ ($pro->id == $recipe['product'][0]) ? 'selected' : '' }}>
                        {{ ($pro->id == $recipe['product'][0]) ? $recipe['product'][1] : $pro->name }}
                    </option>
                    @endforeach
                </select>

                <input type="text" min="1" name="recipe_qty[]" id="recipe_qty" required autocomplete="off"
                    class="form-control @error('recipe_qty') is-invalid @enderror" autofocus
                    value="{{ $recipe['stock_out'] }}" placeholder="Stock out..." title="product quantity" style="text-align: center;">
                <button type="button" class="btn-minus" title="remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endforeach
            @endif

            @error('recipe')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Menu Type</label>
            <select name="menu_type" id="menu_type" class="form-control @error('menu_type') is-invalid @enderror" autofocus required>
                @if($mode == 'create')
                    @foreach($menu_type as $types)
                    <option value="{{ $types->id }}">{{ $types->name }}</option>
                    @endforeach
                @endif

                @if($mode == 'update')
                    <option value="{{ $select_menu_type->id }}">{{ $select_menu_type->name }}</option>
                    @foreach($menu_type as $types)                        
                        @if($select_menu_type->id != $types->id)
                        <option value="{{ $types->id }}">{{ $types->name }}</option>
                        @endif
                    @endforeach
                @endif
            </select>

            <span class="messages">
                <strong id="error-menu-type"></strong>
            </span>

            @error('menu_type')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        
        <div class="input-group">
            <label for="">Status</label>
            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" autofocus>
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
            <button type="button" onclick="window.location.href='{{route('menus.index') }}'" class="btn btn-secondary" id="btn-back">Back</button>
        </div>
    </form>
</div>
</center>
<br>
@endsection
@section('scripts')
<script type="text/javascript">
const Toast = Swal.mixin({
    toast: true,
    position: 'bottom-right',
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: false,
});

$(document).on('click', '#btn-plus', function(){
    var products = <?= $products ?>;
    var length = document.getElementsByClassName('btn-minus').length;
    var cols_length = document.getElementsByClassName('columns').length - 1;

    var recipe = '\
        <div class="columns">\
            <select name="recipe[]" id="recipe" class="form-control" autofocus required title="product name">\
                <option value="" style="display: none;">Select product...</option>\
                @foreach($products as $pro)\
                <option value="{{ $pro->id."|".$pro->name }}">{{ $pro->name }}</option>\
                @endforeach\
            </select>\
            <input type="text" min="1" name="recipe_qty[]" id="recipe_qty" required autocomplete="off"\
                class="form-control" autofocus\
                value="1" placeholder="Stock out..." title="product quantity" style="text-align: center;">\
            <button type="button" class="btn-minus btn-minus'+length+'" title="remove" onclick="removeRecipe('+length+')">\
                <i class="fas fa-times"></i>\
            </button>\
        </div>';
    if (cols_length < products.length) {
        $('#recipe-list').append(recipe);
    }else{            
        Toast.fire({
            icon: 'warning',
            title: 'You have '+products.length+' maximum addition of fields',
        });
    }
});

function removeRecipe(data){
    var remove = $('.btn-minus'+data).parent().remove();        
}

$('.btn-minus').on('click', function(){
    $(this).parent().remove();
});

$("#menu_category").select2();
$("#menu_type").select2();

$(document).on('keyup', "input[name='recipe_qty[]']", function(){
    if (isNaN($(this).val())) {
        Toast.fire({
            icon: 'warning',
            title: 'Please input a number',
        });
    }
});

$('#card-form').on('submit', function(){
    var mode = "{{ $mode }}";
    
    $('#btn-submit').prop('disabled', true);
    $('#btn-reset').prop('disabled', true);
    $('#btn-back').prop('disabled', true);

    $('#btn-submit').html((mode == "update") ? "Submitting Changes.." : "Submitting..");
    $(this).submit();
});

</script>
@endsection