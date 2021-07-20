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
    $.fn.dataTable.ext.errMode = 'none';
    jobsDTRef = jobsDT.DataTable( {
        rowId: 'file_id',
        "ajax": 'api/v1/files/pending?dt',
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
                    var addition = "";
                    var result = "";

                    let fields = ["user_field_1", "user_field_2", "user_field_3", "typist_comments"];
                    /* Additional Popup */
                    fields.forEach(value => {
                        if(row[value] !== null && row[value] !== "")
                        {
                            if(addition !== "")
                            {
                                addition += "<br><br>";
                                // addition += "\n";
                            }
                            addition += `<b>${value}</b>: ${row[value]}`;
                        }
                    });
                    if (row["file_comment"] != null) {

                        result = `<i class="fas fa-comment-alt-lines vspt-fa-blue cTooltip" data-html="true"  title="${htmlEncodeStr(row["file_comment"])}"></i>`;
                    }
                    if(addition !== "")
                    {
                        result += `&nbsp;<i class="fas fa-info-square vspt-fa-blue cTooltip" data-html="true"  title="${addition}"></i>`;
                    }
                    if(result)
                    {
                        result = `<span class="align-middle float-right">${result}</span>`
                    }
                    return data + result;
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

    jobsDT.on( 'error.dt', function ( e, settings, techNote, message ) {
        // console.log( 'An error has been reported by DataTables: ', message );
        console.log( 'Failed to retrieve data' );
    } )

    jobsDT.on( 'draw.dt', function () {

            $('.download-icon').click(function() {
                let file_id = $(this).parent().parent().attr('id');
                download(file_id);
            });

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