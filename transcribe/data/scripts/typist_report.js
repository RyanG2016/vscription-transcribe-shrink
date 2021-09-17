var findAccWindow;
var typistEm;
let maximum_rows_per_page_jobs_list = 10;

$(document).ready(function () {

    // let today = new Date();
    // today.setDate(today.getDate() - 30);
    // today = today.toISOString().split('T')[0];
    // let today = (new Date('2001-08-18')).toISOString().split('T')[0];

    // let tomorrow = new Date();
    // tomorrow.setDate(tomorrow.getDate() + 1);
    // tomorrow = tomorrow.toISOString().split('T')[0];

    let startDate = $( "#startDate" );
    let endDate = $( "#endDate" );
    let startDatePicker = $('#startDatePicker');
    let endDatePicker = $('#endDatePicker');

    let getReport = $( "#getReport" );
    typistEm = $("#typistEmail");
    let typistEl = $ ( "#demo_job_type");

    let reportOptions = $("#reportOptions");
    let generatedOn = $("#genOn");
    let totalLengthField = $("#totalLength");
    let totalPayableField = $("#totalPayable");
    //let accountEl = $ ("#account");

    startDatePicker.datetimepicker(
        {
            format: "YYYY-MM-DD",
            maxDate: moment(),
            defaultDate: moment().subtract(0.5, 'months')
        }
    );
    startDatePicker.on("change.datetimepicker",function (e) {
        checkDates(e.date.format('YYYY-MM-DD'), true);
    });

    endDatePicker.datetimepicker(  {
            format: "YYYY-MM-DD",
            maxDate: moment(),
            defaultDate: moment()
        }
    );
    endDatePicker.on("change.datetimepicker",function (e) {
        checkDates(e.date.format('YYYY-MM-DD'), true);
    });

    startDate.val(moment().subtract(15, "days").format("YYYY-MM-DD"));
    endDate.val(moment().format("YYYY-MM-DD"));

    // typistContainer.html();
    // typistContainer.append(generateLoadingSpinner());
    // typistContainer.appendChild(generateLoadingSpinner());

    // data table
    let typistDT = $("#typistTbl");
    let typistDTRef;

    $.fn.dataTable.ext.errMode = 'none';

    pdfMake.fonts = {
        opensans: {
            normal: 'https://cdn.jsdelivr.net/npm/@typopro/web-open-sans@3.7.5/TypoPRO-OpenSans-Light.ttf',
            bold: 'https://cdn.jsdelivr.net/npm/@typopro/web-open-sans@3.7.5/TypoPRO-OpenSans-Light.ttf',
            italics: 'https://cdn.jsdelivr.net/npm/@typopro/web-open-sans@3.7.5/TypoPRO-OpenSans-Light.ttf',
            bolditalics: 'https://cdn.jsdelivr.net/npm/@typopro/web-open-sans@3.7.5/TypoPRO-OpenSans-Light.ttf'
        }
    };



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
        //console.log("Account: " + accountEl.val());
        // console.log("Typist: " + $("#typistContainer option:selected").val());
        let email = $("#typistEmail").val();
        if(/^[a-z0-9_]+(?:\.[a-z0-9_]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/.test(email))
        {
            let args = new URLSearchParams({
                start_date: startDate.val(),
                end_date: endDate.val(),
                typist_email: email
            }).toString();
            // document.title = "Typist_Bill_report_"+startDate.val()+"_to_" + endDate.val();

            typistDTRef.ajax.url(`../api/v1/billing/typist/?dt&${args}`).load(dtLoadCallback);
        }else{
            $.confirm({
                title: 'Error',
                theme: 'supervan',
                content: "Please enter a valid email address",
                buttons: {
                    confirm: {
                        text: 'ok'
                    }
                }
            });
        }



    });

    function dtLoadCallback(responseJson)
    {
        if(responseJson.count)
        {
            generatedOn.html(responseJson.generated_on);
            totalLengthField.html(responseJson.total_minutes);
            totalPayableField.html(responseJson.total_payable);
            reportOptions.slideDown();

        }else{
            // currentOrganization = "";
            // currentOrgBillRate = 0;
            // BillingRate.html('');
            reportOptions.slideUp();
        }

    }


    // getTypistsSelect();

    // function getTypistsSelect() {
    //     $.get("../api/v1/users/typists/?dt").done(
    //         function (res) {
    //             let response = JSON.parse(res);
    //             let data = response.data;
    //             let no_result = response.no_result;
    //             // let err = response.error;
    //             typistContainer.html(data);
    //
    //             if(no_result){
    //                 getReport.attr("disabled", "disabled");
    //             }
    //             else{
    //                 getReport.removeAttr("disabled");
    //             }
    //         }
    //     );
    //
    //     $.getJSON( "../api/v1/users/typists/?dt", function(data) {
    //     })
    //         .done(function(response) {
    //             if(response.count)
    //             {
    //                 typistContainer.html(response.data);
    //             }
    //         })
    //         .fail(function() {
    //         });
    //
    // }
    //


    /*
    *
    *let arg = {
            startDate: startDate.val(),
            endDate: endDate.val(),
            typist: $("#typistContainer option:selected").val() -> typist email in API: typist_email
        };
        document.title = "Typist_Bill_report_"+startDate.val()+"_to_" + endDate.val();
    * */

    typistDTRef = typistDT.DataTable( {
        rowId: 'file_id',
        // "ajax": '../api/v1/billing/typist/1?dt&startDate=2018-07-19&endDate=2021-07-19',
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
                download: 'open',
                // filename: currentOrganization+'_Bill_Report_' +startDate.val().toString()+ '_to_' +endDate.val().toString(), // * is read from host title tag
                filename: 'file name test',
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
                            // if(column === 0 || column === 1)
                            // {
                            //     return node.children[0].children[1].innerHTML;
                            // }
                            // else return data;
                            return data;
                        }
                    }
                    // orthogonal: 'export',
                },

                customize: function (pdfMakeObj, buttonConfig, tblRef) {
                    // let test = 1;
                    $currentTypist = $("#typistContainer option:selected").val();
                    buttonConfig.filename = `${$currentTypist}_Bill_Report_${startDate.val().toString()}_to_${endDate.val().toString()}`;
                    buttonConfig.title = $currentTypist;
                    buttonConfig.download = 'open';

                    pdfMakeObj.defaultStyle.font = 'opensans';
                    pdfMakeObj.watermark =
                        { text: 'vScription Billing', color: '#bfced9', opacity: 0.3, bold: false, italics: true };

                    pdfMakeObj.pageSize = 'LETTER';
                    pdfMakeObj.pageOrientation = 'landscape';
                    pdfMakeObj.pageMargins = [ 20, 20 ];
                    pdfMakeObj.title = $currentTypist;
                    pdfMakeObj.header = {
                        text: 'vScription Typist Billing',
                        margin: [8,4,0,0]
                    };
                    // pdfMakeObj.content[1].table.widths = [ '*' ,'*','*' ,'*','*' ,'*','*' ,'*'];
                    // pdfMakeObj.content[1].table.widths = [ '*', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', '*'];
                    pdfMakeObj.content[1].table.widths = [ 'auto', 'auto', 'auto', '*', 'auto', '*', '*', 'auto', 'auto', 'auto'];
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
                "title": "Job Number",
                className: "center",
                "data": "job_id"
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
                "title": "Date Dictated",
                "data": "file_date_dict"
            },
            {
                "title": "Audio Length",
                "data": "audio_length",
                render: function (data, type, row) {
                    return new Date(data * 1000).toISOString().substr(11, 8);
                }
            },
            {
                "title": "Date Transcribed",
                "data": "file_transcribed_date"
            },
            {
                "title": "Organization",
                "data": "acc_name"
            },
            {
                "title": "Bill Rate",
                "data": "bill_rate1_min_pay",
                render: function (data) {
                    return "x" + data;
                }
            },
            {
                "title": "Bill",
                "data": "bill",
                render: function (data) {
                    return "$" + data;
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



    typistDTRef.buttons().container()
        .appendTo( $('#vtexTableTools'));


    typistDTRef.on( 'error.dt', function ( e, settings, techNote, message ) {
        // console.log( 'An error has been reported by DataTables: ', message );
        dtLoadCallback();
        // console.log( 'Failed to retrieve data' );
    } );


    // <label for="typist">Typist</label><select id="typist" class="typist-select"><option value="ryangaudet@me.com">Ryan G</option><option value="bonnielhudacek@gmail.com">Bonnie H</option></select>

    function generateLoadingSpinner() {

        // Generate a loading spinner //
        //<div class="spinner">
        //  <div class="bounce1"></div>
        //  <div class="bounce2"></div>
        //  <div class="bounce3"></div>
        //</div>

        const spinnerDiv = document.createElement("div");
        spinnerDiv.setAttribute("class", "spinner");
        const bounce1 = document.createElement("div");
        const bounce2 = document.createElement("div");
        const bounce3 = document.createElement("div");
        bounce1.setAttribute("class", 'bounce1');
        bounce2.setAttribute("class", 'bounce2');
        bounce3.setAttribute("class", 'bounce3');

        spinnerDiv.appendChild(bounce1);
        spinnerDiv.appendChild(bounce2);
        spinnerDiv.appendChild(bounce3);

        return spinnerDiv;
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

    let args = new URLSearchParams(
        {
            col:4,
            row:'id',
            data:'ID,id,Name,name,Email,email,Jobs(All Time),jobs',
            response:'email',
            url:'users/typists'
        }
    ).toString();

    $("#findTypistBtn").on("click", function () {

        if(!findAccWindow || findAccWindow.closed)
        {
            findAccWindow = window.open(`/finder.php?${args}`, "modalPicker", "toolbar=yes,scrollbars=yes," +
                "resizable=yes,top=500,left=500,width=650,height=500");
            findAccWindow.focus();
        }else{
            findAccWindow.focus();
        }
    });


}); // ready end

function popResponse(response)
{
    typistEm[0].value = response;
    findAccWindow = null;
}
