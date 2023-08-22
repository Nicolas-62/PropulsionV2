<?php

namespace App\Entity\Traits;

use App\Entity\Media;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;

trait ExtraFieldtrait
{




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
            if(method_exists($this, 'set' . $data->getFieldKey())){
                $this->{'set' . $data->getFieldKey()}($data->getFieldValue());
            }
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