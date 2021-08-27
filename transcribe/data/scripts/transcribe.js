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

var shortcutsDT;
var shortcutsDTRef;
var shortcutModal;

// var demoFields;
var loadingOv;
var has_captions = false;
var refreshShortcuts = true;

var lastShortcutValue = "";

$(document).ready(function () {
    var loadingText = $("#loadingText");
    var demoDiv = $("#demoDiv");
    // var demoSideBarShowing = true;
    var demoSideBar = $("#demoSidebar")
    var toggleDemoBarBtn = $("#toggleDemoBar");

    const backend_url = "data/parts/backend_request.php";
    const files_api = "../api/v1/files/";
    const shortcuts_end_point = "../api/v1/users/shortcuts";
    const form = document.querySelector("form");
    loadingOv = $("#overlay");


    var captions = '';

    toggleDemoBarBtn.on("click", function(){
        toggleDemoBarBtn.children().toggleClass("right"); // toggle arrow
        demoSideBar.transition('slide left');
        // demoSideBarShowing = !demoSideBarShowing;
    })

    // highlighting variables
    var currentHighlightedID = null;
    var endLimit = 0;       // current highlighted text end time
    var startLimit = 0;     // current highlighted text start time

    var jobTypeDropDown = $('#jobType');
    var captionsSearch = $('#captionsSearch');
    let userFields = $('#userFields');
    // var captionResult = $('#captionResult');
    $('.ui.sdropdown').sdropdown();

    // $("#demoDiv").show()
    let modalCapSearch = document.getElementById("modalSearchCaptions");

    var searchEngine =   $("#searchEngine");
    var compactView =   $("#pop");
    var showCompBtn =   $("#showCompBtn");
    var showingCompleted = false;

    compactView.popover({
        // html: true,
        content: "Compact View",
        trigger: "hover"

    });

    $("#capSrcClose").on('click', function(){
        modalCapSearch.style.display = "none";
        captionsSearch.val("");
        dt.clear().draw();
    });

    showCompBtn.on('click', function() {
        if (!showingCompleted) {
            showingCompleted = true;
            $(this).html('<i class="fas fa-eye-slash"></i> Hide Completed')
            jobsDTRef.ajax.url( 'api/v1/files/completed?dt' ).load(); // &file_status[mul]=3,11
        } else {
            showingCompleted = false;
            jobsDTRef.ajax.url( 'api/v1/files/pending?dt' ).load(); // &file_status[mul]=0,1,2,7,11
            $(this).html('<i class="fas fa-eye-slash"></i> View Completed')
        }
    });

    $("#capSrcClear").on('click', function(){
        captionsSearch.val("");
        dt.clear().draw();
    });

    searchEngine.on('click', function(){
        modalCapSearch.style.display = "block";
    });

    function parseCapResultForDT(result)
    {
        let arr = [];
        for (let i = 0; i < result.length; i++) {
            // result[i].start
            // result[i].end
            let start = 'ST'+result[i].start.toFixed(2).replace(".","T");
            /*if(result[i].start == 0)
            {
                start = "ST0T00";
            }*/
            arr[i] = {"start":  start,"line": result[i].lines[0]}
        }
        // return {"data": arr};
        return arr;
    }
    var dtData = {};

    let dt = $('#captionsTbl').DataTable( {
        data: dtData,
        rowId: 'start',
        autoWidth: false,
        "language": {
            "emptyTable": "No matching text found"
            // "zeroRecords": "No matching text found"
        },
        "lengthChange": false,
        // "paging":   false,
        "searching": false,
        columns: [
            {
                title: "Search Results",
                data: "line"
            }
            // { }
        ]
    } );

    function seekFromID(id)
    {
        AblePlayerInstances[0].seekTo(id.replace("ST","").replace("T", "."));
    }


    $('#captionsTbl tbody').on('click', 'tr', function () {
        // console.log("click " + dt.row(this).id());
        modalCapSearch.style.display = "none";
        tinymce.activeEditor.selection.select( tinymce.activeEditor.dom.select('#' + dt.row(this).id())[0] );
        tinymce.activeEditor.selection.getNode().scrollIntoView(true);
        seekFromID(dt.row(this).id());
        // changeLoading(true, "Loading "+ jobsDTRef.row(this).data()["job_id"]);
        // jobLoadLookup(fileID);
    });

    $("#searchBtn").on("click", function()
    {
        let result = filterValuePart(captions, captionsSearch.val());
        dtData = parseCapResultForDT(result);
        dt.clear().rows.add(dtData).draw();
    });

    captionsSearch.on("keyup", function(event) {
        // Number 13 is the "Enter" key on the keyboard
        let keycode = event.which || event.keyCode;
        if (keycode === 13) {
            // Cancel the default action, if needed
            event.preventDefault();
            // Trigger the button element with a click
            let result = filterValuePart(captions, captionsSearch.val());
            dtData = parseCapResultForDT(result);

            dt.clear().rows.add(dtData).draw();

            /*captionResult.html(
                JSON.stringify(dtData, null, 2)
            );*/

            // console.log(  JSON.stringify(dtData, null, 2) );
            return false;
        }
    });



    //***************** Websocket Connect on page load *****************//
    window.addEventListener("load", connect, false);
    //***************** Websocket Data *****************//

    var wsocket;

    statusTxt = $("#statusTxt");
    // demoFields = $("#demoItems");

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



    $.ajaxSetup({
        cache: false
    });

    let modal = document.getElementById("modal");
    let loading = document.getElementById("modalLoading");
    shortcutModal = document.getElementById("shortcutsModal");


    // buttons styling init
    // new mdc.ripple.MDCRipple(document.querySelector("#saveBtn"));
    // new mdc.ripple.MDCRipple(document.querySelector("#suspendBtn"));
    // new mdc.ripple.MDCRipple(document.querySelector("#discardBtn"));
    // new mdc.ripple.MDCRipple(document.querySelector("#loadingConfirm"));
    // new mdc.ripple.MDCRipple(document.querySelector("#pop"));

    jobsDT = $("#jobs-tbl");
    shortcutsDT = $("#shortcuts-tbl");
    loadingConfirmBtn = $("#loadingConfirm");
    loadingSub = $("#modalLoading #loadingContent p");
    loadingTitle = $("#modalLoading #loadingContent h2");


    // loading.style.display = "block";

    loadingConfirmBtn.on("click", function () {
        loading.style.display = "none";
    });

    $(".close").on("click", function () {
        modal.style.display = "none";
    });


    /*$("#loadBtn").on("click", function (e) {
        loadNewJob();
    });*/

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
        else if (event.target == shortcutModal) {
            shortcutModal.style.display = "none";
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
    $.fn.dataTable.ext.errMode = 'none';
    jobsDTRef = jobsDT.DataTable({
        rowId: 'file_id',
        "ajax": 'api/v1/files/pending?dt',
        "processing": true,
        lengthChange: false,
        pageLength: maximum_rows_per_page_jobs_list,
        autoWidth: false,

        "columns": [
            {
                "title": "Job #",
                "data": "job_id",
                render: function (data, type, row) {
                    var addition = "";
                    var result = "";

                    let fields = ["user_field_1", "user_field_2", "user_field_3", "typist_comments"];
                    /* Additional Popup */
                    fields.forEach(value => {
                        if(row[value] !== null && row[value] !== "")
                        {
                            if(addition !== "")
                            {
                                addition += "<br><br>";
                                // addition += "\n";
                            }
                            addition += `<b>${value}</b>: ${row[value]}`;
                        }
                    });
                    if (row["file_comment"] != null) {

                        result = `<i class="fas fa-comment-alt-lines vspt-fa-blue cTooltip" data-html="true"  title="${htmlEncodeStr(row["file_comment"])}"></i>`;
                    }
                    if(addition !== "")
                    {
                        result += `&nbsp;<i class="fas fa-info-square vspt-fa-blue cTooltip" data-html="true"  title="${addition}"></i>`;
                    }
                    if(result)
                    {
                        result = `<span class="align-middle float-right">${result}</span>`
                    }
                    return data + result;
                }
            },
            {
                "title": "Author",
                "data": "file_author"
            },
            {
                "title": "Job Type",
                "data": "file_work_type"
            },
            {
                "title": "Date Dictated",
                "data": "file_date_dict"
            },
            {
                "title": "Date Uploaded",
                "data": "job_upload_date"
            },
            {
                "title": "Job Status",
                "data": "file_status_ref"
            },
            {
                "title": "Job Length",
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
                var select = $('<select class="form-control"><option value=""></option></select>')
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
        $(this).html( '<input class="dt-search form-control" type="text"/>' );
    } );

    jobsDT.on( 'error.dt', function ( e, settings, techNote, message ) {
        // console.log( 'An error has been reported by DataTables: ', message );
        console.log( 'Failed to retrieve data' );
    } )

    jobsDT.on('draw.dt', function () {

            $('.download-icon').click(function () {
                let file_id = $(this).parent().parent().attr('id');
                download(file_id);
            });

            if (!$('.cTooltip').hasClass("tooltipstered")) {
                $('.cTooltip').tooltipster({
                    animation: 'grow',
                    side: ['bottom', 'right'],
                    theme: 'tooltipster-punk',
                    contentAsHTML: true,
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
        if (e.which == 13 || e.keyCode == 13) {
            e.preventDefault();
            return false;
        }

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
                // job status = 2 // suspend
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

        if(currentFileData.file_status == 7 || currentFileData.file_status == 11)
        {
            formData.append("file_status", 11);
        }else{
            formData.append("file_status", jobStatus);
        }

        formData.append("job_document_html", tinymceContent);
        formData.append("file_work_type", $("#jobType option:selected").text());
        formData.append("typist_comments", $("#comments").val());
        formData.append("set_role", 3);
        formData.append("user_field_3", $("#user_field_3").val());

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
        $.ajax({
            type: 'POST',
            url: files_api+currentFile,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {

                // clear();

                if(!response.error)
                {
                    clear();
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
                }else{
                    loadingTitle.text("Error");
                    loadingSub.html(response.msg + "<br><i><small>We will attempt to save the text contents to your clipboard if there is any.</small></i>");
                    loadingConfirmBtn.css("display", "");

                    try{
                        tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody());
                        tinyMCE.activeEditor.execCommand("Copy");
                    }catch (e) {
                        alert("Couldn't copy your work to clipboard.")
                    }
                }
            },
            error: function (err) {
                errorWhileSavingFile();
            }
        });



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

    compactView.click(function () {
        // let currentMediaSrc = AblePlayerInstances[0].media.src;
        // let seek = AblePlayerInstances[0].seekBar.position;
        let tinymceContent = tinymce.get('report').getContent().toString();

        if (tinymceContent !== "") {
            confirmDiscardTextPriorPopupSwitch();
        } else {
            prepareAndOpenPopup();
        }
    });


    $("#discardBtn").click(function () {
        clearWithConfirm();
    });


    /* Shortcuts table load */
    shortcutsDTRef = shortcutsDT.DataTable({
        // rowId: 'file_id',
        "ajax": shortcuts_end_point + '?dt',
        "dom": '<"dt_toolbar">frtip',
        "processing": true,
        lengthChange: false,
        pageLength: 10,
        autoWidth: false,

        "columns": [
            {
                "title": "Shortcut",
                "data": "name"
            },
            {
                "title": "Value",
                "data": "val"
            }

        ],

        initComplete: function () {

       /*     this.api().columns([0,3,4,6]).every( function () {
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
            });*/
        }
    });
    $("div.dt_toolbar").html(
        '<button class="btn btn-sm save-button mt-1 ml-1" id="addShortcut" >\n' +
        '            <i class="fas fa-plus"></i> Add\n' +
        '        </button>'
    );

    $("#addShortcut").on('click', function()
    {
        $.confirm({
            title: '<i class="fas fa-plus-circle" style="color: #66bb6a"></i> Add Shortcut',
            content: '' +
                '<form action="" class="formName">' +
                    '<div class="form-group no-gutters">' +
                        '<div class="form-row" style="margin: 0 !important; justify-content: center">' +
                            '<input type="text" placeholder="Shortcut" class="shortcut form-control col-4" required />' +
                            '<input type="text" placeholder="Value" class="value form-control col-7 ml-2" required />' +
                        '</div>'+
                    '</div>'+
                '</form>',
            buttons: {
                formSubmit: {
                    text: 'Add',
                    btnClass: 'btn-blue',
                    action: function () {
                        let name = this.$content.find('.shortcut').val();
                        let value = this.$content.find('.value').val();
                        var formData = new FormData();
                        formData.append("name", name);
                        formData.append("val", value);

                        if(name.trim() == "" || value.trim() == "")
                        {
                            alert("Check your input");
                        }
                        else{
                            // ajax
                            $.confirm({
                                title: 'Please Wait..',
                                // theme: 'supervan',
                                columnClass: 'col-6',
                                content: function(){
                                    var self = this;
                                    // self.setContent('Checking callback flow');
                                    return $.ajax({
                                        type: 'POST',
                                        method: 'POST',
                                        url: shortcuts_end_point,
                                        data: formData,
                                        processData: false,
                                        contentType: false
                                    }).done(function (response) {

                                        // handle responses
                                        // -------------
                                        if(!response.error)
                                        {
                                            refreshShortcuts = true;

                                            self.setTitle("Success!");
                                            self.setType("green");
                                            self.setContent("Shortcut added");

                                            self.buttons.ok.setText("Ok");
                                            self.buttons.ok.addClass("btn-green");
                                            self.buttons.ok.removeClass("btn-default");
                                            self.buttons.close.hide();
                                            shortcutsDTRef.ajax.reload();
                                        }else{

                                            self.setTitle("oops..");
                                            self.setType("red");
                                            self.setContent(xhr.responseJSON["msg"]);
                                            self.buttons.ok.setText("Ok");
                                            self.buttons.ok.addClass("btn-green");
                                            // self.buttons.ok
                                            // self.buttons.ok.btnClass = "btn-green"
                                            self.buttons.ok.removeClass("btn-default")
                                            self.buttons.close.hide();
                                        }


                                    }).fail(function(xhr, status, err){

                                        self.setTitle("oops..");
                                        self.setType("red");
                                        self.setContent(xhr.responseJSON["msg"]);
                                        self.buttons.ok.setText("Ok");
                                        self.buttons.ok.addClass("btn-green");
                                        // self.buttons.ok
                                        // self.buttons.ok.btnClass = "btn-green"
                                        self.buttons.ok.removeClass("btn-default")
                                        self.buttons.close.hide();

                                    })
                                }
                            });
                        }

                    }
                },
                cancel: function () {
                    //close
                },
            },
            onContentReady: function () {
                // bind to events
                var jc = this;
                this.$content.find('form').on('submit', function (e) {
                    // if the user submits the form by pressing enter in the field.
                    e.preventDefault();
                    jc.$$formSubmit.trigger('click'); // reference the button and click it
                });
            }
        });
    });

    /* Right click delete menu */
    $.contextMenu({
        selector: '#shortcuts-tbl tbody tr',
        callback: function (key, options) {
            // var m = "clicked: " + key + "  ";
            // window.console && console.log(m) ;//|| alert(m);
            // shortcutsDTRef.row(this).data()["enabled"] == true;
            var data = shortcutsDTRef.row(this).data();
            switch (key) {
                case "delete":
                    let shortcutName = data.name;
                    let shortcutValue = data.val;

                    $.confirm({
                        title: '<i class="fas fa-trash-alt" style="color: #e74c3c"></i> Delete Shortcut?',
                        content:
                            // 'Are you sure do you want to revoke access to <b>' +
                            shortcutName + '<b> → </b>'+shortcutValue+'<br>'
                        // '<span style="color: red">USE WITH CAUTION THIS WILL DELETE THE access ACCOUNT AND ALL RELATED DATA INCLUDING JOB ENTRIES</span>',
                        ,buttons: {
                            confirm: {
                                text: "yes",
                                btnClass: 'btn-red',
                                action: function () {

                                    $.ajax({
                                        type: 'DELETE',
                                        url: shortcuts_end_point + '?' + $.param({"name": shortcutName, "val" : shortcutValue}),

                                        processData: false,
                                        contentType: false,
                                        success: function (response) {
                                            shortcutsDTRef.ajax.reload(); // refresh access table
                                            refreshShortcuts = true;
                                            $.confirm({
                                                title: 'Success',
                                                content: response["msg"],
                                                buttons: {
                                                    confirm: {
                                                        btnClass: 'btn-green',
                                                        text:'Ok',
                                                        action: function () {
                                                            return true;
                                                        }
                                                    }
                                                }
                                            });
                                        },
                                        error: function (err) {
                                            console.log(err);
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
            }

        },
        items: {
            "delete": {name: "Delete", icon: "fas fa-trash-alt"},
        }
    });

    //***************** Functions ***************//

    function clearWithConfirm() {

        if (rl != 3)
        {
            suspendAndClearForDiscard();
            return;
        }

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

        $.post(files_api + currentFileID + "/discard",
            {
                prev_status: currentFileData.file_status
            }).done(function (data) {
            // console.log(data);
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
        // document.getElementById('comments').value = "";
        document.getElementById('jobType').value = "";
        document.getElementById('authorName').value = "";
        document.getElementById('user_field_1').value = "";
        document.getElementById('user_field_2').value = "";
        document.getElementById('user_field_3').value = "";
        document.getElementById('report').value = "";
        $("#comments").val("")
        $("#file_comment").val("")
		demoDiv.hide();
        userFields.hide();
        // $('#date').garlic('destroy');
        // //		$( '#dateT' ).garlic( 'destroy' );
        // jobTypeDropDown.garlic('destroy');
        // $("#jobType").attr("disabled","")
        // $('#jobNo').garlic('destroy');
        // //		$( '#TypistName' ).garlic( 'destroy' );
        // $('#comments').garlic('destroy');
        // $("#comments").attr("disabled","")
        //
        // $('#authorName').garlic('destroy');
        // $('#user_field_1').garlic('destroy');
        // $('#user_field_2').garlic('destroy');
        // $('#user_field_3').garlic('destroy');
        // $('#report').garlic('destroy');
        document.title = 'Form';
        tinyMCE.activeEditor.setContent('');

        currentFileID = 0;

        $('#saveBtn').attr("disabled", "disabled");
        $('#suspendBtn').attr("disabled", "disabled");
        $('#discardBtn').attr("disabled", "disabled");
        // tinyMCE.activeEditor.setMode("readonly");
        tinyMCE.activeEditor.getBody().setAttribute('contenteditable', 'false');
        compactView.show();

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
        // var loadBtn = $('#loadBtn');
        var completeBtn = $('#completeBtn');
        searchEngine.attr("hidden", "hidden");
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

        AblePlayerInstances[0].refreshControls('init'); // reset player
        /*setTimeout(function () {

            AblePlayerInstances[0].media.load();
        }, 300);*/

        // loadBtn.removeClass('noHover');
        // loadBtn.html('<i class="fas fa-cloud-download"></i>&nbsp;Load');
        // loadBtn.find("i").show();
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
        txt.remove();
        // return txt.value.replace(/&/g,'&amp;').replace(/<</g,'&lt;&lt;').replace(/>>/g,'&gt;&gt;');
        return txt.value.replace(/<</g,'&lt;&lt;').replace(/>>/g,'&gt;&gt;');
    }

    /*-----LOAD FROM SERVER VERSUS LOCAL----*/
    function filterValuePart(arr, part) {
        part = part.toLowerCase();

        return arr.filter(function (obj) {
            return Object.keys(obj)
                .some(function (k) {
                    // return obj[k].toLowerCase().indexOf(part) !== -1;
                    return obj.lines[0].toLowerCase().indexOf(part) !== -1;
                });
        });
    }

// Loading Audio File and details
    function loadIntoPlayer(data) {
        var jobDetails = JSON.parse(data);

        captions = JSON.parse(jobDetails.captions);
        currentFileData = jobDetails;
        currentFileID = jobDetails.file_id; // globally set current fileID
        // console.log(jobDetails.file_id);
        // console.log(currentFileID);

        // load previous suspended text into tinyMCE if suspended
        if (jobDetails.suspendedText !== null && jobDetails.file_status !== 0) {
            tinymce.get('report').setContent(decodeHtml(jobDetails.suspendedText));
            tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
            tinyMCE.activeEditor.selection.collapse(false);


            // var elements = document.getElementsByClassName("iam");
            // Array.from(elements).forEach(function(element) {
            //     element.addEventListener('click', seekInaudibleMarker);
            //   });
        }

        if(jobDetails.has_caption == true)
        {
            has_captions = true;
            searchEngine.removeAttr("hidden");
            compactView.hide();
        }

        // $("#tryme").html(decodeHtml(jobDetails.suspendedText));

        $('#jobNo').val(jobDetails.job_id);
        $('#authorName').val(jobDetails.file_author);
        $("#jobType").removeAttr("disabled");
        $("#jobType").parent().removeClass("disabled");
        // check if value doesn't exist
        var ddlArray = [...document.querySelector("#jobType").options].map( opt => opt.value );     
        if(jQuery.inArray(jobDetails.file_work_type.toLowerCase().trim(), ddlArray) !== -1)        {
            jobTypeDropDown.val(jobDetails.file_work_type.toLowerCase().trim()).change();
        }else{
            // append the option and select it
            var option = '<option value="'+jobDetails.file_work_type.toLowerCase().trim()+'">'+jobDetails.file_work_type+'</option>';
            jobTypeDropDown.html(jobTypeDropDown.html() + option);
            jobTypeDropDown.val(jobDetails.file_work_type.toLowerCase().trim()).change();
        }

        var dispDateFormat = moment(jobDetails.file_date_dict).format("DD-MMM-YYYY hh:mm:ss a");
        $('#date').val(dispDateFormat);
        $('#comments').val(jobDetails.typist_comments);
        $('#file_comment').val(jobDetails.file_comment);
        $("#comments").removeAttr("disabled");
        // console.log("Typist comments: " + jobDetails.typist_comments);

        if(jobDetails.user_field_1 != "" || jobDetails.user_field_2 != "" || jobDetails.user_field_3 != "")
        {
            userFields.show();
            $('#user_field_1').val(jobDetails.user_field_1);
            $('#user_field_2').val(jobDetails.user_field_2);
            $('#user_field_3').val(jobDetails.user_field_3);
        }else
        {
            userFields.hide();
        }

        // var $loadBtn = $('#loadBtn');
        var $completeBtn = $('#completeBtn');


        //tinymce.activeEditor.selection.select(tinymce.activeEditor.dom.select('#transcript'))
        //tinymce.activeEditor.selection.select(tinymce.activeEditor.dom.select('.able-window-toolbar'))
        //

        AblePlayerInstances[0].media.src = audioTempFolder + jobDetails.tmp_name;
        // AblePlayerInstances[0].transcriptType = "manual";
        // AblePlayerInstances[0].$transcriptArea = $("#transcript");
        // AblePlayerInstances[0].$transcriptToolbar = $(".able-window-toolbar");
        // AblePlayerInstances[0].$autoScrollTranscriptCheckbox = $("#autoscroll-transcript-checkbox");
        // AblePlayerInstances[0].setupTranscript();
        // AblePlayerInstances[0].updateTranscript();


        // $loadBtn.addClass('noHover');
        // $loadBtn.text(jobDetails.job_id + ' Loaded');
        // $loadBtn.find("i").hide();

        // enable save etc.. buttons
        if(rl == 3 && jobDetails.file_status != 3)
        {
            $('#saveBtn').removeAttr("disabled");
            $('#suspendBtn').removeAttr("disabled");
        }
        $('#discardBtn').removeAttr("disabled");
        tinyMCE.activeEditor.setMode("design");
        tinyMCE.activeEditor.getBody().setAttribute('contenteditable', 'true');


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
        if (jobDetails.file_status == 2 || jobDetails.file_status == 1) // suspend or being typed
        {
            // seek to last position
            let lastPos = jobDetails.last_audio_position - rewindAmountOnPause;
            if(lastPos < 0) lastPos = 0;
            $("#audio1").attr("data-start-time", lastPos);
            AblePlayerInstances[0].media.currentTime = lastPos;
            // console.log("db last pos: " + jobDetails.last_audio_position);
            // console.log("seeking to: " + lastPos);
        } else {
            $("#audio1").attr("data-start-time", 0);
            AblePlayerInstances[0].media.currentTime = 0;
        }
        AblePlayerInstances[0].media.load();


        // AblePlayerInstances[0].onMediaPause = function () {
        //
        // }

        if(has_captions)
        {
            /*AblePlayerInstances[0].onMediaUpdateTime = function () {
                var elapsed = AblePlayerInstances[0].media.currentTime;

                handleHighlights(elapsed);
            }*/
        }


        modal.style.display = "none"; //hide modal popup
        changeLoading(false, "Loading transcribe..");
        demoDiv.slideDown();
    }

    window.handleMediaPause = function()
    {
        if (AblePlayerInstances[0].seekBar.position - rewindAmountOnPause > 0) {
            AblePlayerInstances[0].seekTo(AblePlayerInstances[0].seekBar.position - rewindAmountOnPause);
        } else {
            AblePlayerInstances[0].seekTo(0);
        }
    }

    // window.handleAbleMediaRestart = function()
    // {
    //     console.log("restarting playback");
    //     AblePlayerInstances[0].seekTo(0);
    // }

    window.handleAbleMediaUpdate = function()
    {
        var elapsed = AblePlayerInstances[0].media.currentTime;

        handleHighlights(elapsed);
    }

    window.handleHighlights = function(elapsedTime)
    {
        if(elapsedTime >= startLimit && elapsedTime <= endLimit)
        {
            return;
        }

        for (let i = 0; i < captions.length; i++) {
            let start = captions[i].start;
            let end = captions[i].end;

            if(elapsedTime >= start && elapsedTime <= end)
            {
                if(currentHighlightedID)
                {
                    // de-highlight prev
                    try{
                        tinymce.activeEditor.dom.select('#' + currentHighlightedID)[0].removeAttribute("style");
                    }catch (e) {}
                }
                let id = startTimeToID(start);
                // console.log("id to highlight: " + id);
                try{
                    tinymce.activeEditor.dom.select('#' + id)[0].setAttribute("style", "background-color:#1e79be;color:white;padding:6px; border-radius: 10px;");
                }catch (e) {}

                currentHighlightedID = id;
                startLimit = start;
                endLimit = end;
                break;
            }

        }
    }

    function startTimeToID(start)
    {
        return 'ST'+start.toFixed(2).replace(".","T");
    }

    function idToTime(id)
    {
        return id.replace("ST", "").replace("T",".");
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

    	// Tutorial area
		//initialize instance
		var enjoyhint_instance
        = new EnjoyHint({
        onEnd:function(){
            tutorialViewed();
        },
        onSkip:function(){
            tutorialViewed();
        }
    });

    //simple config.

    var enjoyhint_script_steps = [
        {
            "next #mceu_14-button":"Click here to choose a job to open"
        },
		// {
		// 	"next #demoSidebar": "Job file information and demographics"
		// },
		{
			"next #divv": "This is the rich text editor. This is where any speech to text content will show and/or where you will type "
		},
		{
			"next #saveBtn": "This saves, finishes and closes the job"
		},
		{
			"next #suspendBtn": "This saves your progress and closes the job. You can continue working on it whenever you want"
		},
		{
			"next #discardBtn": "This closes the job without saving your progress"
		},
		{
			"next #statusTxt": "This tells you if your USB foot control is connected"
		},
		{
			"next #pop": "Click here to switch to mini player"
		},
		{
			"next .pin-collapse-div":"Click here to expand the navigation bar to get access to various pages and settings"
		},
		{
			"click #zohohc-asap-web-launcherbox > a":"Click here to access the online help",
				// shape:"circle",
			"skipButton":{text: "Finish"}
		}

    ];

    //set script config
    enjoyhint_instance.set(enjoyhint_script_steps);

    // get page name
    const currentPageName = location.pathname.split("/").slice(-1)[0].replace(".php","");
    // parse user tutorials data to JSON
    var tutorialsJson = JSON.parse(tutorials);
    // check if tutorial for the current page isn't viewed before
    if(tutorialsJson[currentPageName] == undefined || tutorialsJson[currentPageName] == 0){
        //Insert sample dictation text since there won't be a job loaded
	//$(".able-duration").html = "/ 5:43";
		$("#jobNo").val("VT-001234");
		$("#authorName").val("Sample Author");
		$("#date").val("23-Jan-2021 10:34:00");
		$("#jobType").val("Meeting Notes");
		$("#user_field_1").val("Conf ID: 2234");
		$("#dateT").val("23-Jan-2021 11:01:00");
		$("#comments").val("Jane was speaking very softly. Hard to hear");
		$("#file_comment").val("Please send a copy to Jeremy");
		$("#report").val("Thank you all for taking the time to meet today. I know the weather wasn't favourable and we really appreciate you making it here today");
		$("#saveBtn").prop('disabled', false);
		$("#suspendBtn").prop('disabled', false);
		$("#discardBtn").prop('disabled', false);
		demoDiv.show();
        // show tutorial
        setTimeout(function(){enjoyhint_instance.run()},1000);
    }

    function tutorialViewed() {
        //reset view

        var formData = new FormData();
        formData.append("page", currentPageName);
		$.ajax({
		type: 'POST',
		url: "../api/v1/users/tutorial-viewed/",
		processData: false,
		data: convertToSearchParam(formData)
			});
		clear();
    }

});



$("#jobNo").keypress(function () {
    document.title = $('#jobNo').val();
});


function editUserShortcuts()
{
    shortcutModal.style.display = "block";
}

function loadNewJob()
{
    if(currentFileID !== 0)
    {
        $.confirm({
            title: '<i style=\"color: #f3ca27\" class=\"fas fa-exclamation-triangle\"></i> Warning',
            content: "Please save/suspend your current work first",
            type: "orange"
            ,buttons: {
                confirm: {
                    text: "Ok",
                    // btnClass: 'btn-red',
                }
            }
        });
        return;
    }
    modal.style.display = "block";
    jobsDTRef.ajax.reload();
}

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

function convertToSearchParam(params) {
const searchParams = new URLSearchParams();
for (const [key, value] of params) {
    searchParams.set(key, value);
}
return searchParams;
}


function gotoInaudiblePosition(e) {
    //console.log(`Jumping to inaudible marker`);
    AblePlayerInstances[0].seekTo(e);
    AblePlayerInstances[0].seekBar.setPosition(e);
    AblePlayerInstances[0].media.pause();
}