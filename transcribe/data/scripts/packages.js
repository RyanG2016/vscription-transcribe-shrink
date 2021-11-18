$(document).ready(function () {

    $(".package-card-bottom").on("click", function() {
        $("#package").attr('value', $(this).attr("id"));
        $("#purchase").submit();
    });

});