// assets/bo/cropper.js
import Cropper from 'cropperjs';

(function() {'use strict';
    console.info('welcome to cropper.js');

    // ! Variables

    // Sélecteur principal de la vue.
    let $main                   =       $('body');
    let dropzoneSelector        =       ".my-dropzone";
    let btnCropSelector         =       ".crop-action";
    let btnValidCropSelector    =       ".crop-valid";
    let cropperSelector         =       ".my-cropper";
    let imageSelector           =       "#image-cropper";
    let imageDataInputSelector  =       "#cropData";
    // Dropzone
    let $dropzones               =       null;

    // Fonctions

    /**
     *
     * Initialise/detruit le cropper.
     *
     * @return void.
     * @param event
     */
    function toggleCrop(event){
        // On récupère le bouton de recadrage
        let $btnCrop        =   $(event.currentTarget);
        // On récupère l'id du cropper
        let cropperId       =   $btnCrop.parents(cropperSelector).attr("data-id");
        // On récupère l'image qui contient le cropper
        let $image          =   $main.find(imageSelector+"-"+cropperId);
        let $btnValidate    =   $main.find(btnValidCropSelector+"-"+cropperId);

        // Si on annule le croppage
        if ($btnCrop.hasClass('active')) {
            $btnCrop.removeClass('active');
            $btnCrop.text('Recadrer');
            // On supprime le cropper
            if(typeof $image[0].cropper !== 'undefined') {
                $image[0].cropper.destroy();
            }
            // On remet l'image d'origine
            $image.attr('src', $image.attr('data-src'));
            // On cache le bouton de validation
            $btnValidate.hide();
        }
        // Si on active le croppage
        else {
            $btnCrop.addClass('active');
            $btnCrop.text('Annuler');
            $btnValidate.show();
            // On initialise le cropper
            let cropper = new Cropper($image[0], {
                viewMode: 1, // La zonne de recardage ne peut pas sortir de l'image
                // Spécifiez les dimensions prédéfinies pour le recadrage (1:1 dans cet exemple)
                aspectRatio: $btnCrop.attr('data-width') / $btnCrop.attr('data-height'),
                // Ajoutez d'autres options Cropper.js selon vos besoins
            });
        }
    }

    /**
     *
     * Ajoute l'image recadrée dans la div de prévisualisation et l'ajoute au formulaire de création du média.
     *
     * @return void.
     * @param event
     */
    function validateCrop(event){
        // On récupère le bouton de validation
        let $btnValidCrop   =   $(event.currentTarget);
        // On récupère l'id du cropper
        let cropperId       =   $btnValidCrop.parents(cropperSelector).attr("data-id");
        // On récupère l'image qui contient le cropper
        let $image          =   $main.find(imageSelector+"-"+cropperId);
        // On récupère le cropper
        let cropper         =   $image[0].cropper;
        // Si un cropper est présent
        if(cropper != null) {
            // DEBUG
            // console.log('cropper');
            // console.log(cropper);
            // Obtenez le canvas de l'image recadrée
            let croppedCanvas = cropper.getCroppedCanvas();
            // DEBUG
            // console.log('croppedCanvas');
            // console.log(croppedCanvas);
            // Convertissez le canvas en une URL de données
            let croppedImageDataURL = croppedCanvas.toDataURL();
            // DEBUG
            // console.log('croppedImageDataURL');
            // console.log(croppedImageDataURL);
            // Affichez l'image recadrée dans la div de prévisualisation
            $image.attr('src', croppedImageDataURL);
            // On l'ajoute au formulaire
            $main.find(imageDataInputSelector+'-'+cropperId).val(croppedImageDataURL);
            // On cache le bouton de validation
            $btnValidCrop.hide();
            // On supprime le cropper
            cropper.destroy();
        }
    }




    // When DOM is ready.
    $(document).ready(function() {

        // Récupération des éléments du DOM.
        $dropzones   =   $main.find(dropzoneSelector);
        // Quand l'upload d'une image est terminé
        $dropzones.each(function(index, dropzone) {
            let $dropzone   =   $(dropzone);
            $dropzone.on('dropzone-success', function(response, data){
                // On met à jour l'image uploadée dans le cropper
                let $imageCropper   =   $main.find(imageSelector+"-"+$dropzone.attr('data-id'));
                $imageCropper.attr('data-src', data.url);
                $imageCropper.attr('src',      data.url);
                // On cache le dropzone
                $dropzone.hide();
                // On affiche le conteneur de croppage et de l'image
                $imageCropper.parent().show()
            });

        });

        // AU clique sur le bouton de recadrage
        $main.on('click', btnCropSelector,  toggleCrop);

        // Click sur le bouton de validation du croppage
        $main.on('click', btnValidCropSelector, validateCrop);

    });


})();