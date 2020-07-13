jQuery(document).on('change', '.custom-file-input', function () {
    let fileName = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
    $(this).parent('.custom-file').find('.custom-file-label').text(fileName);
});

var $collectionHolder;

var $addPictureFormButton = $('<button type="button" class="btn btn-info addPictureFormLink">Ajouter une image</button>');
var $newLinkLi = $('<li></li>').append($addPictureFormButton);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of todos
    $collectionHolder = $('ul.pictureForm');

    // add the "add a todo" anchor and li to the todos ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addPictureFormButton.on('click', function(e) {
        // add a new todo form (see next code block)
        addTodoForm($collectionHolder, $newLinkLi);
    });
});

function addTodoForm($collectionHolder, $newLinkLi) {
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

    // Display the form in the page in an li, before the "Add a task" link li
    var $newFormLi = $('<li></li>').append(newForm);

     // add a delete link to the new form
     addTodoFormDeleteLink($newFormLi);

     $newLinkLi.before($newFormLi);
}

function addTodoFormDeleteLink($todoFormLi) {
    var $removeFormButton = $('<button type="button" class="btn btn-danger">Supprimer cette image</button>');
    $todoFormLi.append($removeFormButton);

    $removeFormButton.on('click', function(e) {
        // remove the li for the todo form
        $todoFormLi.remove();
    });
}