(function () {
    "use strict";
})(jQuery);

var loadingOverlay;
var loadingText;
var stateRequest;

var stateGroup;
var signupBtn;
var form;
var pwd;

// boxes
var countryBox;
var stateBox;

var countriesURL = "../api/v1/countries/";
var stateURL = "../api/v1/cities/";
var firstLaunch = true;
// - TODO NEEDS REVIEW -


//*-------------------------------------------------------*\\
//*--------------- Document Ready Scripts ----------------*\\
//*-------------------------------------------------------*\\


$(document).ready(function () {

    signupBtn = $("#signupBtn");
    loadingText = $("#loadingText");
    stateGroup = $("#stateGroup");
    countryBox = $("#countryBox");
    stateBox = $("#stateBox");
    loadingOverlay = $("#overlay");
    pwd = $("#inputPassword");
    form = document.getElementById('signupForm');

    function checkPassword() {
        const regex = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,. <>\/?]).{8,}/gm;
        const str = pwd.val();

        if(regex.test(str))
        {
            console.log("password match");
            pwd.removeClass("is-invalid");
            form.classList.add('was-validated');
            return true;
        }else{
            form.classList.remove('was-validated');
            pwd.addClass("is-invalid");
            console.log("password doesn't match");
            return false;
        }
    }

    pwd.popover({
        html: true,
        content: "<ul>\n" +
            "    <li><b>minimum length is 8 characters.</b></li>\n" +
            "    <li>at least 1 uppercase.</li>\n" +
            "    <li>at least 1 lowercase.</li>\n" +
            "    <li>at least 1 number.</li>\n" +
            "    <li>at least 1 special character.</li>\n" +
            "</ul>"

        });

    pwd.keyup(function(){
        if(firstLaunch)
        {
            firstLaunch = false;
        }else{
            checkPassword();
        }
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var valid = checkPassword() && form.checkValidity();
        if (valid === true) {
            var countryTxt = $('#countryBox option:selected').text();

            let formData = new FormData();
            let other_data = $("#signupForm").serializeArray();

            $.each(other_data, function (key, input) {
                formData.append(input.name, input.value);
            });

            formData.append("country", countryTxt);

            $.ajax({
                type: 'POST',
                // url: backend_url,
                url: api_insert_url,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {

                },
                error: function (err) {

                }
            });

        } // end if valid == true

    }, false);


    $("body").niceScroll({
        hwacceleration: true,
        smoothscroll: true,
        cursorcolor: "#f5862c",
        cursorborder: 0,
        scrollspeed: 10,
        mousescrollstep: 20,
        cursoropacitymax: 0.7
    });

    countryBox.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        // do something...
        // console.log("selection changed to: " + clickedIndex + " and prev was: " + previousValue+ "and e is ");
        // console.log(e);
        // console.log(countryBox.selectpicker('val')); // selected value
        var state = countryBox.selectpicker('val');
        if (state === "203" || state === "204") {
            loadingOverlay[0].style.display = "block";
            stateGroup[0].style.display = "block";
            loadState(countryBox.selectpicker('val'));
        } else {
            stateGroup[0].style.display = "none";
        }

    });

    loadingText.html("Loading countries..");
    loadCountries();


    // TODO NEEDS REVIEW -

});

/*================== Validate =============================== */
var input = $('.validate-input .input100');

$('.validate-form').on('submit', function () {
    var valid = true;


    if (srcIsLogin) {
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
    } else { // reset pwd

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

    if (vrememberme === 1) {
        formData = "rememberme";
    }

    //checkIfUserExist

    $.ajax
    ({
        type: "POST",
        url: "api/v1/login/",
        // dataType: 'json',
        data: formData,
        async: false,
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", "Basic " + btoa(vemail + ":" + vpassword));
        },
        success: function () {
            location.href = 'index.php';
        },
        error: function (err) {

            if (err.responseJSON["code"] !== undefined) {
                if (err.responseJSON["code"] === 5) {

                    // pending email verification //
                    $.confirm({
                        title: "Error",
                        type: "red",
                        content: err.responseJSON["msg"],
                        buttons: {
                            confirm: {
                                btnClass: 'btn-red',
                                text: 'Ok'
                            },
                            resend: {
                                btnClass: 'btn-green',
                                text: 'Resend Email',
                                action: function () {
                                    loginBtn.html("Please wait..");
                                    loginBtn.attr("disabled", "");
                                    var a1 = {
                                        email: vemail
                                    };
                                    $.post("data/parts/backend_request.php", {
                                        reqcode: 50,
                                        args: JSON.stringify(a1)
                                    }).done(function (data) {
                                        // location.href = 'index.php';
                                        $.confirm({
                                            title: 'Success',
                                            type: 'green',
                                            content: "Verification Email sent.",
                                            buttons: {
                                                confirm: {
                                                    text: "OK",
                                                    btnClass: 'btn-green',
                                                    action: function () {
                                                        showLoginFields()
                                                    }
                                                },

                                            }
                                        });
                                    });

                                }
                            }
                        }
                    });

                } else if (err.responseJSON["code"] === 404) {

                    //  //
                    $.confirm({
                        title: "Hmm..",
                        type: "red",
                        content: err.responseJSON["msg"],
                        buttons: {
                            confirm: {
                                btnClass: 'btn-red',
                                text: 'Ok'
                            },
                            /*resend: {
                                btnClass: 'btn-green',
                                text: 'sign up ?',
                                action: function() {
                                   // todo redirect to signup
                                }
                            }*/
                        }
                    });

                } else {
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
            } else {
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


function loadState(id) {
    // stateInputLbl.css("display","none");
    // stateBoxLbl.css("display","none");
    // cityContainer.css("display","none");
    loadingText.html("Loading states..");


    // removeLoadingSpinner(); // if any left over
    // stateContainer.append(generateLoadingSpinner());
    // countryBox.selectpicker('destroy');
    stateBox.selectpicker('destroy');

    if (stateRequest != null) {
        stateRequest.abort();
    }

    stateRequest = $.ajax({
        url: stateURL + id,
        method: "GET",
        success: function (states) {
            // rolesGlobal = roles;
            // updateRoleModalBtn.removeAttr("disabled");
            // const tybox = document.getElementById("roleBox");
            // console.log(countries);

            stateBox.html(""); // clear old values
            for (const state of states) {
                // console.log(country.id);
                stateBox.html(stateBox.html() +
                    "<option value='" + state.id + "'>" +
                    state.city +
                    "</option>");
            }

            stateBox.selectpicker({
                liveSearch: true,
                liveSearchPlaceholder: "Search"
            });
            stateBox.selectpicker('refresh');

            loadingOverlay[0].style.display = "none";

            /*if(!roleIsset){
                checkForSingleRoleToSet();
            }else{
                loading.style.display = "none";
            }*/
            // removeLoadingSpinner();
        }
    });

}

/////////////////////////////////////////

function loadCountries() {
    // stateInputLbl.css("display","none");
    // stateBoxLbl.css("display","none");
    // cityContainer.css("display","none");


    // removeLoadingSpinner(); // if any left over
    // stateContainer.append(generateLoadingSpinner());
    // countryBox.selectpicker('destroy');

    $.ajax({
        url: countriesURL,
        method: "GET",
        success: function (countries) {
            // rolesGlobal = roles;
            // updateRoleModalBtn.removeAttr("disabled");
            // const tybox = document.getElementById("roleBox");
            // console.log(countries);

            countryBox.html(""); // clear old values
            for (const country of countries) {
                // console.log(country.id);
                countryBox.html(countryBox.html() +
                    "<option value='" + country.id + "'>" +
                    country.country +
                    "</option>");
            }

            countryBox.selectpicker({
                liveSearch: true,
                liveSearchPlaceholder: "Search"
            });
            countryBox.selectpicker('refresh');
            /*$(".inner.show").niceScroll({
                hwacceleration: true,
                smoothscroll: true,
                cursorcolor: "#53a13d",
                cursorborder: 0,
                scrollspeed: 10,
                mousescrollstep: 20,
                cursoropacitymax: 0.7
            });*/

            loadState(203);
            /*if(!roleIsset){
                checkForSingleRoleToSet();
            }else{
                loading.style.display = "none";
            }*/
            // removeLoadingSpinner();
        }
    });

}

/////////////////////////////////////////