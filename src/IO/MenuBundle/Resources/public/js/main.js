/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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
});
