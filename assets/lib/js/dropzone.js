// lib/js/dropzone.js
import "dropzone/dist/dropzone.css"
import "../../lib/styles/dropzone.scss";
import { Dropzone } from "dropzone";

import Cropper from 'cropperjs';

(function() {'use strict';
    console.log('welcome to dropzone.js');

    // ! Variables
    let dropzone                =  null;
    let dropzoneSelector        =  "div.my-dropzone";
    let $dropzone               =   $(dropzoneSelector);

    // Sélecteur principal de la vue.
    let $main                 =     $('body');

    // When DOM is ready.
    $(document).ready(function() {

        // ! Events.
        if($dropzone.length > 0) {

            // Init

            // Options de la dropzone
            let options = {
                url: $dropzone.attr("data-upload-url"),
                dictRemoveFile: 'x',
                addRemoveLinks: true,
                maxFiles: 1,
                dictDefaultMessage: $dropzone.attr("data-upload-message"),
                thumbnailWidth: null,
                thumbnailHeight: 200,
                thumbnailMethod: "contain"
            }

            // On instancie la zone de dropzone.
            const dropzone = new Dropzone(dropzoneSelector, options);

            // Si un fichier est correctement uploadé.
            dropzone.on('success', function (file) {
                // On récupère la réponse envoyée
                let response = JSON.parse(file.xhr.responseText);
                // Si pas d'erreurs.
                if (response.error == null) {
                    // On récupère le nom du dossier temporaire et on l'ajoute au formulaire
                    $main.find("#folderId").val(response.folderId);
                    // On ajoute le nom du fichier
                    $main.find("#filename").val(response.filename);
                } else {
                    alert(response.error);
                }
            });
        }

    });
})();