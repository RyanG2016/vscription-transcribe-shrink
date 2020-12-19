(function () {
    "use strict";
})(jQuery);

var loginBtn;
var email;
var pwdInput;
var pwdDiv;
var remember;
var forgotPwd;
var formTitle;
var srcIsLogin = true;
 
//*-------------------------------------------------------*\\
//*--------------- Document Ready Scripts ----------------*\\
//*-------------------------------------------------------*\\


$(document).ready(function () {

    loginBtn = $("#loginBtn");
    email = $('input[name="email"]');
    pwdInput = $('input[name="password"]');
    pwdDiv = $('#passwordDiv');
    forgotPwd = $('#forgotpwd');
    remember = $('#remember');
    formTitle = $('#title');

    email.blur();
    pwdInput.blur();
    pwdInput.attr('maxlength', 30);


    $("#forgotpwd a").click(function () {
        showResetPasswordForm();
    });


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

/*==================================================================
[ Focus input ]*/
$('.input100').each(function () {
    $(this).on('blur', function () {
        if ($(this).val().trim() !== "") {
            $(this).addClass('has-val');
        } else {
            $(this).removeClass('has-val');
        }
    })
})

/*======= 0 Login ======================*/


/*================== Validate =============================== */
var input = $('.validate-input .input100');

$('.validate-form').on('submit', function () {
    var valid = true;


    if(srcIsLogin)
    {
        for (var i = 0; i < input.length; i++) {

            switch (input[i].name) {
                // case "password":
                case "email":

                    if (validate(input[i]) === false) {
                        showValidate(input[i]);
                        valid = false;
                    }

                    break;

                default:
                    break;
            }
        }

        if (valid === true) {
            valid = false;
            login();
        }
    }
    else{ // reset pwd

        if (validate(email) === false) {
            showValidate(email);
            valid = false;
        }

        if (valid === true) {
            loginBtn.html("Please wait..");
            loginBtn.attr("disabled", "");

            valid = false;
            resetpw();
        }
    }

    return valid;

});


$('.validate-form .input100').each(function () {
    $(this).focus(function () {
        hideValidate(this);
    });
});

//validate function
function validate(input) {
    if ($(input).attr('type') === 'email' || $(input).attr('name') === 'email') {
        if ($(input).val().trim().match(/^([a-zA-Z0-9_\-.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(]?)$/) == null) {
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

function showResetPasswordForm() {
    srcIsLogin = false;
    forgotPwd.slideUp();
    pwdDiv.slideUp();
    loginBtn.html("Send Reset Link");
    loginBtn.removeAttr("disabled");
    formTitle.html('Password Reset');
    remember.slideUp();
}

function showLoginFields() {
    srcIsLogin = true;
    forgotPwd.slideDown(); // forgot your password ? reset
    remember.slideDown();
    pwdDiv.slideDown();
    loginBtn.html("Login");
    loginBtn.removeAttr("disabled");
    formTitle.html('Welcome');

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
//*----------------------- JS Functions ------------------*\\
//*-------------------------------------------------------*\\

function login() {

    loginBtn.html("Please wait..");
    loginBtn.attr("disabled", "");
    var vemail = $('.validate-input .input100[name="email"]').val();
    var vpassword = $('input[name="password"]').val();
    var vrememberme = $('input[name="remember"]').is(":checked") ? 1 : 0;
    var formData = {};

    if(vrememberme === 1){
        formData = "rememberme";
    }

    //checkIfUserExist

    $.ajax
    ({
        type: "GET",
        url: "api/v1/login/",
        // dataType: 'json',
        data: formData,
        async: false,
        beforeSend: function (xhr) {
            xhr.setRequestHeader ("Authorization", "Basic " + btoa(vemail + ":" + vpassword));
        },
        success: function (){
            location.href = 'index.php';
        },
        error: function (err) {

            if(err.responseJSON["code"] !== undefined){
                if(err.responseJSON["code"] === 5){

                    // pending email verification //
                    $.confirm({
                        title: "Error",
                        type: "orange",
                        content: err.responseJSON["msg"],
                        buttons: {
                            confirm: {
                                btnClass: 'btn-orange',
                                text: 'Ok'
                            },
                            resend: {
                                // btnClass: 'btn-green',
                                text: 'Resend Email',
                                action: function() {
                                    loginBtn.html("Please wait..");
                                    loginBtn.attr("disabled", "");

                                    var formData = new FormData();
                                    formData.append("email", vemail);

                                    $.ajax({
                                        type: 'POST',
                                        url: "/api/v1/signup/resend/",
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        success: function (response) {
                                            // var ajaxResponse = response;
                                            // console.log(response);

                                            $.confirm({
                                                title: 'Success',
                                                type: 'green',
                                                content: response["msg"],
                                                buttons: {
                                                    confirm: {
                                                        text: "OK",
                                                        btnClass: 'btn-green',
                                                        action: function () {
                                                            showLoginFields();
                                                        }
                                                    },

                                                }
                                            });

                                        },
                                        error: function (err) {

                                            $.confirm({
                                                title: 'oops..',
                                                type: 'red',
                                                content: err.responseJSON["msg"],
                                                buttons: {
                                                    confirm: {
                                                        text: "OK",
                                                        btnClass: 'btn-green',
                                                        action: function () {
                                                            showLoginFields();
                                                        }
                                                    }

                                                }
                                            });
                                        }
                                    });

                                }
                            }
                        }
                    });

                }else if(err.responseJSON["code"] === 404){

                    // Account doesn't exist //
                    $.confirm({
                        title: "Hmm..",
                        type: "red",
                        content: err.responseJSON["msg"],
                        buttons: {
                            confirm: {
                                btnClass: 'btn-red',
                                text: 'Ok'
                            },
                            signup: {
                                btnClass: 'btn-green',
                                text: 'sign up ?',
                                action: function() {
                                   location.href = "signup.php";
                                }
                            }
                        }
                    });

                }else{
                    // show error message
                    $.confirm({
                        title: "Error",
                        type: "red",
                        content: err.responseJSON["msg"],
                        buttons: {
                            confirm: {
                                btnClass: 'btn-red',
                                text: 'Ok'
                            }
                        }
                    });
                }
            }else{
                // show error message
                $.confirm({
                    title: "Error",
                    type: "red",
                    content: err.responseJSON["msg"],
                    buttons: {
                        confirm: {
                            btnClass: 'btn-red',
                            text: 'Ok'
                        }
                    }
                });
            }
            showLoginFields();
        }
    });

    //	return false;
} //end login

/////////////////////////////////////////

function resetpw() {

    var vemail = email.val();

    //checkIfUserExist

    var a1 = {
        email: vemail
    };
    $.post("data/parts/backend_request.php", {
        reqcode: 40,
        args: JSON.stringify(a1)
    }).done(function (data) {
        if (data == 1) //user exists proceed to procedures
        {
            var formData = new FormData();
            formData.append("email", vemail);
            $.ajax({
                type: 'POST',
                url: "/api/v1/login/reset/",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    // var ajaxResponse = response;
                    // console.log(response);

                    $.confirm({
                        title: 'Success',
                        type: 'green',
                        content: response["msg"],
                        buttons: {
                            confirm: {
                                text: "OK",
                                btnClass: 'btn-green',
                                action: function () {
                                    showLoginFields();
                                }
                            },

                        }
                    });

                },
                error: function (err) {

                    $.confirm({
                        title: 'oops..',
                        type: 'red',
                        content: err.responseJSON["msg"],
                        buttons: {
                            confirm: {
                                text: "OK",
                                btnClass: 'btn-green',
                                action: function () {
                                    showLoginFields();
                                }
                            }

                        }
                    });
                }
            });

        } else { //user doesn't exist show error dialog

            $.confirm({
                title: 'Hmm..',
                type: 'red',
                content: "We couldn't find your account.",
                buttons: {
                    confirm: {
                        text: 'Signup?',
                        btnClass: 'btn-green',
                        action: function () {
                            location.href = "signup.php";
                        }
                    },
                    cancel: {
                        text: "try again",
                        btnClass: 'btn-green',
                        action: function () {
                            loginBtn.html("Send Reset Link");
                            loginBtn.removeAttr("disabled");
                        }
                    },

                }
            });

        }
    });


} //reset function

