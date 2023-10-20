<?php

namespace App\Library;

use Imagick;

class Imagique extends \Imagick
{
    /* ! VARIABLES */

    // Chemin complet du fichier.
    public 	$filepath	= 	null;
    // Infos filepath.
    public 	$infos		=	null;

    // Dimensions de l'image
    public $width;
    public $height;
    // Poids de l'image
    public $size;

    // Infos image file.
    public	  $dirname;
    public 	  $basename;
    public	  $filename;
    public	  $extension;

    // Infos image format.
    public 	  $format;
    public	  $type;
    public	  $resolution;

    // Array de thumbnails.
    public	  $thumbnails;

    // Resolution image par défaut.
    public	  $default_resolution   =   150;

    // Compression image par défaut.
    public	  $default_compression  =   90;
    /**
     * Constructeur
     *
     * @description :
     * @access public
     * @return null
     */
    public function __construct($filepath = null, $options = null){

        ini_set('max_execution_time', '300');

        // Chemin complet du fichier.
        $this->filepath		= 	$filepath;

        // Options.
        $this->setOptions($options);

        // Définition de la résolution de l'objet imagick.
        // (Néccéssaire pour les formats d'image vectoriel comme EPS).
        $this->setResolution($this->default_resolution, $this->default_resolution);

        // Instancie Imagick.
        parent::__construct($this->filepath);

        // Récupération des infos de l'objet.
        if(isset($this->filepath)){
            $this->init();
        }
    }

    /**
     * init
     *
     * @description : Récupération des infos de l'objet.
     * @access public
     * @return null
     */
    public function init(){

        $pathinfo					=	pathinfo($this->filepath);

        $this->dirname 		=	$pathinfo['dirname'];
        $this->basename 	=	$pathinfo['basename'];
        $this->filename 	=	$pathinfo['filename'];
        $this->extension 	=	$pathinfo['extension'];


        // DEPRECATED !!! (Utiliser les fonctions ci-dessous à la place !)
        $this->width 			=	$this->getImageWidth();
        $this->height 		=	$this->getImageHeight();
        $this->size 			  =	$this->getImageLength();
        // FIN DEPRECATED

        $this->format			=	strtolower($this->getImageFormat());
        $this->type 			  =	'image/'.$this->format; // DEPRECATED !
        //$this->type 			  =	mime_content_type($this->filepath);
        $this->resolution	=	$this->getImageResolution();
    }

    /**
     * setOptions
     *
     * @description : Surcharge des options.
     * @param : Array d'options.
     * @access public
     * @return null
     */
    public function setOptions($options = null){
        // Si array options défini.
        if(isset($options)){
            // Pour chaque option.
            foreach($options as $key => $value){
                // Surcharge de l'option
                $this->options[$key] 	= 	$value;
            }
        }
    }

    /**
     * serveImage
     * @description : Sert une image au navigateur.
     * @access private
     * @param $width [optionnel] : Largeur de l'image à servir. [Default : null]
     * @param $height [optionnel] : Hauteur de l'image à servir. [Default : null]
     * @param $resolution [optionnel] : Résolution de sortie. [Default : null]
     * @param $compression [optionnel] : Compression de sortie. [Default : null]
     * @param $new_format [optionnel] : Format d'arrivé. [Default : null]
     * @return void
     */
    public function serve($width = null, $height = null, $resolution = null, $compression = null, $new_format = 'jpg'){

        // Default resolution.
        if(!isset($resolution)){
            $resolution = $this->default_resolution;
        }

        // Default compression.
        if(!isset($compression)){
            $compression = $this->default_compression;
        }

        /* Dimensions */

        // Défini les dimensions à affecté à partir des valeurs passé en paramètre.
        $dimensions	=	$this->defineDimensions($width, $height);



        /* Imagick */

        // Copy de l'objet image.
        $imagick	=	clone $this;

        // Conversion de format.
        if(isset($new_format)){
            $imagick->convertFormat($new_format);
        }

        // Conversion de l'espace colorimetrique en sRGB. Todo :  à implémenter
        // $imagick->convertColorSpaceToSRGB();

        // Définition de la résolution d'image pour l'objet.
        $imagick->setResolution($resolution, $resolution);
        $imagick->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
        $imagick->setImageResolution($resolution, $resolution);


        // Compression JPEG.
        if($imagick->format == 'jpg' || $imagick->format == 'jpeg'){
            $imagick->setCompression(Imagick::COMPRESSION_JPEG);
            $imagick->setImageCompressionQuality($compression);
            $imagick->stripImage();
        }

        // Redimensionnement
        if(isset($dimensions['width'])){
            $imagick->scaleImage($dimensions['width'], $dimensions['height'], true);
        }

        /* Serve */


        /* Serve */

        // Header
        // Todo : Trouver un moyen proper de gérer le cache des images générées dynamiquement
        header("Cache-Control: max-age=604133, must-revalidate");
        header("Content-Disposition: inline; filename=$imagick->basename");
        header("Content-Type: $imagick->type");

        // Servir l'image.
        echo $imagick;

        /* Clean objet image */

        // Nettoyage de l'objet image.
        $imagick->clear();
    }


    /**
     * defineDimensions
     * @description : Défini les dimensions appropriées par rapport aux dimensions demandés.
     * @access public
     * @param $width (optional): Largeur demandé. [Default = null]
     * @param $height (optional): Hauteur demandé. [Default = null]
     * @param $upscale (optional): Aggrandissement autorisé. [Default = false]
     * @return array : Array contenant les indexs width et height.
     */
    public function defineDimensions($width = null, $height = null, $upscale = false){

        // ! Definition des valeurs :

        // Si $width est défini.
        if(isset($width)){
            if(is_numeric($width)){
                $width		  =	  intval($width);
            }else{
                $width		  =	  null;
            }
        }

        // Si $height est défini.
        if(isset($height)){
            if(is_numeric($height)){
                $height		=	  intval($height);
            }else{
                $height		=	  null;
            }
        }

        // ! Définition des valeurs max.

        // Si aggrandissement autorisé.
        if($upscale){
            // Largeur et Hauteur max (produit en croix).
            if(isset($height)){
                $max_width    =   intval($height * ($this->getImageWidth()  / $this->getImageHeight()));
            }
            if(isset($width)){
                $max_height   =   intval($width * ($this->getImageHeight() / $this->getImageWidth() ));
            }
        }else{
            // Largeur et Hauteur max (Limite à la taille de l'image).
            $max_width  =   $this->getImageWidth() ;
            $max_height =   $this->getImageHeight();
        }

        // ! Vérification des limites max :

        // Si on dépasse les dimensions de l'original.
        // On prend les dimensions original.
        if(isset($width) && isset($max_width)	&&	$width > $max_width){
            $width			=		$max_width;
        }

        if(isset($height) && isset($max_height)	&&	$height > $max_height){
            $height		 =		$max_height;
        }

        // ! Définition des valeurs manquantes.

        // Calcul des ratios.
        if(isset($width)  &&  isset($height) && $height != 0){
            $image_ratio			=		$width / $height;
        }else{
            $image_ratio			=		1;
        }
        $image_width        =   $this->getImageWidth();
        $image_height       =   $this->getImageHeight();
        if(isset($image_width )  &&  isset($image_height) && $image_height != 0){
            $spec_ratio				=		$image_width  / $image_height;
        }else{
            $spec_ratio				=		1;
        }

        // Calcul largeur d'après hauteur.
        if(!isset($width) &&		isset($height) && !empty($height)){
            // Calcul de la largeur selon ratio.
            $width						=		$height * $spec_ratio;
        }

        // Calcul hauteur d'après largeur.
        if(!isset($height) &&	isset($width) && !empty($width)){
            // Calcul de la largeur selon ratio.
            $height					=		$width * (1 / $spec_ratio);
        }

        return array('width' => $width, 'height' => $height);

    }

    /**
     * convertFormat
     * @description : Défini les dimensions appropriées par rapport aux dimensions demandés.
     * @access public
     * @param $new_format: Format d'arrivé.
     *
     * @return Boolean : Changement de format éffectué.
     */
    private function convertFormat($new_format){

        // Changement de format d'image.
        $converted  =	 $this->setImageFormat($new_format);

        // Format => Extension.
        switch($new_format){

            case 'jpeg':
                $new_extension	=		'jpg';
                break;

            case 'tiff':
                $new_extension	=		'tif';
                break;

            default:
                $new_extension	=		$new_format;

        }

        // Change image path extension.
        $this->filepath	=	str_ireplace('.'.$this->extension, '.'.$new_extension, $this->filepath);

        // Réinitialise les infos de l'image.
        $this->init();

        // Return conversion boolean
        return $converted;
    }
}