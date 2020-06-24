
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
    let htmlTable = $('.billing-report-container');
    startDate.datepicker({dateFormat: "yy-mm-dd"});
    endDate.datepicker({dateFormat: "yy-mm-dd"});
    
    startDate.val(today);
    endDate.val(tomorrow);


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
        let arg = {
            startDate: startDate.val(),
            endDate: endDate.val()
        };
        getData(arg);
    });

    getPDF.on("click", function() {
        var opt = {
            margin: 7,
            filename: 'bill_report.pdf',
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
            printable: 'printableReport',
            type: 'html',
            showModal: true,
            scanStyles: true,
            css: "../data/css/billing_print.css",
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
        });
    }



});