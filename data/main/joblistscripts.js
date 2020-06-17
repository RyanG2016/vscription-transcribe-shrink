(function ($) {
	"use strict";
})(jQuery);


var dataTbl;

function documentReady() {

	const maximum_rows_per_page_jobs_list = 10;

	// $('.tooltip').tooltipster();

	dataTbl = $('.jobs_tbl');



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

		$.post("data/parts/backend_request.php", {
			reqcode: 8
		}).done(function (data) {
			jobListResult.html(data);
			if(data !== "<p>No matches found</p>")
			{
				new mdc.dataTable.MDCDataTable(document.querySelector('.mdc-data-table'));

				dataTbl.on( 'init.dt', function () {
					if(!$('.cTooltip').hasClass("tooltipstered"))
					{
						$('.cTooltip').tooltipster({
							animation: 'grow',
							theme: 'tooltipster-punk',
							arrow: true
						});
					}
				} );
				dataTbl = $('.jobs_tbl').DataTable(
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

				dataTbl.on( 'draw', function () {
						if(!$('.cTooltip').hasClass("tooltipstered"))
						{
							$('.cTooltip').tooltipster({
								animation: 'grow',
								theme: 'tooltipster-punk',
								arrow: true
							});
						}
					}
				);


				$('.download-icon').click(function() {
					let file_id = $(this).parent().parent().attr('id');
					download(file_id);
				});


			}
		});


		callback();
	}

	function makeSortTable() {
		// console.log('Sorting table...');

		/*setTimeout(function () {
			// var table = $('#job-list').tablesort();
			new mdc.dataTable.MDCDataTable(document.querySelector('.mdc-data-table'));
		}, 40);*/

	}

	getJobList(makeSortTable);


}


function download(fileID){
	
	let a1 = {
        file_id: fileID
    };

    $.post("data/parts/backend_request.php", {
        reqcode: 17,
        args: JSON.stringify(a1)
    }).done(function (data) {
		// alert("hash received = " + data.toString());

		// redirect to download with the generated hash
		var win = window.open('./download.php?down='+data.toString(), '_blank');
		win.focus();
		// alert('refresh?');
		location.reload();

    });
}

document.addEventListener("DOMContentLoaded", documentReady);
