(function ($) {
	"use strict";


	/*==================================================================
	[ Focus input ]*/
	$('.input100').each(function () {
		$(this).on('blur', function () {
			if ($(this).val().trim() != "") {
				$(this).addClass('has-val');
			} else {
				$(this).removeClass('has-val');
			}
		})
	})

	/*========0 Login=======================1: Signup=============================*/


	/*==================================================================
	[ Validate ]*/
	var input = $('.validate-input .input100');

	$('.validate-form').on('submit', function () {
		//    $('.login100-form-btn').on('click',function(){
		var check = true;
		var method = $('input[name="method"]').attr("value");

		switch (method) {
			case "0": //login

				for (var i = 0; i < input.length; i++) {

					switch (input[i].name) {
						case "password":
						case "email":

							if (validate(input[i]) == false) {
								showValidate(input[i]);
								check = false;
							}

							break;

						default:
							continue;
					}
				}

				if (check == true) {
					check = false;
					login();
				}

				break;

			case "3": //reset

				var verify = $('input[name="email"]')

				if (validate(verify) == false) {
					showValidate(verify);
					check = false;
				}

				if (check == true) {
					$(".login100-form-btn").html("Please wait..");
					$(".login100-form-btn").attr("disabled", "");


					check = false;
					resetpw();
				}

				break;

			case "1": //signup

				for (i = 0; i < input.length; i++) {

					if (input[i].name == "countryIp" || input[i].name == "stateIp" || input[i].name == "industryIp") {
						continue;
					} else if (validate(input[i]) == false) {


						showValidate(input[i]);
						check = false;
					}
				}

				var country = $('#country').select2('data')[0].text;
				if (country == "") {
					check = false;
					$('#countryDiv .select2-container--default .select2-selection--single').addClass('error');
				}

				// ->State select
				else if (country == "United States" || country == "Canada") {
					//check for city
					var city = $('#state').select2('data')[0].text;
					if (city == "") {
						check = false;
						$('#stateDiv .select2-container--default .select2-selection--single').addClass('error');
					}
				}

				//Industry
				var industry = $('#industry').select2('data')[0].text;
				if (industry == "") {
					check = false;
					$('#industryDiv .select2-container--default .select2-selection--single').addClass('error');
				}

				//if everything is OK.. sign the user up
				if (check == true) {
					$(".login100-form-btn").html("Signing you up..");
					$(".login100-form-btn").attr("disabled", "");


					check = false;
					signup();
				}

				break;

			default:
				check = false;
				break;
		}

		return check;
	});


	$('.validate-form .input100').each(function () {
		$(this).focus(function () {
			hideValidate(this);
		});
	});

	//validate function
	function validate(input) {
		if ($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
			if ($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
				return false;
			}
		}

		//if signup only
		if ($('input[name="method"]').attr("value") == "1") {
			if ($(input).attr('type') == 'password' || $(input).attr('name') == 'password') {
				if ($(input).val().match('(?=.*?[0-9])(?=.*?[A-Z])(?=.*?[a-z])') == null || $(input).val().length < 8 || $(input).val().length > 30) {

					$.confirm({
						title: 'Password Requirements',
						type: 'red',
						content: "• min  8 characters length<br/>• max 30 characters length<br/>• at least one uppercase<br/>• at least one lowercase<br/>• at least one number",
						buttons: {
							confirm: {
								btnClass: 'btn-green',
							}
						}
					});


					return false;
				}
			}
		} else {
			if ($(input).val().trim() == '') {
				return false;
			}
		}
	}

	function showValidate(input) {
		var thisAlert = $(input).parent();

		$(thisAlert).addClass('alert-validate');
	}

	function hideValidate(input) {
		var thisAlert = $(input).parent();

		$(thisAlert).removeClass('alert-validate');
	}

	/*==================================================================
	[ Show pass ]*/
	var showPass = 0;
	$('.btn-show-pass').on('click', function () {
		if (showPass == 0) {
			$(this).next('input').attr('type', 'text');
			$(this).find('i').removeClass('zmdi-eye');
			$(this).find('i').addClass('zmdi-eye-off');
			showPass = 1;
		} else {
			$(this).next('input').attr('type', 'password');
			$(this).find('i').addClass('zmdi-eye');
			$(this).find('i').removeClass('zmdi-eye-off');
			showPass = 0;
		}

	});


})(jQuery);


//*-------------------------------------------------------*\\
//*----------------------- JS Functions ------------------*\\
//*-------------------------------------------------------*\\


function resetpw() {

	var semail = $('.validate-input .input100[name="email"]').val();

	//checkIfUserExist

	var a1 = {
		email: semail
	};
	$.post("data/parts/backend_search.php", {
		reqcode: 40,
		args: JSON.stringify(a1)
	}).done(function (data) {
		//		alert(data);
		if (data == 1) //user exists proceed to procedures
		{
			$.post("data/parts/backend_search.php", {
				reqcode: 30,
				args: JSON.stringify(a1)
			}).done(function (data) {
				location.href = 'index.php';
				//				alert(data);

			});
		} else { //user doesn't exist show error dialog

			$.confirm({
				title: 'Hmm',
				type: 'red',
				content: "We couldn't find your account, signup instead?",
				buttons: {
					confirm: {
						text: 'Signup',
						btnClass: 'btn-green',
						action: function () {
							$(".login100-form-btn").removeAttr("disabled");
							toggleFormAction();
						}
					},
					cancel: {
						text: "try again",
						action: function () {
							$(".login100-form-btn").html("Send Reset Link");
							$(".login100-form-btn").removeAttr("disabled");
						}
					},

				}
			});

		}
	});


} //reset function

function signup() {



	var vemail = $('.validate-input .input100[name="email"]').val();
	var vfname = $('input[name="fname"]').val();
	var vlname = $('input[name="lname"]').val();
	var vpassword = $('input[name="password"]').val();

	var vcountry = $('#country').select2('data')[0].text;
	var vstate;
	if (vcountry == "United States" || vcountry == "Canada") {
		vstate = $('#state').select2('data')[0].text;
	} else {
		vstate = "";
	}
	var vcity = $('input[name="city"]').val();
	var vindustry = $('#industry').select2('data')[0].text;
	var vnewsletter = $('input[name="newsletter"]').is(":checked") ? 1 : 0;

	//checkIfUserExist

	var a1 = {
		email: vemail,
		fname: vfname,
		lname: vlname,
		password: vpassword,
		country: vcountry,
		state: vstate,
		city: vcity,
		industry: vindustry,
		newsletter: vnewsletter
	};


	$.post("data/parts/backend_search.php", {
		reqcode: 31,
		args: JSON.stringify(a1)
	}).done(function (data) {
		//signup data added, returning to home
		location.href = 'index.php';


	});

	//	return false;
} //endsignup
/////////////////////////////////////////

function login() {


	$(".login100-form-btn").html("Please wait..");
	$(".login100-form-btn").attr("disabled", "");
	var vemail = $('.validate-input .input100[name="email"]').val();
	var vpassword = $('input[name="password"]').val();
	var vrememberme = $('input[name="newsletter"]').is(":checked") ? 1 : 0;


	//checkIfUserExist

	var a1 = {
		email: vemail,
		password: vpassword,
		rememberme: vrememberme
	};

	$.post("data/parts/backend_search.php", {
		reqcode: 41,
		args: JSON.stringify(a1)
	}).done(function (data) {
		//location.href = 'index.php'
		alert(data);
	});

	//	return false;
} //end login
/////////////////////////////////////////


function getCountries() {

	var resultDropdown = $('#country'); //populating fields

	$.post("data/parts/backend_search.php", {
		reqcode: 5
	}).done(function (data) {
		resultDropdown.html(data);

	});

}

function getStates(key) { //0: America, 1: Canada

	var resultDropdown = $('#state'); //populating fields

	$.post("data/parts/backend_search.php", {
		reqcode: key
	}).done(function (data) {
		resultDropdown.html(data);
		$('#stateDiv').slideDown();
		$('#state').select2({
			placeholder: 'Select state:'
		});
	});

}

function toggleFormAction() {
	var method = $('input[name="method"]').attr("value");
	if (method == "0" || method == "3") {
		$('input[name="method"]').attr("value", "1"); //signup
		showSignupFields(true);

	} else if (method == "1") {
		$('input[name="method"]').attr("value", "0");
		showSignupFields(false);
	}

}


function showResetPasswordForm() {
	$('#fnamediv').slideUp();
	$('#lnamediv').slideUp();
	$('#forgotpwd').slideUp();
	$('#countryDiv').slideUp();
	$('#stateDiv').slideUp();
	$('#cityDiv').slideUp();
	$('#industryDiv').slideUp();
	$('#passwordDiv').slideUp();
	$(".login100-form-btn").html("Send Reset Link");
	$('#btmtxt1').html("Don’t Have an account?");
	$('#btmtxt2').html("Signup");
	$('input[name="method"]').attr("value", "3");
	$('#title').html('Reset Password');
	$('#newsletter').slideUp();


}

function showSignupFields(yes) {
	if (yes) { //signup


		$('#fnamediv').slideDown();
		$('#lnamediv').slideDown();
		$('#forgotpwd').slideUp();
		$('#countryDiv').slideDown();
		//		//state
		$('#cityDiv').slideDown();
		$('#newsletter').slideDown();
		$('#newsletter .txt1').html("Signup for newsletter?");
		//		$('#em').css('margin-bottom', '2px');


		$('#industryDiv').slideDown(function () {
			//									alert("fire2");
			$("body").getNiceScroll().resize();
		});
		$(".login100-form-btn").html("Signup");
		$('#btmtxt1').html("Have an account?");
		$('#btmtxt2').html("Login");
		$('#passwordDiv').slideDown();
		$('#title').html('Signup');

		//		$('.wrap-login100').css("width","470px").delay(5000);

		$('#country').select2({
			placeholder: 'Select country:'
		});
		$('#industry').select2({
			placeholder: 'Select industry:'
		});

		$('input[name="password"]').tooltipster('content', '• min  8 characters length<br/>• max 30 characters length<br/>• at least one uppercase<br/>• at least one lowercase<br/>• at least one number');




	} else { //login
		$('#fnamediv').slideUp();
		$('#lnamediv').slideUp();
		$('#forgotpwd').slideDown();
		$('#countryDiv').slideUp();
		$('#newsletter').slideDown();
		$('#newsletter .txt1').html("Remember me?");


		//		$('#em').css('margin-bottom', '35px');
		$('#stateDiv').slideUp();
		$('#passwordDiv').slideDown();
		$('#cityDiv').slideUp();
		$('#industryDiv').slideUp(function () {
			//									alert("fire1");
			$("body").getNiceScroll().resize();
		});


		$(".login100-form-btn").html("Login");
		$('#btmtxt1').html("Don’t Have an account?");
		$('#btmtxt2').html("Signup");
		//		$('.wrap-login100').css("width","390px");
		$('#title').html('Welcome');

		//remove password tooltip

		$('input[name="password"]').tooltipster('content', null);

	}

	$("body").niceScroll({
		hwacceleration: true,
		smoothscroll: true,
		cursorcolor: "white",
		cursorborder: 0,
		scrollspeed: 10,
		mousescrollstep: 20,
		cursoropacitymax: 0.7
	});

}


//*-------------------------------------------------------*\\
//*--------------- Document Ready Scripts ----------------*\\
//*-------------------------------------------------------*\\


$(document).ready(function () {

	$('input[name="email"]').blur();
	$('input[name="password"]').blur();
	/*$('input[name="password"]').tooltip({'trigger':'focus', 'title': 'Minddd<br/>as',
										   content: function () {
														  return $(this).prop('title');
													  }
										});*/

	//password tooltip
	$('input[name="password"]').tooltipster({
		content: '• min  8 characters length<br/>• max 30 characters length<br/>• at least one uppercase<br/>• at least one lowercase<br/>• at least one number',
		trigger: 'click',
		contentAsHTML: true,
		theme: 'tooltipster-shadow'
	});

	$('input[name="password"]').tooltipster('content', null);


	$('input[name="password"]').attr('maxlength', 30);


	$("#info a").click(function () {
		toggleFormAction();
	});


	$("#forgotpwd a").click(function () {
		showResetPasswordForm();
	});

	getCountries();
	//		$('.input3-select').slideDown(300);
	//$('#genderS').val('2');

	$('#country').select2({
		placeholder: 'Select country:'
	});

	$('#industry').select2({
		placeholder: 'Select industry:'
	});


	$('.select2-results').select2();

	$('#country').on('select2:open', function () {

		$("#select2-country-results").niceScroll({
			hwacceleration: true,
			smoothscroll: true,
			cursorcolor: "#249fd9",
			scrollspeed: 12,
			mousescrollstep: 22,
			cursoropacitymax: 0.8,
			cursorwidth: 10
		});

		$('#country').off('select2:open');

	});

	$('#state').on('select2:open', function () {

		$("#select2-state-results").niceScroll({
			hwacceleration: true,
			smoothscroll: true,
			cursorcolor: "#249fd9",
			scrollspeed: 12,
			mousescrollstep: 22,
			cursoropacitymax: 0.8,
			cursorwidth: 10
		});

		$('#state').off('select2:open');

	});

	$('#industry').on('select2:open', function () {

		$("#select2-industry-results").niceScroll({
			hwacceleration: true,
			smoothscroll: true,
			cursorcolor: "#249fd9",
			scrollspeed: 12,
			mousescrollstep: 22,
			cursoropacitymax: 0.8,
			cursorwidth: 10
		});

		$('#industry').off('select2:open');

	});


	$('#country').on('select2:select', function () {

		$('#countryIp').addClass('has-val');
		$('#countryDiv .select2-container--default .select2-selection--single').removeClass('error');
		$('#countryDiv').removeClass('alert-validate');
		$('input[name="countryIp"]').attr("value", $('#country').select2('data')[0].text);

		var country = $('#country').select2('data')[0].text;
		if (country == "United States" || country == "Canada") {
			getStates(country == "Canada" ? 1 : 0);

		} else {
			$('#stateDiv').slideUp();
		}

	});

	$('#state').on('select2:select', function () {

		$('#stateIp').addClass('has-val');
		$('#stateDiv .select2-container--default .select2-selection--single').removeClass('error');
		$('#stateDiv').removeClass('alert-validate');
		$('input[name="stateIp"]').attr("value", $('#state').select2('data')[0].text);

	});

	$('#industry').on('select2:select', function () {

		$('#industryIp').addClass('has-val');
		$('#industryDiv .select2-container--default .select2-selection--single').removeClass('error');
		$('#industryDiv').removeClass('alert-validate');
		$('input[name="industryIp"]').attr("value", $('#industry').select2('data')[0].text);

	});
	//	$('input[name="password"]').blur();

	$("body").niceScroll({
		hwacceleration: true,
		smoothscroll: true,
		cursorcolor: "white",
		cursorborder: 0,
		scrollspeed: 10,
		mousescrollstep: 20,
		cursoropacitymax: 0.7
	});

});
