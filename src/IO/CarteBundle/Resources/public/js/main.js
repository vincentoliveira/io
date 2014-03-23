/* 
 * Main.js
 */

    
var nextNumber = 0;

function addDishToOrderForm(dish) {
    var prototype = $('ul.orderLines').attr('data-prototype');
    var newForm = prototype.replace(/__name__/g, nextNumber);
    var $newFormLi = $('<li></li>').append(newForm).append($(dish).attr('data-name'));
    addTagFormDeleteLink($newFormLi);

    $newFormLi.find('#order_orderLines_' + nextNumber + '_dish').val($(dish).attr('data-id'))
    $newFormLi.find('#order_orderLines_' + nextNumber + '_price').val($(dish).attr('data-price'))

    $('ul.orderLines').append($newFormLi);

    nextNumber = nextNumber + 1;
}

function addTagFormDeleteLink($tagFormLi) {
    var $deleteBtn = $("<a class=\"btn btn-danger btn-trans\"><i class=\"fa fa-trash-o\"</i> </a>");
    $tagFormLi.prepend($deleteBtn);

    $deleteBtn.on('click', function(e) {
        // empêche le lien de créer un « # » dans l'URL
        e.preventDefault();

        // supprime l'élément li pour le formulaire de tag
        $tagFormLi.remove();
    });
}
        
$(document).ready(function() {
    $('.btn-delete').click(function(e) {
        var message = "Cette action est immédiate en non-réversible. Voulez-vous continuer ?";
        if (confirm(message)) {
            return true;
        } else {
            e.preventDefault();
            return false;
        }
    });

    // Order
    nextNumber = $('ul.orderLines').children().length;
    $('ul.orderLines').append();
    $('ul.orderLines').find('li').each(function() {
        addTagFormDeleteLink($(this));
    });

    $('.btn-dish').on('click', function(e) {
        e.preventDefault();
        addDishToOrderForm(this);
    });
});
