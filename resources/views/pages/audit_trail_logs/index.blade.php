@extends('layouts.app')
@section('title', $title)

@section('content')
<!-- filter -->
<div class="filters mx-4 mb-3">
    <div class="filters-child">
        <button class="btn btn-secondary" id="btn-export">
            <span>Export</span>
            <span class="download-icon"><i data-feather="download"></i></span>
        </button>
    </div>
    <div class="filters-child">
        <div class="form-control search-group align-items-center">
            <span class="search-icon"><i data-feather="search"></i></span>
            <input type="text" name="search-filter" id="search-filter" placeholder="Search here..">
        </div>

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

<div id="myGrid" class="ag-theme-material mx-4"></div>

<br>
@endsection
@section('scripts')
<script>
$(document).ready(function(){
    var data = <?= $data ?>;

    // assign agGrid to a variable
    var gridDiv = document.querySelector('#myGrid');

    for (var i = data.column.length - 1; i >= 0; i--) {
        if (data.column[i].field == "module") {
            data.column[i].cellRenderer = function display(params) {
                return '<div class="d-flex align-items-center">\
                            <div style="width:5px; height: 5px" class="bg-info rounded-circle mr-2"></div>\
                            ' + params.data.module + '\
                        <div>';
            }
        }

        if (data.column[i].field == "created_at") {
            data.column[i].cellRenderer = function display(params) {
                if (params.data.created_at) {
                    return getNewDateTime(params.data.created_at);
                }
            }
        }

        if (data.column[i].field == "updated_at") {
            data.column[i].cellRenderer = function display(params) {
                if (params.data.updated_at) {
                    return getNewDateTime(params.data.updated_at);
                }
            }
        }
    }

    function getNewDateTime(format){
        date = new Date(format); //'2013-08-0302:00:00Z'
        year = date.getFullYear();
        month = date.getMonth()+1;
        today = date.getDate();
        hours = date.getHours();
        minutes = date.getMinutes();
        seconds = date.getSeconds();

        if (month < 10) {month = '0' + month;}
        if (today < 10) {today = '0' + today;}
        if (hours < 10) {hours = '0' + hours;}
        if (minutes < 10) {minutes = '0' + minutes;}
        if (seconds < 10) {seconds = '0' + seconds;}
        return year + '-' + month + '-' + today + ' ' + hours + ':' + minutes + ':' + seconds;
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

    // PAGE SIZE
    $("#pageSize").change(function(){
        var size = $(this).val();
        // console.log(size);
        pageSize(size);
    });
    // ENDS HERE

    // setup the grid after the page has finished loading
    new agGrid.Grid(gridDiv, gridOptions);

    $('#btn-cancel').on('click', function(){
        $('#form-submit').hide();
    });

    $('#btn-remove').on('click', function(){
        var destroy = '{{ route("audit_trail_logs.destroy", ":id") }}';
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