(function ($) {
	"use strict";
})(jQuery);

function documentReady() {

	const input = document.getElementById('upload_btn');
	const chooseBtn = document.getElementById('upload_btn_lbl');
	const reset = document.getElementById('clear_btn');
	const preview = document.querySelector('.preview');
	const process_files_url = 'process.php';
	const backend_url = 'data/parts/backend_request.php';
	const form = document.querySelector('form');

	new mdc.ripple.MDCRipple(document.querySelector('.clear_btn'));
	new mdc.ripple.MDCRipple(document.querySelector('.upload_btn_lbl'));
	new mdc.ripple.MDCRipple(document.querySelector('.submit_btn'));
	// new mdc.textfield.MDCTextField(document.querySelector('.mdc-text-field'));

	input.style.display = "none";

	input.addEventListener('change', addFilesToUpload);

	function clickUpload() {
		input.click();
	}

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
		files = [];
		resetFiles();
	});

	// form.addEventListener('submit', e => {
	$("#upload_form").on('submit', function (event) {
		e.preventDefault();
		if (validateFields()) {
			const files = document.querySelector('[type=file]').files
			const formData = new FormData()

			// Adding all files to formData //
			for (let i = 0; i < files.length; i++) {
				let file = files[i]
				formData.append('files[]', file)
				console.log(files[i]);
			}

			// TODO SHOULD SHOW A LOADING DIALOG


			//** Get next jobID & jobNumber **//
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
						
				
				//** Upload Files to the server **//
				fetch(backend_url, {
					method: 'POST',
					body: formData,
				}).then(response => {
					response.text() 
					.then(data => {
					if (response.ok) {
						console.log(data);
						console.log('Upload call was successful');
						var responseArr = JSON.parse(data);
						console.log(`Full JSON object: ${JSON.stringify(responseArr)}`);
						
						//Parse the HTML string(s) together so they can be inserted into the DOM html
						resetAfterUpload();
						var htmlEl = "";
						for (var key in responseArr) {
							htmlEl += responseArr[key]; 
    						console.log("Key: " + key);
    						console.log("Value: " + responseArr[key]);
							console.log(htmlEl);
						}			
						const list = document.createElement('ol');
						list.setAttribute("class", "uploadResultList");
						preview.appendChild(list);
						preview.insertAdjacentHTML("afterbegin", htmlEl);
						// TODO HIDE LOADING DIALOG & redirect to main.php
						

/*						setTimeout(function () {
							$('.upload_success_message p').html('Upload(s) Successful! ...Will automatically redirect to Job List in 2 seconds')
							setTimeout(function () {
								$('.upload_success_message p').html('Upload(s) Successful! ...Will automatically redirect to Job List in 1 seconds')
								setTimeout(function () {
									location.href = 'main.php';
								}, 1000);
							}, 1000);
						}, 1000);*/

					} else {
						// TODO HIDE LOADING DIALOG
						resetAfterUpload();
						htmlEl = "<li><span style='color=#ff00multipart/form-data\"00;'>UPLOAD EXCEPTION HAS OCCURRED. PLEASE TRY AGAIN AND IF ERROR PERSISTS, PLEASE CONTACT SUPPORT</span></li>";
						const list = document.createElement('ol');
						preview.appendChild(list);
						preview.insertAdjacentHTML("afterbegin", htmlEl);
					}
					//console.log(response)
				})

			});

		})
		}
		else {
			 	// TODO HIDE THE DIALOG
				alert("Please fill in required fields");
		}
	});

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
