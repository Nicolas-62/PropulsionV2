<?php

namespace App\Entity\Traits;

use App\Entity\Media;
use Doctrine\ORM\Mapping as ORM;

trait MediaTrait
{

    private $media1;
    private $media2;
    private $media3;
    private $media4;
    private $media5;


    private $media11;
    private $media12;
    private $media13;

    // Attribut du formulaire d'upload des photos des articles de la categorie galerie.
    private $galleryMediaUploads;

    // Attribut d'affichage des photos des articles de la catégorie galerie.
    private $galleryMedias;

    /**
     * @return mixed
     */
    public function getGalleryMediaUploads()
    {
        return $this->galleryMediaUploads;
    }

    /**
     * @param mixed $galleryMediaUploads
     */
    public function setGalleryMediaUploads($galleryMediaUploads): void
    {
        $this->galleryMediaUploads = $galleryMediaUploads;
    }

    /**
     * @return mixed
     */
    public function getGalleryMedias()
    {
        return $this->galleryMedias;
    }

    /**
     * @param mixed $galleryMedias
     */
    public function setGalleryMedias($galleryMedias): void
    {
        $this->galleryMedias = $galleryMedias;
    }

    /**
     * @return mixed
     */
    public function getMedia13()
    {
        return $this->media13;
    }

    /**
     * @param mixed $media13
     */
    public function setMedia13($media13): void
    {
        $this->media13 = $media13;
    }


    public function __construct(){

    }

    /**
     * @return mixed
     */
    public function getMedia1()
    {
        return $this->media1;
    }

    /**
     * @param mixed $media1
     * @return ExtraDataTrait
     */
    public function setMedia1($media1): self
    {
        $this->media1 = $media1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMedia2()
    {
        return $this->media2;
    }

    /**
     * @param mixed $media2
     * @return ExtraDataTrait
     */
    public function setMedia2($media2)
    {
        $this->media2 = $media2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMedia3()
    {
        return $this->media3;
    }

    /**
     * @param mixed $media3
     * @return ExtraDataTrait
     */
    public function setMedia3($media3)
    {
        $this->media3 = $media3;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMedia4()
    {
        return $this->media4;
    }

    /**
     * @param mixed $media4
     * @return ExtraDataTrait
     */
    public function setMedia4($media4)
    {
        $this->media4 = $media4;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMedia5()
    {
        return $this->media5;
    }

    /**
     * @param mixed $media5
     * @return ExtraDataTrait
     */
    public function setMedia5($media5)
    {
        $this->media5 = $media5;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMedia11()
    {
        return $this->media11;
    }

    /**
     * @param mixed $media11
     */
    public function setMedia11($media11): void
    {
        $this->media11 = $media11;
    }

    /**
     * @return mixed
     */
    public function getMedia12()
    {
        return $this->media12;
    }

    /**
     * @param mixed $media12
     */
    public function setMedia12($media12): void
    {
        $this->media12 = $media12;
    }

}