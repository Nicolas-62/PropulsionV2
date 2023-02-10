<?php

namespace App\Controller\Backoffice;

use App\Entity\Category;
use App\Entity\Media;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MediaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnIndex()->hideOnForm()->hideOnDetail(),
            TextField::new('legende',"Légende")->setColumns(6),
            ImageField::new('fichier','Fichier')->setColumns(6)
                ->setColumns(6)
                ->setBasePath('assets/images')
                ->setUploadDir('public/assets/images')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            IntegerField::new('width', 'Largeur')->setColumns(6),
            IntegerField::new('height', 'Hauteur')->setColumns(6),
            DateField::new('date_creation','Créé le')->hideOnForm(),
            DateField::new('date_modification', "Modifié le")->hideOnForm(),
            AssociationField::new('category','Catégorie')->setColumns(6),
            AssociationField::new('article','Article')->setColumns(6),
            AssociationField::new('media_type_id','Type de média')->setColumns(6),
            CodeEditorField::new('test'),
        ];
    }



    /**
     * createEntity permet de donner des valeurs par défaut aux différents champs de notre entité
     * @param string $entityFqcn
     * @return Category
     */
    public function createEntity(string $entityFqcn)
    {
        $media = new Media();
        $media->setDateCreation(new \DateTimeImmutable());
        $media->setDateModification(new \DateTimeImmutable());

        return $media;
    }


    /**
     * configureCrud permet de configurer le crud, champs de recherche, redirection vers un template spécial, triage ...
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ...
            ->setEntityLabelInPlural('Medias')
            // Titre de la page et nom de la liste affichée
            ->setHelp('index', 'Liste des médias')
            // Template personnalisé
            ->overrideTemplate('crud/index', 'backoffice/media/medias.html.twig')
            // Champs de recherche
            //->setSearchFields(['title'])
            // ->setDefaultSort(['id' => 'DESC'])
            // ->setPaginatorPageSize(30)
            // ->setPaginatorRangeSize(4)
            // Actions sur la liste visible (par défaut cachées dans un dropdown)
            ->showEntityActionsInlined()
            ;

    }


}
