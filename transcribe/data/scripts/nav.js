$(document).ready(function(){

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

    const CHANGE_ROLE_HEADER = "<i class=\"fas fa-wrench\"></i>&nbsp;Switch Org/Role";
    const SET_DEFAULT_ROLE_HEADER = "<i class=\"fas fa-user-edit\"></i>&nbsp;Set Default";

    var changeRoleBtn = $("#changeRoleBtn");
    var setDefaultRoleBtn = $("#setDefaultRoleBtn");
    var updateRoleModalBtn = $("#updateRoleBtn");
    var changeRoleModal = $("#changeRole");
    var accountBoxNav = $("#accountBoxNav");
    var roleBoxNav = $("#roleBoxNav");
    var navLoverlay = $("#navOverlay");
    var navLoverlayText = $("#navOverlayText");
    var navModalHeaderTitle = $("#navModalHeaderTitle");
    var pinBtn = $("#pinBtn");
    var pinIcon = $("#pinIcon");
    var pinBtnPressed = false;
    var sidebarPinned = getCookie("sidebar_pinned");

    /* NAV UI */
    var navCollapseText = $('#collapse-text');
    var navCollapseIcon = $('#collapse-icon');

    setDefaultRoleBtn.on("click", function (e) {
        setModalUI(true);
        changeRoleModal.modal();
    });

    // Set Active Menu Item //
    setActiveMenuItem();

    console.log(`We are loading the navbar`);

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
            navModalHeaderTitle.html(SET_DEFAULT_ROLE_HEADER);
        } else{
            navModalHeaderTitle.html(CHANGE_ROLE_HEADER);
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

    $("#adminmenu > a").each(
        function (){
            $(this).popover({
                content: $(this).find("span.menu-collapsed").html(),
                trigger: 'hover'
            });
        }
    );

    /* NAV UI Related */
// Hide submenus
    $('#body-row .collapse').collapse('hide');

// Collapse/Expand icon
    navCollapseIcon.addClass('fa-chevron-double-right');


    function SidebarCollapse (silent = false) {

        $('.menu-collapsed').toggleClass('d-none');
        $('.sidebar-submenu').toggleClass('d-none');
        $('.submenu-icon').toggleClass('d-none');
        // $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed col-2 col');
        $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');
        // $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed col-2 col-auto');
        // $(".vspt-page-container").toggleClass("col-10 vspt-col-auto-fix");

        // Treating d-flex/d-none on separators with title
        var SeparatorTitle = $('.sidebar-separator-title');
        if ( SeparatorTitle.hasClass('d-flex') ) {
            SeparatorTitle.removeClass('d-flex');
        } else {
            SeparatorTitle.addClass('d-flex');
        }

        // Collapse/Expand icon
        navCollapseIcon.toggleClass('fa-chevron-double-right fa-chevron-double-left');
        navCollapseText.html(navCollapseText.html() === "Expand"?"Collapse":"Expand");

        navCollapseIcon.parent().parent().popover('dispose').popover({
            content: navCollapseText.html(),
            trigger: 'hover'
        });
        if(!silent) navCollapseIcon.popover('show');
    }

    // check if sidebar is pinned
    if (sidebarPinned === "true"){
        SidebarCollapse(true);
        pinBtn.attr("aria-pressed", "true");
        pinBtn.addClass("active");
        pinIcon.addClass("active");
        pinIcon.removeClass("fa-rotate-315");
    }

    // console.log("sidebar pinned: " + sidebarPinned);
    // Collapse click
    $('[data-toggle=sidebar-collapse-toggle]').click(function() {
        if(pinBtnPressed) pinBtnPressed=false;
        else SidebarCollapse();
    });

    pinBtn.on('click', function(e){
        pinBtnPressed = true; // to prevent collapse from being pressed
        pinIcon.toggleClass("fa-rotate-315 active");

        // pinned?
        sidebarPinned = !pinIcon.hasClass("fa-rotate-315");
        setCookie("sidebar_pinned", sidebarPinned, 365);
    });

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return false;
    }

    function setActiveMenuItem() {
        let path = window.location.pathname;
        let page = path.split("/").pop();
        let el = '';
        let adminMenuEl = '';
        console.log( `Current page is ${page}` );
        switch (page) {
            case "main.php":
                el = document.getElementById('main-nav');
                el.classList.add("active");
                break;
            case "jobupload.php":
                el = document.getElementById('upload-nav');
                el.classList.add("active");
                break;
            case "transcribe.php":
                el = document.getElementById('transcribe-nav');
                el.classList.add("active");
                break;
            case "manage_users.php":
                el = document.getElementById('manage-users-nav');
                el.classList.add("active");
                break;
            case "accounts.php":
                el = document.getElementById('accounts-nav');
                el.classList.add("active");
                adminMenuEl = document.getElementById('adminmenu');
                adminMenuEl.classList.add("show");
                break;
            case "users.php":
                el = document.getElementById('users-nav');
                el.classList.add("active");
                adminMenuEl = document.getElementById('adminmenu');
                adminMenuEl.classList.add("show");
                break;
            case "admin_tools.php":
                el = document.getElementById('admin-tools-nav');
                el.classList.add("active");
                adminMenuEl = document.getElementById('adminmenu');
                adminMenuEl.classList.add("show");
                break;
            case "billing_report.php":
                el = document.getElementById('billing-report-nav');
                el.classList.add("active");
                adminMenuEl = document.getElementById('adminmenu');
                adminMenuEl.classList.add("show");
                break;
            case "typist_report.php":
                el = document.getElementById('typist-report-nav');
                el.classList.add("active");
                adminMenuEl = document.getElementById('adminmenu');
                adminMenuEl.classList.add("show");
                break;
            case "downloads.php":
                el = document.getElementById('downloads-nav');
                el.classList.add("active");
                break;
            case "settings.php":
                el = document.getElementById('settings-nav');
                el.classList.add("active");
                break;   
            case "panel.php":
                el = document.getElementById('settings-nav');
                el.classList.add("active");
                break;                                    
         default:
                el = document.getElementById('home-nav');
                el.classList.add("active");
        }
    }
/*
    function checkCookie() {
        var user = getCookie("username");
        if (user != "") {
            alert("Welcome again " + user);
        } else {
            user = prompt("Please enter your name:", "");
            if (user != "" && user != null) {
                setCookie("username", user, 365);
            }
        }
    }*/

});