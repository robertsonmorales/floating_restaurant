@extends('layouts.app')
@section('title', $title)

@section('content')
<div class="content">
    <div class="filters" style="justify-content: space-between;">
        <div class="add_link">
            <a href="{{ route('menu_categories.create') }}">{{ $add }}</a>
        </div>
        <div class="row">
            <div class="selections">
                <button class="btn-export">
                    <span>Export</span>
                    <span class="download">
                        <i data-feather="download"></i>
                    </span>
                </button>
            </div>
            <div class="selections">
                <p>Page Size:</p>
                <select name="pageSize" id="pageSize">
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>        
    </div>
    <div id="myGrid" class="ag-theme-material"></div>
    <form action="" method="POST" id="delform" style="display: none;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete</button>
    </form>
</div>
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
            var edit_url = '{{ route("menu_categories.edit", ":id") }}';
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
                
                // REMOVE
                var remove_url = '{{ route("menu_categories.destroy", ":id") }}';
                remove_url = remove_url.replace(':id', params.data.id);
                document.getElementById("delform").action = remove_url;
                Swal.fire({
                    title: 'Warning',
                    text: "Are you sure you want to remove this record?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#999',
                    confirmButtonText: '<span class="btn-swal">Yes Do It!</span>',
                    cancelButtonText: '<span class="btn-swal">No</span>'
                }).then((result) => {
                    if (result.value) {
                        document.getElementById('delform').submit();
                    }
                });
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
            fontFamily: ['Poppins', 'sans-serif'],
            fontWeight: 'normal',
            fontSize: '1em',
            color: '#777'
        },
        onGridReady: function () {
            autoSizeAll();
            // gridOptions.api.sizeColumnsToFit();
        }
    };

    function autoSizeAll(skipHeader) {
        var allColumnIds = [];
        gridOptions.columnApi.getAllColumns().forEach(function(column) {
            allColumnIds.push(column.colId);
        });

        gridOptions.columnApi.autoSizeColumns(allColumnIds, skipHeader);
    }

    // change page size
    function pageSize(value){
        gridOptions.api.paginationSetPageSize(value);
    }

    // $("#pageSize").change(function(){
    //     var size = $(this).val();
    //     pageSize(size);
    // }).select2({
    //     minimumResultsForSearch: Infinity
    // });
    // ends here

    // export as csv
    $('.btn-export').on('click', function(){
        gridOptions.api.exportDataAsCsv();
    });

    // setup the grid after the page has finished loading
    new agGrid.Grid(gridDiv, gridOptions);
});
</script>
@endsection