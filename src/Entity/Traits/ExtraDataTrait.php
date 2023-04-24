<?php

namespace App\Entity\Traits;

use App\Entity\Media;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;

trait ExtraDataTrait
{

//    /**
//     * @return mixed
//     */
//    public function getHeadline()
//    {
//        return $this->headline;
//    }
//    /**
//     * @param bool $headline
//     */
//    public function setHeadline(bool $headline): self
//    {
//        $this->headline = $headline;
//        return $this;
//    }

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

}