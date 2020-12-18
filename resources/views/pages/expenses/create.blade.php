@extends('layouts.app')
@section('title', $title)

@section('content')
<form action="{{ ($mode == 'update') ? route('expenses.update', $data->id) : route('expenses.store') }}" method="POST" class="d-flex flex-column align-items-center" id="card-form">
    <div class="mb-4 card-form col-5">
        @csrf

        <h5>{{ ucfirst($mode).' '.\Str::Singular($header) }}</h5>

        <div class="input-group">
            <label for="">Expense Category</label>
            <select name="expense_category" id="expense_category" class="custom-select form-control @error('expense_category') is-invalid @enderror">
                @if($mode == 'create')
                <option value="" style="display: none;">Select a category...</option>
                @endif

                @foreach($expense_categories as $cat)
                <option value="{{ $cat->id }}" {{ ($mode == 'update' && $data->expense_categories_id == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <span class="messages" role="alert">
                <strong id="error-category"></strong>
            </span>

            @error('expense_category')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Name</label>
            <input type="text" name="name" id="name" autocomplete="off" class="form-control @error('name') is-invalid @enderror" value="{{($mode == 'update') ? $data->name : old('name')}}">

            <span class="messages" role="alert">
                <strong id="error-name"></strong>
            </span>

            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="">Cost</label>
            <input type="number" name="cost" id="cost" autocomplete="off" class="form-control @error('cost') is-invalid @enderror" value="{{($mode == 'update') ? $data->cost : old('cost')}}">

            <span class="messages" role="alert">
                <strong id="error-cost"></strong>
            </span>

            @error('cost')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        @if ($mode == 'update')
        @method('PUT')
        <input type="hidden" name="id" value="{{ ($mode == 'update') ? $data->id: ''}}">
        @endif
        
        <div class="actions">           
            <button type="submit" class="btn btn-primary btn-submit" id="btn-submit">{{ ($mode == 'update') ? 'Submit Changes' : 'Submit' }}</button>
            <button type="reset" class="btn btn-secondary" id="btn-reset">Reset</button>
            <button type="button" onclick="window.location.href='{{route('expenses.index') }}'" class="btn btn-secondary" id="btn-back">Back</button>
        </div>
    </div>
</form>
<br>
@endsection
@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
    $('#card-form').on('submit', function(){
        var mode = "{{ $mode }}";
        
        $('#btn-submit').prop('disabled', true);
        $('#btn-reset').prop('disabled', true);
        $('#btn-back').prop('disabled', true);

        $('#btn-submit').html((mode == "update") ? "Submitting Changes.." : "Submitting..");
        $(this).submit();
    });
});
</script>
@endsection