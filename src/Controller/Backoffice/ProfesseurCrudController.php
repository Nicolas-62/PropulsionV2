<?php

namespace App\Controller\Backoffice;

use App\Entity\Matiere;
use App\Entity\Professeur;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

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
                    return 'Professeur : '.$this->entity;
                }
            })

            ->showEntityActionsInlined()
            ;
    }

    /**
     *  Renvoi vers le formulaire d'édition de l'entité
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|Response
     */
    public function edit(AdminContext $context)
    {
        // Récupération de l'article
        $this->entity = $context->getEntity()->getInstance();

        return parent::edit($context);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addColumn(6);
        yield TextField::new('prenom', 'Prenom');
        yield TextField::new('nom', 'Nom');
        yield TextField::new('sexe', 'Genre')->hideOnForm();
        yield ChoiceField::new('sexe', 'Genre')->setChoices([
            'genre' => ['Homme' => 'Homme', 'Femme' => 'Femme', 'Non binaire' => 'Non binaire'],
        ])->hideOnIndex()->autocomplete();

        yield AssociationField::new('classes','Classes')
            ->formatValue(function($value, $professeur) {
            // Concatenation du nom de la catégorie avec les noms des catégories parentes.
            $classes = $professeur->getClasses();
            $value = ' ';
            if( ! $classes->isEmpty()) {
                $classe_names = [];
                foreach ($classes as $classe) {
                    $classe_names[] = $classe->getNom();
                }
                $value = implode(', ', $classe_names);
            }
            return $value;
        })->hideOnForm()
        ;
//        yield AssociationField::new('classes','Classes')->autocomplete()->hideOnIndex();

        yield AssociationField::new('matiere','Matière')
            ->formatValue(function($value, $professeur) {
                // Concatenation du nom de la catégorie avec les noms des catégories parentes.
                $matiere = $professeur->getMatiere();
                $value = ' ';
                if($matiere != null) {
                    $value = $matiere->getNom();
                }
                return $value;
            })
        ;
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ;
    }
}
