// JavaScript Document
var g_fileName;

$(document).tooltip({
    //            track: true
    // items: ':not(#report_ifr)'
    items: ':not(#report_ifr,#TypistName, #jobNo)'
});

var currentFileID = 0;
var loadingConfirmBtn;
var loadingSub;
var loadingTitle;

$(document).ready(function () {

    getLatestAppVersionNumber(checkVersions);


    const backend_url = 'data/parts/backend_request.php';
    const form = document.querySelector('form');


    $("body").niceScroll({
        hwacceleration: true,
        smoothscroll: true,
        cursorcolor: "white",
        cursorborder: 0,
        scrollspeed: 10,
        mousescrollstep: 20,
        cursoropacitymax: 0.7
        //		cursorwidth: 16

    });
    $.ajaxSetup({
        cache: false
    });
    checkBrowser();

    let modal = document.getElementById("modal");
    let loading = document.getElementById("modalLoading");

    // buttons styling init
    new mdc.ripple.MDCRipple(document.querySelector('#saveBtn'));
    new mdc.ripple.MDCRipple(document.querySelector('#suspendBtn'));
    new mdc.ripple.MDCRipple(document.querySelector('#discardBtn'));
    new mdc.ripple.MDCRipple(document.querySelector('#loadingConfirm'));

    loadingConfirmBtn = $('#loadingConfirm');
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



    $('#loadBtn').on('click', function (e) {
        // alert("test");
        modal.style.display = "block";
        chooseJob();
    });

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }


    form.addEventListener('submit', e => {

        e.preventDefault();
        let action = e.submitter.id;

        // console.log("event fired");
        if (validateForm()) {
            const formData = new FormData()
            //let jobDetails = "";  //I don't know what data the JSON.parse will be so it'll be able to mutate
            var job_id = $('#jobNo').val().trim();

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
            loadingConfirmBtn.css('display', 'none');
            loading.style.display = 'block';




            //Get job details form DB
            // console.log('Getting Transcription Job Details for job#: ' + job_id + ' for demographic update');

            let a1 = {
                file_id: currentFileID
            };

            // form submitted get job details
            $.post("data/parts/backend_request.php", {
                reqcode: 11,
                args: JSON.stringify(a1)
            }).done(function (data) {
                // console.log(data);
                // console.log(typeof data);
                prepareDemos(data);
            });
            function prepareDemos(data) {  //I couldn't seem to access the data outside of the post call so I had to pass it to the function. How could this be accomplished without the function?
                var jobDetails = JSON.parse(data);
                var tinymceContent = tinymce.get('report').getContent();
                // Get demographics to update job with

                // var jobLengthStr = $('.able-duration').text().split("/")[1];


                /*if (jobLengthStr === "") {
                    alert('Audio Not Loaded Properly. Aborting');
                    return false;
                }*/

                let jobLengthSecs = AblePlayerInstances[0].seekBar.duration;
                let jobElapsedTimeSecs = Math.floor(AblePlayerInstances[0].seekBar.position);

                var jobTranscribeDate = getCurrentDateTime();
                //Demographics to send to server;

                // console.log(`Data from DB lookup....`);
                // console.log(`Job Number: ${jobDetails.job_id}`);
                // console.log(`Author: ${jobDetails.file_author}`);
                // console.log(`Filename: ${jobDetails.origFilename}`);
                // console.log(`Temp filename is: ${jobDetails.tempFilename}`);
                // console.log(`Dictated Date: ${jobDetails.file_date_dict}`);
                // console.log(`Work Type: ${jobDetails.file_work_type}`);
                // console.log(`Speaker Type: ${jobDetails.file_speaker_type}`);
                // console.log(`Upload Comments: ${jobDetails.file_comment}`);
                // console.log(`Job length is: ${jobLengthSecs} seconds`);
                // console.log(`Job Elapsed Time is: ${jobElapsedTimeSecs} seconds`);
                // console.log(`Job Status is: ${jobStatus}`);
                // console.log(`Transcribe Date is: ${jobTranscribeDate}`);

                //Append form data for POST
                formData.append("report", tinymceContent);
                formData.append("reqcode", 32);
                formData.append("jobNo", jobDetails.job_id);
                // formData.append("jobLengthStr", jobLengthStr);
                formData.append("jobLengthSecs", jobLengthSecs);
                // formData.append("jobElapsedTimeStr", jobElapsedTimeStr);
                formData.append("jobElapsedTimeSecs", jobElapsedTimeSecs);  //If user suspends job, we can use this to resume where they left ;
                formData.append("jobAuthorName", jobDetails.file_author);
                formData.append("jobFileName", jobDetails.origFilename);
                // formData.append("jobTempFileName", jobDetails.tempFilename);
                formData.append("jobDateDic", jobDetails.file_date_dict);
                formData.append("jobType", jobDetails.file_work_type);
                formData.append("jobSpeakerType", jobDetails.file_speaker_type);
                formData.append("jobComments", jobDetails.file_comment);
                formData.append("jobStatus", jobStatus);
                formData.append("file_id", currentFileID);

                //** Send form data to the server **//
                // -->  save or suspend clicked <-- //
                fetch(backend_url, {
                    method: 'POST',
                    body: formData,
                }).then(response => {
                    response.text()
                        .then(data => {
                            if (response.ok) {
                                // console.log(typeof data);
                                // console.log('Job update call was successful');
                                //var responseArr = JSON.parse(data);
                                // console.log(`Full response from backend: ${data})`);
                                //tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody());
                               //tinyMCE.activeEditor.execCommand( "Copy" );
                            // Generate Email Notifications 
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
                                    console.log(data);
                                });
                            }


                                clear();

                            // todo add if clause using the below commented variable
                            // -> jobDetails.file_status
                                
                                loadingTitle.text("Done");
                                loadingSub.text("Job " + job_id + " data updated successfully.");
                                loadingConfirmBtn.css('display', '');

                            } else {

                                alert(`Error Saving Job. Please contact support - ${data}\n We will attempt to save the text contents to your clipboard if there is any`);
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
});

$(function () {
    $("#date").datepicker({
        showAnim: "clip",
        dateFormat: "dd/mm/yy"
    });
});

$(function () {
    $("#dateT").datepicker({
        showAnim: "clip",
        dateFormat: "dd/mm/yy"
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

function suspendAndClearForDiscard()
{
    let a1 = {
        file_id: currentFileID,
        new_status: 2 //suspended

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

$(document).ready(function () {


    var $loadBtn = $('#loadBtn');
    var $completeBtn = $('#completeBtn');

    ////////// Complete Button click \\\\\\\\\\\\\\
    $("#completeBtn").click(function () {


        var tinymceContent = tinymce.get('report').getContent();
        //check for any text in the text area
        if (!tinymceContent == "") //if not empty check saving
        {
            //override complete == true
                validateForm();
 
        } else { //empty text area just complete the file
            completePlayer(); //OK
        }


    });

    $("#loadBtn").click(function () {
        // Complete Button click
        performClick('fileLoadDiag');

    });

    //	$('#mainlogo-td').css("width","100%");

    window.hidetxt = true;
    $("#control a").click(function () {
        hideShowForm();
    });


});

function completePlayer() {
    var $loadBtn = $('#loadBtn');
    var $completeBtn = $('#completeBtn');
    //Delete Temp Audio File
    var fullAudioSrc = AblePlayerInstances[0].media.src;
    var tempAudioFileName = fullAudioSrc.split("/").pop();
    clearTempAudio(tempAudioFileName);

    try {
        vScriptCallback("AudioComplete", g_fileName); //For vScription Application to move file to complete folder
    } catch (err) {}

    //$('#fileLoadDiag').val(''); //clear file dialog
    AblePlayerInstances[0].media.pause();
    AblePlayerInstances[0].media.load();

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

function hideShowForm() {
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
            setTimeout(function () {
                vScriptCallback("hide", document.getElementsByClassName('form-style-5').item(0).offsetHeight, document.getElementsByClassName('form-style-5').item(0).offsetWidth);
            }, 1200);
            $('.form-style-5').css('min-width', '500px');
            $('.form-style-5').css('width', '500px');
            //$('.form-style-5').css('max-width','500px');

        });
        $('#saveBtn').css('display', 'none');
        $('.button-red').css('display', 'none');
        $("#control a").html('Show Text Area');
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
        $("#control a").html('Hide Text Area');

        // Need to set timeout as we need to wait for the slideDown method to run before we get the correct values
        vScriptCallback("show", document.getElementsByClassName('form-style-5').item(0).offsetHeight, document.getElementsByClassName('form-style-5').item(0).offsetWidth);
    }

}

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
    // console.log('Getting Transcription Job Details for job#: ' + jobNum);
    // var jobDetailsResult = $('.table_data'); //populating fields

    var a1 = {
        file_id: fileID
    };
    $.post("data/parts/backend_request.php", {
        reqcode: 7,
        args: JSON.stringify(a1)
    }).done(function (data) {
        loadIntoPlayer(data);
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
    // console.log(data);
    currentFileID = jobDetails.file_id; // globaly set current fileID

    // console.log(`Job Number: ${jobDetails.job_id}`);
    // console.log(`Author: ${jobDetails.file_author}`);
    // console.log(`Filename: ${jobDetails.origFilename}`);
    // console.log(`Temp filename is: ${jobDetails.tempFilename}`);
    // console.log(`Dictated Date: ${jobDetails.file_date_dict}`);
    // console.log(`Work Type: ${jobDetails.file_work_type}`);
    // console.log(`Speaker Type: ${jobDetails.file_speaker_type}`);
    // console.log(`Upload Comments: ${jobDetails.file_comment}`);

    // load previous suspended text into tinyMCE if suspended
    if(jobDetails.suspendedText !== null && jobDetails.job_status !== 0)
    {
        tinymce.get('report').setContent(decodeHtml(jobDetails.suspendedText));
    }

    $('.job').val(jobDetails.job_id);
    $('#authorName').val(jobDetails.file_author);
    $('#jobType').val(jobDetails.file_work_type);
    $('#date').val(jobDetails.file_date_dict);
    $('#comments').val(jobDetails.file_comment);

    var $loadBtn = $('#loadBtn');
    var $completeBtn = $('#completeBtn');
    //g_fileName = fileName;


    // audioTempFolder is a constant inside constants.js
    // AblePlayerInstances[0].media.src = audioTempFolder + jobDetails.tempFilename;
    AblePlayerInstances[0].media.src = jobDetails.base64;
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

        // console.log("media loaded with status: " + jobDetails.job_status);
        if(jobDetails.job_status === 2 || jobDetails.job_status === 1) // suspend or being typed
        {
            // seek to last position
            // console.log("seeking to " + jobDetails.last_audio_position);

            AblePlayerInstances[0].playMedia();
            AblePlayerInstances[0].pauseMedia();
            AblePlayerInstances[0].seekTo(jobDetails.last_audio_position - rewindAmountOnPause);

            /*setTimeout(function() {
                console.log("seek")
                AblePlayerInstances[0].seekTo(jobDetails.last_audio_position - rewindAmountOnPause);
                // AblePlayerInstances[0].seekTo(112);
            }, 1000);*/
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
            console.log('Updating job details on server');
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

function checkBrowser(updateAvailable) {
    var sUsrAg = navigator.userAgent;
    //alert(sUsrAg);
    if (sUsrAg.indexOf("78.0.3904.70") > -1) {
        $("#message_bar").slideUp();
    } else {
        $("#message_bar").slideDown("normal", "easeInOutBack");
    }
    if (updateAvailable) {
        $("#updated_version_bar").slideDown("normal", "easeInOutBack");
    } else {
        $("updated_version_bar").slideUp();
    }
}

function getLatestAppVersionNumber(_callback) {
    $.ajax({
        url: "https://pro.vscription.com/LatestVersion.txt",
        success: function (result) {
            _callback(result.split("-")[0].trim(), checkBrowser);
        }
    });
}

function checkVersions(result, checkBrowser) {
    var latestAppVersion;
    latestAppVersion = result;
    var localAppVersion;
    try {
        localAppVersion = AppVersion;
    } catch (err) {
        localAppVersion = "";
    }
    if (latestAppVersion === localAppVersion || localAppVersion === "") {
        checkBrowser(0);
    } else {
        checkBrowser(1);
    }
};

function getTransJobList(callback) {
    let maximum_rows_per_page_jobs_list = 7;
    // console.log('Getting Transcription Job List...');
    // const maximum_rows_per_page_jobs_list = 7;
    var jobListResult = $('.jobs_tbl'); //populating fields

    $.post("data/parts/backend_request.php", {
        reqcode: 9
    }).done(function (data) {
        jobListResult.html(data);

        new mdc.dataTable.MDCDataTable(document.querySelector('.mdc-data-table'));
        var tbl = $('.jobs_tbl');
        var dataTbl = tbl.DataTable(
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

       /* tbl.on( 'click', 'tr', function () {
            var id = dataTbl.row( this ).id();

            alert( 'Clicked row id '+id );
        } );*/




    });

    setTimeout(function() {
        callback();
    }, 1000);
}
function addRowHandlers() {
    // console.log("Calling addRowHandler");

    // var table = $('#example').DataTable();
    let table = $('.jobs_tbl').DataTable();

    $('.jobs_tbl tbody').on('click', 'tr', function () {
        let fileID = table.row(this).id();
        jobLoadLookup(fileID);
    } );
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

function clearTempAudio(tempFileName) {
    // console.log('Clearing temp audio file');

    var a1 = {
        job_id: tempFileName
    };
    $.post("data/parts/backend_request.php", {
        reqcode: 33,
        args: JSON.stringify(a1)
    }).done(function () {
        //alert(data);
    });

}


/* function updateJobDetailsDB(callback) {
    //var rtfToSave = convertFormToRTF();
    var job_id = $('.job').val();
    console.log('Updating Job Details on DB...');
    var jobLengthStr = $('.able-duration').text().split("/")[1];
    var jobLengthSecs = hmsToSecondsOnly(jobLengthStr);
    var file_transcribe_date = getCurrentDateTime();
    var transcribed_by = $('.typistemail').text();
    
    console.log(`Job Number is: ${job_id}`);
    console.log(`Job length is: ${jobLengthSecs} seconds`);
    console.log(`Job Status is: 3`);
    console.log(`Transcribe Date is: ${getCurrentDateTime()}`);
    console.log(`Transcribed By: ${transcribed_by}`);
    console.log(`Raw text value: ${rtfToSave}`);

    a1 = {
        job_id: job_id,
        audio_length: jobLengthSecs,
        file_status: 3,
        file_transcribe_date: file_transcribe_date,
        transcribed_by: transcribed_by
    }

} */

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
            console.log(`Response data: ${xhr.responseText}`);
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
    console.log(`Raw text is: ${rawtext}`);
}