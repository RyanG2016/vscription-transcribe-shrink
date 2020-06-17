

$(document).ready(function () {

    // new mdc.textfield.MDCTextField(document.querySelector('.mdc-text-field'));
    new mdc.ripple.MDCRipple(document.querySelector('#hashPwd'));
    let tf = new mdc.textField.MDCTextField(document.querySelector('.mdc-text-field'));


    $('#hashPwd').on('click', function () {

        let arg = {
            pwd: tf.value
        };

        $.post("../data/parts/backend_request.php", {
            reqcode: 66,
            args: JSON.stringify(arg)
        }).done(function (data) {
            tf.value = data;
        });

    });


});
