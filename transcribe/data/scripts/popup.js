// JavaScript Document
// var g_fileName;
var dataTbl;

var currentFileID = 0;
var currentFileData;
var loadingConfirmBtn;
var loadingSub;
var loadingTitle;
var isConnected = false;
var isConnecting = true;
var firstLaunch = true;
var statusTxt; 
var overlay;
var not_connected = "<i>Couldn't connect to controller <u id=\"reconnect\">reconnect?</u></i>";
var connecting = "<i>connecting to controller please wait...</i>";
var connected = "<i>Connected to vScription Controller</i>";
var not_running = "<i>Controller not running. <u id=\"reconnect\">reconnect?</u></i>";
var greenColor = "#3e943c";
var orangeColor = "#f78d2d";
var versionCheck = "vCheck-"; // DONOT MODIFY
const welcomeName = "welcome-"; // DONOT MODIFY
var backend_url = "data/parts/backend_request.php";
var files_api = "../api/v1/files/";
var wsocket;
var jobPickerWindow;

if(window.opener === null){
    window.location.href = "/index.php";
}

$(document).ready(function () {

    $(".overlay").css("display", "");
    $(".loaded").css("display", "none");


    new mdc.ripple.MDCRipple(document.querySelector("#saveBtn"));
    new mdc.ripple.MDCRipple(document.querySelector("#suspendBtn"));
    new mdc.ripple.MDCRipple(document.querySelector("#discardBtn"));
    new mdc.ripple.MDCRipple(document.querySelector("#loadBtn"));
    new mdc.ripple.MDCRipple(document.querySelector("#loadingConfirm"));

    //***************** Websocket Connect on page load *****************//
    window.addEventListener("load", connect, false);
    //***************** Websocket Data *****************//

    statusTxt = $("#statusTxt");

    function connect() {

        if(!isConnected || isConnecting)
        {
            isConnecting = true;
            setControllerStatus(connecting);

            wsocket = new WebSocket("ws://localhost:8001");
            wsocket.onopen = onopen;
            wsocket.onmessage = onmessage;
            wsocket.onerror = onerror;
            wsocket.onclose = onclose;
        }
        /*else{
            // already connecting or connecting
        }*/

    }

    function onopen() {
        isConnected = true;
        isConnecting = false;

        setControllerStatus(connected, true);
    }

    function onclose() {
        isConnected = false;
        isConnecting = false;

        if(firstLaunch)
        {
            firstLaunch = false;
            setControllerStatus(not_running);
        }
        else{
            setControllerStatus(not_connected);
        }

    }

    function onmessage(event) {
        // console.log("Data received: " + event.data);
        var msg = event.data.toString();
        // console.log("received from server: " + msg);
        switch (msg) {

            case "play":
                playAblePlayer(true);
                break;

            case "pause":
                playAblePlayer(false);
                break;

            case "rw":
                AblePlayerInstances[0].handleRewind();
                break;

            case "ff":
                AblePlayerInstances[0].handleFastForward();
                break;

            default:
                if(msg.substring(0,7) === versionCheck)
                {
                    // var controllerVersion = msg.substring(7);
                    getLatestAppVersionNumber(msg.substring(7), checkVersions);
                } else if (msg.substring(0,8) === welcomeName) {
                    // todo re-enable if client name is needed to be shown on UI
                    // setControllerStatus(connected + "<i>" + msg.substr(8) + "</i>", true);
                }
                // break;

        }
    }

    function playAblePlayer(play){
        if(isAblePlayerMediaSet())
        {
            if(play)
            {
                AblePlayerInstances[0].playMedia();
                // console.log("Playing able player.");
            }
            else{
                AblePlayerInstances[0].pauseMedia();
                // console.log("Pausing able player.");
            }
        }
        /*else{
            // console.log("Able Player not loaded");
        }*/
    }

    function isAblePlayerMediaSet()
    {
        return AblePlayerInstances[0].media.src !== "";
    }


    // WEB SOCKET FUNCTIONS //
    let setControllerStatus = function (status, connected=false) {
        // text
        statusTxt.html(status);


        // text color
        switch (connected) {
            case true:
                statusTxt.css("color", greenColor);
                break;

            case false:
                statusTxt.css("color", orangeColor);

                $("#reconnect").on("click", function (e) {
                    connect();
                });
                break;
        }
    }

    //***************** End Websocket data *****************//


    $.ajaxSetup({
        cache: false
    });

    var loading = document.getElementById("modalLoading");

    loadingConfirmBtn = $("#loadingConfirm");
    loadingSub = $("#modalLoading .modal-content p i");
    loadingTitle = $("#modalLoading .modal-content h2");


    // loading.style.display = "block";

    loadingConfirmBtn.on("click", function() {
        loading.style.display = "none";
    });

    
    $("#loadBtn").on("click", function() {
        chooseJob();
    });

    $("#switchBackBtn").click(function () {
        postToParent();
    });


    function submit(complete) {

        if (validateForm()) {
            var formData = new FormData();

            var job_id = $(".jobNo").html().trim();

            var jobStatus = 5; // assume save & complete click -> 5 Completed No Text
            if(!complete) {
                    // job status = 2 //suspend
                    jobStatus = 2;
            }

            // show popup dialog

            loadingSub.text("Saving " + job_id + " data");
            loadingTitle.text("Please wait..");
            loadingConfirmBtn.css("display", "none");
            loading.style.display = "block";


            var jobLengthSecsRaw = Math.round(AblePlayerInstances[0].seekBar.duration);
            var jobElapsedTimeSecs = Math.floor(AblePlayerInstances[0].seekBar.position).toString();

            formData.append("audio_length", jobLengthSecsRaw);
            formData.append("last_audio_position", jobElapsedTimeSecs);  //If user suspends job, we can use this to resume where they left ;


            if(currentFileData.file_status == 7 || currentFileData.file_status == 11)
            {
                formData.append("file_status", 11);
            }else{
                formData.append("file_status", jobStatus);
            }

            formData.append("set_role", 3);


            let currentFile = currentFileID;
            if(clear()){
                $.ajax({
                    type: 'POST',
                    url: files_api+currentFile,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {

                        // clear();

                        if (jobStatus === 2) {

                            loadingTitle.text("Done");
                            loadingSub.text("Job " + job_id + " suspended");
                            loadingConfirmBtn.css("display", "");
                        } else if (jobStatus === 3) {

                            loadingTitle.text("Done");
                            loadingSub.text("Job " + job_id + " marked as complete");
                            loadingConfirmBtn.css("display", "");
                        } else {
                            loadingTitle.text("Done");
                            loadingSub.text("Job " + job_id + " updated successfully");
                            loadingConfirmBtn.css("display", "");
                        }
                    },
                    error: function (err) {
                        errorWhileSavingFile();
                    }
                });
            }


            function errorWhileSavingFile() {
                alert("Error Saving Job. Please contact support - ${data}\n We will attempt to save the text contents to your clipboard if there is any");
                tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody());
                tinyMCE.activeEditor.execCommand( "Copy" );


                // clear();
                // loadingTitle.text("Done");
                // loadingSub.text("Job " + job_id + " data updated successfully.");
                // loadingConfirmBtn.css('display', '');
                loading.style.display = "none";
            }

        }

    }

    //***************** Functions ***************//

    function confirmDiscardTextPriorFullModeSwitch() {

        $.confirm({
            title: "Discard?",
            content: "Are you sure do you want to discard current text changes and switch to full view?",
            buttons: {
                confirm: {
                    btnClass: "btn-red",
                    action: function () {
                        prepareAndOpenPopup();
                        return true;
                    }
                },
                cancel: function () {
                    return true;
                },
            }
        });

    }

    function suspendAndClearForDiscard()
    {
        var new_status = 2;
        if(currentFileData.file_status == 0){
            new_status = 0;
        }
        else if(currentFileData.file_status == 7)
        {
            new_status = 7;
        }else if(currentFileData.file_status == 11)
        {
            new_status == 11;
        }


        var a1 = {
            file_id: currentFileID,
            new_status: new_status // suspend or awaiting.

        };
        $.post("data/parts/backend_request.php", {
            reqcode: 16,
            args: JSON.stringify(a1)
        }).done(function (data) {

        });

        clear();
    }


    function clear() {

        $(".title-default-text").css("display", "inline-block");
        $(".title-text-holder").css("display", "none");

        currentFileID = 0;

        /*//clearing validation
        $(".validate-form input").each(function () {
            hideValidate(this);
        });*/

        switchUI(false);
        return completePlayer();
    }

    function completePlayer() {
        var fullAudioSrc = AblePlayerInstances[0].media.src;
        var tempAudioFileName = fullAudioSrc.split("/").pop();
        // $(".pop").css("display", "none");

        AblePlayerInstances[0].seekTo(0);
        AblePlayerInstances[0].media.pause();

        AblePlayerInstances[0].media.removeAttribute("src");
        AblePlayerInstances[0].media.load();
        return true;
    }

    /*-----Open dialog for typist to choose job---*/

    function chooseJob() {
        jobPickerWindow = window.open("job_picker.php", "modalPicker", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=1000,height=500");
    }

    $("#discardBtn").click(function () {
        clearWithConfirm();
    });

    $("#saveBtn").click(function () {
        submit(true);
    });

    $("#suspendBtn").click(function () {
        submit(false);
    });


    function clearWithConfirm() {

        $.confirm({
            title: 'Discard Form?',
            content: 'Are you sure do you want to discard current job?',
            buttons: {
                confirm: {
                    btnClass: 'btn-red',
                    action: function () {

                        suspendAndClearForDiscard();
                        return true;
                    }
                },
                cancel: function () {},
            }
        });

    }

    function validateForm() {

        // var jobID = $("input[name=\"jobNo\"]");
        // var authorName = $("input[name=\"authorName\"]");
        // var TypistName = $("input[name=\"TypistName\"]");

        // Row 2
        // var jobType = $("input[name=\"jobType\"]");
        // var DateDic = $("input[name=\"DateDic\"]");
        // var DateTra = $("input[name=\"DateTra\"]");

        var check = true;

        /*if (jobID.val().trim() == "") {
            showValidate(jobID);
            check = false;
        }

        if (authorName.val().trim() === "") {
            showValidate(authorName);
            check = false;
        }

        if (TypistName.val().trim() === "") {
            showValidate(TypistName);
            check = false;
        }

        if (jobType.val().trim() === "") {
            showValidate(jobType);
            check = false;
        }

        if (DateDic.val().trim() === "") {
            showValidate(DateDic);
            check = false;
        }

        if (DateTra.val().trim() === "") {
            showValidate(DateTra);
            check = false;
        }*/

        return check;
    }

    /*$(".validate-form input").each(function () {
        $(this).focus(function () {
            hideValidate(this);
        });
    });*/


    /*function showValidate(input) {
        var thisAlert = $(input); //.parent();

        thisAlert.addClass("alert-validate");
    }

    function hideValidate(input) {
        var thisAlert = $(input);
        thisAlert.removeClass("alert-validate");
    }*/

    function getLatestAppVersionNumber(currentVersion ,_callback) {
        $.ajax({
            url: baseURL + "LatestVersion.txt",
            success: function (result) {
                console.log("latestversion: " + result);
                _callback(result.split("-")[0].trim(), currentVersion);
            }
        });
    }

    function checkVersions(latestAppVersion, currentControllerVersion) {

        if (latestAppVersion > currentControllerVersion) {
            checkBrowser(true, latestAppVersion);
        } else {
            checkBrowser(false, latestAppVersion);
        }
    }

    function checkBrowser(updateAvailable, v) {
        if (updateAvailable) {
            $("#updated_version_bar span").html(v);
            $("#updated_version_bar a").attr("href", "controller.php");
            $("#updated_version_bar").slideDown("normal", "easeInOutBack");
        } else {
            $("#updated_version_bar").slideUp();
        }
    }


    function getCurrentDateTime() {
        var today = new Date();
        var date = `${today.getFullYear()}-${today.getMonth() + 1}-${today.getDate()}`;
        var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
        return `${date} ${time}`;
    }

    $.post(backend_url, {
        reqcode: 204
    }).done(function (data) {
        // console.log("session saved id is " + data);
        if(data)
        {
            if(data === "0") // no job to load just a regular switch to mini view
            {
                switchUI(false);
            }
            else{
                loadID(data);
            }
        }
        else{
            switchUI(false);
        }
    });

});

/*----Lookup job details-----*/

function loadID(fileID) {
    // console.log("ID to load: " + fileID);
    $(".overlay").css("display", "");

    if(jobPickerWindow)
    {
        jobPickerWindow.close();
    }


    $.get(files_api + fileID + "?tr", {
    }).done(function (data) {
        if(data)
        {
            loadIntoPlayer(data);
        }
        else{
            switchUI(false);
            $.confirm({
                title: 'Error',
                content: "Job doesn't exist or you don't have permission to access it.",
                buttons: { confirm: {btnClass: 'btn-green', text: 'ok'} }
            });
        }
    });

}

/*-----LOAD FROM SERVER VERSUS LOCAL----*/
// Loading Audio File and details
function loadIntoPlayer(data) {
    var jobDetails = JSON.parse(data);

    currentFileData = jobDetails;
    currentFileID = jobDetails.file_id; // globally set current fileID

    $("#jobNo").html(jobDetails.job_id);
    $("#author").html(jobDetails.file_author);
    $("#jobType").html(jobDetails.file_work_type);

    // audioTempFolder is a varant inside varants.js
    AblePlayerInstances[0].media.src = audioTempFolder + jobDetails.tmp_name;
    // AblePlayerInstances[0].media.src = jobDetails.base64;

/*    AblePlayer.prototype.onMediaNewSourceLoad = function () {

        // var playPromise = AblePlayerInstances[0].playMedia();
        if(jobDetails.job_status == 2 || jobDetails.job_status == 1) // suspend or being typed
        {
            // seek to last position
            AblePlayerInstances[0].seekTo(jobDetails.last_audio_position - rewindAmountOnPause);
            setTimeout(function(){
                AblePlayerInstances[0].seekBar.setPosition(jobDetails.last_audio_position - rewindAmountOnPause);
            }, 50);
        }else {
            AblePlayerInstances[0].seekTo(0);
        }
        switchUI(true);
    }*/

    if (jobDetails.file_status == 2 || jobDetails.file_status == 1) // suspend or being typed
    {
        // seek to last position
        $("#audio1").attr("data-start-time",jobDetails.last_audio_position - rewindAmountOnPause);
        AblePlayerInstances[0].media.currentTime = jobDetails.last_audio_position - rewindAmountOnPause;
    } else {
        $("#audio1").attr("data-start-time",0);
        AblePlayerInstances[0].media.currentTime = 0;
    }
    AblePlayerInstances[0].media.load();
    switchUI(true);

    AblePlayerInstances[0].onMediaPause = function () {
        if (AblePlayerInstances[0].seekBar.position - rewindAmountOnPause > 0) {
            AblePlayerInstances[0].seekTo(AblePlayerInstances[0].seekBar.position - rewindAmountOnPause);
        } else {
            AblePlayerInstances[0].seekTo(0);
        }
    }

}
/*----END LOAD FROM SERVER -----*/
function switchUI(loaded)
{
    if(loaded)
    {
        $(".title-default-text").css("display", "none");
        $(".title-text-holder").css("display", "");
        $(".loaded").css("display", "");
        $(".not-loaded").css("display", "none");
        $(".overlay").css("display", "none");
    }
    else{
        $(".title-default-text").css("display", "inline-block");
        $(".title-text-holder").css("display", "none");
        $(".loaded").css("display", "none");
        $(".not-loaded").css("display", "");
        $(".overlay").css("display", "none");
    }
}

// switch back to transcribe full mode
function postToParent()
{
    // disconnect();
    if(window.opener !== null)
    {
        window.opener.switchBack();
    } else{
        // transcribe isn't open -> open it
        window.open("transcribe.php", "_blank");
        close();
    }
}
/*
function disconnect() {
    if(isConnected || isConnecting)
    {
        wsocket.send("transcribe client disconnecting..");
    }
}*/
