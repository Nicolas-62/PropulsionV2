// assets/bo/pictures.js

import './picture';

(function(){ 'use strict';
    console.info('welcome to pictures.js');
    // Variables.

    // Sélecteur principal de la vue.
    let $main               =   $('body');
    // // Modale de suppression d'un média.
    let $modale_delete      =   $main.find('#modal-delete');
    // // Liste des liens avec le media.
    let $list_media_links   =   $modale_delete.find('.media-entity-list');


    // Events

    // A l'affichage de la modale de suppression
    $modale_delete.on('shown.bs.modal', function(event){

        // Récupération du bouton délcancheur de l'ouverture de la modale.
        let $link = $(event.relatedTarget);

        // Appel, liste des publications liées au médià
        $.ajax(
            {
                // URL.
                url: $link.attr('data-get-entites-url'),
                // Methode d'envoie des params.
                type: 'GET',
                // Params.
                dataType: "json",
                // Fonction exécuté en cas de succès.
                success: function(datas){
                    // DEBUG
                    // console.log(datas);

                    // Capteur méida associé à des publications
                    let has_link = false;
                    // Pour chaque type de publication (article/catégorie)
                    $.each(datas.entities, function(type, elements){
                        // On créer une ligne et on l'ajoute à la liste.
                        $list_media_links.append(
                            $('<div />', {'text': type + ' :'})
                        );
                        // Pour chaque publication
                        $.each(elements, function(id, name) {
                            // Capteur présence de liens avec ce média.
                            has_link = true;
                            // On créer une ligne et on l'ajoute à la liste.
                            $list_media_links.append(
                                $('<div />', {'text': name})
                            );
                        });

                    });
                    // Si le média est lié à des publications on affiche un message d'alerte.
                    if(has_link){
                        $list_media_links.show();
                        $list_media_links.prev().show();
                    }
                },
            }
        );
    });

    // // A la fermeture de la modale de suppression
    $modale_delete.on('hide.bs.modal', function(event){
        // On cache les messages d'erreur.
        $list_media_links.hide();
        $list_media_links.prev().hide();
        // On vide la liste des liens avec le média sélectionné.
        $list_media_links.html('');
    });


})(); // EOF.
