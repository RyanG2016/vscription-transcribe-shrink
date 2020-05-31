(function ($) {
	"use strict";
})(jQuery);

function documentReady() {

	const input = document.getElementById('upload_btn');
	const chooseBtn = document.getElementById('upload_btn_lbl');
	const reset = document.getElementById('clear_btn');
	const preview = document.querySelector('.preview');
	const url = 'process.php'
	const form = document.querySelector('form')

	new mdc.ripple.MDCRipple(document.querySelector('.clear_btn'));
	new mdc.ripple.MDCRipple(document.querySelector('.upload_btn_lbl'));
	new mdc.ripple.MDCRipple(document.querySelector('.submit_btn'));

	input.style.opacity = 0;

	input.addEventListener('change', addFilesToUpload);

	function clickUpload() {
		input.click()
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
		//alert($("#demo_speaker_type").val());
	})

	form.addEventListener('submit', e => {
		e.preventDefault()
		const files = document.querySelector('[type=file]').files
		const formData = new FormData()


		for (let i = 0; i < files.length; i++) {
			let file = files[i]

			formData.append('files[]', file)
			insertUploadDB();
		}

		fetch(url, {
			method: 'POST',
			body: formData,
		}).then(response => {
			if (response.ok) {
				document.querySelector('.upload_success_message').style.display = "block";
				console.log('Upload was successful');
				resetFiles();

			} else {
				document.querySelector('.upload_failed_message').style.display = "block";
				console.log('Upload Failed. Please try again');
			}
			//console.log(response)
		})
	})

	function addFilesToUpload() {
		document.querySelector('.upload_success_message').style.display = "none";
		document.querySelector('.upload_failed_message').style.display = "none";
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
};


function insertUploadDB() {

	var vfile_author_name = $('.demo_author').val();
	var vfile_job_type = $("#demo_job_type option:selected").html();
	var vfile_dict_date = $('.demo_dictdate').val();
	var vfile_speaker_type = $("#demo_speaker_type").val();
	var vfile_job_comments = $('#demo_comments').val();
	var vjob_uploaded_by = 'TEST USER';

	var a1 = {
		file_author: vfile_author_name,
		file_work_type: vfile_job_type,
		file_dict_date: vfile_dict_date,
		file_speaker_type: vfile_speaker_type,
		file_comment: vfile_job_comments,
		job_uploaded_by: vjob_uploaded_by

	};
	console.log(a1);


	$.post("data/parts/backend_search.php", {
		reqcode: 39,
		args: JSON.stringify(a1)
	}).done(function (data) {
		console.log(data);
		setTimeout(function () {
			location.href = 'main.php';
		}, 3000);
		//alert(data);
	});

}
/////////////////////////////////////////


document.addEventListener("DOMContentLoaded", documentReady);
