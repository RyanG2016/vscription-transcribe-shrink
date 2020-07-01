$(document).ready(function () {
    getTransJobList();
});

function getTransJobList() {
    var maximum_rows_per_page_jobs_list = 7;

    // var maximum_rows_per_page_jobs_list = 7;
    var jobListResult = $(".jobs_tbl"); //populating fields

    $.post("data/parts/backend_request.php", {
        reqcode: 9
    }).done(function (res) {
        var response = JSON.parse(res);
        var data = response.data;
        var error = response.error;

        if(error){
            jobListResult.html(response.data);
            return true;
        }

        jobListResult.html(response.data);

        new mdc.dataTable.MDCDataTable(document.querySelector(".mdc-data-table"));
        dataTbl = $(".jobs_tbl");
        dataTbl.on( "init.dt", function () {
            if(!$(".cTooltip").hasClass("tooltipstered"))
            {
                $(".cTooltip").tooltipster({
                    animation: "grow",
                    theme: "tooltipster-punk",
                    arrow: true
                });
            }
        } );
        dataTbl = dataTbl.DataTable(
            {
                lengthChange: false,
                searching: false,
                lengthMenu: false,
                pageLength: maximum_rows_per_page_jobs_list,
                destroy: true
                /*"columnDefs": [{
                    "targets": [0],
                    "visible": true,
                    "searchable": false,
                    "orderable": false
                }]*/
            }
        );
        // this function below fires after new page is compvarely drawn
        dataTbl.on("draw", function () {
            if (!$(".cTooltip").hasClass("tooltipstered")) {
                $(".cTooltip").tooltipster({
                    animation: "grow",
                    theme: "tooltipster-punk",
                    arrow: true
                });
            }
        });

        setTimeout(function() {
            addRowHandlers(response.error);
        }, 1000);
    });

}
function addRowHandlers(error) {

    if (!error) {
        var table = $(".jobs_tbl").DataTable();

        $(".jobs_tbl tbody").on("click", "tr", function () {
            var fileID = table.row(this).id();
            postToParent(fileID);
        });
    }

}

function postToParent(id)
{
    window.opener.loadID(id);
}