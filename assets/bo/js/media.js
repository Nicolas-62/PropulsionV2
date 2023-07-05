// assets/bo/media.js
import "../../lib/js/dropzone.js";

import { Dropzone } from "dropzone";

import Cropper from 'cropperjs';

(function() {'use strict';
    console.info('welcome to media.js');

    // ! Variables

    // Sélecteur principal de la vue.
    let $main                      =        $('body');
    let dropzoneSelector    =       ".my-dropzone";
    let btnCropSelector     =       ".crop-action";
    let btnValidCropSelector=       ".crop-valid";
    let cropperSelector     =       ".my-cropper";
    let imageSelector       =       "#image";
    let imageDataInputSelector =    "#cropData";

    let $dropzone             =       null;
    let $cropper              =       null;
    let $image                =       null;
    // input hidden qui contiendra les données du crop
    let $imageDataInput        =       null;
    let cropper               =       null;
    let $data                 =       null;
    let $btnValidate = $main.find(btnValidCropSelector);

    // Fonctions
    function toggleCrop(event){
        let $btnCrop = $(event.currentTarget);
    }




    /**
     *
     * Initialise/detruit le cropper.
     *
     * @param cropper
     * @return void.
     */
    function toggleCrop(event){

        let $btnCrop = $(event.currentTarget);

        if ($btnCrop.hasClass('active')) {
            $btnCrop.removeClass('active');
            $btnCrop.text('Recadrer');
            // On supprime le cropper
            cropper.destroy();
            // On remet l'image d'origine
            $image.attr('src', $image.attr('data-src'));
            // On cache le bouton de validation
            $btnValidate.hide();
        } else {
            $btnCrop.addClass('active');
            $btnCrop.text('Annuler');
            $btnValidate.show();
            cropper = new Cropper($image[0], {
                minContainerWidth: 300,
                minContainerHeight: 300, // Spécifiez les dimensions prédéfinies pour le recadrage (1:1 dans cet exemple)
                // Ajoutez d'autres options Cropper.js selon vos besoins
            });
        }
    };

    /**
     *
     * Ajoute l'image recadrée dans la div de prévisualisation et l'ajoute au formulaire de création du média.
     *
     * @param cropper
     * @return void.
     */
    function validateCrop(){
        if(cropper != null) {
            // Obtenez le canvas de l'image recadrée
            let croppedCanvas = cropper.getCroppedCanvas();
            // Convertissez le canvas en une URL de données
            let croppedImageDataURL = croppedCanvas.toDataURL();
            // Affichez l'image recadrée dans la div de prévisualisation
            $image.attr('src', croppedImageDataURL);
            // On l'ajoute au formulaire
            $imageDataInput.val(croppedImageDataURL);

            // On supprime le bouton de validation
            $btnValidate.hide();
            // On supprime le cropper
            cropper.destroy();
        }
    };




    // When DOM is ready.
    $(document).ready(function() {

        // Récupération des élément du DOM.
        $dropzone               =   $main.find(dropzoneSelector);
        $cropper                =   $main.find(cropperSelector);
        $image                  =   $main.find(imageSelector);
        $imageDataInput         =   $main.find(imageDataInputSelector);

        // Quand l'upload d'une image est terminé
        $main.on('dropzone-success', function(response, data){
            // On met à jour l'image uploadée
            $image.attr('data-src', data.url);
            $image.attr('src', data.url);
            // On cache le dropzone
            $dropzone.hide();
            // On affiche le conteneur de croppage et de l'image
            $cropper.show();
        });
        // AU clique sur le bouton de recadrage
        $main.on('click', btnCropSelector,  toggleCrop);

        // Click sur le bouton de validation du croppage
        $main.on('click', btnValidCropSelector, validateCrop);
    });


})();