// assets/bo/article.js
import "../../lib/js/dropzone.js";
import "./cropper.js";

// import 'select2';
// import '../../lib/js/select';
import Cropper from 'cropperjs';



(function() {'use strict';
    console.info('welcome to article.js');
    // ! INFO
    // Les selecteur d'easyAdmin sont gérés par défaut avec la librairie tomselect
    // https://symfony.com/bundles/EasyAdminBundle/current/fields/AssociationField.html
    // https://tom-select.js.org/

    // ! Variables

    // Sélecteur principal de la vue.
    let $main                         =      $('body');
    // Sélecteurs des sélecteurs...
    let articleParentSelector  =     '#Article_parent';
    let categoryParentSelector =     '#Article_category';
    let formSelector           =     ".entity-detail-media-card";
    let dropzoneSelector        =    "div.my-dropzone";
    let cropperSelector         =    ".my-cropper";
    let imageCropperSelector    =    ".image-cropper";


    /*
     * Supprime un fichier uploadé de la dropzone.
     * @param {Event} event
     */
    function deleteUpload(event){
        // On prévient l'action déclenché par le click sur le bouton.
        event.preventDefault();
        // On récupère le bouton cliqué.
        let $btn = $(this);
        let $formContainer = $btn.parents(formSelector);
        // On appel en ajax l'url de suppression.
        $.ajax($btn.attr('href'), {
            method: 'GET',
            success: function (data) {
                // On supprime le fichier de la dropzone.
                if(typeof $formContainer.find(dropzoneSelector)[0].dropzone !== 'undefined') {
                    $formContainer.find(dropzoneSelector)[0].dropzone.removeAllFiles();
                }
                // On affiche la dropzone.
                $formContainer.find(dropzoneSelector).show();
                // On cache le cropper
                $formContainer.find(cropperSelector).hide();
                // On réinitialise le cropper
                $formContainer.trigger('resetCrop');
                // On supprime le fichier du cropper
                if(typeof $formContainer.find(imageCropperSelector)[0].cropper !== 'undefined') {
                    $formContainer.find(imageCropperSelector)[0].cropper.destroy();
                }
            },
            error: function (data) {
                // On affiche l'erreur.
                alert(data.error);
            }
        });
    }


    // When DOM is ready.
    $(document).ready(function() {

        // Lorsqu'un article parent est choisi.
        $main.on('change', articleParentSelector, function(event){
            if($(this).val() !== ''){
                // On vide le champ catégorie parent.
                $main.find(categoryParentSelector)[0].tomselect.clear();
            }
        });
        // Lorsqu'une catégorie parentz est choisi.
        $main.on('change', categoryParentSelector, function(event){
            if($(this).val() !== ''){
                // On vide le champ article parent.
                $main.find(articleParentSelector)[0].tomselect.clear();
            }
        });

        // Lorsque l'on veut supprimer un media uploadé de la dropzone.
        $main.on('click', '.btn-delete-upload', deleteUpload);

    });
})();