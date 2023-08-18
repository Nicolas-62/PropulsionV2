<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    // DEBUG true en dev
    if($context['APP_ENV'] == 'dev'){
        $debug = 'TRUE';
    }
    // Possibilité de passer en débug dans les autres environnements en créant un cookie dans le navigateur.
    else {
        $debug = isset($_COOKIE['DEBUG']) && $_COOKIE['DEBUG'] === 'TRUE';
    }

    return new Kernel($context['APP_ENV'], $debug);
};
