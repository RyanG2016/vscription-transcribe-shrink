(function ($) {
    "use strict";
})(jQuery);

var accessDT;
var accessDTRef;
var createAcc;
var createAccModal;
var createAccForm;
var createAccBtn;
var updateAccBtn;
var modalHeaderTitle;
var accountsGlobal;
var rolesGlobal;
const ACCOUNTS_URL = "../api/v1/accounts/?access-model";
const ROLES_URL = "../api/v1/roles/";
const API_INSERT_URL = '../api/v1/access/';
const SET_DEFAULT_ACCESS_URL = "../api/v1/users/set-default/";

const CREATE_ACC_HEADER = "<i class=\"fas fa-key\"></i>&nbsp;Add Permission";
const UPDATE_ACC_HEADER = "<i class=\"fas fa-user-edit\"></i>&nbsp;Update Permission";

/** Fields */

// var email;

// -- combobox vars -- //
var accountBox;
var roleBox;


var update = false;
var updateData;


var currentID;
var uid;

$(document).ready(function () {

    const maximum_rows_per_page_jobs_list = 10;

    var uidIn = $("#uidIn");
    uid = uidIn.val();


    createAccModal = document.getElementById("modal");
    // createAccModal.style.display = "block";
    accessDT = $("#access-tbl");
    createAcc = $("#createAcc");
    createAccForm = $("#createAccForm");
    createAccBtn = $("#createAccBtn");
    updateAccBtn = $("#updateAccBtn");
    modalHeaderTitle = $("#modalHeaderTitle");

    // comboBoxes
    accountBox = $("#accountBox");
    roleBox = $("#roleBox");

    /** Fields */
    // email = $("#email");

    $(".modal").niceScroll({
        hwacceleration: true,
        smoothscroll: true,
        cursorcolor: "white",
        cursorborder: 0,
        scrollspeed: 10,
        mousescrollstep: 20,
        cursoropacitymax: 0.7
        //  cursorwidth: 16

    });
    //
    // $("body").niceScroll({
    //     hwacceleration: true,
    //     smoothscroll: true,
    //     cursorcolor: "white",
    //     cursorborder: 0,
    //     scrollspeed: 10,
    //     mousescrollstep: 20,
    //     cursoropacitymax: 0.7
    //     //  cursorwidth: 16
    //
    // });

    $.ajaxSetup({
        cache: false
    });

    const refreshBtn = document.querySelector('#refresh_btn');
    // const goToUploader = document.querySelector('#newupload_btn');

    // Activate ripples effect for material buttons
    new mdc.ripple.MDCRipple(document.querySelector('#refresh_btn'));

    function goForm(update = false, id = null) {

        var formData = new FormData(document.querySelector("#createAccForm"));
        formData.append("uid", uid);
        // console.log(formData.entries().toString());
        // for (var pair of formData.entries()) {
        // 	console.log(pair[0]+ ', ' + pair[1]);
        // }

        createAccBtn.attr("disabled", "disabled");
        updateAccBtn.attr("disabled", "disabled");

        // Insert to API
        // - on response -
        // if success - > reset the form
        // show status msg dialog
        // close the modal
        // refresh access table

        $.ajax({
            type: update ? 'PUT' : 'POST',
            url: update ? API_INSERT_URL + id + "/" : API_INSERT_URL,
            processData: false,
            // contentType: "application/x-www-form-urlencoded",
            data: convertToSearchParam(formData),
            // headers: update?{'Content-Type': 'application/x-www-form-urlencoded'}:{'Content-Type': "multipart/form-data; boundary="+formData.boundary},

            success: function (response) {
                resetForm();
                accessDTRef.ajax.reload(); // refresh access table
                createAccModal.style.display = "none";

                $.confirm({
                    title: 'Success',
                    content: response["msg"],
                    buttons: {
                        confirm: {
                            text: 'ok',
                            btnClass: 'btn-green',
                            action: function () {
                                return true;
                            }
                        }
                    }
                });


            },
            error: function (err) {
                $.confirm({
                    title: 'Error',
                    content: err.responseJSON["msg"],
                    buttons: {
                        confirm: {
                            btnClass: 'btn-red',
                            action: function () {
                                createAccBtn.removeAttr("disabled");
                                updateAccBtn.removeAttr("disabled");
                                return true;
                            }
                        }
                    }
                });
            }
        });


    }

    $('.createAccForm input:not(input[type=radio])').each(function () {
        $(this).focus(function () {
            hideValidate(this);
        });
    });

    $('.createAccForm input[type=radio]').each(function () {
        $(this).focus(function () {
            hideValidate(this, true);
        });
    });
    $.fn.dataTable.ext.errMode = 'none';

    accessDTRef = accessDT.DataTable({
        rowId: 'acc_id',
        "ajax": '../api/v1/access?dt&uid=' + uid,
        "processing": true,
        lengthChange: false,
        pageLength: maximum_rows_per_page_jobs_list,
        autoWidth: false,

        "columns": [
            {
                "title": "ID",
                "data": "access_id"
            },
            {
                "title": "Acc ID",
                "data": "acc_id"
            },
            {
                "title": "Account",
                "data": "acc_name"
            },
            {
                "title": "Username",
                "data": "username"
            },
            {
                "title": "Email",
                "data": "email"
            },
            {
                "title": "Role ID",
                "data": "acc_role"
            },
            {
                "title": "Role",
                "data": "role_desc"
            },
            {
                "title": "Def",
                "data": "access_id",
                render: function (data, type, row) {
                    if (data == row["def_access_id"]) {
                        return "<i class=\"fas fa-check-circle vtex-status-icon\"></i>";
                    } else {
                        return "";
                    }
                }
            }
            /*,
            render: function (data, type, row) {
                if(data)
                {
                    return data;
                }else{

                }
            }
        },
        { "title": "account_status",
 "data": "account_status" },
        { "title": "enabled",
 "data": "enabled",
            render: function (data) {
                if(data == true)
                {
                    return "<i class=\"fas fa-check-circle vtex-status-icon\"></i>";
                }else{
                    return "<i class=\"fas fa-times-circle vtex-status-icon\"></i>";
                }
            }
        }*/
        ]
    });

    accessDT.on( 'error.dt', function ( e, settings, techNote, message ) {
        // console.log( 'An error has been reported by DataTables: ', message );
        console.log( 'Failed to retrieve data' );
    } )

    $.contextMenu({
        selector: '#access-tbl tbody tr',
        callback: function (key, options) {
            // var m = "clicked: " + key + "  ";
            // window.console && console.log(m) ;//|| alert(m);
            // accessDTRef.row(this).data()["enabled"] == true;
            var data = accessDTRef.row(this).data();
            switch (key) {
                case "delete":
                    var id = data["access_id"];
                    var accName = data["email"];
                    $.confirm({
                        title: '<i class="fas fa-user-minus"></i> Revoke Access?',
                        content: 'Are you sure do you want to revoke access to <b>' +
                            data["acc_name"]+ '</b> as <b>'+data["role_desc"]+'</b> ?<br><br>'
                            // '<span style="color: red">USE WITH CAUTION THIS WILL DELETE THE access ACCOUNT AND ALL RELATED DATA INCLUDING JOB ENTRIES</span>',
                        ,buttons: {
                            confirm: {
                                text: "yes",
                                btnClass: 'btn-red',
                                action: function () {

                                    $.ajax({
                                        type: 'DELETE',
                                        url: API_INSERT_URL + id,
                                        processData: false,
                                        contentType: false,
                                        success: function (response) {
                                            accessDTRef.ajax.reload(); // refresh access table
                                            $.confirm({
                                                title: 'Success',
                                                content: response["msg"],
                                                buttons: {
                                                    confirm: {
                                                        btnClass: 'btn-green',
                                                        action: function () {
                                                            return true;
                                                        }
                                                    }
                                                }
                                            });


                                        },
                                        error: function (err) {
                                            accessDTRef.ajax.reload(); // refresh access table
                                            $.confirm({
                                                title: 'Error',
                                                content: err.responseJSON["msg"],
                                                buttons: {
                                                    confirm: {
                                                        btnClass: 'btn-red',
                                                        action: function () {
                                                            return true;
                                                        }
                                                    }
                                                }
                                            });
                                        }
                                    });

                                    return true;
                                }
                            },
                            cancel:
                                {
                                    text: "no",
                                    btnClass: 'btn-green',
                                    function() {
                                        return true;
                                    }
                                }
                        }
                    });
                    break;


                case "def":
                    var access_id = data["access_id"];
                    var acc_id = data["acc_id"];
                    var acc_role = data["acc_role"];
                    var uid = data["uid"];
                    // var accName = data["email"];
                    $.confirm({
                        title: '<i class="fas fa-key"></i> Set as default?',
                        content: 'Are you sure do you want to set <b>' +
                            access_id + '</b> as default access ?<br><br>'

                        ,buttons: {
                            confirm: {
                                text: "yes",
                                btnClass: 'btn-green',
                                action: function () {

                                    var formData = new FormData();
                                    formData.append("acc_id", acc_id);
                                    formData.append("acc_role", acc_role);
                                    formData.append("uid", uid);

                                    $.ajax({
                                        type: 'POST',
                                        url: SET_DEFAULT_ACCESS_URL,
                                        processData: false,
                                        // contentType: "application/x-www-form-urlencoded",
                                        data: convertToSearchParam(formData),
                                        // headers: update?{'Content-Type': 'application/x-www-form-urlencoded'}:{'Content-Type': "multipart/form-data; boundary="+formData.boundary},

                                        success: function (response) {

                                            accessDTRef.ajax.reload(); // refresh access table
                                            return true;

                                        },
                                        error: function (err) {
                                            $.confirm({
                                                title: 'Error',
                                                content: err.responseJSON["msg"],
                                                buttons: {
                                                    confirm: {
                                                        btnClass: 'btn-red',
                                                        action: function () {
                                                            accessDTRef.ajax.reload(); // refresh access table
                                                            return true;
                                                        }
                                                    }
                                                }
                                            });
                                        }
                                    });

                                    return true;
                                }
                            },
                            cancel:
                                {
                                    text: "no",
                                    btnClass: 'btn-red',
                                    function() {
                                        return true;
                                    }
                                }
                        }
                    });
                    break;

                case "edit":
                    preFillForm(data);
                    break;
            }

        },
        items: {
            "edit": {name: "Edit", icon: "fas fa-key"},
            "def": {name: "Set as default", icon: "fas fa-toggle-on"},
            "sep1": "---------",
            "delete": {name: "Delete", icon: "fas fa-trash-alt"},
            // "quit2": {name: "Quit2", icon: "quit"},
            // "quit": {name: "Quit", icon: function(){
            // 		console.log("return function for quit");
            // 		return 'context-menu-icon context-menu-icon-quit';
            // 	}}
        }
    });

    refreshBtn.addEventListener('click', e => {
        accessDTRef.ajax.reload();
    });

    createAcc.on("click", function (e) {

        update = false;
        updateAccBtn.css("display", "none");
        createAccBtn.css("display", "inline");
        createAccModal.style.display = "block";
        $('#modal').stop().animate({
            scrollTop: 0
        }, 500);
    });

    updateAccBtn.on("click", function (e) {
        goForm(true, currentID);
    });

    createAccBtn.on("click", function (e) {
        goForm();
    });

    $(".cancel-acc-button").on("click", function (e) {
        resetForm();
        createAccModal.style.display = "none";
    });

    $.ajax({
        url: ACCOUNTS_URL,
        method: "GET",
        success: function (accounts) {
            accountsGlobal = accounts;
            const cbox = document.getElementById("accountBox");
            for (const account of accounts) {
                cbox.innerHTML += "<option value='" + account.acc_id + "'>" +
                    account.acc_id +
                    " - " +
                    account.acc_name +
                    "</option>";
            }
            accountBox.selectpicker({
                liveSearch: true,
                liveSearchPlaceholder: "Choose Account"
            });

            createAccBtn.removeAttr("disabled");
            updateAccBtn.removeAttr("disabled");
        }
    });


    $.ajax({
        url: ROLES_URL,
        method: "GET",
        success: function (roles) {
            rolesGlobal = roles;
            // console.log(countriesGlobal);
            // setupComboBox(countries, "country");
            const cbox = document.getElementById("roleBox");
            for (const role of roles) {
                cbox.innerHTML += "<option value='" + role.role_id + "'>" +
                    role.role_id +
                    " - " +
                    role.role_desc +
                    "</option>";
            }
            roleBox.selectpicker({
                liveSearch: true,
                liveSearchPlaceholder: "Choose Role"
            });
        }
    });

});

function htmlEncodeStr(s) {
    return s.replace(/&/g, "&amp;")
        .replace(/>/g, "&gt;")
        .replace(/</g, "&lt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&lsquo;");
}

function resetForm() {
    modalHeaderTitle.html(CREATE_ACC_HEADER);
    updateAccBtn.css("display", "none");
    createAccBtn.css("display", "inline");
    createAccForm.find("input:not(input[type=radio])").val("");
    createAccForm.find("input[type=radio]").prop('checked', false).change();

    createAccBtn.removeAttr("disabled");
    updateAccBtn.removeAttr("disabled");
}


function preFillForm(data) {
    update = true;
    updateData = data;
    // data: dataTable Item Array
    // accessDTRef.row(this).data()["item"]
    // data["item"]

    // pass current ID
    currentID = data["access_id"];

    // header title
    modalHeaderTitle.html(UPDATE_ACC_HEADER);

    // buttons
    updateAccBtn.css("display", "inline");
    createAccBtn.css("display", "none");


    accountBox.selectpicker('val', data["acc_id"]);
    roleBox.selectpicker('val', data["acc_role"]);


    // show form
    createAccModal.style.display = "block";
    $('#modal').stop().animate({
        scrollTop: 0
    }, 500);

}


function hideValidate(input, isRadioBtn = false) {

    var self;
    if (isRadioBtn) {
        self = $(input).parent();
    } else {
        self = $(input);
    }
    // self.val()
    self.removeClass("vtex-validate-error");
}

function isEmptyInput(el) {
    return !$.trim(el.val())
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

function validateEmail(mail) {
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
        return (true)
    }
    // alert("You have entered an invalid email address!")
    return (false)
}