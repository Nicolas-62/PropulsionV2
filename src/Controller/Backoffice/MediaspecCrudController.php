<?php

namespace App\Controller\Backoffice;

use App\Entity\Category;
use App\Entity\Mediaspec;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class MediaspecCrudController extends AbstractCrudController
{
    public function __construct(
        // Services
        // Gestionnaire d'entité Symfony
        protected EntityManagerInterface $entityManager,
        // Code Langue
        protected string $locale
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Mediaspec::class;
    }


    /**
     * configureCrud permet de configurer le crud, champs de recherche, redirection vers un template spécial, triage ...
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // showEntityActionsInlined : permet d'afficher les actions en ligne plutot que dans un menu
            ->showEntityActionsInlined()
            // Seul les développeurs peuvent éditer les médiaspecs
            ->setEntityPermission('ROLE_DEV')
            ;

    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm()->setPermission('ROLE_DEV');
        yield TextField::new('name','Nom')->setColumns(12);
        yield IntegerField::new('width','Largeur')->setColumns(6);
        yield IntegerField::new('height','Hauteur')->setColumns(6);
        // AU moins un des deux champs suivant doit être saisi.
        yield AssociationField::new('article','Article')->setRequired(false)->setColumns(6);
        // On récupère les catégories auxquelles on peut associer des articles.
        $category_form_options = [
            // Choix possibles.
            'choices' => $this->entityManager->getRepository(Category::class)->getHasCreateCategories($this->getUser(), $this->locale),
            // On ajoute dans le label le nom des ancètres
            'choice_label' => function($category, $key, $value) {
                $value = $category->getTitle();
                foreach($category->getAncestors() as $ancestor) {
                    $value = $ancestor->getTitle() . ' / '. $value;
                }
                return $value;
            },
        ];
        yield AssociationField::new('category','Category')->setRequired(false)->setColumns(6)->formatValue(function($value, $mediaspec) {
            // Concatenation du nom de la catégorie avec les noms des catégories parentes.
            $category = $mediaspec->getCategory();
            if($category != null) {
                $value = $category->getTitle();
                foreach($category->getAncestors() as $ancestor) {
                    $value = $ancestor->getTitle() . ' / '. $value;
                }
            }
            return $value;
        })->setFormTypeOptions($category_form_options);
        if($pageName == Crud::PAGE_INDEX) {
            yield ArrayField::new('heritage', 'Héritage')->setColumns(6);
        }else if(in_array($pageName, array(Crud::PAGE_EDIT, Crud::PAGE_NEW)) ) {
            yield CollectionField::new('heritage', 'Héritage')->setColumns(6)->setFormTypeOptions([
                'entry_type' => IntegerType::class,
                'entry_options' => [
                    'attr' => ['type' => 'number', 'min' => 0, 'max' => 9]
                ]
            ]);
        }
        yield AssociationField::new('mediaType','Type de média');

        yield BooleanField::new('mandatory','Est Obligatoire')->setColumns(3);
        yield BooleanField::new('haslegend','Possède une légende')->setColumns(3);

        yield DateField::new('created_at','Date de création')->hideOnForm();
        yield DateField::new('updated_at','Date de modification')->hideOnForm();

    }

}
