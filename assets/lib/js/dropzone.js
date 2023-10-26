// lib/js/dropzone.js
import "dropzone/dist/dropzone.css"
import "../../lib/styles/dropzone.scss";
import { Dropzone } from "dropzone";

import Cropper from 'cropperjs';

(function() {'use strict';
    console.info('welcome to dropzone.js');

    // ! Variables
    let dropzone              =   null;
    let dropzoneSelector    =   "div.my-dropzone";
    let $dropzone                   =   $(dropzoneSelector);
    let formSelector        =   ".entity-detail-media-card";
    let btnDeleteUploadSelector     =   ".btn-delete-upload";
    // Sélecteur principal de la vue.
    let $main               =   $('body');


    // ! Events



    // When DOM is ready.
    $(document).ready(function() {

        // Si des dropzones sont présentes.
        if($dropzone.length > 0) {
            // Pour chaque dropzone
            $dropzone.each(function(index, dropzoneElement){
                // Wrap de l'element html dans un Objet JQuery
                let $dropzoneElement    =   $(dropzoneElement);
                // On récupère l'id du formulaire pour la mediaspec concernée
                let formId          =   $dropzoneElement.parents(formSelector).attr("data-form-id");
                // Init

                // Options de la dropzone
                let options = {
                    url: $dropzoneElement.attr("data-upload-url"),
                    addRemoveLinks: false,
                    maxFiles: 1,
                    dictDefaultMessage: $dropzoneElement.attr("data-upload-message"),
                    thumbnailWidth: null,
                    thumbnailHeight: 200,
                    thumbnailMethod: "contain",
                    acceptedFiles: $dropzoneElement.attr("data-accepted-file-types"),
                }

                // On instancie la zone de dropzone.
                const dropzone      =   new Dropzone(dropzoneElement, options);

                // Si un fichier est correctement uploadé.
                dropzone.on('success', function (file) {
                    // On récupère la réponse envoyée
                    let response    =    JSON.parse(file.xhr.responseText);
                    // DEBUG
                    //console.log(response);
                    // Si pas d'erreurs.
                    if (response.error == null) {
                        // Si une vignette est présente (cas des PDFs)
                        if(response.thumbUrl != null) {
                            // On ajoute la vignette dans la dropzone
                            let thumb_file = {name: response.filename, size: response.filesize, dataURL: response.thumbUrl}
                            // Supprime la vignette de téléchargement du pdf
                            dropzone.removeAllFiles();
                            // Ajoute celle de la vignette
                            dropzone.files.push(thumb_file);
                            dropzone.emit("addedfile", thumb_file);
                            // Créationde la vignette dans la zone de dropzone
                            dropzone.createThumbnailFromUrl(thumb_file,
                                dropzone.options.thumbnailWidth, dropzone.options.thumbnailHeight,
                                dropzone.options.thumbnailMethod, true, function (thumbnail) {
                                    dropzone.emit('thumbnail', thumb_file, thumbnail);
                                });
                            // Ajout de la classe thumbnail à l'image (voir dropzone.scss)
                            $main.find('.dropzone .dz-preview .dz-image').addClass('thumbnail');
                            // Make sure that there is no progress bar, etc...
                            dropzone.emit('complete', thumb_file);
                        }
                        // On récupère le nom du dossier temporaire et on l'ajoute au formulaire
                        $main.find("#folderId-"+formId).val(response.folderId);
                        // On ajoute le nom du dossier temporaire dans le lien du bouton de suppression
                        let $btnDeleteUpload     =    $dropzoneElement.parents(formSelector).find(btnDeleteUploadSelector);
                        let url                 =    new URL($btnDeleteUpload.attr('data-href'));
                        url.searchParams.append("folderId", response.folderId);
                        $btnDeleteUpload.attr("href", url);
                        // On ajoute le nom du fichier
                        $main.find("#filename-"+formId).val(response.filename);
                        // On déclenche un évènement
                        $dropzoneElement.trigger("dropzone-success", [response]);
                    } else {
                        alert(response.error);
                    }
                });
            });
            // Pour chaque dropzone multiple

        }
    });
})();