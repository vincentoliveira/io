/* 
 * Main.js
 */

    
var nextNumber = 0;
var $deleteBtn = "";//"<a class=\"btn btn-danger btn-delete-dish\">x</a>"

function addDishToOrderForm(dish)
{
    var prototype = $('ul.orderLines').attr('data-prototype');
    var newForm = prototype.replace(/__name__/g, nextNumber);
    var $newFormLi = $('<li></li>').append(newForm).append($(dish).attr('data-name')).append($deleteBtn);

    $newFormLi.find('#order_orderLines_' + nextNumber + '_dish').val($(dish).attr('data-id'))
    $newFormLi.find('#order_orderLines_' + nextNumber + '_price').val($(dish).attr('data-price'))

    $('ul.orderLines').append($newFormLi);

    nextNumber = nextNumber + 1;
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

    $('.btn-dish').on('click', function(e) {
        e.preventDefault();
        addDishToOrderForm(this);
    });
});
