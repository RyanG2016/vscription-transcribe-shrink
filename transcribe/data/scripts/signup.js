(function () {
    "use strict";
})(jQuery);

var stateRequest;

var stateGroup;
var signupBtn;
var form;
var pwd;
var confirmPwd;

// boxes
var countryBox;
var stateBox;

var countriesURL = "../api/v1/countries/";
var stateURL = "../api/v1/cities/";
var signupURL = "../api/v1/signup/";
var firstLaunch = true;

//*-------------------------------------------------------*\\
//*--------------- Document Ready Scripts ----------------*\\
//*-------------------------------------------------------*\\


$(document).ready(function () {

    signupBtn = $("#signupBtn");
    stateGroup = $("#stateGroup");
    countryBox = $("#countryBox");
    stateBox = $("#stateBox");
    pwd = $("#inputPassword");
    confirmPwd = $("#inputConfirmPassword");
    form = document.getElementById('signupForm');

    function checkPassword() {
        const regex = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,. <>\/?]).{8,}/gm;
        const str = pwd.val();

        if(regex.test(str))
        {
            // console.log("password match");
            pwd.removeClass("is-invalid");

            // check for confirm password
            if(pwd.val() === confirmPwd.val())
            {
                form.classList.add('was-validated');
                confirmPwd.removeClass("is-invalid");
                return true;
            }else{
                form.classList.remove('was-validated');
                confirmPwd.addClass("is-invalid");
            }
        }else{
            form.classList.remove('was-validated');
            pwd.addClass("is-invalid");
            // console.log("password doesn't match");
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

    confirmPwd.keyup(function(){
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
            if(ref)
            {
                formData.append("ref", ref);
            }

            $.ajax({
                type: 'POST',
                url: signupURL,
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
                                    location.href = "index.php";
                                }
                            },

                        }
                    });

                },
                error: function (err) {
                    // var ajaxError = err;
                    // console.log(err);

                    if(err.responseJSON["code"] != null && err.responseJSON["code"] == 301)
                    {
                        $.confirm({
                            title: 'oops..',
                            type: 'red',
                            content: err.responseJSON["msg"],
                            buttons: {
                                confirm: {
                                    text: "Yes",
                                    btnClass: 'btn-green',
                                    action: function () {
                                        location.href = "index.php";
                                    }
                                }
                                ,cancel: {
                                    text: "No",
                                    btnClass: 'btn-red'
                                }

                            }
                        });
                    }else {
                        $.confirm({
                            title: 'oops..',
                            type: 'red',
                            content: err.responseJSON["msg"],
                            buttons: {
                                confirm: {
                                    text: "OK",
                                    btnClass: 'btn-green'
                                }

                            }
                        });
                    }

                }
            });

        }

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
            $("#stateSpin")[0].style.display = "block";
            stateGroup[0].style.display = "block";
            loadState(countryBox.selectpicker('val'));
        } else {
            stateGroup[0].style.display = "none";
        }

    });

    loadCountries();

});


//*-------------------------------------------------------*\\
//*----------------------- JS Functions ------------------*\\
//*-------------------------------------------------------*\\


function loadState(id) {
    // stateInputLbl.css("display","none");
    // stateBoxLbl.css("display","none");
    // cityContainer.css("display","none");


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
            $("#stateSpin")[0].style.display = "none";
            stateBox.display = "block";

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
            $("#countrySpin")[0].style.display = "none";

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