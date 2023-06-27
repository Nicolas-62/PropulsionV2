// assets/bo/article.js
import "../../lib/js/dropzone.js";

// import 'select2';
// import '../../lib/js/select';
import Cropper from 'cropperjs';

document.addEventListener('DOMContentLoaded', function() {
    var image = document.getElementById('image');
    var imageDataInput = document.getElementById('image-data');
    var cropButton = document.getElementById('crop-button');
    var croppedImagePreview = document.getElementById('cropped-image-preview');
    var cropper;

    image.addEventListener('load', function() {
        cropper = new Cropper(image, {
            aspectRatio: 1,
            minContainerWidth: 300,
            minContainerHeight: 300,
            crop: function(event) {
                // Mettez à jour la valeur de l'input avec les coordonnées de la zone sélectionnée
                imageDataInput.value = JSON.stringify(event.detail);
            }, // Spécifiez les dimensions prédéfinies pour le recadrage (1:1 dans cet exemple)
            // Ajoutez d'autres options Cropper.js selon vos besoins
        });
    });

    cropButton.addEventListener('click', function() {
        // Obtenez le canvas de l'image recadrée
        var croppedCanvas = cropper.getCroppedCanvas();

        // Convertissez le canvas en une URL de données
        var croppedImageDataURL = croppedCanvas.toDataURL();

        // Affichez l'image recadrée dans la div de prévisualisation
        croppedImagePreview.innerHTML = '<img src="' + croppedImageDataURL + '" alt="cropped image" style="max-width: 100%;">';

        // Mettez à jour la valeur de l'input avec les données de l'image recadrée
        imageDataInput.value = croppedImageDataURL;
    });
});


(function() {'use strict';
    console.info('welcome to article.js');

    // ! Variables

    // Sélecteur principal de la vue.
    let $main                 =     $('body');

    // When DOM is ready.
    $(document).ready(function() {

    });
})();