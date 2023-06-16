<?php

namespace App\Service;

class Secure{

    public function __construct()
    {
    }

    public function random_hash($length = 25){
        // Génére une chaine de bytes pseudo-aléatoire.
        $bytes 	= 	random_bytes($length);
        // Transforme la chaine de bytes en chaine hexadécimale.
        $hex   	= 	bin2hex($bytes);
        // Retourne la chaine hexadécimale.
        return $hex;
    }
}