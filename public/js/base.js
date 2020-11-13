function confirmDeleteTrick(trickId) {
    $("#delete_trick_modal").modal();
    $("#delete_trick_button").attr("onclick", "deleteTrick(" + trickId + ")");
}

function deleteTrick(trickId) {
    document.location.href = "/suppression_trick/" + trickId;
}