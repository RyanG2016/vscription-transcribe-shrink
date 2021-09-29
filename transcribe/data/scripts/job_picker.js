var jobsDT;
var jobsDTRef;
let maximum_rows_per_page_jobs_list = 7;

$(document).ready(function () {

    jobsDT = $("#jobs-tbl");
    var showingCompleted = false;


    jobsDT.on( 'init.dt', function () {
        if(!$('.cTooltip').hasClass("tooltipstered"))
        {
            $('.download-icon').click(function() {
                let file_id = $(this).parent().parent().attr('id');
                download(file_id);
            });

            $('.cTooltip').tooltipster({
                animation: 'grow',
                theme: 'tooltipster-punk',
                arrow: true
            });
        }
    } );
    $.fn.dataTable.ext.errMode = 'none';
    jobsDTRef = jobsDT.DataTable( {
        rowId: 'file_id',
        "ajax": 'api/v1/files/pending?dt',
        "processing": true,
        lengthChange: false,
        responsive: true,
        pageLength: maximum_rows_per_page_jobs_list,
        autoWidth: false,
        /*columnDefs: [
            {
                targets: ['_all'],
                className: 'mdc-data-table__cell'
            }
        ],*/
        "columns": [
            {   "title": "Job #",
                 "data": "job_id",
                "class":"vspt-except",
                render: function (data, type, row) {
                    var result = data;

                    if (row["file_comment"] != null) {
                        result += "<sup>●</sup>" ;
                        // result = `<i class="fas fa-comment-alt-lines vspt-fa-blue cTooltip" data-html="true"  title="${htmlEncodeStr(row["file_comment"])}"></i>`;
                    }

                    if(((new Date() - new Date(row.job_upload_date)) / (1000 * 60 * 60 * 24)) < 1)
                    {
                        result += "&nbsp;<span class=\"badge badge-success\">New</span>";
                    }

                    return result;
                }
            },
            {                "title": "Author",
                "data": "file_author" },
            {                 "title": "Job Type",
                "data": "file_work_type" },
            {                 "title": "Date Dictated",
                "data": "file_date_dict" },
            {                 "title": "Date Uploaded",
                "data": "job_upload_date" },
            {                 "title": "Job Status",
                "data": "file_status_ref" },
            {                 "title": "Job Length",
                "data": "audio_length",
                render: function (data) {
                    return new Date(data * 1000).toISOString().substr(11, 8);
                }
            },
            {
                "title": "File Comments",
                "className":"none",
                "data": "file_comment"
            }
        ]
    } );

    jobsDT.on( 'error.dt', function ( e, settings, techNote, message ) {
        // console.log( 'An error has been reported by DataTables: ', message );
        console.log( 'Failed to retrieve data' );
    } )

    jobsDT.on( 'draw.dt', function () {

            $('.download-icon').click(function() {
                let file_id = $(this).parent().parent().attr('id');
                download(file_id);
            });

        $('sup').tooltip(
            {
                title:'Job has comments, please review'
            }
        );

            if(!$('.cTooltip').hasClass("tooltipstered"))
            {
                $('.cTooltip').tooltipster({
                    animation: 'grow',
                    side: ['bottom', 'right'],
                    theme: 'tooltipster-punk',
                    contentAsHTML: true,
                    arrow: true
                });
            }
        }
    );

    $('#jobs-tbl tbody').on('click', 'tr', function (event) {

        if(!$(event.target).hasClass("vspt-except"))
        {
            let fileID = jobsDTRef.row(this).id();
            let jpFileStatus = jobsDTRef.row(this).data()["file_status"];
            postToParent(fileID, jpFileStatus,0);
        }

    } );

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


});

function postToParent(id, ps, alj)
{
    window.opener.loadID(id, ps, alj);
}

function htmlEncodeStr(s)
{
    return s.replace(/&/g, "&amp;")
        .replace(/>/g, "&gt;")
        .replace(/</g, "&lt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&lsquo;");
}