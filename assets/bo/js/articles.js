// assets/bo/articles.js

(function() {'use strict';
    console.info('welcome to articles.js');

    // ! Variables

    // Sélecteur principal de la vue.
    let $main                 =     $('body');


    window.copierLien = function(button) {
        /* Récupérez l'URL à partir de l'attribut data */
        const url = button.getAttribute("data-link");

        /* Créez un élément texte temporaire */
        const tempTextArea = document.createElement("textarea");
        tempTextArea.value = url;

        document.body.appendChild(tempTextArea);

        tempTextArea.select();

        document.execCommand("copy");

        document.body.removeChild(tempTextArea);

        alert("Le lien a été copié ");
    };
    // When DOM is ready.
    $(document).ready(function() {

        //Bouton Copier




    });
})();