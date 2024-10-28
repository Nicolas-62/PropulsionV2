<?php

namespace App\Controller\Backoffice;

use App\Entity\Classe;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
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
            ->setPageTitle('detail', function (Matiere $matiere){
                return 'Matière : '.$matiere->getNom();
            })
            ->showEntityActionsInlined()
            ->setDefaultSort(['nom' => 'ASC'])

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
        yield FormField::addColumn(6);
        if($pageName == Crud::PAGE_DETAIL){
            yield FormField::addFieldset('infos');

        }
        yield TextField::new('Nom', 'Nom')->hideOnIndex();
        if($pageName == Crud::PAGE_INDEX){
            yield TextField::new('Nom', 'Nom')->formatValue(function ($value, $entity){
                return '<a href="'.
                    $this->container->get(AdminUrlGenerator::class)
                        ->setAction(Action::DETAIL)
                        ->setEntityId($entity->getId())
                        ->generateUrl()
                    .'">'.$entity->getNom().'</a>';
            });
        }
            // On récupère les catégories auxquelles on peut associer des articles.
            $form_options = [
                // Choix possibles.
                'choices' => $this->entityManager->getRepository(Professeur::class)->findAll(),
                // On ajoute dans le label le nom des ancètres
                'choice_label' => function ($professeur) {
                    return $professeur->getPrenom() . ' ' . $professeur->getNom();
                },
            ];
            yield AssociationField::new('professeurs', 'Professeurs')->setColumns(6)->formatValue(function ($value, $matiere) {
                // Concatenation du nom de la catégorie avec les noms des catégories parentes.
                $professeurs = $matiere->getProfesseurs();
                $value = ' ';
                if (!$professeurs->isEmpty()) {
                    $professeur_names = [];
                    foreach ($professeurs as $professeur) {
                        $professeur_names[] = $professeur->getPrenom() . ' ' . $professeur->getNom();
                    }
                    $value = implode(', ', $professeur_names);
                }
                return $value;
            })->setFormTypeOptions($form_options)->hideOnForm()->hideOnDetail();
            yield AssociationField::new('professeurs','Professeurs')->formatValue(function($value, $entity) {
                $value = '';
                foreach($entity->getProfesseurs() as $prof){
                    $value .= '<a href='
                        .
                        $this->container->get(AdminUrlGenerator::class)
                            ->setController(ProfesseurCrudController::class)
                            ->setAction(Action::DETAIL)
                            ->setEntityId($prof->getId())
                            ->generateUrl()
                        .
                        '>'.$prof->getNom().' '.$prof->getPrenom().'</a>';
                    $value .= '<br>';
                }
                return $value;
            })->onlyOnDetail()
                ->setTemplatePath('backoffice/field/eleves.html.twig')
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)

            ;
    }
}
