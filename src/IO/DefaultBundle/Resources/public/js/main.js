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

    $(document).on('dragenter', function(e)
    {
        e.stopPropagation();
        e.preventDefault();
    });
    $(document).on('dragover', function(e)
    {
        e.stopPropagation();
        e.preventDefault();
        obj.css('border', '2px dotted #0B85A1');
    });
    $(document).on('drop', function(e)
    {
        e.stopPropagation();
        e.preventDefault();
    });
});
