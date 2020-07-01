// JavaScript Document
var g_fileName;
var dataTbl;

$(document).tooltip({
    //            track: true
    // items: ':not(#report_ifr)'
    items: ":not(#report_ifr,#TypistName, #jobNo, .tooltip)"
});

var currentFileID = 0;
var currentFileData;
var loadingConfirmBtn;
var loadingSub;
var loadingTitle;
var isConnected = false;
var isConnecting = true;
var firstLaunch = true;
let statusTxt;
const not_connected = "<i>Couldn't connect to controller <u id=\"reconnect\">reconnect?</u></i>";
const connecting = "<i>connecting to controller please wait...</i>";
const connected = "<i>Connected to vScription Controller as </i>";
const not_running = "<i>Controller not running. <u id=\"reconnect\">reconnect?</u></i>";
const greenColor = "#3e943c";
const orangeColor = "#f78d2d";
const versionCheck = "vCheck-"; // DONOT MODIFY
const welcomeName = "welcome-"; // DONOT MODIFY
var compactViewWindow;

$(document).ready(function () {

    const backend_url = "data/parts/backend_request.php";
    const form = document.querySelector("form");


    //***************** Websocket Connect on page load *****************//
    window.addEventListener("load", connect, false);
    //***************** Websocket Data *****************//

    var wsocket;

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
        else{
            // already connecting or connecting
        }

    }

    function onopen() {
        isConnected = true;
        isConnecting = false;

        setControllerStatus(connected, true);
        // wsocket.send("Transcribe Client Connected.");
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
        let msg = event.data.toString();
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
                    // let controllerVersion = msg.substring(7);
                    getLatestAppVersionNumber(msg.substring(7), checkVersions);
                }else if(msg.substring(0,8) === welcomeName){
                    // console.log("welcome name received = " + msg.substr(8));
                    setControllerStatus(connected + "<i>" + msg.substr(8) + "</i>", true);
                }
                break;

        }
    }

    function playAblePlayer(play){
        if(isAblePlayerMediaSet())
        {
            if(play)
            {
                AblePlayerInstances[0].playMedia()
                // console.log("Playing able player.");
            }
            else{
                AblePlayerInstances[0].pauseMedia()
                // console.log("Pausing able player.");
            }
        }
        else{
            // console.log("Able Player not loaded");
        }
    }

    function isAblePlayerMediaSet()
    {
        return AblePlayerInstances[0].media.src !== "";
    }


    $(document).ready(function () {

        $("#send").on("click", function (e) {
            let text = $("#txt").val();
            // console.log("should send " + text);
            wsocket.send(text);
        });

    });

    window.addEventListener("unload", logData, false);

    function logData() {
        wsocket.send("transcribe client disconnecting..");
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
    //checkBrowser();

    let modal = document.getElementById("modal");
    let loading = document.getElementById("modalLoading");

    // buttons styling init
    new mdc.ripple.MDCRipple(document.querySelector("#saveBtn"));
    new mdc.ripple.MDCRipple(document.querySelector("#suspendBtn"));
    new mdc.ripple.MDCRipple(document.querySelector("#discardBtn"));
    new mdc.ripple.MDCRipple(document.querySelector("#loadingConfirm"));

    loadingConfirmBtn = $("#loadingConfirm");
    loadingSub = $("#modalLoading .modal-content p i");
    loadingTitle = $("#modalLoading .modal-content h2");


    // loading.style.display = "block";

    loadingConfirmBtn.on("click", function() {
        // loading.style.display = "none";
        location.reload();
    });

    $(".close").on("click", function() {
        modal.style.display = "none";
    });


 /*   $(".modal-content").on("animationend webkitAnimationEnd", function(){

        $(".textarea-holder textarea").niceScroll(
            {
                hwacceleration: true,
                smoothscroll: true,
                cursorcolor: "#1e79be",
                autohidemode: false
            }
        );

    });*/

    $("#loadBtn").on("click", function (e) {
        modal.style.display = "block";
        chooseJob();
    });

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }


    form.addEventListener("submit", e => {

        e.preventDefault();
        let action = e.submitter.id;

        if (validateForm()) {
            const formData = new FormData()
            //let jobDetails = "";  //I don't know what data the JSON.parse will be so it'll be able to mutate
            var job_id = $("#jobNo").val().trim();

            var jobStatus = 3; //Need to figure out how to pass a 1 as jobStatus if clicking suspend and a 2 if clicking Save and Complete
            switch (action) {
                case "saveBtn":
                    // job status = 3 // complete
                    jobStatus = 3;
                    break;

                case "suspendBtn":
                    // job status = 2 //suspend
                    jobStatus = 2;
                    break;
            }

            // show popup dialog

            loadingSub.text("Saving " + job_id + " data");
            loadingTitle.text("Please wait..")
            loadingConfirmBtn.css("display", "none");
            loading.style.display = "block";




            //Get job details form DB

            let a1 = {
                file_id: currentFileID
            };

            // form submitted get job details
            $.post("data/parts/backend_request.php", {
                reqcode: 11,
                args: JSON.stringify(a1)
            }).done(function (data) {
                prepareDemos(data);
            });
            function prepareDemos(data) {  //I couldn't seem to access the data outside of the post call so I had to pass it to the function. How could this be accomplished without the function?
                var jobDetails = JSON.parse(data);
                var tinymceContent = tinymce.get("report").getContent();
                // Get demographics to update job with

                // var jobLengthStr = $('.able-duration').text().split("/")[1];


                /*if (jobLengthStr === "") {
                    alert('Audio Not Loaded Properly. Aborting');
                    return false;
                }*/

                let jobLengthSecsRaw = Math.round(AblePlayerInstances[0].seekBar.duration);
                let jobLengthSecs = new Date(jobLengthSecsRaw * 1000).toISOString().substr(11, 8).toString();
                let jobElapsedTimeSecs = Math.floor(AblePlayerInstances[0].seekBar.position).toString();

                var jobTranscribeDate = getCurrentDateTime();
                //Demographics to send to server;

                //Append form data for POST
                formData.append("report", tinymceContent);
                formData.append("reqcode", 32);
                formData.append("jobNo", jobDetails.job_id);
                // formData.append("jobLengthStr", jobLengthStr);
                formData.append("jobLengthSecs", jobLengthSecs);
                formData.append("jobLengthSecsRaw", jobLengthSecsRaw);
                // formData.append("jobElapsedTimeStr", jobElapsedTimeStr);
                formData.append("jobElapsedTimeSecs", jobElapsedTimeSecs);  //If user suspends job, we can use this to resume where they left ;
                formData.append("jobAuthorName", jobDetails.file_author);
                formData.append("jobFileName", jobDetails.origFilename);
                formData.append("tempFilename", jobDetails.tempFilename);
                $fmtOrigDateDic = moment(jobDetails.file_date_dict).format("yyyy-MM-D");
                formData.append("jobDateDic", $fmtOrigDateDic);
                formData.append("jobType", jobDetails.file_work_type);
                formData.append("jobSpeakerType", jobDetails.file_speaker_type);
                formData.append("jobComments", jobDetails.file_comment);
                formData.append("jobStatus", jobStatus);
                formData.append("file_id", currentFileID);

                //** Send form data to the server **//
                // -->  save or suspend clicked <-- //
                fetch(backend_url, {
                    method: "POST",
                    body: formData,
                }).then(response => {
                    response.text()
                        .then(data => {
                            if (response.ok) {
                            var a1 = {
                                mailtype: 10,
                                usertype: 2    //Client Admins
                            };

                            if(jobStatus === 3) // completed then send an email notification by this
                            {
                                $.post("data/parts/backend_request.php", {
                                    reqcode: 80,
                                    args: JSON.stringify(a1)
                                }).done(function (data) {
                                    // console.log(data);
                                });
                            }


                                clear
                                ();

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


                            } else {

                                alert("Error Saving Job. Please contact support - ${data}\n We will attempt to save the text contents to your clipboard if there is any");
                                tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody());
                                tinyMCE.activeEditor.execCommand( "Copy" );


                                clear();
                                // loadingTitle.text("Done");
                                // loadingSub.text("Job " + job_id + " data updated successfully.");
                                // loadingConfirmBtn.css('display', '');
                                loading.style.display = "none";
                            }
                        })
                });
            }

        }

        });

    /*$("#loadBtn").click(function () {
        // Complete Button click
        performClick('fileLoadDiag');

    });*/

    //	$('#mainlogo-td').css("width","100%");

    window.hidetxt = true;
    /*$("#control a").click(function () {
        hideShowForm();
    });*/

    $("#pop").click(function () {
        let currentMediaSrc = AblePlayerInstances[0].media.src;
        let seek = AblePlayerInstances[0].seekBar.position;
        let tinymceContent = tinymce.get('report').getContent().toString();

        if(tinymceContent !== "")
        {
            confirmDiscardTextPriorPopupSwitch();
        }else{
            prepareAndOpenPopup();
        }


        // openWindowWithPost(currentMediaSrc, seek);
        // document.getElementById('modalPlayerForm').submit();
    });

    $("#discardBtn").click(function () {
        clearWithConfirm();
    });

    //***************** Functions ***************//

    function clearWithConfirm() {

        $.confirm({
            title: 'Discard Form?',
            content: 'Are you sure do you want to discard current data?',
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

    function prepareAndOpenPopup(){

        var a1 = {
            fileID: currentFileID
        };
        $.post("data/parts/backend_request.php", {
            reqcode: 203,
            args: JSON.stringify(a1)
        }).done(function (data) {
            // 1. close websocket connection
            if(isConnected){
                isConnected = false;
                isConnecting = false;
                wsocket.send("transcribe client disconnecting..");
            }

            // 2. close/discard this
            if(currentFileID !== 0){
                suspendAndClearForDiscard();
            }

            // 3. open popup
            openPopupWindow();
        });
    }

    function confirmDiscardTextPriorPopupSwitch() {

        $.confirm({
            title: 'Discard?',
            content: 'Are you sure do you want to discard current text changes and switch to compact view?',
            buttons: {
                confirm: {
                    btnClass: 'btn-red',
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
        if(currentFileData.job_status === 0){
            new_status = 0;
        }


        let a1 = {
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

    function clearAfterDownload(askCompletePlayer) {
        //	 var retVal = confirm("Clear Form ?");


        $.confirm({
            title: 'File Saved',
            type: 'green',
            content: 'Do you want to clear the form?',
            buttons: {
                confirm: {
                    btnClass: 'btn-red',
                    action: function () {

                        //				$.alert('Confirmed!');
                        clear();

                        /*				if($('#completeBtn').hasClass('button-green') && askCompletePlayer)
                                           {
                                            askForCompletePlayer();
                                        }*/

                        return true;
                    }
                },
                cancel: function () {

                    /*				if ($('#completeBtn').hasClass('button-green') && askCompletePlayer) {
                                        askForCompletePlayer();
                                    }*/

                },

            }
        });

        /*
        if(retVal === true){
            clear();
            return true;
        }
        else{
            return false;
        }*/
    }

    function askForCompletePlayer() {
        $.confirm({
            title: 'Unload Audio File?',
            content: 'Do you want to unload and complete the loaded audio file?',
            buttons: {
                confirm: {
                    btnClass: 'btn-green',
                    action: function () {

                        completePlayer();
                        return true;
                    }
                },
                cancel: function () {},
                /*somethingElse: {
                    text: 'Something else',
                    btnClass: 'btn-blue',
                    keys: ['enter', 'shift'],
                    action: function(){
                        $.alert('Something else?');
                    }
                }*/
            }
        });
    }

    function clear() {
        document.getElementById("date").value = "";
        //		document.getElementById('dateT').value= "";
        document.getElementById('jobNo').value = "";
        //		document.getElementById('TypistName').value= "";
        document.getElementById('comments').value = "";
        document.getElementById('jobType').value = "";
        document.getElementById('authorName').value = "";
        document.getElementById('report').value = "";
        $('#date').garlic('destroy');
        //		$( '#dateT' ).garlic( 'destroy' );
        $('#jobType').garlic('destroy');
        $('#jobNo').garlic('destroy');
        //		$( '#TypistName' ).garlic( 'destroy' );
        $('#comments').garlic('destroy');
        $('#authorName').garlic('destroy');
        $('#report').garlic('destroy');
        document.title = 'Form';
        tinyMCE.activeEditor.setContent('');

        currentFileID = 0;

        completePlayer();

        //clearing validation
        $('.validate-form input').each(function () {
            //        $(this).focus(function(){
            hideValidate(this);
            //       });
        });

        $('#saveBtn').attr("disabled", "disabled");
        $('#suspendBtn').attr("disabled", "disabled");
        $('#discardBtn').attr("disabled", "disabled");
        tinyMCE.activeEditor.setMode("readonly");
    }

    function openPopupWindow() {
        AblePlayerInstances[0].pauseMedia();
        // var f = document.getElementById('modalPlayerForm');
        // f.src.value = src;
        // f.seek.value = seek;
        compactViewWindow = window.open("popup.php", "modalPlayer", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=530,height=262");
        // f.submit();
    }

    function completePlayer() {
        var $loadBtn = $('#loadBtn');
        var $completeBtn = $('#completeBtn');
        //Delete Temp Audio File
        var fullAudioSrc = AblePlayerInstances[0].media.src;
        var tempAudioFileName = fullAudioSrc.split("/").pop();
        // $(".pop").css("display", "none");
        // clearTempAudio(tempAudioFileName);

        AblePlayerInstances[0].seekTo(0);
        AblePlayerInstances[0].media.pause();

        setTimeout(function () {
            AblePlayerInstances[0].media.removeAttribute('src');
            AblePlayerInstances[0].media.load();
        }, 300);

        $loadBtn.removeClass('noHover');
        $('#loadBtn').html('<i class="fas fa-cloud-upload-alt"></i>&nbsp;Load');
        $loadBtn.find("i").show();
        $completeBtn.addClass('noHover');
        $completeBtn.addClass('button');
        $completeBtn.removeClass('button-green');
    }

    /*function hideShowForm() {
        if (window.hidetxt) {
            window.hidetxt = false;
            //hide text area
            $('#audio-td').attr('align', 'center');
            //$('#help-td').attr('align','center');
            $('#fix-td').css('display', 'none');
            $('#demo-td legend').css('display', 'none');
            $('#mainlogo-td').css('display', 'none');

            //$('fieldset').slideUp();
            $('fieldset').slideUp(400, function () {
                $('.form-style-5').css('min-width', '500px');
                $('.form-style-5').css('width', '500px');
                //$('.form-style-5').css('max-width','500px');

            });
            $('#saveBtn').css('display', 'none');
            $('.button-red').css('display', 'none');
            // $("#control a").html('Show Text Area');
        } else // show text area
        {
            window.hidetxt = true;
            $('#audio-td').attr('align', 'right');
            $('#help-td').attr('align', 'right');
            $('#fix-td').css('display', '');
            $('#demo-td legend').css('display', '');
            $('#mainlogo-td').css('display', '');
            $('fieldset').slideDown(400);
            $('.form-style-5').css('width', '90%');
            $('.form-style-5').css('min-width', '860px');
            $('#saveBtn').css('display', '');
            $('.button-red').css('display', '');
            // $("#control a").html('Hide Text Area');

            // Need to set timeout as we need to wait for the slideDown method to run before we get the correct values
            // vScriptCallback("show", document.getElementsByClassName('form-style-5').item(0).offsetHeight, document.getElementsByClassName('form-style-5').item(0).offsetWidth);
        }

    }*/

    function performClick(elemId) {
        var elem = document.getElementById(elemId);
        if (elem && document.createEvent) {
            var evt = document.createEvent("MouseEvents");
            evt.initEvent("click", true, false);
            elem.dispatchEvent(evt);
        }
    }

    /*-----Open dialog for typist to choose job---*/

    function chooseJob() {
        getTransJobList(addRowHandlers);
    }


    /*----Lookup job details-----*/

    function jobLoadLookup(fileID) {

        var a1 = {
            file_id: fileID
        };
        $.post("data/parts/backend_request.php", {
            reqcode: 7,
            args: JSON.stringify(a1)
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

    function decodeHtml(html) {
        var txt = document.createElement("textarea");
        txt.innerHTML = html;
        return txt.value;
    }

    /*-----LOAD FROM SERVER VERSUS LOCAL----*/
// Loading Audio File and details
    function loadIntoPlayer(data) {
        var jobDetails = JSON.parse(data);

        currentFileData = jobDetails;
        currentFileID = jobDetails.file_id; // globaly set current fileID

        // load previous suspended text into tinyMCE if suspended
        if(jobDetails.suspendedText !== null && jobDetails.job_status !== 0)
        {
            tinymce.get('report').setContent(decodeHtml(jobDetails.suspendedText));
            tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
            tinyMCE.activeEditor.selection.collapse(false);
        }

        $('.job').val(jobDetails.job_id);
        $('#authorName').val(jobDetails.file_author);
        $('#jobType').val(jobDetails.file_work_type);
        var dispDateFormat = moment(jobDetails.file_date_dict).format("D-MMM-yyyy");
        $('#date').val(dispDateFormat);
        $('#comments').val(jobDetails.file_comment);

        var $loadBtn = $('#loadBtn');
        var $completeBtn = $('#completeBtn');


        // audioTempFolder is a constant inside constants.js
        AblePlayerInstances[0].media.src = audioTempFolder + jobDetails.tempFilename;
        // AblePlayerInstances[0].media.src = jobDetails.base64;

        $loadBtn.addClass('noHover');
        $loadBtn.text(jobDetails.job_id + ' Loaded');
        $loadBtn.find("i").hide();

        // enable save etc.. buttons
        $('#saveBtn').removeAttr("disabled");
        $('#suspendBtn').removeAttr("disabled");
        $('#discardBtn').removeAttr("disabled");
        tinyMCE.activeEditor.setMode("design");


        var playPromise = AblePlayerInstances[0].media.play();

        if (playPromise !== undefined) {
            playPromise.then(_ => {
                // Automatic playback started!
                // Show playing UI.
                AblePlayerInstances[0].media.pause();
                AblePlayerInstances[0].seekTo(0);

                AblePlayerInstances[0].onMediaPause = function () {
                    if(AblePlayerInstances[0].seekBar.position-rewindAmountOnPause > 0)
                    {
                        AblePlayerInstances[0].seekTo(AblePlayerInstances[0].seekBar.position-rewindAmountOnPause);
                    }
                    else{
                        AblePlayerInstances[0].seekTo(0);
                    }

                }
            })
                .catch(error => {
                    // Auto-play was prevented
                    // Show paused UI.
                });
        }

        AblePlayer.prototype.onMediaNewSourceLoad = function () {


            if(jobDetails.job_status === 2 || jobDetails.job_status === 1) // suspend or being typed
            {
                // seek to last position

                AblePlayerInstances[0].playMedia();
                AblePlayerInstances[0].pauseMedia();
                // AblePlayerInstances[0].media.load();
                AblePlayerInstances[0].seekTo(jobDetails.last_audio_position - rewindAmountOnPause);
                // AblePlayerInstances[0].media.load();
            }

            // $(".pop").css("display", "inline");
        }

        AblePlayerInstances[0].onMediaPause = function () {
            if (AblePlayerInstances[0].seekBar.position - rewindAmountOnPause > 0) {
                AblePlayerInstances[0].seekTo(AblePlayerInstances[0].seekBar.position - rewindAmountOnPause);
            } else {
                AblePlayerInstances[0].seekTo(0);
            }
        }


        modal.style.display = "none"; //hide modal popup
    }
    /*----END LOAD FROM SERVER -----*/

    function validateForm() {

        var jobID = $('input[name="jobNo"]');
        var authorName = $('input[name="authorName"]');
        var TypistName = $('input[name="TypistName"]');

        // Row 2
        var jobType = $('input[name="jobType"]');
        var DateDic = $('input[name="DateDic"]');
        var DateTra = $('input[name="DateTra"]');

        var check = true;

        if (jobID.val().trim() == '') {
            showValidate(jobID);
            check = false;
        }

        if (authorName.val().trim() == '') {
            showValidate(authorName);
            check = false;
        }

        if (TypistName.val().trim() == '') {
            showValidate(TypistName);
            check = false;
        }

        if (jobType.val().trim() == '') {
            showValidate(jobType);
            check = false;
        }

        if (DateDic.val().trim() == '') {
            showValidate(DateDic);
            check = false;
        }

        if (DateTra.val().trim() == '') {
            showValidate(DateTra);
            check = false;
        }

        return check;
        //        return check;
        /*     if (check) {
                    document.getElementById('form').submit();

                    var jobNum = jobID.val().trim();
                    updateJobDetailsDB(jobStatus, jobNum);
                    //clear();
                    //clearAfterDownload(false); //ask to complete player = false


            } else {
                $(window).scrollTop(0);
                $.alert({

                    title: 'Error!',
                    type: 'red',
                    content: 'Please fill in all required form fields.',
                });
            } */
        //    });


    }

    $('.validate-form input').each(function () {
        $(this).focus(function () {
            hideValidate(this);
        });
    });


    function showValidate(input) {
        var thisAlert = $(input); //.parent();

        thisAlert.addClass('alert-validate');
    }

    function hideValidate(input) {
        var thisAlert = $(input);
        thisAlert.removeClass('alert-validate');
    }

    function getLatestAppVersionNumber(currentVersion ,_callback) {
        $.ajax({
            url: baseURL + "LatestVersion.txt",
            success: function (result) {
                // console.log("latestversion: " + result);
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
        /*var sUsrAg = navigator.userAgent;
        //alert(sUsrAg);
        if (sUsrAg.indexOf("78.0.3904.70") > -1) {
            $("#message_bar").slideUp();
        } else {
            $("#message_bar").slideDown("normal", "easeInOutBack");
        }*/
        if (updateAvailable) {
            $("#updated_version_bar span").html(v);
            $('#updated_version_bar a').attr('href', 'controller.php');
            $("#updated_version_bar").slideDown("normal", "easeInOutBack");
        } else {
            $("#updated_version_bar").slideUp();
        }
    }

    function getTransJobList(callback) {
        let maximum_rows_per_page_jobs_list = 7;

        // const maximum_rows_per_page_jobs_list = 7;
        var jobListResult = $('.jobs_tbl'); //populating fields

        $.post("data/parts/backend_request.php", {
            reqcode: 9
        }).done(function (res) {
            let response = JSON.parse(res);
            let data = response.data;
            let error = response.error;

            if(error){
                jobListResult.html(response.data);
                return true;
            }

            jobListResult.html(response.data);

            new mdc.dataTable.MDCDataTable(document.querySelector('.mdc-data-table'));
            dataTbl = $('.jobs_tbl');
            dataTbl.on( 'init.dt', function () {
                if(!$('.cTooltip').hasClass("tooltipstered"))
                {
                    $('.cTooltip').tooltipster({
                        animation: 'grow',
                        theme: 'tooltipster-punk',
                        arrow: true
                    });
                }
            } );
            dataTbl = dataTbl.DataTable(
                {
                    lengthChange: false,
                    searching: false,
                    lengthMenu: false,
                    pageLength: maximum_rows_per_page_jobs_list,
                    destroy: true
                    /*"columnDefs": [{
                        "targets": [0],
                        "visible": true,
                        "searchable": false,
                        "orderable": false
                    }]*/
                }
            );
            // this function below fires after new page is completely drawn
            dataTbl.on( 'draw', function () {
                if(!$('.cTooltip').hasClass("tooltipstered"))
                    $('.cTooltip').tooltipster({
                        animation: 'grow',
                        theme: 'tooltipster-punk',
                        arrow: true
                    });
            } );

            setTimeout(function() {
                callback(response.error);
            }, 1000);
        });

    }
    function addRowHandlers(error) {

        if(!error)
        {
            let table = $('.jobs_tbl').DataTable();

            $('.jobs_tbl tbody').on('click', 'tr', function () {
                let fileID = table.row(this).id();
                jobLoadLookup(fileID);
            } );
        }


    }

    function toggleClass(el, className) {
        if (el.className.indexOf(className) >= 0) {
            el.className = el.className.replace(className,"");
        }
        else {
            el.className  += className;
        }
    }

    /*----Lookup job details-----*/

    /*function clearTempAudio(tempFileName) {
        var a1 = {
            job_id: tempFileName
        };
        $.post("data/parts/backend_request.php", {
            reqcode: 33,
            args: JSON.stringify(a1)
        }).done(function () {
            //alert(data);
        });

    }*/


//Function to convert hh:mm:ss to seconds. This value is taken from ableplayer so
//we are assuming that it is calculating correctly
    function hmsToSecondsOnly(str) {
        var p = str.split(':'),
            s = 0, m = 1;

        while (p.length > 0) {
            s += m * parseInt(p.pop(), 10);
            m *= 60;
        }

        return s;
    }

    function getCurrentDateTime() {
        var today = new Date();
        var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
        var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
        var dateTime = date+' '+time;

        return dateTime;
    }

//
    function convertFormToRTF() {
        event.preventDefault();
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "formsave.php");
        xhr.onload = function(event){
            // console.log(`Response data: ${xhr.responseText}`);
            //alert("Success, server responded with: " + event.target.response); // raw response
            // return xhr.responseText;
            responseReceived(xhr.responseText)
        };
        // or onerror, onabort
        var formData = new FormData(document.getElementById("form"));
        xhr.send(formData);

    }

    function responseReceived(rawtext)
    {
        // console.log(`Raw text is: ${rawtext}`);
    }

});

$(function () {
    $("#date").datepicker({
        showAnim: "clip",
        dateFormat: "d-M-yy"
    });
});

$(function () {
    $("#dateT").datepicker({
        showAnim: "clip",
        dateFormat: "d-M-yy"
    });
});


$("#jobNo").keypress(function () {
    document.title = $('#jobNo').val();
});

$(function () {
    $("#accord").accordion({
        collapsible: true,
        header: "h3",
        //heightStyle: "fill",
        heightStyle: "content",
        active: false,
        activate: function () {
            $("body").getNiceScroll().resize();
        }
    });
});

function switchBack() // back to full view request from popup compact view
{
    compactViewWindow.close();
    $("#reconnect").click();
}