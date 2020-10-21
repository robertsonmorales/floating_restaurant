@extends('layouts.app')
@section('title', $title)

@section('content')
<div class="row mx-2 my-4 align-items-start">
	<div class="col-2 d-flex flex-column ml-2">		

		@foreach($menu_categories as $categories)
		@if($categories->upload_type != null)

		<button type="button" class="btn btn-primary btn-cart text-left font-weight-bold mb-2 p-3" id="{{ $categories->id }}" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.3), rgba(0,0,0,0.6)), url('{{ ($categories->upload_type == '0|URL') ? $categories->category_image : asset('images/menu_categories/'.$categories->category_image) }}'); background-size: cover; border-radius: .5rem;">
			<!-- <i class="{{ $categories->category_icon }}"></i> -->
			<span>{{ $categories->name }}</span>
		</button>

		@else
		<button type="button" class="btn btn-primary btn-cart text-left font-weight-bold mb-2 py-3" id="{{ $categories->id }}" style="border-radius: .5rem;">
			<!-- <i class="{{ $categories->category_icon }}"></i> -->
			<span>{{ $categories->name }}</span>
		</button>
		@endif
		@endforeach
	</div>
	<div class="col-6 d-flex flex-column mr-3">
		<div class="form-group form-control d-flex align-items-center">
			<span class="mr-2">
				<i data-feather="search"></i>
			</span>
			<input type="text" name="search-bar" placeholder="Search here..." class="border-0" style="outline: none; width: 100%;">
		</div>

		<div class="row no-gutters" style="display: grid; grid-template-columns: repeat(1, 1fr); grid-gap: 20px; {{ ($countMenus > 4) ? 'overflow-y: auto;' : '' }}  overflow-x: hidden;">
			@foreach($menus as $menu)
			<div class="card" style="box-shadow: 0 6px 12px rgba(140,152,164,.075); border: .0625rem solid rgba(231,234,243,.7); border-radius: .75rem;">
			  <div class="row card-body align-items-start justify-content-between">
			  	<div class="col-5 d-flex justify-content-center" style="overflow: hidden;">
			  		@if($menu->upload_type == "0|URL")
			  		<img src="{{ $menu->menu_image }}" width="150" class="class-img-top rounded" alt="no image found">
			  		@elseif($menu->upload_type == "1|File Upload")
			  		<img src="{{ asset('images/menus/'.$menu->menu_image) }}" width="150" class="class-img-top rounded" alt="no image found">
			  		@endif
			  	</div>
			  	<div class="col">
			  		<div class="card-title" style="margin-bottom: 0 !important;">{{ ucFirst($menu->name) }}</div>
			  		<div class="card-subtitle text-muted py-2 h6">{{ "Php ".$menu->price.".00" }}</div>

			  		<div class="input-group my-3">
			  			<div class="input-group-append">
							<button class="btn btn-primary border rounded-left" type="button">
								<i data-feather="minus"></i>
							</button>
			  			</div>

			  		  	<input type="text" name="qty" class="form-control border text-center font-weight-bold" maxlength="3" value="1">

			  		  	<div class="input-group-append">
			  		  		<button class="btn btn-primary border" type="button">
			  		  			<i data-feather="plus"></i>
			  		  		</button>
			  		  	</div>
			  		</div>
			  		<a href="#" class="btn btn-primary btn-cart font-weight-bold">
			  			<span class="mr-2">Add To Cart</span>
			  			<span><i data-feather="plus-circle"></i></span>
			  		</a>
			  	</div>

			  </div>
			</div>
		    @endforeach
	    </div>
	</div>
	<div class="col mr-4 p-3" style="box-shadow: 0 6px 12px rgba(140,152,164,.075); border: .0625rem solid rgba(231,234,243,.7); border-radius: .75rem;">
		<h5>Bill</h5>
		<hr>

		<ul class="list-group mb-2 no-gutters">
			<li class="list-group-item border-0 h6" style="padding: 5px 0;">Hawaiian Spare Ribs </li>
			<li class="list-group-item border-0 d-flex justify-content-between h6" style="padding: 5px 0;">
				<span>Amount</span>
				<span class="text-danger">Php 240.00</span>
			</li>
			<li class="list-group-item border-0 d-flex justify-content-between h6" style="padding: 5px 0;">
				<span>Quantity</span> 
				<span class="text-danger">2</span>
			</li>
		</ul>

		<hr>

		<ul class="list-group">
			<li class="list-group-item border-0 d-flex justify-content-between h6" style="padding: 5px 0;">
				<span>Total</span>
				<span class="text-danger font-weight-bold">Php 720.00</span>
			</li>
		</ul>
	</div>
</div>
@endsection