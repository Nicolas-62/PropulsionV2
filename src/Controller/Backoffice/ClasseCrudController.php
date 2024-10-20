<?php

namespace App\Controller\Backoffice;

use App\Entity\Classe;
use App\Entity\Professeur;
use App\Factory\ProfesseurFactory;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\FieldProvider;
use http\Client\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
                    return $this->entity->getNom();
                }
            })

            ->showEntityActionsInlined()
            ;

    }


    /**
     *  Renvoi vers le formulaire d'édition de l'entité
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(AdminContext $context)
    {
        // Récupération de l'article
        $this->entity = $context->getEntity()->getInstance();
        $this->professeur  = $this->entity->getProfesseur();

        return parent::edit($context);
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('Paramètres');
        yield TextField::new('Nom', 'Nom')->setColumns(6);

        // Professeur
        // On récupère les prefesseur
        $form_options = [
            // Catégorie associée.
            'data' => $this->professeur,
            // Choix possibles.
            'choices' => $this->entityManager->getRepository(Professeur::class)->findAll(),
            // On ajoute dans le label le nom des ancètres
            'choice_label' => function($professeur) {
                return $professeur->getPrenom() . ' ' . $professeur->getNom();
            },
        ];
        yield AssociationField::new('professeur','Professeur')->setColumns(6)->formatValue(function($value, $classe) {
            // Concatenation du nom de la catégorie avec les noms des catégories parentes.
            $professeur = $classe->getProfesseur();
            if($professeur != null) {
                $value = $professeur->getPrenom() . ' ' . $professeur->getNom();
            }
            return $value;
        })->setFormTypeOptions($form_options);
        // Nombre d'élèves
        yield AssociationField::new('eleves','Eleves')->hideOnForm();
    }
}
