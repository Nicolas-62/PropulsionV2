<?php

namespace App\Controller\Backoffice;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{

    // Utilisateur courant
    private ?User $entity = null;

    public function __construct
    (
        // Services
        private EntityRepository $entityRepository,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        if ( !  $this->isGranted('ROLE_ADMIN')) {
            yield IdField::new('id')->hideOnForm();
        }

        yield TextField::new('firstname', 'prénom')
            ->setColumns(7)

        ;
        yield TextField::new('lastname', 'nom')
            ->setColumns(7)
        ;
        yield TextField::new('email')
            ->setColumns(7)

        ;
        // Si l'utilsiateur existe déjà on n'afficha pas la saisie du mot de passe.
        if($this->entity == null) {
            yield TextField::new('password', 'mot de passe')
                ->setFormType(PasswordType::class)
                ->onlyOnForms()
                ->setColumns(7)
                ->setRequired(true);
        }

        yield ChoiceField::new('roles')
            ->allowMultipleChoices()
            ->renderAsBadges([
                'ROLE_ADMIN' => 'success',
                'ROLE_AUTHOR'=> 'warning'
            ])
            ->setChoices([
                'Administrateur' => 'ROLE_ADMIN',
                'Auteur'         => 'ROLE_AUTHOR'
            ])
            ->setColumns(7)
        ;


    }

    /**
     * Renvoi vers le formulaire d'édition de l'utilisateur
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

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->showEntityActionsInlined()
            ;
    }

    /**
     * Personnalise la liste des entitées à afficher
     *
     * @param SearchDto $searchDto
     * @param EntityDto $entityDto
     * @param FieldCollection $fields
     * @param FilterCollection $filters
     * @return QueryBuilder
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        // récupération de la requête
        $response = $this->entityRepository->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        // Ajout d'un filtre, on affiche pas les utilisateurs développeurs
        $response->andWhere('entity.roles != :roleDev')->setParameter('roleDev', 'ROLE_DEV');

        return $response;
    }

    /**
     * Pré-traitements sur l'éntité Utilisateur à sauvegarder.
     *
     * @param EntityManagerInterface $entityManager
     * @param $entityInstance
     * @return void
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $user = $entityInstance;
        // Hash le password de l'utilisateur avant de l'enregistrer en bdd.
        $plainPassword = $user->getPassword();
        // Si un mot de passe a été renseigné
        if($plainPassword != null) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }

        parent::persistEntity($entityManager, $entityInstance); // TODO: Change the autogenerated stub
    }

}
