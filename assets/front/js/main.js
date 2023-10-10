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

// test bootstrap & jquery well imported
$('.dropdown-toggle').dropdown();
