(function () {
    "use strict";
})(jQuery);

//*-------------------------------------------------------*\\
//*--------------- Document Ready Scripts ----------------*\\
//*-------------------------------------------------------*\\


$(document).ready(function () {

    // var stateRequest;
    // var stateGroup;
    var signupBtn;
    var signedUp = false;
    var pwd;
    var confirmPwd;
    // var prevDiv = $(".prev-btn-div");
    // var nextDiv = $(".next-btn-div");

    var currentCountry = "Canada";

    // boxes
    /*var countryBox;*/
    // var caStates = {};
    // var usStates = {};

    var countriesURL = "/data/thirdparty/typeahead/countries.json";
    // var countriesURL = "../api/v1/countries/";
    var signupURL = "../api/v1/signup/";
    var verifyURL = "verify.php";
    var loginURL = "../api/v1/login";

    // var statesJson = false;
    var countriesJson = false;

    const regex = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,. <>\/?]).{8,60}$/;


    const EMAIL_REGEX = /^[a-z0-9_]+(?:\.[a-z0-9_]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/;
    const NAME_REGEX = /^[^0-9\.\,\'\"\?\!\;\:\#\$\%\&\(\)\*\+\-\/\<\>\=\@\[\]\\\^\_\{\}\|\~]+$/;
    const ACC_REGEX = /^$|^[^\.\,\'\"\?\!\;\:\#\$\%\&\(\)\*\+\-\/\<\>\=\@\[\]\\\^\_\{\}\|\~]+$/;
    // const CITY_REGEX = /^[^\,\'\"\?\!\;\:\#\$\%\&\*\+\-\/\<\>\=\@\[\]\\\^\_\{\}\(\)\|\~]{2,}$/;
    // const CITY_FILTER_REGEX = /[^a-zA-Z0-9. ]/gi;
    // const ADDRESS_REGEX = /^[^\,\'\"\?\!\;\:\#\$\%\&\*\+\-\/\<\>\=\@\[\]\\\^\_\\(\){\}\|\~]{5,100}$/;
    const percentagePerProgress = 0.20; // in percentage eg. 0.2 = 20%

    // progress variables
    var em = false;
    var pw = false;
    var cp = false;
    var fn = false;
    var ln = false;
    var correctCount = 0;
    var maxCount = 5;

    var currentPage = 0;
    // var nextPageBtn = $("#nextBtn");
    // var prevPageBtn = $("#prevBtn");
    // var zipCode = $("#inputZip");
    // var address = $("#inputAddress");
    var tosDiv = $("#tosDiv");
    var tos = $("#tos");
    var haveAccDiv = $("#haveAccDiv");
    signupBtn = $("#signupBtn");
    email = $("#inputEmail");
    fName = $("#inputfName");
    accName = $("#inputAccName");
    lName = $("#inputlName");
    // city = $("#inputCity");
    code = $("#code");
    progressDiv = $("#formProgressDiv");
    progressBar = $("#formProgressBar");
    loginProgressDiv = $("#loginProgressDiv");
    loginProgress = $("#loginProgress");
    // stateGroup = $("#stateGroup");
    countryBox = $("#countryBox");
    // stateBox = $("#stateBox");
    carousel = $("#signupCarousel");
    pwd = $("#inputPassword");
    confirmPwd = $("#inputConfirmPassword");


    // carousel settings
    // stop autoplay
    // carousel.

    function changeProgress(itemBoolean, result)
    {
        $("body").getNiceScroll().resize();
        // to reduce unnecessary calculations
        if(itemBoolean != result)
        {
            // value changed
            var total = progressDiv.width();
            var valuePerProgress = Math.ceil(total*percentagePerProgress)+2;

            if(itemBoolean && !result)
            {
                // deduce value
                correctCount--;
            }else if(!itemBoolean && result){
                // add value
                correctCount++;
            }
            progressBar.width(correctCount * valuePerProgress);

            if(correctCount == maxCount && currentPage == 0 && !signedUp)
            {
                // allow signup
                signupBtn.removeAttr('disabled');

            }
            else if(signedUp)
            {
                signupBtn.removeAttr('disabled');
            }
            else{
                //disable signup
                signupBtn.attr('disabled','disabled');
            }
        }

        return result;
    }

    function checkAccName()
    {
        return regexCheck(accName,ACC_REGEX);
    }


    function checkName(nameIndex) {
        switch (nameIndex) {
            case 1:
                var res = regexCheck(fName, NAME_REGEX);
                changeProgress(fn, res);
                fn = res;
                return res;

                break;
            case 2:
                var res = regexCheck(lName, NAME_REGEX);
                changeProgress(ln ,regexCheck(lName, NAME_REGEX));
                ln = res;
                return res;
        }
    }
    
    function checkEmail() {
        var res = regexCheck(email, EMAIL_REGEX);
        changeProgress(em, res);
        em = res;
        return res;
    }

    function checkPassword() {
        var res2 = regexCheck(pwd, regex);
        changeProgress(pw, res2);
        pw = res2;
        checkConfirmPassword();
        return res2;
    }

    function checkConfirmPassword() {

        if(confirmPwd.val() == "")
        {
            setValidation(confirmPwd, false);
            changeProgress(cp , false);
            cp = false;
            return false;
        }else{
            if(pwd.val() === confirmPwd.val())
            {
                setValidation(confirmPwd,true);
                changeProgress(cp, true);
                cp = true;
                return true;
            }else{
                setValidation(confirmPwd, false);
                changeProgress(cp , false);
                cp = false;
                return false;
            }
        }
    }

    function regexCheck(item, regx){
        if(regx.test(item.val()))
        {
            setValidation(item, true);
            return true;
        }else{
            setValidation(item, false);
            return false;
        }
    }

    function setValidation(item, bool)
    {
        if(bool)
        {
            item.removeClass("is-invalid");
            item.addClass("is-valid");
        }
        else{
            item.removeClass("is-valid");
            item.addClass("is-invalid");
        }
    }


    pwd.popover({
        html: true,
        placement: "top",
        content: "<ul>\n" +
            "    <li><b>Password length should be between 8 and 60 characters</b></li>\n" +
            "    <li>at least 1 uppercase.</li>\n" +
            "    <li>at least 1 lowercase.</li>\n" +
            "    <li>at least 1 number.</li>\n" +
            "    <li>at least 1 special character.</li>\n" +
            "</ul>"

        });

    email.keyup(function() {
        checkEmail();
    });


    accName.keyup(function() {
        checkAccName();
    });

    pwd.keyup(function(){
        checkPassword();
    });

    confirmPwd.keyup(function(){
        checkConfirmPassword();
    });

    fName.keyup(function(){
        checkName(1);
    });

    lName.keyup(function(){
        checkName(2);
    });

    carousel.on('slide.bs.carousel', function(e){
        /*e.direction     // The direction in which the carousel is sliding (either "left" or "right").
        e.relatedTarget // The DOM element that is being slid into place as the active item.
        e.from          // The index of the current item.
        e.to            // The index of the next item.*/
        var nextH = $(e.relatedTarget).height();
        $(this).find('.carousel-item.active').parent().animate({
            height: nextH
        }, 500)
            .promise().done(function () {


            // fix height problems with error sub-lines
            setTimeout( function(){
                $('.carousel-inner').height("100%");
            }  , 100 );
        });

        currentPage = e.to;

        if(correctCount == maxCount && currentPage == 0 && !signedUp)
        {
            // allow signup
            signupBtn.removeAttr('disabled');
        }else{
            //disable signup
            signupBtn.attr('disabled','disabled');
            // console.log("here2");
        }
        // alert("moving to page " + e.to);
        // var nextH = $(e.relatedTarget).height();
    });

    carousel.on('slid.bs.carousel', function(e){
        //focus set
        switch (e.to) {
            case 0:
                email.focus();
                break;
            case 1:
                code.focus();
                break;
        }
        $("body").getNiceScroll().resize();
    });



    /*nextPageBtn.click(function() {
        switch (currentPage) {

            case 0:
                carousel.carousel(1);
                prevDiv.show();
                break;
            case 1:
                carousel.carousel(2);
                nextDiv.hide();
                break;

            // wont happen
            case 2:
                break;
        }
    });*/

  /*  prevPageBtn.click(function() {

        switch (currentPage) {

            // wont happen
            // case 0:
            //     break;
            case 1:
                carousel.carousel(0);
                prevDiv.hide();
                break;
            case 2:
                carousel.carousel(1);
                nextDiv.show();
                break;
        }
    });*/

    function checkAll(){
        var pass = false;
        if (checkEmail() &&
            checkPassword() &&
            checkConfirmPassword() &&
            checkName(1) &&
            checkName(2)) {

            if(!ref)
            {
                if(checkAccName() && checkEmail())
                {
                    pass = true;
                }
            }else{
                pass = true;
            }
        }
        return pass;
    }

    signupBtn.on("click", function() {

        if(!checkAll())
        {
            // console.log("max " + maxCount + "<br> correct " + correctCount );
            $.confirm({
                title: 'Error',
                content: 'Please check your information',
                type: 'red',
                typeAnimated: true,
                buttons: {
                    ok: {
                        text: 'OK'
                        // btnClass: 'btn-red'
                    },
                    close: {isHidden: true}
                }
            });
        }else{
            if(tos.prop('checked') !== true)
            {
                // tos must be checked
                $.alert({
                    title: 'TOS',
                    type: 'orange',
                    content: 'You must agree to the Terms of Service',
                    theme: 'supervan',
                    columnClass: 'col-8'
                });
                return;
            }
            // information are correct proceed with signup process
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

            // proceed with signup ajax

            $.confirm({
                title: 'Signup',
                theme: 'supervan',
                columnClass: 'col-8',
                content: function(){
                    var self = this;
                    // self.setContent('Checking callback flow');
                    return $.ajax({
                        type: 'POST',
                        method: 'POST',
                        url: signupURL,
                        data: formData,
                        processData: false,
                        contentType: false
                    }).done(function (response) {

                        // handle responses
                        // -------------

                        if(!response.error)
                        {
                            signedUp = true;
                            setUIforVerification();

                            self.setTitle("Thanks for Signing Up!");
                            self.setType("green");
                            self.setContent(response["msg"]);

                            self.buttons.ok.setText("Ok");
                            self.buttons.ok.addClass("btn-green");
                            self.buttons.ok.removeClass("btn-default");
                            self.buttons.close.hide();
                        }else{
                            if(response.code != null && response.code === 301)
                            {
                                // redirect to login screen

                                self.setTitle("oops..");
                                self.setType("red");
                                self.setContent(response.msg);

                                self.buttons.ok.setText("Yes");
                                self.buttons.ok.addClass("btn-green");
                                self.buttons.ok.removeClass("btn-default");

                                self.buttons.ok.action = function(){
                                    location.href = "index.php";
                                };

                                self.buttons.close.setText("No");
                                self.buttons.close.show();


                            }else {
                                self.setTitle("oops..");
                                self.setType("red");
                                self.setContent(response.msg);
                                self.buttons.ok.setText("Ok");
                                self.buttons.ok.addClass("btn-green");
                                // self.buttons.ok
                                // self.buttons.ok.btnClass = "btn-green"
                                self.buttons.ok.removeClass("btn-default")
                                self.buttons.close.hide();
                            }
                        }


                        // self.setContentAppend('<div>Done!</div>');

                    }).fail(function(xhr, status, err){
                        self.setTitle("oops..");
                        self.setType("red");
                        self.setContent(xhr.responseJSON["msg"]);
                        self.buttons.ok.setText("Ok");
                        self.buttons.ok.addClass("btn-green");
                        // self.buttons.ok
                        // self.buttons.ok.btnClass = "btn-green"
                        self.buttons.ok.removeClass("btn-default")
                        self.buttons.close.hide();
                    })
/*                        .always(function(){
                        // self.setContentAppend('<div>Always!</div>');
                        /!*self.setContent('Description: ' + response.description);
                        self.setContentAppend('<br>Version: ' + response.version);
                        self.setTitle(response.name);*!/
                    });*/

                }/*
                ,
                contentLoaded: function(data, status, xhr){
                    // self.setContentAppend('<div>Content loaded!</div>');
                },
                onContentReady: function(){
                    // this.setContentAppend('<div>Content ready!</div>');
                }*/
            });

        }
    });

    // DEBUG testing verification pages here--
    // signedUp = true;
    // loginUser();
    // carousel.carousel(3);
    // setUIforVerification();

    function loginUser(){
        carousel.carousel(2);
        signupBtn.hide();
        // prevDiv.hide();
        // nextDiv.hide();
        progressDiv.hide();
        loginProgressDiv.show();

        login();
    }


    function login() {
        var vemail = email.val();
        var vpassword = pwd.val();
        var vrememberme = 0;
        var formData = {};

        /*if(vrememberme === 1){
            formData = "rememberme";
        }*/


        $.confirm({
            title: 'Login',
            theme: 'supervan',
            columnClass: 'col-8',
            content: function(){
                var self = this;
                // self.setContent('Checking callback flowChecking callback flow');
                return $.ajax({
                    type: 'GET',
                    method: 'GET',
                    url: loginURL,
                    data:formData,
                    async: false,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Basic " + btoa(vemail + ":" + vpassword));
                    },
                }).done(function (response) {
                    self.setTitle("Success");
                    self.setContent("Logging in, redirecting..");

                    self.buttons.ok.hide();
                    self.buttons.close.hide();
                    location.href = "index.php";
                }).fail(function(xhr, status, err){

                    self.setTitle("oops..");
                    self.setType("red");
                    self.setContent("Something went wrong, press ok to go to login page");
                    self.buttons.ok.setText("Ok");
                    self.buttons.close.hide();

                    self.buttons.ok.action = function(){location.href = "index.php"};

                })
            }
        });


    } //end login


    function setUIforVerification()
    {
        $("#loginHyperLink").hide();
        carousel.carousel(1);
        $("#title").html("Verify Your Account");
        signupBtn.off("click");
        signupBtn.html("Verify");
        // prevDiv.hide();
        // nextDiv.hide();
        signupBtn.removeAttr('disabled');
        progressDiv.hide();
        tosDiv.hide();


        signupBtn.click(function(){
            /*alert(
                "trying to verify account with code: " + code.val() + " for account email: " + email.val()
            );*/

            // verifying account
            $.confirm({
                title: 'Signup',
                theme: 'supervan',
                columnClass: 'col-8',
                content: function(){
                    var self = this;

                    return $.ajax({
                        type: 'GET',
                        method: 'GET',
                        url: verifyURL,
                        data: { notify: "1", user: email.val(), token: code.val() } ,
                    }).done(function (response) {
                        result = JSON.parse(response);
                        error = result["error"];
                        msg = result["msg"];
                        // handle responses
                        // -------------
                        self.setTitle(error?"oops..":"Success");
                        self.setType(error?"red":"green");
                        self.setContent(msg);

                        self.buttons.ok.setText("Ok");
                        self.buttons.ok.addClass("btn-green");
                        self.buttons.ok.removeClass("btn-default");
                        self.buttons.close.hide();

                        if(!error)
                        {
                            self.close();
                            loginUser();
                        }

                    }).fail(function(xhr, status, err){

                        self.setTitle("oops..");
                        self.setType("red");
                        self.setContent(xhr.responseJSON["msg"]);
                        self.buttons.ok.setText("Ok");
                        self.buttons.close.hide();

                    })
                }
            });

        });

    }



    $("body").niceScroll({
        hwacceleration: true,
        smoothscroll: true,
        cursorcolor: "silver",
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
        currentCountry = countryBox.selectpicker('val');
        // var state = countryBox.selectpicker('val');
        // if (state === "203" || state === "204") {
        //     $("#stateSpin")[0].style.display = "block";
        //     stateGroup[0].style.display = "block";
        //     // loadState(countryBox.selectpicker('val'));
        // } else {
        //     stateGroup[0].style.display = "none";
        // }

    });

    loadCountries();


    //*-------------------------------------------------------*\\
//*----------------------- JS Functions ------------------*\\
//*-------------------------------------------------------*\\

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
                        "<option value='" + country + "'>" +
                        country +
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

                // loadState(203);
                /*if(!roleIsset){
                    checkForSingleRoleToSet();
                }else{
                    loading.style.display = "none";
                }*/
                // removeLoadingSpinner();
            }
        });

    }
    email.keyup();

    pwd.focusout(function(){
        if (!checkPassword()) {
             pwd.popover('show')
         } else {
             pwd.popover('hide')
         }
    });

/////////////////////////////////////////
});


