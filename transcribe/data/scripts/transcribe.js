// JavaScript Document
var g_fileName;
var dataTbl;

/*$(document).tooltip({
    //            track: true
    // items: ':not(#report_ifr)'
    items: ":not(#report_ifr,#TypistName, #jobNo, .tooltip)"
});*/

var currentFileID = 0;
var currentFileData;
var loadingConfirmBtn;
var loadingSub;
var loadingTitle;
var isConnected = false;
var isConnecting = true;
var firstLaunch = true;
let statusTxt;
const not_connected = "<i>Couldn't connect to controller <u id=\"reconnect\">reconnect?</u> <span class='download-controller' id=\"downloadController\">or download here</span></i>";
const connecting = "<i>connecting to controller please wait...</i>";
const connected = "<i>Connected to vScription Controller</i>";
const not_running = "<i>Controller not running. <u id=\"reconnect\">reconnect?</u> <span class='download-controller' id=\"downloadController\">or download here</span></i>";
const greenColor = "#3e943c";
const orangeColor = "#d34038";
const versionCheck = "vCheck-"; // DONOT MODIFY
const welcomeName = "welcome-"; // DONOT MODIFY
var compactViewWindow;
var jobsDT;
var jobsDTRef;
var demoExpandArrow;
var demoFields;
var loadingOv;

$(document).ready(function () {
    var loadingText = $("#loadingText");;
    const backend_url = "data/parts/backend_request.php";
    const files_api = "../api/v1/files/";
    const form = document.querySelector("form");
    loadingOv = $("#overlay");

    var jobTypeDropDown = $('#jobType');

    //***************** Websocket Connect on page load *****************//
    window.addEventListener("load", connect, false);
    //***************** Websocket Data *****************//

    var wsocket;

    statusTxt = $("#statusTxt");
    demoExpandArrow = $("#demoExpand");
    demoFields = $("#demoItems");

    function connect() {

        if (!isConnected || isConnecting) {
            isConnecting = true;
            setControllerStatus(connecting);

            wsocket = new WebSocket("ws://localhost:8001");
            wsocket.onopen = onopen;
            wsocket.onmessage = onmessage;
            wsocket.onerror = onerror;
            wsocket.onclose = onclose;
        } else {
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

        if (firstLaunch) {
            firstLaunch = false;
            setControllerStatus(not_running);
        } else {
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
                if (msg.substring(0, 7) === versionCheck) {
                    // let controllerVersion = msg.substring(7);
                    getLatestAppVersionNumber(msg.substring(7), checkVersions);
                }
                // else if (msg.substring(0, 8) === welcomeName) {
                    // todo re-enable if client name is needed to be shown on UI
                    // setControllerStatus(connected + "<i>" + msg.substr(8) + "</i>", true);
                // }
                break;

        }
    }

    demoExpandArrow.on("click", function () {
        if(demoExpandArrow.hasClass("fa-chevron-down"))
        {
            demoFields.slideUp();
            demoExpandArrow.removeClass("fa-chevron-down");
            demoExpandArrow.addClass("fa-chevron-right");
        }else{
            demoFields.slideDown();
            demoExpandArrow.removeClass("fa-chevron-right");
            demoExpandArrow.addClass("fa-chevron-down");
        }

    });

    function playAblePlayer(play) {
        if (isAblePlayerMediaSet()) {
            if (play) {
                AblePlayerInstances[0].playMedia();
                // console.log("Playing able player.");
            } else {
                AblePlayerInstances[0].pauseMedia();
                // console.log("Pausing able player.");
            }
        } else {
            // console.log("Able Player not loaded");
        }
    }

    function isAblePlayerMediaSet() {
        return AblePlayerInstances[0].media.src !== "";
    }


/*    $(document).ready(function () {

        $("#send").on("click", function (e) {
            let text = $("#txt").val();
            // console.log("should send " + text);
            wsocket.send(text);
        });

    });*/

    window.addEventListener("unload", logData, false);

    function logData() {
        wsocket.send("transcribe client disconnecting..");
    }


    // WEB SOCKET FUNCTIONS //
    let setControllerStatus = function (status, connected = false) {
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

                $("#downloadController").on("click", function (e) {
                    window.open(
                        'downloads.php',
                        '_blank'
                    );
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

    let modal = document.getElementById("modal");
    let loading = document.getElementById("modalLoading");

    // buttons styling init
    new mdc.ripple.MDCRipple(document.querySelector("#saveBtn"));
    new mdc.ripple.MDCRipple(document.querySelector("#suspendBtn"));
    new mdc.ripple.MDCRipple(document.querySelector("#discardBtn"));
    new mdc.ripple.MDCRipple(document.querySelector("#loadingConfirm"));
    new mdc.ripple.MDCRipple(document.querySelector("#pop"));
    new mdc.ripple.MDCRipple(document.querySelector("#logoutBtn"));

    jobsDT = $("#jobs-tbl");
    loadingConfirmBtn = $("#loadingConfirm");
    loadingSub = $("#modalLoading #loadingContent p i");
    loadingTitle = $("#modalLoading #loadingContent h2");


    // loading.style.display = "block";

    loadingConfirmBtn.on("click", function () {
        // loading.style.display = "none";
        location.reload();
    });

    $(".close").on("click", function () {
        modal.style.display = "none";
    });


    $("#loadBtn").on("click", function (e) {
        modal.style.display = "block";
        jobsDTRef.ajax.reload();
    });

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    let maximum_rows_per_page_jobs_list = 7;

    jobsDT.on('init.dt', function () {
        if (!$('.cTooltip').hasClass("tooltipstered")) {
            $('.download-icon').click(function () {
                let file_id = $(this).parent().parent().attr('id');
                download(file_id);
            });

            $('.cTooltip').tooltipster({
                animation: 'grow',
                theme: 'tooltipster-punk',
                arrow: true
            });
        }
    });

    jobsDTRef = jobsDT.DataTable({
        rowId: 'file_id',
        "ajax": 'api/v1/files?dt&file_status[mul]=0,1,2',
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
        "columns": [
            {
                "data": "job_id",
                render: function (data, type, row) {
                    if (row["file_comment"] != null) {
                        return data + " <i class=\"material-icons mdc-button__icon job-comment cTooltip\" aria-hidden=\"true\" title='"
                            + htmlEncodeStr(row["file_comment"])
                            + "'>speaker_notes</i>";
                    } else {
                        return data;
                    }
                }
            },
            {"data": "file_author"},
            {"data": "file_work_type"},
            {"data": "file_date_dict"},
            {"data": "job_upload_date"},
            {"data": "file_status_ref"},
            {
                "data": "audio_length",
                render: function (data) {
                    return new Date(data * 1000).toISOString().substr(11, 8);
                }
            }
        ],

        initComplete: function () {

            this.api().columns([0,3,4,6]).every( function () {
                var that = this;

                $( 'input', this.footer() ).on( 'keyup change clear', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
            } );

            this.api().columns([1,2,5]).every(function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo($(column.footer()).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                    });

                column.data().unique().sort().each(function (d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>')
                });
            });
        }
    });

    $(
        '#jobs-tbl tfoot th:eq(0),' +
        '#jobs-tbl tfoot th:eq(3),' +
        '#jobs-tbl tfoot th:eq(4),' +
        '#jobs-tbl tfoot th:eq(6)'
    ).each( function () {
        // var title = $(this).text();
        // $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
        $(this).html( '<input class="dt-search" type="text"/>' );
    } );

    jobsDT.on('draw.dt', function () {

            $('.download-icon').click(function () {
                let file_id = $(this).parent().parent().attr('id');
                download(file_id);
            });

            if (!$('.cTooltip').hasClass("tooltipstered")) {
                $('.cTooltip').tooltipster({
                    animation: 'grow',
                    theme: 'tooltipster-punk',
                    arrow: true
                });
            }
        }
    );

    $('#jobs-tbl tbody').on('click', 'tr', function () {
        let fileID = jobsDTRef.row(this).id();
        changeLoading(true, "Loading "+ jobsDTRef.row(this).data()["job_id"]);
        jobLoadLookup(fileID);
    });


    form.addEventListener("submit", e => {

        e.preventDefault();
        let action = e.submitter.id;

        // if (validateForm()) {
        const formData = new FormData();
        //let jobDetails = "";  //I don't know what data the JSON.parse will be so it'll be able to mutate
        // var job_id = $("#jobNo").val().trim();

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
        loadingSub.text("Saving " + currentFileData.job_id + " data");
        loadingTitle.text("Please wait..")
        loadingConfirmBtn.css("display", "none");
        loading.style.display = "block";

        /*
            audio_length=,
            last_audio_position=,
            file_status=?,
            file_transcribed_date=, // built in API
            job_transcribed_by=,  // built in API

            job_document_html=,
            job_document_rtf=
            */

        var tinymceContent = tinymce.get("report").getContent();
        // Get demographics to update job with

        let jobLengthSecsRaw = Math.round(AblePlayerInstances[0].seekBar.duration);
        // let jobLengthSecs = new Date(jobLengthSecsRaw * 1000).toISOString().substr(11, 8).toString();
        let jobElapsedTimeSecs = Math.floor(AblePlayerInstances[0].seekBar.position).toString();

        // var jobTranscribeDate = getCurrentDateTime();
        //Demographics to send to server;

        formData.append("audio_length", jobLengthSecsRaw);
        formData.append("last_audio_position", jobElapsedTimeSecs);  //If user suspends job, we can use this to resume where they left ;
        formData.append("file_status", jobStatus);
        formData.append("job_document_html", tinymceContent);
        formData.append("file_work_type", $("#jobType").val());
        formData.append("typist_comments", $("#comments").val());
        formData.append("set_role", 3);

        //Append form data for POST

        // formData.append("reqcode", 32);
        // formData.append("jobNo", jobDetails.job_id);
        // formData.append("jobLengthStr", jobLengthStr);
        // formData.append("jobLengthSecs", jobLengthSecs);
        // formData.append("jobElapsedTimeStr", jobElapsedTimeStr);
        // formData.append("jobAuthorName", jobDetails.file_author);
        // formData.append("jobFileName", jobDetails.origFilename);
        // formData.append("tempFilename", jobDetails.tempFilename);
        // $fmtOrigDateDic = moment(jobDetails.file_date_dict).format("yyyy-MM-D");
        // formData.append("jobDateDic", $fmtOrigDateDic);

        // formData.append("jobSpeakerType", jobDetails.file_speaker_type);
        // formData.append("jobComments", jobDetails.file_comment);

        // formData.append("file_id", currentFileID);

        //** Send form data to the server **//
        // -->  save or suspend clicked <-- //

        let currentFile = currentFileID;
        if(clear())
        {
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
                        loadingSub.text("Job " + currentFileData.job_id + " suspended");
                        loadingConfirmBtn.css("display", "");
                    } else if (jobStatus === 3) {

                        loadingTitle.text("Done");
                        loadingSub.text("Job " + currentFileData.job_id + " marked as complete");
                        loadingConfirmBtn.css("display", "");
                    } else {
                        loadingTitle.text("Done");
                        loadingSub.text("Job " + currentFileData.job_id + " updated successfully");
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
            tinyMCE.activeEditor.execCommand("Copy");


            // clear();
            // loadingTitle.text("Done");
            // loadingSub.text("Job " + currentFileData.job_id + " data updated successfully.");
            // loadingConfirmBtn.css('display', '');
            loading.style.display = "none";
        }

        // }

    });

    window.hidetxt = true;

    $("#pop").click(function () {
        // let currentMediaSrc = AblePlayerInstances[0].media.src;
        // let seek = AblePlayerInstances[0].seekBar.position;
        let tinymceContent = tinymce.get('report').getContent().toString();

        if (tinymceContent !== "") {
            confirmDiscardTextPriorPopupSwitch();
        } else {
            prepareAndOpenPopup();
        }


        // openWindowWithPost(currentMediaSrc, seek);
        // document.getElementById('modalPlayerForm').submit();
    });

    $("#logoutBtn").click(function () {
        location.href = "logout.php";
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
                cancel: function () {
                },
            }
        });

    }

    function prepareAndOpenPopup() {

        var a1 = {
            fileID: currentFileID
        };
        $.post("data/parts/backend_request.php", {
            reqcode: 203,
            args: JSON.stringify(a1)
        }).done(function (data) {
            // 1. close websocket connection
            if (isConnected) {
                isConnected = false;
                isConnecting = false;
                wsocket.send("transcribe client disconnecting..");
            }

            // 2. close/discard this
            if (currentFileID !== 0) {
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

    function suspendAndClearForDiscard() {
        var new_status = 2;
        if (currentFileData.job_status === 0) {
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

    }

    function clear() {
        document.getElementById("date").value = "";
        //		document.getElementById('dateT').value= "";
        document.getElementById('jobNo').value = "";
        //		document.getElementById('TypistName').value= "";
        document.getElementById('comments').value = "";
        document.getElementById('jobType').value = "";
        document.getElementById('authorName').value = "";
        document.getElementById('user_field_1').value = "";
        document.getElementById('user_field_2').value = "";
        document.getElementById('user_field_3').value = "";
        document.getElementById('report').value = "";
        $('#date').garlic('destroy');
        //		$( '#dateT' ).garlic( 'destroy' );
        jobTypeDropDown.garlic('destroy');
        $("#jobType").attr("disabled","")
        $('#jobNo').garlic('destroy');
        //		$( '#TypistName' ).garlic( 'destroy' );
        $('#comments').garlic('destroy');
        $("#comments").attr("disabled","")

        $('#authorName').garlic('destroy');
        $('#user_field_1').garlic('destroy');
        $('#user_field_2').garlic('destroy');
        $('#user_field_3').garlic('destroy');
        $('#report').garlic('destroy');
        document.title = 'Form';
        tinyMCE.activeEditor.setContent('');

        currentFileID = 0;

        $('#saveBtn').attr("disabled", "disabled");
        $('#suspendBtn').attr("disabled", "disabled");
        $('#discardBtn').attr("disabled", "disabled");
        tinyMCE.activeEditor.setMode("readonly");

        return completePlayer();
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
        var loadBtn = $('#loadBtn');
        var completeBtn = $('#completeBtn');
        //Delete Temp Audio File
        var fullAudioSrc = AblePlayerInstances[0].media.src;
        var tempAudioFileName = fullAudioSrc.split("/").pop();
        // $(".pop").css("display", "none");
        // clearTempAudio(tempAudioFileName);

        AblePlayerInstances[0].seekTo(0);
        AblePlayerInstances[0].media.pause();

        AblePlayerInstances[0].media.removeAttribute('src');
        AblePlayerInstances[0].seekBar.setPosition(0);
        AblePlayerInstances[0].media.load();
        /*setTimeout(function () {

            AblePlayerInstances[0].media.load();
        }, 300);*/

        loadBtn.removeClass('noHover');
        loadBtn.html('<i class="fas fa-cloud-upload-alt"></i>&nbsp;Load');
        loadBtn.find("i").show();
        completeBtn.addClass('noHover');
        completeBtn.addClass('button');
        completeBtn.removeClass('button-green');
        return true;
    }

    /*----Lookup job details-----*/

    function jobLoadLookup(fileID) {

        $.get(files_api + fileID + "?tr").done(function (data) {
            if (data) {
                loadIntoPlayer(data);
            } else {
                switchUI(false);
                changeLoading(false, "Loading transcribe..");
                $.confirm({
                    title: 'Error',
                    content: "Job doesn't exist or you don't have permission to access it.",
                    buttons: {confirm: {btnClass: 'btn-green', text: 'ok'}}
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
        if (jobDetails.suspendedText !== null && jobDetails.job_status !== 0) {
            tinymce.get('report').setContent(decodeHtml(jobDetails.suspendedText));
            tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
            tinyMCE.activeEditor.selection.collapse(false);
        }

        $('#jobNo').val(jobDetails.job_id);
        $('#authorName').val(jobDetails.file_author);
        $("#jobType").removeAttr("disabled");
        // check if value doesn't exist

        if(jobTypeDropDown.find("[value='"+jobDetails.file_work_type.toLowerCase().trim()+"']").length)
        {
            jobTypeDropDown.val(jobDetails.file_work_type.toLowerCase().trim());
        }else{
            // append the option and select it
            var option = '<option value="'+jobDetails.file_work_type.toLowerCase().trim()+'">'+jobDetails.file_work_type+'</option>';
            jobTypeDropDown.html(jobTypeDropDown.html() + option);
            jobTypeDropDown.val(jobDetails.file_work_type.toLowerCase().trim());
        }

        // console.log("job type from database : =-----> " + jobDetails.file_work_type);

        var dispDateFormat = moment(jobDetails.file_date_dict).format("DD-MMM-YYYY hh:mm:ss a");
        $('#date').val(dispDateFormat);
        $('#comments').val(jobDetails.typist_comments);
        $('#file_comment').val(jobDetails.file_comment);
        $("#comments").removeAttr("disabled");
        // console.log("Typist comments: " + jobDetails.typist_comments);
        $('#user_field_1').val(jobDetails.user_field_1);
        $('#user_field_2').val(jobDetails.user_field_2);
        $('#user_field_3').val(jobDetails.user_field_3);

        var $loadBtn = $('#loadBtn');
        var $completeBtn = $('#completeBtn');


        // audioTempFolder is a constant inside constants.js
        AblePlayerInstances[0].media.src = audioTempFolder + jobDetails.tmp_name;
        // AblePlayerInstances[0].media.src = jobDetails.base64;

        $loadBtn.addClass('noHover');
        $loadBtn.text(jobDetails.job_id + ' Loaded');
        $loadBtn.find("i").hide();

        // enable save etc.. buttons
        if(rl == 3)
        {
            $('#saveBtn').removeAttr("disabled");
            $('#suspendBtn').removeAttr("disabled");
        }
        $('#discardBtn').removeAttr("disabled");
        tinyMCE.activeEditor.setMode("design");

        // AblePlayerInstances[0].
 /*       AblePlayerInstances[0].onMediaNewSourceLoad = function () {

            if (jobDetails.job_status == 2 || jobDetails.job_status == 1) // suspend or being typed
            {
                // seek to last position
                AblePlayerInstances[0].seekTo(jobDetails.last_audio_position - rewindAmountOnPause);
                setTimeout(function(){
                    AblePlayerInstances[0].seekBar.setPosition(jobDetails.last_audio_position - rewindAmountOnPause);
                    }, 50);
            } else {
                AblePlayerInstances[0].seekTo(0);
            }
        }
*/
        if (jobDetails.job_status == 2 || jobDetails.job_status == 1) // suspend or being typed
        {
            // seek to last position
            $("#audio1").attr("data-start-time",jobDetails.last_audio_position - rewindAmountOnPause);
            AblePlayerInstances[0].media.currentTime = jobDetails.last_audio_position - rewindAmountOnPause;
        } else {
            $("#audio1").attr("data-start-time",0);
            AblePlayerInstances[0].media.currentTime = 0;
        }
        AblePlayerInstances[0].media.load();


        AblePlayerInstances[0].onMediaPause = function () {
            if (AblePlayerInstances[0].seekBar.position - rewindAmountOnPause > 0) {
                AblePlayerInstances[0].seekTo(AblePlayerInstances[0].seekBar.position - rewindAmountOnPause);
            } else {
                AblePlayerInstances[0].seekTo(0);
            }
        }


        modal.style.display = "none"; //hide modal popup
        changeLoading(false, "Loading transcribe..");
    }

    /*----END LOAD FROM SERVER -----*/



    function getLatestAppVersionNumber(currentVersion, _callback) {
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

        if (updateAvailable) {
            $("#updated_version_bar span").html(v);
            $('#updated_version_bar a').attr('href', 'downloads.php');
            $("#updated_version_bar").slideDown("normal", "easeInOutBack");
        } else {
            $("#updated_version_bar").slideUp();
        }
    }

    changeLoading(false);
    function changeLoading(show, text = false) {
        if(!show){
            loadingOv[0].style.display = "none";
            // loadingOv.fadeOut();
            $("body").css("overflow", "auto");
        }else{
            $("body").css("overflow", "none");
            if(text) loadingText.html(text);
            loadingOv[0].style.display = "block";
            // loading.fadeIn();
        }
    }
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

function htmlEncodeStr(s) {
    return s.replace(/&/g, "&amp;")
        .replace(/>/g, "&gt;")
        .replace(/</g, "&lt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&lsquo;");
}

function switchBack() // back to full view request from popup compact view
{
    compactViewWindow.close();
    $("#reconnect").click();
}