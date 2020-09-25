$(document).ready(function(){
	$("#btn-dropdown").click(function(event){
		event.stopPropagation();
		var profile_dropdown = document.getElementById('admin-dropdown');
		var arrow = document.getElementById('admin-arrow');

		if(profile_dropdown.style.maxHeight){
		    profile_dropdown.style.maxHeight = null;
		    // if (arrow.classList.contains('arrow-focus')) {
		    // 	arrow.classList.remove("arrow-focus");
		    // }
		}else{ 
		    profile_dropdown.style.maxHeight = profile_dropdown.scrollHeight + "px";
		    // arrow.classList.add("arrow-focus");
		}
	});

	$(document).click(function(){
		var profile_dropdown = document.getElementById('admin-dropdown');
		if (profile_dropdown.style.maxHeight) {
			profile_dropdown.style.maxHeight = null;
		}
	});

	$('.btn-logout').click(function(){
		$("#logout-form").submit();
	});

	$('.nav-list').click(function(){
		var maxHeight = $(this).parent().children()[1];

		if (maxHeight.style.maxHeight) {
			maxHeight.style.maxHeight = null;
			$(this).children()[1].classList.remove('dropdown-focus');
		}else{
			$(this).children()[1].classList.add('dropdown-focus');
			maxHeight.style.maxHeight = $(this).parent().children()[1].scrollHeight + "px";
		}
	});

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

	function startTime() {
	    var today = new Date();
	    var y = today.getFullYear();
	    var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
	    var month = months[today.getMonth()];

	    var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
	    var d = days[today.getDay()];
	    var date = today.getDate();

	    var h = today.getHours();
	    var ampm = (h >= 12) ? 'PM' : 'AM';
	    var m = today.getMinutes();
	    var s = today.getSeconds();
	    m = checkTime(m);
	    
	    document.getElementById('date-time').innerHTML = d + " " + month + " " + date + ", " + y + " | " + h + ":" + m + ampm;
	    var t = setTimeout(startTime, 500);
	}

	function checkTime(i) {
	    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
	    return i;
	}

	startTime();
	loadSidebar();
});