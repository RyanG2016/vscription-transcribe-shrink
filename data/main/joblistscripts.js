(function ($) {
	"use strict";
})(jQuery);

function documentReady() {

	const url = 'process.php';
	const refreshJobList = document.querySelector('#refresh_btn');
	const goToUploader = document.querySelector('#newupload_btn');
	// const refreshJobListLabel = document.querySelector('.refresh_lbl');
	// const goToUploaderLabel = document.querySelector('.upload_lbl');

	// Activate ripples effect for material buttons
	new mdc.ripple.MDCRipple(document.querySelector('#newupload_btn'));
	new mdc.ripple.MDCRipple(document.querySelector('#refresh_btn'));
	// new mdc.dataTable.MDCDataTable(document.querySelector('.mdc-data-table'));

	goToUploader.addEventListener('click', e => {
		console.log("We should be going to the uploader page");
		document.location.href = 'jobupload.php';
	});

	refreshJobList.addEventListener('click', e => {
		//console.log("Refreshing job List");
		getJobList(makeSortTable);
		//var table = $('#job-list').DataTable();
	});

	//For button styling
	// refreshJobList.style.opacity = 0;
	// goToUploader.style.opacity = 0;

	//Get job list on page load

	//Job list query
	//Currently it is getting all jobs from files with a status of 0 (Awaiting Transcription)
	function getJobList(callback) {

		console.log('Getting Job List...');

		var jobListResult = $('.jobs_tbl'); //populating fields

		$.post("data/parts/backend_search.php", {
			reqcode: 8
		}).done(function (data) {
			jobListResult.html(data);
			new mdc.dataTable.MDCDataTable(document.querySelector('.mdc-data-table'));
		});


		callback();
	}

	function makeSortTable() {
		console.log('Sorting table...');

		/*setTimeout(function () {
			// var table = $('#job-list').tablesort();
			new mdc.dataTable.MDCDataTable(document.querySelector('.mdc-data-table'));
		}, 40);*/

	}

	getJobList(makeSortTable);
}


document.addEventListener("DOMContentLoaded", documentReady);