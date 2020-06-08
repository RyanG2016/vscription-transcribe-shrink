(function ($) {
	"use strict";
})(jQuery);

function documentReady() {

	const input = document.getElementById('upload_btn');
	const chooseBtn = document.getElementById('upload_btn_lbl');
	const reset = document.getElementById('clear_btn');
	const cancel_popup_btn = document.getElementById('cancelUpload');
	const confirm_popup_btn = document.getElementById('confirmUpload');
	const preview = document.querySelector('.preview');
	const previewModal = document.querySelector('.previewModal');
	const process_files_url = 'process.php';
	const backend_url = 'data/parts/backend_request.php';
	const form = document.querySelector('form');

	new mdc.ripple.MDCRipple(document.querySelector('.clear_btn'));
	new mdc.ripple.MDCRipple(document.querySelector('.upload_btn_lbl'));
	new mdc.ripple.MDCRipple(document.querySelector('.submit_btn'));
	new mdc.ripple.MDCRipple(document.querySelector('#cancelUpload'));
	new mdc.ripple.MDCRipple(document.querySelector('#confirmUpload'));
	// new mdc.textfield.MDCTextField(document.querySelector('.mdc-text-field'));
	let modal = document.getElementById("modal");


	// const modalProgress = new mdc.linearProgress.MDCLinearProgress(document.querySelector('#modalProgress'));
	// const modalProgressLay = $('#modalProgress');

	/*window.onclick = function(event) {
		if (event.target == modal) {
			modal.style.display = "none";
		}
	}*/


	input.style.display = "none";

	input.addEventListener('change', addFilesToUpload);

	function clickUpload() {
		input.click();
	}
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
			// let file = $('input[type=file]')[0].files[0];
			let files = $('input[type=file]')[0].files;
			let other_data = $("#upload_form").serializeArray();

			$.each(other_data, function (key, input) {
				formData.append(input.name, input.value);
			});
			// formData.append('uploaded_file', file);

			for (let i = 0; i < files.length; i++) {
				let file = files[i]
				// formData.append('files[]', file)
				formData.append('file'+i, file)
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
						console.log("REQ65 RESPONSE: " + msg);
						stopProgressWatcher();
						updateUI(100);

						console.log(msg);
						console.log('Upload call was successful');
						console.log(`Full JSON object: ${JSON.stringify(msg)}`);
						//Parse the HTML string(s) together so they can be inserted into the DOM html
						resetAfterUpload();
						var htmlEl = "";
						let size = Object.keys(msg).length

						for (i = 0; i < size; i++) {
							htmlEl += msg[i];
							console.log("Key: " + i);
							console.log("Value: " + msg[i]);
							console.log(htmlEl);
						}

						const list = document.createElement('ol');
						list.setAttribute("class", "uploadResultList");
						previewModal.appendChild(list);
						previewModal.insertAdjacentHTML("afterbegin", htmlEl);
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
				console.log("enable watchdog1");
				enableProgressWatcher('job_upload');
				console.log("enable watchdog2");


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
					console.log("watchdog running");
					console.log("watchdog msg: " + msg);
					if (msg === 'null') {
						clearInterval(timer);
						updateUI(100);
					} else {
						let progress = JSON.parse(msg);
						let processed_bytes = progress['bytes_processed'];
						let total_bytes = progress['content_length'];
						// lets do math now
						let total_percent = Math.floor(processed_bytes * 100 / total_bytes);
						console.log("percentage completed: " + total_percent);

						updateUI(total_percent);

						if (total_percent >= 100) {

							// console.log("Should stop the timer");
							clearInterval(timer)
							updateUI(100);
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


	function updateUI(percentage)
	{
		linearProgressLay.removeClass('mdc-linear-progress--closed'); // Show progressbar
		linearProgress.progress = percentage / 100.0;
		if(percentage !== 100)
		{
			progressTxt.text(percentage + "%")
		}
		else
		{
			progressTxt.text("Complete.");
			cancel_popup_btn.style.display = "none";
			confirm_popup_btn.style.display = "inline-block";
		}
	}

	function addFilesToUpload() {
		while (preview.firstChild) {
			preview.removeChild(preview.firstChild);
		}

		const curFiles = input.files;
		if (curFiles.length === 0) {
			const par = document.createElement('p');
			par.textContent = 'No files currently selected for upload';
			preview.appendChild(par);
		} else {
			document.querySelector('.submit_btn').removeAttribute("disabled");
			document.querySelector('.clear_btn').removeAttribute("disabled");
			const list = document.createElement('ol');
			preview.appendChild(list);

			for (const file of curFiles) {
				const listItem = document.createElement('li');
				const par = document.createElement('p');

				if (validFileType(file)) {
					par.textContent = `File name ${file.name}, file size ${returnFileSize(file.size)}.`;

					listItem.appendChild(par);
				} else {
					par.textContent = `File name ${file.name}: Not a valid file type. Update your selection.`;
					listItem.appendChild(par);
				}

				list.appendChild(listItem);
			}
		}
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
		preview.removeChild(preview.firstChild);
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
