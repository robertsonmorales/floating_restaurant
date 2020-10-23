@extends('layouts.app')
@section('title', $title)

@section('content')
<div class="row mx-2 my-4 align-items-start">
	<div class="col-2 d-flex flex-column ml-2">		

		@foreach($menu_categories as $categories)
		@if($categories->upload_type != null)

		<button type="button" class="btn btn-primary btn-cart text-left mb-2 px-3 py-4 radius bg-cover" id="{{ $categories->id }}" style="background: linear-gradient(45deg, rgba(0,0,0,0.6), rgba(0,0,0,0.3), rgba(0,0,0,0.6)), url('{{ ($categories->upload_type == '0|URL') ? $categories->category_image : asset('images/menu_categories/'.$categories->category_image) }}');">
			<!-- <i class="{{ $categories->category_icon }}"></i> -->
			<span>{{ $categories->name }}</span>
		</button>

		@else
		<button type="button" class="btn btn-primary btn-cart text-left mb-2 px-3 py-4 radius" id="{{ $categories->id }}">
			<!-- <i class="{{ $categories->category_icon }}"></i> -->
			<span>{{ $categories->name }}</span>
		</button>
		@endif
		@endforeach
	</div>
	<div class="col-6 d-flex flex-column mr-3">
		<div class="form-group form-control d-flex align-items-center search-text">
			<span>
				<i data-feather="search"></i>
			</span>
			<input type="text" name="search-bar" placeholder="Search here..." class="border-0 search-bar" maxlength="50">
		</div>

		<div class="row no-gutters menu-card">
			@foreach($paginator as $menu)
			<div class="card card-shadow">
			  <div class="row card-body align-items-start justify-content-between">
			  	<div class="col-6 d-flex justify-content-center" style="overflow: hidden;">
			  		@if($menu->upload_type == "0|URL")
			  		<img src="{{ $menu->menu_image }}" class="img-thumbnail border-0 radius" alt="no image found">
			  		@elseif($menu->upload_type == "1|File Upload")
			  		<img src="{{ asset('images/menus/'.$menu->menu_image) }}" class="img-thumbnail border-0 radius" alt="no image found">
			  		@endif
			  	</div>
			  	<div class="col">
			  		<div class="card-title title-size">{{ ucFirst($menu->name) }}</div>
			  		<div class="card-subtitle subtitle-size text-muted mt-1 mb-3">{{ "Php ".$menu->price.".00" }}</div>

			  		<div class="input-group mb-3">

			  			<button class="btn btn-primary rounded-circle d-flex align-items-center mr-1 fixed-size minus-qty" type="button" id="{{ $menu->id }}">
			  				<i data-feather="minus"></i>
			  			</button>

			  		  	<input type="text" name="qty" class="border text-center rounded-circle d-flex align-items-center fixed-size outline-0 input-qty" id="input-qty-{{ $menu->id }}" maxlength="3" value="1">

			  		  	<button class="btn btn-primary rounded-circle d-flex align-items-center ml-1 fixed-size add-qty" type="button" id="{{ $menu->id }}">
			  		  		<i data-feather="plus"></i>
			  		  	</button>

			  		</div>
			  		<a href="#" class="btn btn-primary btn-cart subtitle-size font-weight-500 add-to-cart" id="{{ $menu->id }}">
			  			<span class="mr-2">Add To Cart</span>
			  			<span><i data-feather="plus-circle"></i></span>
			  		</a>
			  	</div>

			  </div>
			</div>
		    @endforeach

			{{ $paginator->links() }}
	    </div>
	</div>

	<div class="col left-padding-0">
		<div class="d-flex flex-column mb-3 py-3 px-4 card-transaction">
			<button class="btn btn-primary btn-cart d-flex justify-content-center w-100 subtitle-size font-weight-500 mb-3">
				<span class="mr-2">
					<i data-feather="users"></i>
				</span>
				<span>Add New Customer</span>
			</button>

			<h5 class="d-flex justify-content-between align-items-center">
				<span>Order</span>
				<span id="no-items" class="text-blue font-weight-500"></span>
			</h5>

			<div id="order-list"></div>

		</div>

		<div class="d-flex flex-column py-3 px-4 card-transaction">
			<h5>Billing</h5>

			<ul class="list-group">
				<li class="list-group-item list-group-padding border-0 d-flex justify-content-between subtitle-size">
					<span>Initial Amount</span>
					<span class="text-blue">Php 720.00</span>
				</li>
				<li class="list-group-item list-group-padding border-0 d-flex justify-content-between subtitle-size">
					<span>Discount</span>
					<span class="text-blue">20%</span>
				</li>
				<li class="list-group-item list-group-padding border-0 d-flex justify-content-between subtitle-size">
					<span>Total Amount</span>
					<span class="text-blue">Php 576.00</span>
				</li>
				<li class="list-group-item list-group-padding border-0 mt-2">
					<button class="btn btn-success text-white d-flex justify-content-center w-100 subtitle-size font-weight-500">
						<span class="mr-2"><i data-feather="check"></i></span>
						<span>Pay Order</span>
					</button>
				</li>
			</ul>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
	var token = $("meta[name='csrf-token']").attr("content");

	$('.add-qty').on('click', function(){
		var id = $(this).attr('id');

		var qty = Number($('#input-qty-'+id).val());

		$('.minus-qty').prop('disabled', false);
		$('#input-qty-'+id).val(qty + 1);
	});

	$('.minus-qty').on('click', function(){
		var id = $(this).attr('id');

		var qty = Number($('#input-qty-'+id).val());
		if (qty <= 1) {
			$(this).prop('disabled', true);
		}else{
			$('#input-qty-'+id).val(qty - 1);
		}
		
	});

	$('.add-to-cart').on('click', function(){
		var id = $(this).attr('id');
		$.ajax({
			type: "POST",
			url: "{{ route('orders.store') }}",
			dataType: "json",
			data: {
				_token: token,
				menu_id: id,
				qty: $('#input-qty-'+id).val()
			},
			success: function (result, status, xhr){
				if(result.status == 200){
					refreshOrders();
				}
			},
			error: function (xhr, status, error) {
	        	alert(error);
	    	}
		});
	});

	function refreshOrders(){
		$("#order-list").html("");
		$.get("{{ route('orders.get_orders') }}", function(data, status){
			$('#no-items').html(data.length);

			var trash_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';

			for (var i = data.length - 1; i >= 0; i--) {
				var content = '<ul class="list-group mb-2 no-gutters">\
						<li class="list-group-item list-group-padding border-0 subtitle-size">'+data[i].menu_name+'</li>\
						<li class="list-group-item list-group-padding border-0 d-flex justify-content-between subtitle-size">\
							<span class="text-muted">Amount</span>\
							<span class="text-blue">Php '+ data[i].unit_price +'.00</span\
						</li>\
						<li class="list-group-item list-group-padding border-0 d-flex justify-content-between subtitle-size">\
							<span class="text-muted">Quantity</span>\
							<span class="text-blue">'+data[i].qty+'</span>\
						</li>\
						<li class="list-group-item list-group-padding border-0 d-flex justify-content-end">\
							<button class="btn btn-outline-danger btn-sm btn-remove-'+data[i].id+'" id="'+data[i].id+'" onclick="removeMenu('+data[i].id+')">'+trash_icon+'</button>\
						</li>\
					</ul>\
					<hr>';

				$("#order-list").append(content);
			}			
		});
	}

	function loadQty(){
		var qty = Number($('.input-qty').val());

		if (qty <= 1) {
			$('.minus-qty').prop('disabled', true);
		}
	}

	loadQty();
	refreshOrders();
});

function removeMenu(data){
	alert(data);
	// $('.btn-remove-'+data).parent().parent().remove();
}
</script>
@endsection