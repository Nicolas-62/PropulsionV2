/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (layout.html.twig).
 */

const $ = require('jquery');
// import $ from 'jquery';
global.$ = $;
window.jQuery = $;

// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything

import 'bootstrap'; // adds functions to jQuery
//import * as bootstrap from 'bootstrap';
//import { Dropdown } from 'bootstrap';


// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

// $(document).ready(function() {
//     $('[data-toggle="popover"]').popover();
// });



import 'select2';
import './libs/utilities';
import './libs/lightBox';

import verge from 'verge';
$.extend(verge);

// Formulaire de contact
$('.custom-select').select2({
    theme: 'bootstrap-5',
});

$(document).ready(function() {

    // Sélecteur principal de la vue
    let $main               = $('body');
    let $pageContainer      = $('#page-container');
    let $footerContainer    = $('#footer-container');
    let $pageLinkContainer  = $('#page-link-container');

    // Fonctions

    /*
    * Affiche le contenu correspondant à l'entrée sélectionnée
    * */
    function showContent() {
        // Récupérer l'entrée sélectionnée
        let entryId = $(this).attr('data-entry');
        // Récupérer le footer séléctionné
        let footerId = $(this).attr('data-footer');
        // Cacher tous les contenus
        $pageContainer.children().hide();
        $footerContainer.children().hide();
        // Afficher le contenu correspondant à l'entrée sélectionnée
        $("#"+entryId).show();
        $("#"+footerId).show();
        // Move the blue dot to all menu items
        let menuItems = $main.find('.menu-item');
        menuItems.removeClass('active');
        // Move the blue dot to the selected menu item
        let selectedMenuItem = $main.find('.menu-item[data-entry="' + entryId + '"]');
        selectedMenuItem.addClass('active');
    }



    // Ajoutez un gestionnaire d'événement de clic au bouton
    $('#btn-modal').click(function() {
        // Affichez la modal en utilisant jQuery
        $('#Modal').modal('show');
    });

    // Initialisation

    // Afficher le premier élément
    $pageLinkContainer.find('.menu-item').first().trigger('click');

    // Initialisation des événements

    // Au clique sur un élément du menu gauche (pages infos pratiques et institutionnel)
    $pageLinkContainer.find('.menu-item').on('click', showContent);



    // SELECTEUR DE CATEGORIES D'EVENEMENTS  ToDo : à réécrire en jQuery

    // Récupération des éléments
    const dropDownItems = document.querySelectorAll('.dropdown-select-item');
    const dropDownItemsLi = document.querySelectorAll('.dropdown-select-item-li');
    const dropDownButton = document.querySelector('.dropdown-select-button');
    const allEventCards = document.querySelectorAll('.category-tous-les-evenements');

    // Ajout des écouteurs d'événements
    dropDownItems.forEach(function(item) {
        item.addEventListener('click', function(event) {
            // console.log('changement de selecteur');
            dropDownButton.innerHTML = event.target.innerHTML;

            // Affichage des cartes en fonction de la catégorie sélectionnée
            allEventCards.forEach(function(card) {
                let selectedCategory = document.querySelector('.dropdown-select-button').innerHTML.toLowerCase();
                selectedCategory = selectedCategory.replace(/ /g, '-');
                selectedCategory = selectedCategory.replace(/[éè]/g, 'e');
                //console.log('Catégorie sélectionnée ' + selectedCategory);

                if(card.classList.contains('category-' + selectedCategory)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });

        });
    });
});
