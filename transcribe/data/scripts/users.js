(function ($) {
	"use strict";
})(jQuery);

var usersDT;
var usersDTRef;
var createAcc;
var createAccModal;
var createAccForm;
var createAccBtn;
var updateAccBtn;
var modalHeaderTitle;
var countriesGlobal;
const COUNTRIES_URL = "../api/v1/countries/?box_model";
const CITIES_URL = "../api/v1/cities/"; // + id + "?box_model"
const API_INSERT_URL = '../api/v1/users/';

const CREATE_ACC_HEADER = "<i class=\"fas fa-user-plus\"></i>&nbsp;Create New User";
const UPDATE_ACC_HEADER = "<i class=\"fas fa-user-edit\"></i>&nbsp;Update User";

/** Fields */
var fname;
var lname;
var email;
var newsletterRadioGroup;
var enabledRadioGroup;
var acc_retention_time;
var act_log_retention_time;
// -- combobox vars -- //
var countryBox;

// state
var stateContainer;
var stateBoxLbl; // for combo box
var stateBox;

var stateInputLbl; // for input
var stateInput;
// ====================
// city
var cityContainer;
var cityInput;
// var cityLbl;
// ---------
var cityRequest;


var update = false;
var updateData;


var currentID;

$(document).ready(function () {

	const maximum_rows_per_page_jobs_list = 10;

	createAccModal = document.getElementById("modal");
	// createAccModal.style.display = "block";
	usersDT = $("#users-tbl");
	createAcc = $("#createAcc");
	createAccForm = $("#createAccForm");
	createAccBtn = $("#createAccBtn");
	updateAccBtn = $("#updateAccBtn");
	modalHeaderTitle = $("#modalHeaderTitle");

	// country
	countryBox = $("#country");

	// state
	stateContainer = $("#stateContainer");
	stateBoxLbl = $("#stateBoxLbl");
	stateBox = $("#stateBox");
	stateInputLbl = $("#stateInputLbl");
	stateInput = $("#stateInput");

	// city
	cityContainer = $("#cityContainer");
	cityInput = $("#cityInput");

	/** Fields */
	fname = $("#fname");
	lname = $("#lname");
	email = $("#email");
	newsletterRadioGroup = $(".newsletter-radios");
	enabledRadioGroup = $(".enabled-radios");

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

	$("body").niceScroll({
		hwacceleration: true,
		smoothscroll: true,
		cursorcolor: "white",
		cursorborder: 0,
		scrollspeed: 10,
		mousescrollstep: 20,
		cursoropacitymax: 0.7
		//  cursorwidth: 16

	});

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
			// refresh user table

			$.ajax({
				type: update?'PUT':'POST',
				url: update?API_INSERT_URL+id+"/":API_INSERT_URL,
				processData: false,
				// contentType: "application/x-www-form-urlencoded",
				data: convertToSearchParam(formData),
				// headers: update?{'Content-Type': 'application/x-www-form-urlencoded'}:{'Content-Type': "multipart/form-data; boundary="+formData.boundary},

				success: function (response) {
					resetForm();
					usersDTRef.ajax.reload(); // refresh users table
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
	usersDTRef = usersDT.DataTable( {
		rowId: 'acc_id',
		"ajax": '../api/v1/users?dt',
		"processing": true,
		lengthChange: false,
		pageLength: maximum_rows_per_page_jobs_list,
		autoWidth: false,
		// columnDefs: [
		// 	{
		// 		targets: ['_all'],
		// 		className: 'mdc-data-table__cell'
		// 	}
		// ],
		/*
		* 				<th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Status</th>
                        <th>Enabled</th>
		* */
		"columns": [
			{ "data": "id"},
			{ "data": "first_name",
				render: function ( data, type, row ) {
					return data + " " + row["last_name"];
				}
			},
			{ "data": "email" },
			{ "data": "country" },
			{ "data": "city" },
			{ "data": "state",
				render: function (data, type, row) {
					if(data)
					{
						return data;
					}else{
						return row["state_ref"];
					}
				}
			},
			{ "data": "account_status" },
			{ "data": "acc_name",
				render: function (data, type, row) {
					if(data == null)
					{
						return "—";
					}else{
						return data + " - " + row["acc_role"];
					}
				} },
			{ "data": "enabled",
				render: function (data) {
					if(data == true)
					{
						return "<i class=\"fas fa-check-circle vtex-status-icon\"></i>";
					}else{
						return "<i class=\"fas fa-times-circle vtex-status-icon\"></i>";
					}
				}
			}
		]
	} );

	$.contextMenu({
		selector: '.users-tbl tbody tr',
		callback: function(key, options) {
			// var m = "clicked: " + key + "  ";
			// window.console && console.log(m) ;//|| alert(m);
						// usersDTRef.row(this).data()["enabled"] == true;
			var data =  usersDTRef.row(this).data();
			switch(key){
				case "delete":
					var accId = data["id"];
					var accName = data["email"];
					$.confirm({
						title: '<i class="fas fa-user-minus"></i> Delete User?',
						content: 'Are you sure do you want to delete <b>'+
							 accId + "-"+
							accName +'</b> user?<br><br>' +
							'<span style="color: red">USE WITH CAUTION THIS WILL DELETE THE USER ACCOUNT AND ALL RELATED DATA INCLUDING JOB ENTRIES</span>',
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
											usersDTRef.ajax.reload(); // refresh users table
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
											usersDTRef.ajax.reload(); // refresh users table
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

				case "permit":
					$('<form action="access.php" method="post" style="display: none">' +
						'<input name="uid-access" value="'+data["id"]+'" />' +
						'</form>').appendTo('body').submit();
					break;
			}

		},
		items: {
			"edit": {name: "Edit", icon: "fas fa-user-edit"},
			"permit": {name: "Manage Access", icon: "fas fa-user-shield"},
			"sep1": "---------",
			"delete": {name: "Delete", icon: "fas fa-user-minus"},
			// "quit2": {name: "Quit2", icon: "quit"},
			// "quit": {name: "Quit", icon: function(){
			// 		console.log("return function for quit");
			// 		return 'context-menu-icon context-menu-icon-quit';
			// 	}}
		}
	});

	refreshBtn.addEventListener('click', e => {
		usersDTRef.ajax.reload();
	});

	createAcc.on("click", function (e) {

		update = false;
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
		url: COUNTRIES_URL,
		method: "GET",
		success: function (countries) {
			countriesGlobal = countries;
			// console.log(countriesGlobal);
			// setupComboBox(countries, "country");
			const cbox = document.getElementById("country");
			for (const country of countries) {
				cbox.innerHTML += "<option value='"+country.value+"'>"+country.label+"</option>";
			}
			countryBox.selectpicker({
				liveSearch: true,
				liveSearchPlaceholder: "Choose Country"
			});

			loadCityAndState(countryBox.selectpicker('val')); // first time

			countryBox.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
				// do something...
				// console.log("selection changed to: " + clickedIndex + " and prev was: " + previousValue+ "and e is ");
				// console.log(e);
				// console.log(countryBox.selectpicker('val')); // selected value
				loadCityAndState(countryBox.selectpicker('val'));
			});
		}
	});

	function loadCityAndState(cID) {
		stateInputLbl.css("display","none");
		stateBoxLbl.css("display","none");

		cityContainer.css("display","none");

		createAccBtn.attr("disabled","disabled");
		updateAccBtn.attr("disabled","disabled");
		removeLoadingSpinner(); // if any left over
		stateContainer.append(generateLoadingSpinner());
		stateBox.selectpicker('destroy');

		if(cID == 204 || cID == 203)
		{
			setStateForm(true);
			if(cityRequest != null)
			{
				cityRequest.abort();
			}

			cityRequest = $.ajax({
				url: CITIES_URL + cID + "?box_model",
				method: "GET",
				success: function (cities) {
					// console.log(cities);

					const tybox = document.getElementById("stateBox");
					tybox.innerHTML = ""; // clear old values
					for (const city of cities) {
						tybox.innerHTML += "<option value='"+city.value+"'>"+city.label+"</option>"
					}
					stateBox.selectpicker({
						liveSearch: true,
						liveSearchPlaceholder: "Choose City"
					});
					stateBox.selectpicker('refresh');

					removeLoadingSpinner();

					cityContainer.css("display","inline");

					if(update){
						updateAccBtn.removeAttr("disabled");
						// update city with city ID
						if(updateData["state_id"] != null && updateData["state_id"] != 0 ){
							// update city
							stateBox.selectpicker('val',updateData["state_id"]);
							stateBoxLbl.css("display","inline");
						}else{
							stateBoxLbl.css("display","inline");
						}
					} else{
						stateBoxLbl.css("display","inline");
						createAccBtn.removeAttr("disabled");
					}
					// city_input_lbl.css("display","inline");
				}
			});
		}else{
			setStateForm(false);
			removeLoadingSpinner();
			stateInputLbl.css("display","inline");
			cityContainer.css("display","inline");

			if(update){
				stateInput.val(updateData["state"]);
				updateAccBtn.removeAttr("disabled");
			} else{
				createAccBtn.removeAttr("disabled");
			}
		}
	}


});

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
	setStateForm(true);
	updateAccBtn.css("display","none");
	createAccBtn.css("display","inline");
	createAccForm.find("input:not(input[type=radio])").val("");
	createAccForm.find("input[type=radio]").prop('checked', false).change();

	createAccBtn.removeAttr("disabled");
	updateAccBtn.removeAttr("disabled");
}

function setStateForm(comboBoxID){
	if(comboBoxID)
	{
		stateInput.removeAttr("name");
		stateBox.attr("name", "state_id");
	}else{
		stateBox.removeAttr("name");
		stateInput.attr("name", "state");
	}
}

function preFillForm(data)
{
	update = true;
	updateData = data;
	// data: dataTable Item Array
	// usersDTRef.row(this).data()["item"]
	// data["item"]

	// pass current ID
	currentID = data["id"];

	// header title
	modalHeaderTitle.html(UPDATE_ACC_HEADER);

	// buttons
	updateAccBtn.css("display","inline");
	createAccBtn.css("display","none");

	// name
	fname.val(data["first_name"]);
	lname.val(data["last_name"]);

	email.val(data["email"]);

	// radio buttons
	if(data["enabled"] == 1){
		$("#enabled-t").prop('checked', true).change();
	}else{
		$("#enabled-f").prop('checked', true).change();
	}
	if(data["newsletter"] == 1){
		$("#newsletter-t").prop('checked', true).change();
	}else{
		$("#newsletter-f").prop('checked', true).change();
	}

	// Setting address --
	if(data["country_id"] != 0)
	{
		countryBox.selectpicker('val',data["country_id"]);
	}
	// cityInput
	if(data["city"] != null)
	{
		cityInput.val(data["city"]);
	}else{
		cityInput.val("");
	}


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
	if($(".newsletter-radios input[type=radio]:checked").length === 0){
		check = false;
		newsletterRadioGroup.addClass("vtex-validate-error");
	}
	// newsletter
	if($(".enabled-radios input[type=radio]:checked").length === 0){
		check = false;
		enabledRadioGroup.addClass("vtex-validate-error");
	}

	/**  User Name  */
	if(isEmptyInput(fname)) {
		check = false;
		fname.addClass("vtex-validate-error");
	}

	if(isEmptyInput(lname)) {
		check = false;
		lname.addClass("vtex-validate-error");
	}


	if( !validateEmail(email.val()) ) {
		check = false;
		email.addClass("vtex-validate-error");
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


function removeLoadingSpinner() {
	$(".spinner").remove();
}
function generateLoadingSpinner() {

	// Generate a loading spinner //
	//<div class="spinner">
	//  <div class="bounce1"></div>
	//  <div class="bounce2"></div>
	//  <div class="bounce3"></div>
	//</div>

	const spinnerDiv = document.createElement("div");
	spinnerDiv.setAttribute("class", "spinner");
	const bounce1 = document.createElement("div");
	const bounce2 = document.createElement("div");
	const bounce3 = document.createElement("div");
	bounce1.setAttribute("class", 'bounce1');
	bounce2.setAttribute("class", 'bounce2');
	bounce3.setAttribute("class", 'bounce3');

	spinnerDiv.appendChild(bounce1);
	spinnerDiv.appendChild(bounce2);
	spinnerDiv.appendChild(bounce3);

	return spinnerDiv;
}

function validateEmail(mail)
{
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail))
	{
		return (true)
	}
	// alert("You have entered an invalid email address!")
	return (false)
}