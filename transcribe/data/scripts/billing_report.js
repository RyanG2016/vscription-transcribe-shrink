var findAccWindow;
var accountID;
let maximum_rows_per_page_jobs_list = 30;

$(document).ready(function () {

    var totalDur = 0;
    var invoiceData = [];

    var confirmDialog = $.confirm({
        icon: 'fa fa-spinner fa-spin',
        title: 'Please wait',
        draggable: false,
        backgroundDismiss: false,
        lazyOpen: true,
        content: 'Sit back, we are processing your PDF.',
        buttons: {
            /*tryAgain: {
                text: 'Try again',
                btnClass: 'btn-red',
                action: function(){
                }
            },*/
            close: {
                text: 'close',
                isHidden: true
            }
        }
    });

    const invoiceURL = '/api/v1/zoho/invoice';

    let startDate = $( "#startDate" );
    let endDate = $( "#endDate" );
    let startDatePicker = $('#startDatePicker');
    let endDatePicker = $('#endDatePicker');
    let getReport = $( "#getReport" );
    let generateInvoiceBtn = $( "#generateInvoiceBtn" );
    let reportOptions = $("#reportOptions");
    let billJobs = $("#billJobs");
    let mabJobs = $("#mabJobs");
    let totalMins = $("#totalMins");
    let totalBillMins = $("#totalBillMins");
    let BillingRate = $("#BillingRate");
    let invoiceTotal = $("#invoiceTotal");
    let jobsCount = $(".jobs-count");

    accountID = $("#accountID");
    startDatePicker.datetimepicker(
        {
            format: "YYYY-MM-DD",
            maxDate: moment()
        }
    );
    // startDatePicker.on("change.datetimepicker",function (e) {
    //     checkDates(e.date.format('YYYY-MM-DD'), true);
    // });

    startDatePicker.on("change.datetimepicker", ({date, oldDate}) => {
        if(date){
            checkDates(date.format('YYYY-MM-DD'), true)
        }
    });

    endDatePicker.datetimepicker(
        {
            format: "YYYY-MM-DD",
            maxDate: moment()
        }
    );

    // endDatePicker.on("change.datetimepicker",function (e) {
    endDatePicker.on("change.datetimepicker", ({date, oldDate}) => {
        if(date){
            checkDates(date.format('YYYY-MM-DD'), true)
        }
    });

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


    // startDate.val(moment().subtract(48, "months").format("YYYY-MM-DD"));
    startDate.val(moment().subtract(1, "months").format("YYYY-MM-DD"));
    endDate.val(moment().format("YYYY-MM-DD"));


    // data table
    let billingDT = $("#billing-tbl");
    let billingDTRef;

    let currentOrganization = ""; // for pdf header
    let currentOrgBillRate = 0;


    $.fn.dataTable.ext.errMode = 'none';

    pdfMake.fonts = {
        opensans: {
            normal: 'https://cdn.jsdelivr.net/npm/@typopro/web-open-sans@3.7.5/TypoPRO-OpenSans-Light.ttf',
            bold: 'https://cdn.jsdelivr.net/npm/@typopro/web-open-sans@3.7.5/TypoPRO-OpenSans-Light.ttf',
            italics: 'https://cdn.jsdelivr.net/npm/@typopro/web-open-sans@3.7.5/TypoPRO-OpenSans-Light.ttf',
            bolditalics: 'https://cdn.jsdelivr.net/npm/@typopro/web-open-sans@3.7.5/TypoPRO-OpenSans-Light.ttf'
        },
        Roboto: {
            normal: 'Roboto-Regular.ttf',
            bold: 'Roboto-Medium.ttf',
            italics: 'Roboto-Italic.ttf',
            bolditalics: 'Roboto-MediumItalic.ttf'
        }
    };


    billingDTRef = billingDT.DataTable( {
        rowId: 'file_id',
        // "ajax": '../api/v1/billing/1?dt&startDate=2018-07-19&endDate=2021-07-19',
        "processing": true,
        // select: true,
        searching: false,
        responsive: true,
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
                download: 'open',
                // filename: currentOrganization+'_Bill_Report_' +startDate.val().toString()+ '_to_' +endDate.val().toString(), // * is read from host title tag
                // filename: '',
                // title: 'current org testo',
                // title: currentOrganization,
                // messageTop: 'top msg',
                // messageBottom: 'bottom msg',
                titleAttr: 'PDF',
                orientation: 'landscape',
                pageSize: 'letter',
                exportOptions:{
                    // columns: [0,1,3],
                    stripHtml: true,
                    format: {
                        body: function (data, row, column, node) {
                            // Strip $ from salary column to make it numeric
                            if(column === 1 || column === 2)
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

                    buttonConfig.filename = `${currentOrganization}_Bill_Report_${startDate.val().toString()}_to_${endDate.val().toString()}`;
                    buttonConfig.title = currentOrganization;
                    buttonConfig.download = 'open';

                    pdfMakeObj.defaultStyle.font = 'opensans';
                    // pdfMakeObj.watermark =
                    //     { text: 'vScription Billing', color: '#bfced9', opacity: 0.3, bold: false, italics: true };

                    pdfMakeObj.pageSize = 'LETTER';
                    pdfMakeObj.pageOrientation = 'landscape';
                    pdfMakeObj.pageMargins = [ 20, 20 ];
                    pdfMakeObj.title = currentOrganization;
                    pdfMakeObj.header = {
                        text: 'vScription Transcribe Billing',
                        margin: [8,4,0,0]
                    };
                    // pdfMakeObj.content[1].table.widths = [ '*' ,'*','*' ,'*','*' ,'*','*' ,'*'];
                    pdfMakeObj.content[1].table.widths = [ 'auto','auto', 'auto', '*', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', '*'];
                    pdfMakeObj.footer= {
                        columns: [
                            {
                                text: `\ \ ${startDate.val()} to ${endDate.val()}`,
                                alignment: 'left',
                                margin: [8,0,0,0]
                            },
                            {
                                text: `Generated on: ${new Date().toUTCString()}\ \ `,
                                alignment: 'right',
                                margin: [0,0,8,8]
                                // margin: 8
                            }
                        ]
                    };

                    /* add summary table */
                    pdfMakeObj.content.push(
                        // [{text: 'Summary', pageBreak: 'before', style: 'subheader'},
                        {
                            // layout: 'lightHorizontalLines', // optional
                            layout: {
                                fillColor: function (rowIndex, node, columnIndex) {
                                    return (rowIndex % 2 === 0) ? '#f3f3f3': null;
                                }
                            },
                            pageBreak: 'before',
                            table: {
                                // headers are automatically repeated if the table spans over multiple pages
                                // you can declare how many rows should be treated as headers
                                headerRows: 1,
                                // widths: [ '*', 'auto', 100, '*' ],
                                widths: [ 'auto', 150],

                                body: [
                                    [{text: 'Summary', style: 'tableHeader', colSpan: 2, alignment: 'center'}, {}],
                                    [ 'Billing',        {text:($('.billing-selection-table tr:nth-child(2) td:nth-child(2)').text()).trim(), alignment: 'right'}  ],
                                    [ 'Mark as Billed', {text:($('.billing-selection-table tr:nth-child(3) td:nth-child(2)').text()).trim(), alignment: 'right'}  ],
                                    [ 'Total Minutes',  {text:($('.billing-selection-table tr:nth-child(4) td:nth-child(2)').text()).trim(), alignment: 'right'}  ],
                                    [ 'Billed Minutes', {text:($('.billing-selection-table tr:nth-child(5) td:nth-child(2)').text()).trim(), alignment: 'right'}  ],
                                    [ 'Bill Rate',      {text:($('.billing-selection-table tr:nth-child(6) td:nth-child(2)').text()).trim(), alignment: 'right'}  ],
                                    [ 'Invoice Total',  {text:($('.billing-selection-table tr:nth-child(7) td:nth-child(2)').text()).trim(), alignment: 'right'}  ]
                                ]
                            }
                        });


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
                "title":"",
                // data: "",
                data: null,
                orderable: false,
                // className: "center",
                render: function (data, type, row) {
                    return '';
                },

            },
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
                    // if (row["file_id"] != 0 && !calculatedIds.includes(row["file_id"])) {
                        // console.log(`totalDur: ${totalDur}`);
                        // totalDur += returnMinsAndSecs(parseInt(data));
                        // totalDur += parseInt(data);

                        // calculatedIds.push(row["file_id"]);
                        /*console.log(`${calculatedIds.length} | ${billingDTRef.data().length}`);
                        if(calculatedIds.length == billingDTRef.data().length)
                        {
                        }*/
                        // console.log(`${totalDur} | ${roundToNearestQMinute(parseInt(data))}`);

                        // totalMins.html(totalDur.toFixed(2));
                        // totalBillMins.html(totalDur.toFixed(2));
                    // }


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

    $('#billing-tbl > thead > tr > th:nth-child(3)').tooltip(
        {
            title: 'Mark as billed'
        }
    );

    // generateInvoiceBtn.on("click", function() {
    //     createPDFBase64();
    // });
    generateInvoiceBtn.on("click", function() {
        confirmDialog.open();

        createPDFBase64();

    });

    function requestNewInvoice(pdfBlob)
    {
        let ajaxData = billingDTRef.ajax.json();
        invoiceData.length = 0; // clear array
        invoiceData.data = [];
        invoiceData.organization = ajaxData.organization;
        invoiceData.bill_rate = ajaxData.billrate1;
        invoiceData.org_id = ajaxData.org_id;
        invoiceData.start_date = ajaxData.start_date;
        invoiceData.end_date = ajaxData.end_date;

        invoiceData.contact_type    = 'customer';
        invoiceData.quantity = parseFloat(totalBillMins.text());
        invoiceData.total    = invoiceTotal.text();


        for (let [key, value] of Object.entries(ajaxData.data) ) {

            let item = {
                file_id: value.file_id,
                mab: billingDTRef.cell(`#${value.file_id}`, 2).node().children[0].children[1].innerHTML === "Yes",
                bill: billingDTRef.cell(`#${value.file_id}`, 1).node().children[0].children[1].innerHTML === "Yes"
            };

            invoiceData.data.push(item);
        }

        // $.post("", {
        //     invoiceData:
        // });
        // $.ajax({
        //     type: "POST",
        //     url: url,
        //     data: JSON.stringify(data),
        //     contentType: "application/json; charset=utf-8",
        //     dataType: "json",
        //     error: function() {
        //         alert("Error");
        //     },
        //     success: function() {
        //         alert("OK");
        //     }
        // });

        var formData = new FormData();
        formData.append('invoiceData', JSON.stringify({...invoiceData}));
        formData.append('pdf', pdfBlob);
        formData.append('pdfName', accountID.val() + "_Bill_"+`${startDate.val().toString()}_to_${endDate.val().toString()}`);

        $.confirm({
            title: 'Signup',
            theme: 'supervan',
            columnClass: 'col-8',
            content: function(){
                var self = this;
                // self.setContent('Checking callback flow');
                return $.ajax({
                    type: 'POST',
                    method: 'POST',
                    url: invoiceURL,
                    data: formData,
                    processData: false,
                    contentType: false
                    // contentType: "application/json; charset=utf-8"
                })
                    .done(function (response) {

                        // handle responses
                        // -------------

                        // self.setTitle("Success");
                        // self.setType("green");
                        // self.setContent(response["msg"]);
                        //
                        // self.buttons.ok.setText("Ok");
                        // self.buttons.ok.addClass("btn-green");
                        // self.buttons.ok.removeClass("btn-default");
                        // self.buttons.close.hide();
                        // self.buttons.ok.action = function () {
                        //     location.reload();
                        // };
                        billingDTRef.ajax.reload(dtLoadCallback);
                        if(!response.error)
                        {
                            self.setTitle("Success");
                            self.setType("green");
                            self.setContent(response.msg);

                            self.buttons.ok.setText("Ok");
                            self.buttons.ok.addClass("btn-green");
                            self.buttons.ok.removeClass("btn-default");
                            self.buttons.close.hide();
                        }else{
                            self.setTitle("oops..");
                            self.setType("red");
                            self.setContent(response.msg);
                            self.buttons.ok.setText("Ok");
                            self.buttons.ok.addClass("btn-green");
                            // self.buttons.ok
                            // self.buttons.ok.btnClass = "btn-green"
                            self.buttons.ok.removeClass("btn-default")
                            self.buttons.close.hide();
                        }
                        // self.buttons.ok.action = function () {
                        //     location.reload();
                        // };


                        // self.setContentAppend('<div>Done!</div>');

                    }).fail(function(xhr, status, err){
                        self.setTitle("oops..");
                        self.setType("red");
                        self.setContent(xhr.responseJSON["msg"]);
                        self.buttons.ok.setText("Ok");
                        self.buttons.ok.addClass("btn-green");
                        // self.buttons.ok
                        // self.buttons.ok.btnClass = "btn-green"
                        self.buttons.ok.removeClass("btn-default");
                        self.buttons.close.hide();
                    })
            }
        });

        /*
        * $.post(invoiceURL, {
                    invoiceData: JSON.stringify({...invoiceData}),
                    pdf: pdfBlob,
                    pdf_file_name: accountID.val() + "_Bill_"+`${startDate.val().toString()}_to_${endDate.val().toString()}`
                })
                    .done(function (response) {

                        // handle responses
                        // -------------

                        // self.setTitle("Success");
                        // self.setType("green");
                        // self.setContent(response["msg"]);
                        //
                        // self.buttons.ok.setText("Ok");
                        // self.buttons.ok.addClass("btn-green");
                        // self.buttons.ok.removeClass("btn-default");
                        // self.buttons.close.hide();
                        // self.buttons.ok.action = function () {
                        //     location.reload();
                        // };

                        if(!response.error)
                        {
                            self.setTitle("Success");
                            self.setType("green");
                            self.setContent(response["msg"]);

                            self.buttons.ok.setText("Ok");
                            self.buttons.ok.addClass("btn-green");
                            self.buttons.ok.removeClass("btn-default");
                            self.buttons.close.hide();
                        }else{
                            self.setTitle("oops..");
                            self.setType("red");
                            self.setContent(response.msg);
                            self.buttons.ok.setText("Ok");
                            self.buttons.ok.addClass("btn-green");
                            // self.buttons.ok
                            // self.buttons.ok.btnClass = "btn-green"
                            self.buttons.ok.removeClass("btn-default")
                            self.buttons.close.hide();
                        }
                        // self.buttons.ok.action = function () {
                        //     location.reload();
                        // };


                        // self.setContentAppend('<div>Done!</div>');

                    }).fail(function(xhr, status, err){
                        self.setTitle("oops..");
                        self.setType("red");
                        self.setContent(xhr.responseJSON["msg"]);
                        self.buttons.ok.setText("Ok");
                        self.buttons.ok.addClass("btn-green");
                        // self.buttons.ok
                        // self.buttons.ok.btnClass = "btn-green"
                        self.buttons.ok.removeClass("btn-default")
                        self.buttons.close.hide();
                    })
        * */

        // ajax trick
        // use json_decode(file_get_contents('php://input'),1);
        /*    $.confirm({
                title: 'Signup',
                theme: 'supervan',
                columnClass: 'col-8',
                content: function(){
                    var self = this;
                    // self.setContent('Checking callback flow');
                    return $.ajax({
                        type: 'POST',
                        method: 'POST',
                        url: invoiceURL,
                        data: JSON.stringify({...invoiceData}),
                        processData: false,
                        contentType: false
                        // contentType: "application/json; charset=utf-8"
                    }).done(function (response) {

                        // handle responses
                        // -------------

                        // self.setTitle("Success");
                        // self.setType("green");
                        // self.setContent(response["msg"]);
                        //
                        // self.buttons.ok.setText("Ok");
                        // self.buttons.ok.addClass("btn-green");
                        // self.buttons.ok.removeClass("btn-default");
                        // self.buttons.close.hide();
                        // self.buttons.ok.action = function () {
                        //     location.reload();
                        // };

                        if(!response.error)
                        {
                            self.setTitle("Success");
                            self.setType("green");
                            self.setContent(response["msg"]);

                            self.buttons.ok.setText("Ok");
                            self.buttons.ok.addClass("btn-green");
                            self.buttons.ok.removeClass("btn-default");
                            self.buttons.close.hide();
                        }else{
                            self.setTitle("oops..");
                            self.setType("red");
                            self.setContent(response.msg);
                            self.buttons.ok.setText("Ok");
                            self.buttons.ok.addClass("btn-green");
                            // self.buttons.ok
                            // self.buttons.ok.btnClass = "btn-green"
                            self.buttons.ok.removeClass("btn-default")
                            self.buttons.close.hide();
                        }
                        // self.buttons.ok.action = function () {
                        //     location.reload();
                        // };


                        // self.setContentAppend('<div>Done!</div>');

                    }).fail(function(xhr, status, err){
                        self.setTitle("oops..");
                        self.setType("red");
                        self.setContent(xhr.responseJSON["msg"]);
                        self.buttons.ok.setText("Ok");
                        self.buttons.ok.addClass("btn-green");
                        // self.buttons.ok
                        // self.buttons.ok.btnClass = "btn-green"
                        self.buttons.ok.removeClass("btn-default")
                        self.buttons.close.hide();
                    })
                }
            });*/


        /*$.ajax({
            type: "POST",
            url: '/api/v1/zoho/invoice',
            data: JSON.stringify({...invoiceData}),
            // data: invoiceData,
            processData: false,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            error: function() {
                // alert("Error");
            },
            success: function() {
                // alert("OK");
            }
        });*/
        // console.log("data to record: " + invoiceData);



        // console.log("data to record: " + `{${JSON.stringify({...invoiceData})}`);
        // console.log(JSON.stringify({...invoiceData}));
    }

    function createPDFBase64(){
        var config = {
            extend: 'pdfHtml5',
            text: '<i class="fa fa-file-pdf-o"></i>',
            download: 'open',
            header: true,
            footer: false,
            filename: currentOrganization+'_Bill_Report_' +startDate.val().toString()+ '_to_' +endDate.val().toString(), // * is read from host title tag
            title: currentOrganization+'_Bill_Report_' +startDate.val().toString()+ '_to_' +endDate.val().toString(), // * is read from host title tag
            // filename: 'file name test',
            // title: 'current org testo',
            // title: currentOrganization,
            // messageTop: 'top msg',
            // messageBottom: 'bottom msg',
            titleAttr: 'PDF',
            orientation: 'landscape',
            pageSize: 'letter',
            exportOptions:{
                // columns: [0,1,3],
                stripHtml: true,
                format: {
                    body: function (data, row, column, node) {
                        // Strip $ from salary column to make it numeric
                        if(column === 1 || column === 2)
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

                buttonConfig.filename = `${currentOrganization}_Bill_Report_${startDate.val().toString()}_to_${endDate.val().toString()}`;
                buttonConfig.title = currentOrganization;
                buttonConfig.download = 'open';

                pdfMakeObj.defaultStyle.font = 'opensans';
                // pdfMakeObj.watermark =
                //     { text: 'vScription Billing', color: '#bfced9', opacity: 0.3, bold: false, italics: true };

                pdfMakeObj.pageSize = 'LETTER';
                pdfMakeObj.pageOrientation = 'landscape';
                pdfMakeObj.pageMargins = [ 20, 20 ];
                pdfMakeObj.title = currentOrganization;
                pdfMakeObj.header = {
                    text: 'vScription Transcribe Billing',
                    margin: [8,4,0,0]
                };
                // pdfMakeObj.content[1].table.widths = [ '*' ,'*','*' ,'*','*' ,'*','*' ,'*'];
                // pdfMakeObj.content[0].table.widths = [ 'auto','auto', 'auto', '*', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', '*'];
                pdfMakeObj.content[1].table.widths = [ 'auto','auto', 'auto', '*', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', '*'];
                pdfMakeObj.footer= {
                    columns: [
                        {
                            text: `\ \ ${startDate.val()} to ${endDate.val()}`,
                            alignment: 'left',
                            margin: [8,0,0,0]
                        },
                        {
                            text: `Generated on: ${new Date().toUTCString()}\ \ `,
                            alignment: 'right',
                            margin: [0,0,8,8]
                            // margin: 8
                        }
                    ]
                };
                var debug = true;

            }
        };
        var data = billingDTRef.buttons.exportData( config.exportOptions);
        var info = billingDTRef.buttons.exportInfo( config );
        var rows = [];

        if ( config.header ) {
            rows.push( $.map( data.header, function ( d ) {
                return {
                    text: typeof d === 'string' ? d : d+'',
                    style: 'tableHeader'
                };
            } ) );
        }

        for ( var i=0, ien=data.body.length ; i<ien ; i++ ) {
            rows.push( $.map( data.body[i], function ( d ) {
                if ( d === null || d === undefined ) {
                    d = '';
                }
                return {
                    text: typeof d === 'string' ? d : d+'',
                    style: i % 2 ? 'tableBodyEven' : 'tableBodyOdd'
                };
            } ) );
        }

        if ( config.footer && data.footer) {
            rows.push( $.map( data.footer, function ( d ) {
                return {
                    text: typeof d === 'string' ? d : d+'',
                    style: 'tableFooter'
                };
            } ) );
        }

        var doc = {
            pageSize: config.pageSize,
            pageOrientation: config.orientation,
            content: [
                {
                    table: {
                        headerRows: 1,
                        body: rows
                    },
                    layout: 'noBorders'
                }
            ],
            styles: {
                tableHeader: {
                    bold: true,
                    fontSize: 11,
                    color: 'white',
                    fillColor: '#2d4154',
                    alignment: 'center'
                },
                tableBodyEven: {},
                tableBodyOdd: {
                    fillColor: '#f3f3f3'
                },
                tableFooter: {
                    bold: true,
                    fontSize: 11,
                    color: 'white',
                    fillColor: '#2d4154'
                },
                title: {
                    alignment: 'center',
                    fontSize: 15
                },
                message: {}
            },
            defaultStyle: {
                fontSize: 10
            }
        };

        if ( info.messageTop ) {
            doc.content.unshift( {
                text: info.messageTop,
                style: 'message',
                margin: [ 0, 0, 0, 12 ]
            } );
        }

        if ( info.messageBottom ) {
            doc.content.push( {
                text: info.messageBottom,
                style: 'message',
                margin: [ 0, 0, 0, 12 ]
            } );
        }

        if ( info.title ) {
            doc.content.unshift( {
                text: info.title,
                style: 'title',
                margin: [ 0, 0, 0, 12 ]
            } );
        }

        // customizations
        /* ============================ */
        if ( config.customize ) {
            config.customize( doc, config, billingDT );
        }
        /* ============================ */


        // add summary table
        /* ============================ */
        doc.content.push(
            // [{text: 'Summary', pageBreak: 'before', style: 'subheader'},
            {
            // layout: 'lightHorizontalLines', // optional
            layout: {
                fillColor: function (rowIndex, node, columnIndex) {
                    return (rowIndex % 2 === 0) ? '#f3f3f3': null;
                }
            },
            pageBreak: 'before',
            table: {
                // headers are automatically repeated if the table spans over multiple pages
                // you can declare how many rows should be treated as headers
                headerRows: 1,
                // widths: [ '*', 'auto', 100, '*' ],
                widths: [ 'auto', 150],

                body: [
                    [{text: 'Summary', style: 'tableHeader', colSpan: 2, alignment: 'center'}, {}],
                    [ 'Billing',        {text:($('.billing-selection-table tr:nth-child(2) td:nth-child(2)').text()).trim(), alignment: 'right'}  ],
                    [ 'Mark as Billed', {text:($('.billing-selection-table tr:nth-child(3) td:nth-child(2)').text()).trim(), alignment: 'right'}  ],
                    [ 'Total Minutes',  {text:($('.billing-selection-table tr:nth-child(4) td:nth-child(2)').text()).trim(), alignment: 'right'}  ],
                    [ 'Billed Minutes', {text:($('.billing-selection-table tr:nth-child(5) td:nth-child(2)').text()).trim(), alignment: 'right'}  ],
                    [ 'Bill Rate',      {text:($('.billing-selection-table tr:nth-child(6) td:nth-child(2)').text()).trim(), alignment: 'right'}  ],
                    [ 'Invoice Total',  {text:($('.billing-selection-table tr:nth-child(7) td:nth-child(2)').text()).trim(), alignment: 'right'}  ]
                ]
            }
        });

        /* ============================ */

        var pdf = pdfMake.createPdf( doc );

        // if ( config.download === 'open' && ! _isDuffSafari() ) {
        /*pdf.getBase64((data) => {
            console.log(data);
            // send and save to server then fetch URL

        });*/
        pdf.getBlob((blob) => {
            // console.log(blob);
            confirmDialog.close();
            requestNewInvoice(blob);
        });

        // pdf.open();
        // }
        // else {
        //     pdf.download( info.filename );
        // }
    }

    getReport.on("click", function() {


        let reqData = new URLSearchParams({
            start_date: startDate.val()+' 00:00:00',
            end_date: endDate.val()+' 23:59:59'
        }).toString();
        document.title = "Bill_report_"+startDate.val()+"_to_" + endDate.val();

        // billingDTRef.ajax.url( '../api/v1/billing/1?dt&startDate=2018-07-19&endDate=2021-07-19').load();
        billingDTRef.ajax.url(`../api/v1/billing/${accountID.val()}?dt&${reqData}`).load(dtLoadCallback);
        // getData(arg);
    });

    // getReport.click();

    function dtLoadCallback(responseJson)
    {
        // console.log("callback");
        // clear total durations
        // calculatedIds.length = 0;
        totalDur = 0;
        // console.log(responseJson);
        if(responseJson.count)
        { 
            currentOrganization = responseJson.organization;
            currentOrgBillRate = fix2(responseJson.billrate1);
            BillingRate.html(currentOrgBillRate);
            jobsCount.each(function(){
                this.innerHTML = responseJson.count;
            });
            mabJobs.html(responseJson.count);
            billJobs.html(responseJson.count);

            // calculate total minutes
            var data = billingDTRef.data().toArray();
            for (const key in data) {

                totalDur += getMinsFloat(data[key]['audio_length']);
            }

            totalMins.html(fix2(totalDur));
            totalBillMins.html(fix2(totalDur));

            // total minutes calculation
            calcInvoiceTotal();

            // document.title = `${currentOrganization.replaceAll(" ", "_")}_Bill_Report_${startDate.val().toString()}_to_${endDate.val().toString()}`;

            reportOptions.slideDown();

            // generateInvoiceBtn.click();
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
            let audioValue = getMinsFloat(billingDTRef.data()[$(this).closest('tr').index()].audio_length);
            if(this.checked)
            {
                billJobs.html(parseInt(billJobs.html()) + 1)
                $(this).parent().children()[1].innerHTML = 'Yes';
                totalBillMins.html(fix2(parseFloat(totalBillMins.html()) + audioValue));
            }else{
                billJobs.html(parseInt(billJobs.html()) - 1)
                $(this).parent().children()[1].innerHTML = 'No';
                totalBillMins.html(fix2(parseFloat(totalBillMins.html()) - audioValue));
            }
            calcInvoiceTotal();
        });
    });


    let args = new URLSearchParams(
        {
            col:3,
            row:'acc_id',
            data:'ID,acc_id,Account,acc_name,Prefix,job_prefix',
            response:'acc_id',
            url:'accounts'
        }
    ).toString();
    $("#findAccBtn").on("click", function () {

        if(!findAccWindow || findAccWindow.closed)
        {
            findAccWindow = window.open(`/finder.php?${args}`
                , "modalPicker", "toolbar=yes,scrollbars=yes," +
                "resizable=yes,top=500,left=500,width=650,height=550");
            findAccWindow.focus();
        }else{
            findAccWindow.focus();
        }
    });

    function calcInvoiceTotal()
    {
        if(currentOrgBillRate !== 0)
        {
            // invoiceTotal.html(round((currentOrgBillRate * parseFloat(totalBillMins.html()) * 100)) / 100);
            invoiceTotal.html(fix2(parseFloat(totalBillMins.html()) * currentOrgBillRate));

        }else{
            invoiceTotal.html('');
        }
    }

    // function roundToNearestQMinute(seconds)
    // {
    //     let minutes = Math.floor(seconds / 60);
    //     let remainder = seconds % 60;

    //     if(remainder <= 15)
    //     {
    //         minutes += 0.25;
    //     }else if(remainder <= 30)
    //     {
    //         minutes += 0.5;
    //     }else if (remainder <= 45)
    //     {
    //         minutes += 0.75;
    //     }else{
    //         minutes ++;
    //     }

    //     return minutes;
    // }

    /*function returnMinsAndSecs(seconds)
    {
        let minutes = Math.floor(seconds / 60);
        let remainder = seconds % 60;
        let remainderSeconds = remainder / 100;
        minutes += remainderSeconds;
        return minutes;
    }*/

    function fix2(num) {
        return num.toFixed(2);
        // return Math.round(num, 2).toFixed(2);
    }

    function getMinsFloat(numStr)
    {
        return parseFloat(fix2(parseFloat(numStr)/60));
    }

    function fix2Float(num)
    {
        return parseFloat(fix2(num));
    }

    function getMinsStr(num)
    {
        return getMinsFloat(num).toString();
    }

});

function popResponse(response)
{
    accountID[0].value = response;
    findAccWindow = null;
}
