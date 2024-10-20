<?php

namespace App\Controller\Backoffice;

use App\Entity\Matiere;
use App\Entity\Professeur;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProfesseurCrudController extends AbstractCrudController
{
    protected ?Professeur $entity = null;

    public function __construct(
        // Services
        // Gestionnaire d'entité Symfony
        protected EntityManagerInterface $entityManager,
    )
    {
    }
    public static function getEntityFqcn(): string
    {
        return Professeur::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ...
            ->setEntityLabelInPlural('Professeurs')
            // Titre de la page et nom de la liste affichée
            ->setPageTitle('index', function (){
                return 'Professeurs';
            })
            ->setPageTitle('edit', function (){
                if($this->entity != null){
                    return 'Professeur : '.$this->entity->getNom();
                }
            })

            ->showEntityActionsInlined()
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('prenom', 'Prenom');
        yield TextField::new('nom', 'Nom');
        yield TextField::new('sexe', 'Genre');
        yield AssociationField::new('classes','Classes')->formatValue(function($value, $professeur) {
            // Concatenation du nom de la catégorie avec les noms des catégories parentes.
            $classes = $professeur->getClasses();
            if( ! $classes->isEmpty()) {
                $classe_names = [];
                foreach ($classes as $classe) {
                    $classe_names[] = $classe->getNom();
                }
                $value = implode(', ', $classe_names);
            }
            return $value;
        });
    }
}
