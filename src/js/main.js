$(document).ready(function () {


    $("a.btn-navigate").click(function () {
        $("html, body").animate({
            scrollTop: $($(this).attr("href")).offset().top - 50 + "px"
        }, {
            duration: 500,
            easing: "swing"
        });
        return false;
    });


});
