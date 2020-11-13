var offset = 0;
var trickId = $('#trickID').html();

// var isBtnMoreTricksDisplayed = false;

function loadMoreComments(trickId, offset) {
    $.ajax({
        url: "/load_more_comments/" + trickId + "/" + offset, // La ressource ciblée
        type: 'GET', // Le type de la requête HTTP.
        success: function (html) {
            $('#test').append(html);
            // alert('youpi');
        }
    });
}

jQuery(document).ready(function () {
    $("#loadMoreComments").on("click", function (e) {
        offset += 10;
        loadMoreComments(trickId, offset);
    });

    $("#action_see_medias").on("click", function (e) {
        $("#see_medias").removeClass("d-md-none").addClass("d-none");
        $("#trickMedias").removeClass("d-none d-md-block");
    });
});