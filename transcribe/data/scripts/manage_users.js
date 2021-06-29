(function ($) {
    "use strict";
})(jQuery);

var accessDT;
var accessDTRef;
var createAcc;
var createAccModal;
var sendInviteBtn;
var modalHeaderTitle;
var accountsGlobal;
// const ACCOUNTS_URL = "../api/v1/accounts/?access-model";
const TYPISTS_URL = "../api/v1/users/typists";
const INVITE_TYPISTS_URL = "../api/v1/users/invite/";
const API_INSERT_URL = '../api/v1/access/';
const SET_DEFAULT_ACCESS_URL = "../api/v1/users/set-default/";

const CREATE_ACC_HEADER = "<i class=\"fas fa-key\"></i>&nbsp;Add Permission";
const UPDATE_ACC_HEADER = "<i class=\"fas fa-user-edit\"></i>&nbsp;Update Permission";

/** Fields */

// var email;

// -- combobox vars -- //
var accountBox;

var navLoverlay;
var loadingText;


$(document).ready(function () {

    const maximum_rows_per_page_jobs_list = 10;
    const REVOKE_ACCESS = '../api/v1/access/'; // + id ?out
    const ROLES_URL = "../api/v1/roles/";

    createAccModal = $("#modal");
    // createAccModal.style.display = "block";
    accessDT = $("#access-tbl");
    createAcc = $("#createAcc");
    sendInviteBtn = $("#sendInviteBtn");
    modalHeaderTitle = $("#modalHeaderTitle");
    loadingText = $("#loadingText");
    // loading = document.getElementById("overlay");
    loading = $("#overlay");

    // comboBoxes
    accountBox = $("#accountBox");
    let roleBox = $("#roleBox");

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

    $.ajaxSetup({
        cache: false
    });

    const refreshBtn = document.querySelector('#refresh_btn');
    // const goToUploader = document.querySelector('#newupload_btn');

    // Activate ripples effect for material buttons
    new mdc.ripple.MDCRipple(document.querySelector('#refresh_btn'));

    function goForm() {

        var formData = new FormData();
        formData.append("email", accountBox.val());
        formData.append("role", roleBox.val());
        changeLoading(true, "Sending invite..");
        $.ajax({
            type: 'POST',
            url: INVITE_TYPISTS_URL,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if(!response.error)
                {
                    accessDTRef.ajax.reload(); // refresh access table
                    createAccModal.modal('hide');
                    changeLoading(false);
                    $.confirm({
                        title: 'Success',
                        content: response.msg,
                        buttons: {
                            confirm: {
                                text: "Ok",
                                btnClass: 'btn-green'
                            }
                        }
                    });
                    accountBox.val("");
                }else{
                    createAccModal.modal('hide');
                    changeLoading(false);
                    accountBox.val("");
                    $.confirm({
                        title: 'Error',
                        content: response.msg,
                        buttons: {
                            confirm: {
                                btnClass: 'btn-red',
                                text: "Ok"
                            }
                        }
                    });
                }

            },
            error: function (err) {
                createAccModal.modal('hide');
                changeLoading(false);
                accountBox.val("");
                $.confirm({
                    title: 'Error',
                    content: err.responseJSON["msg"],
                    buttons: {
                        confirm: {
                            btnClass: 'btn-red',
                            text: "Ok"
                        }
                    }
                });
            }
        });


    }


    accessDTRef = accessDT.DataTable({
        rowId: 'access_id',
        "ajax": '../api/v1/access?dt&jl&out',
        "processing": true,
        lengthChange: false,
        pageLength: maximum_rows_per_page_jobs_list,
        autoWidth: false,
        "columns": [
            // {"data": "access_id"},
            // {"data": "acc_id"},
            // {"data": "acc_name"},
            // {"data": "username"},
            {"title": "Email",
            "data": "email"},
            {"title": "Role",
            "data": "role_desc"},
            // {"data": "access_id"}
        ]
    });

    $.contextMenu({
        selector: '.access-tbl tbody tr',
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
                            data["acc_name"]+ '</b> ?<br><br>'
                            // '<span style="color: red">USE WITH CAUTION THIS WILL DELETE THE access ACCOUNT AND ALL RELATED DATA INCLUDING JOB ENTRIES</span>',
                        ,buttons: {
                            confirm: {
                                text: "yes",
                                btnClass: 'btn-red',
                                action: function () {

                                    $.ajax({
                                        type: 'DELETE',
                                        url: REVOKE_ACCESS + id + "?out",
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
                                                        text: "Ok",
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
                                                        text: "Ok",
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
            }

        },
        items: {
            "delete": {name: "Revoke", icon: "fas fa-times"}
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
        sendInviteBtn.css("display", "inline");
        createAccModal.modal('show');
        $('#modal').stop().animate({
            scrollTop: 0
        }, 500);
    });

    createAccModal.on("hidden.bs.modal", function(){
        accountBox.val("");
    });

    sendInviteBtn.on("click", function (e) {
        goForm();
    });

    $("#closeAccModal").on("click", function (e) {
        createAccModal.modal('hide');
    });

    // retreive roles from db
    $.ajax({
        url: ROLES_URL,
        method: "GET",
        success: function (roles) {
            rolesGlobal = roles;
            roleBox.empty();
            for (const role of roles) {
                roleBox.append("<option value='" + role.role_id + "'>" +
                    role.role_desc +
                    "</option>");
            }
            roleBox.selectpicker('refresh');
        }
    });

    roleBox.selectpicker({
        liveSearch: true,
        liveSearchPlaceholder: "Choose Role"
    });

    /*getTypists();

    function getTypists()
    {
        $.ajax({
            url: TYPISTS_URL,
            method: "GET",
            success: function (accounts) {
                accountsGlobal = accounts;
                const cbox = document.getElementById("accountBox");
                cbox.innerHTML = ""; // clear fields
                for (const account of accounts) {
                    cbox.innerHTML += "<option value='" + account.email + "'>" +
                        // account.acc_id +
                        // " - " +
                        account.email +
                        "</option>";
                }
                accountBox.selectpicker({
                    liveSearch: true,
                    liveSearchPlaceholder: "Find Typist"
                });
                accountBox.selectpicker('refresh');
            }
        });
    }*/

});

function htmlEncodeStr(s) {
    return s.replace(/&/g, "&amp;")
        .replace(/>/g, "&gt;")
        .replace(/</g, "&lt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&lsquo;");
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

function validateEmail(mail) {
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
        return true;
    }
    // alert("You have entered an invalid email address!")
    return false;
}