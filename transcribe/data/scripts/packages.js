$(document).ready(function () {

    $(".package-card-bottom").on("click", function() {
        $("#package").attr('value', 9);
        $("#purchase").submit();
    });

});