@extends('layouts.app')
@section('title', $title)

@section('content')
<form class="d-flex flex-column align-items-center" action="{{ ($mode == 'update') ? 
        route('menus.update', $data->id) : 
        route('menus.store') }}"
        method="POST" enctype="multipart/form-data">
    <div class="col-5 mb-4 card-form">
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
            <label for="">Menu Category</label>
            <select name="menu_category" id="menu_category" class="custom-select form-control @error('menu_category') is-invalid @enderror" autofocus required>
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

        <div class="input-group">
            <label for="">Menu Type</label>
            <select name="menu_type" id="menu_type" class="custom-select form-control @error('menu_type') is-invalid @enderror" autofocus required>
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
    </div>

    <div class="col-5 mb-4 card-form" style="width: 45%;">
        <h5>{{ ($mode == 'create') ? 'Upload Image' : 'Change Image' }}</h5>

        <div class="input-group">
            <label>Upload Type</label>
            <select id="upload_type" name="upload_type" class="custom-select form-control @error('upload_type') is-invalid @enderror">
                <option value="None" {{ ($mode == 'update' && $data->upload_type == "None") ? 'selected' : '' }}>None</option>
                <option value="1|File Upload" {{ ($mode == 'update' && $data->upload_type == "1|File Upload") ? 'selected' : '' }}>File Upload</option>
                <option value="0|URL" {{ ($mode == 'update' && $data->upload_type == "0|URL") ? 'selected' : '' }}>URL</option>
            </select>

            <span class="messages">
                <strong id="error-upload-type"></strong>
            </span>

            @error('upload_type')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group" id="file-group">
            <label>Upload Image</label>
            <input type="file" id="menu_image" name="menu_image" class="form-control @error('menu_image') is-invalid @enderror" accept="image/*">

            <span class="messages">
                <strong id="error-menu-image"></strong>
            </span>

            @error('menu_image')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group" id="url-group">
            <label for="">URL</label>
            <input type="url" name="url_image" id="url_image" autocomplete="off"
                class="form-control @error('url_image') is-invalid @enderror" autofocus placeholder="https://www.example.com/img/example.png" value="{{ ($mode == 'update' && $data->upload_type == '0|URL') ? $data->menu_image : '' }}">

            <span class="messages">
                <strong id="error-url-image"></strong>
            </span>

            @error('url_image')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <img src="{{ ($mode == 'update' && $data->upload_type == '1|File Upload') ? asset('uploads/menus/'.$data->menu_image) : '' }}" id="image-preview" width="300" class="rounded">
        </div>
    </div>

    <div class="col-5 mb-4 card-form" style="width: 45%;">
        <h5>{{ ucfirst($mode).' '.'Recipe' }}</h5>         

        <div class="input-group">
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-info btn-sm d-flex align-items-center" id="btn-plus">
                        <span class="btn-text mr-2">Add Recipe</span>
                        <i data-feather="plus-circle"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="input-group" id="recipe-list">
            
            @if($mode == 'update')
            @foreach($recipes as $recipe)
            <div class="row align-items-center mb-3 recipe-pro">
                <div class="col-8">
                    <select name="recipe[]" id="recipe" class="custom-select form-control" autofocus required title="product name">
                        @foreach($products as $pro)
                        <option value="{{ ($pro->id == $recipe->product_id) ? $recipe->product_id.'|'.$recipe->product_name : $pro->id.'|'.$pro->name }}" {{ ($pro->id == $recipe->product_id) ? 'selected' : '' }}>
                            {{ ($pro->id == $recipe->product_id) ? $recipe->product_name : $pro->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2">
                    <input type="text" min="1" name="recipe_qty[]" id="recipe_qty" required autocomplete="off"
                        class="form-control @error('recipe_qty') is-invalid @enderror" autofocus
                        value="{{ $recipe->stock_out }}" title="product quantity">
                </div>
                <div class="col">
                    <button type="button" class="btn btn-sm text-danger btn-minus" title="remove">
                        <i data-feather="x"></i>
                    </button>
                </div>
            </div>

            @endforeach
            @endif
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
    </div>
</form>
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

function fileUpload(){
    if ($('#upload_type').val() == "1|File Upload") {
        $('#file-group').show(300);
        $('#url-group').hide(300);
    }else if($('#upload_type').val() == "0|URL"){
        $('#url-group').show(300);
        $('#url_image').val(); //host + '/uploads/menu_categories/' + $("#url_image").val()
        $('#file-group').hide(300);

        $('#image-preview').attr('src', $("#url_image").val());
        $('#image-preview').show(300);
    }else{
        $('#file-group').hide(300);
        $('#url-group').hide(300);
        $('#image-preview').hide(300);
    }
}

fileUpload();

$('#upload_type').on('change', function(){
    if ($(this).val() == "1|File Upload") {
        $('#file-group').show(300);
        $('#url-group').hide(300);
    }else if($(this).val() == "0|URL"){
        $('#url-group').show(300);
        $('#file-group').hide(300);

        $('#image-preview').attr('src', $("#url_image").val());
        $('#image-preview').show(300);


    }else{
        $('#file-group').hide(300);
        $('#url-group').hide(300);
        $('#image-preview').hide(300);
    }
});

$('#menu_image').on('change', function(){
    var file = $("#menu_image").get(0).files[0];
    
    if(file){
        var reader = new FileReader();

        reader.onload = function(){
            $("#image-preview").attr("src", reader.result);
        }

        reader.readAsDataURL(file);
    }
});

$("#url_image").on('keyup', function(){
    $('#image-preview').attr('src', $(this).val());
});

$('#btn-plus').on('click', function(){
    var products = <?= $products ?>;
    var length = document.getElementsByClassName('btn-minus').length;
    var cols_length = $('#recipe-list').children().length;

    var x = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';

    var recipe = '\
        <div class="row align-items-center mb-3">\
            <div class="col-8">\
                <select name="recipe[]" id="recipe" class="custom-select form-control" autofocus required title="product name">\
                    <option value="" style="display: none;">Select product...</option>\
                    @foreach($products as $pro)\
                    <option value="{{ $pro->id."|".$pro->name }}">{{ $pro->name }}</option>\
                    @endforeach\
                </select>\
            </div>\
            <div class="col-2">\
                <input type="text" min="1" name="recipe_qty[]" id="recipe_qty" required autocomplete="off"\
                    class="form-control" autofocus\
                    value="1" title="product quantity">\
            </div>\
            <div class="col">\
                <button type="button" class="btn text-danger btn-sm btn-minus btn-minus'+length+'" title="remove" onclick="removeRecipe('+length+')">'+x+'\
                </button>\
            </div>\
        </div>';

    if (cols_length < products.length) {
        $('#recipe-list').append(recipe);
    }else{
        $(this).prop('disabled', true);
        Toast.fire({
            icon: 'warning',
            title: 'Maximum adding recipe reached',
        });
    }
});

function removeRecipe(data){
    var remove = $('.btn-minus'+data).parent().parent().remove();
    $('.btn-text').html('Add Recipe');
    $('#btn-plus').prop('disabled', false);
}

$('.btn-minus').on('click', function(){
    $(this).parent().parent().remove();
    $('.btn-text').html('Add Recipe');
    $('#btn-plus').prop('disabled', false);
});

$(document).on('keyup', "input[name='recipe_qty[]']", function(){
    if (isNaN($(this).val())) {
        Toast.fire({
            icon: 'warning',
            title: 'Please input a number',
        });
    }
});

$('form').on('submit', function(){
    var mode = "{{ $mode }}";
    
    $('#btn-submit').prop('disabled', true);
    $('#btn-reset').prop('disabled', true);
    $('#btn-back').prop('disabled', true);
    $('#btn-plus').prop('disabled', true);
    $('.btn-minus').prop('disabled', true);

    $('#btn-submit').html((mode == "update") ? "Submitting Changes.." : "Submitting..");
    $(this).submit();
});

</script>
@endsection