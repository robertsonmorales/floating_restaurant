@extends('layouts.app')
@section('title', $title)

@section('content')
<center>
<div class="content" style="width: 45%;">
    <form action="{{ ($mode == 'update') ? 
        route('menu_categories.update', $data->id) : 
        route('menu_categories.store') }}"
        method="POST" class="card-form" id="card-form">
        @csrf

        <h5>{{ ucfirst($mode).' '.\Str::Singular($header) }}</h5>
        <div class="divider"></div>

        <div class="input-group">
            <label for="">Name</label>
            <input type="text" name="name" id="name" autocomplete="off"
                class="form-control @error('name') is-invalid @enderror" autofocus
                value="{{($mode == 'update') ? $data->name : old('name')}}">

            <span class="messages">
                <strong id="error-name"></strong>
            </span>

            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        
        <div class="input-group">
            <label for="">Status</label>
            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" autofocus>
                <option value="1" {{ ($mode == 'update' && $data->status == 1) ? 'selected' : '' }}>Active</option>
                <option value="0" {{ ($mode == 'update' && $data->status == 0) ? 'selected' : '' }}>In-active</option>
            </select>

            <span class="messages">
                <strong id="error-status"></strong>
            </span>

            @error('status')
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
            <button type="submit" class="btn btn-primary btn-submit">{{ ($mode == 'update') ? 'Update' : 'Submit'}}</button>
            <button type="reset" class="btn btn-info">Reset</button>            
            <a href="{{route('menu_categories.index')}}" class="btn btn-secondary">Back</a>
        </div>
    </form>
</div>
</center>
<br>
@endsection