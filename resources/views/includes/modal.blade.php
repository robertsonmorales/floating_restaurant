<!-- The Modal -->
<form class="modal" action="" method="POST" id="form-submit">
    @csrf
    @method('DELETE')

    <div class="modal-content">
        <div class="modal-header">      
            <div class="modal-icon modal-icon-error">
                <i data-feather="alert-triangle"></i>
            </div>

            <div class="modal-body">
                <h5>Remove Record</h5>
                <p>Are you sure you want to remove this record? This will be permanently removed. This action cannot be undone.</p>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-danger" id="btn-remove">Remove</button>
            <button type="button" class="btn btn-outline-secondary" id="btn-cancel">Cancel</button>
        </div>
    </div>
</form>
<!-- Ends here -->