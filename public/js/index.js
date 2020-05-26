const nbrTricksLoaded = 5;
var limit = 5;
var offset = 0;

function loadMoreTricks(limit, offset) {
    $.get("/" + limit + "/" + offset, function (data) {
        $("div#listTricks").append(data);
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

    $("#loadMoreTricks").on("click", function (e) {
        limit += nbrTricksLoaded;
        offset += nbrTricksLoaded;
        loadMoreTricks(limit, offset);
    });
});