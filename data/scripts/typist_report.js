
$(document).ready(function () {

    new mdc.ripple.MDCRipple(document.querySelector('#getPrint'));
    new mdc.ripple.MDCRipple(document.querySelector('#getPDF'));
    new mdc.ripple.MDCRipple(document.querySelector('#getReport'));
    let today = new Date().toISOString().split('T')[0];
    let tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow = tomorrow.toISOString().split('T')[0];

    let startDate = $( "#startDate" );
    let endDate = $( "#endDate" );
    let getReport = $( "#getReport" );
    let getPDF = $ ( "#getPDF" );
    let getPrintJS = $ ( "#getPrint" );
    let typistEl = $ ( "#demo_job_type");
    //let accountEl = $ ("#account");
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
        //console.log("Account: " + accountEl.val());
        console.log("Typist: " + $("#typist option:selected").val());
        let arg = {
            startDate: startDate.val(),
            endDate: endDate.val(),
            typist: $("#typist option:selected").val()
        };
        document.title = "Typist_Bill_report_"+startDate.val()+"_to_" + endDate.val();
        getData(arg);
    });

    getPDF.on("click", function() {
        var opt = {
            margin: 7,
            filename: "Typist_Bill_report_"+startDate.val()+"_to_" + endDate.val()+".pdf",
            image: {type: 'jpeg', quality: 0.98 },
            html2canvas: {scale: 2},
            jsPDF: {unit: 'mm', format: 'letter', orientation: 'landscape'}
        }
        html2pdf($('.billing-report-container').html(), opt);
    });

    getPrintJS.on("click", function() {
        printJS({
            printable: 'printableReport',
            type: 'html',
            showModal: true,
            scanStyles: true,
            css: "../data/css/billing_print.css",
            style: '@page { size: Letter landscape; }'
        });
    });


    function getData(args) {
        $.post("/data/parts/backend_request.php", {
            reqcode: 201
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



});