@extends('layouts.app')
@section('title', $title)

@section('content')
<div class="customer-content">
    <div class="user-container">
        <div class="user-options">
            <ul style="
                background-image: url('{{ asset('images/svg/wave.svg') }}');
                background-size: cover;
                background-position: center;">
                <li>
                    <div class="user-profile">
                        <div class="profile-image-form">
                            <span class="image-wrapper" style="background-image: url('{{ (Auth::user()->profile_image) ? asset('images/user_profiles/'.Auth::user()->username.Auth::user()->id.'/'.Auth::user()->profile_image.'') : asset('images/user_profiles/avatar.svg') }}');">
                            </span>                            

                            <div class="offset">
                                <button type="button" class="btn-change-profile" title="Edit profile">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                        </div>
                        <div class="user-details">
                            <h5>{{ ucfirst(Crypt::decryptString(Auth::user()->first_name)). ' '.ucfirst(Crypt::decryptString(Auth::user()->last_name)) }}</h5>

                            <div class="status {{ (Auth::check()) ? 'status-online' : 'status-offline' }}">
                                <span class="circle">
                                    <i class="fas fa-circle"></i>
                                </span>
                                <span class="status-code">Online</span>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <a href="{{ route('my_account.index') }}" id="btn-account">
                        <i data-feather="user"></i>
                        <span>Personal Information</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('my_account.change_password') }}">
                        <i data-feather="lock"></i>
                        <span>Change Password</span>
                    </a>
                </li>
                <li>
                    <a href="">
                        <i data-feather="clock"></i>
                        <span>Activity Logs</span>
                    </a>
                </li>
            </ul>
        </div>

        @include('pages.my_account.general.general')
    </div>
</div>

<!-- <div class="modal" id="modal">
    <div class="modal-content" id="change-profile">
        <button class="dismiss-modal" title="close">
            <i class="fas fa-times"></i>    
        </button>

        <h5>Edit Photo</h5>
        <div class="divider"></div>

        <div class="row">
            <div class="profile-image">
                <img id="previewImg" src="{{ (Auth::user()->profile_image) ? asset('images/user_profiles/'.Auth::user()->username.Auth::user()->id.'/'.Auth::user()->profile_image.'') : asset('images/user_profiles/avatar.svg') }}" alt="preview profile">
            </div>

            <form action="{{ route('my_account.change_profile') }}" method="POST" class="image-form" id="image-form" enctype="multipart/form-data">
                @csrf
                <div class="input-group">

                    <input type="file" name="profile-image" id="profile-image" onchange="previewFile(this)" accept="image/*" style="visibility: hidden;">

                    <button type="button" class="btn-choose-photo">
                        <span>Upload a Different Photo</span>
                        <i data-feather="camera"></i>
                    </button>

                </div>

                <br>
                <div class="action-btn">
                    <button type="button" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-upload">Save</button>
                </div>
            </form>

        </div>
    </div>
</div> -->

<!-- The Modal -->
<form class="modal" action="" method="POST" id="form-submit">
    @csrf

    <div class="modal-content">
        <div class="modal-header">      
            <div class="modal-icon">
                <i data-feather="alert-triangle"></i>
            </div>

            <div class="modal-body">
                <h5>Remove Record</h5>
                <p>Are you sure you want to remove this record? This will be permanently removed. This action cannot be undone.</p>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" id="btn-cancel">Cancel</button>
            <button type="button" class="btn btn-danger" id="btn-remove">Remove</button>
        </div>
    </div>

</form>
<!-- Ends here -->

@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
    var profile = document.getElementById('change-profile');
    var modal = document.getElementById('form-submit');
    $('.action-btn').hide();

    $('.btn-change-profile').on('click', function(){
        profile.style.height = 'auto';
        modal.style.maxHeight = '100%';
    });

    $('.dismiss-modal').on('click', function(){
        modal.style.maxHeight = '0%';
    });

    $('.btn-cancel').on('click', function(){
        modal.style.maxHeight = '0%';
    });

    $(".btn-choose-photo").on('click', function(){
        $('#profile-image').trigger('click');
    });
});

function previewFile(input){
    var file = $("#profile-image").get(0).files[0];
    
    if(file){
        var reader = new FileReader();

        reader.onload = function(){
            $("#previewImg").attr("src", reader.result);
            $('.action-btn').show();
        }

        reader.readAsDataURL(file);
    }
}
</script>
@endsection