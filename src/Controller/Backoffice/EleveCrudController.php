<?php

namespace App\Controller\Backoffice;

use App\Entity\Eleve;
use App\Entity\Matiere;
use App\Entity\Note;
use App\Form\NoteType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use function Zenstruck\Foundry\Persistence\persist;

class EleveCrudController extends AbstractCrudController
{
    protected ?Eleve $entity = null;

    public function __construct(
        // Services
        // Gestionnaire d'entité Symfony
        protected EntityManagerInterface $entityManager,
        // Repository EasyAdmin
        protected EntityRepository $entityRepository,
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
                    return 'Elève : '.$this->entity->getPrenom() . ' ' . $this->entity->getNom();
                }
            })
            ->setPageTitle('detail', function (Eleve $eleve){
                return 'Elève : '.$eleve->getPrenom() . ' ' . $eleve->getNom();
            })

            ->showEntityActionsInlined()
            ->setDefaultSort(['nom' => 'ASC'])
            ->setFormThemes(['backoffice/field/notes.html.twig', '@EasyAdmin/crud/form_theme.html.twig'])
            ;
    }
    public function configureFields(string $pageName): iterable
    {

        yield FormField::addTab('Description');
        yield FormField::addColumn(6);
        yield TextField::new('prenom', 'Prenom')->hideOnIndex();
        yield TextField::new('nom', 'Nom')->hideOnIndex();

        if($pageName == Crud::PAGE_INDEX){
            yield TextField::new('nom', 'Nom')->formatValue(function ($value, $entity){
                return '<a href="'.
                    $this->container->get(AdminUrlGenerator::class)
                        ->setAction(Action::DETAIL)
                        ->setEntityId($entity->getId())
                        ->generateUrl()
                    .'">'.$entity->getNom().' '.$entity->getPrenom().'</a>';
            });
        }

        yield TextField::new('sexe', 'Genre')->hideOnForm();
        yield ChoiceField::new('sexe', 'Genre')->setChoices([
            'genre' => ['Homme' => 'Homme', 'Femme' => 'Femme', 'Non binaire' => 'Non binaire'],
        ])->hideOnIndex()->autocomplete();
        yield AssociationField::new('classe','Classe');
        if($pageName != Crud::PAGE_INDEX and $this->entity != null) {
            yield FormField::addTab('Notes');
            yield FormField::addColumn(12);
            yield CollectionField::new('notes', '')
                ->setEntryType(NoteType::class)
                ->allowAdd(false)->allowDelete(false)
                ->renderExpanded()
                ->setFormTypeOptions([
                    'block_name' => 'notes',
                ])
            ;
        }
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_NEW, Action::SAVE_AND_CONTINUE)
            ;
    }
    /**
     * Fourni à la vue les variables dont elle a besoin pour fonctionner.
     *
     * @param KeyValueStore $responseParameters
     * @return KeyValueStore
     */
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        if (Crud::PAGE_EDIT === $responseParameters->get('pageName')) {
            $twig = $this->container->get('twig');
            $twig->addGlobal('matieres', $this->entityManager->getRepository(Matiere::class)->findAll());
        }
        return parent::configureResponseParameters($responseParameters);
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

    /**
     *  Detail
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|Response
     */
    public function detail(AdminContext $context)
    {
        // Récupération de l'article
        $this->entity = $context->getEntity()->getInstance();
        return parent::detail($context);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Gestion des notes et appréciations par matière
        $matieres = $entityManager->getRepository(Matiere::class)->findAll();
        foreach ($matieres as $matiere) {
            $note = new Note();
            $note->setMatiere($matiere);
            $note->setEleve($entityInstance);
            $entityManager->persist($note);
        }

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    /**
     * Requêtage des entités à afficher.
     *
     * @param SearchDto $searchDto
     * @param EntityDto $entityDto
     * @param FieldCollection $fields
     * @param FilterCollection $filters
     * @return QueryBuilder
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        // récupération des articles.
        $response = $this->entityRepository->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        return $response;
    }
}
?>