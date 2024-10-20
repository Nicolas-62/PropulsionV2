<?php

namespace App\Controller\Backoffice;

use App\Entity\Matiere;
use App\Entity\Professeur;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
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
                    return 'Professeur : '.$this->entity->getNom();
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
        yield TextField::new('prenom', 'Prenom');
        yield TextField::new('nom', 'Nom');
        yield TextField::new('sexe', 'Genre');
        yield AssociationField::new('classes','Classes')->setColumns(6)
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
        yield AssociationField::new('matieres','Matières')->setColumns(6)
            ->formatValue(function($value, $professeur) {
                // Concatenation du nom de la catégorie avec les noms des catégories parentes.
                $matieres = $professeur->getMatieres();
                $value = ' ';
                if( ! $matieres->isEmpty()) {
                    $matiere_names = [];
                    foreach ($matieres as $matiere) {
                        $matiere_names[] = $matiere->getNom();
                    }
                    $value = implode(', ', $matiere_names);
                }
                return $value;
            })->hideOnForm()
        ;
    }
}
