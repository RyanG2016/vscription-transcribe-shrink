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
var roleBox;

var loading;
var loadingText;


$(document).ready(function () {

    const maximum_rows_per_page_jobs_list = 10;
    const REVOKE_ACCESS = '../api/v1/access/'; // + id ?out

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

    $("body").niceScroll({
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
        // formData.append("uid", uid);
        changeLoading(true, "Sending invite..");
        $.ajax({
            type: 'POST',
            url: INVITE_TYPISTS_URL,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                accessDTRef.ajax.reload(); // refresh access table
                getTypists(); // refresh typists dropdown list
                createAccModal.modal('hide');
                changeLoading(false);
                $.confirm({
                    title: 'Success',
                    content: response["msg"],
                    buttons: {
                        confirm: {
                            btnClass: 'btn-green'
                        }
                    }
                });


            },
            error: function (err) {
                createAccModal.modal('hide');
                changeLoading(false);
                $.confirm({
                    title: 'Error',
                    content: err.responseJSON["msg"],
                    buttons: {
                        confirm: {
                            btnClass: 'btn-red',
                            text: "OK"
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
        columnDefs: [
            {
                targets: ['_all'],
                className: 'mdc-data-table__cell'
            }
        ],

        /*
        * 				<th>ID</th>
                        <th>Acc ID</th>
                        <th>Account</th>
                        <th>accessname</th>
                        <th>Role ID</th>
                        <th>Role</th>

        * */
        "columns": [
            // {"data": "access_id"},
            // {"data": "acc_id"},
            // {"data": "acc_name"},
            // {"data": "username"},
            {"data": "email"},
            // {"data": "acc_role"},
            {"data": "role_desc"},
            // {"data": "access_id"}
            /*,
            render: function (data, type, row) {
                if(data)
                {
                    return data;
                }else{
                    return row["state_ref"];
                }
            }
        },
        { "data": "account_status" },
        { "data": "enabled",
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
                                            getTypists();
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
            // "edit": {name: "Edit", icon: "fas fa-key"},
            // "def": {name: "Set as default", icon: "fas fa-toggle-on"},
            // "sep1": "---------",
            "delete": {name: "Revoke", icon: "fas fa-times"}
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
        sendInviteBtn.css("display", "inline");
        createAccModal.modal('show');
        $('#modal').stop().animate({
            scrollTop: 0
        }, 500);
    });

    sendInviteBtn.on("click", function (e) {
        goForm();
    });

    $("#closeAccModal").on("click", function (e) {
        createAccModal.modal('hide');
    });


    getTypists();

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
    }

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