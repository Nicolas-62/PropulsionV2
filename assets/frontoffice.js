/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (layout.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/frontoffice.css';

// start the Stimulus application
import './bootstrap';


import $ from 'jquery';

import 'select2';

$('.custom-select').select2({
    theme: 'bootstrap-5',

});
