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
});
