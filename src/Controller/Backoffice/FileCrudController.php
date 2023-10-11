<?php

namespace App\Controller\Backoffice;


use App\Field\FileUploadField;
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
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mime\MimeTypes;
use Twig\Environment;
use Vich\UploaderBundle\Form\Type\VichImageType;
use function PHPUnit\Framework\throwException;

class FileCrudController extends MediaCrudController
{
    public array $acceptedExtensions  =   ['pdf'];
    public array $acceptedFileTypes   =   [];
    public string $bo_model_name      =   'file';
    public string $bo_models_name     =   'files';
    public string $model_label        =   'fichier';
    public string $models_label       =   'fichiers';
    public string $model_type_label   =   'pdf';

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
     * Défini les actions suppélemnetaires disponibles dans la vue
     *
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        // Ajout d'un bouton de téléchargement du fichier PDF
        $dowloadFile = Action::new('downloadFile', 'Télécharger', 'fas fa-download')
            ->linkToCrudAction('download')
            ->setHtmlAttributes(['target' => '_blank'])
            ->setCssClass('text-light')
        ;

        $actions->add(Crud::PAGE_INDEX, $dowloadFile);
        return parent::configureActions($actions);
    }

    /**
     * Permet le téléchargement du fichier associé au média dont l'id est passé en parametre.
     *
     * @param AdminContext $context
     * @return BinaryFileResponse|RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function download(AdminContext $context){
        // On récupère le média
        $entity = $this->entityManager->getRepository(Media::class)->find($context->getRequest()->get('entityId'));
        // Si le media existe
        if($entity != null) {
            // On construit le chemin du fichier
            $filepath = $this->getParameter('app.dyn_img_path') . $entity->getMedia();
            $file = new File($filepath);
            // Si la fichier existe
            if($file->isFile()){
                // Envoi du fichier au navigateur
                return new BinaryFileResponse($file);
            }else{
                $this->addFlash('danger', 'Le fichier est introuvable');
            }
        }else{
            $this->addFlash('danger', 'Le média est introuvable');
        }
        // Création de l'url de redirection
        $url = $this->container->get(AdminUrlGenerator::class)
            ->setAction(Action::INDEX)
            ->setEntityId($entity->getId())
            ->generateUrl();
        return $this->redirect($url);
    }

    /**
     * Définie les assets nécessaires pour le controleur de médias.
     * @param Assets $assets
     * @return Assets
     */
    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry('bo_files');
    }

}
