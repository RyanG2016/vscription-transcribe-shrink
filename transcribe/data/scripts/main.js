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
			this.api().columns().every( function () {
				var column = this;
				var select = $('<select><option value=""></option></select>')
					.appendTo( $(column.footer()).empty() )
					.on( 'change', function () {
						var val = $.fn.dataTable.util.escapeRegex(
							$(this).val()
						);

						column
							.search( val ? '^'+val+'$' : '', true, false )
							.draw();
					} );

				column.data().unique().sort().each( function ( d, j ) {
					select.append( '<option value="'+d+'">'+d+'</option>' )
				} );
			} );
		}

		
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
			$("#cbm").html("Current Backlog Minutes: " + new Date(totalDur * 1000).toISOString().substr(11, 8));
		}
	);





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