<?php

namespace App\Twig;

use App\Constants\Constants;
use App\Entity\Media;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getName', [$this, 'getName']),
            new TwigFunction('getThumbnailPath', [$this, 'getThumbnailPath']),
            new TwigFunction('getSiteImagePath', [$this, 'getSiteImagePath']),
            new TwigFunction('getGalleryPath', [$this, 'getGalleryPath']),
            new TwigFunction('const', [$this, 'const']),
            new TwigFunction('truncateHtml', [$this, 'truncateHtml']),
            new TwigFunction('formatCustomDate', [$this, 'formatCustomDate']),
            new TwigFunction('formatCustomDateWithoutYears', [$this, 'formatCustomDateWithoutYears']),
            new TwigFunction('getDatetimeEvent', [$this, 'getDatetimeEvent']),
            new TwigFunction('getTacLink', [$this, 'getTacLink']),


        ];
    }

    public function getName($filename): string
    {
        return Media::getNameOf($filename);
    }

    public function getThumbnailPath($filename): string
    {
        return Constants::DYN_IMG_PATH.$filename;
    }

    public function getSiteImagePath($filename): string
    {
        return Constants::STATIC_IMG_PATH.$filename;
    }


    public function getGalleryPath($filename): string
    {
        return Constants::GALLERY_PATH.$filename;
    }

    public function const(string $value): string
    {
        $value = strtoupper($value);
        if(defined("App\Constants\Constants::$value")) {
            return constant("App\Constants\Constants::$value");
        }else{
            return '';
        }
    }

    public function truncateHtml($input, $length)
    {
        $plainText = strip_tags($input);

        if (mb_strlen($plainText) <= $length) {
            return $input;
        }

        $truncatedText = mb_substr($plainText, 0, $length);

        $lastOpenTag = strrpos($truncatedText, '<');

        $lastCloseTag = strrpos($truncatedText, '>');

        if ($lastOpenTag > $lastCloseTag) {
            $truncatedText = substr($truncatedText, 0, $lastOpenTag);
        }

        $result = substr($input, 0, mb_strlen($truncatedText));

        return $result;

    }

    public function getDatetimeEvent($date, $datetime){

        if($date == null){
            $date = '0000-00-00 00:00:00';
        }else{
            $date = $date->format('Y-m-d H:i:s');
        }

        if($datetime == null){
            $datetime = '00:00:00';
        }else{
            $datetime = $datetime->format('H:i:s');
        }
        return str_replace('00:00:00', $datetime, $date);
    }

    public function formatCustomDate($date)
    {
        if($date != null and $date instanceof \DateTimeInterface ) {

            $dayOfWeek = [
              'Mon' => 'LUN.',
              'Tue' => 'MAR.',
              'Wed' => 'MER.',
              'Thu' => 'JEU.',
              'Fri' => 'VEN.',
              'Sat' => 'SAM.',
              'Sun' => 'DIM.',
            ];

            $monthNames = [
              'Jan' => 'JANVIER',
              'Feb' => 'FÉVRIER',
              'Mar' => 'MARS',
              'Apr' => 'AVRIL',
              'May' => 'MAI',
              'Jun' => 'JUIN',
              'Jul' => 'JUILLET',
              'Aug' => 'AOÛT',
              'Sep' => 'SEPTEMBRE',
              'Oct' => 'OCTOBRE',
              'Nov' => 'NOVEMBRE',
              'Dec' => 'DÉCEMBRE',
            ];

            $formattedDate = $dayOfWeek[$date->format('D')] . ' ' . $date->format('j') . ' ' . $monthNames[$date->format('M')] . ' ' . $date->format('Y');

            return strtoupper($formattedDate);
        }else{
            return '';
        }
    }

    public function formatCustomDateWithoutYears($date)
    {
        if($date != null and $date instanceof \DateTimeInterface ) {

            $dayOfWeek = [
              'Mon' => 'LUN.',
              'Tue' => 'MAR.',
              'Wed' => 'MER.',
              'Thu' => 'JEU.',
              'Fri' => 'VEN.',
              'Sat' => 'SAM.',
              'Sun' => 'DIM.',
            ];

            $monthNames = [
              'Jan' => 'JANVIER',
              'Feb' => 'FÉVRIER',
              'Mar' => 'MARS',
              'Apr' => 'AVRIL',
              'May' => 'MAI',
              'Jun' => 'JUIN',
              'Jul' => 'JUILLET',
              'Aug' => 'AOÛT',
              'Sep' => 'SEPTEMBRE',
              'Oct' => 'OCTOBRE',
              'Nov' => 'NOVEMBRE',
              'Dec' => 'DÉCEMBRE',
            ];

            $formattedDate = $dayOfWeek[$date->format('D')] . ' ' . $date->format('j') . ' ' . $monthNames[$date->format('M')];

            return strtoupper($formattedDate);
        }else{
            return '';
        }
    }

    /**
     * Récupère les infos d'une balise et la remplace par une balise spécifique tarteaucitron (TAC)
     * @param $service : nom du service
     * @param $html_tag : balise html à remplacer par la balise tarteaucitron
     * @return string
     */
    public function getTacLink($service, $html_tag): string
    {
        // Contenu balise TAC
        $tac_html = '';
        // Service Soundcloud
        if($service == 'soundcloud'){
            /*
             * exemple de lien :
             * <iframe
             * height="300"
             * ...
             * src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/1677565218&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true"
             * >
             * </iframe>
             * <div ...><a href="https://soundcloud.com/danyl-sc"...>Danyl</a> · <a href="https://soundcloud.com/danyl-sc/mazel"...">Mazel</a></div>
             */
            // Récupération de l'iframe
            $iframe = substr($html_tag, 0, strpos($html_tag,'</iframe>')+9);
            // Récupération du footer
            $infos =  substr($html_tag, strpos($html_tag,'</iframe>')+9, strlen($html_tag));
            // Suppression des balises
            $iframe = preg_replace('/<iframe\s|<\/iframe|>/', '', $iframe);
            // Attributs de la balise
            $iframe_attributs = explode(' ', $iframe);
            // Hauteur par défaut
            $height = '300';
            // Récupération des attributs
            foreach($iframe_attributs as $attribut){
                // Attribut src
                if(str_starts_with($attribut, 'src')){
                    $source = $attribut;
                }
                // Attribut height
                if(str_starts_with($attribut, 'height')){
                    $height = preg_replace('/height=|"/', '', $attribut);
                }
            }
            // Si on a récupéré l'attribut src
            if(isset($source)) {
                // Attributs TAC
                $attributs = [
                    'data_playable_id' => substr($source, strpos($source, '/tracks/') + 8, ( - strlen($source) + strpos($source, '&')) ),
                    'data_playable_type'=> 'tracks',
                    'data_height' => $height,
                    'data_color' => substr($source, strpos($source, '&color=') + 7, ( -strlen($source) + strpos($source, '&color=') +16) ),
                    'data_auto_play' => 'false',
                    'data_hide_related' => 'false',
                    'data_show_comments' => 'true',
                    'data_show_user' => 'true',
                    'data_show_reposts' => 'false',
                    'data_show_teaser' => 'true',
                    'data_visual' => 'true',
                    'data_artwork' => 'false',
                ];
                $attributs = (object) $attributs;
                // Balise TAC
                $tac_html = "
                    <div
                      class='soundcloud_player'
                      data-playable-id='$attributs->data_playable_id'
                      data-playable-type='$attributs->data_playable_type'
                      data-height='$attributs->data_height'
                      data-color='$attributs->data_color'
                      data-auto-play='$attributs->data_auto_play)'
                      data-hide-related='$attributs->data_hide_related'
                      data-show-comments='$attributs->data_show_comments'
                      data-show-user='$attributs->data_show_user'
                      data-show-reposts='$attributs->data_show_reposts'
                      data-show-teaser='$attributs->data_show_teaser'
                      data-visual='$attributs->data_visual'
                      data-artwork='$attributs->data_artwork'
                     >
                    </div>";
            }
        }
        return $tac_html;
    }



}