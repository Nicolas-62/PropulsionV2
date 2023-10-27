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
                    // On cache le bouton de mise en vedette
                    $btnDelete.parents('.gallery-thumbnail').find('.photo-star').hide();
                    $btnDelete.next().show();
                    return false;
                }else{
                    alert(response.error);
                }
            });
        });

        // Au clique sur le bouton de  mise en vedette d'une image
        $main.on('click', '.photo-star', function(event){
            // Bouton de mise en vedette
            let $btnStar= $(this);
            event.preventDefault();
            // Si le bouton n'est pas encore en vedette
            if(!$btnStar.hasClass('active')) {
                // Mise en vedette
                $.get($btnStar.attr('href'), function (response) {
                    // Si pas d'erreurs.
                    if (response.error == null) {
                        // On enlève la classe active à tous les boutons
                        $('.photo-star').removeClass('active');
                        // On ajoute la classe active au bouton cliqué
                        $btnStar.addClass('active');
                        return false;
                    } else {
                        alert(response.error);
                    }
                });
            }
        });

    });
})();