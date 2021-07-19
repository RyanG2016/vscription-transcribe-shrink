var findAccWindow;
var accountID;
let maximum_rows_per_page_jobs_list = 7;

$(document).ready(function () {

    let today = new Date().toISOString().split('T')[0];
    let tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow = tomorrow.toISOString().split('T')[0];

    let startDate = $( "#startDate" );
    let endDate = $( "#endDate" );
    let getReport = $( "#getReport" );
    let getPDF = $ ( "#getPDF" );
    let getPrintJS = $ ( "#getPrint" );
    let reportOptions = $("#reportOptions");
    let htmlTable = $('.billing-report-container');
    accountID = $("#accountID");
    $('#startDatePicker').datetimepicker({format: "YYYY-MM-DD"});
    $('#endDatePicker').datetimepicker({format: "YYYY-MM-DD"});

    // data table
    let billingDT = $("#billing-tbl");
    let billingDTRef;

    startDate.val(today);
    endDate.val(tomorrow);

    $.fn.dataTable.ext.errMode = 'none';

    billingDTRef = billingDT.DataTable( {
        rowId: 'file_id',
        // "ajax": '../api/v1/billing/1?dt&startDate=2018-07-19&endDate=2021-07-19',
        "processing": true,
        select: true,
        lengthChange: false,
        pageLength: maximum_rows_per_page_jobs_list,
        autoWidth: false,
        order:[[0,"desc"]],
        // dom: 'Blfrtip',
        // buttons: [
        //     'copy', 'excel', 'pdf', 'pdfHtml5', 'print'
        // ],

        "columns": [
            {
                "title": "Job Number",
                "data": "job_id",
             /*   render: function (data, type, row) {

                    var addition = "";

                    let fields = ["user_field_1", "user_field_2", "user_field_3", "typist_comments"];
                    /!* Additional Popup *!/
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
                    if(addition !== "")
                    {
                        // addition = '<i class=\"fas fa-info-circle custom-info-font-awesome btTooltip float-right\" data-toggle="tooltip" data-html="true"  title="'+addition+'"></i>';
                        // addition = '<i class=\"fad fa-info-square custom-info-font-awesome btTooltip float-right\" data-toggle="tooltip" data-html="true"  title="'+addition+'"></i>';
                        // addition = '<i class=\"fas fa-info-circle custom-info-font-awesome cTooltip float-right\" data-html="true"  title="'+addition+'"></i>';
                        addition = '<i class=\"fad fa-info-square custom-info-font-awesome cTooltip float-right\" data-html="true"  title="'+addition+'"></i>';
                    }

                    if (row["file_comment"] != null) {

                        return data + " <i class=\"material-icons mdc-button__icon job-comment cTooltip\" aria-hidden=\"true\" title='"
                            + htmlEncodeStr(row["file_comment"])
                            + "'>speaker_notes</i>" +
                            addition;
                    } else {
                        return data  + addition;
                    }
                }*/
            },
            {
                "title": "Author",
                "data": "file_author"
            },
            {
                "title": "Job Type",
                "data": "file_work_type"
            },
            {
                "title": "Date Uploaded",
                "data": "job_upload_date"
            },
            {
                "title": "Date Dictated",
                "data": "file_date_dict"
            },
            {
                "title": "Date Transcribed",
                "data": "file_transcribed_date"
            },
            {
                "title": "Audio Length",
                "data": "audio_length"
            },
            {
                "title": "Comments",
                "data": "file_comment"
            }
        ],

        /*initComplete: function () {

            calculatedIds = []; // freeing resources
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

        }*/

    } );

    billingDT.on( 'error.dt', function ( e, settings, techNote, message ) {
        // console.log( 'An error has been reported by DataTables: ', message );
        dtLoadCallback();
        // console.log( 'Failed to retrieve data' );
    } );

    // get data ↓


    function checkDates(val, startDateGiven) {
        if(startDateGiven){

            let otherDate = endDate.val();
            if(val > otherDate){
                endDate.val(val);
            }

        }else{ // val = current end date
            let otherDate = startDate.val();
            if(val < otherDate)
            {
                startDate.val(val);
            }
        }
    }


    startDate.on("change paste keyup", function() {
        checkDates($(this).val(), true);
    });


    endDate.on("change paste keyup", function() {
        checkDates($(this).val(), false);
    });

    getReport.on("click", function() {


        let reqData = new URLSearchParams({
            startDate: startDate.val(),
            endDate: endDate.val()
        }).toString();
        document.title = "Bill_report_"+startDate.val()+"_to_" + endDate.val();

        // billingDTRef.ajax.url( '../api/v1/billing/1?dt&startDate=2018-07-19&endDate=2021-07-19').load();
        billingDTRef.ajax.url(`../api/v1/billing/${accountID.val()}?dt&${reqData}`).load(dtLoadCallback);
        // getData(arg);
    });

    function dtLoadCallback()
    {
        if(billingDTRef.data().count())
        {
            reportOptions.slideDown();
        }else{
            reportOptions.slideUp();
        }
    }

    getPDF.on("click", function() {
        var opt = {
            margin: 7,
            filename: "Bill_report_"+startDate.val()+"_to_" + endDate.val()+".pdf",
            image: {type: 'jpeg', quality: 0.98 },
            html2canvas: {scale: 2},
            jsPDF: {unit: 'mm', format: 'letter', orientation: 'landscape'}
        }
        html2pdf($('.billing-report-container').html(), opt);
    });

    getPrintJS.on("click", function() {
        // var opt = {
        //     margin: 7,
        //     filename: 'bill_report.pdf',
        //     image: {type: 'jpeg', quality: 0.98 },
        //     html2canvas: {scale: 2},
        //     jsPDF: {unit: 'mm', format: 'letter', orientation: 'landscape'}
        // }

        // printJS('printableReport', 'html');
        // printJS({printable: 'printableReport', type: 'html', properties: ['prop1', 'prop2', 'prop3']});
        printJS({
            // printable: 'printableReport',
            printable: 'billing-tbl',
            type: 'html',
            showModal: true,
            scanStyles: true,
            // css: "../data/css/billing_print.css",
            css: "https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.25/b-1.7.1/b-html5-1.7.1/b-print-1.7.1/sb-1.1.0/sp-1.3.0/sl-1.3.3/datatables.min.css",
            style: '@page { size: Letter landscape; }'
        });
        // html2pdf($('.billing-report-container').html(), opt);
    });


    function getData(args) {
        $.post("/data/parts/backend_request.php", {
            reqcode: 200
            ,args: JSON.stringify(args)
        }).done(function (res) {
            let response = JSON.parse(res);
            let data = response.data;
            // let error = res.error;
            $('.billing-report-container').html(data);
            if(data !== "No Results Found"){
                getPDF.removeAttr("disabled");
                getPrintJS.removeAttr("disabled");
            }
            else{
                getPDF.attr("disabled", "disabled");
                getPrintJS.attr("disabled", "disabled");
            }
        });
    }

    $("#findAccBtn").on("click", function () {

        if(!findAccWindow || findAccWindow.closed)
        {
            findAccWindow = window.open("/acc_finder.php", "modalPicker", "toolbar=yes,scrollbars=yes," +
                "resizable=yes,top=500,left=500,width=650,height=500");
            findAccWindow.focus();
        }else{
            findAccWindow.focus();
        }
    });

});

function setAccID(accID)
{
    accountID[0].value = accID;
    findAccWindow = null;
}