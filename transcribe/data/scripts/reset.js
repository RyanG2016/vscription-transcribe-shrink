
(function ($) {
    "use strict";


    /*==================================================================
    [ Focus input ]*/
    $('.input100').each(function(){
        $(this).on('blur', function(){
            if($(this).val().trim() != "") {
                $(this).addClass('has-val');
            }
            else {
                $(this).removeClass('has-val');
            }
        })    
    })

	/*========0 Login=======================1: Signup=============================*/
	
	
    /*==================================================================
    [ Validate ]*/
    //var input = $('.validate-input .input100');
	var pwdInput = $('input[name="password"]');

    $('.validate-form').on('submit',function(){
//    $('.login100-form-btn').on('click',function(){
        var check = true;

		if(validate( pwdInput ) == false){
			showValidate(pwdInput);
			check=false;
		}

		if(check == true)
		{
			check = false;
			$(".login100-form-btn").html("Please wait..");
			$(".login100-form-btn").attr("disabled","");
			resetpw();
		}
		
        return false;
//        return check;
    });


    $('.validate-form .input100').each(function(){
        $(this).focus(function(){
           hideValidate(this);
        });
    });

	//validate function
    function validate (input) {
		const PWREGEX = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,. <>\/?]).{8,60}$/;
		if($(input).attr('type') == 'password' || $(input).attr('name') == 'password') {
			if($(input).val().match(PWREGEX) == null) {

				$.confirm({
					title: 'Password Requirements',
					type: 'red',
					// content: "Min 8 characters length<br/>Max 30 characters length<br/>One capital<br/>One lowercase<br/>One number",
					content: "<ul>\n" +
						"    <li><b>Password length should be between 8 and 60 characters</b></li>\n" +
						"    <li>at least 1 uppercase.</li>\n" +
						"    <li>at least 1 lowercase.</li>\n" +
						"    <li>at least 1 number.</li>\n" +
						"    <li>at least 1 special character.</li>\n" +
						"</ul>",
					buttons: {
						confirm: {
						btnClass: 'btn-green',
						}
					}
				});


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
    $('.btn-show-pass').on('click', function(){
        if(showPass == 0) {
            $(this).next('input').attr('type','text');
            $(this).find('i').removeClass('zmdi-eye');
            $(this).find('i').addClass('zmdi-eye-off');
            showPass = 1;
        }
        else {
            $(this).next('input').attr('type','password');
            $(this).find('i').addClass('zmdi-eye');
            $(this).find('i').removeClass('zmdi-eye-off');
            showPass = 0;
        }
        
    });


})(jQuery);


//*-------------------------------------------------------*\\
//*----------------------- JS Functions ------------------*\\
//*-------------------------------------------------------*\\


function resetpw(){
	
	var semail = window.email;
	var stoken = window.token;
	var spwd = $('input[name="password"]').val();
//	checkIfUserExist
//	alert(stoken);
	
	var a1 = {email:semail,token:stoken,password:spwd};
	$.post("data/parts/backend_request.php", {reqcode: 42,args:JSON.stringify(a1) }).done(function(){
		location.href = 'index.php';
	});	
	
	
}//reset function

/////////////////////////////////////////

//*-------------------------------------------------------*\\
//*--------------- Document Ready Scripts ----------------*\\
//*-------------------------------------------------------*\\


$(document).ready(function() {
//	
//$("#info a").click(function() {
//        toggleFormAction();
//    });
	//password tooltip
	$('input[name="password"]').popover({
		html: true,
		content: "<ul>\n" +
			"    <li><b>Password length should be between 8 and 60 characters</b></li>\n" +
			"    <li>at least 1 uppercase.</li>\n" +
			"    <li>at least 1 lowercase.</li>\n" +
			"    <li>at least 1 number.</li>\n" +
			"    <li>at least 1 special character.</li>\n" +
			"</ul>"

	});
	/*$('input[name="password"]').tooltipster({
	content:'• min  8 characters length<br/>• max 30 characters length<br/>• at least one uppercase<br/>• at least one lowercase<br/>• at least one number',
	trigger:'click',
	contentAsHTML: true,
	theme: 'tooltipster-shadow'
	});*/
	
	
    $("body").niceScroll({
		hwacceleration: true,
		smoothscroll: true,
		cursorcolor:"white",
		cursorborder:0,
		scrollspeed: 10,
		mousescrollstep: 20,
		cursoropacitymax: 0.7
	});
	
});