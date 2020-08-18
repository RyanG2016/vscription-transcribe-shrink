var currentRole;
var currentAccName;
var chooseJobModal;
var updateRoleModalBtn;
var closeModalBtn;
var setDefaultRoleBtn;
var changeRoleBtn;
var accNameInput;

var accessesGlobal;
var rolesGlobal;
var typistAccessCount;

var modalHeaderTitle;
var accessId;

var roleRequest;

const ACCESS_URL = "../api/v1/access/?out";
const SET_DEFAULT_ACCESS_URL = "../api/v1/users/set-default/";
const ROLES_FOR_ACCOUNT_URL = "../api/v1/access?out&acc_id="; // + acc_id
const ROLES_COUNT_FOR_ACCOUNT_URL = "../api/v1/access?out&count"; // + acc_id
const CREATE_ACC_URL = "../api/v1/accounts/?out";

const CHANGE_ROLE_HEADER = "<i class=\"fas fa-wrench\"></i>&nbsp;Change Role";
const SET_DEFAULT_ROLE_HEADER = "<i class=\"fas fa-user-edit\"></i>&nbsp;Set Default";

// -- combobox vars -- //
var accountBox;
var roleBox;

var setDefaultModal;

var accountsArray = [];
var loading;
var loadingText;
var roleIsset; //-> from PHP (landing.php)

$(document).ready(function () {
    // loading = document.getElementById("overlay");
    loading = $("#overlay");
    changeLoading(true);

    //initialize instance
    var enjoyhint_instance = new EnjoyHint({});

    //simple config.
    //Only one step - highlighting(with description) "New" button
    //hide EnjoyHint after a click on the button.
        var enjoyhint_script_steps = [
            {
                "next .navbar-text": "Here you will find your current email, account and role."
            },
            {
                "next #adminCard": "Here you can create/manage your client administrator account <br> " +
                    "<i>- You can only create <b>one</b> account -</i>",
                shape:"circle"
            },
            {
                "next #typistCardHead":'This is the typist card',
                // shape:"circle",
                // "skipButton":{text: "Finish"}
            }
            ,
            {
                "click #typistCard>div":'Here you can find information about your current status as a typist',
                // shape:"circle",
                "skipButton":{text: "Finish"}
            }
        ];

    //set script config
    //     enjoyhint_instance.set(enjoyhint_script_steps);

    //run Enjoyhint script
        enjoyhint_instance.run();

    loadingText = $("#loadingText");
    accNameInput = $("#accNameTxt");
    currentRole = $("#currentRole");
    currentAccName = $("#currentAccountName");
    updateRoleModalBtn = $("#updateRoleBtn");
    closeModalBtn = $("#closeModalBtn");
    changeRoleBtn = $("#changeRoleBtn");
    modalHeaderTitle = $("#modalHeaderTitle");
    setDefaultRoleBtn = $("#setDefaultRoleBtn");
    accessId = $("#accessId");

    // comboBoxes
    accountBox = $("#accountBox");
    roleBox = $("#roleBox");

    chooseJobModal = $("#modal");

    closeModalBtn.on("click", function (e) {
        chooseJobModal.modal('hide');
    });


    changeRoleBtn.on("click", function (e) {
        setModalUI(false);
        // chooseJobModal.style.display = "block";
        chooseJobModal.modal('show');
    });

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

    setDefaultRoleBtn.on("click", function (e) {
        setModalUI(true);
        // chooseJobModal.style.display = "block";
        chooseJobModal.modal();
    });
    getRolesCount(3);

    updateRoleModalBtn.on("click", function (e) {

        updateRoleModalBtn.attr("disabled", "disabled");
        changeLoading(true, "Setting role, please wait..");
        var formData = new FormData();
        formData.append("acc_id", accountBox.selectpicker('val'));
        formData.append("acc_role", roleBox.selectpicker('val'));


        $.ajax({
            type: 'POST',
            url: setDefaultModal?SET_DEFAULT_ACCESS_URL:ACCESS_URL,
            processData: false,
            // contentType: "application/x-www-form-urlencoded",
            data: convertToSearchParam(formData),
            // headers: update?{'Content-Type': 'application/x-www-form-urlencoded'}:{'Content-Type': "multipart/form-data; boundary="+formData.boundary},

            success: function (response) {
                // resetForm();
                // accessDTRef.ajax.reload(); // refresh access table
                // chooseJobModal.style.display = "none";
                chooseJobModal.modal('hide');

                location.reload();
                /*$.confirm({
                    title: 'Success',
                    content: response["msg"],
                    buttons: {
                        confirm: {
                            btnClass: 'btn-green',
                            action: function () {
                                updateRoleModalBtn.removeAttr("disabled");
                                location.reload();
                                return true;
                            }
                        }
                    }
                });*/


            },
            error: function (err) {
                changeLoading(false);
                $.confirm({
                    title: 'Error',
                    content: err.responseJSON["msg"],
                    buttons: {
                        confirm: {
                            btnClass: 'btn-red',
                            action: function () {
                                updateRoleModalBtn.removeAttr("disabled");
                                return true;
                            }
                        }
                    }
                });
            }
        });

    });


    $.ajax({
        url: ACCESS_URL,
        method: "GET",
        success: function (accesses) {
            accessesGlobal = accesses;

            const cbox = document.getElementById("accountBox");
            for (const access of accesses) {

                // using an array to prevent multiple select options for the same account
                if(accountsArray.indexOf(access.acc_id) != -1)
                {
                    continue;
                }
                accountsArray.push(access.acc_id);

                cbox.innerHTML += "<option value='" + access.acc_id + "'>" +
                    // access.acc_id +
                    // " - " +
                    access.acc_name +
                    "</option>";
            }
            accountBox.selectpicker({
                liveSearch: true,
                liveSearchPlaceholder: "Choose Account"
            });

            loadRolesForAccount(accountBox.selectpicker('val')); // first time

            accountBox.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                // do something...
                // console.log("selection changed to: " + clickedIndex + " and prev was: " + previousValue+ "and e is ");
                // console.log(e);
                // console.log(countryBox.selectpicker('val')); // selected value
                changeLoading(true, "Loading roles..");
                loadRolesForAccount(accountBox.selectpicker('val'));
            });
        }
    });

    function validAccName()
    {
        "hel@#l6o".search()
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


    function checkForSingleRoleToSet() {

        if(accessesGlobal.length == 1)
        {
            if(rolesGlobal.length == 1){
                // only one access - set it as current
                setCurrentRole(
                    rolesGlobal[0]["acc_id"],
                    rolesGlobal[0]["acc_role"]
                );
            }else{
                // chooseJobModal.style.display = "block";
                chooseJobModal.modal('show');
                changeLoading(false);
            }
        }else{
            if(accessesGlobal.length != 0){
                // chooseJobModal.style.display = "block";
                chooseJobModal.modal('show');
            }
            changeLoading(false);
        }
    }

    function setCurrentRole(accID, accRole) {
        var formData = new FormData();
        formData.append("acc_id", accID);
        formData.append("acc_role", accRole);


        $.ajax({
            type: 'POST',
            url: ACCESS_URL,
            processData: false,
            // contentType: "application/x-www-form-urlencoded",
            data: convertToSearchParam(formData),
            // headers: update?{'Content-Type': 'application/x-www-form-urlencoded'}:{'Content-Type': "multipart/form-data; boundary="+formData.boundary},

            success: function (response) {
                // resetForm();
                // accessDTRef.ajax.reload(); // refresh access table
                // chooseJobModal.style.display = "none";
                chooseJobModal.modal('hide');
                location.reload();
            },
            error: function (err) {
                /*$.confirm({
                    title: 'Error',
                    content: err.responseJSON["msg"],
                    buttons: {
                        confirm: {
                            btnClass: 'btn-red',
                            action: function () {
                                updateRoleModalBtn.removeAttr("disabled");
                                return true;
                            }
                        }
                    }
                });*/
            }
        });
    }

    function loadRolesForAccount(accID) {
        // stateInputLbl.css("display","none");
        // stateBoxLbl.css("display","none");
        // cityContainer.css("display","none");



        // removeLoadingSpinner(); // if any left over
        // stateContainer.append(generateLoadingSpinner());
        roleBox.selectpicker('destroy');

        if (roleRequest != null) {
            roleRequest.abort();
        }

        roleRequest = $.ajax({
            url: ROLES_FOR_ACCOUNT_URL + accID,
            method: "GET",
            success: function (roles) {
                rolesGlobal = roles;
                updateRoleModalBtn.removeAttr("disabled");
                const tybox = document.getElementById("roleBox");
                tybox.innerHTML = ""; // clear old values
                for (const role of roles) {
                    tybox.innerHTML += "<option value='" + role.acc_role + "'>" +
                        // role.acc_role +
                        // " - " +
                        role.role_desc + "</option>"
                }
                roleBox.selectpicker({
                    liveSearch: true,
                    liveSearchPlaceholder: "Choose Role"
                });
                roleBox.selectpicker('refresh');

                if(!roleIsset){
                    checkForSingleRoleToSet();
                }else{
                    changeLoading(false);
                }
                // removeLoadingSpinner();
            }
        });

    }

    function getRolesCount(type = 0) {

        var param = "";
        if(type){
            param = "&type="+type;
        }

        $.ajax({
            url: ROLES_COUNT_FOR_ACCOUNT_URL + param,
            method: "GET",
            success: function (response) {
                typistAccessCount = response["count"];
                $("#typistCount").html( typistAccessCount);
                if(typistAccessCount > 0)
                {
                    $("#typist0").remove();
                    $("#alertT0").remove();
                }else{
                    $("#typist1").remove();
                    $("#alertT1").remove();
                }
            }
        });

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

function changeLoading(show, text = false) {
    if(!show){
        // loading.style.display = "none";
        loading.fadeOut();
        $("body").css("overflow", "auto");
    }else{
        $("body").css("overflow", "none");
        if(text) loadingText.html(text);
        loading[0].style.display = "block";
        // loading.fadeIn();
    }
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

function setModalUI(setDefaultModalBool = false) {
    setDefaultModal = setDefaultModalBool;

    if(setDefaultModalBool) {
        modalHeaderTitle.html(SET_DEFAULT_ROLE_HEADER);
    } else{
        modalHeaderTitle.html(CHANGE_ROLE_HEADER);
    }
}