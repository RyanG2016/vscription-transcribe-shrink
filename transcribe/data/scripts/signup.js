(function () {
    "use strict";
})(jQuery);

//*-------------------------------------------------------*\\
//*--------------- Document Ready Scripts ----------------*\\
//*-------------------------------------------------------*\\


$(document).ready(function () {

    var stateRequest;
    var stateGroup;
    var signupBtn;
    var form;
    var pwd;
    var confirmPwd;
    var prevDiv = $(".prev-btn-div");
    var nextDiv = $(".next-btn-div");

    // boxes
    var countryBox;
    var stateBox;

    var countriesURL = "../api/v1/countries/";
    var stateURL = "../api/v1/cities/";
    var signupURL = "../api/v1/signup/";

    const regex = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,. <>\/?]).{8,60}$/;
    const EMAIL_REGEX = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;
    const NAME_REGEX = /^[^0-9\.\,\'\"\?\!\;\:\#\$\%\&\(\)\*\+\-\/\<\>\=\@\[\]\\\^\_\{\}\|\~]+$/;
    const ACC_REGEX = /^$|^[^0-9\.\,\'\"\?\!\;\:\#\$\%\&\(\)\*\+\-\/\<\>\=\@\[\]\\\^\_\{\}\|\~]+$/;
    const percentagePerProgress = 0.2; // in percentage eg. 0.2 = 20%

    // progress variables
    var em = false;
    var pw = false;
    var cp = false;
    var fn = false;
    var ln = false;
    var correctCount = 0;
    var maxCount = 5;

    var currentPage = 0;
    var nextPageBtn = $("#nextBtn");
    var prevPageBtn = $("#prevBtn");
    signupBtn = $("#signupBtn");
    email = $("#inputEmail");
    fName = $("#inputfName");
    accName = $("#inputAccName");
    lName = $("#inputlName");
    city = $("#inputCity");
    code = $("#code");
    progressDiv = $(".progress");
    progress = $(".progress-bar");
    stateGroup = $("#stateGroup");
    countryBox = $("#countryBox");
    stateBox = $("#stateBox");
    carousel = $("#signupCarousel");
    pwd = $("#inputPassword");
    confirmPwd = $("#inputConfirmPassword");


    // carousel settings
    // stop autoplay
    // carousel.

    function setUIforVerification()
    {
        signupBtn.html("Verify");
        prevDiv.hide();
        nextDiv.hide();
        signupBtn.removeAttr('disabled');
        progressDiv.hide();

        signupBtn.off("click");
        signupBtn.click(function(){
            alert(
                "trying to verify account with code: " + code.val() + " for account email: " + email.val()
            );
        })
    }

    function changeProgress(itemBoolean, result)
    {
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
            progress.width(correctCount * valuePerProgress);

            if(correctCount == maxCount && currentPage == 2)
            {
                // allow signup
                signupBtn.removeAttr('disabled');
            }else{
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

        if(correctCount == maxCount && currentPage == 2)
        {
            // allow signup
            signupBtn.removeAttr('disabled');
        }else{
            //disable signup
            signupBtn.attr('disabled','disabled');
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
                fName.focus();
                break;
            case 2:
                accName.focus();
                break;
        }
        $("body").getNiceScroll().resize();
    });



    nextPageBtn.click(function() {
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
    });

    prevPageBtn.click(function() {

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
    });

    function checkAll(){
        var pass = false;
        if (checkEmail() &&
            checkPassword() &&
            checkConfirmPassword() &&
            checkAccName() &&
            checkName(1) &&
            checkName(2)) {
            pass = true;
        }
        return pass;
    }

    signupBtn.click(function() {

        if(!checkAll())
        {
            console.log("max " + maxCount + "<br> correct " + correctCount );
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
                content: function(){
                    var self = this;
                    self.setContent('Checking callback flow');
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
                        carousel.carousel(3);
                        setUIforVerification();

                        self.setTitle("Success");
                        self.setType("green");
                        self.setContent(response["msg"]);

                        self.buttons.ok.setText("Yes");
                        self.buttons.ok.addClass("btn-green");
                        self.buttons.ok.removeClass("btn-default");
                        self.buttons.close.hide();

                        // self.setContentAppend('<div>Done!</div>');

                    }).fail(function(xhr, status, err){

                        if(xhr.responseJSON["code"] != null && xhr.responseJSON["code"] == 301)
                        {
                            // redirect to login screen

                            self.setTitle("oops..");
                            self.setType("red");
                            self.setContent(xhr.responseJSON["msg"]);

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
                            self.setContent(xhr.responseJSON["msg"]);
                            self.buttons.ok.setText("Ok");
                            self.buttons.ok.addClass("btn-green");
                            // self.buttons.ok
                            // self.buttons.ok.btnClass = "btn-green"
                            self.buttons.ok.removeClass("btn-default")
                            self.buttons.close.hide();
                        }

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
});


