var limit = 10;
var offset = 0;
var isBtnMoreTricksDisplayed = false;

function loadMoreTricks(limit, offset) {
    $.get("/tricks/" + limit + "/" + offset, function (data) {
        $("#arrow").hide();
        if (limit == 10)//click on arrow
        {
            $("#main").append(data);
            // smooth page autoscroll taking specified milliseconds to scroll to the specified area
            $("html, body").animate({
                scrollTop: $("#main").offset().top
            }, 800, 'swing', function () {
                // Add hash (#) to URL when done scrolling (default click behavior)
            });

            $("#loadMoreTricks").show();
        }
        else {
            $("div#listTricks").append(data);
        }
    });
}

jQuery(document).ready(function () {

    $("#loadFirstTricks, #loadMoreTricks").on("click", function (e) {
        loadMoreTricks(limit, offset);
        limit += 10;
        offset += 10;
    });

    $(window).on("load", function () {
        if (isBtnMoreTricksDisplayed === false) {
            $("#loadMoreTricks").hide();
        }
        else {
            $("#loadMoreTricks").show();
        }
    });
});