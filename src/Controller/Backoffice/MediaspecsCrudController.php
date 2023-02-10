<?php

namespace App\Controller\Backoffice;

use App\Entity\Mediaspecs;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MediaspecsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Mediaspecs::class;
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
