var findAccWindow;
var accountID;
let maximum_rows_per_page_jobs_list = 7;

$(document).ready(function () {

    var calculatedIds = [];
    var totalDur = 0;

    // let today = new Date().toISOString().split('T')[0];
    let today = (new Date('2001-08-18')).toISOString().split('T')[0];
    let tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow = tomorrow.toISOString().split('T')[0];

    let startDate = $( "#startDate" );
    let endDate = $( "#endDate" );
    let getReport = $( "#getReport" );
    let getPDF = $ ( "#getPDF" );
    let getPrintJS = $ ( "#getPrint" );
    let reportOptions = $("#reportOptions");
    let billJobs = $("#billJobs");
    let mabJobs = $("#mabJobs");
    let totalMins = $("#totalMins");
    let totalBillMins = $("#totalBillMins");
    let BillingRate = $("#BillingRate");
    let invoiceTotal = $("#invoiceTotal");

    let jobsCount = $(".jobs-count");

    // let htmlTable = $('.billing-report-container');
    accountID = $("#accountID");
    $('#startDatePicker').datetimepicker({format: "YYYY-MM-DD"});
    $('#endDatePicker').datetimepicker({format: "YYYY-MM-DD"});

    // data table
    let billingDT = $("#billing-tbl");
    let billingDTRef;

    let currentOrganization = ""; // for pdf header
    let currentOrgBillRate = 0;

    startDate.val(today);
    endDate.val(tomorrow);

    $.fn.dataTable.ext.errMode = 'none';

    pdfMake.fonts = {
        opensans: {
            normal: 'https://cdn.jsdelivr.net/npm/@typopro/web-open-sans@3.7.5/TypoPRO-OpenSans-Light.ttf',
            bold: 'https://cdn.jsdelivr.net/npm/@typopro/web-open-sans@3.7.5/TypoPRO-OpenSans-Light.ttf',
            italics: 'https://cdn.jsdelivr.net/npm/@typopro/web-open-sans@3.7.5/TypoPRO-OpenSans-Light.ttf',
            bolditalics: 'https://cdn.jsdelivr.net/npm/@typopro/web-open-sans@3.7.5/TypoPRO-OpenSans-Light.ttf'
        }
    };


    billingDTRef = billingDT.DataTable( {
        rowId: 'file_id',
        // "ajax": '../api/v1/billing/1?dt&startDate=2018-07-19&endDate=2021-07-19',
        "processing": true,
        // select: true,
        searching: false,
        lengthChange: false,
        pageLength: maximum_rows_per_page_jobs_list,
        autoWidth: false,
        order:[[0,"desc"]],
        // dom: 'Blfrtip',
        buttons: [
            // 'copy', 'excel', 'pdfHtml5', 'print'
            {
                extend:    'copyHtml5',
                text:      '<i class="fa fa-files-o"></i>',
                titleAttr: 'Copy'
            },
            {
                extend:    'excelHtml5',
                text:      '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel'
            },
            {
                extend:    'csvHtml5',
                text:      '<i class="fa fa-file-text-o"></i>',
                titleAttr: 'CSV'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf-o"></i>',
                titleAttr: 'PDF',
                download: 'open',
                // download: 'download',
                orientation: 'landscape',
                pageSize: 'letter',
                exportOptions:{
                    // columns: [0,1,3],
                    stripHtml: true,
                    format: {
                        body: function (data, row, column, node) {
                            // Strip $ from salary column to make it numeric
                            if(column === 0 || column === 1)
                            {
                                return node.children[0].children[1].innerHTML;
                            }
                            else return data;
                            // return data;
                        }
                    }
                    // orthogonal: 'export',
                },

                customize: function (pdfMakeObj, buttonConfig, tblRef) {
                    // let test = 1;

                    pdfMakeObj.defaultStyle.font = 'opensans';
                    pdfMakeObj.watermark =
                        { text: 'vScription Billing', color: '#bfced9', opacity: 0.3, bold: false, italics: true };

                    pdfMakeObj.pageSize = 'LETTER';
                    pdfMakeObj.pageOrientation = 'landscape';
                    pdfMakeObj.pageMargins = [ 20, 20 ];
                    pdfMakeObj.title = `testo`;
                    pdfMakeObj.header = `vScription Transcribe Billing`;
                    pdfMakeObj.footer= {
                        columns: [
                            `${startDate.val()} to ${endDate.val()}`,
                            {
                                text: `Generated on: ${new Date().toUTCString()}`,
                                alignment: 'right'
                                // margin: 8
                            }
                        ]
                    };

                    // pdfMakeObj.watermark.text = "vScription Billing";
                    // pdfMakeObj.watermark.opacity = '0.3';
                    // pdfMakeObj.watermark.italics = 'true';
                    // pdfMakeObj.content.table= {widths: [ '*' ,'*','*' ,'*','*' ,'*','*' ,'*']};

                    /*pdfMakeObj.content.columns = [
                    {
                        // auto-sized columns have their widths based on their content
                        width: 'auto',
                        text: 'First column'
                    },
                    {
                        // star-sized columns fill the remaining space
                        // if there's more than one star-column, available width is divided equally
                        width: '*',
                        text: 'Second column'
                    },
                    {
                        // fixed width
                        width: 100,
                        text: 'Third column'
                    },
                    {
                        // % width
                        width: '20%',
                        text: 'Fourth column'
                    },
                    {
                        // auto-sized columns have their widths based on their content
                        width: 'auto',
                        text: 'First column'
                    },
                    {
                        // star-sized columns fill the remaining space
                        // if there's more than one star-column, available width is divided equally
                        width: '*',
                        text: 'Second column'
                    },
                    {
                        // fixed width
                        width: 100,
                        text: 'Third column'
                    },
                    {
                        // % width
                        width: '20%',
                        text: 'Fourth column'
                    }
                ];*/


                }
            },
            'colvis'
        ],

        "columns": [
            {
                "title":"Bill",
                // data: "✓",
                // data: null,
                className: "center",
                // render: function (data, type, row) {
                //     return `<div>
                //                     <input type="checkbox" class="include-chk" checked>
                //                     <label>Yes</label>
                //                   </div>`;
                // },
                defaultContent: `<div>
                                    <input type="checkbox" class="include-chk" checked>
                                    <label>Yes</label>
                                  </div>`
            },
            {
                "title":"MAB",
                // data: null,
                // data: "✗",
                className: "center",
                "autoWidth": true,
                // render: function (data, type, row) {
                //     `<div>
                //         <input class="mark-as-billed-chk" type="checkbox" checked>
                //         <label>Yes</label>
                //     </div>`
                // }
                defaultContent: `<div>
                                    <input class="mark-as-billed-chk" type="checkbox" checked>
                                    <label>Yes</label>
                                </div>`
            },
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
                "data": "audio_length",
                render: function (data, type, row) {
                    if (row["file_id"] != 0 && !calculatedIds.includes(row["file_id"])) {
                        totalDur += roundToNearestQMinute(parseInt(data));

                        calculatedIds.push(row["file_id"]);
                        /*console.log(`${calculatedIds.length} | ${billingDTRef.data().length}`);
                        if(calculatedIds.length == billingDTRef.data().length)
                        {
                        }*/
                        // console.log(`${totalDur} | ${roundToNearestQMinute(parseInt(data))}`);
                        totalMins.html(totalDur);
                        totalBillMins.html(totalDur);
                    }


                    return new Date(data * 1000).toISOString().substr(11, 8);
                }
            },
            {
                "title": "Comments",
                "data": "file_comment"
            }
        ],
        // initComplete: function () {
        //     console.log(calculatedIds);
        //     console.log(data);
            // totalMins.html(totalDur);
        // }
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



    billingDTRef.buttons().container()
        .appendTo( $('#vtexTableTools'));


    billingDT.on( 'error.dt', function ( e, settings, techNote, message ) {
        // console.log( 'An error has been reported by DataTables: ', message );
        dtLoadCallback();
        // console.log( 'Failed to retrieve data' );
    } );

    // get data ↓

    $('#billing-tbl > thead > tr > th:nth-child(2)').tooltip(
        {
            title: 'Mark as billed'
        }
    );

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
            start_date: startDate.val(),
            end_date: endDate.val()
        }).toString();
        document.title = "Bill_report_"+startDate.val()+"_to_" + endDate.val();

        // billingDTRef.ajax.url( '../api/v1/billing/1?dt&startDate=2018-07-19&endDate=2021-07-19').load();
        billingDTRef.ajax.url(`../api/v1/billing/${accountID.val()}?dt&${reqData}`).load(dtLoadCallback);
        // getData(arg);
    });

    getReport.click();


    function dtLoadCallback(responseJson)
    {
        // console.log("callback");
        // clear total durations
        calculatedIds.length = 0;
        totalDur = 0;
        // console.log(responseJson);
        if(responseJson.count)
        {
            currentOrganization = responseJson.organization;
            currentOrgBillRate = responseJson.billrate1;
            BillingRate.html(currentOrgBillRate);
            jobsCount.each(function(){
                this.innerHTML = responseJson.count;
            });
            mabJobs.html(responseJson.count);
            billJobs.html(responseJson.count);

            // total minutes calculation
            calcInvoiceTotal();



            reportOptions.slideDown();
        }else{
            currentOrganization = "";
            currentOrgBillRate = 0;
            BillingRate.html('');
            reportOptions.slideUp();
        }
    }

    billingDT.on( 'draw.dt', function () {
        $('input[type=checkbox].mark-as-billed-chk').off('change').on('change', function () {
            // console.log("checked: " + this.checked);
            if(this.checked)
            {
                mabJobs.html(parseInt(mabJobs.html()) + 1)
                $(this).parent().children()[1].innerHTML = 'Yes';
            }else{
                mabJobs.html(parseInt(mabJobs.html()) - 1)
                $(this).parent().children()[1].innerHTML = 'No';
            }
        });

        $('input[type=checkbox].include-chk').off('change').on('change', function () {
            // console.log("checked: " + this.checked);
            if(this.checked)
            {
                billJobs.html(parseInt(billJobs.html()) + 1)
                $(this).parent().children()[1].innerHTML = 'Yes';
            }else{
                billJobs.html(parseInt(billJobs.html()) - 1)
                $(this).parent().children()[1].innerHTML = 'No';
            }
        });


    });

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

    function calcInvoiceTotal()
    {
        if(currentOrgBillRate !== 0)
        {
            invoiceTotal.html(currentOrgBillRate * parseFloat(totalBillMins.html()));
        }else{
            invoiceTotal.html('');
        }
    }

    function roundToNearestQMinute(seconds)
    {
        let minutes = Math.floor(seconds / 60);
        let remainder = seconds % 60;

        if(remainder <= 15)
        {
            minutes += 0.25;
        }else if(remainder <= 30)
        {
            minutes += 0.5;
        }else if (remainder <= 45)
        {
            minutes += 0.75;
        }else{
            minutes ++;
        }

        return minutes;
    }

});

function setAccID(accID)
{
    accountID[0].value = accID;
    findAccWindow = null;
}
