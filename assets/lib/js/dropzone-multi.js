// lib/js/dropzone.js
import "dropzone/dist/dropzone.css"
import "../../lib/styles/dropzone-multi.scss";
import { Dropzone } from "dropzone";

import Cropper from 'cropperjs';

(function() {'use strict';
    console.info('welcome to dropzone-multi.js');

    // ! Variables
    let dropzone            =   null;
    // Dropzone acceptant plusieurs fichiers (galerie)
    let dropzoneMultiSelector    =   "div.my-multi-dropzone";
    let $dropzoneMulti           =   $(dropzoneMultiSelector);
    // Sélecteur principal de la vue.
    let $main               =   $('body');


    // ! Events



    // When DOM is ready.
    $(document).ready(function() {

        // Si des dropzones pluti fichiers sont présentes.
        if($dropzoneMulti.length > 0) {
            // Pour chaque dropzone
            $dropzoneMulti.each(function(index, dropzoneElement){
                // Wrap de l'element html dans un Objet JQuery
                let $dropzoneElement    =   $(dropzoneElement);
                let dropzoneId   =   $dropzoneElement.attr("data-id");

                // Init

                // Options de la dropzone
                let options = {
                    url: $dropzoneElement.attr("data-upload-url"),
                    addRemoveLinks: true,
                    dictDefaultMessage: $dropzoneElement.attr("data-upload-message"),
                    thumbnailHeight: '200',
                    thumbnailWidth: '200',
                    thumbnailMethod: "contain",
                    uploadMultiple: false,
                    acceptedFiles: $dropzoneElement.attr("data-accepted-file-types"),
                    dictRemoveFile: 'x',

                }

                // On instancie la zone de dropzone.
                const dropzone      =   new Dropzone(dropzoneElement, options);

                // Si un fichier est correctement uploadé.
                dropzone.on('success', function (file) {
                    // On récupère la réponse envoyée
                    let response    =    JSON.parse(file.xhr.responseText);
                    // Si pas d'erreurs.
                    if (response.error == null) {
                        // On récupère le nom du dossier d'upload et on l'ajoute au formulaire
                        $main.find("#folderId-"+dropzoneId).val(response.folderId);
                    } else {
                        alert(response.error);
                    }
                });
                dropzone.on('removedfile', function(file){
                    // Construction du lien de suppression de l'image
                    let delete_url    =    new URL($dropzoneElement.attr('data-delete-href'));
                    delete_url.searchParams.append("folderId", $main.find("#folderId-"+dropzoneId).val());
                    delete_url.searchParams.append("filename", file.name);
                    // On lance la suppression de l'image
                    $.get(delete_url, function(response){
                        // Si pas d'erreurs.
                        if (response.error == null) {
                            return false;
                        }else{
                            alert(response.error);
                        }
                    });
                })
            });// end foreach dropzone
        }
    });
})();