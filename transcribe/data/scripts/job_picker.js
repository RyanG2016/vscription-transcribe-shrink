var jobsDT;
var jobsDTRef;
let maximum_rows_per_page_jobs_list = 7;

$(document).ready(function () {

    jobsDT = $("#jobs-tbl");

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

    jobsDTRef = jobsDT.DataTable( {
        rowId: 'file_id',
        "ajax": 'api/v1/files?dt&file_status[mul]=0,1,2,7,11',
        "processing": true,
        lengthChange: false,
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
                render: function (data, type, row) {
                    if (row["file_comment"] != null) {

                        /*return data + " <i class=\"material-icons mdc-button__icon job-comment cTooltip\" aria-hidden=\"true\" title='"
                            + htmlEncodeStr(row["file_comment"])
                            + "'>speaker_notes</i>";*/

                        return data +
                            ' <i class="fas fa-comment-dots job-comment cTooltip" title="'+ htmlEncodeStr(row["file_comment"]) +'"></i>';
                    } else {
                        return data;
                    }
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
            }
        ]
    } );

    jobsDT.on( 'draw.dt', function () {

            $('.download-icon').click(function() {
                let file_id = $(this).parent().parent().attr('id');
                download(file_id);
            });

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

    $('#jobs-tbl tbody').on('click', 'tr', function () {
        let fileID = jobsDTRef.row(this).id();
        postToParent(fileID);
    } );


});

function postToParent(id)
{
    window.opener.loadID(id);
}

function htmlEncodeStr(s)
{
    return s.replace(/&/g, "&amp;")
        .replace(/>/g, "&gt;")
        .replace(/</g, "&lt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&lsquo;");
}