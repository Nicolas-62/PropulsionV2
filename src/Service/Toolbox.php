<?php

namespace App\Service;

class Toolbox{

    public function __construct(
        private string $config_dir
    ){
    }

    public function convert_accented_characters($str){
        // ToDo a tester sur le serveur
        dump($this->config_dir);
        if (is_file($this->config_dir.'foreign_chars.php'))
        {
            include($this->config_dir.'foreign_chars.php');
        }
        if ( ! isset($foreign_characters))
        {
            return $str;
        }

        return preg_replace(array_keys($foreign_characters), array_values($foreign_characters), $str);
    }

    public function url_compliant($string)
    {

        $separateur = '-';
        $ascii_only = TRUE;

        if ($ascii_only === TRUE) { // Si les caractères sont ascii.

            $string = $this->convert_accented_characters($string);

            // Supprime tout sauf separateur, a-z, 0-9, ou espace
            $string = preg_replace('![^' . preg_quote($separateur) . 'a-z0-9\s_]+!', '', strtolower($string));

        } else {

            // Supprime tout sauf separateur, a-z, 0-9, ou espace (Et converti en UTF8)
            $string = preg_replace('![^' . preg_quote($separateur) . '\pL\pN\s_]+!u', '', UTF8::strtolower($string));

        }

        // Remplace  separator et espace par separator unique
        $string = preg_replace('![' . preg_quote($separateur) . '\s_]+!u', $separateur, $string);

        // Trim separators debut et fin
        return trim($string, $separateur);
    }


}