<?php

namespace App\Entity\Traits;

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
     * @return MediaTrait
     */
    public function setMedia1($media1)
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
     * @return MediaTrait
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
     * @return MediaTrait
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
     * @return MediaTrait
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
     * @return MediaTrait
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