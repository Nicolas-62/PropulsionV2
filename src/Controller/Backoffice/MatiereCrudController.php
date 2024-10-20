<?php

namespace App\Controller\Backoffice;

use App\Entity\Classe;
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

class MatiereCrudController extends AbstractCrudController
{
    protected ?Matiere $entity = null;

    public function __construct(
        // Services
        // Gestionnaire d'entité Symfony
        protected EntityManagerInterface $entityManager,
    )
    {
    }
    public static function getEntityFqcn(): string
    {
        return Matiere::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ...
            ->setEntityLabelInPlural('Matières')
            // Titre de la page et nom de la liste affichée
            ->setPageTitle('index', function (){
                return 'Matières';
            })
            ->setPageTitle('edit', function (){
                if($this->entity != null){
                    return 'Matière : '.$this->entity->getNom();
                }
            })

            ->showEntityActionsInlined()
            ;
    }

    /**
     * Renvoi vers le formulaire d'édition de l'article
     *
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|Response
     */
    public function edit(AdminContext $context)
    {
        // Récupération de l'article
        $this->entity = $context->getEntity()->getInstance();

        return parent::edit($context);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('Nom', 'Nom');

        // On récupère les catégories auxquelles on peut associer des articles.
        $form_options = [
            // Choix possibles.
            'choices' => $this->entityManager->getRepository(Professeur::class)->findAll(),
            // On ajoute dans le label le nom des ancètres
            'choice_label' => function($professeur) {
                return $professeur->getPrenom().' '.$professeur->getNom();
            },
        ];
        yield AssociationField::new('professeur','Professeur')->setColumns(6)->formatValue(function($value, $matiere) {
            // Concatenation du nom de la catégorie avec les noms des catégories parentes.
            $professeur = $matiere->getProfesseur();
            if($professeur != null) {
                $value = $professeur->getPrenom() . ' ' . $professeur->getNom();
            }
            return $value;
        })->setFormTypeOptions($form_options);
    }
}
