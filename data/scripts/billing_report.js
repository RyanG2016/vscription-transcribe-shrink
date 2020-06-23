
$(document).ready(function () {

    // let arg = {
    //     pwd: tf.value
    // };

    $( "#startDate" ).datepicker();
    $( "#endDate" ).datepicker();

    $.post("/data/parts/backend_request.php", {
        reqcode: 200
        // ,args: JSON.stringify(arg)
    }).done(function (res) {
        let response = JSON.parse(res);
        // console.log("response received " + res);
        let data = response.data;
        console.log("response received " + data);
        // let error = res.error;
        $('.billing-report-container').append(data);
    });

});