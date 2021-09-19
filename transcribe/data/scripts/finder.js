var jobsDT;
var jobsDTRef;
let maximum_rows_per_page_jobs_list = 10;

$(document).ready(function () {

    jobsDT = $("#jobs-tbl");

    $('#jobs-tbl tfoot th').each( function () {
        // var title = $(this).text();
        // $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
        $(this).html( '<input class="dt-search" type="text"/>' );
    } );


    let getParameters = new URLSearchParams(this.location.search);

    if(
        !getParameters.has("col") ||
        !getParameters.has("data") ||
        !getParameters.has("url") ||
        !getParameters.has("row") ||
        !getParameters.has("response")
    )
    {
        alert("Data missing, exiting");
        this.close();
    }

    let responseKey = getParameters.get("response");
    let columnsArrData = getParameters.get("data").split(',');
    let url = getParameters.get("url");
    let rowID = getParameters.get("row");
    let columnsDT = [];
    for (let i = 0; i < columnsArrData.length; i += 2) {
        columnsDT.push({"title": columnsArrData[i], "data": columnsArrData[i+1]});
    }

    jobsDTRef = jobsDT.DataTable( {
        rowId: rowID,
        "ajax": `api/v1/${url}/?dt`,
        // "ajax": 'api/v1/files?dt&file_status[mul]=0,1,2',
        "processing": true,
        // searching: false,
        lengthChange: false,
        pageLength: maximum_rows_per_page_jobs_list,
        autoWidth: false,

        // "columns": [
        //     { "title" : "ID",
        //         "data": "acc_id" },
        //     {"title" : "Account", "data": "acc_name" },
        //     { "title" : "Prefix", "data": "job_prefix" }
        // ],
        "columns": columnsDT,
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
        // let accID = jobsDTRef.row(this).id();
        postToParent(jobsDTRef.row(this).data()[responseKey]);
    } );


});

function postToParent(response)
{
    window.opener.popResponse(response);
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