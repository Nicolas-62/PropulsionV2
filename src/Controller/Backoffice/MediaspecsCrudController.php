<?php

namespace App\Controller\Backoffice;

use App\Entity\Mediaspec;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\Date;

class MediaspecsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Mediaspec::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('objet','Objet')->setColumns(6),
            TextField::new('nom','Nom')->setColumns(6),
            IntegerField::new('width','Largeur')->setColumns(6),
            IntegerField::new('height','Hauteur')->setColumns(6),
            AssociationField::new('article','Article parent')->setColumns(6),
            AssociationField::new('category','Category parent')->setColumns(6),
            BooleanField::new('is_mandatory','Est Obligatoire')->setColumns(3),
            BooleanField::new('haslegende','Possède une légende')->setColumns(3),
            DateField::new('date_creation','Date de création')->setColumns(3)->hideOnForm(),
            DateField::new('date_modification','Date de modification')->setColumns(3)->hideOnForm(),
            AssociationField::new('mediaType','Type de média'),
        ];
    }

}
