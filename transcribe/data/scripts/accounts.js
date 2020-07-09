(function ($) {
	"use strict";
})(jQuery);

var accountsDT;
var accountsDTRef;

$(document).ready(function () {

	const maximum_rows_per_page_jobs_list = 10;

	// $('.tooltip').tooltipster();

	accountsDT = $("#accounts-tbl");

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

	const refreshBtn = document.querySelector('#refresh_btn');
	// const goToUploader = document.querySelector('#newupload_btn');

	// Activate ripples effect for material buttons
	new mdc.ripple.MDCRipple(document.querySelector('#refresh_btn'));
	// new mdc.ripple.MDCRipple(document.querySelector('#newupload_btn'));

	/*goToUploader.addEventListener('click', e => {
		console.log("We should be going to the uploader page");
		document.location.href = 'jobupload.php';
	});*/

	accountsDTRef = accountsDT.DataTable( {
		rowId: 'file_id',
		"ajax": '../api/v1/accounts?dt',
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
		/*
		* 				<th>ID</th>
                        <th>Name</th>
                        <th>Prefix</th>
                        <th>Date Created</th>
                        <th>Retention</th>
                        <th>Log Retention</th>
                        <th>Enabled</th>
                        <th>Billable</th>
		* */
		"columns": [
			{ "data": "acc_id"},
			{ "data": "acc_name" },
			{ "data": "job_prefix" },
			{ "data": "acc_creation_date" },
			{ "data": "acc_retention_time" },
			{ "data": "act_log_retention_time" },
			{ "data": "enabled",
				render: function (data) {
					if(data == true)
					{
						return "<i class=\"fas fa-check-circle vtex-status-icon\"></i>";
					}else{
						return "<i class=\"fas fa-times-circle vtex-status-icon\"></i>";
					}
				}
			},
			{ "data": "billable",
				render: function (data) {
					if(data == true)
					{
						return "<i class=\"fas fa-check-circle vtex-status-icon\"></i>";
					}else{
						return "<i class=\"fas fa-times-circle vtex-status-icon\"></i>";
					}
				}
			}
		]
	} );

	refreshBtn.addEventListener('click', e => {
		accountsDTRef.ajax.reload();
	});


});


function htmlEncodeStr(s)
{
	return s.replace(/&/g, "&amp;")
		.replace(/>/g, "&gt;")
		.replace(/</g, "&lt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&lsquo;");
}