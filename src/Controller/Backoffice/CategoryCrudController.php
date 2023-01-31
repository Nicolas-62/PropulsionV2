<?php

namespace App\Controller\Backoffice;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'title'),
            IntegerField::new('position', 'position'),
            DateField::new('created_at', 'creation'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ...

            ->overrideTemplate('crud/index', 'category/index.html.twig');

    }
}
