
$(document).ready(function() {
    $('.loader').delay(1000).fadeOut(function() {

    });
    setTimeout(function downCase() {
        $('.email-input').val($('.email-input').val().toLowerCase());
        console.log('down');
        setTimeout(downCase, 500);
    }, 500);
});
