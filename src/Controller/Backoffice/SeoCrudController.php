<?php

namespace App\Controller\Backoffice;

use App\Entity\Seo;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;


class SeoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Seo::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
