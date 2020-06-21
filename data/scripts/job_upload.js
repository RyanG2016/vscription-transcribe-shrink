(function ($) {
	"use strict";
})(jQuery);

function documentReady() {

	const input = document.getElementById('upload_btn');
	const chooseBtn = document.getElementById('upload_btn_lbl');
	const reset = document.getElementById('clear_btn');
	const submitUploadBtn = document.querySelector('.submit_btn');
	const cancel_popup_btn = document.getElementById('cancelUpload');
	const confirm_popup_btn = document.getElementById('confirmUpload');
	const preview = document.querySelector('.preview');
	const previewModal = document.querySelector('.previewModal');
	// const process_files_url = 'process.php';
	const backend_url = 'data/parts/backend_request.php';
	const form = document.querySelector('form');

	new mdc.ripple.MDCRipple(document.querySelector('.clear_btn'));
	new mdc.ripple.MDCRipple(document.querySelector('.upload_btn_lbl'));
	new mdc.ripple.MDCRipple(document.querySelector('.submit_btn'));
	new mdc.ripple.MDCRipple(document.querySelector('#cancelUpload'));
	new mdc.ripple.MDCRipple(document.querySelector('#confirmUpload'));
	// new mdc.textfield.MDCTextField(document.querySelector('.mdc-text-field'));
	let modal = document.getElementById("modal");

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


	// const modalProgress = new mdc.linearProgress.MDCLinearProgress(document.querySelector('#modalProgress'));
	// const modalProgressLay = $('#modalProgress');

	/*window.onclick = function(event) {
		if (event.target == modal) {
			modal.style.display = "none";
		}
	}*/


	input.style.display = "none";

	input.addEventListener('click', function(){
		resetFiles();
	})

	input.addEventListener('change', addFilesToUpload);

	// modal.style.display = "block";
	const linearProgress = new mdc.linearProgress.MDCLinearProgress(document.querySelector('.mdc-linear-progress'));
	const linearProgressLay = $('.mdc-linear-progress');
	const progressTxt = $('#progressTxt');
	// linearProgressLay.removeClass('mdc-linear-progress--closed');
	// linearProgress.progress = 0.5;
	// linearProgress.determinate = false

	// input.addEventListener('click', clickUpload);

	const clear_btn = document.querySelector('.clear_btn');
	clear_btn.addEventListener('click', e => {
		/*    ---Doesn't seem to work. The Cancel doesn't NOT remove the files---  
		      status = window.confirm('Remove files queued for download?');
		      if (status) {
		        files = [];
		        resetFiles();
		      } else {
		        alert('Cancel was pressed. Files should remain');
		      }*/
		linearProgressLay.addClass('mdc-linear-progress--closed');
		files = [];
		resetFiles();
	});

	cancel_popup_btn.addEventListener('click', e => {
		// cancel the upload
		location.reload(); // reload is sufficient to cancel it
	});

	confirm_popup_btn.addEventListener('click', e => {
		// confirm upload results -> return to job list ?
		location.href = 'main.php';
	});

	// form.addEventListener('submit', e => {

	// progress timer
	var timer;

	$("#upload_form").on('submit', function (event) {
		event.preventDefault();

		if (validateFields()) {
			modal.style.display = "block"; // show the upload progress window

			event.preventDefault();
			// lets do ajax...
			let formData = new FormData();
			// let files = $('input[type=file]')[0].files;
			let files = filesArr;
			let other_data = $("#upload_form").serializeArray();

			$.each(other_data, function (key, input) {
				formData.append(input.name, input.value);
			});
			// formData.append('uploaded_file', file);

			for (let i = 0; i < files.length; i++) {
				let file = files[i];
				// formData.append('files[]', file)
				formData.append('file'+i, file);
				formData.append('dur'+i, filesDur[i]);
				console.log(files[i]);
			}


			$.post("data/parts/backend_request.php", {
				reqcode: 60
				// ,args: JSON.stringify(arg)
			}).done(function (data) {
				let nextNums = JSON.parse(data);
				let nextJobID = nextNums.next_job_id;
				let nextJobNum = nextNums.next_job_num;

				formData.append("nextFileID", nextJobID);
				formData.append("nextJobNum", nextJobNum);
				formData.append("reqcode", 61);
				formData.append("authorName", $('.demo_author').val());
				formData.append("jobType", $("#demo_job_type option:selected").html());
				formData.append("dictDate", $('.demo_dictdate').val());
				formData.append("speakerType", $("#demo_speaker_type").val());
				formData.append("comments", $('#demo_comments').val());


				// CHECK UPLOADED FILES AND SAVE IT TO DB
				$.ajax({
					type: 'POST',
					url: backend_url,
					data: formData,
					processData: false,
					contentType: false,
					success: function (msg) {
						var error = false;
						// console.log("REQ65 RESPONSE: " + msg);
						if(msg === "Bad request")
						{
							error = true;
						}
						stopProgressWatcher();
						updateUI(100, error);

						// console.log(msg);
						// console.log('Upload call was successful');
						// console.log(`Full JSON object: ${JSON.stringify(msg)}`);
						//Parse the HTML string(s) together so they can be inserted into the DOM html
						resetAfterUpload();
						var htmlEl = "";
						let size = Object.keys(msg).length

						for (i = 0; i < size; i++) {
							htmlEl += msg[i];
							// console.log("Key: " + i);
							// console.log("Value: " + msg[i]);
							// console.log(htmlEl);
						}
						if(error){
							htmlEl += ", make sure your uploaded files are less than 128 MB and are less than 10 files."
						}

						const list = document.createElement('ol');
						list.setAttribute("class", "uploadResultList");
						previewModal.appendChild(list);
						previewModal.insertAdjacentHTML("afterbegin", htmlEl);

						var a1 = {
							mailtype: 15,
							usertype: 3   //Typist
						};
						// Generate Email Notifications
						$.post("data/parts/backend_request.php", {
							reqcode: 80,
							args: JSON.stringify(a1)
						}).done(function (data) {
							// console.log(data);
						});
						// TODO HIDE LOADING DIALOG & redirect to main.php


						/*setTimeout(function () {
							$('.upload_success_message p').html('Upload(s) Successful! ...Will automatically redirect to Job List in 2 seconds')
							setTimeout(function () {
								$('.upload_success_message p').html('Upload(s) Successful! ...Will automatically redirect to Job List in 1 seconds')
								setTimeout(function () {
									location.href = 'main.php';
								}, 1000);
							}, 1000);
						}, 1000);*/

					},
					error: function (err) {
						console.log("REQ65 RESPONSE: " + err);
						stopProgressWatcher();

						// TODO HIDE LOADING DIALOG
						resetAfterUpload();
						htmlEl = "<li><span style='color=#ff00multipart/form-data\"00;'>UPLOAD EXCEPTION HAS OCCURRED. PLEASE TRY AGAIN AND IF ERROR PERSISTS, PLEASE CONTACT SUPPORT</span></li>";
						const list = document.createElement('ol');
						previewModal.appendChild(list);
						previewModal.insertAdjacentHTML("afterbegin", htmlEl);
					}
				});

				// now query for upload progress...
				// console.log("enable watchdog1");
				enableProgressWatcher('job_upload');
				// console.log("enable watchdog2");


			})




			//** Get next jobID & jobNumber **//
			// MOVE CODE HERE //
		}
		else {
			// TODO HIDE THE DIALOG
			alert("Please fill in required fields");
		}
	});



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
						let progress = JSON.parse(msg);
						let processed_bytes = progress['bytes_processed'];
						let total_bytes = progress['content_length'];
						// lets do math now
						let total_percent = Math.floor(processed_bytes * 100 / total_bytes);
						console.log("percentage completed: " + total_percent);

						updateUI(total_percent, false);

						if (total_percent >= 100) {

							// console.log("Should stop the timer");
							clearInterval(timer)
							updateUI(100, false);
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

	function unlockUploadUI(unlock) {
		if(unlock)
		{
			if(filesArr.length > 0)
			{
				submitUploadBtn.removeAttribute("disabled");
			}
		}
		else{
			submitUploadBtn.setAttribute("disabled", "true");
		}
	}

	function computeDuration(id, file, status) {

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
			$("#qfile"+id+" td:nth-child(5)").html(Math.round(duration));

			// increase done files counter
			duratedFiles ++;

			// add duration to upload Que in (secs) for Queued files
			if(status === 0) // status OK
			{
				filesDur[filesIds.indexOf(id)] = Math.round(duration);// adding duration in the same arrangement as filesArr
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

		const data5 = document.createElement("td");
		data5.appendChild(generateLoadingSpinner());

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
		row.appendChild(data5);
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

	function addFilesToUpload() {
		while (preview.firstChild) {
			preview.removeChild(preview.firstChild);
		}

		curFiles = input.files; // current selected files to upload by user

		// init filesArr (to be sent for upload) -> handling done on this

		if (curFiles.length === 0) {
			const par = document.createElement('p');
			par.textContent = 'No files currently selected for upload';
			preview.appendChild(par);
		} else {

			filesCount = curFiles.length;

			// document.querySelector('.submit_btn').removeAttribute("disabled");
			document.querySelector('.clear_btn').removeAttribute("disabled");
			// const list = document.createElement('ol');
			// preview.appendChild(list);

			const tbl = document.createElement("table");
			const header = document.createElement("tr");
			const headerD1 = document.createElement("th"); // file number
			const headerD2 = document.createElement("th"); // file name
			const headerD3 = document.createElement("th"); // file size
			const headerD4 = document.createElement("th"); // file duration
			const headerD5 = document.createElement("th"); // in secs
			const headerD6 = document.createElement("th"); // status

			headerD1.innerHTML = '#';
			headerD2.innerHTML = 'File Name';
			headerD3.innerHTML = 'Size';
			headerD4.innerHTML = 'Duration';
			headerD5.innerHTML = '(secs)';
			headerD6.innerHTML = 'Status';
			header.append(headerD1);
			header.append(headerD2);
			header.append(headerD3);
			header.append(headerD4);
			header.append(headerD5);
			header.append(headerD6);

			tbl.setAttribute("class", "que-files");
			tbl.appendChild(header);
			preview.appendChild(tbl);

			let i = 0;
			for (const file of curFiles) {
				// const listItem = document.createElement('li');
				const par = document.createElement('p');

				if (validFileType(file)) {

					// get file upload criteria
					let status = getFileUploadStatus(i, file.size);

					// generate a table entry
					tbl.appendChild(generateTblFileEntry(i, file.name, file.size, status));

					// Get audio duration
					computeDuration(i, file, status); // async
				} else {
					tbl.appendChild(generateErrTblFileEntry(i, file.name));

					// preview.appendChild(par);
				}

				preview.appendChild(par);
				i++;

				// console.log("current files: " + filesArr.toString());
			}
		}
		console.log("current files: " + filesArr.toString());
	}

	//https://developer.mozilla.org/en-US/docs/Web/Media/Formats/Audio_codecs
	const fileTypes = [
        'audio/mpeg',
        'audio/ogg',
        'audio/wav',
        'audio/dss',
        'audio/ds2',
        'audio/vnd.wave',
        'audio/wave',
        'audio/x-wav'
    ];

	function validFileType(file) {
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
		document.querySelector('.submit_btn').setAttribute("disabled", "true");
		document.querySelector('.clear_btn').setAttribute("disabled", "true");
	}
	
	function resetAfterUpload() {
		preview.removeChild(preview.firstChild);
		const par = document.createElement('p');
		preview.appendChild(par);
		document.querySelector('.submit_btn').setAttribute("disabled", "true");
		document.querySelector('.clear_btn').setAttribute("disabled", "true");
		$('.demo_author').val("");
		$("#demo_job_type option:selected").html();
		$('.demo_dictdate').val("yyyy-mm-dd");
		$("#demo_speaker_type").val(0);
		$('#demo_comments').val("");		
	}
}

/////////////////////////////////////////
function validateFields() {
			var passed = 1;
			if ($('.demo_author').val() === ""){
				document.querySelector('.demo_author').style.backgroundColor = '#eec5c9';
				passed = 0;
			}
			if ($('.demo_dictdate').val() === ""){
				document.querySelector('.demo_dictdate').style.backgroundColor = '#eec5c9';
				passed = 0;
			}
			var selOpt = document.getElementById('demo_speaker_type');
			var opt = selOpt.options[selOpt.selectedIndex];
			if (opt.text === "--Please Select--"){
				document.querySelector('#demo_speaker_type').style.backgroundColor = '#eec5c9';
				passed = 0;
			}

			return passed === 1;
}

document.addEventListener("DOMContentLoaded", documentReady);
