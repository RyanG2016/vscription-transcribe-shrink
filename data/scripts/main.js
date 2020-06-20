// JavaScript Document

$(document).ready(function () {


	// getLatestAppVersionNumber(checkVersions);

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
	checkBrowser();
});




//***************** Functions ***************//


$(document).ready(function () {


	window.hidetxt = true;
	$("#control a").click(function () {
		hideShowForm();
	});


});




function checkBrowser(updateAvailable) {
	var sUsrAg = navigator.userAgent;
	//alert(sUsrAg);
	if (sUsrAg.indexOf("78.0.3904.70") > -1) {
		$("#message_bar").slideUp();
	} else {
		$("#message_bar").slideDown("normal", "easeInOutBack");
	}
	if (updateAvailable) {
		$("#updated_version_bar").slideDown("normal", "easeInOutBack");
	} else {
		$("updated_version_bar").slideUp();
	}
}

function getLatestAppVersionNumber(_callback) {
	$.ajax({
		url: "https://www.vtexvsi.com/vscription/transcribe/LatestVersion.txt",
		success: function (result) {
			_callback(result.split("-")[0].trim(), checkBrowser);
		}
	});
}

function checkVersions(result, checkBrowser) {
	var latestAppVersion;
	latestAppVersion = result;
	var localAppVersion;
	try {
		localAppVersion = AppVersion;
	} catch (err) {
		localAppVersion = "";
	}
	if (latestAppVersion === localAppVersion || localAppVersion === "") {
		checkBrowser(0);
	} else {
		checkBrowser(1);
	}
}

function toggleClass(el, className) {
    if (el.className.indexOf(className) >= 0) {
        el.className = el.className.replace(className,"");
    }
    else {
        el.className  += className;
    }
}

