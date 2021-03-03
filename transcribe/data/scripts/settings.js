var currentRole;
var currentAccName;
var updateRoleModalBtn;
var setDefaultRoleBtn;
var changeRoleBtn;
var accNameInput;

var accessId;

const CREATE_ACC_URL = "../api/v1/accounts/?out";

// -- combobox vars -- //

var typistAvSwitch;
var typistAvSwitchMDC;

var srSwitchMDC;
var srOwnSwitchMDC;

var roleIsset; //-> from PHP (landing.php)
var redirectID; //-> from PHP (landing.php) this is current role ID but named redirect for reasons

$(document).ready(function () {

    const UPDATE_USER_URL = "../api/v1/users/update";
    const UPDATE_ORGANIZATION_URL = "../api/v1/accounts/update";
    const UPDATE_SELF_ORGANIZATION_URL = "../api/v1/accounts/update/self";

    let loading = $("#overlay");
    const CITY_REGEX = /^[^\,\'\"\?\!\;\:\#\$\%\&\*\+\-\/\<\>\=\@\[\]\\\^\_\{\}\(\)\|\~]{2,}$/;
    const CITY_FILTER_REGEX = /\(.*| st.*|[^a-zA-Z0-9. ]/gi;
    // changeLoading(true);

    $("#typistWorkHelp").popover({
        html: true,
        trigger: 'hover',
        content: "<div>" +
            "<b>This allows administrators to be able to invite you as a typist for their jobs.</b>" +
            "<br><br>" +
            "<i>This doesn't affect your current jobs</i>" +
            "</div>"
    });

    $(".vtex-help-icon").each(function () {
        $(this).popover({
            html: true,
            trigger: 'hover',
            content: "<div>" +
                "<b>This will enable automated speech recognition to all of your next job uploads<br><br></b>" +
                // "<br><br>" +
                "" +
                "<i>This doesn't affect your current jobs</i>" +
                "</div>"
        });
    });


    //<editor-fold desc="Tutorial Snippet to copy">
    /**
     * Copy this fold to any JS file for any page and just edit the following
     * 1. enjoyhint_script_steps -> steps of the tutorial text and screen items to be highlighted
     * 2. tutorialViewed function -> ajax 'url' relative path MAY need to be edited
     * finally copy over the following to the page PHP code before the initializing of the JS file
     *
     <?php $tuts=(isset($_SESSION['tutorials']))?$_SESSION['tutorials']:'{}'; ?>
     <script type="text/javascript">
     var tutorials='<?php echo $tuts;?>';
     </script>
     */
    //initialize instance
    /*    var enjoyhint_instance
            = new EnjoyHint({
            onEnd:function(){
                tutorialViewed();
            },
            onSkip:function(){
                tutorialViewed();
            }
        });

        //simple config.
        //Only one step - highlighting(with description) "New" button
        //hide EnjoyHint after a click on the button.
        var enjoyhint_script_steps = [
            {
                "next #adminCard": "Start here by setting up your account. This needs to done before you can upload jobs",
                shape:"circle"
            }
            ,
            {
                "next .navbar-text": "Here you will find your login name, current account and role within that account"
            }
            ,
            {
                "next #typistCard>div":'Here you can find information about your current access permissions',
                // shape:"circle",
            }
            ,
            {
                "click #alertT2":'Here you can set whether or not you\'re open for new work invites from other accounts.',
                // shape:"circle",
                "skipButton":{text: "Finish"}
            }
        ];

        //set script config
        enjoyhint_instance.set(enjoyhint_script_steps);

        // get page name
        const currentPageName = location.pathname.split("/").slice(-1)[0].replace(".php","");
        // parse user tutorials data to JSON
        var tutorialsJson = JSON.parse(tutorials);
        // check if tutorial for the current page isn't viewed before
        if(tutorialsJson[currentPageName] == undefined || tutorialsJson[currentPageName] == 0){
            // show tutorial
            enjoyhint_instance.run();
        }

        function tutorialViewed() {
            var formData = new FormData();
            formData.append("page", currentPageName);
            $.ajax({
                type: 'POST',
                url: "../api/v1/users/tutorial-viewed/",
                processData: false,
                data: convertToSearchParam(formData)
            });
        }*/
    //</editor-fold>


    // console.log("tutorials variable: " + tutorials);
    // console.log(JSON.parse(tutorials));
    //run Enjoyhint script
    //     enjoyhint_instance.run();

    let loadingText = $("#loadingText");
    let typistAvSwitch = $("#typist_av_switch");
    let srSwitch = $("#srSwitch");
    let srOwnSwitch = $("#srOwnSwitch");
    let srMinutes = $("#srMinutes");
    let srOwnMinutes = $("#srOwnMinutes");
    let accNameInput = $("#accNameTxt");
    let currentRole = $("#currentRole");
    let currentAccName = $("#currentAccountName");
    let accessId = $("#accessId");
    let zip = $("#zip");
    let city = $("#city");
    let state = $("#state");
    let userForm = $("#userForm");
    let orgForm = $("#orgForm");
    let ownOrgForm = $("#ownOrgForm");
    let country = $("#country");
    let newsletter = $("#newsletter");
    let jobUpdates = $("#emailTranscript");
    let email = $("#email");
    let lastZipRequested = "";
    let currentEmail = email.val();

    /*    userForm.parsley({
            /!*errorsWrapper: '<br><ul class="parsley-error-list"></ul>'*!/
            errorsContainer: function (field) {
                return field.$element.closest('.block, .control')
            }

        }).on('field:validated', function() {
            var ok = $('.parsley-error').length === 0;
            $('.bs-callout-info').toggleClass('hidden', !ok);
            $('.bs-callout-warning').toggleClass('hidden', ok);
        })
            .on('form:submit', function() {
                return false; // Don't submit form
            });*/

    userForm.parsley().on('form:submit', function () {

        var formData = new FormData(userForm[0]);
        formData.append("newsletter", newsletter.hasClass("active") ? "1" : "0");
        formData.append("email_notification", jobUpdates.hasClass("active") ? "1" : "0");

        if(email.val() !== currentEmail)
        {
            // inform user of a possible logout
            $.confirm({
                title: 'Important!',
                // theme: 'bootstrap',
                type: 'orange',
                columnClass: 'col-6',
                content: 'By changing your email address you will be logged out until your account is verified by visiting the link mailed to you.',
                buttons: {
                    confirm: function () {
                        // $.alert('Confirmed!');
                        // proceed
                        updateUserInfo(formData, true);
                    },
                    cancel: function () {
                        return true;
                    }
                }
            });
        }else{
            updateUserInfo(formData);
        }

        return false; // Don't submit form
    });

    function updateUserInfo(formData, reload = false){
        $.confirm({
            title: 'Updating User Info',
            theme: 'supervan',
            columnClass: 'col-8',
            content: function(){
                var self = this;
                // self.setContent('Checking callback flow');
                return $.ajax({
                    type: 'POST',
                    method: 'POST',
                    url: UPDATE_USER_URL,
                    data: formData,
                    processData: false,
                    contentType: false
                }).done(function (response) {

                    // handle responses
                    // -------------

                    self.setTitle("User Updated!");
                    self.setType("green");
                    // self.setContent(response["msg"]);
                    self.setContent("");

                    self.buttons.ok.setText("Ok");
                    self.buttons.ok.addClass("btn-green");
                    self.buttons.ok.removeClass("btn-default");
                    self.buttons.close.hide();

                    if(reload)
                    {
                        location.reload();
                    }

                    // self.setContentAppend('<div>Done!</div>');

                }).fail(function(xhr, status, err){

                        self.setTitle("Oops..");
                        self.setType("red");
                        self.setContent(xhr.responseJSON["msg"]);

                        self.buttons.ok.setText("Ok");
                        self.buttons.ok.addClass("btn-green");
                        self.buttons.ok.removeClass("btn-default");

                        self.buttons.ok.action = function(){
                            // location.href = "index.php";
                            // location.reload();
                        };

                        self.buttons.close.hide();

                }).always(function () {
                    userForm.find(".parsley-success").each(function(){
                        $(this).removeClass("parsley-success");
                    });
                })

            }
        });
    }

    if (roleIsset && redirectID != 3) {
        orgForm.parsley().on('form:submit', function () {

            var formData = new FormData(orgForm[0]);
            $.confirm({
                title: 'Updating Organization Info',
                theme: 'supervan',
                columnClass: 'col-8',
                content: function () {
                    var self = this;
                    // self.setContent('Checking callback flow');
                    return $.ajax({
                        type: 'POST',
                        method: 'POST',
                        url: UPDATE_ORGANIZATION_URL,
                        data: formData,
                        processData: false,
                        contentType: false
                    }).done(function (response) {

                        // handle responses
                        // -------------
                        let success = !response.error;
                        if (success) {
                            self.setTitle("Organization Updated!");
                            self.setType("green");
                            // self.setContent(response["msg"]);
                            self.setContent("");

                            self.buttons.ok.setText("Ok");
                            self.buttons.ok.addClass("btn-green");
                            self.buttons.ok.removeClass("btn-default");
                            self.buttons.close.hide();
                        }else{
                            self.setTitle("Oops..");
                            self.setType("red");
                            self.setContent(response.msg);

                            self.buttons.ok.setText("Ok");
                            self.buttons.ok.addClass("btn-green");
                            self.buttons.ok.removeClass("btn-default");

                            self.buttons.ok.action = function () {
                                // location.href = "index.php";
                                // location.reload();
                            };

                            self.buttons.close.hide();
                        }

                        // self.setContentAppend('<div>Done!</div>');

                    }).fail(function (xhr, status, err) {

                        self.setTitle("Oops..");
                        self.setType("red");
                        self.setContent(xhr.responseJSON["msg"]);

                        self.buttons.ok.setText("Ok");
                        self.buttons.ok.addClass("btn-green");
                        self.buttons.ok.removeClass("btn-default");

                        self.buttons.ok.action = function () {
                            // location.href = "index.php";
                            // location.reload();
                        };

                        self.buttons.close.hide();

                    }).always(function () {
                        orgForm.find(".parsley-success").each(function () {
                            $(this).removeClass("parsley-success");
                        });
                    })

                }
            });

            return false; // Don't submit form
        });
    }

    if(hasOwnOrg && !ownMatchesCurrent)
    {
        ownOrgForm.parsley().on('form:submit', function () {
            var formData = new FormData(ownOrgForm[0]);
            $.confirm({
                title: 'Updating Organization Info',
                theme: 'supervan',
                columnClass: 'col-8',
                content: function(){
                    var self = this;
                    // self.setContent('Checking callback flow');
                    return $.ajax({
                        type: 'POST',
                        method: 'POST',
                        url: UPDATE_SELF_ORGANIZATION_URL,
                        data: formData,
                        processData: false,
                        contentType: false
                    }).done(function (response) {

                        // handle responses
                        // -------------

                        self.setTitle("Organization Updated!");
                        self.setType("green");
                        // self.setContent(response["msg"]);
                        self.setContent("");

                        self.buttons.ok.setText("Ok");
                        self.buttons.ok.addClass("btn-green");
                        self.buttons.ok.removeClass("btn-default");
                        self.buttons.close.hide();

                        // self.setContentAppend('<div>Done!</div>');

                    }).fail(function(xhr, status, err){

                        self.setTitle("Oops..");
                        self.setType("red");
                        self.setContent(xhr.responseJSON["msg"]);

                        self.buttons.ok.setText("Ok");
                        self.buttons.ok.addClass("btn-green");
                        self.buttons.ok.removeClass("btn-default");

                        self.buttons.ok.action = function(){
                            // location.href = "index.php";
                            // location.reload();
                        };

                        self.buttons.close.hide();

                    }).always(function () {
                        ownOrgForm.find(".parsley-success").each(function(){
                            $(this).removeClass("parsley-success");
                        });
                    })

                }
            });

            return false; // Don't submit form
        });
    }


    const zippoURL = "https://api.zippopotam.us/";
    zip.keyup(function () {
        // check for matching regex
        var CA_REGEX = /^[a-zA-Z0-9]{3}$|^[a-zA-Z0-9]{6}$|^[a-zA-Z0-9]{3} [a-zA-Z0-9]{3}$/;
        var US_REGEX = /^[0-9]{5}$/;
        zipValue = zip.val();

        switch (zipValue.length) {

            // CA
            case 3:
            case 6:
                if (CA_REGEX.test(zipValue)) {
                    lookupZip(zipValue.slice(0, 3), "ca");
                }
                break;

            // US
            case 5:
                if (US_REGEX.test(zipValue)) {
                    lookupZip(zipValue, "us");
                }
                break;
        }

    });


    function lookupZip(zipCode, countryLookup) {
        if (lastZipRequested != zipCode) {
            $.get(zippoURL + countryLookup + "/" + zipCode, function () {
            })
                .done(function (response) {
                    city.val(response["places"][0]["place name"].replace(CITY_FILTER_REGEX, "").trim());
                    // checkCity();

                    state.val(response["places"][0]["state"]);

                    if (countryLookup === "ca") {
                        country.typeahead('val', "Canada");
                        // enableCaEngine();
                        // calculateTaxes();
                    } else if (countryLookup === "us") {
                        country.typeahead('val', "United States");
                        // enableUsEngine();
                    }
                });
        }

        lastZipRequested = zip;
    }

    var countriesEngine = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        // url points to a json file that contains an array of country names, see
        // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
        prefetch: '/data/thirdparty/typeahead/countries.json'
    });

// passing in `null` for the `options` arguments will result in the default
// options being used
    country.typeahead(null, {
        name: 'countries',
        source: countriesEngine
    }).blur(function () {
        let match = false;
        for (var i = 0; i < Object.keys(countriesEngine.index.datums).length; i++) {
            let currentCountry = $(this).val();
            if (currentCountry == Object.keys(countriesEngine.index.datums)[i]) {
                match = true;
                // countryCheck = true;
                country.removeClass("vtex-err-border");

                if (currentCountry === "United States") {
                    // enableUsEngine();
                    state.blur();
                } else if (currentCountry === "Canada") {
                    // enableCaEngine();
                    state.blur();
                } else {
                    // disableEngines();
                }
                break;
            }
        }
        if (!match) {
            // console.log("Invalid Selection");
            country.addClass("vtex-err-border");
            // countryCheck = false;
        }
    });

    $.each($(".twitter-typeahead"), function () {
        // Do something
        $(this).addClass("col");
        this.style.padding = 0;
    });


    typistAvSwitchMDC = new mdc.switchControl.MDCSwitch(document.querySelector('#typist_av_switch'));


    typistAvSwitch.on('change', function (e) {
        typistAvSwitchMDC.disabled = true;
        if (typistAvSwitchMDC.checked) {
            setAvailabilityAsTypist(1);
        } else {
            setAvailabilityAsTypist(2);
        }
    });

    if (roleIsset && (redirectID == 1 || redirectID == 2)) {
        srSwitchMDC = new mdc.switchControl.MDCSwitch(document.querySelector('#srSwitch'));

        srSwitch.on('change', function (e) {
            srSwitchMDC.disabled = true;
            if (srSwitchMDC.checked) {
                setSRenabled(1);
            } else {
                setSRenabled(0);
            }
        });

        getSRenabled();

        // get remaining minutes balance
        getSRMinutes();
    }

    if(hasOwnOrg && !ownMatchesCurrent)
    {
        srOwnSwitchMDC = new mdc.switchControl.MDCSwitch(document.querySelector('#srOwnSwitch'));
        srOwnSwitch.on('change', function (e) {
            srOwnSwitchMDC.disabled = true;
            if (srOwnSwitchMDC.checked) {
                setSROwnEnabled(1);
            } else {
                setSROwnEnabled(0);
            }
        });

        getOwnSRenabled();
        getOwnSRMinutes();
    }

    accNameInput.keyup(function () {
        validAccName();
    });

    $("#createAdminAccBtn").on("click", function (e) {
        if (validAccName()) {
            changeLoading(true, "Creating account...");
            $('#createAccModal').modal('hide'); // close the create dialog
            // block UI and create the account
            var formData = new FormData();
            formData.append("acc_name", accNameInput.val());

            $.ajax({
                type: 'POST',
                url: CREATE_ACC_URL,
                data: formData,
                processData: false,
                contentType: false,
                // success: function (response) {
                success: function () {

                    loadingText.html("Account created!, redirecting..");
                    setTimeout(function () {
                            location.reload();
                        }
                        , 1000);
                },
                error: function (err) {
                    changeLoading(false);
                    $.confirm({
                        title: 'Error',
                        content: err.responseJSON["msg"],
                        buttons: {
                            confirm: {
                                btnClass: 'btn-red'
                            }
                        }
                    });
                }
            });
        }
    });


    function changeLoading(show, text = false) {
        if(!show){
            loading.fadeOut();
            $("body").css("overflow", "auto");
        }else{
            $("body").css("overflow", "none");
            if(text) loadingText.html(text);
            loading.fadeIn();
        }
    }


    getAvailabilityAsTypist();

    function getAvailabilityAsTypist() {
        $.ajax({
            url: "../api/v1/users/available/",
            method: "GET",
            success: function (available) {
                if (available == 1) {
                    typistAvSwitchMDC.checked = true;
                } else {
                    typistAvSwitchMDC.checked = false;
                }
                typistAvSwitchMDC.disabled = false;
            }
        });
    }


    function getSRenabled() {
        $.ajax({
            url: "../api/v1/users/sr-enabled/",
            method: "GET",
            success: function (available) {
                if (available == 1) {
                    srSwitchMDC.checked = true;
                } else {
                    srSwitchMDC.checked = false;
                }
                srSwitchMDC.disabled = false;
            }
        });

    }


    function getOwnSRenabled() {

        $.ajax({
            url: "../api/v1/users/sr-enabled/self",
            method: "GET",
            success: function (available) {
                if (available == 1) {
                    srOwnSwitchMDC.checked = true;
                } else {
                    srOwnSwitchMDC.checked = false;
                }
                srOwnSwitchMDC.disabled = false;
            }
        });
    }

    function getSRMinutes() {
        $.ajax({
            url: "../api/v1/users/sr-mins/",
            method: "GET",
            dataType: "text",
            success: function (data) {
                srMinutes.html(data);
            },
            error: function (jqxhr) {
                // $("#register_area").text(jqxhr.responseText); // @text = response error, it is will be errors: 324, 500, 404 or anythings else
            }
        });

    }

    function getOwnSRMinutes() {

        $.ajax({
            url: "../api/v1/users/sr-mins/self",
            method: "GET",
            dataType: "text",
            success: function (data) {
                srOwnMinutes.html(data);
            },
            error: function (jqxhr) {
                // $("#register_area").text(jqxhr.responseText); // @text = response error, it is will be errors: 324, 500, 404 or anythings else
            }
        });
    }

    function setAvailabilityAsTypist(availability) {
        typistAvSwitchMDC.disabled = true;
        var formData = new FormData();
        formData.append('av', availability);

        $.ajax({
            type: 'POST',
            url: "../api/v1/users/set-available/",
            data: formData,
            processData: false,
            contentType: false,
            success: function (success) {
                if (success) {
                    typistAvSwitchMDC.checked = availability === 1;
                    typistAvSwitchMDC.disabled = false;
                } else {
                    getAvailabilityAsTypist();
                }
            },
            error: function (err) {
                getAvailabilityAsTypist();
            }
        });
    }

    function setSRenabled(enabled) {
        srSwitchMDC.disabled = true;
        var formData = new FormData();
        formData.append('sr', enabled);

        $.ajax({
            type: 'POST',
            url: "../api/v1/users/sr-enabled/",
            data: formData,
            processData: false,
            contentType: false,
            success: function (success) {
                if (success) {
                    srSwitchMDC.checked = enabled === 1;
                    srSwitchMDC.disabled = false;
                    sttToastNotification(enabled);
                } else {
                    getSRenabled();
                }
            },
            error: function (err) {
                getSRenabled();
            }
        });
    }

    const sttToast = $("#sttToast");
    const sttBody = sttToast.find(".toast-body");

    function sttToastNotification(enabled = true)
    {
        let info = enabled?"enabled":"disabled";
        sttBody.html("Speech To Text has been " + info);
        sttToast.toast('show');
    }

    function setSROwnEnabled(enabled) {
        srOwnSwitchMDC.disabled = true;
        var formData = new FormData();
        formData.append('sr', enabled);

        $.ajax({
            type: 'POST',
            url: "../api/v1/users/sr-enabled/self",
            data: formData,
            processData: false,
            contentType: false,
            success: function (success) {
                if (success) {
                    srOwnSwitchMDC.checked = enabled === 1;
                    srOwnSwitchMDC.disabled = false;
                    sttToastNotification(enabled);
                } else {
                    getOwnSRenabled();
                }
            },
            error: function () {
                getOwnSRenabled();
            }
        });
    }

    function validAccName() {
        if (accNameInput.val() === "" ||
            accNameInput.val().length >= 50 ||
            accNameInput.val().search(/[!@#$%^&*)(+=._-]+/g) !== -1) {
            accNameInput.addClass("is-invalid");
            accNameInput.removeClass("is-valid");
            return false;
        } else {
            accNameInput.removeClass("is-invalid");
            accNameInput.addClass("is-valid");
            return true;
        }
    }

});

function convertToSearchParam(params) {
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
