// var nbrTricksLoaded = 5;
var limit = 5;
var offset = 0;

function loadMoreTricks(limit, offset) {
    $.get("/tricks/" + limit + "/" + offset, function (data) {
        $("#arrow").hide();
        alert(limit);
        alert(offset);
        if(limit == 5)
        {
            $("html").append(data);
            
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
        limit += 5;
        offset += 5;
        alert(limit);
        alert(offset);
    });

    // $("#loadMoreTricks").on("click", function (e) {
    //     loadMoreTricks(limit, offset);
    //     limit += nbrTricksLoaded;
    //     offset += nbrTricksLoaded;
    // });

    // Add smooth scrolling on links
    // $("#navbarSupportedContent #slideToTricksLink, #arrow a, #arrow2 a").on("click", function(event) {

    //     // Make sure this.hash has a value before overriding default behavior
    //     if (this.hash !== "") {
    //         event.preventDefault(); // Prevent default anchor click behavior
    //         var hash = this.hash; // Store hash

    //         // smooth page autoscroll taking specified milliseconds to scroll to the specified area
    //         $("html, body").animate({
    //         scrollTop: $(hash).offset().top
    //         }, 800, function(){
    //         window.location.hash = hash; // Add hash (#) to URL when done scrolling (default click behavior)
    //         });
    //     }
    // });
});