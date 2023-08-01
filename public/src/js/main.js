

$(document).ready(function (){
    $('.search-icon').click(function (){
        $('.searchbox').slideToggle();
    })
});

$(document).ready(function (){
    $('#hamberger').click(function (){
        $('.navigation').slideToggle();
    })
    $('.navigation ul.sub-menu').before("<i class='sub-menu-arrow fa fa-angle-left'></i> ");


    $( ".navigation .sub-menu-arrow" ).click(function() {
        if($(this).hasClass("fa-angle-left")) {
            $(this).next("ul.sub-menu").slideToggle();
            $(this).removeClass("fa-angle-left").addClass("fa-angle-down");
        }
        else {
            $(this).next("ul.sub-menu").hide(500);
            $(this).removeClass("fa-angle-down").addClass("fa-angle-left");
        }

    });
});