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
let urlParams = new URLSearchParams(window.location.search);

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



    // SELECTEUR DE CATEGORIES D'EVENEMENTS

    // Récupération des éléments
    const $dropDownItems    =   $('.dropdown-select-item');
    const $dropDownButton   =   $('.dropdown-select-button');
    const $allEventCards    =   $('.card-event');
    const $noEventText      =   $('.no-event');
    const $tri         = urlParams.get('tri');
    let $temoinTri = 0;

    // On remet no-event en display none
    $noEventText.hide();
    if ($tri !== null) {
        $allEventCards.each(function(index, element) {
            let $card = $(element);
            console.log('tri : ' + $tri);
            console.log('card : ' + $card.attr('data-id'));
            $dropDownItems.each(function(index, element) {
                if($(element).attr('data-id') === $tri){
                    $dropDownButton.text($(element).text());
                }

            });

            if ($tri === $card.attr('data-id')) {
                $temoinTri = 1;
                $card.show();
            } else {
                $card.hide();
            }
        });
    }
    // Ajout des écouteurs d'événements
    $dropDownItems.on('click', function(event) {
        // On récupère l'élément sur lequel on a cliqué
        let $dropDownItem = $(this);
        // On injecte son label dans le bouton du dropdown.
        $dropDownButton.text($dropDownItem.text());
        // Si l'élément cliqué est associé à un filtre
        if($dropDownItem.attr('data-id')){
            // On crée un témoin pour savoir si on a affiché au moins une card
            let atLeastOneCardDisplayed = false;
            // On affiche que les cards dont la sous-catégorie correspond à celle de l'élément cliqué
            $allEventCards.each(function(index, element) {
                let $card = $(element);
                    // Si la card est associée à la sous-catégorie de l'élément cliqué
                    if ($card.attr('data-id') === $dropDownItem.attr('data-id')) {
                        // On l'affiche
                        atLeastOneCardDisplayed = true;
                        $card.show();
                    } else {
                        $card.hide();
                    }

            });
            if( atLeastOneCardDisplayed === true ) {
                $noEventText.hide();
            }else{
                $noEventText.show();
            }
        }else{
            // On affiche toutes les cards
            $allEventCards.show();
        }
    });
});
