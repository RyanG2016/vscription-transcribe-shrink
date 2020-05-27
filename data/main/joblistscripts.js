(function ($) {
	"use strict";
})(jQuery);

function documentReady() {

	const url = 'process.php';
	const refreshJobList = document.querySelector('#refresh_btn');
	const goToUploader = document.querySelector('#newupload_btn');
	const refreshJobListLabel = document.querySelector('.refresh_lbl');
	const goToUploaderLabel = document.querySelector('.upload_lbl');


	goToUploaderLabel.addEventListener('click', e => {
		console.log("We should be going to the uploader page");
		document.location.href = 'jobupload.php';
	});

	refreshJobListLabel.addEventListener('click', e => {
		console.log("Refreshing job List");
		getJobList();
	});

	//For button styling
	refreshJobList.style.opacity = 0;
	goToUploader.style.opacity = 0;

	//Get job list on page load
	getJobList();

	//Job list query
	//Currently it is getting all jobs from files with a status of 0 (Awaiting Transcription)
	function getJobList() {

		var jobListResult = $('.joblist'); //populating fields

		$.post("data/parts/backend_search.php", {
			reqcode: 8
		}).done(function (data) {
			jobListResult.html(data);

		});
	}
}


document.addEventListener("DOMContentLoaded", documentReady);
