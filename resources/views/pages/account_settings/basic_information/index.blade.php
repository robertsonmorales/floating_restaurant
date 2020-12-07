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
    @include('pages.account_settings.basic_information.form')

</div>

@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
    $('#settings-form').on('submit', function(){
        $('#btn-save').prop('disabled', true);
        $('#btn-reset').prop('disabled', true);

        $('#btn-save').html('Saving Changes..');
        $(this).submit();
    });
});
</script>
@endsection