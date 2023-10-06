// assets/bo/gallery.js

import '../styles/gallery.scss';
import "../../lib/js/dropzone-multi.js";


(function() {'use strict';
    console.info('welcome to gallery.js');

    // ! Variables

    // Sélecteur principal de la vue.
    let $main                 =     $('body');


    // When DOM is ready.
    $(document).ready(function() {
        // Au clique sur le bouton de suppression d'une image
        $main.on('click', '.photo-remove', function(event){
            // Bouton de suppression
            let $btnDelete = $(this);
            event.preventDefault();
            // Suppression sur l'image
            $.get($btnDelete.attr('href'), function(response){
                // Si pas d'erreurs.
                if (response.error == null) {
                    // On cache le bouton et on affiche un overlay
                    $btnDelete.hide();
                    $btnDelete.next().show();
                    return false;
                }else{
                    alert(response.error);
                }
            });
        });

    });
})();