<?php

namespace App\Controller\Backoffice;

use App\Entity\Eleve;
use App\Entity\Matiere;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EleveCrudController extends AbstractCrudController
{
    protected ?Eleve $entity = null;

    public function __construct(
        // Services
        // Gestionnaire d'entité Symfony
        protected EntityManagerInterface $entityManager,
    )
    {
    }
    public static function getEntityFqcn(): string
    {
        return Eleve::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ...
            ->setEntityLabelInPlural('Elèves')
            // Titre de la page et nom de la liste affichée
            ->setPageTitle('index', function (){
                return 'Elèves';
            })
            ->setPageTitle('edit', function (){
                if($this->entity != null){
                    return 'Elève : '.$this->entity->getNom();
                }
            })

            ->showEntityActionsInlined()
            ;

    }
}
