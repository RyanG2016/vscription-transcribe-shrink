(function ($) {
	"use strict";
})(jQuery);


var dataTbl;
var jobsDT;
var jobsDTRef;
var totalDur = 0;
var totalTrDur = 0;

$(document).ready(function () {

	const maximum_rows_per_page_jobs_list = 10;
	var calculatedIds = [];

	// $('.tooltip').tooltipster();

	dataTbl = $('.jobs_tbl');
	jobsDT = $("#jobs-tbl");


	$.ajaxSetup({
		cache: false
	});

	const refreshJobList = document.querySelector('#refresh_btn');
	const goToUploader = document.querySelector('#newupload_btn');

	// Activate ripples effect for material buttons
	new mdc.ripple.MDCRipple(document.querySelector('#newupload_btn'));
	new mdc.ripple.MDCRipple(document.querySelector('#refresh_btn'));


	goToUploader.addEventListener('click', e => {
		// console.log("We should be going to the uploader page");
		document.location.href = 'jobupload.php';
	});

	jobsDT.on( 'init.dt', function () {
		// alert("initiating");
		if(!$('.cTooltip').hasClass("tooltipstered"))
		{
			$('.download-icon').click(function() {
				let file_id = $(this).parent().parent().attr('id');
				download(file_id);
			});

			$('.view-icon').click(function() {
				let file_id = $(this).parent().parent().attr('id');
				view(file_id);
			});

			$('.cTooltip').tooltipster({
				animation: 'grow',
				theme: 'tooltipster-punk',
				arrow: true
			});
		}
	} );

	jobsDTRef = jobsDT.DataTable( {
		rowId: 'file_id',
		"ajax": 'api/v1/files?dt',
		"processing": true,
		lengthChange: false,
		pageLength: maximum_rows_per_page_jobs_list,
		autoWidth: false,

		"columns": [
			{
				"title": "Job #",
				"data": "job_id",
				render: function (data, type, row) {
					if (row["file_comment"] != null) {
						return data + " <i class=\"material-icons mdc-button__icon job-comment cTooltip\" aria-hidden=\"true\" title='"
							+ htmlEncodeStr(row["file_comment"])
							+ "'>speaker_notes</i>";
					} else {
						return data;
					}
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
				"title": "Job Length",
				"data": "audio_length",
				render: function (data, type, row) {
					if (row["file_id"] != 0 && !calculatedIds.includes(row["file_id"])) {
						totalDur += parseInt(data);

						if (row["file_status"] == 1 || row["file_status"] == 2 || row["file_status"] == 0) {
							totalTrDur += parseInt(data);
						}
						calculatedIds.push(row["file_id"]);
					}

					return new Date(data * 1000).toISOString().substr(11, 8);
				}
			},
			{
				"title": "Job Status",
				"data": "file_status_ref"
			},
			{
				"title": "Job Transcribed",
				"data": "file_transcribed_date"
			},
			{
				"title": "Initial Download",
				"data": "text_downloaded_date"
			},
			{
				"title": "Actions",
				"data": "times_text_downloaded_date",
				render: function (data, type, row) {
					if (row["file_status"] == 3 || row["file_status"] == 7) {
						return "<a id='view-icon' class=\"material-icons view-icon\">visibility</a> <a class=\"material-icons download-icon\">cloud_download</a> <span class='times-downloaded'>+"+data+"</span>";
					}else{
						return "";
					}
				}
			}
		],

		initComplete: function () {

			calculatedIds = []; // freeing resources
			this.api().columns([0,3,4,5,7,8]).every( function () {
				var that = this;

				$( 'input', this.footer() ).on( 'keyup change clear', function () {
					if ( that.search() !== this.value ) {
						that
							.search( this.value )
							.draw();
					}
				} );
			} );

			this.api().columns([1,2,6]).every(
				function () {
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
				}
			);

		}

		
	} );

	$(
		'#jobs-tbl tfoot th:eq(0),' +
		'#jobs-tbl tfoot th:eq(3),' +
		'#jobs-tbl tfoot th:eq(4),' +
		'#jobs-tbl tfoot th:eq(7),' +
		'#jobs-tbl tfoot th:eq(8),' +
		'#jobs-tbl tfoot th:eq(5)'
	 ).each( function () {
		$(this).html( '<input class="dt-search form-control" type="text" placeholder="" />' );
	} );

	refreshJobList.addEventListener('click', e => {
		// totalDur = 0;
		jobsDTRef.ajax.reload();
	});

	jobsDT.on( 'draw.dt', function () {

		$('.download-icon').click(function () {
			let file_id = $(this).parent().parent().attr('id');
			download(file_id);
		});

		$('.view-icon').click(function () {
			let file_id = $(this).parent().parent().attr('id');
			view(file_id);
		});

		if (!$('.cTooltip').hasClass("tooltipstered")) {
			$('.cTooltip').tooltipster({
				animation: 'grow',
				theme: 'tooltipster-punk',
				arrow: true
			});
		}	// calculate total jobs duration
			$("#tjd").html("Total Jobs Length: " + new Date(totalDur * 1000).toISOString().substr(11, 8));
			$("#cbm").html("Current Backlog Minutes: " + new Date(totalTrDur * 1000).toISOString().substr(11, 8));
		}
		);
		
		// Tutorial area
	
		/**
		 * Copy this fold to any JS file for any page and just edit the following
		 * 1. enjoyhint_script_steps -> steps of the tutorial text and screen items to be highlighted
		 * 2. tutorialViewed function -> ajax 'url' relative path MAY need to be edited
		 * finally copy over the following to the page PHP code before the initializing of the JS file
		 *
		 <?php $tuts=(isset($_SESSION['tutorials']))?$_SESSION['tutorials']:'{}'; ?>
		<script type="text/javascript">
				var tutorials='<?php echo $tuts;?>';
		</script>
		 */
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
		//Only one step - highlighting(with description) "New" button
		//hide EnjoyHint after a click on the button.
		var enjoyhint_script_steps = [
			{
				"next #jobs-tbl_wrapper": "Here is the job list"
			},
			{
				"next td:nth-child(10)": "Here is where you can view or download completed documents",
			},
			{
				"next tfoot":'Use these to filter your job list',
			}
			,
			{
				"next #tjd":'Here is the length of all jobs in your account',
			}
			,
			{
				"next #cbm":'Here is the length of backlog (Jobs awaiting typing) for your account',
			}
			,		
			{
				"next #newupload_btn > div":'Click here to manually upload new jobs',
				// shape:"circle",
			}
			,
			{
				"next #collapse-icon":"Click here to expand the navigation bar to get access to various pages and settings",
				// shape:"circle",
			}
			,		
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
			//Insert sample dictation row in case there are not files in the account
			jobsDTRef.row.add(
				{acc_id: "1",
				audio_length: "60",
				billed: "0",
				file_author: "Sample Author",
				file_comment: "Sample file comment",
				file_date_dict: "2021-01-28 03:50:25",
				file_id: "0",
				file_speaker_type: "1",
				file_status: "3",
				file_status_ref: "Completed",
				file_transcribed_date: "2021-01-29 03:50:25",
				file_type: null,
				file_work_type: "Sample",
				filename: "F1_AA000_Sample_File.mp3",
				isBillable: "0",
				job_id: "AA-0000001",
				job_transcribed_by: "typist@mail.com",
				job_upload_date: "2021-01-28 03:50:25",
				job_uploaded_by: "admin@mail.com",
				last_audio_position: "0",
				org_ext: "mp3",
				orig_filename: "Sample_File.mp3",
				text_downloaded_date: "2021-02-01 14:50:25",
				times_text_downloaded_date: "5",
				tmp_name: null,
				typist_comments: "Welcome to vScription Transcribe!",
				user_field_1: "",
				user_field_2: "",
				user_field_3: ""}
				).draw();
			// show tutorial
			enjoyhint_instance.run();
		}

		function tutorialViewed() {
			var formData = new FormData();
			formData.append("page", currentPageName);
			jobsDTRef.row('#0').remove().draw();
			$.ajax({
				type: 'POST',
				url: "../api/v1/users/tutorial-viewed/",
				processData: false,
				data: convertToSearchParam(formData)
			});
		}

});


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
		// location.reload();
		// totalDur = 0;
		jobsDTRef.ajax.reload();

	});
}


function view(fileID){

	let a1 = {
		file_id: fileID
	};

	$.post("data/parts/backend_request.php", {
		reqcode: 17,
		args: JSON.stringify(a1)
	}).done(function (data) {

		// redirect to download with the generated hash
		var win = window.open('./view.php?down='+data.toString(), 'popUpWindow',
			'height=800,width=750,left=100,top=100,resizable=yes,' +
			'scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no, status=yes'
			);

		win.focus();
		// alert('refresh?');
		// location.reload();
		// totalDur = 0;
		jobsDTRef.ajax.reload();

	});
}

function htmlEncodeStr(s)
{
	return s.replace(/&/g, "&amp;")
		.replace(/>/g, "&gt;")
		.replace(/</g, "&lt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&lsquo;");
}
function convertToSearchParam(params) {
const searchParams = new URLSearchParams();
for (const [key, value] of params) {
    searchParams.set(key, value);
}
return searchParams;
}