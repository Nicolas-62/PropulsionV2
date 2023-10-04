<?php

namespace App\Field;

use App\Constants\Constants;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Filesystem\Filesystem;

class FileUploadField  implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string $propertyName
     * @param string|null $label
     * @return mixed
     */
    public static function new(string $propertyName, ?string $label = null): ImageField
    {
        // ToDo récupérer ces variables autrement
        // Chemin d'accès des images depuis le dossier 'public' de l'application
        $base_path  = Constants::DYN_IMG_PATH;
        // Chémin d'upload des images.
        $upload_dir = Constants::PUBLIC_PATH . Constants::DYN_IMG_PATH;

        // Configuration du champ d'upload d'un média.
        return (ImageField::new($propertyName, $label)
            ->setColumns(6)
            ->setBasePath($base_path)
            ->setUploadDir($upload_dir)
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