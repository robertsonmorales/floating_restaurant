@extends('layouts.app')
@section('title', $title)

@section('content')
<div class="m-4 d-flex align-items-start">
	<div class="d-flex flex-column">

		@foreach($menu_categories as $categories)
		@if($categories->upload_type != null)

		<button type="button" class="btn btn-primary btn-cart text-center mb-2 py-3 radius bg-cover font-weight-500 h5" id="{{ $categories->id }}" style="background: linear-gradient(45deg, rgba(0,0,0,0.6), rgba(0,0,0,0.3), rgba(0,0,0,0.6)), url('{{ ($categories->upload_type == '0|URL') ? $categories->category_image : asset('images/menu_categories/'.$categories->category_image) }}');">
			<!-- <i class="{{ $categories->category_icon }}"></i> -->
			<span>{{ $categories->name }}</span>
		</button>

		@else
		<button type="button" class="btn btn-primary btn-cart text-center mb-2 py-3 radius font-weight-500 h5" id="{{ $categories->id }}">
			<!-- <i class="{{ $categories->category_icon }}"></i> -->
			<span>{{ $categories->name }}</span>
		</button>
		@endif
		@endforeach
	</div>

	<div class="col-7 d-flex flex-column">
		<div class="form-group form-control d-flex align-items-center search-text">
			<span>
				<i data-feather="search"></i>
			</span>
			<input type="text" name="search-bar" placeholder="Search here..." class="border-0 search-bar" maxlength="50">
		</div>

		<div class="row no-gutters" id="menu-list">
			@foreach($paginator as $menu)
			<div class="card card-shadow mb-3">
			  <div class="row card-body align-items-center justify-content-between">
			  	<div class="col-6 d-flex justify-content-center" style="overflow: hidden;" title="{{ ucFirst($menu->name) }}">
			  		@if($menu->upload_type == "0|URL")
			  		<img src="{{ $menu->menu_image }}" class="img-thumbnail border-0 radius" alt="{{ ucFirst($menu->name) }}">
			  		@elseif($menu->upload_type == "1|File Upload")
			  		<img src="{{ asset('images/menus/'.$menu->menu_image) }}" class="img-thumbnail border-0 radius" alt="{{ ucFirst($menu->name) }}">
			  		@endif
			  	</div>
			  	<div class="col">
			  		<div class="badge badge-pill {{ ($menu['menu_categories_id']['1'] == null) ? 'badge-warning' : '' }} font-weight-500 text-white" style="background-color: {{ $menu['menu_categories_id']['1'] }};">{{ $menu['menu_categories_id']['0'] }}</div>
			  		<div class="card-title title-size d-flex align-items-center justify-content-start">{{ ucFirst($menu->name) }}</div>
			  		<div class="card-subtitle font-weight-500 text-muted mt-1 mb-3">{{ "₱".$menu->price }}</div>

			  		<div class="input-group mb-3">

			  			<button class="btn btn-primary rounded-circle d-flex align-items-center mr-1 fixed-size minus-qty" type="button" id="{{ $menu->id }}">
			  				<i data-feather="minus"></i>
			  			</button>

			  		  	<input type="text" name="qty" class="border text-center rounded-circle d-flex align-items-center fixed-size outline-0 input-qty" id="input-qty-{{ $menu->id }}" maxlength="3" value="1">

			  		  	<button class="btn btn-primary rounded-circle d-flex align-items-center ml-1 fixed-size add-qty" type="button" id="{{ $menu->id }}">
			  		  		<i data-feather="plus"></i>
			  		  	</button>

			  		</div>
			  		<button class="btn btn-primary btn-cart rounded subtitle-size font-weight-500 add-to-cart add-to-cart-{{ $menu->id }}" id="{{ $menu->id }}">
			  			<span class="mr-1" id="cart-{{ $menu->id }}">Add To Cart</span>
			  			<span><i data-feather="plus-circle"></i></span>
			  		</button>
			  	</div>

			  </div>
			</div>
		    @endforeach
	    </div>

	    {{ $paginator->links() }}
	</div>

	<div class="d-flex">
		<div class="p-4 position-fixed card-transaction">
			<div class="d-flex justify-content-start align-items-center">
				<span class="h5 mb-0">Ordered Items</span>
				<span id="no-items" class="badge badge-pill badge-danger ml-2">{{ $orderedMenuCount }}</span>
			</div>
			<div class="row no-gutters mb-2">
				<span class="text-muted font-weight-500" style="font-size: .7em;">Transaction No: #{{ sprintf("%06d", $transaction_no) }}</span>
			</div>

			<div class="row no-gutters mb-2">
				<button class="btn btn-primary btn-cart d-flex justify-content-center align-items-center mr-2">
					<span class="mr-1">
						<i data-feather="users"></i>
					</span>
					<span class="subtitle-size font-weight-500">Add New Customer</span>
				</button>

				<div class="btn-group">
				  <button class="btn btn-outline-light btn-dropdown text-secondary d-flex justify-content-center align-items-center rounded" data-toggle="dropdown" title="More options">
				  	<span>
				  		<i data-feather="more-horizontal"></i>
				  	</span>
				  </button>

				  <div class="dropdown-menu dropdown-menu-right mt-2 py-2">
				    <button class="dropdown-item py-2" type="button">
				    	<span class="mr-1"><i data-feather="settings"></i></span>
				    	<span class="subtitle-size">Add Discount</span>
				    </button>
				    <button class="dropdown-item py-2" type="button">
				    	<span class="mr-1"><i data-feather="user-check"></i></span>
				    	<span class="subtitle-size">Customer Assignment</span>
				    </button>
				  </div>
				</div>

			</div>

			<hr>

			<ul class="list-group no-gutters" id="order-list">
				@if($orderedMenuCount <= 0)
				<div class="row no-gutters justify-content-start text-muted mt-2" id="no-orders">
					<span class="h5">No Orders Yet.</span>
				</div>
				@endif

				@foreach($orderedMenus as $orders)
				<li class="list-group-item list-group-padding border-0 d-flex align-items-center justify-content-between py-2 btn" title="{{ ucFirst($orders->menu_name) }}" style="border-top: 1px solid #f3f3f3 !important;">
					<div class="row no-gutters align-items-center">
						<div class="d-flex justify-content-center" style="overflow: hidden;">
							@if($orders->upload_type == "0|URL")
							<img src="{{ $orders->menu_image }}" class="img-thumbnail" alt="{{ $orders->menu_name }}" width="50">
							@elseif($orders->upload_type == "1|File Upload")
							<img src="{{ asset('images/menus/'.$orders->menu_image) }}" class="img-thumbnail" alt="{{ $orders->menu_name }}" width="50">
							@endif
						</div>
						<div class="d-flex flex-column ml-2">
							<span class="subtitle-size font-weight-500 mb-0 ellipsis">{{ ucFirst($orders->menu_name) }}</span>
							<span class="text-muted font-weight-500 text-left" style="font-size: .7em;">Quantity: {{ $orders->qty }}</span>
						</div>
					</div>
					<div class="row no-gutters align-items-center">
						<span class="mb-0 text-secondary font-weight-500">₱{{ $orders->total_price }}</span>
					</div>
				</li>
				@endforeach
			</ul>

			<hr>

			<ul class="list-group">
				<li class="list-group-item list-group-padding border-0 d-flex justify-content-between subtitle-size font-weight-500 text-secondary">
					<span>Initial Amount</span>
					<span id="initial">₱{{ $orderedMenuTotal }}</span>
				</li>
				<li class="list-group-item list-group-padding border-0 d-flex justify-content-between subtitle-size font-weight-500 text-secondary">
					<span>Discount</span>
					<span>0%</span>
				</li>
				<li class="list-group-item list-group-padding border-0 d-flex justify-content-between subtitle-size font-weight-500 text-secondary">
					<span>Total Amount</span>
					<span>₱{{ $orderedMenuTotal }}</span>
				</li>
				<li class="list-group-item list-group-padding border-0 mt-2">
					<button id="btn-process-order" class="btn btn-success text-white d-flex justify-content-center w-100 subtitle-size font-weight-500">
						<span class="mr-2 btn-process-icon"><i data-feather="check" id="check-icon"></i></span>
						<span class="btn-process-child">Place Order</span>
					</button>
				</li>
			</ul>
		</div>		
	</div>
</div>

<!-- The Modal -->
<div class="modal" id="modal-warning">
    <div class="modal-content">
        <div class="modal-header">      
            <div class="modal-icon">
                <i data-feather="alert-triangle"></i>
            </div>

            <div class="modal-body">
                <h5></h5>
                <p></p>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-warning" id="btn-okie">Okay, I got it!</button>
        </div>
    </div>
</form>
<!-- Ends here -->
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
		var trash_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';

		$.ajax({
			type: "POST",
			url: "{{ route('orders.store') }}",
			dataType: "json",
			data: {
				_token: token,
				menu_id: id,
				qty: $('#input-qty-'+id).val()
			},
			beforeSend: function(xhr){
				$('.add-to-cart-'+id).prop('disabled', true);
				$('#cart-'+id).html('Adding To Cart..');
			},
			success: function (result, status, xhr){
				$('.add-to-cart-'+id).prop('disabled', false);
				$('#cart-'+id).html('Add To Cart');

				if(result.status == 200){
					var data = result.data;
					var menuName = data.ordered_menu.menu_name;
					var newMenuName = menuName.charAt(0).toUpperCase() + menuName.substr(1);
					var qty = data.ordered_menu.qty;
					var totalPrice = data.ordered_menu.total_price;
					var path = "{{ asset('images/menus/') }}";
					var image = (data.menu.upload_type == "0|URL") ? data.menu.menu_image : path + '/' + data.menu.menu_image;

					var content = '<li class="list-group-item list-group-padding border-0 d-flex align-items-center justify-content-between py-2 btn" title="'+newMenuName+'" style="border-top: 1px solid #f3f3f3 !important;">\
							<div class="row no-gutters align-items-center">\
								<div class="d-flex justify-content-center" style="overflow: hidden;">\
									<img src="'+image+'" alt="'+newMenuName+'" class="img-thumbnail" width="50">\
								</div>\
								<div class="d-flex flex-column ml-2">\
									<span class="subtitle-size font-weight-500 mb-0 ellipsis">'+newMenuName+'</span>\
									<span class="text-muted font-weight-500 text-left" style="font-size: .7em;">Quantity: '+qty+'</span>\
								</div>\
							</div>\
							<div class="row no-gutters align-items-center">\
								<span class="mb-0 text-secondary font-weight-500">₱'+totalPrice+'</span>\
							</div>\
						</li>';
					$('#no-orders').hide();
					$("#order-list").prepend(content);
					$("#no-items").html(data.ordered_items);
					loadOrdersScrollBar();
				}

				if (result.status == 404) {
					$('#modal-warning').attr('style', 'display: flex;');
					$('#modal-warning .modal-icon').addClass('modal-icon-warning');
					$('#btn-okie').addClass('btn-warning');
					$('#modal-warning .modal-body h5').html(result.icon);
					$('#modal-warning .modal-body p').html(result.text);
				}
			},
			error: function (xhr, status, error) {
	        	alert(error);
	    	}
		});
	});

	// mouse events
	// $('.mouseEvent').on('mousedown', function(event) {
	//     switch (event.which) {
	//         case 1:
	//             alert('Left mouse button is pressed');
	//             break;
	//         case 2:
	//             alert('Middle mouse button is pressed');
	//             break;
	//         case 3:
	//             alert('Right mouse button is pressed');
	//             break;
	//         default:
	//             alert('Nothing');
	//     }
	// });

	$('#btn-okie').on('click', function(){
        $('.modal').hide();
    });

    $('.btn-remove').on('click', function(){
		var id = $(this).attr('id');
		$(this).parent().parent().remove();
	});

    $('#btn-process-order').on('click', function(){
    	$(this).attr('disabled', true);
    	$('.btn-process-child').html('Placing Order..');
    });

	function loadQty(){
		var qty = Number($('.input-qty').val());

		if (qty <= 1) {
			$('.minus-qty').prop('disabled', true);
		}
	}

	function loadOrdersScrollBar(){
		if($("#order-list").height() > 260){
		    $("#order-list").addClass('scrollbar-show');
		}
	}

	loadQty();
	loadOrdersScrollBar();
});

function removeMenu(data){
	alert(data);
	// $('.btn-remove-'+data).parent().parent().remove();
}
</script>
@endsection