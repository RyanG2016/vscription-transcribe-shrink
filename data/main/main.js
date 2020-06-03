// JavaScript Document

var g_fileName;

$(document).tooltip({
	//            track: true
	items: ':not(#report_ifr)'
	//	items: ':not(#report_ifr,#TypistName)'
});

$(document).ready(function () {


	getLatestAppVersionNumber(checkVersions);

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

	//appends an "active" class to .popup and .popup-content when the "Open" button is clicked
		$(".button-orange").on("click", function() {
			var fullAudioSrc = AblePlayerInstances[0].media.src;
			var tempAudioFileName = fullAudioSrc.split("/").pop();
			clearTempAudio(tempAudioFileName);
		});

		//removes the "active" class to .popup and .popup-content when the "Close" button is clicked
		$(".close").on("click", function() {
		  $(".popup-overlay, .popup-content").removeClass("active");
		});
		$('#loadBtn').on('click', function (e) {
			chooseJob();
		});
});

$(function () {
	$("#date").datepicker({
		showAnim: "clip",
		dateFormat: "dd/mm/yy"
	});
});

$(function () {
	$("#dateT").datepicker({
		showAnim: "clip",
		dateFormat: "dd/mm/yy"
	});
});


$("#jobNo").keypress(function () {
	document.title = $('#jobNo').val();
});

$(function () {
	$("#accord").accordion({
		collapsible: true,
		header: "h3" //,heightStyle: "fill"
			,
		active: false,
		activate: function () {
			$("body").getNiceScroll().resize();
		}
	});
});






//***************** Functions ***************//



function clearWithConfirm() {
	//	 var retVal = confirm("Clear Form ?");


	$.confirm({
		title: 'Discard Form?',
		content: 'Are you sure do you want to discard current data?',
		buttons: {
			confirm: {
				btnClass: 'btn-red',
				action: function () {

					//				$.alert('Confirmed!');
					clear();
					return true;
				}
			},
			cancel: function () {},
		}
	});

}

function clearAfterDownload(askCompletePlayer) {
	//	 var retVal = confirm("Clear Form ?");


	$.confirm({
		title: 'File Saved',
		type: 'green',
		content: 'Do you want to clear the form?',
		buttons: {
			confirm: {
				btnClass: 'btn-red',
				action: function () {

					//				$.alert('Confirmed!');
					clear();

					/*				if($('#completeBtn').hasClass('button-green') && askCompletePlayer)
								   	{
										askForCompletePlayer();
									}*/

					return true;
				}
			},
			cancel: function () {

				/*				if ($('#completeBtn').hasClass('button-green') && askCompletePlayer) {
									askForCompletePlayer();
								}*/

			},

		}
	});

	/*
	if(retVal === true){
		clear();
		return true;
	}
	else{
		return false;
	}*/
}

function askForCompletePlayer() {
	$.confirm({
		title: 'Unload Audio File?',
		content: 'Do you want to unload and complete the loaded audio file?',
		buttons: {
			confirm: {
				btnClass: 'btn-green',
				action: function () {

					completePlayer();
					return true;
				}
			},
			cancel: function () {},
			/*somethingElse: {
				text: 'Something else',
				btnClass: 'btn-blue',
				keys: ['enter', 'shift'],
				action: function(){
					$.alert('Something else?');
				}
			}*/
		}
	});
}

function clear() {
	document.getElementById("date").value = "";
	//		document.getElementById('dateT').value= "";
	document.getElementById('jobNo').value = "";
	//		document.getElementById('TypistName').value= "";
	document.getElementById('comments').value = "";
	document.getElementById('jobType').value = "";
	document.getElementById('authorName').value = "";
	document.getElementById('report').value = "";
	$('#date').garlic('destroy');
	//		$( '#dateT' ).garlic( 'destroy' );
	$('#jobType').garlic('destroy');
	$('#jobNo').garlic('destroy');
	//		$( '#TypistName' ).garlic( 'destroy' );
	$('#comments').garlic('destroy');
	$('#authorName').garlic('destroy');
	$('#report').garlic('destroy');
	document.title = 'Form';
	tinyMCE.activeEditor.setContent('');


	completePlayer();

	//clearing validation
	$('.validate-form input').each(function () {
		//        $(this).focus(function(){
		hideValidate(this);
		//       });
	});


}

$(document).ready(function () {


	var $loadBtn = $('#loadBtn');
	var $completeBtn = $('#completeBtn');

	////////// Complete Button click \\\\\\\\\\\\\\
	$("#completeBtn").click(function () {


		var tinymceContent = tinymce.get('report').getContent();
		//check for any text in the text area
		if (!tinymceContent == "") //if not empty check saving
		{
			//override complete == true
			validateForm(true);
		} else { //empty text area just complete the file
			console.log("There is no text there to save.");
			//completePlayer(); //OK
		}


	});

	$("#loadBtn").click(function () {
		// Complete Button click
		performClick('fileLoadDiag');

	});

	//	$('#mainlogo-td').css("width","100%");

	window.hidetxt = true;
	$("#control a").click(function () {
		hideShowForm();
	});


});

function completePlayer() {
	var $loadBtn = $('#loadBtn');
	var $completeBtn = $('#completeBtn');
	//Delete Temp Audio File
	var fullAudioSrc = AblePlayerInstances[0].media.src;
	var tempAudioFileName = fullAudioSrc.split("/").pop();
	clearTempAudio(tempAudioFileName);

	try {
		vScriptCallback("AudioComplete", g_fileName); //For vScription Application to move file to complete folder
	} catch (err) {}

	//$('#fileLoadDiag').val(''); //clear file dialog
	AblePlayerInstances[0].media.pause();
	AblePlayerInstances[0].media.load();

	setTimeout(function () {
		AblePlayerInstances[0].media.removeAttribute('src');
		AblePlayerInstances[0].media.load();
	}, 300);

	$loadBtn.removeClass('noHover');
	$('#loadBtn').html('<i class="fas fa-cloud-upload-alt"></i> Load');
	$loadBtn.find("i").show();
	$completeBtn.addClass('noHover');
	$completeBtn.addClass('button');
	$completeBtn.removeClass('button-green');
}

function hideShowForm() {
	if (window.hidetxt) {
		window.hidetxt = false;
		//hide text area
		$('#audio-td').attr('align', 'center');
		//$('#help-td').attr('align','center');
		$('#fix-td').css('display', 'none');
		$('#demo-td legend').css('display', 'none');
		$('#mainlogo-td').css('display', 'none');

		//$('fieldset').slideUp();
		$('fieldset').slideUp(400, function () {
			setTimeout(function () {
				vScriptCallback("hide", document.getElementsByClassName('form-style-5').item(0).offsetHeight, document.getElementsByClassName('form-style-5').item(0).offsetWidth);
			}, 1200);
			$('.form-style-5').css('min-width', '500px');
			$('.form-style-5').css('width', '500px');
			//$('.form-style-5').css('max-width','500px');

		});
		$('#saveBtn').css('display', 'none');
		$('.button-red').css('display', 'none');
		$("#control a").html('Show Text Area');
	} else // show text area
	{
		window.hidetxt = true;
		$('#audio-td').attr('align', 'right');
		$('#help-td').attr('align', 'right');
		$('#fix-td').css('display', '');
		$('#demo-td legend').css('display', '');
		$('#mainlogo-td').css('display', '');
		$('fieldset').slideDown(400);
		$('.form-style-5').css('width', '90%');
		$('.form-style-5').css('min-width', '860px');
		$('#saveBtn').css('display', '');
		$('.button-red').css('display', '');
		$("#control a").html('Hide Text Area');

		// Need to set timeout as we need to wait for the slideDown method to run before we get the correct values
		vScriptCallback("show", document.getElementsByClassName('form-style-5').item(0).offsetHeight, document.getElementsByClassName('form-style-5').item(0).offsetWidth);
	}

}

function performClick(elemId) {
	var elem = document.getElementById(elemId);
	if (elem && document.createEvent) {
		var evt = document.createEvent("MouseEvents");
		evt.initEvent("click", true, false);
		elem.dispatchEvent(evt);
	}
}

/*-----Open dialog for typist to choose job---*/

function chooseJob() {
	getTransJobList(addRowHandlers);
	// Show file load box
	$(".popup-overlay, .popup-content").addClass("active");
}


/*----Lookup job details-----*/

function jobLoadLookup(jobNum) {
		console.log('Getting Transcription Job Details...');
		var jobDetailsResult = $('.table_data'); //populating fields

	var a1 = {
		job_id: jobNum
	};
		$.post("data/parts/backend_search.php", {
			reqcode: 7,
			args: JSON.stringify(a1)
		}).done(function (data) {
			loadIntoPlayer(data);
		});

}
/*-----LOAD FROM SERVER VERSUS LOCAL----*/
// Loading Audio File and details
function loadIntoPlayer(data) {
	var jobDetails = JSON.parse(data);
	console.log(`Job Number: ${jobDetails.job_id}`);
	console.log(`Author: ${jobDetails.file_author}`);
	console.log(`Filename: ${jobDetails.origFilename}`);
	console.log(`Temp filename is: ${jobDetails.tempFilename}`);
	console.log(`Dictated Date: ${jobDetails.file_date_dict}`);
	console.log(`Work Type: ${jobDetails.file_work_type}`);
	console.log(`Speaker Type: ${jobDetails.file_speaker_type}`);
	console.log(`Upload Comments: ${jobDetails.file_comment}`);

	$('.job').val(jobDetails.job_id);
	$('#authorName').val(jobDetails.file_author);
	$('#jobType').val(jobDetails.file_work_type);
	$('#date').val(jobDetails.file_date_dict);
	$('#comments').val(jobDetails.file_comment);

	var $loadBtn = $('#loadBtn');
	var $completeBtn = $('#completeBtn');
	//g_fileName = fileName;
	var audioTempFolder = "http://vscriptiontranscribeupload.local:8888/workingTemp/"
	AblePlayerInstances[0].media.src = audioTempFolder + jobDetails.tempFilename;
	$loadBtn.addClass('noHover');
	$loadBtn.text(jobDetails.job_id + ' Loaded');
	$loadBtn.find("i").hide();
	var playPromise = AblePlayerInstances[0].media.play();

	if (playPromise !== undefined) {
		playPromise.then(_ => {
				// Automatic playback started!
				// Show playing UI.
				AblePlayerInstances[0].media.pause();
				AblePlayerInstances[0].seekTo(0);
			})
			.catch(error => {
				// Auto-play was prevented
				// Show paused UI.
			});
	}
};
/*----END LOAD FROM SERVER -----*/



function validateForm(override) {

	var jobID = $('input[name="jobNo"]');
	var authorName = $('input[name="authorName"]');
	var TypistName = $('input[name="TypistName"]');

	// Row 2
	var jobType = $('input[name="jobType"]');
	var DateDic = $('input[name="DateDic"]');
	var DateTra = $('input[name="DateTra"]');

	var check = true;

	if (jobID.val().trim() == '') {
		showValidate(jobID);
		check = false;
	}

	if (authorName.val().trim() == '') {
		showValidate(authorName);
		check = false;
	}

	if (TypistName.val().trim() == '') {
		showValidate(TypistName);
		check = false;
	}

	if (jobType.val().trim() == '') {
		showValidate(jobType);
		check = false;
	}

	if (DateDic.val().trim() == '') {
		showValidate(DateDic);
		check = false;
	}

	if (DateTra.val().trim() == '') {
		showValidate(DateTra);
		check = false;
	}


	//        return check;
	if (check) {

		document.getElementById('form').submit();

			completePlayer();
			clear();


	} else {
		$(window).scrollTop(0);
		$.alert({

			title: 'Error!',
			type: 'red',
			content: 'Please fill in all required form fields.',
		});
	}
	//    });

	return check;
}

$('.validate-form input').each(function () {
	$(this).focus(function () {
		hideValidate(this);
	});
});


function showValidate(input) {
	var thisAlert = $(input); //.parent();

	thisAlert.addClass('alert-validate');
}

function hideValidate(input) {
	var thisAlert = $(input);
	thisAlert.removeClass('alert-validate');
}

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
};

function getTransJobList(callback) {

		console.log('Getting Transcription Job List...');
		const maximum_rows_per_page_jobs_list = 7;
		var jobListResult = $('.table_data'); //populating fields

		$.post("data/parts/backend_search.php", {
			reqcode: 9
		}).done(function (data) {
			jobListResult.html(data);

		});

		setTimeout(function() {
			callback();
		}, 1000);
	}
function addRowHandlers() {
	console.log("Calling addRowHandler");
  var table = document.getElementById("translist");
  var rows = table.getElementsByTagName("tr");
  for (i = 0; i < rows.length; i++) {
    var currentRow = table.rows[i];
    var createClickHandler = function(row) {
      return function() {
        var cell = row.getElementsByTagName("td")[0];
        var id = cell.innerHTML;
		  jobLoadLookup(id);
		  $(".popup-overlay, .popup-content").removeClass("active");
        //alert("id:" + id);
      };
    };
    currentRow.ondblclick = createClickHandler(currentRow);
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

/*----Lookup job details-----*/

function clearTempAudio(tempFileName) {
		console.log('Clearing temp audio file');

	var a1 = {
		job_id: tempFileName
	};
		$.post("data/parts/backend_search.php", {
			reqcode: 33,
			args: JSON.stringify(a1)
		}).done(function () {
			//alert(data);
		});

}




