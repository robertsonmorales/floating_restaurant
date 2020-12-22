$(document).ready(function(){
	$("#btn-dropdown").on('click', function(event){
		var profile_dropdown = document.getElementById('admin-dropdown');

		if(profile_dropdown.style.maxHeight){
		    profile_dropdown.style.maxHeight = null;
		    profile_dropdown.style.opacity = 0;
		}else{ 
			var scrollHeight = profile_dropdown.scrollHeight - 16;
		    profile_dropdown.style.maxHeight = scrollHeight + "px";
		    profile_dropdown.style.opacity = 1;
		}
	});

	$('.btn-logout').on('click', function(){
		$("#logout-form").on('submit');
	});

	$('#btn-slide').on('click', function(){
		$('#sidebar').removeClass('d-none');
		$('#sidebar').addClass('slide-nav');
	});

	$('#btn-close').on('click', function(){
		$('#sidebar').addClass('d-none');
		$('#sidebar').removeClass('slide-nav');
	});
});