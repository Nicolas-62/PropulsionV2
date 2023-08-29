// lib/js/dropzone.js
import "dropzone/dist/dropzone.css"
import "../../lib/styles/dropzone.scss";
import { Dropzone } from "dropzone";

import Cropper from 'cropperjs';

(function() {'use strict';
    console.info('welcome to dropzone.js');

    // ! Variables
    let dropzone               =  null;
    let dropzoneSelector     =  "div.my-dropzone";
    let $dropzone                   =   $(dropzoneSelector);

    // Sélecteur principal de la vue.
    let $main                 =     $('body');


    // ! Events



    // When DOM is ready.
    $(document).ready(function() {

        // Si des dropzones sont présentes.
        if($dropzone.length > 0) {
            // Pour chaque dropzone
            $dropzone.each(function(index, dropzoneElement){
                // Wrap de l'element html dans un Objet JQuery
                let $dropzoneElement = $(dropzoneElement);
                let dropzoneId = $dropzoneElement.attr("data-id");

                // Init

                // Options de la dropzone
                let options = {
                    url: $dropzoneElement.attr("data-upload-url"),
                    dictRemoveFile: 'x',
                    addRemoveLinks: true,
                    maxFiles: 1,
                    dictDefaultMessage: $dropzoneElement.attr("data-upload-message"),
                    thumbnailWidth: null,
                    thumbnailHeight: 200,
                    thumbnailMethod: "contain"
                }

                // On instancie la zone de dropzone.
                const dropzone  = new Dropzone(dropzoneElement, options);

                // Si un fichier est correctement uploadé.
                dropzone.on('success', function (file) {
                    // On récupère la réponse envoyée
                    let response = JSON.parse(file.xhr.responseText);
                    // Si pas d'erreurs.
                    if (response.error == null) {
                        // On récupère le nom du dossier temporaire et on l'ajoute au formulaire
                        $main.find("#folderId-"+dropzoneId).val(response.folderId);
                        // On ajoute le nom du dossier temporaire dans le lien du bouton de suppression
                        let btnDeleteUpload = $main.find("#btn-delete-upload-"+dropzoneId);
                        let url = new URL(btnDeleteUpload.attr('data-href'));
                        url.searchParams.append("folderId", response.folderId);
                        btnDeleteUpload.attr("href", url);
                        // On ajoute le nom du fichier
                        $main.find("#filename-"+dropzoneId).val(response.filename);

                        // On déclenche un évènement
                        $dropzoneElement.trigger("dropzone-success", [response]);
                    } else {
                        alert(response.error);
                    }
                });
            });
        }



    });
})();