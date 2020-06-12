var limit = 10;
var offset = 0;
var isBtnMoreTricksDisplayed = false;

function loadMoreTricks(limit, offset) {
    $.get("/tricks/" + limit + "/" + offset, function (data) {
        $("#arrow").hide();        
        if(limit == 10)//click on arrow
        {
            $("#main").append(data);
            // smooth page autoscroll taking specified milliseconds to scroll to the specified area
            $("html, body").animate({
                scrollTop: $("#main").offset().top
                }, 800, 'swing', function(){
                    // Add hash (#) to URL when done scrolling (default click behavior)
                });

            $("#loadMoreTricks").show();
        }
        else
        {
            $("div#listTricks").append(data);            
        }
        //var nbrTricks = $("div#content-index-tricks").children().length;
        /* the arrow appearance if more than 15 tricks loaded is too much,
           after 8 is better to preview with 10 initial tricks */
        // if (nbrTricks > 8 && isArrow2displayed === false) {
        //     $("#arrow2").show();
        //     isArrow2displayed = true;
        //}
    });
}

jQuery(document).ready(function () {

    $("#loadFirstTricks, #loadMoreTricks").on("click", function (e) {
        loadMoreTricks(limit, offset);
        limit += 10;
        offset += 10;        
    });

    $( window ).on( "load", function() {
        if (isBtnMoreTricksDisplayed === false){
            $("#loadMoreTricks").hide();
        }
        else
        {
            $("#loadMoreTricks").show();
        }
    });
});