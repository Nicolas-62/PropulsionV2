<?php

namespace App\Controller\Backoffice;

use App\Entity\Mediaspec;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MediaspecsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Mediaspec::class;
    }


    /**
     * configureCrud permet de configurer le crud, champs de recherche, redirection vers un template spécial, triage ...
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            //showEntityActionsInlined : permet d'afficher les actions en ligne plutot que dans un menu
            ->showEntityActionsInlined()
            ;

    }

    public function configureFields(string $pageName): iterable
    {

        yield TextField::new('name','Nom')->setColumns(12);
        yield IntegerField::new('width','Largeur')->setColumns(6);
        yield IntegerField::new('height','Hauteur')->setColumns(6);
        yield AssociationField::new('article','Article')->setColumns(6);
        yield AssociationField::new('category','Category')->setColumns(6);
        yield IntegerField::new('heritage','Héritage')->setColumns(6);
        yield AssociationField::new('mediaType','Type de média');

        yield BooleanField::new('mandatory','Est Obligatoire')->setColumns(3);
        yield BooleanField::new('haslegend','Possède une légende')->setColumns(3);

        yield DateField::new('created_at','Date de création')->hideOnForm();
        yield DateField::new('updated_at','Date de modification')->hideOnForm();

    }

}
