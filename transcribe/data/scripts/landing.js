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

var srMinutes;
var srSwitch;
var srSwitchMDC;

var roleIsset; //-> from PHP (landing.php)
var redirectID; //-> from PHP (landing.php) this is current role ID but named redirect for reasons

$(document).ready(function () {
    // loading = document.getElementById("overlay");
    // console.log(redirectID);
    loading = $("#overlay");
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

    $("#srSwitchHelp").popover({
        html: true,
        trigger: 'hover',
        content: "<div>" +
            "<b>This will enable automated speech recognition to all of your next job uploads<br><br></b>" +
            // "<br><br>" +
            "" +
            "<i>This doesn't affect your current jobs</i>" +
            "</div>"
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
    var enjoyhint_instance
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
    }
    //</editor-fold>



        // console.log("tutorials variable: " + tutorials);
        // console.log(JSON.parse(tutorials));
    //run Enjoyhint script
    //     enjoyhint_instance.run();

    loadingText = $("#loadingText");
    typistAvSwitch = $("#typist_av_switch");
    srMinutes = $("#srMinutes");
    srSwitch = $("#srSwitch");
    accNameInput = $("#accNameTxt");
    currentRole = $("#currentRole");
    currentAccName = $("#currentAccountName");
    accessId = $("#accessId");



    typistAvSwitchMDC = new mdc.switchControl.MDCSwitch(document.querySelector('#typist_av_switch'));


    typistAvSwitch.on('change', function (e) {
        typistAvSwitchMDC.disabled = true;
        if(typistAvSwitchMDC.checked)
        {
            setAvailabilityAsTypist(1);
        }else{
            setAvailabilityAsTypist(2);
        }
    });

    if(roleIsset && (redirectID == 1 || redirectID == 2))
    {
        srSwitchMDC = new mdc.switchControl.MDCSwitch(document.querySelector('#srSwitch'));

        srSwitch.on('change', function (e) {
            srSwitchMDC.disabled = true;
            if(srSwitchMDC.checked)
            {
                setSRenabled(1);
            }else{
                setSRenabled(0);
            }
        });
        getSRenabled();

        // get remaining minutes balance
        getSRMinutes();
    }

    accNameInput.keyup(function(){
        validAccName();
    });

    $("#createAdminAccBtn").on("click", function (e) {
        if(validAccName()){
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
                    setTimeout(function()
                    {
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

    getAvailabilityAsTypist();
    function getAvailabilityAsTypist()
    {
        $.ajax({
            url: "../api/v1/users/available/",
            method: "GET",
            success: function (available) {
                if(available == 1)
                {
                    typistAvSwitchMDC.checked = true;
                }else{
                    typistAvSwitchMDC.checked = false;
                }
                typistAvSwitchMDC.disabled = false;
            }
        });
    }


    function getSRenabled()
    {
        $.ajax({
            url: "../api/v1/users/sr-enabled/",
            method: "GET",
            success: function (available) {
                if(available == 1)
                {
                    srSwitchMDC.checked = true;
                }else{
                    srSwitchMDC.checked = false;
                }
                srSwitchMDC.disabled = false;
            }
        });
    }
    function getSRMinutes()
    {
        $.ajax({
            url: "../api/v1/users/sr-mins/",
            method: "GET",
            dataType: "text",
            success: function (data) {
                srMinutes.html(data);
            },
            error: function(jqxhr) {
                // $("#register_area").text(jqxhr.responseText); // @text = response error, it is will be errors: 324, 500, 404 or anythings else
            }
        });
    }

    function setAvailabilityAsTypist(availability)
    {
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
                if(success)
                {
                    typistAvSwitchMDC.checked = availability === 1;
                    typistAvSwitchMDC.disabled = false;
                }else{
                    getAvailabilityAsTypist();
                }
            },
            error: function (err) {
                getAvailabilityAsTypist();
            }
        });
    }

    function setSRenabled(enabled)
    {
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
                if(success)
                {
                    srSwitchMDC.checked = enabled === 1;
                    srSwitchMDC.disabled = false;
                }else{
                    getSRenabled();
                }
            },
            error: function (err) {
                getSRenabled();
            }
        });
    }

    function validAccName()
    {
        // "hel@#l6o".search()
        if(accNameInput.val() === "" ||
            accNameInput.val().length >= 50 ||
            accNameInput.val().search(/[!@#$%^&*)(+=._-]+/g) !== -1)
        {
            accNameInput.addClass("is-invalid");
            accNameInput.removeClass("is-valid");
            return false;
        }else{
            accNameInput.removeClass("is-invalid");
            accNameInput.addClass("is-valid");
            return true;
        }
    }

});

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
