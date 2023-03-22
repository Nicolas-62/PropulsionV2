<?php

namespace App\Controller\Backoffice;

use App\Entity\Category;
use App\Entity\Media;
use App\Field\MediaSelectField;
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
       // if(Crud::PAGE_EDIT === $pageName || Crud::PAGE_NEW === $pageName) {
        yield TextField::new('getName', 'nom')->onlyOnIndex();
        yield MediaSelectField::new('file', 'Fichier');
        yield TextField::new('legend', 'Légende')
                ->setColumns(6);

    }



    /**
     * createEntity permet de donner des valeurs par défaut aux différents champs de notre entité
     * @param string $entityFqcn
     * @return Category
     */
    public function createEntity(string $entityFqcn)
    {
        $media = new Media();
        $media->setCreatedAt( new \DateTimeImmutable() );
        $media->setUpdatedAt( new \DateTimeImmutable() );

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
