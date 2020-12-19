$(document).ready(function(){
	$("#btn-dropdown").click(function(event){
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

	$('.nav-list').on('click', function(){
		var navBar = $(this).parent().children()[1];
		if (navBar.style.maxHeight) {
			navBar.style.maxHeight = null;
			navBar.style.opacity = .2;
			$(this).children()[1].classList.remove('dropdown-focus');
		}else{
			navBar.style.maxHeight = $(this).parent().children()[1].scrollHeight + "px";
			navBar.style.opacity = 1;
			$(this).children()[1].classList.add('dropdown-focus');
		}
	});

	$('#btn-slide').on('click', function(){
		$('.sidebar').toggleClass('d-none');
		$('.sidebar').toggleClass('slide-nav');
	});

	$('#btn-close').on('click', function(){
		$('.sidebar').toggleClass('d-none');
		$('.sidebar').toggleClass('slide-nav');
	});

	// function checkViewport(){
	// 	if (window.matchMedia().matches) {
	// 		$('.sidebar').addClass('d-none');
	// 		$('.sidebar').removeClass('slide-nav');
	// 	}
	// }

	function loadSidebar(){
		var path = location.href;
		var path_mode = path.split("/").reverse();
		var checked_hash = path_mode[0].substr(path_mode[0].length - 1);
		var checked_mode = (checked_hash != "#")
			? path_mode[0]
			: path_mode[0].substring(0, path_mode[0].length - 1);
		var ifBtnSidebar = $("#"+checked_mode).parent().parent();
		if (ifBtnSidebar[0].className == "btn-sidebar") {
			$("#"+checked_mode).addClass('sidebar-focus');
		}else{
			var parent_nav = $("#"+checked_mode).parent().parent().parent().children()[0];
			var sub_nav = $("#"+checked_mode).parent().parent();
			$("#"+parent_nav.id).children()[1].classList.add('dropdown-focus');
			$("#"+checked_mode).addClass('sidebar-focus');
			sub_nav[0].style.maxHeight = sub_nav[0].scrollHeight + "px";
		}
	}

	// checkViewport();
	// loadSidebar();
});