// assets/bo/cropper.js
import Cropper from 'cropperjs';

(function() {'use strict';
    console.info('welcome to cropper.js');

    // ! Variables

    // Sélecteur principal de la vue.
    let $main                          =       $('body');
    let dropzoneSelector        =       ".my-dropzone";
    let btnCropSelector         =       ".crop-action";
    let btnValidCropSelector    =       ".crop-valid";
    let cropperSelector         =       ".my-cropper";
    let imageCropperSelector    =       ".image-cropper";
    let imageDataInputSelector  =       "#cropData";
    let formSelector            =       ".entity-detail-media-card";

    // Dropzone
    let $dropzones                =       null;

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

        // Si on annule le croppage
        if ($btnCrop.hasClass('active')) {
            // On déclenche le reset du crop
            $btnCrop.parents(formSelector).trigger('resetCrop');
        }
        // Si on active le croppage
        else {
            // Le bouton 'recarder' devient le bouton 'annuler'
            $btnCrop.addClass('active');
            $btnCrop.text('Annuler');
            // Affiche le bouton de validation du crop
            $btnCrop.parents(formSelector).find(btnValidCropSelector).show();
            // On initialise le cropper
            let cropper = new Cropper($btnCrop.parents(formSelector).find(imageCropperSelector)[0], {
                viewMode: 1, // La zonne de recardage ne peut pas sortir de l'image
                // Spécifiez les dimensions prédéfinies pour le recadrage (1:1 dans cet exemple)
                aspectRatio: $btnCrop.attr('data-width') / $btnCrop.attr('data-height'),
                // Ajoutez d'autres options Cropper.js selon vos besoins
            });
        }
    }

    function resetCrop(event){
        let $formMediaCard  =   $(event.currentTarget);

        $formMediaCard.find(btnCropSelector).removeClass('active');
        $formMediaCard.find(btnCropSelector).text('Recadrer');
        // On supprime le cropper
        let $image          =   $formMediaCard.find(imageCropperSelector);
        if(typeof $image[0].cropper !== 'undefined') {
            $image[0].cropper.destroy();
        }
        // On remet l'image d'origine
        $image.attr('src', $image.attr('data-src'));
        let $btnValidate    =   $formMediaCard.find(btnValidCropSelector);
        // On cache le bouton de validation
        $btnValidate.hide();
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
        let formId =   $btnValidCrop.parents(formSelector).attr("data-form-id");
        // On récupère l'image qui contient le cropper
        let $image          =   $btnValidCrop.parents(formSelector).find(imageCropperSelector);
        // Si un cropper est présent
        if($image[0].cropper != null) {
            // Obtenez le canvas de l'image recadrée
            let croppedCanvas = $image[0].cropper.getCroppedCanvas();
            // Convertissez le canvas en une URL de données
            let croppedImageDataURL = croppedCanvas.toDataURL();
            // Affichez l'image recadrée dans la div de prévisualisation
            $image.attr('src', croppedImageDataURL);
            // On l'ajoute au formulaire
            $main.find(imageDataInputSelector+'-'+formId).val(croppedImageDataURL);
            // On cache le bouton de validation
            $btnValidCrop.hide();
            // On supprime le cropper
            $image[0].cropper.destroy();
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
                let $imageCropper   =   $dropzone.parents(formSelector).find(imageCropperSelector);
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
        // Quand le fichier est supprimé, si on est en cours de croppage, on réinitilise le cropper
        $main.on('resetCrop', formSelector, resetCrop);
    });


})();