<?php

namespace App\Entity\Traits;

use App\Entity\Media;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use function PHPUnit\Framework\isEmpty;

trait ExtraFieldtrait
{

    /**
     * Surchage de la fonction magique __isset pour récupération des champs sans getters et setters par twig
     * @param $key
     * @return bool
     */
    public function __isset($key){
        if ( isset($this->$key) ) return true ;
        return false;
    }

    /**
     * Surcharge de la fonction magique __get pour appeler les getters.
     *
     * @param $property
     * @return null
     */
    public function __get($property)
    {
        // Si on a un getter
        if(method_exists($this, 'get' . ucfirst($property))) {
            return $this->{'get' . ucfirst($property)}();
        // Sinon appel de la methode magique __call
        }else if(is_callable(array($this, $property))){
            return $this->{'get' . ucfirst($property)}();
        }
        return null;
    }

    /**
     * Fonction d'appel des proprités qui n'ont pas de getter/setter.
     * @param $name
     * @param $args
     * @return \DateTimeImmutable|void|null
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        // Nom de la proprieté
        $property = lcfirst(substr($name, 3));
        if ('get' === substr($name, 0, 3)) {
            // Si c'est une date on retourne null un objet date
            if(str_contains($property, 'date')){
                // Si le champ est vide
                if(empty($this->{$property})){
                    return null;
                }
                return new \DateTimeImmutable($this->{$property});
            }else{
                return $this->{$property} ?? null;
            }
        } elseif ('set' === substr($name, 0, 3)) {
            // Valeur de la proprieté
            $value = 1 == count($args) ? $args[0] : null;
            // Si c'est une date
            if(str_contains($property, 'date')){
                // Si le champ est vide
                if(empty($value)){
                    $this->{$property} = '';
                // Sinon on retourne une date formatée sous forme de string
                }else{
                    if(is_string($value)) {
                        $value = new \DateTimeImmutable($value);
                    }
                    $this->{$property} = $value->format($this->getExtraField($property)['format']);
                }
            }else{
                $this->{$property} = $value;
            }
        }
    }

    /**
     * @return Collection
     */
    public function getData(): Collection
    {
        return $this->data;
    }

    /**
     * @param Collection $data
     */
    public function setData(Collection $data): void
    {
        $this->data = $data;
    }

    
    public function getExtraFields(): array
    {
        return $this->extraFields;
    }

    public function getExtraFieldNames(): array
    {
        $extraFieldNames = array();
        foreach($this->getExtraFields() as $extraFieldDatas){
            $extraFieldNames[] = $extraFieldDatas['name'];
        }
        return $extraFieldNames;
    }

    public function getExtraField($fieldName): ?array
    {
        foreach($this->getExtraFields() as $extraFieldDatas){
            if($extraFieldDatas['name'] == $fieldName){
                return $extraFieldDatas;
            }
        }
        return null;
    }


    /**
     * Donne le type easyadmin du champ.
     * @param string $eaType
     * @return string|null
     */
    public function getEasyAdminFieldType(string $eaType): ?string
    {
        // Espace de nom easy admin.
        $fieldType = "EasyCorp\\Bundle\\EasyAdminBundle\\Field\\";
        return $fieldType . $eaType;
    }


    /**
     * @param $code_langue
     * @return void
     */
    public function getDatas($code_langue): void
    {
        $datas = $this->data->filter(function($data) use ($code_langue) {
            return $data->getLanguage()->getCode() === $code_langue;
        });
        // Si on a un setter pour le champ renseigné en BDD
        foreach($datas as $data){
            $this->{'set' . $data->getFieldKey()}($data->getFieldValue());
        }
    }

    public function addData($data): self
    {
        if (!$this->data->contains($data)) {
            $this->data->add($data);
            $data->setObject($this);
        }

        return $this;
    }

    public function removeData($data): self
    {
        if ($this->data->removeElement($data)) {
            // set the owning side to null (unless already changed)
            if ($data->getObject() === $this) {
                $data->setObject(null);
            }
        }

        return $this;
    }

}