@extends('layouts.app')
@section('title', $title)

@section('content')
<!-- filter -->
<div class="filters">
    <div class="filters-child">
        <button class="btn btn-primary" id="btn-export">
            <span>Export</span>
            <span class="download-icon"><i data-feather="download"></i></span>
        </button>
    </div>
    <div class="filters-child">
        <div class="form-control search-group align-items-center">
            <span class="search-icon"><i data-feather="search"></i></span>
            <input type="text" name="search-filter" id="search-filter" placeholder="Search here..">
        </div>

        <select name="sortBy" id="sortBy" class="custom-select">
            <option style="display: none;">Sort by</option>
            <option disabled selected>Sort by</option>
            <option value="ascending">Ascending</option>
            <option value="descending">Descending</option>
            <option value="date-created">Date created</option>
            <option value="date-modified">Date modified</option>
        </select>

        <select name="pageSize" id="pageSize" class="custom-select">
            <option style="display: none;">Page size</option>
            <option disabled selected>Page size</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>

    </div>
</div>
<!-- ends here -->

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

    for (var i = data.column.length - 1; i >= 0; i--) {
        if (data.column[i].field == "stocks") {
            data.column[i].cellRenderer = function display(params) {
                var qty = params.data.stocks.split(" ");
                var minimum = params.data.minimum_stocks;

                if (qty[0] <= minimum) {
                    return '<span class="status is-below-minimum">' + qty[0] + ' ' + qty[1] + '</span>';
                }

                if(qty[0] > minimum){
                    return '<span class="status active-status">' + qty[0] + ' ' + qty[1] + '</span>';
                }

                console.log(qty[0] + ':' + minimum);
            }
        }
    }

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
});
</script>
@endsection