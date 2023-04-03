<?php

namespace App\Field;

use App\Constants\Constants;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class MediaUploadField  implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string $propertyName
     * @param string|null $label
     * @return mixed
     */
    public static function new(string $propertyName, ?string $label = null): ImageField
    {
        // Configuration du champ d'upload d'un média.
        return (ImageField::new($propertyName, $label)
            ->setColumns(6)
            ->setBasePath(Constants::ASSETS_IMG_PATH)
            ->setUploadDir(Constants::UPLOAD_DIR.Constants::ASSETS_IMG_PATH)
            ->setUploadedFileNamePattern('[name]_[randomhash].[extension]')
            ->setRequired(false)
        );
    }

    // TEST VICHUPLOAD BUNDLE
//    public static function new(string $propertyName, ?string $label = null)
//    {
//        // Configuration du champ d'upload d'un média.
//        return (new self())
//            ->setProperty($propertyName)
//            ->setLabel($label)
//            ->setColumns(6)
//            ->setTemplatePath('backoffice/field/mediaupload.html.twig')
//            ->setFormType(VichImageType::class)
//            ->addCssClass('field-vich-image')
//            ;
//    }

}