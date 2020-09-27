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

	// $('.tooltip').tooltipster();

	dataTbl = $('.jobs_tbl');
	jobsDT = $("#jobs-tbl");

	$("body").niceScroll({
		hwacceleration: true,
		smoothscroll: true,
		cursorcolor: "white",
		cursorborder: 0,
		scrollspeed: 10,
		mousescrollstep: 20,
		cursoropacitymax: 0.7
		//		cursorwidth: 16

	});
	$.ajaxSetup({
		cache: false
	});

	const refreshJobList = document.querySelector('#refresh_btn');
	const goToUploader = document.querySelector('#newupload_btn');
	const manage_typists_btn = document.querySelector('#manage_typists_btn');
	// const refreshJobListLabel = document.querySelector('.refresh_lbl');
	// const goToUploaderLabel = document.querySelector('.upload_lbl');

	// Activate ripples effect for material buttons
	new mdc.ripple.MDCRipple(document.querySelector('#newupload_btn'));
	new mdc.ripple.MDCRipple(document.querySelector('#refresh_btn'));
	new mdc.ripple.MDCRipple(document.querySelector('#manage_typists_btn'));

	manage_typists_btn.addEventListener('click', e => {
		document.location.href = 'manage_typists.php';
	});

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
		columnDefs: [
			{
				targets: ['_all'],
				className: 'mdc-data-table__cell'
			}
		],
		"columns": [
			{ "data": "job_id",
				render: function ( data, type, row ) {
					if(row["file_comment"] != null)
					{
						return data + " <i class=\"material-icons mdc-button__icon job-comment cTooltip\" aria-hidden=\"true\" title='"
							+htmlEncodeStr(row["file_comment"])
							+"'>speaker_notes</i>";
					}else{
						return data;
					}
				}
			},
			{ "data": "file_author" },
			{ "data": "file_work_type" },
			{ "data": "file_date_dict" },
			{ "data": "job_upload_date" },
			{ "data": "audio_length",
				render: function (data,type,row) {
					totalDur += parseInt(data);

					if(row["file_status"] == 1 || row["file_status"] == 2 || row["file_status"] == 0)
					{
						totalTrDur += parseInt(data);
					}

					return new Date(data * 1000).toISOString().substr(11, 8);
				}
			},
			{ "data": "file_status_ref" },
			{ "data": "file_transcribed_date" },
			{ "data": "text_downloaded_date" },
			{ "data": "times_text_downloaded_date",
				render: function ( data, type, row ) {
					if(row["file_status"] == 3){
						return "<a id='view-icon' class=\"material-icons view-icon\">visibility</a> <a class=\"material-icons download-icon\">cloud_download</a> <span class='times-downloaded'>+"+data+"</span>";
					}else{
						return "";
					}
				}
			}
		],

		initComplete: function () {

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
					var select = $('<select><option value=""></option></select>')
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
		// var title = $(this).text();
		// $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
		$(this).html( '<input class="dt-search" type="text"/>' );
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
				"next td#tjd":'Here is the length of all jobs in your account',
			}
			,
			{
				"next td#cbm":'Here is the length of backlog (Jobs awaiting typing) for your account',
			}
			,		
			{
				"next .mdc-button__ripple":'Click here to manually upload new jobs',
				// shape:"circle",
			}
			,
			{
				"next .navbar-toggler collapsed":"Click here to expand the navigation bar to get access to various settings",
				// shape:"circle",
			}
			,		
			{
				"click #help > a":"Click here to access the online help",
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
			// show tutorial
			enjoyhint_instance.run();
		}

		function tutorialViewed() {
			var formData = new FormData();
			formData.append("page", currentPageName);
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