@extends('layouts.app')
@section('title', $title)

@section('content')
<form action="{{ ($mode == 'update') ? route('menu_categories.update', $data->id) : route('menu_categories.store') }}" method="POST" class="d-flex flex-column align-items-center" id="card-form" enctype="multipart/form-data">
    <div class="mb-4 card-form col-5">
        @csrf

        <h5>{{ ucfirst($mode).' '.\Str::Singular($header) }}</h5>

        <div class="input-group">
            <label for="">Name</label>
            <input type="text" name="name" id="name" autocomplete="off" class="form-control @error('name') is-invalid @enderror" value="{{($mode == 'update') ? $data->name : old('name')}}">

            <span class="messages" role="alert">
                <strong id="error-name"></strong>
            </span>

            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Tag Color</label>
            <input type="color" name="tag_color" id="tag-color" autocomplete="off"
                class="form-control-color @error('tag-color') is-invalid @enderror"
                value="{{($mode == 'update') ? $data->tag_color : old('tag-color')}}">

            <span class="messages">
                <strong id="error-tag-color"></strong>
            </span>

            @error('color')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Icon</label>
            <input type="text" name="category_icon" id="category_icon" autocomplete="off" class="form-control @error('name') is-invalid @enderror" value="{{($mode == 'update') ? $data->category_icon : old('category_icon')}}">

            <span class="messages" role="alert">
                <strong id="error-category-icon"></strong>
            </span>

            @error('category_icon')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        
        <div class="input-group">
            <label for="">Status</label>
            <select name="status" id="status" class="custom-select form-control @error('status') is-invalid @enderror">
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

    <div class="mb-4 card-form col-5">
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
            <input type="file" id="category_image" name="category_image" onchange="previewFile(this)" class="form-control @error('category_image') is-invalid @enderror" accept="image/*">

            <span class="messages">
                <strong id="error-category-image"></strong>
            </span>

            @error('category_image')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group" id="url-group">
            <label for="">URL</label>
            <input type="url" name="url_image" id="url_image" autocomplete="off"
                class="form-control @error('url_image') is-invalid @enderror" autofocus placeholder="https://www.example.com/img/example.png" value="{{ ($mode == 'update') ? $data->category_image : '' }}">

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
            <img src="{{ ($mode == 'update' && $data->upload_type == '1|File Upload') ? asset('images/menu_categories/'.$data->category_image) : '' }}" id="image-preview" width="300" class="rounded">
        </div>

        @if ($mode == 'update')
        @method('PUT')
        <input type="hidden" name="id" value="{{ ($mode == 'update') ? $data->id: ''}}">
        @endif

        <div class="actions">           
            <button type="submit" class="btn btn-primary btn-submit" id="btn-submit">{{ ($mode == 'update') ? 'Submit Changes' : 'Submit' }}</button>
            <button type="reset" class="btn btn-secondary" id="btn-reset">Reset</button>
            <button type="button" onclick="window.location.href='{{route('menu_categories.index') }}'" class="btn btn-secondary" id="btn-back">Back</button>
        </div>
    </div>
</form>
<br>
@endsection
@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
    function fileUpload(){
        if ($('#upload_type').val() == "1|File Upload") {
            $('#file-group').show(500);
            $('#url-group').hide(500);
        }else if($('#upload_type').val() == "0|URL"){
            $('#url-group').show(500);
            $('#file-group').hide(500);

            $('#image-preview').attr('src', $("#url_image").val());
            $('#image-preview').show(500);
        }else{
            $('#file-group').hide(500);
            $('#url-group').hide(500);
            $('#image-preview').hide(500);
        }
    }

    $("#url_image").on('keyup', function(){
        $('#image-preview').attr('src', $(this).val());
    });

    $('#upload_type').on('change', function(){
        if ($(this).val() == "1|File Upload") {
            $('#file-group').show(500);
            $('#url-group').hide(500);
        }else if($(this).val() == "0|URL"){
            $('#url-group').show(500);
            $('#file-group').hide(500);

            $('#image-preview').attr('src', $("#url_image").val());
            $('#image-preview').show(500);
        }else{
            $('#file-group').hide(500);
            $('#url-group').hide(500);
            $('#image-preview').hide(500);
        }
    });

    $('#category_image').on('change', function(){
        var file = $("#category_image").get(0).files[0];
        
        if(file){
            var reader = new FileReader();

            reader.onload = function(){
                $("#image-preview").attr("src", reader.result);
            }

            reader.readAsDataURL(file);
        }
    })

    $('#card-form').on('submit', function(){
        var mode = "{{ $mode }}";
        
        $('#btn-submit').prop('disabled', true);
        $('#btn-reset').prop('disabled', true);
        $('#btn-back').prop('disabled', true);

        $('#btn-submit').html((mode == "update") ? "Submitting Changes.." : "Submitting..");
        $(this).submit();
    });

    fileUpload();
});
</script>
@endsection