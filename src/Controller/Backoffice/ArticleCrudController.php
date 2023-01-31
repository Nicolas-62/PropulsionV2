<?php

namespace App\Controller\Backoffice;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Validator\Constraints\Date;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    private AdminUrlGenerator $adminUrlGenerator;
    private EntityManagerInterface $entityManager;
    private EntityRepository $entityRepository;

    private int $entityId;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityRepository $entityRepository, EntityManagerInterface $entityManager)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityRepository  = $entityRepository;
        $this->entityManager  = $entityManager;
        $this->entityId          = 0;
    }

    /**
     * configureFields permet la configuration des différents champs que l'on va retrouver sur les pages du crud
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title','titre'),
            TextEditorField::new('content','description'),
            DateField::new('created_at','créé à')->hideOnForm(),
            DateField::new('updated_at','dernière édition')->hideOnForm(),
            IdField::new('article_id'),
        ];
    }

    /**
     * createEntity permet de definir les valeurs par défaut de l'entité
     * @param string $entityFqcn
     * @return Article
     */
    public function createEntity(string $entityFqcn)
    {
        $articles = $this->entityManager->getRepository(Article::class)->findAll();
        $indice = 0;
        foreach ( $articles as $art ){
            $indice = $indice + 1;
        }

        $article = new Article();
        $article->setCreatedAt( new \DateTimeImmutable() );
        $article->setUpdatedAt( new \DateTimeImmutable() );
        $article->setPosition( $indice + 1 );

        return $article;
    }


    /**
     * configureCrud permet de configurer le crud, champs de recherche, redirection vers un template spécial, triage ...
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ...

            ->overrideTemplate('crud/index', 'backoffice/article/articles.html.twig');

    }


    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {

        $articles = $this->entityManager->getRepository(Article::class)->findAll();
        $responseParameters->set('articles',$articles);


        return parent::configureResponseParameters($responseParameters);
    }

}
