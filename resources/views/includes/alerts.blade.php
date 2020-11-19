<!-- alert -->
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
    <span><i data-feather="check"></i> {{ session()->get('error') }}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true" class="dismiss-icon"><i data-feather="x"></i> </span>
    </button>
</div>
@endif

@if(session()->get('import'))
<div class="alert alert-success alert-dismissible fade show alerts mx-4 mb-3" role="alert">
    <span><i data-feather="check"></i> {{ session()->get('import') }}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true" class="dismiss-icon"><i data-feather="x"></i> </span>
    </button>
</div>
@endif

@if(session()->get('import_failed'))
<div class="alert alert-danger alert-dismissible fade show alerts mx-4 mb-3" role="alert">
    <span><i data-feather="check"></i> {{ session()->get('import_failed') }}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true" class="dismiss-icon"><i data-feather="x"></i> </span>
    </button>
</div>
@endif
<!-- ends here -->