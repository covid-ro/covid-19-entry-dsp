window.addEventListener('load', function() {

    // auto remove alert after 5sec
    setTimeout(function () {
        if ($('.alert').is(':visible')){
            $('.alert').fadeOut();
        }
    }, 7000)

});
