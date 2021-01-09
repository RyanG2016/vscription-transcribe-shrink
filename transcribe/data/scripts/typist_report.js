
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
    let typistContainer = $("#typistContainer");
    let typistEl = $ ( "#demo_job_type");
    //let accountEl = $ ("#account");
    let htmlTable = $('.billing-report-container');
    $('#startDatePicker').datetimepicker({format: "YYYY-MM-DD"});
    $('#endDatePicker').datetimepicker({format: "YYYY-MM-DD"});
    // startDate.datepicker({dateFormat: "yy-mm-dd"});
    // endDate.datepicker({dateFormat: "yy-mm-dd"});

    typistContainer.html();
    typistContainer.append(generateLoadingSpinner());
    // typistContainer.appendChild(generateLoadingSpinner());
    console.log(generateLoadingSpinner());
    
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

    getTypistsSelect();


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

    function getTypistsSelect() {
        $.post("/data/parts/backend_request.php", {
            reqcode: 202
        }).done(function (res) {
            let response = JSON.parse(res);
            let data = response.data;
            let no_result = response.no_result;
            // let err = response.error;
            typistContainer.html(data);

            if(no_result){
                getReport.attr("disabled", "disabled");
            }
            else{
                getReport.removeAttr("disabled");
            }
        });
    }
    
    
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

});