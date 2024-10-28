<?php

namespace App\Controller\Backoffice;

use App\Entity\Eleve;
use App\Entity\Matiere;
use App\Entity\Note;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
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

            ->showEntityActionsInlined()
            ;
    }
    public function configureFields(string $pageName): iterable
    {

        yield FormField::addTab('Description');
        yield FormField::addColumn(6);
        yield TextField::new('prenom', 'Prenom');
        yield TextField::new('nom', 'Nom');
        yield TextField::new('sexe', 'Genre')->hideOnForm();
        yield ChoiceField::new('sexe', 'Genre')->setChoices([
            'genre' => ['Homme' => 'Homme', 'Femme' => 'Femme', 'Non binaire' => 'Non binaire'],
        ])->hideOnIndex()->autocomplete();
        yield AssociationField::new('classe','Classe');
        if($pageName == Crud::PAGE_EDIT) {

            yield FormField::addTab('Notes');
            // Gestion des notes et appréciations par matière
            $matieres = $this->entityManager->getRepository(Matiere::class)->findAll();
            foreach ($matieres as $matiere) {
                yield FormField::addColumn(6);
                yield FormField::addFieldset(ucfirst($matiere->getNom()));
                yield NumberField::new('note'.$matiere->getId(), 'Note');
                yield TextareaField::new('rate'.$matiere->getId(), 'Appréciation');
            }
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
        // Ordonne par id
        $response->orderBy('entity.id', 'DESC');
        return $response;
    }
}
?>