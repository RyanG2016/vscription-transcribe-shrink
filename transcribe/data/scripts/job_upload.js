(function ($) {
	"use strict";
})(jQuery);

var uploadAjax;

function documentReady() {

	const input = document.getElementById('filesInput');

	const submitUploadBtn = document.querySelector('.submit_btn');
	const cancel_popup_btn = document.getElementById('cancelUpload');
	const confirm_popup_btn = document.getElementById('confirmUpload');
	const preview = document.querySelector('.preview');
	const previewModal = document.querySelector('.previewModal');
	const backend_url = 'data/parts/backend_request.php';
	const api_insert_url = 'api/v1/files/';


	// 23-Feb-2020 12:35:40 AM

	const flatPickr = $("#dictDatePicker").flatpickr({
		enableTime: true,
		altInput: true,
		altFormat: "d-M-Y h:i:S K",
		dateFormat: "Y-m-d H:i:S",
		defaultDate: new Date()
	});


	new mdc.ripple.MDCRipple(document.querySelector('.submit_btn'));
	new mdc.ripple.MDCRipple(document.querySelector('#cancelUpload'));
	new mdc.ripple.MDCRipple(document.querySelector('#confirmUpload'));
	var uploadCarousel = $("#uploadCarousel");
	
	let modal = document.getElementById("modal");

	// const mainUploadBtn = $("#mainUploadBtn");
	const dropUploadContent = $("#vsptDropUploadContent");
	const dropUploadMainContent = $("#vsptDropMainContent");
	const clearDiv = $("#clear");
	const progressList = $("#vsptProgressList");
	const nextBtn = $("#demoNextBtn");
	const prevBtn = $("#demoBackBtn");
	const dropZone = $("#vsptDropZone");
	const speakerTypeDiv = $("#speakerTypeDiv");
	const dictDateLbl = $("#dictDateLbl");

	// allowed files for upload queue variables
	var filesArr = [];
	var filesDur = [];
	var filesIds = [];
	var filesCount = 0;

	var curFiles;
	var qCount = 0; // Queue count
	var duratedFiles = 0;
	var commSize = 0; // accumulated file sizes
	let maxFileSize  = 134217728;
	const MAX_FILES_COUNT = 10;

	var srEnabled = false;
	getSRenabled();
	var srMinutesRemaining = 0;
	var srMinutes = $("#srMinutes");

	$("#chooseFile").on("click", function(){
		input.click();
	});

	nextBtn.on("click", function(){
		uploadCarousel.carousel(2);
	});
	prevBtn.on("click", function(){
		uploadCarousel.carousel(0);
	});

	let p1nBtn = $("#p1nBtn");
	let p3bBtn = $("#p3Bbtn");

	p1nBtn.on("click", function(){
		if(filesArr.length > 0)
		{
			uploadCarousel.carousel(1);
		}
	});

	p3bBtn.on("click", function(){

		uploadCarousel.carousel(1);

	});

	dropZone.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
		e.preventDefault();
		e.stopPropagation();
	})
		.on('dragover dragenter', function() {
			dropZone.addClass('is-dragover');
			// console.log("entered");
		})
		// .on('dragleave dragend drop', function() {
		.on('dragleave drop', function() {
			dropZone.removeClass('is-dragover');
			// console.log("left");
		})
		.on('drop', function(e) {
			// console.log("file dropped");
			curFiles = e.originalEvent.dataTransfer.files;
			if(curFiles.length > 10)
			{
				setDropText("Files exceeded maximum limit (10 files)", false);
			}else{
				addFilesToUpload();
			}

		});


	uploadCarousel.on('slide.bs.carousel', function (e) {
		/*
			e.direction     // The direction in which the carousel is sliding (either "left" or "right").
		    e.relatedTarget // The DOM element that is being slid into place as the active item.
		    e.from          // The index of the current item.
		    e.to            // The index of the next item.
		*/

		switch(e.to)
		{
			case 2:
				progressList.children().eq(2).addClass("active");
			case 1:
				progressList.children().eq(1).addClass("active");
			case 0:
				progressList.children().eq(0).addClass("active");
				break;
		}

		switch(e.to + 1)
		{
			case 0:
			case 1:
				progressList.children().eq(1).removeClass("active");
			case 2:
				progressList.children().eq(2).removeClass("active");
				break;
			default: break;
		}
	})

	input.addEventListener('click', function(){
		resetFiles();
	})

	input.addEventListener('change', function(){
		curFiles = input.files;
		addFilesToUpload();
	})

	// modal.style.display = "block";
	const linearProgress = new mdc.linearProgress.MDCLinearProgress(document.querySelector('.mdc-linear-progress'));
	const linearProgressLay = $('.mdc-linear-progress');
	const progressTxt = $('#progressTxt');

	$("#clearBtn").on("click", function(){
		linearProgressLay.addClass('mdc-linear-progress--closed');
		files = [];
		resetFiles();
	});

	cancel_popup_btn.addEventListener('click', e => {
		// cancel the upload
		if(uploadAjax !== undefined) {
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
	var uploadForm =  $("#upload_form");


	uploadForm.on('submit', function (event) {
		event.preventDefault();

		uploadForm.addClass('was-validated');
		// uploadForm.addClass('was-validated');

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
				formData.append('file'+i, file);
				formData.append('dur'+i, filesDur[i]);
				// console.log(files[i]);
			}



			formData.append("sr_enabled", srEnabled);
			formData.append("authorName", $("#demo_author").val());
			formData.append("jobType", $("#demo_job_type option:selected").html());
			formData.append("dictDate", $("#dictDatePicker").val() );
			// formData.append("dictDate", $('.demo_dictdate').val());
			formData.append("speakerType", $("#demo_speaker_type").val());
			if($('#demo_comments').val() !== "")
			{
				formData.append("comments",$('#demo_comments').val() );
			}


			// CHECK UPLOADED FILES AND SAVE IT TO DB
			uploadAjax = $.ajax({
				type: 'POST',
				// url: backend_url,
				url: api_insert_url,
				data: formData,
				processData: false,
				contentType: false,
				success: function (response) {
					stopProgressWatcher();
					updateUI(100, false);

					// console.log(msg);
					// console.log('Upload call was successful');
					// console.log(`Full JSON object: ${JSON.stringify(msg)}`);
					//Parse the HTML string(s) together so they can be inserted into the DOM html
					resetAfterUpload();
					var htmlEl = "";
					let size = Object.keys(response).length

					for (i = 0; i < size; i++) {
						if(response[i]["error"] === false)
						{
							htmlEl += "<li>File: "+response[i]["file_name"]+" - <span style='color:green;'>"+response[i]["status"]+"</span></li>"
						}else{
							htmlEl += "<li>File: "+response[i]["file_name"]+" - <span style='color:red;'>"+response[i]["status"]+"</span></li>"
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
					if(err.responseJSON !== undefined){
						progressTxt.text("Error.");
						htmlEl =
							"<span style='color: darkred'>"+err.responseJSON["msg"]+"</span>"
						;
						const list = document.createElement('ol');
						previewModal.appendChild(list);
						previewModal.insertAdjacentHTML("afterbegin", htmlEl);
					}else{ // upload was cancelled by user - no error
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
		}
		else {
			event.stopPropagation();
			// alert("Please fill in required fields");
		}
	});

	function getSRMinutes()
	{
		$.ajax({
			url: "../api/v1/users/sr-mins/",
			method: "GET",
			dataType: "text",
			success: function (data) {
				srMinutesRemaining = data;
				srMinutes.html(data);
				$("#srBalance")[0].style.display = "block";
			},
			error: function(jqxhr) {
				// $("#register_area").text(jqxhr.responseText); // @text = response error, it is will be errors: 324, 500, 404 or anythings else
			}
		});
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
								clearInterval(timer)
								updateUI(100, false);
							}
						}

					}
				}
			});
		}, 600);
	}

	function stopProgressWatcher()
	{
		if(timer != null)
		{
			clearInterval(timer);
		}
	}


	function updateUI(percentage, err)
	{
		linearProgressLay.removeClass('mdc-linear-progress--closed'); // Show progressbar
		linearProgress.progress = percentage / 100.0;
		if(percentage !== 100)
		{
			progressTxt.text(percentage + "%")
		}
		else
		{
			if(!err){
				progressTxt.text("Complete.");
				$('.modal-content p i').html(""); // clear please wait message
				cancel_popup_btn.style.display = "none";
				confirm_popup_btn.style.display = "inline-block";
			}else{
				progressTxt.text("Cancelled.");
				$('.modal-content p i').html(""); // clear please wait message
				cancel_popup_btn.style.display = "none";
				confirm_popup_btn.style.display = "inline-block";
			}
		}
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

	function roundUpToAnyIncludeCurrent(number) {
		let roundTo = 15;
		// return (Math.round(number)%roundTo === 0) ? Math.round(number) : Math.round((number+roundTo/2)/roundTo)*roundTo;
		if(number%roundTo === 0)
		{
			return  Math.round(number);
		}else{
			return (Math.round((number+roundTo/2)/roundTo)*roundTo);
		}
	}

	function calculateTotalSRminutes()
	{
		let totalSRseconds = 0.0;

		for (let i = 0; i < filesDur.length; i++) {
			var sec = roundUpToAnyIncludeCurrent(filesDur[i]);
			totalSRseconds += sec;
		}

		return secsToMin(totalSRseconds);
	}

	function unlockUploadUI(unlock) {
		if(unlock)
		{
			if(filesArr.length > 0)
			{
				// check for SR
				if(srEnabled)
				{
					// check for total minutes length and sufficient balance
					var totalMinutes = calculateTotalSRminutes();
					// totalMinutes = 60;
					let balanceAfterUpload = srMinutesRemaining - totalMinutes;
					if(balanceAfterUpload < 0)
					{
						// show error msg
						submitUploadBtn.setAttribute("disabled", "true");
						let subPar = document.createElement('p');
						subPar.innerHTML = "INSUFFICIENT BALANCE | Total upload mins: " + totalMinutes +
						" | SR Balance: " + srMinutesRemaining + "<br>"
						+ "<br><span><i><u id='skipSR' style='color: #404040; cursor: pointer'>Click here to skip SR this time</u></i></span>";

						subPar.setAttribute("style", "color: darkred");
						subPar.setAttribute("id", "subBar");
						preview.appendChild(subPar);

						$("#skipSR").on("click",function(){
							srEnabled = false;
							$("#subBar")[0].innerHTML = "Speech recognition is off for this upload";
							submitUploadBtn.removeAttribute("disabled");
						});
						return;
					}

					let subPar = document.createElement('p');
					subPar.setAttribute("id", "subBar");
					subPar.innerHTML = "Total upload mins: " + totalMinutes + " | SR minutes remaining: " + balanceAfterUpload
						+ "<br><span><i><u id='skipSR' style='color: #404040; cursor: pointer'>Click here to skip SR this time</u></i></span>";
					preview.appendChild(subPar);

					$("#skipSR").on("click",function(){
						srEnabled = false;
						$("#subBar")[0].innerHTML = "Speech recognition is off for this upload";
						submitUploadBtn.removeAttribute("disabled");
					});
				}

				submitUploadBtn.removeAttribute("disabled");
			}
		}
		else{
			submitUploadBtn.setAttribute("disabled", "true");
		}
	}

	function secsToMin($seconds)
	{
		return ($seconds/60);
	}

	function computeDuration(id, file, status, dssType = 0) {

		if(dssType === 2 || file.type == "audio/ds2"){
			let duration = (file.size/1024.0)/3.4584746913; //apprx

			let durationTxt = new Date(duration * 1000).toISOString().substr(11, 8).toString();
			$("#qfile"+id+" td:nth-child(4)").html("~"+durationTxt);
			// $("#qfile"+id+" td:nth-child(5)").html(Math.round(duration));

			// increase done files counter
			duratedFiles ++;

			// add duration to upload Que in (secs) for Queued files
			if(status === 0) // status OK
			{
				// filesDur[filesIds.indexOf(id)] = Math.round(duration);// adding duration in the same arrangement as filesArr
				filesDur[filesIds.indexOf(id)] = duration;// adding duration in the same arrangement as filesArr
			}

			// check if all files are durated
			if(duratedFiles === filesCount){

				// unlock the upload button
				unlockUploadUI(true);
			}

			return;
		}

		// Create a non-dom allocated Audio element
		let audio = document.createElement('audio');
		audio.setAttribute("preload", "metadata");

		let objectUrl = URL.createObjectURL(file);
		// audio.prop("src", objectUrl);
		audio.setAttribute("src", objectUrl);

		audio.onloadedmetadata = function(){
			// alert('meta loaded for file -> ' + id + "\n duration for file " + id + " is "+ audio.duration);

			// Obtain the duration in seconds of the audio file (with milliseconds as well, a float value)
			let duration = audio.duration;

			// example 12.3234 seconds
			// console.log("The duration of file ("+id+") is of: " + duration + " seconds");
			// Alternatively, just display the integer value with
			// parseInt(duration)
			// 12 seconds
			// Update table with the duration
			let durationTxt = new Date(duration * 1000).toISOString().substr(11, 8).toString();
			$("#qfile"+id+" td:nth-child(4)").html(durationTxt);
			// $("#qfile"+id+" td:nth-child(5)").html(Math.round(duration));

			// increase done files counter
			duratedFiles ++;

			// add duration to upload Que in (secs) for Queued files
			if(status === 0) // status OK
			{
				filesDur[filesIds.indexOf(id)] = duration;// adding duration in the same arrangement as filesArr
				// filesDur[filesIds.indexOf(id)] = Math.round(duration);// adding duration in the same arrangement as filesArr
			}

			// check if all files are durated
			if(duratedFiles === filesCount){

				// unlock the upload button
				unlockUploadUI(true);
			}

			audio.remove();

		};
	}
											 // 3 -> file count exceeds limit of 20
	function getFileUploadStatus(id, size) { // 0 -> allowed,   1 -> file exceeds limit,     2 -> request exceeds limit

		if(qCount <= MAX_FILES_COUNT-1) // -1 as the qCount++ is performed after the check
		{
			if(size > maxFileSize) // 128 MB
			{
				// file size not allowed
				// remove file from upload queue
				return 1;

			}else{ // SINGLE SIZE OK -> CHECK FOR ACCUMULATIVE FILE SIZE
				if(commSize+size > maxFileSize){ // Check if total uploaded files exceeds 128MB
					// remove file from upload queue
					return 2;

				}else{ // File OK
					commSize += size; // add file size to accumulative sizes
					addFileToQueue(id);
					return 0;
				}
			}
		}else{
			// File count exceeds the limit of @MAX_FILES_COUNT
			return 3;
		}

	}

	function generateTblFileEntry(id, filename, size, status) {
		// generating a file entry
		const row = document.createElement("tr");
		row.setAttribute("id", "qfile"+id);

		const data1 = document.createElement("td");
		data1.innerHTML = id+1;

		const data2 = document.createElement("td");
		data2.innerHTML = filename;

		const data3 = document.createElement("td");
		data3.innerHTML = returnFileSize(size);

		const data4 = document.createElement("td");
		// data4.innerHTML = generateLoadingSpinner();
		data4.appendChild(generateLoadingSpinner());

		// const data5 = document.createElement("td");
		// data5.appendChild(generateLoadingSpinner());

		const data6 = document.createElement("td");



		// Check for file size to decide its upload status
		switch (status) {
			case 0: // allowed
				data6.setAttribute("style", "color: #53a13d;");
				data6.innerHTML = "Ready to upload.";
				break;

			case 1: // file exceeds limit

				data6.setAttribute("style", "color: #B00020;");
				data6.innerHTML = "File exceeds 128MB - Skipped";
				break;

			case 2: //request exceeds limit
				data6.setAttribute("style", "color: #B00020;");
				data6.innerHTML = "Total files exceed 128MB - Skipped";
				break;

			case 3:
				data6.setAttribute("style", "color: #B00020;");
				data6.innerHTML = "File count exceeds the limit of "+ MAX_FILES_COUNT +" - Skipped";
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

	function addFileToQueue(id) {
		// removing from files to be uploaded
		// filesArr.splice(id, 1);

		filesArr.push(curFiles[id]);
		filesIds.push(id); // to keep track of file index in filesArr to add its duration when duration is computed

		qCount++;
	}

	function generateErrTblFileEntry(id, filename) {
		// generating a file entry
		const row = document.createElement("tr");
		row.setAttribute("id", "qfile"+id);

		const data1 = document.createElement("td");
		data1.innerHTML = id+1;

		const data2 = document.createElement("td");
		data2.innerHTML = filename;

		const data3 = document.createElement("td");
		data3.setAttribute("colspan","4");
		data3.innerHTML = `Not a valid file type. Update your selection.`;
		data3.setAttribute("style", "color: #B00020;");
		
		// remove file from upload queue
		removeFile(id);

		row.appendChild(data1);
		row.appendChild(data2);
		row.appendChild(data3);

		return row;
	}

	function setDropText(text, allowStep = true)
	{
		if(text === "")
		{
			dropUploadMainContent.show();
			dropUploadContent.html("");
			clearDiv.hide();
			p1nBtn.hide();
		}else{
			dropUploadContent.html(text);
			dropUploadMainContent.hide();
			clearDiv.show();

			if(allowStep)
			{
				p1nBtn.show();
			}else{
				p1nBtn.hide();
			}
		}
	}

	function addFilesToUpload() {

		// clear old arrays
		filesCount = 0;
		duratedFiles = 0;
		commSize = 0;
		qCount = 0;

		// clearing arrays
		filesArr = [];
		filesDur = [];
		filesIds = [];
		while (preview.firstChild) {
			preview.removeChild(preview.firstChild);
		}

		// curFiles = input.files; // current selected files to upload by user

		/*if (f.length > 1) {
			console.log(input.files, 1);
			el.text('Sorry, multiple files are not allowed');
			return;
		}*/


		// el.removeClass('focus');
		/*el.html(f[0].name + '<br>' +
			'<span class="sml">' +
			'type: ' + f[0].type + ', ' +
			Math.round(f[0].size / 1024) + ' KB</span>');*/

		// init filesArr (to be sent for upload) -> handling done on this

		if (curFiles.length === 0) {
			const par = document.createElement('p');
			par.textContent = 'No files currently selected for upload';
			preview.appendChild(par);

			setDropText("");
		} else {

			// mainUploadBtn.attr("disabled", "disabled");

			filesCount = curFiles.length;
			let dropText = '';
			for (let i = 0; i < curFiles.length; i++) {
				dropText += ((i+1) + ". " + curFiles[i].name + " <br> ");
			}

			setDropText(dropText);

			const tbl = document.createElement("table");
			const header = document.createElement("tr");
			const headerD1 = document.createElement("th"); // file number
			const headerD2 = document.createElement("th"); // file name
			const headerD3 = document.createElement("th"); // file size
			const headerD4 = document.createElement("th"); // file duration
			// const headerD5 = document.createElement("th"); // in secs
			const headerD6 = document.createElement("th"); // status

			headerD1.innerHTML = '#';
			headerD2.innerHTML = 'File Name';
			headerD3.innerHTML = 'Size';
			headerD4.innerHTML = 'Duration';
			// headerD5.innerHTML = '(secs)';
			headerD6.innerHTML = 'Status';
			header.append(headerD1);
			header.append(headerD2);
			header.append(headerD3);
			header.append(headerD4);
			// header.append(headerD5);
			header.append(headerD6);

			tbl.setAttribute("class", "que-files");
			tbl.appendChild(header);
			preview.appendChild(tbl);

			let i = 0;
			for (const file of curFiles) {
				// const listItem = document.createElement('li');
				const par = document.createElement('p');

				if (validFileType(file))
				{

					// get file upload criteria
					let status = getFileUploadStatus(i, file.size);

					// generate a table entry
					tbl.appendChild(generateTblFileEntry(i, file.name, file.size, status));

					// Get audio duration
					computeDuration(i, file, status); // async
				}else if(file.type === "" && file.name.substr(file.name.length-3,3).toLowerCase() === "ds2"){
					let status = getFileUploadStatus(i, file.size);

					// generate a table entry
					tbl.appendChild(generateTblFileEntry(i, file.name, file.size, status));

					// Get audio duration
					computeDuration(i, file, status, 2); // async // 2: ds2
				} else {
					tbl.appendChild(generateErrTblFileEntry(i, file.name));

					// preview.appendChild(par);
				}

				preview.appendChild(par);


				i++;

				// console.log("current files: " + filesArr.toString());
			}
		}
		// console.log("current files: " + filesArr.toString());
		let report = true;
	}

	//https://developer.mozilla.org/en-US/docs/Web/Media/Formats/Audio_codecs
	const fileTypes = [
        'audio/mpeg',
        'audio/ogg',
        'audio/wav',
        'audio/ds2',
        'audio/vnd.wave',
        'audio/wave',
        'audio/x-wav',
		'audio/aac',
		'audio/alac',
		'audio/x-m4a'
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

		while (preview.firstChild) {
			preview.removeChild(preview.firstChild);
		}
		const par = document.createElement('p');
		par.textContent = 'No files currently selected for upload';
		preview.appendChild(par);
		clearDiv.hide();
		document.getElementById('clear').setAttribute("disabled", "true");

		setDropText("");
		// mainUploadBtn.removeAttr("disabled");
	}
	
	function resetAfterUpload() {
		preview.removeChild(preview.firstChild);
		const par = document.createElement('p');
		preview.appendChild(par);
		document.querySelector('.submit_btn').setAttribute("disabled", "true");
		clearDiv.hide();
		$('.demo_author').val("");
		$("#demo_job_type option:selected").html();
		flatPickr.setDate(new Date());
		$("#demo_speaker_type").val(0);
		$('#demo_comments').val("");
		getSRMinutes();
		// mainUploadBtn.removeAttr("disabled");

		setDropText("");
	}
		// Tutorial area
	
		//initialize instance
		/*var enjoyhint_instance
			= new EnjoyHint({
			onEnd:function(){
				tutorialViewed();
			},
			onSkip:function(){
				tutorialViewed();
			}
		});

		var enjoyhint_script_steps = [
			{
				"next .mdc-button__ripple": "Click here to open a file dialog where you can choose your file(s) to upload"
			},
			{
				"next .box.box7>h3": "Fill in the job details here"
			},
			{
				"next .submit_btn":'Click here to upload the file(s)'
			}
			,
			{
				"next .preview":'This will show you all of the file(s) you will be uploading'
			}
			,
			{
				" .clear_btn":'Accidentally choose the wrong files? Click here to remove the file(s) from the upload list',
				"showNext":false,
				"skipButton":{text: "Finish"}
			}
			,				
			/!**{
				"click #help > a":"Click here to access the online help",
				// shape:"circle",
				"skipButton":{text: "Finish"}
			} **!/
		];

		//set script config
		enjoyhint_instance.set(enjoyhint_script_steps);

		// get page name
		const currentPageName = location.pathname.split("/").slice(-1)[0].replace(".php","");
		// parse user tutorials data to JSON
		var tutorialsJson = JSON.parse(tutorials);
		// check if tutorial for the current page isn't viewed before
		if(tutorialsJson[currentPageName] == undefined || tutorialsJson[currentPageName] == 0){
			
			//Prep page with sample data for tutorial
			document.querySelector('.clear_btn').removeAttribute("disabled");
			document.querySelector('.submit_btn').removeAttribute("disabled");

			const tbl = document.createElement("table");
			const header = document.createElement("tr");
			const headerD1 = document.createElement("th"); // file number
			const headerD2 = document.createElement("th"); // file name
			const headerD3 = document.createElement("th"); // file size
			const headerD4 = document.createElement("th"); // file duration
			const headerD6 = document.createElement("th"); // status

			headerD1.innerHTML = '#';
			headerD2.innerHTML = 'File Name';
			headerD3.innerHTML = 'Size';
			headerD4.innerHTML = 'Duration';
			headerD6.innerHTML = 'Status';
			header.append(headerD1);
			header.append(headerD2);
			header.append(headerD3);
			header.append(headerD4);
			header.append(headerD6);

			tbl.setAttribute("class", "que-files");
			tbl.appendChild(header);
			tbl.appendChild(generateTblFileEntry(0, "PSP0876.DS2", "1346", 0));
			preview.appendChild(tbl);

			//Start Tutorial
			enjoyhint_instance.run();
		}*/

		function getSRenabled()
		{
			$.ajax({
				url: "../api/v1/users/sr-enabled/",
				method: "GET",
				dataType: "text",
				success: function (data) {
					if(data == 1)
					{
						srEnabled = true;
						speakerTypeDiv.hide();
						dictDateLbl.html("File Date");
						getSRMinutes();
					}
					else{
						srEnabled = false;
						speakerTypeDiv.show();
						dictDateLbl.html("Dictated Date");
					}
				}
			});
		}

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

	$('#filesInput').on('focus', function() {
		$(this).parent().addClass('focus');
	});

	$('#filesInput').on('blur', function() {
		$(this).parent().removeClass('focus');
	});
}

document.addEventListener("DOMContentLoaded", documentReady);
document.addEventListener('beforeunload', function(event) {
	if(uploadAjax !== undefined) {
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