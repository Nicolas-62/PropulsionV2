// front/js/home.js
console.log('this is the home.js file');

// Page d'accueil ?
// $(window).on( "load resize", function( event ){
//     var o = $("#offcanvasControl").offset();
//     console.log(o);
//     $("#offcanvasNav").offset( { top:o.top + 20 } );
// });

// ???
$(".prog").hover(
    function(){
        $(this).children(".card-img-top").addClass("opacity-50");
        $(this).children(".overlay").css( "top" , $(this).width()/2 - 33 + "px" );
        $(this).children(".overlay").show()
    },
    function(){
        $(this).children(".card-img-top").removeClass("opacity-50");
        $(this).children(".overlay").hide();
    }
)