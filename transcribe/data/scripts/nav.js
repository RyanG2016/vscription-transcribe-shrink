var setDefaultModal;
var accessesGlobal;
var rolesGlobal;
var typistAccessCount;
var accountsArray = [];
var roleRequest;

const ROLES_FOR_ACCOUNT_URL = "../api/v1/access?out&acc_id="; // + acc_id
const ROLES_COUNT_FOR_ACCOUNT_URL = "../api/v1/access?out&count"; // + acc_id
const ACCESS_URL = "../api/v1/access/?out";
const SET_DEFAULT_ACCESS_URL_NAV = "../api/v1/users/set-default/";

const CHANGE_ROLE_HEADER = "<i class=\"fas fa-wrench\"></i>&nbsp;Change Role";
const SET_DEFAULT_ROLE_HEADER = "<i class=\"fas fa-user-edit\"></i>&nbsp;Set Default";

changeRoleBtn = $("#changeRoleBtn");
setDefaultRoleBtn = $("#setDefaultRoleBtn");
updateRoleModalBtn = $("#updateRoleBtn");
changeRoleModal = $("#changeRole");
accountBoxNav = $("#accountBoxNav");
roleBoxNav = $("#roleBoxNav");
navLoverlay = $("#navOverlay");
navLoverlayText = $("#navOverlayText");
modalHeaderTitle = $("#modalHeaderTitle");

setDefaultRoleBtn.on("click", function (e) {
    setModalUI(true);
    changeRoleModal.modal();
});

updateRoleModalBtn.on("click", function (e) {

    updateRoleModalBtn.attr("disabled", "disabled");
    changeLoading(true, "Setting role, please wait..");
    var formData = new FormData();
    formData.append("acc_id", accountBoxNav.selectpicker('val'));
    formData.append("acc_role", roleBoxNav.selectpicker('val'));


    $.ajax({
        type: 'POST',
        url: setDefaultModal?SET_DEFAULT_ACCESS_URL_NAV:ACCESS_URL,
        processData: false,
        data: convertToSearchParam(formData),

        success: function (response) {
            changeRoleModal.modal('hide');

            location.reload();
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
changeRoleBtn.on("click", function (e) {
    setModalUI(false);
    // changeRoleModal.style.display = "block";
    changeRoleModal.modal('show');
});

getRolesCount(3);

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


function setModalUI(setDefaultModalBool = false) {
    setDefaultModal = setDefaultModalBool;

    if(setDefaultModalBool) {
        modalHeaderTitle.html(SET_DEFAULT_ROLE_HEADER);
    } else{
        modalHeaderTitle.html(CHANGE_ROLE_HEADER);
    }
}



$.ajax({
    url: ACCESS_URL,
    method: "GET",
    success: function (accesses) {
        accessesGlobal = accesses;

        const cbox = document.getElementById("accountBoxNav");
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
        accountBoxNav.selectpicker({
            liveSearch: true,
            liveSearchPlaceholder: "Choose Account"
        });

        loadRolesForAccount(accountBoxNav.selectpicker('val')); // first time

        accountBoxNav.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
            // do something...
            // console.log("selection changed to: " + clickedIndex + " and prev was: " + previousValue+ "and e is ");
            // console.log(e);
            // console.log(countryBox.selectpicker('val')); // selected value
            changeLoading(true, "Loading roles..");
            loadRolesForAccount(accountBoxNav.selectpicker('val'));
        });
    }
});



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
            // changeRoleModal.style.display = "block";
            changeRoleModal.modal('show');
            changeLoading(false);
        }
    }else{
        if(accessesGlobal.length != 0){
            // changeRoleModal.style.display = "block";
            changeRoleModal.modal('show');
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
            // changeRoleModal.style.display = "none";
            changeRoleModal.modal('hide');
            // location.reload();
            if(accRole == 2)
            {
                location.href = 'main.php';
            }else{
                location.reload();
            }

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
    roleBoxNav.selectpicker('destroy');

    if (roleRequest != null) {
        roleRequest.abort();
    }

    roleRequest = $.ajax({
        url: ROLES_FOR_ACCOUNT_URL + accID,
        method: "GET",
        success: function (roles) {
            rolesGlobal = roles;
            updateRoleModalBtn.removeAttr("disabled");
            const tybox = document.getElementById("roleBoxNav");
            tybox.innerHTML = ""; // clear old values
            for (const role of roles) {
                tybox.innerHTML += "<option value='" + role.acc_role + "'>" +
                    // role.acc_role +
                    // " - " +
                    role.role_desc + "</option>"
            }
            roleBoxNav.selectpicker({
                liveSearch: true,
                liveSearchPlaceholder: "Choose Role"
            });
            roleBoxNav.selectpicker('refresh');

            if(!roleIsset){
                checkForSingleRoleToSet();
            }else{
                changeLoading(false);
            }
        }
    });

}

function changeLoading(show, text = false) {
    if(!show){
        navLoverlay.fadeOut();
        $("body").css("overflow", "auto");
    }else{
        $("body").css("overflow", "none");
        if(text) navLoverlayText.html(text);
        navLoverlay[0].style.display = "block";
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

$("#sidebar-container > ul > a").each(
    function (){
        $(this).popover({
            content: $(this).find("div > span.menu-collapsed").html(),
            trigger: 'hover'
        });
    }
);