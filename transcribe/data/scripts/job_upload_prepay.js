(function ($) {
    "use strict";
})(jQuery);

var uploadAjax;

function documentReady() {

    const NO_FILES_TBL_ROW = "<tr><td colspan=\"5\" style=\"text-align: center\">No files currently selected for upload</td></tr>";

    const input = document.getElementById('filesInput');

    const submitUploadBtn = document.querySelector('.submit_btn');
    const cancel_popup_btn = document.getElementById('cancelUpload');
    const confirm_popup_btn = document.getElementById('confirmUpload');
    const previewModal = document.querySelector('.previewModal');
    const backend_url = 'data/parts/backend_request.php';
    const api_insert_url = 'api/v1/files/';
    const api_set_compmins_url = '../api/v1/accounts/update-comp-mins/';
 
    //RG 30NOV2021
    //Need to make sure there is no localStorage item called prepay_upload set to true or the files will upload
    //whether payment done or not.
    //I don't think this is a good implementation as a user could simply set the localStorage prepay_upload=true and bypass payment

    if(localStorage.getItem("prepay_upload") =="true"){
        localStorage.removeItem("prepay_upload");
    }

    // 23-Feb-2020 12:35:40 AM

    const flatPickr = $("#dictDatePicker").flatpickr({
        enableTime: true,
        altInput: true,
        altFormat: "d-M-Y h:i:S K",
        dateFormat: "Y-m-d H:i:S",
        defaultDate: new Date()
    });

    flatPickr.config.onChange.push(function (selectedDates, dateStr, instance) {
        if (dateStr === "") {
            instance.setDate(new Date());
        }
    })

    new mdc.ripple.MDCRipple(document.querySelector('.submit_btn'));
    new mdc.ripple.MDCRipple(document.querySelector('#cancelUpload'));
    new mdc.ripple.MDCRipple(document.querySelector('#confirmUpload'));
    var uploadCarousel = $("#uploadCarousel");

    let modal = document.getElementById("modal");

    // const mainUploadBtn = $("#mainUploadBtn");
    const dropUploadContent = $("#vsptDropUploadContent");
    const dropUploadMainContent = $("#vsptDropMainContent");
    const clearDiv = $("#clear");
    const mediaInfoDiv = $("#getMediaInfo");
    const progressList = $("#vsptProgressList");
    const nextBtn = $("#demoNextBtn");
    const prevBtn = $("#demoBackBtn");
    const dropZone = $("#vsptDropZone");
    const speakerTypeDiv = $("#speakerTypeDiv");
    const dictDateLbl = $("#dictDateLbl");
    const tblBody = $("#queFilesBody");
    const srBar = $("#srBar");
    const addMinsBtn = $("#addMinsBtn");
    const uploadToast = $("#uploadToast");
    const payNextBtn = $("#payNextBtn");
    const payBackBtn = $("#payBackBtn");

    const uploadToastBody = uploadToast.find(".toast-body");


    // allowed files for upload queue variables
    var filesArr = [];
    var filesDur = [];
    var filesIds = [];
    var filesCount = 0;

    var curFiles;
    var qCount = 0; // Queue count
    var duratedFiles = 0;
    var commSize = 0; // accumulated file sizes
    // let maxFileSize = 134217728;
    let maxFileSize = 367001600; 
    let mediaInfoOutput = null;
    const MAX_FILES_COUNT = 10;

    // var srEnabled = false;
    // getSRenabled();
    var srMinutesRemaining = 0;
    var srMinutes = $("#srMinutes");

    $("#chooseFile").on("click", function () {
        softClearQueuedFiles();
        input.click();
    });

    nextBtn.on("click", function () {
        
        let totalMinutesForCalc = calculateTotalMinutesForCalc();
        let totalMinutesForDisplay = calculateTotalMinutes();
    	// console.log(comp_mins);
        if(lifetime_minutes ==0 && promo ==1){
            $("#total_mins_charge").text((totalMinutesForCalc-comp_mins));
            $("#total_charge").text(((totalMinutesForCalc-comp_mins)*bill_rate1+(totalMinutesForCalc-comp_mins)*bill_rate1*0.05).toFixed(2));
        	if((totalMinutesForCalc-comp_mins) <0){
        		$("#total_mins_charge").text(0);
        		$("#total_charge").text(0);
    			$("#mdc-button__label").text("Upload File(s)");
                //The only thing different in the scenario here is the comp_mins will go to 0 after first upload regardless
                // of whether they use them all or not.
        	}
        }else{
            $("#total_mins_charge").text((totalMinutesForCalc-comp_mins));
            $("#total_charge").text(((totalMinutesForCalc-comp_mins)*bill_rate1+(totalMinutesForCalc-comp_mins)*bill_rate1*0.05).toFixed(2));
        	if((totalMinutesForCalc-comp_mins) <0){
        		$("#total_mins_charge").text(0);
        		$("#total_charge").text(0);
				$("#mdc-button__label").text("Upload File(s)");
        	}
        }
        // console.log(calculateTotalMinutes().toFixed(2));
        uploadCarousel.carousel(2);
    });
    prevBtn.on("click", function () {
        uploadCarousel.carousel(0);
    });
   //  payNextBtn.on("click", function () {
   //  	if(lifetime_minutes ==0 && promo == 1){
   //  		if(eval(calculateTotalSRminutes()-10-comp_mins) < 0){
   //  		}
   //  	}else{
			// if(eval(calculateTotalSRminutes()-comp_mins) <0){
   //  		}

   //  	}
   //      uploadCarousel.carousel(3);
   //  });
    payBackBtn.on("click", function () {
        uploadCarousel.carousel(1);
    });

    let p1nBtn = $("#p1nBtn");
    let p3bBtn = $("#p3Bbtn");

    p1nBtn.on("click", function () {
        if (filesArr.length > 0) {
            uploadCarousel.carousel(1);
        }
    });

    p3bBtn.on("click", function () {
        uploadCarousel.carousel(1);
    });	

    dropZone.on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
    })
        .on('dragover dragenter', function () {
            dropZone.addClass('is-dragover');
        })
        .on('dragleave drop', function () {
            dropZone.removeClass('is-dragover');
        })
        .on('drop', function (e) {
            curFiles = Array.from(e.originalEvent.dataTransfer.files);
            var filesToUpload = [];

            // check type
            for (let i = 0; i < curFiles.length; i++) {
                let fileExt = curFiles[i].name.split(".").pop().toLowerCase();
                if (!validFileType(curFiles[i]) &&
                    !(curFiles[i].type === "" && (fileExt  === "ds2" || fileExt === "dss"))
                ) {
                    uploadToastBody.html(`(${curFiles[i].name}) File type is not supported`);
                    uploadToast.toast('show');
                    continue;
                }

                // prevent Dup
                let arrFilter = filesArr.filter(function (prevFiles) {
                    return prevFiles.name.includes(curFiles[i].name);
                });

                if (arrFilter.length === 0) {
                    filesToUpload.push(curFiles[i]);
                }
            }
            curFiles = filesToUpload;
            if (curFiles.length > 0) addFilesToUpload();
        });


    uploadCarousel.on('slide.bs.carousel', function (e) {
        // console.log(e.to + 1)
        /*  
            e.direction     // The direction in which the carousel is sliding (either "left" or "right").
            e.relatedTarget // The DOM element that is being slid into place as the active item.
            e.from          // The index of the current item.
            e.to            // The index of the next item.
        */

        switch (e.to) {
            case 3:
                progressList.children().eq(3).addClass("active");
            case 2:
                progressList.children().eq(2).addClass("active");
            case 1:
                progressList.children().eq(1).addClass("active");
            case 0:
                progressList.children().eq(0).addClass("active");
                break;
        }

        switch (e.to + 1) {
            case 0:
            case 1:
                progressList.children().eq(1).removeClass("active");
            case 2:
                progressList.children().eq(2).removeClass("active");
            case 3:
                progressList.children().eq(3).removeClass("active");
            default:
                break;
        }
    })

    input.addEventListener('click', function () {
        resetFiles();
    })

    input.addEventListener('change', function () {
        curFiles = Array.from(input.files);
        addFilesToUpload();
    })

    // modal.style.display = "block";
    const linearProgress = new mdc.linearProgress.MDCLinearProgress(document.querySelector('.mdc-linear-progress'));
    const linearProgressLay = $('.mdc-linear-progress');
    const progressTxt = $('#progressTxt');

    $("#clearBtn").on("click", function () {
        linearProgressLay.addClass('mdc-linear-progress--closed');
        files = [];
        resetFiles();
    });

    cancel_popup_btn.addEventListener('click', e => {
        // cancel the upload
        if (uploadAjax !== undefined) {
            stopProgressWatcher();
            uploadAjax.abort();
            document.getElementById("upload_form").reset();
            // console.log("Upload Cancelled (1)");
        }
        location.reload(); // reload
    });

    confirm_popup_btn.addEventListener('click', e => {
        // confirm upload results -> return to job list ?
        location.href = 'main.php';
    });

// Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission


    // progress timer
    var timer;
    var uploadForm = $("#upload_form");
    const prepayStatus = $("#prepay_status").val();
    const lifetime_minutes = $("#lifetime_minutes").val();
    const promo = $("#promo").val();
    const comp_mins = $("#comp_mins").val()>=0?$("#comp_mins").val():0.00;
    const bill_rate1 = $("#bill_rate1").val();

    uploadForm.on('submit', function (event) {
        event.preventDefault();
        document.querySelector('.submit_btn').setAttribute("disabled", "true");
    // Not sure why we have this. This form only gets loaded if prepay is true
	if(prepayStatus == 1){ 
	//   if(lifetime_minutes ==0 && promo ==1){
        let totalCalcMins = calculateTotalMinutesForCalc();
        totalDisplayMins = calculateTotalMinutes();
	  	if(eval(totalCalcMins-comp_mins) > 0){
			//$("#total_mins").val(calculateTotalMinutes().toFixed(2)-comp_mins);
            $("#total_mins").val(roundSeconds(totalCalcMins-comp_mins));
            $("#total_display_minutes").val(totalDisplayMins)
            $("#total_files").val(filesDur.length);
			$("#prepayForm").submit();
			var prepayInterval = setInterval(()=>{
			  if(localStorage.getItem("prepay_upload") =="true"){
			  	localStorage.removeItem("prepay_upload");
				 prepayUpload();
			  } else if(localStorage.getItem("prepay_upload") =='0'){
                //   console.log("We should be reenabling the button now");
                  localStorage.setItem('prepay_upload',"false");
                //document.querySelector('.submit_btn').setAttribute("disabled", "false");
                submitUploadBtn.removeAttribute("disabled");
                // clearInterval(prepayInterval);
              }
			},3000)
	  	}else{
            // We need to call the API endpoint when not accessing the prepayment.php page to update
            // the comp_mins
            setCompMins(calculateTotalMinutesForCalc(), prepayUpload);
	  	}
	//   }else{
	//   	if(eval(calculateTotalMinutes()-comp_mins) > 0){	  		
	// 	  	$("#total_mins").val(calculateTotalMinutes().toFixed(2)-comp_mins);
	// 		$("#prepayForm").submit();
	// 		var prepayInterval = setInterval(()=>{
    //             if(localStorage.getItem("prepay_upload") =="true"){
    //                 localStorage.removeItem("prepay_upload");
    //                prepayUpload();
    //             }
    //           },3000)
	//   	}else{
	//   		prepayUpload();
	//   	}
	//   }
	}else{
	  prepayUpload();
	}
     
    });
    function prepayUpload(){
       uploadForm.addClass('was-validated');

        // if (validateFields()) {
        if (uploadForm[0].checkValidity() === true) {
            modal.style.display = "block"; // show the upload progress window
            let formData = new FormData();
            let files = filesArr;
            let other_data = $("#upload_form").serializeArray();

            $.each(other_data, function (key, input) {
                formData.append(input.name, input.value);
            });

            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                formData.append('file' + i, file);
                formData.append('dur' + i, filesDur[i]);
                // console.log(files[i]);
            }

            formData.append("sr_enabled", 0); //This is a required field
            formData.append("authorName", $("#demo_author").val());
            formData.append("jobType", $("#demo_job_type option:selected").html());
            formData.append("dictDate", $("#dictDatePicker").val());
            formData.append("speakerType", $("#demo_speaker_type").val());
            if ($('#demo_comments').val() !== "") {
                formData.append("comments", $('#demo_comments').val());
            }

            // CHECK UPLOADED FILES AND SAVE IT TO DB
            uploadAjax = $.ajax({
                type: 'POST',
                url: api_insert_url,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    stopProgressWatcher();
                    updateUI(100, false);
                    //Parse the HTML string(s) together so they can be inserted into the DOM html
                    resetAfterUpload();
                    var htmlEl = "";
                    let size = Object.keys(response).length

                    for (i = 0; i < size; i++) {
                        if (response[i]["error"] === false) {
                            htmlEl += "<li>File: " + response[i]["file_name"] + " - <span style='color:green;'>" + response[i]["status"] + "</span></li>"
                        } else {
                            htmlEl += "<li>File: " + response[i]["file_name"] + " - <span style='color:red;'>" + response[i]["status"] + "</span></li>"
                        }
                    }
                    const list = document.createElement('ol');
                    list.setAttribute("class", "uploadResultList");
                    previewModal.appendChild(list);
                    previewModal.insertAdjacentHTML("afterbegin", htmlEl);

                },
                error: function (err) {
                    stopProgressWatcher();
                    resetAfterUpload();
                    updateUI(100, true);
                    if (err.responseJSON !== undefined) {
                        progressTxt.text("Error.");
                        htmlEl =
                            "<span style='color: darkred'>" + err.responseJSON["msg"] + "</span>"
                        ;
                        const list = document.createElement('ol');
                        previewModal.appendChild(list);
                        previewModal.insertAdjacentHTML("afterbegin", htmlEl);
                    } else { // upload was cancelled by user - no error
                        progressTxt.text("Upload Cancelled.");
                    }
                }
            });

            // now query for upload progress...
            // console.log("enable watchdog1");
            enableProgressWatcher('job_upload');
            // console.log("enable watchdog2");

            //** Get next jobID & jobNumber **//
            // MOVE CODE HERE //
        } else {
            event.stopPropagation();
            // alert("Please fill in required fields");
        }
    }

    function enableProgressWatcher(progressSuffix) {

        let formData = new FormData();
        formData.append("reqcode", 65);
        formData.append("suffix", progressSuffix);

        timer = setInterval(function () {
            $.ajax({
                type: 'POST',
                url: backend_url,
                data: formData,
                processData: false,
                contentType: false,
                success: function (msg) {
                    // console.log("watchdog running");
                    // console.log("watchdog msg: " + msg);
                    if (msg === 'null') {
                        clearInterval(timer);
                        updateUI(100, false);
                    } else {
                        if (msg !== "starting") {
                            let progress = JSON.parse(msg);
                            let processed_bytes = progress['bytes_processed'];
                            let total_bytes = progress['content_length'];
                            // lets do math now
                            let total_percent = Math.floor(processed_bytes * 100 / total_bytes);
                            // console.log("percentage completed: " + total_percent);

                            updateUI(total_percent, false);

                            if (total_percent >= 100) {

                                // console.log("Should stop the timer");
                                updateUI(100, false);
                            }
                        }

                    }
                }
            });
        }, 600);
    }

    function stopProgressWatcher() {
        if (timer != null) {
            clearInterval(timer);
        }
    }


    function updateUI(percentage, err) {
        linearProgressLay.removeClass('mdc-linear-progress--closed'); // Show progressbar
        linearProgress.progress = percentage / 100.0;
        if (percentage !== 100) {
            progressTxt.text(percentage + "%")
        } else {
            if (!err) {
                progressTxt.text("Complete.");
                $('.modal-content p i').html(""); // clear please wait message
                cancel_popup_btn.style.display = "none";
                confirm_popup_btn.style.display = "inline-block";
            } else {
                progressTxt.text("Cancelled.");
                $('.modal-content p i').html(""); // clear please wait message
                cancel_popup_btn.style.display = "none";
                confirm_popup_btn.style.display = "inline-block";
            }
        }
    }

    function generateLoadingSpinner() {
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

    // SR Rounding method
    function roundUpToAnyIncludeCurrent(number) {
        let roundTo = 15;
        // return (Math.round(number)%roundTo === 0) ? Math.round(number) : Math.round((number+roundTo/2)/roundTo)*roundTo;
        if (number % roundTo === 0) {
            return Math.round(number);
        } else {
            return (Math.round((number + roundTo / 2) / roundTo) * roundTo);
        }
    }

    // Transcription Services Rounding Method. Rounds up to next second
    function roundUpToNext(number) {
        let roundTo = 1;
        // return (Math.round(number)%roundTo === 0) ? Math.round(number) : Math.round((number+roundTo/2)/roundTo)*roundTo;
        if (number % roundTo === 0) {
            return Math.round(number);
        } else {
            return (Math.round((number + roundTo / 2) / roundTo) * roundTo);
        }
    }

    //This is used to round calculations that result in more than 2 decimals ie: adding minutes, subtracting
    // We are expecting that the value passed here will be a min/1/100 sec value ie: 5.0166666
    function roundSeconds(value) {
        let mins = Math.trunc(value);
        let secs = value % 1;
        let roundedSecs = Math.round(secs * 100) /100;
        let minsHundredsRounded = mins + roundedSecs;

        return minsHundredsRounded;
    }
    //The return of this is used for display values ie: 00:05:23
    function calculateTotalMinutes() {
        let totalseconds = 0.0;

        for (let i = 0; i < filesDur.length; i++) {
            var sec = roundUpToNext(filesDur[i]);
            // var sec = filesDur[i];
            totalseconds += sec;
        }
        return secsToMin(totalseconds);
    }

    //The return of this is used for calculations and returns the seconds in hundredths of a second ie: 5.38
    function calculateTotalMinutesForCalc() {
        let totalseconds = 0.0;
        for (let i = 0; i < filesDur.length; i++) {
            var sec = roundUpToNext(filesDur[i]);
            // var sec = filesDur[i];
            totalseconds += secsToHundMins(sec);
        }
        // return secsToHundMins(totalseconds);
        return totalseconds;
    }

    function unlockUploadUI(unlock) {
        if (unlock) {
            if (filesArr.length > 0) {
                //We are using this elsewhere so need to calculate even if no SR
                var totalMinutes = calculateTotalMinutesForCalc();
                var totalMinutesDisplay = calculateTotalMinutes();
                var a = totalMinutesDisplay.split(':'); // split it at the colons

                // minutes are worth 60 seconds. Hours are worth 60 minutes.
                var minutes = ((+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2])*60); 

                if (prepayStatus == 1) {
                    // $("#totals").html(
                    //     `Total amount to be charged: ${totalMinutes} mins - $${comp_mins} X $${bill_rate1} = $${((totalMinutes-comp_mins)*bill_rate1).toFixed(2)} (Plus applicable taxes)`)
                    // $("#sum_sub").html(`${(totalMinutes).toFixed(2)}`);
                    $("#sum_sub").html(`${(totalMinutesDisplay)}`);
                    $("#sum_comp").html(`-${comp_mins}`);
                    $("#sum_br").html(`$${bill_rate1}`);
                    var displayTotalMins = ((totalMinutes-comp_mins)*bill_rate1) >0 ? ((totalMinutes-comp_mins)*bill_rate1).toFixed(2) : "0.00";
                    $("#sum_gt").html(`$${displayTotalMins}`);
                }
                submitUploadBtn.removeAttribute("disabled");
            }
        } else {
            submitUploadBtn.setAttribute("disabled", "true");
        }
    }

    // function secsToMin($seconds) {
    //     console.log(`--AUDIO LENGTH DEBUG-- Result of converting seconds to minutes is: ${$seconds / 60}`);
    //     return ($seconds / 60);
    // }

    function secsToMin(seconds) {
        let roundUpTo = 1;
        let roundedSeconds = 0;
        if (seconds % roundUpTo === 0) {
            roundedSeconds = Math.round(seconds);
        } else {
            roundedSeconds = (Math.round((seconds + roundUpTo / 2) / roundUpTo) * roundUpTo);
        }
        let hhmmss = new Date(roundedSeconds * 1000).toISOString().substr(11, 8).toString();
        minsPrettied = hhmmss.replace(/^0(?:0:0?)?/, '');

        return minsPrettied;
    }

    function secsToHundMins(seconds) {
        let mins = Math.trunc(seconds / 60);
        let secs = seconds % 60;
        let secsHundreds = secs / 60;
        let minsHundreds = mins + secsHundreds;
        return roundSeconds(minsHundreds);
    }

    function computeDuration(id, file, status, dssType = 0) {
        if (dssType === 1 || dssType === 2 || file.type == "audio/ds2") {
            get_dss_duration(file,id,status);
            return;
        }

        // Create a non-dom allocated Audio element
        let audio = document.createElement('audio');
        audio.setAttribute("preload", "metadata");

        let objectUrl = URL.createObjectURL(file);
        // audio.prop("src", objectUrl);
        audio.setAttribute("src", objectUrl);

        audio.onloadedmetadata = function () {
            // alert('meta loaded for file -> ' + id + "\n duration for file " + id + " is "+ audio.duration);

            // Obtain the duration in seconds of the audio file (with milliseconds as well, a float value)
            let duration = audio.duration;

            // example 12.3234 seconds
            // console.log("The duration of file ("+id+") is of: " + duration + " seconds");
            // Alternatively, just display the integer value with
            // parseInt(duration)
            // 12 seconds
            // Update table with the duration
            // We are rounding up to the nearest second
            // let roundUpTo = 1;
            // let roundedSeconds = 0;
            // if (duration % roundUpTo === 0) {
            //     roundedSeconds = Math.round(duration);
            // } else {
            //     roundedSeconds = (Math.round((duration + roundUpTo / 2) / roundUpTo) * roundUpTo);
            // }
            // let durationTxt = new Date(roundedSeconds * 1000).toISOString().substr(11, 8).toString();

            let durationTxt = secsToMin(duration);

            $("#qfile" + id + " td:nth-child(4)").html(durationTxt);
            // $("#qfile"+id+" td:nth-child(5)").html(Math.round(duration));

            // increase done files counter
            duratedFiles++;

            // add duration to upload Que in (secs) for Queued files
            if (status === 0) // status OK
            {
                filesDur[filesIds.indexOf(id)] = duration;// adding duration in the same arrangement as filesArr
                // filesDur[filesIds.indexOf(id)] = Math.round(duration);// adding duration in the same arrangement as filesArr
            }

            // console.log(`computed new file ${file.name} with fileID: ${id} | total computed: ${duratedFiles} | total files now: ${filesCount}`)
            // check if all files are durated
            if (duratedFiles === filesCount) {

                // unlock the upload button
                // console.log("++ unlocked upload ++")
                unlockUploadUI(true);
            }else{
                // console.log("-- locked upload -- ")

                unlockUploadUI(false);
            }

            audio.remove();

        };
    }

    // 3 -> file count exceeds limit of 10
    function getFileUploadStatus(id, size, fileArrElement) { // 0 -> allowed,   1 -> file exceeds limit,     2 -> request exceeds limit

        if (qCount <= MAX_FILES_COUNT - 1) // -1 as the qCount++ is performed after the check
        {
            if (size > maxFileSize) // 350 MB
            {
                // file size not allowed
                // remove file from upload queue
                return 1;

            } else { // SINGLE SIZE OK -> CHECK FOR ACCUMULATIVE FILE SIZE
                if (commSize + size > maxFileSize) { // Check if total uploaded files exceeds 350MB
                    // remove file from upload queue
                    return 2;

                } else { // File OK
                    commSize += size; // add file size to accumulative sizes
                    addFileToQueue(id, fileArrElement);
                    return 0;
                }
            }
        } else {
            // File count exceeds the limit of @MAX_FILES_COUNT
            return 3;
        }
    }

    function generateTblFileEntry(id, filename, size, status) {
        // generating a file entry
        const row = document.createElement("tr");
        if(status === 0) row.setAttribute("id", "qfile" + id);

        const data1 = document.createElement("td");
        if(status !== 0)
        {
            data1.innerHTML = "-";
            row.setAttribute("style", "color: slategrey;font-style: italic;")
        }else{
            data1.innerHTML = id + 1;
        }

        const data2 = document.createElement("td");
        data2.innerHTML = filename;

        const data3 = document.createElement("td");
        data3.innerHTML = returnFileSize(size);

        const data4 = document.createElement("td");
        if(status !== 0)
        {
            data1.innerHTML = "-";
        }else{
            data4.appendChild(generateLoadingSpinner());
        }

        const data6 = document.createElement("td");


        // Check for file size to decide its upload status
        switch (status) {
            case 0: // allowed
                data6.setAttribute("style", "color: #53a13d;");
                data6.innerHTML = "Ready to upload.";
                setDropText(`${filesArr.length}. ${filename} <br>`, true, true);
                break;

            case 1: // file exceeds limit

                data6.setAttribute("style", "color: #B00020;");
                data6.innerHTML = "File exceeds 350MB - Skipped";

                uploadToastBody.html(`(${filename}) File exceeds 350MB - Skipped`);
                uploadToast.toast('show');
                break;

            case 2: //request exceeds limit
                data6.setAttribute("style", "color: #B00020;");
                data6.innerHTML = "Total files exceed 350MB - Skipped";

                uploadToastBody.html(`(${filename}) Total files exceed 350MB - Skipped`);
                uploadToast.toast('show');
                break;

            case 3:
                data6.setAttribute("style", "color: #B00020;");
                data6.innerHTML = `File count exceeds the limit of ${MAX_FILES_COUNT} - Skipped`;

                uploadToastBody.html(`(${filename}) File count exceeds the limit of ${MAX_FILES_COUNT} - Skipped`);
                uploadToast.toast('show');
                break;
        }


        row.appendChild(data1);
        row.appendChild(data2);
        row.appendChild(data3);
        row.appendChild(data4);
        // row.appendChild(data5);
        row.appendChild(data6);

        return row;
    }

    function addFileToQueue(id, fileArrElement) {

        filesArr.push(fileArrElement);
        filesIds.push(id); // to keep track of file index in filesArr to add its duration when duration is computed

        qCount++;
    }

    function generateErrTblFileEntry(id, filename) {
        // generating a file entry
        const row = document.createElement("tr");
        row.setAttribute("id", "qfile" + id);

        const data1 = document.createElement("td");
        data1.innerHTML = id + 1;

        const data2 = document.createElement("td");
        data2.innerHTML = filename;

        const data3 = document.createElement("td");
        data3.setAttribute("colspan", "4");
        data3.innerHTML = `Not a valid file type. Update your selection.`;
        data3.setAttribute("style", "color: #B00020;");

        // remove file from upload queue
        removeFile(id);

        row.appendChild(data1);
        row.appendChild(data2);
        row.appendChild(data3);

        return row;
    }

    function setDropText(text, allowStep = true, append = true) {
        if (text === "") {
            softClearQueuedFiles();
            dropUploadMainContent.show();
            dropUploadContent.html("");
            clearDiv.hide();
            mediaInfoDiv.hide();
            p1nBtn.hide();
        } else {
            if(append) dropUploadContent.append(text);
            else dropUploadContent.html(text);
            dropUploadMainContent.hide();
            clearDiv.show();
            mediaInfoDiv.show();

            if (allowStep) {
                p1nBtn.show();
            } else {
                p1nBtn.hide();
            }
        }
    }

    function softClearQueuedFiles() {
        // clear old arrays
        filesCount = 0;
        duratedFiles = 0;
        commSize = 0;
        qCount = 0;

        // clearing arrays
        filesArr = [];
        filesDur = [];
        filesIds = [];

        tblBody.html(NO_FILES_TBL_ROW);
    }

    function addFilesToUpload() {

        if (filesArr.length === 0) {
            tblBody.empty();
        }

        if (curFiles.length === 0) {
            tblBody.html(NO_FILES_TBL_ROW);

            setDropText("");
        } else {

            filesCount = curFiles.length + filesArr.length;
            let fileID = filesArr.length;

            let i = 0;

            for (const file of curFiles) {
                // const par = document.createElement('p');

                let fileTypeIsValid = validFileType(file);
                let fileExt = curFiles[i].name.split(".").pop().toLowerCase();

                if (
					fileTypeIsValid
                    ||
                    (curFiles[i].type === "" && (fileExt  === "ds2" || fileExt === "dss"))
                ) {
                    // get file upload criteria
                    let status = getFileUploadStatus(fileID, file.size, file);

                    // generate a table entry
                    tblBody.append(generateTblFileEntry(fileID, file.name, file.size, status));
                    // Get audio duration
                    if(status !== 0)
                    {
                        filesCount--;
                        // console.log(`removing 1 file ${file.name} | total now ${filesCount}`);
                    }
                    else{

                        computeDuration(fileID, file, status, fileTypeIsValid ? 0 : (fileExt === "ds2"?2:1));
                    } // async // 2: ds2 // 1: dss

                } else {
                    tblBody.append(generateErrTblFileEntry(fileID, file.name));
                }

                i++;
                fileID++;
            }
        }
    }

    // Code for new feature to gather media data from upload screen for troubleshooting
    // Code modified from examples from https://github.com/buzz/mediainfo.js

    function get_file_info(mediainfo, file) {
        let getSize = () => file.size
        let readChunk = (chunkSize, offset) =>
          new Promise((resolve, reject) => {
            let reader = new FileReader()
            reader.onload = (event) => {
              if (event.target.error) {
                reject(event.target.error)
              }
              resolve(new Uint8Array(event.target.result))
            }
            reader.readAsArrayBuffer(file.slice(offset, offset + chunkSize))
          })
      
        return mediainfo
          .analyzeData(getSize, readChunk)
          .then((result) => {
            //Display outcome in html
            mediaInfoOutput = `${mediaInfoOutput}File Name: ${file.name}\n${result}`
          })
          .catch((error) => {
            mediaInfoOutput = `${mediaInfoOutput}\n\nAn error occured:\n${error.stack}`
          })
      }

      function get_dss_duration(file,id,status) {
        let reader = new FileReader();   
        let seconds = 0; 
        //Just read in the header data
        let blob = file.slice(0, 700);
        reader.readAsText(blob);
        reader.onload = function() {
            const dssDurationHHMMSS = `${reader.result.substr(62,2)}:${reader.result.substr(64,2)}:${reader.result.substr(66,2)}`;
            //console.log(`File length is: ${dssDurationHHMMSS}`);
            const arr = dssDurationHHMMSS.split(":");
            seconds = arr[0]*3600+arr[1]*60+(+arr[2]);
            //console.log(`or ${seconds} seconds`);
            $("#qfile" + id + " td:nth-child(4)").html(dssDurationHHMMSS); // add computed to duration to UI table
                       
            if (status === 0) // status OK
            {
                filesDur[filesIds.indexOf(id)] = seconds;// adding duration in the same arrangement as filesArr
            }

            // check if all files are durated
            if (duratedFiles === filesCount) {
                // unlock the upload button
                unlockUploadUI(true);
            }

            reader.onerror = function() {
                console.log(reader.error);
            }; 
        };
        duratedFiles++;
    }

    async function genMediaInfo(mediainfo) {
        let file
        mediaInfoOutput = ''
        if (filesArr.length >= 2) {
          for (let i = 0; i < filesArr.length; i++) {
            file = filesArr[i]
            if (file) {
              await get_file_info(mediainfo, file)
              if (i + 1 == filesArr.length) {
                //console.log(`We should output ${mediaInfoOutput} to file`)
                downloadMediaInfo("vScription Transcribe Media Analyzer Results.txt", `Report generated on ${Date()}\nPlease email this file to support@vscription.com\n\n${mediaInfoOutput}\n`)
                return
              }
            }  
          }
        } else {
          file = filesArr[0]
          if (file) {
            await get_file_info(mediainfo, file)
            //console.log(`We should output ${mediaInfoOutput} to file`)
            downloadMediaInfo("vScription Transcribe Media Analyzer Results.txt", `Report generated on ${Date()}\nPlease email this file to support@vscription.com\n\n${mediaInfoOutput}\n`)
          }
        }
      }
     
    MediaInfo({ format: 'text' }, (mediainfo) => {
        getMediaInfoBtn.addEventListener('click', () => genMediaInfo(mediainfo))
    })

    function downloadMediaInfo(filename, text) {
        let element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
        element.setAttribute('download', filename);      
        element.style.display = 'none';
        document.body.appendChild(element);   
        element.click();  
        document.body.removeChild(element);
      }

    //https://developer.mozilla.org/en-US/docs/Web/Media/Formats/Audio_codecs
    const fileTypes = [
        'audio/mpeg',
        'audio/ogg',
        'audio/wav',
        'audio/ds2',
        'audio/dss',
        'audio/x-dss',
        'audio/vnd.wave',
        'audio/wave',
        'audio/x-wav',
        'audio/aac',
        'audio/alac',
        'audio/x-m4a',
        'video/mp4'
    ];

    function validFileType(file) {
        //console.log(file.type);
        return fileTypes.includes(file.type);
    }

    function returnFileSize(number) {
        if (number < 1024) {
            return number + 'bytes';
        } else if (number > 1024 && number < 1048576) {
            return (number / 1024).toFixed(1) + 'KB';
        } else if (number > 1048576) {
            return (number / 1048576).toFixed(1) + 'MB';
        }
    }

    function resetFiles() {
        input.value = "";
        filesCount = 0;
        duratedFiles = 0;
        commSize = 0;
        qCount = 0;

        // clearing arrays
        filesArr = [];
        filesDur = [];
        filesIds = [];

        tblBody.html(NO_FILES_TBL_ROW);

        clearDiv.hide();
        mediaInfoDiv.hide();
        document.getElementById('clear').setAttribute("disabled", "true");

        setDropText("");
        // mainUploadBtn.removeAttr("disabled");
    }

    resetAfterUpload();

    function resetAfterUpload() {
        tblBody.html(NO_FILES_TBL_ROW);
        document.querySelector('.submit_btn').setAttribute("disabled", "true");
        clearDiv.hide();
        mediaInfoDiv.hide();
        $('.demo_author').val("");
        $("#demo_job_type option:selected").html();
        flatPickr.setDate(new Date());
        // $("#demo_speaker_type").val(0);
        $('#demo_comments').val("");
        // getSRMinutes();
        // mainUploadBtn.removeAttr("disabled");

        setDropText("");
    }

    // function getSRenabled() {
    //     $.ajax({
    //         url: "../api/v1/users/sr-enabled/",
    //         method: "GET",
    //         dataType: "text",
    //         success: function (data) {
    //             if (data == 1) {
    //                 srEnabled = true;
    //                 speakerTypeDiv.hide();
    //                 dictDateLbl.html("File Date");
    //                 getSRMinutes();
    //                 addMinsBtn.attr("onclick", "window.open('/packages.php', '_blank')");
    //                 addMinsBtn.html('<i class="fas fa-plus-circle" ></i> ADD MINS');
    //                 $("#srBalance")[0].style.display = "block";
    //             } else {
    //                 srEnabled = false;
    //                 speakerTypeDiv.show();
    //                 dictDateLbl.html("Dictated Date");
    //                 // change add mins button
    //                 addMinsBtn.attr("onclick", "window.open('/settings.php#srSwitch', '_blank')");
    //                 addMinsBtn.html('<i class="fal fa-toggle-on"></i> Enable');
    //                 $("#srBalance")[0].style.display = "none";
    //             }
    //         }
    //     });
    // }

    function tutorialViewed() {
        resetFiles();
        var formData = new FormData();
        formData.append("page", currentPageName);
        $.ajax({
            type: 'POST',
            url: "../api/v1/users/tutorial-viewed/",
            processData: false,
            data: convertToSearchParam(formData)
        });
    }

    $('#filesInput').on('focus', function () {
        $(this).parent().addClass('focus');
    });

    $('#filesInput').on('blur', function () {
        $(this).parent().removeClass('focus');
    });

    function setCompMins(mins, callback) {
        // console.log(mins);
        var formData = new FormData();
        formData.append('cm', mins);

        $.ajax({
            type: 'POST',
            url: api_set_compmins_url,
            data: formData,
            contentType: false,
            processData: false,
            success: function () {
                callback();
            },
            error: function (err) {
                let error = JSON.stringify(err);
                alert(`An error occurred updating comp_mins. Please try again. Error: ${error}`);
            }
        });
    }
}

document.addEventListener("DOMContentLoaded", documentReady);
document.addEventListener('beforeunload', function (event) {
    if (uploadAjax !== undefined) {
        uploadAjax.abort();
        document.getElementById("upload_form").reset();
        // console.log("Upload Cancelled (2)");
    }
});

function convertToSearchParam(params) {
    const searchParams = new URLSearchParams();
    for (const [key, value] of params) {
        searchParams.set(key, value);
    }
    return searchParams;
}