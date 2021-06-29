(function ($) {
	"use strict";
})(jQuery);

var accountsDT;
var accountsDTRef;
var createAcc;
var createAccModal;
var createAccForm;
var createAccBtn;
var updateAccBtn;
var modalHeaderTitle;
var closeAccModal;
var speakerTypesGlobal;
const SPEAKER_TYPES_URL = "../api/v1/speaker-types/?box_model";
const API_INSERT_URL = '../api/v1/accounts/';

const CREATE_ACC_HEADER = "<i class=\"fas fa-user-plus\"></i>&nbsp;Create New Account";
const UPDATE_ACC_HEADER = "<i class=\"fas fa-user-edit\"></i>&nbsp;Update Account";

/** Fields */
var accName;
var billableRadioGroup;
var enabledRadioGroup;
var acc_retention_time;
var act_log_retention_time;
// -- billing vars -- //
var br1;
var br2;
var br3;
var br4;
var br5;

var br1min;
var br2min;
var br3min;
var br4min;
var br5min;

var br1_TAT;
var br2_TAT;
var br3_TAT;
var br4_TAT;
var br5_TAT;

var br1type;
var br2type;
var br3type;
var br4type;
var br5type;

var br1desc;
var br2desc;
var br3desc;
var br4desc;
var br5desc;

var currentID;

$(document).ready(function () {

	const maximum_rows_per_page_jobs_list = 10;

	createAccModal = document.getElementById("modal");

	accountsDT = $("#accounts-tbl");
	createAcc = $("#createAcc");
	createAccForm = $("#createAccForm");
	createAccBtn = $("#createAccBtn");
	updateAccBtn = $("#updateAccBtn");
	modalHeaderTitle = $("#modalHeaderTitle");

	/** Fields */
	accName = $("#accName");
	billableRadioGroup = $(".billable-radios");
	enabledRadioGroup = $(".enabled-radios");
	acc_retention_time = $("#acc_retention_time");
	act_log_retention_time = $( "#act_log_retention_time" );

	br1 = $("#bill_rate1");
	br2 = $("#bill_rate2");
	br3 = $("#bill_rate3");
	br4 = $("#bill_rate4");
	br5 = $("#bill_rate5");

	br1min = $("#bill_rate1_min_pay");
	br2min = $("#bill_rate2_min_pay");
	br3min = $("#bill_rate3_min_pay");
	br4min = $("#bill_rate4_min_pay");
	br5min = $("#bill_rate5_min_pay");

	br1_TAT = $("#bill_rate1_TAT");
	br2_TAT = $("#bill_rate2_TAT");
	br3_TAT = $("#bill_rate3_TAT");
	br4_TAT = $("#bill_rate4_TAT");
	br5_TAT = $("#bill_rate5_TAT");

	br1type = $("#br1box");
	br2type = $("#br2box");
	br3type = $("#br3box");
	br4type = $("#br4box");
	br5type = $("#br5box");

	br1desc = $("#bill_rate1_desc");
	br2desc = $("#bill_rate2_desc");
	br3desc = $("#bill_rate3_desc");
	br4desc = $("#bill_rate4_desc");
	br5desc = $("#bill_rate5_desc");



	$(".modal").niceScroll({
		hwacceleration: true,
		smoothscroll: true,
		cursorcolor: "white",
		cursorborder: 0,
		scrollspeed: 10,
		mousescrollstep: 20,
		cursoropacitymax: 0.7
		//  cursorwidth: 16

	});

	// $("body").niceScroll({
	// 	hwacceleration: true,
	// 	smoothscroll: true,
	// 	cursorcolor: "white",
	// 	cursorborder: 0,
	// 	scrollspeed: 10,
	// 	mousescrollstep: 20,
	// 	cursoropacitymax: 0.7
	// 	//  cursorwidth: 16
	//
	// });

	$( ".radio-no-icon" ).checkboxradio({
		icon: false
	});

	$.ajaxSetup({
		cache: false
	});

	const refreshBtn = document.querySelector('#refresh_btn');
	// const goToUploader = document.querySelector('#newupload_btn');

	// Activate ripples effect for material buttons
	new mdc.ripple.MDCRipple(document.querySelector('#refresh_btn'));

	/*createAccForm.bind('submit', function (e) {
		e.preventDefault();
		createAccBtn.attr("disabled","disabled");

		var formData = new FormData(document.querySelector("#createAccForm"));
		// console.log(formData.entries().toString());
		// for (var pair of formData.entries()) {
		// 	console.log(pair[0]+ ', ' + pair[1]);
		// }
		if(validateForm())
		{
			// Insert to API
			// - on response -
			// if success - > reset the form
			// show status msg dialog
			// close the modal
			// refresh account table

			$.ajax({
				type: 'POST',
				url: API_INSERT_URL,
				data: formData,
				processData: false,
				contentType: false,
				success: function (response) {
					resetForm();
					accountsDTRef.ajax.reload(); // refresh accounts table
					createAccModal.style.display = "none";

					$.confirm({
						title: 'Success',
						content: response["msg"],
						buttons: {
							confirm: {
								btnClass: 'btn-green',
								action: function () {
									return true;
								}
							}
						}
					});


				},
				error: function (err) {
					$.confirm({
						title: 'Error',
						content: err.responseJSON["msg"],
						buttons: {
							confirm: {
								btnClass: 'btn-red',
								action: function () {
									createAccBtn.removeAttr("disabled");
									return true;
								}
							}
						}
					});
				}
			});

		}
		return false;
	});*/

	function goForm(update = false, id = null) {

		var formData = new FormData(document.querySelector("#createAccForm"));
		// console.log(formData.entries().toString());
		// for (var pair of formData.entries()) {
		// 	console.log(pair[0]+ ', ' + pair[1]);
		// }
		if(validateForm())
		{
			createAccBtn.attr("disabled","disabled");
			updateAccBtn.attr("disabled","disabled");

			// Insert to API
			// - on response -
			// if success - > reset the form
			// show status msg dialog
			// close the modal
			// refresh account table

			$.ajax({
				type: update?'PUT':'POST',
				url: update?API_INSERT_URL+id+"/":API_INSERT_URL,
				processData: false,
				// contentType: "application/x-www-form-urlencoded",
				data: convertToSearchParam(formData),
				// headers: update?{'Content-Type': 'application/x-www-form-urlencoded'}:{'Content-Type': "multipart/form-data; boundary="+formData.boundary},

				success: function (response) {
					resetForm();
					accountsDTRef.ajax.reload(); // refresh accounts table
					createAccModal.style.display = "none";

					$.confirm({
						title: 'Success',
						content: response["msg"],
						buttons: {
							confirm: {
								btnClass: 'btn-green',
								action: function () {
									return true;
								}
							}
						}
					});


				},
				error: function (err) {
					$.confirm({
						title: 'Error',
						content: err.responseJSON["msg"],
						buttons: {
							confirm: {
								btnClass: 'btn-red',
								action: function () {
									createAccBtn.removeAttr("disabled");
									updateAccBtn.removeAttr("disabled");
									return true;
								}
							}
						}
					});
				}
			});

		}
	}

	$('.createAccForm input:not(input[type=radio])').each(function () {
		$(this).focus(function () {
			hideValidate(this);
		});
	});

	$('.createAccForm input[type=radio]').each(function () {
		$(this).focus(function () {
			hideValidate(this, true);
		});
	});
	accountsDTRef = accountsDT.DataTable( {
		rowId: 'acc_id',
		"ajax": '../api/v1/accounts?dt',
		"processing": true,
		lengthChange: false,
		pageLength: maximum_rows_per_page_jobs_list,
		autoWidth: false,

		"columns": [

			{
				"title": "ID",
				"data": "acc_id"
			},
			{
				"title": "Name",
				"data": "acc_name"
			},
			{
				"title": "Prefix",
				"data": "job_prefix"
			},
			{
				"title": "Date Created",
				"data": "acc_creation_date"
			},
			{
				"title": "Ret.",
				"data": "acc_retention_time"
			},
			{
				"title": "Log Ret.",
				"data": "act_log_retention_time"
			},
			{
				"title": "Prefix",
				"data": "job_prefix"
			},
			{
				"title": "Enabled",
				"data": "enabled",
				render: function (data) {
					if (data == true) {
						return "<i class=\"fas fa-check-circle vtex-status-icon\"></i>";
					} else {
						return "<i class=\"fas fa-times-circle vtex-status-icon\"></i>";
					}
				}
			},
			{
				"title": "Billable",
				"data": "billable",
				render: function (data) {
					if (data == true) {
						return "<i class=\"fas fa-check-circle vtex-status-icon\"></i>";
					} else {
						return "<i class=\"fas fa-times-circle vtex-status-icon\"></i>";
					}
				}
			},
			{
				"title": "STT minutes",
				"data": "sr_minutes_remaining"
			}
		]
	});

	$.contextMenu({
		selector: '.accounts-tbl tbody tr',
		callback: function(key, options) {
			// var m = "clicked: " + key + "  ";
			// window.console && console.log(m) ;//|| alert(m);
						// accountsDTRef.row(this).data()["enabled"] == true;
			var data =  accountsDTRef.row(this).data();
			switch(key){
				case "delete":
					var accId = data["acc_id"];
					var accName = data["acc_name"];
					$.confirm({
						title: '<i class="fas fa-user-minus"></i> Delete Account?',
						content: 'Are you sure do you want to delete <b>'+
							 accId + "-"+
							accName +'</b> account?<br><br>' +
							'<span style="color: red">USE WITH CAUTION THIS WILL DELETE THE ACCOUNT AND ALL RELATED DATA INCLUDING JOB ENTRIES</span>',
						buttons: {
							confirm: {
								text: "yes",
								btnClass: 'btn-red',
								action: function () {

									$.ajax({
										type: 'DELETE',
										url: API_INSERT_URL+accId,
										processData: false,
										contentType: false,
										success: function (response) {
											accountsDTRef.ajax.reload(); // refresh accounts table
											$.confirm({
												title: response["error"]?"Error":"Success",
												content: response["msg"],
												buttons: {
													confirm: {
														btnClass: response["error"]?"btn-red":"btn-green",
														action: function () {
															return true;
														}
													}
												}
											});


										},
										error: function (err) {
											accountsDTRef.ajax.reload(); // refresh accounts table
											$.confirm({
												title: 'Error',
												content: err.responseJSON["msg"],
												buttons: {
													confirm: {
														btnClass: 'btn-red',
														action: function () {
															return true;
														}
													}
												}
											});
										}
									});

									return true;
								}
							},
							cancel:
								{
									text: "no",
									btnClass: 'btn-green',
									function () {
										return true;
									}
							}
						}
					});
					break;

				case "edit":
					preFillForm(data);
					break;

					case "add":
						addMinutesToAcc(data);
					break;
			}

		},
		items: {
			"edit": {name: "Edit", icon: "fas fa-user-edit"},
			"add": {name: "Add minutes", icon: "fas fa-plus-circle"},
			"sep1": "---------",
			"delete": {name: "Delete", icon: "fas fa-user-minus"},
			// "quit2": {name: "Quit2", icon: "quit"},
			// "quit": {name: "Quit", icon: function(){
			// 		console.log("return function for quit");
			// 		return 'context-menu-icon context-menu-icon-quit';
			// 	}}
		}
	});

	function addMinutesToAcc(data)
	{
		$.confirm({
			title: 'Add STT minutes',
			content: '' +
				'<form action="" class="formName">' +
				'<div class="form-group">' +
				'<label>Acc: '+data["acc_name"]+'</label>' +
				'<input type="text" placeholder="minutes to add" class="name form-control" required />' +
				'</div>' +
				'</form>',
			buttons: {
				formSubmit: {
					text: 'Submit',
					btnClass: 'btn-blue',
					action: function () {
						var name = this.$content.find('.name').val();
						if(!name){
							$.alert('provide a valid minutes value');
							return false;
						}
						// ajax update
						var formData = new FormData();
						formData.append("update-sr-min", true);
						formData.append("min", name);
						$.ajax({
							type: 'PUT',
							url: API_INSERT_URL+data["acc_id"]+"/",
							processData: false,
							data: convertToSearchParam(formData),

							success: function (response) {
								accountsDTRef.ajax.reload(); // refresh accounts table

								$.confirm({
									title: 'Success',
									content: response["msg"],
									buttons: {
										confirm: {
											btnClass: 'btn-green',
											action: function () {
												return true;
											}
										}
									}
								});


							},
							error: function (err) {
								$.confirm({
									title: 'Error',
									content: err.responseJSON["msg"],
									buttons: {
										confirm: {
											btnClass: 'btn-green',
											action: function () {
												return true;
											}
										}
									}
								});
							}
						});
					}
				},
				cancel: function () {
					//close
				},
			},
			onContentReady: function () {
				// bind to events
				var jc = this;
				this.$content.find('form').on('submit', function (e) {
					// if the user submits the form by pressing enter in the field.
					e.preventDefault();
					jc.$$formSubmit.trigger('click'); // reference the button and click it
				});
			}
		});
	}

	refreshBtn.addEventListener('click', e => {
		accountsDTRef.ajax.reload();
	});

	createAcc.on("click", function (e) {

		updateAccBtn.css("display","none");
		createAccBtn.css("display","inline");
		createAccModal.style.display = "block";
		$('#modal').stop().animate({
			scrollTop: 0
		}, 500);
	});

	updateAccBtn.on("click", function (e) {
		goForm(true, currentID);
	});

	createAccBtn.on("click", function (e) {
		goForm();
	});

	$(".cancel-acc-button").on("click", function (e) {
		resetForm();
		createAccModal.style.display = "none";
	});

	$.ajax({
		url: SPEAKER_TYPES_URL,
		method: "GET",
		success: function (speakerTypes) {
			speakerTypesGlobal = speakerTypes;
			setupComboBox(speakerTypes, "br1box");
			setupComboBox(speakerTypes, "br2box");
			setupComboBox(speakerTypes, "br3box");
			setupComboBox(speakerTypes, "br4box");
			setupComboBox(speakerTypes, "br5box");
			createAccBtn.removeAttr("disabled");
			updateAccBtn.removeAttr("disabled");
		}
	});

	//
	// act_log_retention_time.spinner({
	// 	spin: function( event, ui ) {
	// 		if ( ui.value < 0 ) {
	// 			$( this ).spinner( "value", 0 );
	// 			return false;
	// 		}
	// 	}
	// });

	// $(
	// 	"#bill_rate1_TAT,"+
	// 	"#bill_rate2_TAT,"+
	// 	"#bill_rate3_TAT,"+
	// 	"#bill_rate4_TAT,"+
	// 	"#bill_rate5_TAT"
	// ).spinner({
	// 	spin: function( event, ui ) {
	// 		if ( ui.value < 0 ) {
	// 			$( this ).spinner( "value", 0 );
	// 			return false;
	// 		}
	// 	}
	// });
	//
	// $( "#bill_rate1, #bill_rate1_min_pay," +
	// 	"#bill_rate2, #bill_rate2_min_pay," +
	// 	"#bill_rate3, #bill_rate3_min_pay," +
	// 	"#bill_rate4, #bill_rate4_min_pay," +
	// 	"#bill_rate5, #bill_rate5_min_pay"
	// ).spinner({
	// 	min: 0,
	// 	step: 0.01,
	// 	numberFormat: "C",
	// 	spin: function( event, ui ) {
	// 		if ( ui.value < 0 ) {
	// 			$( this ).spinner( "value", 0 );
	// 			return false;
	// 		}
	// 	}
	// });



});

function setupComboBox(data, boxID){

	// <option value="ActionScript">ActionScript</option>

	var type1cmb = document.getElementById(boxID);


	var option;
	data.forEach(function(entry) {
		option = document.createElement("option");
		option.setAttribute("value", entry["value"]);
		option.setAttribute("id", "test");
		// option.inn("test");
		option.innerHTML = entry["label"];
		type1cmb.appendChild(option);
	});



	// $( "#" +boxID ).combobox();
}

function htmlEncodeStr(s)
{
	return s.replace(/&/g, "&amp;")
		.replace(/>/g, "&gt;")
		.replace(/</g, "&lt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&lsquo;");
}

function resetForm(){
	modalHeaderTitle.html(CREATE_ACC_HEADER);
	updateAccBtn.css("display","none");
	createAccBtn.css("display","inline");
	createAccForm.find("input:not(input[type=radio])").val("");
	createAccForm.find("input[type=radio]").prop('checked', false).change();

	createAccBtn.removeAttr("disabled");
	updateAccBtn.removeAttr("disabled");
}

function preFillForm(data)
{
	// data: dataTable Item Array
	// accountsDTRef.row(this).data()["item"]
	// data["item"]

	// pass current ID
	currentID = data["acc_id"];

	// header title
	modalHeaderTitle.html(UPDATE_ACC_HEADER);

	// buttons
	updateAccBtn.css("display","inline");
	createAccBtn.css("display","none");

	// acc name
	accName.val(data["acc_name"]);

	// acc retention
	acc_retention_time.val(data["acc_retention_time"]);

	// log retention
	act_log_retention_time.val(data["act_log_retention_time"]);

	// radio buttons
	if(data["enabled"] === "1"){
		$("#enabled-t").prop('checked', true).change();
	}else{
		$("#enabled-f").prop('checked', true).change();
	}
	if(data["billable"] === "1"){
		$("#billable-t").prop('checked', true).change();
	}else{
		$("#billable-f").prop('checked', true).change();
	}

	// Billing //
	br1.val(data["bill_rate1"]);
	br2.val(data["bill_rate2"]);
	br3.val(data["bill_rate3"]);
	br4.val(data["bill_rate4"]);
	br5.val(data["bill_rate5"]);

	br1min.val(data["bill_rate1_min_pay"]);
	br2min.val(data["bill_rate2_min_pay"]);
	br3min.val(data["bill_rate3_min_pay"]);
	br4min.val(data["bill_rate4_min_pay"]);
	br5min.val(data["bill_rate5_min_pay"]);

	br1_TAT.val(data["bill_rate1_TAT"]);
	br2_TAT.val(data["bill_rate2_TAT"]);
	br3_TAT.val(data["bill_rate3_TAT"]);
	br4_TAT.val(data["bill_rate4_TAT"]);
	br5_TAT.val(data["bill_rate5_TAT"]);

	var b1type = data["bill_rate1_type"]?data["bill_rate1_type"]:0;
	var b3type = data["bill_rate3_type"]?data["bill_rate3_type"]:0;
	var b2type = data["bill_rate2_type"]?data["bill_rate2_type"]:0;
	var b4type = data["bill_rate4_type"]?data["bill_rate4_type"]:0;
	var b5type = data["bill_rate5_type"]?data["bill_rate5_type"]:0;


	br1type.val(b1type);
	$(".custom-br1box input").val(speakerTypesGlobal[b1type].label);

	br2type.val(b2type);
	$(".custom-br2box input").val(speakerTypesGlobal[b2type].label);

	br3type.val(b3type);
	$(".custom-br3box input").val(speakerTypesGlobal[b3type].label);

	br4type.val(b4type);
	$(".custom-br4box input").val(speakerTypesGlobal[b4type].label);

	br5type.val(b5type);
	$(".custom-br5box input").val(speakerTypesGlobal[b5type].label);


	br1desc.val(data["bill_rate1_desc"]);
	br2desc.val(data["bill_rate2_desc"]);
	br3desc.val(data["bill_rate3_desc"]);
	br4desc.val(data["bill_rate4_desc"]);
	br5desc.val(data["bill_rate5_desc"]);





	// show form
	createAccModal.style.display = "block";
	$('#modal').stop().animate({
		scrollTop: 0
	}, 500);

}

function validateForm()
{
	var check = true;
	/** radio buttons (2) */
	// enabled
	if($(".billable-radios input[type=radio]:checked").length === 0){
		check = false;
		billableRadioGroup.addClass("vtex-validate-error");
	}
	// billable
	if($(".enabled-radios input[type=radio]:checked").length === 0){
		check = false;
		enabledRadioGroup.addClass("vtex-validate-error");
	}

	/**  Account Name  */
	if(isEmptyInput(accName)) {
		check = false;
		accName.addClass("vtex-validate-error");
	}

	/**  Account Activity log retention time  */
	if(act_log_retention_time.val() <= 0){
		check = false;
		act_log_retention_time.addClass("vtex-validate-error");
	}

	/**  Account Files retention time  */
	if(acc_retention_time.val() <= 0){
		check = false;
		acc_retention_time.addClass("vtex-validate-error");
	}

	if(!check){
		// $("#modal").scrollTop(0);
		$.confirm({
			title: 'Error',
			content: 'Make sure to correctly fill the highlighted fields.',
			scrollToPreviousElement: false,
			scrollToPreviousElementAnimate: false,
			buttons: {
				confirm: {
					btnClass: 'btn-red',
					text: "OK",
					action: function () {
						$('#modal').stop().animate({
							scrollTop: 0
						}, 500);

						return true;
					}
				}
			}
		});
	}
	return check;
}

function hideValidate(input, isRadioBtn = false) {

	var self;
	if(isRadioBtn){
		self = $(input).parent();
	}else{
		self = $(input);
	}
	// self.val()
	self.removeClass("vtex-validate-error");
}

function isEmptyInput( el ){
	return !$.trim(el.val())
}

function convertToSearchParam(params){
	const searchParams = new URLSearchParams();
	// for (const prop in params) {
	// 	searchParams.set(prop, params[prop]);
	// }

	for (const [key, value] of params) {
		// console.log('»', key, value);
		searchParams.set(key, value);
	}

	return searchParams;
}