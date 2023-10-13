<?php

namespace App\Controller\Backoffice;


use App\Field\ImageUploadField;

use App\Service\MediaService;
use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\MimeTypes;
use Twig\Environment;
use Vich\UploaderBundle\Form\Type\VichImageType;
use function PHPUnit\Framework\throwException;


class PictureCrudController extends MediaCrudController
{

    public array $acceptedExtensions  =   ['png', 'jpg', 'jpeg'];
    public array $acceptedFileTypes   =   [];
    public string $bo_model_name      =   'picture';
    public string $bo_models_name     =   'pictures';
    public string $model_label        =   'image';
    public string $models_label       =   'images';
    public string $model_type_label   =   'image';


    public function __construct(
        // Repository EasyAdmin
        EntityRepository $entityRepository,
        Environment $twig,
        EntityManagerInterface $entityManager

    )
    {
        // Appel du constructeur du controller parent
        parent::__construct($entityRepository, $twig, $entityManager);
    }

    /**
     * Définie les assets nécessaires pour le controleur
     * @param Assets $assets
     * @return Assets
     */
    public function configureAssets(Assets $assets): Assets
    {
        $assets->addWebpackEncoreEntry('bo_pictures');
        return parent::configureAssets($assets);
    }

    public function selectorPagePicture(){
// Création d'une instance de Response
        $response = new Response();
        $response->setContent('<html><body>Hello, Symfony!</body></html>');
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');
        $response->send();
    }


}
