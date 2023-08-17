<?php

namespace App\Controller\Backoffice;

use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

use DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;


class ThemeCrudController extends AbstractCrudController
{
    public function __construct(

      // Générateur de routes
      private AdminUrlGenerator $adminUrlGenerator,
      // Gestionnaire d'entité Symfony
      private EntityManagerInterface $entityManager,
      // Repository EasyAdmin
      private EntityRepository $entityRepository,

    )
    {
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // Seul les administrateurs peuvent éditer les thèmes
            ->setEntityPermission('ROLE_ADMIN')
            ;
    }

    public static function getEntityFqcn(): string
    {
        return Theme::class;
    }


    public function configureFields(string $pageName): iterable
    {

            yield IdField::new('id')->hideOnForm()->setPermission('ROLE_DEV');
            yield TextField::new('name');
            yield IntegerField::new('ordre');
            yield DateTimeField::new('createdAt')->hideOnForm();
            yield DateTimeField::new('updatedAt')->hideOnForm();
    }


    public function createEntity(string $entityFqcn)
    {
      $theme = new Theme();
      $theme->setCreatedAt( new DateTimeImmutable() );
      $theme->setUpdatedAt( new DateTimeImmutable() );
      $theme->setOrdre( $this->entityManager->getRepository(Theme::class)->count([]) + 1 );

      return $theme;
    }

}
