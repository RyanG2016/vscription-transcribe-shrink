var jobsDT;
var jobsDTRef;
let maximum_rows_per_page_jobs_list = 7;

$(document).ready(function () {

    jobsDT = $("#jobs-tbl");

    $('#jobs-tbl tfoot th').each( function () {
        // var title = $(this).text();
        // $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
        $(this).html( '<input class="dt-search" type="text"/>' );
    } );


    jobsDTRef = jobsDT.DataTable( {
        rowId: 'acc_id',
        "ajax": 'api/v1/accounts/?dt',
        // "ajax": 'api/v1/files?dt&file_status[mul]=0,1,2',
        "processing": true,
        // searching: false,
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
            { "data": "acc_id" },
            { "data": "acc_name" },
            { "data": "job_prefix" }
        ],
        initComplete: function () {
            this.api().columns().every( function () {
                var that = this;
                $( 'input', this.footer() ).on( 'keyup change clear', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
        }
    } );

    $('#jobs-tbl tbody').on('click', 'tr', function () {
        let accID = jobsDTRef.row(this).id();
        postToParent(accID);
    } );


});

function postToParent(id)
{
    window.opener.setAccID(id);
    this.close();
}

function htmlEncodeStr(s)
{
    return s.replace(/&/g, "&amp;")
        .replace(/>/g, "&gt;")
        .replace(/</g, "&lt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&lsquo;");
}