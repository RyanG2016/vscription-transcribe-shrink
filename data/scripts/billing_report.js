
$(document).ready(function () {

    let today = new Date().toISOString().split('T')[0];

    // let arg = {
    //     pwd: tf.value
    // };
    let startDate = $( "#startDate" );
    let endDate = $( "#endDate" );
    startDate.datepicker({dateFormat: "yy-mm-dd"});
    endDate.datepicker({dateFormat: "yy-mm-dd"});
    
    startDate.val(today);
    endDate.val(today);


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
    })


    function getData() {
        $.post("/data/parts/backend_request.php", {
            reqcode: 200
            // ,args: JSON.stringify(arg)
        }).done(function (res) {
            let response = JSON.parse(res);
            let data = response.data;
            // let error = res.error;
            $('.billing-report-container').append(data);
        });
    }

    getData();



});