// JavaScript Document

$(document).ready(function () {


	$("body").niceScroll({
		hwacceleration: true,
		smoothscroll: true,
		cursorcolor: "white",
		cursorborder: 0,
		scrollspeed: 10,
		mousescrollstep: 20,
		cursoropacitymax: 0.7
		//		cursorwidth: 16

	});
	$.ajaxSetup({
		cache: false
	});
});




//***************** Functions ***************//


$(document).ready(function () {


	window.hidetxt = true;
	$("#control a").click(function () {
		hideShowForm();
	});


});


function toggleClass(el, className) {
    if (el.className.indexOf(className) >= 0) {
        el.className = el.className.replace(className,"");
    }
    else {
        el.className  += className;
    }
}

