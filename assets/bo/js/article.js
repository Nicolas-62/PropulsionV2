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

    });
})();