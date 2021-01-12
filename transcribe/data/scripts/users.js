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
const COUNTRIES_URL = '/data/thirdparty/typeahead/countries.json';
// const COUNTRIES_URL = "../api/v1/countries/?box_model";
// const CITIES_URL = "../api/v1/cities/"; // + id + "?box_model"
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

// ====================
// city
var cityContainer;
var city;
// var cityLbl;
// ---------
var cityRequest;


var update = false;
var updateData;


var currentID;

$(document).ready(function () {

	const maximum_rows_per_page_jobs_list = 10;

	var lastZipRequested = '';
	const zippoURL = "https://api.zippopotam.us/";
	const CITY_FILTER_REGEX = /\(.*| st.*|[^a-zA-Z0-9. ]/gi;

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
	state = $("#stateInput");


	// city
	cityContainer = $("#cityContainer");
	city = $("#cityInput");
	zip = $("#zipcode");

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
		"columns": [
			{
				"title": "ID",
				"data": "id"
			},
			{
				"title": "Name",
				"data": "first_name",
				render: function (data, type, row) {
					return data + " " + row["last_name"];
				}
			},
			{
				"title": "Email",
				"data": "email"
			},
			{
				"title": "Country",
				"data": "country"
			},
			{
				"title": "City",
				"data": "city"
			},
			{
				"title": "State",
				"data": "state"
			},
			{
				"title": "Status",
				"data": "account_status"
			},
			{
				"title": "Def Access",
				"data": "acc_name",
				render: function (data, type, row) {
					if (data == null) {
						return "—";
					} else {
						return data + " - " + row["acc_role"];
					}
				}
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
			// setupComboBox(countries, "country");
			const cbox = document.getElementById("country");
			for (const country of countries) {
				// cbox.innerHTML += "<option value='"+country.label+"'>"+country.label+"</option>";
				cbox.innerHTML += "<option value='"+country+"'>"+country+"</option>";
			}
			countryBox.selectpicker({
				liveSearch: true,
				liveSearchPlaceholder: "Choose Country"
			});

		}
	});

	zip.keyup(function () {
		// check for matching regex
		var CA_REGEX = /^[a-zA-Z0-9]{3}$|^[a-zA-Z0-9]{6}$|^[a-zA-Z0-9]{3} [a-zA-Z0-9]{3}$/;
		var US_REGEX = /^[0-9]{5}$/;
		zipValue = zip.val();

		switch (zipValue.length) {

			// CA
			case 3:
			case 6:
				if(CA_REGEX.test(zipValue))
				{
					// lookup CA address
					/*if(currentCountry != "Canada")
                    {

                        $("#countryBox").selectpicker('val', 203);
                    }*/

					lookupZip(zipValue.slice(0,3), "ca");
				}
				break;

			// US
			case 5:
				if(US_REGEX.test(zipValue))
				{
					// lookup US address
					/*if (currentCountry != 204) {
                        $("#countryBox").selectpicker('val', 204);
                    }*/
					lookupZip(zipValue, "us");
				}
				break;
		}

	});

	function lookupZip(zip, countryLookup)
	{
		if(lastZipRequested !== zip)
		{
			// var jqxhr = $.get( "example.php", function() {
			// console.log("Looking up in " + country + " Zip: " + zip);
			$.get( zippoURL + countryLookup + "/"+ zip, function() {
				// alert( "success" );
			})
				.done(function(response) {
					// var location = JSON.parse(response);
					// "Winnipeg (St. Boniface NE)"
					city.val(response["places"][0]["place name"].replace(CITY_FILTER_REGEX,"").trim());
					// checkCity();

					state.val(response["places"][0]["state"]);

					if(countryLookup ===  "ca")
					{
						countryBox.selectpicker('val', "Canada");
						// enableCaEngine();
						// calculateTaxes();
					}else if(countryLookup === "us")
					{
						countryBox.selectpicker('val',"United States");
						// enableUsEngine();
					}



					// console.log(response);
				})
				.fail(function(error) {
					// couldn't get address
					// console.log("Failed to locate address by zip/postal code");
					// alert( "error" );
				});
			/*.always(function() {
                alert( "finished" );
            });*/
		}

		lastZipRequested = zip;
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
	// setStateForm(true);
	updateAccBtn.css("display","none");
	createAccBtn.css("display","inline");
	createAccForm.find("input:not(input[type=radio])").val("");
	createAccForm.find("input[type=radio]").prop('checked', false).change();

	createAccBtn.removeAttr("disabled");
	updateAccBtn.removeAttr("disabled");
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
	if(data["country"] != null)
	{
		countryBox.selectpicker('val',data["country"]);
	}
	if(data["state"] != null)
	{
		state.val(updateData["state"]);
	}else{
		state.val("");
	}

	// city
	if(data["city"] != null)
	{
		city.val(data["city"]);
	}else{
		city.val("");
	}
	updateAccBtn.removeAttr("disabled");

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