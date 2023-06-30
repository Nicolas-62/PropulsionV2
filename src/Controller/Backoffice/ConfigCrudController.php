<?php

namespace App\Controller\Backoffice;

use App\Entity\Config;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConfigCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Config::class;
    }


    public function configureFields(string $pageName): iterable
    {


        // Onglet Configuration générale
        yield FormField::addTab('Configuration générale');
        yield IntegerField::new('cache_flush_auto');



        // Onglet Informations Contact
        yield FormField::addTab('Informations Contact');
        yield TextField::new('email_contact','Email Contact')->setColumns(6);
        yield TextField::new('email_objet',"Objet de l'email")->setColumns(6);
    }

}
