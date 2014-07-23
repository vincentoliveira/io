/* 
 * Main.js
 */

$(document).ready(function() {
    $('.confirm-delete').click(function(e) {
        return confirm('Etes-vous sûr de vouloir supprimer cet élément ?');
    });

    $('.confirm-cancel').click(function(e) {
        return confirm('Etes-vous sûr de vouloir annuler cette commande ?');
    });

    if ($(".order-list").length) {
        window.setInterval(autorefreshOrderList, 10000);
    }
    
    // Open modal
    $('[data-toggle="modal"]').click(function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var modalId = $(this).attr('data-target');
        $.get(url, function(data) {
            $(modalId).find('.modal-content').html(data);
        });
    });

    $(document).on('dragenter', function(e)
    {
        e.stopPropagation();
        e.preventDefault();
    });
    $(document).on('dragover', function(e)
    {
        e.stopPropagation();
        e.preventDefault();
    });
    $(document).on('drop', function(e)
    {
        e.stopPropagation();
        e.preventDefault();
    });
});
