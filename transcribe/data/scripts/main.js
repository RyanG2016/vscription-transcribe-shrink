(function ($) {
	"use strict";
})(jQuery);


var dataTbl;
var jobsDT;
var jobsDTRef;
var totalDur = 0;
var totalTrDur = 0;
var autoListRefresh = 0;
var autoListRefreshInterval = 10000;

//getAutoListRefreshInterval();

$(document).ready(function () {

	const maximum_rows_per_page_jobs_list = 15;
	var calculatedIds = [];

	getAutoListRefreshEnabled();
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

			$('sup').tooltip(
				{
					title:'Job has comments, please review'
				}
			);

			$('.cTooltip').tooltipster({
				animation: 'grow',
				side: ['bottom', 'right'],
				theme: 'tooltipster-punk',
				contentAsHTML: true,
				arrow: true
			});
		}
	} );
	$.fn.dataTable.ext.errMode = 'none';
	jobsDTRef = jobsDT.DataTable( {
		rowId: 'file_id',
		"ajax": 'api/v1/files?dt',
		"processing": true,
		// dom: 'Bfrtip',
		// buttons: [
		// 	{
		// 		text: 'My button',
		// 		action: function ( e, dt, node, config ) {
		// 			alert( 'Button activated' );
		// 		}
		// 	}
		// ],

		responsive: true,
		lengthChange: false,
		pageLength: maximum_rows_per_page_jobs_list,
		autoWidth: false,
		order:[[0,"desc"]],

		"columns": [
			{
				"title": "Job #",
				"className":"all",
				"data": "job_id",
				render: function (data, type, row) {
					// console.log(row);
					// var addition = "";
					var result = data;

					// let fields = ["user_field_1", "user_field_2", "typist_comments"];
					// /* Additional Popup */
					// fields.forEach(value => {
					// 	if(row[value] !== null && row[value] !== "")
					// 	{
					// 		if(addition !== "")
					// 		{
					// 			addition += "<br><br>";
					// 			// addition += "\n";
					// 		}
					// 		addition += `<b>${value}</b>: ${row[value]}`;
					// 	}
					// });

					if (row["file_comment"] != null) {
						result += "<sup>●</sup>" ;
						// result = `<i class="fas fa-comment-alt-lines vspt-fa-blue cTooltip" data-html="true"  title="${htmlEncodeStr(row["file_comment"])}"></i>`;
					}

					if(((new Date() - new Date(row.job_upload_date)) / (1000 * 60 * 60 * 24)) < 1)
					{
						result += "&nbsp;<span class=\"badge badge-success\">New</span>";
					}

					// if(addition !== "")
					// {
					// 	result += `&nbsp;<i class="fas fa-info-square vspt-fa-blue cTooltip" data-html="true"  title="${addition}"></i>`;
					// }
					// if(result)
					// {
					// 	result = `<span class="align-middle float-right">${result}</span>`
					// }
					// return data + result + "&nbsp;<span class=\"badge badge-info\">New</span>";

					return result;
				}
			},
			{
				"title": "Job Identifier",
				"data": "user_field_3"
			},
			/*{
				"title": "Job user_field_1",
				"data": "user_field_1"
			},
			{
				"title": "Job user_field_2",
				"data": "user_field_2"
			},*/
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
			/*{
				"title": "Job Transcribed",
				"data": "file_transcribed_date"
			},*/
			{
				"title": "Initial Download",
				"data": "text_downloaded_date"
			},
			{
				"title": "Actions",
				"className":"all",
				"data": "times_text_downloaded_date",
				render: function (data, type, row) {
					if (row["file_status"] == 3 || row["file_status"] == 7) {
						return "<a id='view-icon' class=\"material-icons view-icon\">visibility</a> <a class=\"material-icons download-icon\">cloud_download</a> <span class='times-downloaded'>+"+data+"</span>";
					}else{
						return "";
					}
				}
			},
			{
				"title": "Transcribed Date",
				"className":"none",
				"data": "job_upload_date"
			},
			{
				"title": "Date Uploaded",
				"className":"none",
				"data": "job_upload_date"
			},
			{
				"title": "Uploaded By",
				"className":"none",
				"data": "job_uploaded_by"
			},
			{
				"title": "Original filename",
				"className":"none",
				"data": "orig_filename"
			},
			{
				"title": "User Field 1",
				"className":"none",
				"data": "user_field_1"
			},
			{
				"title": "User Field 2",
				"className":"none",
				"data": "user_field_2"
			},
			{
				"title": "User Field 3",
				"className":"none",
				"data": "user_field_3"
			},
			{
				"title": "File Comments",
				"className":"none",
				"data": "file_comment"
			},
			{
				"title": "Typist Comments",
				"className":"none",
				"data": "typist_comments"
			}
		],

		initComplete: function () {
			calculatedIds = []; // freeing resources
			this.api().columns([0,1,4,5,7]).every( function () {
				var that = this;

				$( 'input', this.footer() ).on( 'keyup change clear', function () {
					if ( that.search() !== this.value ) {
						that
							.search( this.value )
							.draw();
					}
				} );
			} );

			this.api().columns([2,3,6]).every(
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
			$("#tjd").html("Total Jobs Length: " + new Date(totalDur * 1000).toISOString().substr(11, 8));
			$("#cbm").html("Current Backlog Minutes: " + new Date(totalTrDur * 1000).toISOString().substr(11, 8));
		}

		
	} );

	// jobsDTRef.buttons().container()
	// 	.appendTo( $('#jobs-tbl_wrapper > div:nth-child(1) > div:nth-child(1)'));

	$(
		'#jobs-tbl tfoot th:eq(0),' +
		'#jobs-tbl tfoot th:eq(1),' +
		'#jobs-tbl tfoot th:eq(4),' +
		'#jobs-tbl tfoot th:eq(7),' +
		'#jobs-tbl tfoot th:eq(5)'
	 ).each( function () {
		$(this).html( '<input class="dt-search form-control" type="text" placeholder="" />' );
	} );

	refreshJobList.addEventListener('click', e => {
		// totalDur = 0;
		jobsDTRef.ajax.reload(dtTableReloadCallback);
		$("#tjd").html("Total Jobs Length: " + new Date(totalDur * 1000).toISOString().substr(11, 8));
		$("#cbm").html("Current Backlog Minutes: " + new Date(totalTrDur * 1000).toISOString().substr(11, 8));
	});

	$("#showCompBtn").on('click', function() {
		if (!showingCompleted) {
			showingCompleted = true;
			$(this).html('<i class="fas fa-eye-slash"></i> Hide Completed')
			jobsDTRef.ajax.url( 'api/v1/files/completed?dt' ).load(); // &file_status[mul]=3,11
		} else {
			showingCompleted = false;
			jobsDTRef.ajax.url( 'api/v1/files/pending?dt' ).load(); // &file_status[mul]=0,1,2,7,11
			$(this).html('<i class="fas fa-eye-slash"></i> View Completed')
		}
	});

	jobsDT.on( 'error.dt', function ( e, settings, techNote, message ) {
		// console.log( 'An error has been reported by DataTables: ', message );
		console.log( 'Failed to retrieve data' );
	} )

	jobsDT.on( 'draw.dt search.dt', function () {

		$('.download-icon').click(function () {
			let file_id = $(this).parent().parent().attr('id');
			download(file_id);
		});

		$('.view-icon').click(function () {
			let file_id = $(this).parent().parent().attr('id');
			view(file_id);
		});

		$('.btTooltip').tooltip({"trigger": 'hover focus'});

		$('sup').tooltip(
			{
				title:'Job has comments, please review'
			}
		);

		if (!$('.cTooltip').hasClass("tooltipstered")) {
			$('.cTooltip').tooltipster({
				animation: 'grow',
				side: ['bottom', 'right'],
				theme: 'tooltipster-punk',
				contentAsHTML: true,
				arrow: true
			});
		}	// calculate total jobs duration
		if (tutorialStarted = false) {
			$("#tjd").html("Total Jobs Length: " + new Date(totalDur * 1000).toISOString().substr(11, 8));
			$("#cbm").html("Current Backlog Minutes: " + new Date(totalTrDur * 1000).toISOString().substr(11, 8));
		}
		}
		);
		
		// get page name
		const currentPageName = location.pathname.split("/").slice(-1)[0].replace(".php","");
		// parse user tutorials data to JSON
		var tutorialsJson = JSON.parse(tutorials);
		// check if tutorial for the current page isn't viewed before
		var tutorialStarted = false;
		if(tutorialsJson[currentPageName] == undefined || tutorialsJson[currentPageName] == 0){
			//Insert sample dictation row in case there are not files in the account
			tutorialStarted = true;
			jobsDTRef.row.add(
				{acc_id: "1",
				audio_length: "89",
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
				user_field_1: "143445678",
				user_field_2: "",
				user_field_3: ""}
				).draw();
				$("#tjd").html("Total Jobs Length: " + new Date(89 * 1000).toISOString().substr(11, 8));
				$("#cbm").html("Current Backlog Minutes: " + new Date(89 * 1000).toISOString().substr(11, 8));

			// show tutorial
			setTimeout(function() {
				introJs().setOptions({
				steps: [
				{
				title: 'Job Lister Page Tutorial',
				intro: 'Please take a minute to go through this short tutorial to get familiar with the Job Lister page. This is where you will upload, download and view completed documents. You can skip the tutorial for now by pressing ESC on your keyboard or click Next to start'
				},
				{
				title: 'Job List',
				element: '#jobs-tbl > tbody > tr',
				intro: 'Here is the job list. By default it loads all jobs but we\'ll show you how to filter them',
				position: 'top'
				},
				{
				title: 'Additional Job Info',
				element: 'td:nth-child(1)',
				intro: 'Click the arrow button to open the job fold to see more job information'
				},
				{
				title: 'Comment Indicator',
				element: 'td.all.dtr-control.sorting_1 > sup',
				intro: 'If you see this orange dot, it means there are additional comments you may want to look at. Expand the fold to see them. We went through this on the previous step.'
				},
				{
				title: 'View Completed Job',
				element: '#view-icon',
				intro: 'Clicking this icon will open a popup window displaying the document text. </br> <i>Note: You\'ll only see this if the job has been typed or has been converted using speech to text</i>'
				},
				{
				title: 'Download Completed Job',
				element: 'td:nth-child(9) > a.material-icons.download-icon',
				intro: 'Clicking this icon will download the completed document text. </br><i>Note: You\'ll only see this if the job has been typed or has been converted using speech to text</i>'
				},            
				{
				title: 'Activity Indicator',
				element: 'td:nth-child(9) > span',
				intro: 'This shows you how many times a report has been viewed or downloaded. All activity is logged in the audit table.'
				},
				{
				title: 'Job Filters',
				element: '#jobs-tbl > tfoot > tr',
				intro: 'Using these filters, you can filter the list as you need. You can also search and we\'ll show you that too'
				},
				{
				title: 'Total Job Backlog',
				element: '#cbm',
				intro: 'This shows you how many of your jobs are awaiting typing'
				},
				{
				title: 'Total Active Jobs',
				element: '#tjd',
				intro: 'This tells you how many active jobs you have in the system. '
				},
				{
				title: 'Job List Auto-Refresh',
				element: '#jlr',
				intro: 'This tells you whether your job list will auto refresh or not. This can be configured in the settings'
				},
				{
				title: 'Upload New Jobs',
				element: '#newupload_btn > div',
				intro: 'Click this button if you want to upload jobs. This will take you to the Job Upload page.'
				},
				{
				title: 'Navigation Bar',
				element: '#sidebar-container',
				intro: 'Use the navigation bar to get access to various pages and settings'
				},
				{
				title: 'Need Help?',
				element: '#zohohc-asap-web-launcherbox',
				intro: 'Click here to access the online help'
				}]
			}).oncomplete(function() {
				//alert("Tutorial is done");
				tutorialViewed();
			  })
			  //.setOption("showStepNumbers", true)
			  .setOption("exitOnOverlayClick", "false")
			  .setOption("overlayOpacity", ".75")
			  .setOption("skipLabel","Skip")
			  //.setOption("tooltipPosition", "bottom-middle-aligned")
			//   .setOption("exitOnEsc", "true")
			  .onbeforeexit(function() {
				jobsDTRef.row('#0').remove().draw();
			  })
			.start(); 
		}, 1500);
		}

		function tutorialViewed() {
			var formData = new FormData();
			formData.append("page", currentPageName);
			tutorialStarted = false;
			jobsDTRef.row('#0').remove().draw();
			$.ajax({
				type: 'POST',
				url: "../api/v1/users/tutorial-viewed/",
				processData: false,
				data: convertToSearchParam(formData)
			});
			jobsDTRef.ajax.reload(dtTableReloadCallback);
			$("#tjd").html("Total Jobs Length: " + new Date(totalDur * 1000).toISOString().substr(11, 8));
			$("#cbm").html("Current Backlog Minutes: " + new Date(totalTrDur * 1000).toISOString().substr(11, 8));			
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
		jobsDTRef.ajax.reload(dtTableReloadCallback, false);

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
		jobsDTRef.ajax.reload(dtTableReloadCallback, false);

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
function getAutoListRefreshEnabled() {
	$.ajax({
		url: "../api/v1/users/list-refresh-enabled/",
		method: "GET",
		dataType: "text",
		success: function (data) {
			//console.log(`What the api returned was ${data}`);
			if (data == 1) {
				autoListRefresh = 1;
				getAutoListRefreshInterval(function(output){
					// console.log(output);
					autoListRefreshInterval = output*1000;
					// autoListRefreshInterval = 1000;
					$("#jlr").removeClass("jlrd").addClass("jlre");
					$("#jlr").html("Auto Job List Refresh is Enabled");
					startRefreshTimer();
				});
			} else {
				autoListRefresh = 0;
				$("#jlr").html("Auto Job List Refresh is Disabled");
				$("#jlr").removeClass("jlre").addClass("jlrd");
			}
		}
	});
}

function getAutoListRefreshInterval(handleData) {
	$.ajax({
		url: "../api/v1/users/list-refresh-interval/",
		method: "GET",
		dataType: "text",
		success: function (data) {
			handleData(data);
		},
		error: function (jqxhr) {
			// $("#register_area").text(jqxhr.responseText); // @text = response error, it is will be errors: 324, 500, 404 or anythings else
		}
	});
}

function startRefreshTimer() {
	console.log("starting timer");
	var ping = setInterval(function () {

		$.get( "/api/v1/session-info", function() {
			// alert( "success" );
		})
			.done(function(response) {
				// console.log("logged in? -> " + response.logged_in)
				if(response.logged_in)
				{
					jobsDTRef.ajax.reload(dtTableReloadCallback);
					$("#tjd").html("Total Jobs Length: " + new Date(totalDur * 1000).toISOString().substr(11, 8));
					$("#cbm").html("Current Backlog Minutes: " + new Date(totalTrDur * 1000).toISOString().substr(11, 8));
				}else{
					clearInterval(ping);
					$.confirm({
						title: 'Session Expired',
						theme: 'supervan',
						content: 'Your session has expired you will be redirected to the login page',
						buttons: {
							confirm:{
								text: "Ok",
								action: function () {
									location.href = 'index.php';
								}
							}

						}
					});

				}
			})
			.fail(function(error) {

			});
	}, autoListRefreshInterval);
	// }, 1500);
	// }, 100); // stress test to force data failing to be retrieved
}

function dtTableReloadCallback() {
	// reload custom filters
	jobsDTRef.columns([1, 2, 6]).every(
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