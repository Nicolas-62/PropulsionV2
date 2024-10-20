<?php

namespace App\Controller\Backoffice;

use App\Entity\Classe;
use App\Entity\Professeur;
use App\Factory\ProfesseurFactory;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\FieldProvider;

class ClasseCrudController extends AbstractCrudController
{

    protected ?Classe $entity = null;

    protected ?Professeur $professeur = null;

    public function __construct(
        // Services
        // Gestionnaire d'entité Symfony
        protected EntityManagerInterface $entityManager,
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Classe::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ...
            ->setEntityLabelInPlural('Classes')
            // Titre de la page et nom de la liste affichée
            ->setPageTitle('index', function (){
                    return 'Classes';
            })
            ->setPageTitle('edit', function (){
                if($this->entity != null){
                    return 'Classe : '.$this->entity->getNom();
                }
            })

            ->showEntityActionsInlined()
            ;

    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('Paramètres');
        yield TextField::new('Nom', 'Nom');

        // SPECIFIQUE INDEX
        // Professeur
        yield AssociationField::new('professeur','Professeur')->formatValue(function($value, $classe) {
            // Concatenation du nom de la catégorie avec les noms des catégories parentes.
            $professeur = $classe->getProfesseur();
            if($professeur != null) {
                $value = $professeur->getPrenom() . ' ' . $professeur->getNom();
            }
            return $value;
        })->hideOnForm();

        // Nombre d'élèves
        yield AssociationField::new('eleves','Eleves')->hideOnForm();


        // SPECIFIQUE FORM_EDIT

        // On récupère les prefesseur
        $professeur_form_options = [
            // Catégorie associée.
            'data' => $this->professeur,
            // Choix possibles.
            'choices' => $this->entityManager->getRepository(Professeur::class)->findAll(),
            // On ajoute dans le label le nom des ancètres
            'choice_label' => function($professeur) {
                return $professeur->getPrenom() . ' ' . $professeur->getNom();
            },
        ];
        // Catégorie parent
        yield AssociationField::new('professeur', 'Professeur')
            ->hideOnDetail()->setColumns(12)->hideOnIndex()->setRequired(false)->setFormTypeOptions($professeur_form_options)
        ;


    }
}
