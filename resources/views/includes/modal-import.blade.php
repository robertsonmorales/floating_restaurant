<!-- The Import Modal -->
<form class="modal" action="" method="POST" id="import-form-submit" enctype="multipart/form-data">
    @csrf
    <div class="modal-content">
        <div class="modal-header">      
            <div class="modal-icon modal-icon-info">
                <i data-feather="alert-triangle"></i>
            </div>

            <div class="modal-body">
                <h5>Import Records</h5>
                
                <input type="file" name="import_file" id="import_file" class="form-control @error('import_file') is-invalid @enderror" accept=".xlsx, .xls, .csv">
                @error('import_file')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-info" id="btn-import-submit">Import File</button>
            <button type="button" class="btn btn-outline-secondary" id="btn-import-cancel">Cancel</button>
        </div>
    </div>
</form>
<!-- ends here -->