@extends('layouts.app')
@section('title', $title)

@section('content')
<div class="filters">
    <div class="filters-child">
        <a href="{{ route('product_units.create') }}" class="btn btn-primary" id="btn-add-record">{{ $add }}</a>
        <a href="#" class="btn btn-primary" id="btn-export">
            <span>Export</span>
            <span class="download-icon"><i data-feather="download"></i></span>
        </a>
    </div>
    <div class="filters-child">
        <div class="form-group">
            <span class="search-icon">
                <i data-feather="search"></i>
            </span>
            <input type="text" name="search-filter" id="search-filter" placeholder="Search here..">
        </div>
        <select name="sortBy" id="sortBy">
            <option disabled>Sort by</option>
            <option value="ascending">Ascending</option>
            <option value="descending">Descending</option>
            <option value="date-created">Date created</option>
            <option value="date-modified">Date modified</option>
        </select>
        <select name="pageSize" id="pageSize">
            <option disabled>Page size</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
    </div>
</div>

<!-- alert -->
@if(session()->get('success'))
<div class="alert alert-success alert-dismissible fade show alerts" role="alert">
    <span><i data-feather="check"></i> {{ session()->get('success') }}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true" class="dismiss-icon"><i data-feather="x"></i> </span>
    </button>
</div>
@endif
<!-- ends here -->

<div class="content">
    <div id="myGrid" class="ag-theme-material"></div>
</div>

<!-- The Modal -->
<form class="modal" action="" method="POST" id="form-submit">
    @csrf
    @method('DELETE')

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
            <button type="button" class="btn btn-danger" id="btn-remove">Remove</button>
            <button type="button" class="btn btn-outline-secondary" id="btn-cancel">Cancel</button>
        </div>
    </div>

</form>
<!-- Ends here -->

<br>
@endsection
@section('scripts')
<script>
$(document).ready(function(){
    var data = <?= $data ?>;

    // assign agGrid to a variable
    var gridDiv = document.querySelector('#myGrid');

    var columnDefs = [];
    columnDefs = {
        headerName: 'Controls',
        field: 'Controls',
        sortable: false,
        filter: false,
        // width: 150,
        flex: 1,
        pinned: 'left',
        cellRenderer: function(params){
            // EDIT
            var edit_url = '{{ route("product_units.edit", ":id") }}';
            edit_url = edit_url.replace(':id', params.data.id);

            var eDiv = document.createElement('div');
            eDiv.innerHTML = '';
            eDiv.innerHTML+='<button id="'+params.data.id+'" title="Edit" class="btn btn-primary btn-edit"><i class="far fa-edit"></i></button>&nbsp;';
            eDiv.innerHTML+='<button id="'+params.data.id+'" title="Delete" class="btn btn-primary btn-remove"><i class="far fa-trash-alt"></i></button>&nbsp;';

            var btn_edit = eDiv.querySelectorAll('.btn-edit')[0];
            var btn_remove = eDiv.querySelectorAll('.btn-remove')[0];

            btn_edit.addEventListener('click', function() {
                window.location.href = edit_url;
            });

            btn_remove.addEventListener('click', function() {
                var data_id = $(this).attr("id");
                $('.modal').attr('style', 'display: flex;');
                $('.modal-content').attr('id', params.data.id);
            });
            
            return eDiv;
        }
    }

    for (var i = data.column.length - 1; i >= 0; i--) {
        if (data.column[i].field == "status") {
            data.column[i].cellRenderer = function display(params) {
                if (params.data.status == "Active") {
                    return '<span class="status active-status">' + params.data.status + '</span>';
                }else{
                    return '<span class="status inactive-status">' + params.data.status + '</span>';
                }
            }
        }
    }

    data.column.push(columnDefs);

    var gridOptions = {
        // sortingOrder: ['desc', 'asc', null],
        columnDefs: data.column,
        rowData: data.rows,
        groupSelectsChildren: true,
        suppressRowTransform: true,
        rowHeight: 50,
        animateRows: true,
        pagination: true,
        paginationPageSize: 10,
        pivotPanelShow: "always",
        colResizeDefault: "shift",
        rowSelection: "multiple",
        rowStyle: { 
            fontFamily: ['Poppins', 'Montserrat', 'sans-serif'],
            fontWeight: 'normal',
            fontSize: '1em',
            color: '#777'
        },
        onGridReady: function () {
            autoSizeAll();
            // gridOptions.api.sizeColumnsToFit();
        }
    }

    function autoSizeAll(skipHeader) {
        var allColumnIds = [];
        gridOptions.columnApi.getAllColumns().forEach(function(column) {
            allColumnIds.push(column.colId);
        });

        gridOptions.columnApi.autoSizeColumns(allColumnIds, skipHeader);
    }

    // export as csv
    $('#btn-export').on('click', function(){
        gridOptions.api.exportDataAsCsv();
    });

    function search(data) {
      gridOptions.api.setQuickFilter(data);
    }

    $("#search-filter").on("keyup", function() {
      search($(this).val());
    });

    // change page size
    function pageSize(value){
        gridOptions.api.paginationSetPageSize(value);
    }

    // SORT 
    $("#sortBy").on('change', function(){      
        if ($(this).val() == "ascending") {
            gridOptions.columnApi.applyColumnState({
              state: [{ colId: 'name', sort: 'asc' }],
              defaultState: { sort: null },
            });
        }else if($(this).val() == "descending"){
            gridOptions.columnApi.applyColumnState({
              state: [{ colId: 'name', sort: 'desc' }],
              defaultState: { sort: null },
            });
        }else if($(this).val() == "date-created"){
            alert('under construction');
        }else if($(this).val() == "date-modified"){
            alert('under construction');
        }
    });
    // ENDS HERE

    // PAGE SIZE
    $("#pageSize").change(function(){
        var size = $(this).val();
        // console.log(size);
        pageSize(size);
    });

    // .select2({
    //     minimumResultsForSearch: Infinity
    // });
    // ENDS HERE

    // setup the grid after the page has finished loading
    new agGrid.Grid(gridDiv, gridOptions);

    $('#btn-cancel').on('click', function(){
        $('#form-submit').hide();
    });

    // window.onclick = function(event) {
    //     if (event.target == $('.modal')[0]) {
    //         $('#form-submit').hide();
    //     }
    // }

    $('#btn-remove').on('click', function(){
        var destroy = '{{ route("product_units.destroy", ":id") }}';
        url = destroy.replace(':id', $('.modal-content').attr('id'));

        $('#btn-cancel').prop('disabled', true);
        $('#btn-remove').prop('disabled', true);
        $('#btn-remove').html("Removing..");

        document.getElementById("form-submit").action = url;
        document.getElementById("form-submit").submit();
    });
});
</script>
@endsection