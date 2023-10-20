<?php

namespace App\Controller\Frontoffice;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\CategoryData;
use App\Entity\Media;
use App\Library\Imagique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/ressources/', name: 'ressources_')]

class RessourcesController extends AbstractController
{
    // Taille par défaut des vignettes affichées dans la vue.
    protected $thumbnails   = array(
        'large' =>  array('width' => 1500, 'height' => 1500),
        'medium' => array('width' => 800, 'height' => 800),
        'small'  => array('width' => 200, 'height' => 200)
    );

    // Compression par defaut des images servies.
    protected $default_compression  =   80;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        private ContainerBagInterface $params,
    ) {
    }

    #[Route('{width}/{height}/{media}', name: 'image')]
    public function media($width, $height, $media): Response
    {
        // Récupération du média par son nom.
        $media = $this->entityManager->getRepository(Media::class)->findOneBy(['media' => $media]);
        // Si il existe
        if($media) {
            // Si pas de taille définie on part de la large qui est générée par défaut dans toutes les instances.
            $filepath = $this->params->get('app.dyn_img_path') . $media->getSectionPath() . $media->getMedia();
            // Récupération du fichier
            $file = new File($filepath);
            // Si le fichier existe
            if ($file->isFile()) {
                // Instanciation d'un objet Imagique.
                $imagique = new Imagique($filepath);

                // Dimensions nulls par défaut (sert l'image source)
                $width_px  = null;
                $height_px = null;
                // si la largeur et la hauteur sont des dimensions en pixel
                if (ctype_digit($width) && ctype_digit($height)) {
                    $width_px = $width;
                    $height_px = $height;
                } else if (isset($this->thumbnails[$width])) {
                    // On vérifie si c'est un thumbnail connu, on récupère ses dimensions.
                    $width_px = $this->thumbnails[$width]['width'];
                    $height_px = $this->thumbnails[$width]['height'];
                }
                //dump($width_px); exit();
                if ($width_px && $width_px < 300) {
                    // Sert l'image avec compression plus importante
                    $imagique->serve($width_px, $height_px, 72, 40);
                    exit();
                } else {
                    // Sert l'image.
                    $imagique->serve($width_px, $height_px, 72, $this->default_compression);
                    exit();
                }

            }else {
                return $this->file_not_found();
            }
        }else {
            return $this->file_not_found();
        }
        return $this->file_not_found();
    }

}
