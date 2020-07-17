jQuery(document).on('change', '.custom-file-input', function () {
    let fileName = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
    $(this).parent('.custom-file').find('.custom-file-label').text(fileName);
});

var $collectionHolderPicture;
var $collectionHolderVideo;

var $addPictureFormButton = $('<button type="button" class="btn btn-info addPictureFormLink">Ajouter une image</button>');
var $newPictureLinkLi = $('<li></li>').append($addPictureFormButton);
var $addVideoFormButton = $('<button type="button" class="btn btn-info addVideoFormLink">Ajouter une vidéo</button>');
var $newVideoLinkLi = $('<li></li>').append($addVideoFormButton);

jQuery(document).ready(function () {
    // Get the ul that holds the collection of pictureForm
    $collectionHolderPicture = $('ul.pictureForm');
    $collectionHolderVideo = $('ul.videoForm');

    // add the "add a picture" button and li to the pictureForm ul
    $collectionHolderPicture.append($newPictureLinkLi);
    $collectionHolderVideo.append($newVideoLinkLi);

    // count the current form inputs we have, use that as the new
    // index when inserting a new item
    $collectionHolderPicture.data('index', $collectionHolderPicture.find(':input').length);
    $collectionHolderVideo.data('index', $collectionHolderVideo.find(':input').length);

    $addPictureFormButton.on('click', function (e) {
        // add a new pictureform (see next code block)
        addPictureForm($collectionHolderPicture, $newPictureLinkLi);
    });

    $addVideoFormButton.on('click', function (e) {
        // add a new pictureform (see next code block)
        addPictureForm($collectionHolderVideo, $newVideoLinkLi);
    });

    var $checkboxChecked;
    $('.essai').each(function () {
        alert('coucou');
        if ($(this).is(':checked')) {
            alert('coucou');
            $checkboxChecked = $(this);
        }
        $('.essai').each(function () {
            if ($(this) != $checkboxChecked) {
                alert('coucou');
                $(this).prop('checked', false);
            }
        })
    });
});

function addPictureForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your taskss field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a picture" link li
    // var $newFormLi = $('<li></li>').append(newForm);

    // add a delete link to the new form
    var $newForm = $(newForm);
    $newLinkLi.before($newForm);
    addTodoFormDeleteLink($newForm);
}

function addTodoFormDeleteLink($formPicture) {
    // var $removeFormButton = $('<button type="button" class="btn btn-danger">Supprimer cette image</button>');
    // var $removeFormButton = $('<button type="button" class="deleteTrickPicture"><i class="fas fa-backspace fa-2x"></i></button>');
    // $formPicture.append($removeFormButton);
    // var $formPictureGroup = $formPicture.children('div').children('div');
    // $formPictureGroup.append($removeFormButton);
    var $removeFormButton = $formPicture.find("button");

    $removeFormButton.on('click', function (e) {
        // remove the li for the todo form
        $formPicture.remove();
    });
}