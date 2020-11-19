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
<div class="alert alert-danger alert-dismissible fade show alerts mx-4 mb-3" role="alert">
    <span><i data-feather="x"></i> {{ session()->get('error') }} </span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true" class="dismiss-icon"><i data-feather="x"></i> </span>
    </button>
</div>
@endif

<div class="row no-gutters user-container align-items-start mx-4 mb-3">
    @include('pages.account_settings.sidebar')

    <div class="col-6 user-content">
        <h5>Delete Account</h5>

        <div class="input-group">
            <label for="">Once you delete your account, you will loose all data associated with it.</label>
        </div>

        <div class="actions">                        
            <button type="button" class="btn btn-danger" id="btn-delete">
                <span class="mr-1"><i data-feather="trash"></i></span>
                <span>Delete Account</span>
            </button>
        </div>
    </div>

</div>

<!-- The Modal -->
<form class="modal" form action="{{ route('account_settings.destroy', Auth::id()) }}" method="post" id="settings-form">
    @csrf
    @method('DELETE')

    <div class="modal-content">
        <div class="modal-header">      
            <div class="modal-icon modal-icon-error">
                <i data-feather="alert-triangle"></i>
            </div>

            <div class="modal-body">
                <h5>Delete Your Account</h5>
                <p>Are you sure? This action cannot be undone.</p>
            </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-danger" id="btn-remove">Yes Do It!</button>
            <button type="button" class="btn btn-outline-secondary" id="btn-cancel">Cancel</button>
        </div>
    </div>

</form>
<!-- Ends here -->
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
    $("#btn-delete").on('click', function(){
        $('.modal').attr('style', 'display: flex;');
    });

    $('#btn-cancel').on('click', function(){
        $('.modal').hide();
    });

    $('#settings-form').on('submit', function(){
        $('#btn-remove').prop('disabled', true);
        $('#btn-remove').html("Deleting Account..");
    });
});
</script>
@endsection