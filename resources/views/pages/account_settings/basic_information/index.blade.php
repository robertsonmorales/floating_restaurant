@extends('layouts.app')
@section('title', $title)

@section('content')

@if(session()->get('success'))
<div class="alert alert-success alert-dismissible fade show alerts" role="alert">
    <span><i data-feather="check"></i> {{ session()->get('success') }}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true" class="dismiss-icon"><i data-feather="x"></i> </span>
    </button>
</div>
@endif

@if(session()->get('error'))
<div class="alert alert-danger alert-dismissible fade show alerts" role="alert">
    <span><i data-feather="x"></i> {{ session()->get('error') }} </span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true" class="dismiss-icon"><i data-feather="x"></i> </span>
    </button>
</div>
@endif

<div class="row no-gutters user-container">
    @include('pages.account_settings.sidebar')
    <div class="col-6">
        @include('pages.account_settings.basic_information.form')
    </div>
</div>

<!-- The Modal -->
<form class="modal" action="{{ route('account_settings.change_profile') }}" method="POST" id="form-submit" enctype="multipart/form-data">
    @csrf

    <div class="modal-content">
        <button type="button" class="btn" id="close-modal">
            <i data-feather="x"></i>
        </button>

        <div class="modal-header">
            <input type="file" name="profile-image" id="profile-image" onchange="previewFile(this)" accept="image/*" style="display: none;">
            <div class="modal-body" style="padding: 0 !important;">
                <h5>Change Profile</h5>

                <div class="profile-image">
                    <img id="previewImg" src="{{ (Auth::user()->profile_image) ? asset('images/user_profiles/'.Auth::user()->username.Auth::user()->id.'/'.Auth::user()->profile_image.'') : asset('images/user_profiles/avatar.svg') }}" alt="preview profile">
                </div>

                <button type="button" class="btn btn-outline-primary btn-choose-photo">
                    <span>Upload a different photo</span>
                </button>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="btn-save">Set New Profile Picture</button>
            <button type="button" class="btn btn-outline-secondary" id="btn-cancel">Cancel</button>
        </div>
    </div>
</form>
<!-- Ends here -->

@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
    $('.modal-footer').hide();

    $('.btn-change-profile').on('click', function(){
        $('#form-submit').attr('style', 'display: flex;');
    });

    $('#btn-cancel').on('click', function(){
        $('#form-submit').attr('style', 'display: none;');
    });

    $('#close-modal').on('click', function(){
        $('#form-submit').attr('style', 'display: none;');
    });

    $(".btn-choose-photo").on('click', function(){
        $('#profile-image').trigger('click');
    });

    $('#btn-save').on('click', function(){
        $('#btn-cancel').prop('disabled', true);
        $(this).prop('disabled', true);
        $(this).html("Setting New Profile Picture..");

        document.getElementById("form-submit").submit();
    });

    $('#settings-form').on('submit', function(){
        $('#btn-save').prop('disabled', true);
        $('#btn-reset').prop('disabled', true);

        $('#btn-save').html('Saving Changes..');
        $(this).submit();
    });
});

function previewFile(input){
    var file = $("#profile-image").get(0).files[0];
    
    if(file){
        var reader = new FileReader();

        reader.onload = function(){
            $("#previewImg").attr("src", reader.result);
            $('.modal-footer').show();
        }

        reader.readAsDataURL(file);
    }
}
</script>
@endsection