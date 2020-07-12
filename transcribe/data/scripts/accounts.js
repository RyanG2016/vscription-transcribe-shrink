(function ($) {
	"use strict";
})(jQuery);

var accountsDT;
var accountsDTRef;
var createAcc;
var createAccModal;
var createAccForm;
var createAccBtn;
var closeAccModal;
const SPEAKER_TYPES_URL = "../api/v1/speaker-types/?box_model";
const API_INSERT_URL = '../api/v1/accounts/';

/** Fields */
var accName;
var billableRadioGroup;
var enabledRadioGroup;
var acc_retention_time;
var act_log_retention_time;


$(document).ready(function () {

	const maximum_rows_per_page_jobs_list = 10;

	createAccModal = document.getElementById("modal");

	accountsDT = $("#accounts-tbl");
	createAcc = $("#createAcc");
	createAccForm = $("#createAccForm");
	createAccBtn = $("#createAccBtn");

	/** Fields */
	accName = $("#accName");
	billableRadioGroup = $(".billable-radios");
	enabledRadioGroup = $(".enabled-radios");
	acc_retention_time = $("#acc_retention_time");
	act_log_retention_time = $( "#act_log_retention_time" );

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

	createAccForm.bind('submit', function (e) {
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
	});

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
		rowId: 'file_id',
		"ajax": '../api/v1/accounts?dt',
		"processing": true,
		lengthChange: false,
		pageLength: maximum_rows_per_page_jobs_list,
		autoWidth: false,
		columnDefs: [
			{
				targets: ['_all'],
				className: 'mdc-data-table__cell'
			}
		],
		/*
		* 				<th>ID</th>
                        <th>Name</th>
                        <th>Prefix</th>
                        <th>Date Created</th>
                        <th>Retention</th>
                        <th>Log Retention</th>
                        <th>Enabled</th>
                        <th>Billable</th>
		* */
		"columns": [
			{ "data": "acc_id"},
			{ "data": "acc_name" },
			{ "data": "job_prefix" },
			{ "data": "acc_creation_date" },
			{ "data": "acc_retention_time" },
			{ "data": "act_log_retention_time" },
			{ "data": "enabled",
				render: function (data) {
					if(data == true)
					{
						return "<i class=\"fas fa-check-circle vtex-status-icon\"></i>";
					}else{
						return "<i class=\"fas fa-times-circle vtex-status-icon\"></i>";
					}
				}
			},
			{ "data": "billable",
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

	refreshBtn.addEventListener('click', e => {
		accountsDTRef.ajax.reload();
	});

	createAcc.on("click", function (e) {
		createAccModal.style.display = "block";
		$('#modal').stop().animate({
			scrollTop: 0
		}, 500);
	});

	$(".cancel-acc-button").on("click", function (e) {
		resetForm();
		createAccModal.style.display = "none";
	});

	$.ajax({
		url: SPEAKER_TYPES_URL,
		method: "GET",
		success: function (speakerTypes) {
			setupComboBox(speakerTypes, "br1box");
			setupComboBox(speakerTypes, "br2box");
			setupComboBox(speakerTypes, "br3box");
			setupComboBox(speakerTypes, "br4box");
			setupComboBox(speakerTypes, "br5box");
			createAccBtn.removeAttr("disabled");
		}
	});

	acc_retention_time.spinner({
		spin: function( event, ui ) {
			if ( ui.value < 0 ) {
				$( this ).spinner( "value", 0 );
				return false;
			}
		}
	});

	act_log_retention_time.spinner({
		spin: function( event, ui ) {
			if ( ui.value < 0 ) {
				$( this ).spinner( "value", 0 );
				return false;
			}
		}
	});

	$(
		"#bill_rate1_TAT,"+
		"#bill_rate2_TAT,"+
		"#bill_rate3_TAT,"+
		"#bill_rate4_TAT,"+
		"#bill_rate5_TAT"
	).spinner({
		spin: function( event, ui ) {
			if ( ui.value < 0 ) {
				$( this ).spinner( "value", 0 );
				return false;
			}
		}
	});

	$( "#bill_rate1, #bill_rate1_min_pay," +
		"#bill_rate2, #bill_rate2_min_pay," +
		"#bill_rate3, #bill_rate3_min_pay," +
		"#bill_rate4, #bill_rate4_min_pay," +
		"#bill_rate5, #bill_rate5_min_pay"
	).spinner({
		min: 0,
		step: 0.01,
		numberFormat: "C",
		spin: function( event, ui ) {
			if ( ui.value < 0 ) {
				$( this ).spinner( "value", 0 );
				return false;
			}
		}
	});



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

	$.widget( "custom.combobox", {
		_create: function() {
			this.wrapper = $( "<span>" )
				.addClass( "custom-combobox custom-"+boxID )
				.insertAfter( this.element );

			this.element.hide();
			this._createAutocomplete();
			this._createShowAllButton();
		},

		_createAutocomplete: function() {
			var selected = this.element.children( ":selected" ),
				value = selected.val() ? selected.text() : "";

			this.input = $( "<input>" )
				.appendTo( this.wrapper )
				.val( value )
				.attr( "title", "" )
				.addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
				.autocomplete({
					delay: 0,
					minLength: 0,
					source: $.proxy( this, "_source" )
					// source: data
				})
				.tooltip({
					classes: {
						"ui-tooltip": "ui-state-highlight"
					}
				});

			this._on( this.input, {
				autocompleteselect: function( event, ui ) {
					ui.item.option.selected = true;
					this._trigger( "select", event, {
						item: ui.item.option
					});
				},

				autocompletechange: "_removeIfInvalid"
			});
		},

		_createShowAllButton: function() {
			var input = this.input,
				wasOpen = false;

			$( "<a>" )
				.attr( "tabIndex", -1 )
				// .attr( "title", "Show All Items" )
				// .tooltip()
				.appendTo( this.wrapper )
				.button({
					icons: {
						primary: "ui-icon-triangle-1-s"
					},
					text: false
				})
				.removeClass( "ui-corner-all" )
				.addClass( "custom-combobox-toggle ui-corner-right" )
				.on( "mousedown", function() {
					wasOpen = input.autocomplete( "widget" ).is( ":visible" );
				})
				.on( "click", function() {
					input.trigger( "focus" );

					// Close if already visible
					if ( wasOpen ) {
						return;
					}

					// Pass empty string as value to search for, displaying all results
					input.autocomplete( "search", "" );
				});
		},

		_source: function( request, response ) {
			var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
			response( this.element.children( "option" ).map(function() {
				var text = $( this ).text();
				if ( this.value && ( !request.term || matcher.test(text) ) )
					return {
						label: text,
						value: text,
						option: this
					};
			}) );
		},

		_removeIfInvalid: function( event, ui ) {

			// Selected an item, nothing to do
			if ( ui.item ) {
				return;
			}

			// Search for a match (case-insensitive)
			var value = this.input.val(),
				valueLowerCase = value.toLowerCase(),
				valid = false;
			this.element.children( "option" ).each(function() {
				if ( $( this ).text().toLowerCase() === valueLowerCase ) {
					this.selected = valid = true;
					return false;
				}
			});

			// Found a match, nothing to do
			if ( valid ) {
				return;
			}

			// Remove invalid value
			this.input
				.val( "" )
				.attr( "title", value + " didn't match any item" )
				.tooltip( "open" );
			this.element.val( "" );
			this._delay(function() {
				this.input.tooltip( "close" ).attr( "title", "" );
			}, 2500 );
			this.input.autocomplete( "instance" ).term = "";
		},

		_destroy: function() {
			this.wrapper.remove();
			this.element.show();
		}
	});

	$( "#" +boxID ).combobox();
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
	createAccForm.find("input").val("");
	createAccForm.find("input[type=radio]").prop('checked', false).change();
	createAccBtn.removeAttr("disabled");
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