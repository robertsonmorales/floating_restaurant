@extends('layouts.app')
@section('title', $title)

@section('content')

@if(session()->get('success'))
<div class="alert alert-success alert-dismissible fade show alerts mx-4 mb-3" role="alert">
    <span><i data-feather="check"></i> {{ session()->get('success') }}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true" class="dismiss-icon"><i data-feather="x"></i> </span>
    </button>
</div>
@endif

@if(session()->get('error'))
<div class="alert alert-danger alert-dismissible fade show alerts mx-4 mb-3" role="alert">
    <span><i data-feather="x"></i> {{ session()->get('error') }} </span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true" class="dismiss-icon"><i data-feather="x"></i> </span>
    </button>
</div>
@endif

<div class="row no-gutters user-container mx-4 mb-3">
    @include('pages.account_settings.sidebar')
    @include('pages.account_settings.password.form')
</div>

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
</script>
@endsection